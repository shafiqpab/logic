<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
//document.getElementById('txt_delivery_date').value
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_sewing_end_line_inspection_summary_report_controller', $data+'_'+this.value+'_'+document.getElementById('cbo_production_type').value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	
}

// if ($action=="load_drop_down_floor")
// {
// 	$data=explode("_",$data);
// 	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and location_id='$data[1]' and production_process=$data[2] order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
// 	exit();    	 
// }
if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	if ($data[2]==11) {
		$process_con=" and production_process in (13,5)";
	}else{
		$process_con=" and production_process=$data[2]";
	}
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and location_id='$data[1]' $process_con order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	//echo "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and location_id='$data[1]' and production_process=$data[2] order by floor_name";
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
	// $lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}
	// print_r($lineArr);
	
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
	
	if(str_replace("'","",$cbo_company_name)==0) $company_qc_cond=""; else $company_qc_cond=" and d.company_id=$cbo_company_name";
		
	if(str_replace("'","",$cbo_working_company_id)==0) $company_working_cond=""; else $company_working_cond=" and d.serving_company=$cbo_working_company_id";
		
	if(str_replace("'","",$cbo_buyer_name)==0)$buyer_cond=""; else $buyer_cond=" and b.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))==""){$prod_date_qc_cond="";}
	else {$prod_date_qc_cond=" and d.production_date between $txt_date_from and $txt_date_to";}
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	if ($location==0) $location_qc_cond=""; else $location_qc_cond=" and d.location=".$location." "; 
	if(str_replace("'","",$cbo_floor)==0) $floor_name_cond="";else $floor_name_cond=" and d.floor_id=$cbo_floor";
	$prod_qty_arr=array(); $tot_prod_qty_arr=array(); $defect_line_arr=array(); $order_colspan_arr=array();	
	
	if(str_replace("'","",$cbo_production_type)==5 || str_replace("'","",$cbo_production_type)==11)
	{
		$days_run_sql = "SELECT d.sewing_line,b.style_ref_no,d.production_date FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f	WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.id=c.job_id and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3)  $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond 
		group by d.sewing_line,b.style_ref_no,d.production_date	";	
		// echo $days_run_sql;die();
		foreach(sql_select($days_run_sql) as $vals)
		{
			$style_wise_days[$vals[csf('style_ref_no')]][$vals[csf('sewing_line')]]+=1;
		}  
		//print_r($style_wise_days);

		if(str_replace("'","",$cbo_production_type)==5)
		{
			$sql = "SELECT f.mst_id, b.style_ref_no,b.buyer_name, sum(c.po_quantity) as po_quantity, d.sewing_line,d.floor_id, d.prod_reso_allo, sum(f.production_qnty) as qc_pass_qty, sum(f.alter_qty) as alter_qnty, sum(case when is_rescan=0 then  f.reject_qty else 0 end )-sum(case when is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty_bk,sum(case when is_rescan=0 then  f.reject_qty else 0 end )  as reject_qnty,  sum(f.spot_qty) as spot_qnty, sum(f.replace_qty) as replace_qty,sum(case when is_rescan=1 then f.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.id=c.job_id  and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,b.buyer_name,d.sewing_line,d.floor_id, d.prod_reso_allo ";

			$reject_sql = "SELECT f.bundle_no, f.mst_id, b.style_ref_no, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo,    sum(case when is_rescan=0 then  f.reject_qty else 0 end ) as reject_qnty,sum(case when is_rescan=1 then f.production_qnty else 0 end ) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1  and f.is_deleted=0 and f.status_active=1 and b.id=c.job_id and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond  $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond  group by f.bundle_no, f.mst_id,b.style_ref_no,d.sewing_line, d.prod_reso_allo ";
			//echo $reject_sql;die;
			foreach(sql_select($reject_sql) as $row)
			{
				$line_name="";
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
					foreach($line_resource_mst_arr as $resource_id)
					{
						$line_name.=$lineArr[$resource_id].", ";
					}
					$line_name=chop($line_name," , ");
				}
				else
				{
					$line_name=$lineArr[$row[csf('sewing_line')]];
				}
				
				if($line_name!="")
				{

					if($row[csf('reject_qnty')]<$row[csf('replace_qty')])$row[csf('replace_qty')]=$row[csf('reject_qnty')];
					$line_def_rej_qty_arr2[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('reject_qnty')]-$row[csf('replace_qty')];

					$line_def_replace_qty_arr2[$row[csf('sewing_line')]]+=$row[csf('replace_qty')]; 
				}

			}

		}
		elseif (str_replace("'","",$cbo_production_type)==11) 
		{
			$sql = "SELECT f.mst_id, b.style_ref_no,b.buyer_name, sum(c.po_quantity) as po_quantity, d.sewing_line,d.floor_id, d.prod_reso_allo, sum(f.production_qnty) as qc_pass_qty, d.alter_qnty as alter_qnty, sum(case when is_rescan=0 then  f.reject_qty else 0 end )-sum(case when is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty, d.spot_qnty as spot_qnty, sum(f.replace_qty) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.id=c.job_id and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,b.buyer_name,d.sewing_line,d.floor_id, d.prod_reso_allo,d.alter_qnty,d.spot_qnty ";
		} 
   		// echo  $sql;die();	
   		// echo "<pre>";print_r($line_def_rej_qty_arr2);
		$result = sql_select($sql);
		$count=0;
		$data_array = array();
		foreach($result as $row)
		{
			$line_name="";
			if(str_replace("'","",$cbo_production_type)==5)
			{
				if($row[csf('prod_reso_allo')]==1)
				{
					$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);
					foreach($line_resource_mst_arr as $resource_id)
					{
						$line_name.=$lineArr[$resource_id].",";
					}
					$line_name=chop($line_name,",");
					$sewing_line_id = $line_resource_mst_arr[0]; // always 1st line id will take
				}
				else
				{
					$line_name=$lineArr[$row[csf('sewing_line')]];
					$sewing_line_id=$row[csf('sewing_line')];
				}
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$sewing_line_id=$row[csf('sewing_line')];
			}
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else 
			{
				$slNo=$lineSerialArr[$sewing_line_id];
			}
			
			if($line_name!="")
			{
				$mst_id_arr[$row[csf('mst_id')]]=$row[csf('mst_id')];
				$prod_line_arr[$row[csf('sewing_line')]]=$line_name;			//$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
				$qc_qty_arr[$row[csf('sewing_line')]]+=($row[csf('qc_pass_qty')]+$row[csf('alter_qnty')]+$row[csf('reject_qnty')]+$row[csf('spot_qnty')]) ;
				$qc_pass_arr[$row[csf('sewing_line')]]+=($row[csf('qc_pass_qty')]);
				//$qc_qty_arr[$row[csf('sewing_line')]]+=$row[csf('qc_pass_qty')];
				//$prod_def_qty_arr[$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
				 
				$line_def_rej_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('reject_qnty')];
				$line_def_replace_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('replace_qty')];
				 
				 
				$line_def_alt_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('alter_qnty')];
				$line_def_spot_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('spot_qnty')];
				
				$line_def_rescan_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('today_rescan')];
				$style_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];	

				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['qc_qty']+=($row[csf('qc_pass_qty')]+$row[csf('alter_qnty')]+$row[csf('reject_qnty')]+$row[csf('spot_qnty')]) ;			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['qc_pass_qty']+=$row[csf('qc_pass_qty')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['alter_qnty']+=$row[csf('alter_qnty')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['spot_qnty']+=$row[csf('spot_qnty')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['reject_qnty']+=$row[csf('reject_qnty')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['replace_qty']+=$row[csf('replace_qty')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['rej_replace']+=$row[csf('today_rescan')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['buyer_name']=$row[csf('buyer_name')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];			
				$data_array[$row[csf('floor_id')]][$slNo][$row[csf('sewing_line')]][$row[csf('style_ref_no')]]['line_name']=$line_name;
			
			}
		
		}
		unset($result);		
	
		// echo "<pre>";print_r($data_array);die();
		ksort($prod_line_arr);
	
		if(count($mst_id_arr)>0)
		{
			$pre_cost_id_cond= where_con_using_array($mst_id_arr,0,"d.id");	 
		}		
		
		
		$defect_type_id=" and  a.defect_type_id in (3,4) ";
		if($prod_type==11)$defect_type_id=" and  a.defect_type_id in (1,2)";
		
		$sql = "SELECT a.defect_type_id, a.defect_point_id, sum(a.defect_qty) as defect_qty,sum(d.reject_qnty) as reject_qnty, d.sewing_line,b.style_ref_no 
		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE b.id=c.job_id and d.po_break_down_id=c.id and d.id=a.mst_id $pre_cost_id_cond $defect_type_id and a.production_type=$prod_type and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and c.status_active in(1,2,3)  $prod_date_qc_cond $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond
		group by d.sewing_line ,b.style_ref_no ,a.defect_type_id, a.defect_point_id";
		
		// echo $sql;
		
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$prod_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]][$row[csf('defect_type_id')]][$row[csf('defect_point_id')]]+=$row[csf('defect_qty')];
			$tot_prod_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('defect_qty')];
			//$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
			$prod_def_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('defect_qty')];
			$prod_def_rej_qty_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]+=$row[csf('reject_qnty')];
			if($row[csf("defect_type_id")]==3)
			{
				$sew_fin_alter_defect_type_new[$row[csf('defect_point_id')]]=$sew_fin_alter_defect_type[$row[csf('defect_point_id')]];
			}
			if($row[csf("defect_type_id")]==4)
			{
				$sew_fin_spot_defect_type_new[$row[csf('defect_point_id')]]=$sew_fin_spot_defect_type[$row[csf('defect_point_id')]];
			}

			if($row[csf("defect_type_id")]==1)
			{
				$sew_fin_alter_defect_type_new[$row[csf('defect_point_id')]]=$sew_fin_alter_defect_type[$row[csf('defect_point_id')]];
			}
			if($row[csf("defect_type_id")]==2)
			{
				$sew_fin_spot_defect_type_new[$row[csf('defect_point_id')]]=$sew_fin_spot_defect_type[$row[csf('defect_point_id')]];
			}

			
		}
		unset($result);	
		//print_r($sew_fin_alter_defect_type_new);
		//var_dump($prod_line_arr);
		//echo $sql;
	
		if($type==1)
		{		
			if ($prod_type==5 || $prod_type==11)
			{
				$tbl_width=((count($sew_fin_alter_defect_type_new)+count($sew_fin_spot_defect_type_new))*120)+1260; 
			}			
			
			ob_start();
			?>
			<div style="width:<? echo $tbl_width+18;?>px;">
			<table cellspacing="0" width="<? echo $tbl_width;?>">
				<tr class="form_caption" style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?> (<? echo $production_type[$prod_type]; ?>)</td>
				</tr>
				<tr style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
					Company Name: <? 
						$company=str_replace("'","",$cbo_company_name)?$cbo_company_name:$cbo_working_company_id;
						echo $company_library[str_replace("'","",$company)]; 
					?>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<br /> 

	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" align="left">
	            <thead>
	                <tr>
	                    <th rowspan="2" width="30"><p>SL No</p></th>
	                    <th rowspan="2" width="80"><p>Line No</p></th>	                    
	                    <th rowspan="2" width="80"><p>Buyer</p></th>	                    
	                    <th rowspan="2" width="130"><p>Style</p></th>	                    
	                    <th rowspan="2" width="60"><p>Check Qty</p></th>	                    
	                    <th rowspan="2" width="60"><p>Good Qty</p></th>	                    
	                    <th rowspan="2" width="60"><p>DHU (%)</p></th>	                    
	                    <th rowspan="2" width="60"><p>Days of Run</p></th>
	                    <th colspan="<?=(count($sew_fin_alter_defect_type_new)+count($sew_fin_spot_defect_type_new))*2;?>"><p>All Name of Defects</p></th>


	                    <th width="60" rowspan="2"><p>Total Defect</p></th>
	                    <th width="60" rowspan="2"><p>Total Gmts Reject</p></th>
	                    <th width="60" rowspan="2"><p>Total Gmts Alter</p></th>
	                    <th width="60" rowspan="2"><p>Total Gmts Spot</p></th>
	                    <th width="60" rowspan="2"><p>Total Gmts Alter Replace</p></th>
	                    <th width="60" rowspan="2"><p>Total Reject Replace</p></th>
	                    <th width="60" rowspan="2"><p>Reject Balance to rectify</p></th>
	                    <th width="60" rowspan="2"><p>Alter Balance to rectify</p></th>	                    
	                    <th width="60" rowspan="2"><p>Defect % Against Check Qty</p></th>
	                    <th width="60" rowspan="2"><p>Defect % Against Defect Qty</p></th>
	                </tr>
	                <tr>
	                	<?
	                	foreach ($sew_fin_alter_defect_type_new as $dfkey => $dfval) 
	                	{
	                		?>
	                		<th width="60"><p><?=$dfval;?></p></th>
	                		<th width="60">%</th>
	                		<?
	                	}
	                	// spot
	                	foreach ($sew_fin_spot_defect_type_new as $dfkey => $dfval) 
	                	{
	                		?>
	                		<th width="60"><p><?=$dfval;?></p></th>
	                		<th width="60">%</th>
	                		<?
	                	}
	                	?>
	                </tr>
	            </thead>
	        </table>
	        
			<div style="max-height:350px; float:left; overflow-y:scroll;width:<? echo $tbl_width+18;?>px;" id="scroll_body" > 
				<table width="<? echo $tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="" align="left" >
				<?
				
				$i=1;
				$alter=3;
				$spot=4;
				if($prod_type==11) 
				{
					$alter=1;
					$spot=2;
				}

				$gr_chk_qty = 0;
				$gr_gd_qty = 0;
				$gr_def_qty = 0;
				$gr_rej_qty = 0;
				$gr_spt_qty = 0;
				$gr_rpls_qty = 0;
				$gr_altr_qty = 0;
				$gr_rej_rpls_qty = 0;
				$gr_rej_rctfy_qty = 0;
				$gr_altr_rctfy_qty = 0;
				$gr_chk_qty_dft_prsnt = 0;
				$gr_dft_qty_dft_prsnt = 0;
				$alter_qty_arr = array();
				$spot_qty_arr = array();
				foreach($data_array as $f_id=>$f_data)
				{
					ksort($f_data);
					foreach ($f_data as $sl_key => $sl_data) 
					{
						foreach ($sl_data as $l_key => $l_data) 
						{
							foreach ($l_data as $sty_key => $row) 
							{
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								$dhu = ($row['qc_qty']>0) ? ($prod_def_qty_arr[$l_key][$sty_key]/$row['qc_qty'])*100 : 0;
								$days_run = $style_wise_days[$sty_key][$l_key];
								$reject_qty = $line_def_rej_qty_arr2[$l_key][$sty_key];
								$alter_qty = $line_def_alt_qty_arr[$l_key][$sty_key];
								$rej_rectify_qty = $line_def_rej_qty_arr2[$l_key][$sty_key]- $row['rej_replace'];
								$alter_rectify_qty = $line_def_alt_qty_arr[$l_key][$sty_key] - $row['replace_qty'];

								$dft_ag_chk = ($row['qc_qty']>0) ? ($tot_prod_qty_arr[$l_key][$sty_key]/$row['qc_qty'])*100 : 0;
								$dft_ag_dft = ($prod_def_qty_arr[$l_key][$sty_key]>0) ? ($tot_prod_qty_arr[$l_key][$sty_key]/$prod_def_qty_arr[$l_key][$sty_key])*100 : 0;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><?=$i;?></td>
				                    <td width="80"><?=$row['line_name'];?></td>	                    
				                    <td width="80"><?=$buyerArr[$row['buyer_name']];?></td>	                    
				                    <td width="130"><?=$sty_key;?></td>	                    
				                    <td width="60" align="right"><?=number_format($row['qc_qty'],0);?></td>	                    
				                    <td width="60" align="right"><?=number_format($row['qc_pass_qty'],0);?></td>	                    
				                    <td width="60" align="right"><?=number_format($dhu,2);?></td>	                    
				                    <td width="60" align="right"><?=$days_run;?></td>
				                    <?
				                	foreach ($sew_fin_alter_defect_type_new as $dfkey => $dfval) 
				                	{
				                		$prsnt = ($row['qc_qty']>0) ? ($prod_qty_arr[$l_key][$sty_key][$alter][$dfkey]/$row['qc_qty'])*100 : 0;
				                		$check_qty_per_arr[$alter][$dfkey]['qty']+=$prsnt;
				                		if($prsnt>0)
				                		{
				                			$check_qty_per_arr[$alter][$dfkey]['line']++;
				                		}

				                		$dft_prsnt = ($prod_def_qty_arr[$l_key][$sty_key]>0) ? ($prod_qty_arr[$l_key][$sty_key][$alter][$dfkey]/$prod_def_qty_arr[$l_key][$sty_key])*100 : 0;
				                		$def_qty_per_arr[$alter][$dfkey]+=$dft_prsnt;
				                		if($dft_prsnt>0)
				                		{
				                			$def_qty_per_arr[$alter][$dfkey]['line']++;
				                		}
				                		?>
				                		<td align="right" width="60"><?=number_format($prod_qty_arr[$l_key][$sty_key][$alter][$dfkey],0);?></td>
				                		<td align="right" width="60"><?=number_format($prsnt,2);?></td>
				                		<?
				                		$alter_qty_arr[$dfkey] += $prod_qty_arr[$l_key][$sty_key][$alter][$dfkey];
				                	}
				                	// spot
				                	foreach ($sew_fin_spot_defect_type_new as $dfkey => $dfval) 
				                	{
				                		$prsnt = ($row['qc_qty']>0) ? ($prod_qty_arr[$l_key][$sty_key][$spot][$dfkey]/$row['qc_qty'])*100 : 0;
				                		$check_qty_per_arr[$spot][$dfkey]['qty']+=$def_qty_per;
				                		if($def_qty_per>0)
				                		{
				                			$check_qty_per_arr[$spot][$dfkey]['line']++;
				                		}

				                		$dft_prsnt = ($prod_def_qty_arr[$l_key][$sty_key]>0) ? ($prod_qty_arr[$l_key][$sty_key][$spot][$dfkey]/$prod_def_qty_arr[$l_key][$sty_key])*100 : 0;
				                		$def_qty_per_arr[$spot][$dfkey]['qty']+=$dft_prsnt;
				                		if($dft_prsnt>0)
				                		{
				                			$def_qty_per_arr[$spot][$dfkey]['line']++;
				                		}
				                		?>
				                		<td align="right" width="60"><?=number_format($prod_qty_arr[$l_key][$sty_key][$spot][$dfkey],0);?></td>
				                		<td align="right" width="60"><?=number_format($prsnt,2);?></td>
				                		<?
				                		$spot_qty_arr[$dfkey] += $prod_qty_arr[$l_key][$sty_key][$spot][$dfkey];
				                	} 
				                	?>
				                    <td align="right" width="60"><?=number_format(array_sum($prod_qty_arr[$l_key][$sty_key][$alter])+array_sum($prod_qty_arr[$l_key][$sty_key][$spot]),0);?></td>

				                    <td align="right" width="60"><?=number_format($reject_qty,0);?></td>
				                    <td align="right" width="60"><?=number_format($alter_qty,0);?></td>
				                    <td align="right" width="60"><?=number_format($row['spot_qnty'],0);?></td>
				                    <td align="right" width="60"><?=number_format($row['replace_qty'],0);?></td>
				                    <td align="right" width="60"><?=number_format($row['rej_replace'],0);?></td>
				                    <td align="right" width="60"><?=number_format($rej_rectify_qty,0);?></td>
				                    <td align="right" width="60"><?=number_format($alter_rectify_qty,0);?></td>	                    
				                    <td align="right" width="60"><?=number_format($dft_ag_chk,0);?></td>
				                    <td align="right" width="60"><?=number_format($dft_ag_dft,0);?></td>
								<?
								$i++;
								$gr_chk_qty += $row['qc_qty'];
								$gr_gd_qty += $row['qc_pass_qty'];
								$gr_def_qty += array_sum($prod_qty_arr[$l_key][$sty_key][$alter])+array_sum($prod_qty_arr[$l_key][$sty_key][$spot]);
								$gr_rej_qty += $reject_qty;
								$gr_spt_qty += $row['spot_qnty'];
								$gr_rpls_qty += $row['replace_qty'];
								$gr_altr_qty += $alter_qty;
								$gr_rej_rpls_qty += $row['rej_replace'];
								$gr_rej_rctfy_qty += $rej_rectify_qty;
								$gr_altr_rctfy_qty += $alter_rectify_qty;
								$gr_chk_qty_dft_prsnt += $dft_ag_chk;
								$gr_dft_qty_dft_prsnt += $dft_ag_dft;

								$tot_prod_qty += $tot_prod_qty_arr[$l_key][$sty_key];
								$tot_prod_def_qty += $prod_def_qty_arr[$l_key][$sty_key];
							}
						}
					}
				}			

				?>
				</table>
	        </div>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tfoot>
	                <tr>
	                    <th width="30"></th>
	                    <th width="80"></th>	                    
	                    <th width="80"></th>	                    
	                    <th width="130">Total</th>	                    
	                    <th width="60"><?=number_format($gr_chk_qty,0);?></th>	                    
	                    <th width="60"><?=number_format($gr_gd_qty,0);?></th>	                    
	                    <th width="60"><?=number_format($a,0);?></th>	                    
	                    <th width="60"></th>
	                    <?
	                	foreach ($sew_fin_alter_defect_type_new as $dfkey => $dfval) 
	                	{
	                		?>
	                		<th width="60"><?=$alter_qty_arr[$dfkey];?></th>
	                		<th width="60"></th>
	                		<?
	                	}
	                	// spot
	                	foreach ($sew_fin_spot_defect_type_new as $dfkey => $dfval) 
	                	{
	                		?>
	                		<th width="60"><?=$spot_qty_arr[$dfkey];?></th>
	                		<th width="60"></th>
	                		<?
	                	}
	                	?>
	                    <th width="60"><?=number_format($gr_def_qty,0);?></th>
	                    <th width="60"><?=number_format($gr_rej_qty,0);?></th>
	                    <th width="60"><?=number_format($gr_altr_qty,0);?></th>
	                    <th width="60"><?=number_format($gr_spt_qty,0);?></th>
	                    <th width="60"><?=number_format($gr_rpls_qty,0);?></th>
	                    <th width="60"><?=number_format($gr_rej_rpls_qty,0);?></th>
	                    <th width="60"><?=number_format($gr_rej_rctfy_qty,0);?></th>
	                    <th width="60"><?=number_format($gr_altr_rctfy_qty,0);?></th>	                    
	                    <th width="60"><?=number_format((($tot_prod_qty/$gr_chk_qty)*100),2);?></th>
	                    <th width="60"><?=number_format((($tot_prod_qty/$tot_prod_def_qty)*100),2);?></th>
	                </tr>	                
	            </tfoot>
			</table>
	        </div>
	        <div style="width:100%; float:left; margin-top:25px;">
				<div align="center" style="width:850px; height:500px;  margin-left:20px; border:solid 1px">
					<table style="margin-left:60px; font-size:12px" align="left">
					<tr>
						<td align="left" bgcolor="#4f81bd" width="10"></td>
						<td><? echo $production_type[$prod_type]; ?> DEFECT % AGAINST CHECK QTY</td>
					</tr>
					</table>
					<div style="display: none;" id="canvas_div"></div>
					<canvas id="canvas" height="400" width="800"></canvas>
				</div>
			</div>
	        
	        
			<div style="width:100%; float:left; margin-top:25px;">
				<div align="center" style="width:850px; height:500px;  margin-left:20px; border:solid 1px">
					<table style="margin-left:60px; font-size:12px" align="left">
					<tr>
	                    <td align="left" bgcolor="#8064a2" width="10"></td>
						<td><? echo $production_type[$prod_type]; ?> DEFECT % AGINST DEFECT QTY</td>
					</tr>
					</table>
					<div style="display: none;" id="canvas2_div"></div>
					<canvas id="canvas2" height="400" width="800"></canvas>
				</div>
			</div>
	        
			<?
			$check_qty_per_arr_new = array();
			$def_qty_per_arr_new = array();
			foreach ($check_qty_per_arr as $alt_key => $alt_value) 
			{
				foreach ($alt_value as $dft_key => $val) 
				{
					$tot = $val['qty']/$val['line'];

					$check_qty_per_arr_new[$alt_key][$dft_key]=number_format($tot,2);
					// echo $val['qty']/$val['line']."<br>";
				}
				
			}
			foreach ($def_qty_per_arr as $alt_key => $alt_value) 
			{
				foreach ($alt_value as $dft_key => $val) 
				{
					$tot = $val['qty']/$val['line'];
					$def_qty_per_arr_new[$alt_key][$dft_key]=number_format($tot,2);
				}
				
			}
			// =================================

			foreach ($check_qty_per_arr as $spt_key => $alt_value) 
			{
				foreach ($alt_value as $dft_key => $val) 
				{
					$tot = $val['qty']/$val['line'];
					$check_qty_per_arr_new[$spt_key][$dft_key]=number_format($tot,2);
				}
				
			}
			foreach ($def_qty_per_arr as $spt_key => $alt_value) 
			{
				foreach ($alt_value as $dft_key => $val) 
				{
					$tot = $val['qty']/$val['line'];
					$def_qty_per_arr_new[$spt_key][$dft_key]=number_format($tot,2);
				}
				
			}
			//$reject_qty_type=array(0=>"Reject");
			$bar_arr=array();$val_arr=array();
			foreach($sew_fin_alter_defect_type_new as $kk=>$vv)
			{
				$bar_arr[]=$vv;
				$val_arr[]=number_format($check_qty_per_arr_new[$alter][$kk],2);
				$val_arr2[]=number_format($def_qty_per_arr_new[$alter][$kk],2);
			}
			
			foreach($sew_fin_spot_defect_type_new as $key=>$val)
			{
				$bar_arr[]=$val;
				$val_arr[]=number_format($check_qty_per_arr_new[$spot][$key],2);
				$val_arr2[]=number_format($def_qty_per_arr_new[$spot][$key],2);
			}
			
			/*	foreach($reject_qty_type as $keyr=>$valr)
			{
				$bar_arr[]=$valr;
				//$val_arr[]=$reject_qty_arr[$keyr];
				$val_arr[]=22;
			}*/
			
			$bar_arr= json_encode($bar_arr);
			$val_arr= json_encode($val_arr);
			$val_arr2= json_encode($val_arr2);
			?>
		   <script>
				var barChartData = {
				labels : <? echo $bar_arr; ?>,
				datasets : [
						{
							fillColor : "#4f81bd",
							strokeColor : "rgba(220,220,220,0.8)",
							highlightFill: "rgb(255,99,71)",
							highlightStroke: "rgba(220,220,220,1)",
							data : <? echo $val_arr; ?>
						}
						
					]
				}
				
				var ctx = document.getElementById("canvas").getContext("2d");
				window.myBar = new Chart(ctx).Bar(barChartData, {
				responsive : true
				});
				//----------------------------------------------------------------------------------------
				var barChartData2 = {
				labels : <? echo $bar_arr; ?>,
				datasets : [
						{
							fillColor : "#8064a2",
							strokeColor : "rgba(220,220,220,0.8)",
							highlightFill: "rgb(255,99,71)",
							highlightStroke: "rgba(220,220,220,1)",
							data : <? echo $val_arr2; ?>
						}
						
					]
				}
				
				var ctx = document.getElementById("canvas2").getContext("2d");
				window.myBar = new Chart(ctx).Bar(barChartData2, {
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
	else if(str_replace("'","",$cbo_production_type)==1)
	{
	   
		$sql = "SELECT 
		f.mst_id,
		b.buyer_name,
		b.style_ref_no,
		c.id as po_id,
		c.po_number,
		c.grouping,
		c.po_quantity,
		sum(f.production_qnty) as qc_pass_qty, 
		sum(f.alter_qty) as alter_qnty, 
		sum(f.reject_qty) as reject_qnty, 
		sum(f.spot_qty) as spot_qnty,
		sum(f.replace_qty) as replace_qty
		
		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f
		WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id    and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3) and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id
		$company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond
		group by f.mst_id,b.buyer_name,b.style_ref_no,c.id ,c.po_number,c.grouping,c.po_quantity
		";	 

	    //echo  $sql;	
			
			
			
		$result = sql_select($sql);
		foreach($result as $row)
		{
			
			$qcDataArr[$row[csf('po_id')]]=array(
				buyer_name=>$row[csf('buyer_name')],
				style_ref_no=>$row[csf('style_ref_no')],
				po_number=>$row[csf('po_number')],
				grouping=>$row[csf('grouping')],
				po_quantity=>$row[csf('po_quantity')],
				reject_qnty=>$row[csf('reject_qnty')],
				qc_pass_qty=>$row[csf('qc_pass_qty')],
				replace_qty=>$row[csf('replace_qty')]
			);
			$mst_id_arr[$row[csf('mst_id')]]=$row[csf('mst_id')];
			$qc_pass_qty_arr[$row[csf('po_id')]]+=$row[csf('qc_pass_qty')];
			$rej_qty_arr[$row[csf('po_id')]]+=$row[csf('reject_qnty')];
			$replace_qty_arr[$row[csf('po_id')]]+=$row[csf('replace_qty')];
			
			
			//$prod_line_arr[$row[csf('po_id')]]=$line_name;
			//$qc_check_qty_arr[$row[csf('po_id')]]+=($row[csf('qc_pass_qty')]+$row[csf('reject_qnty')])-$row[csf('replace_qty')];
			$qc_check_qty_arr[$row[csf('po_id')]]+=($row[csf('qc_pass_qty')]+$row[csf('reject_qnty')])-($row[csf('replace_qty')]);
			
			//$style_arr[$row[csf('po_id')]][$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];
		
		}
		unset($result);	
	
	 	//var_dump($mst_id_arr);die;
	
	
		if(count($mst_id_arr)>995)
		{
			$pre_cost_id_app_byuser_chunk_arr=array_chunk(explode(",",$mst_id_arr),995) ;
			foreach($pre_cost_id_app_byuser_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$pre_cost_id_cond.=" and d.id  in($chunk_arr_value)";	
			}
		}
		else
		{
			$chunk_arr_value=implode(",",$mst_id_arr);
			$pre_cost_id_cond=" and d.id  in($chunk_arr_value)";	 
		}
		
		
		$sql = "SELECT a.defect_type_id, a.defect_point_id, sum(a.defect_qty) as defect_qty, a. po_break_down_id 
		FROM pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE d.id=a.mst_id $pre_cost_id_cond and  a.defect_type_id in (3) and a.production_type=$prod_type and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 $prod_date_qc_cond
		group by a. po_break_down_id ,a.defect_type_id, a.defect_point_id
		";	
		
		 //echo $sql;
		
		$result = sql_select($sql);
		$cutting_qc_reject_type_new = array();
		foreach($result as $row)
		{
			$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('po_break_down_id')]][$row[csf('defect_point_id')]]=$row[csf('defect_qty')];
			$prod_def_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('defect_qty')];
			$cutting_qc_reject_type_new[$row[csf('defect_point_id')]] = $cutting_qc_reject_type[$row[csf('defect_point_id')]];
		}
		unset($result);	
		 
		// var_dump($prod_qty_arr[3]);
		//echo $sql;
		
		if($type==1)
		{	
		
			if ($prod_type==1)
			{
				$colspan=count($cutting_qc_reject_type_new);
				$tbl_width=($colspan*80)+1370;
			}
			
			
			
			ob_start();
			?>
			<div style="width:<? echo $tbl_width+18;?>px;">
			<table cellspacing="0" width="<? echo $tbl_width;?>">
				<tr class="form_caption" style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?> (<? echo $production_type[$prod_type]; ?>)</td>
				</tr>
				<tr style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
					Company Name: <? 
						$company=str_replace("'","",$cbo_company_name)?$cbo_company_name:$cbo_working_company_id;
						echo $company_library[str_replace("'","",$company)]; 
					?>                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<br /> 

	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" align="left">
	            <thead>
	                <tr>
	                    <th width="30" rowspan="2"><p>SL No</p></th>
	                    <th width="130" rowspan="2"><p>Buyer</p></th>
	                    <th width="130" rowspan="2"><p>Style No</p></th>
	                    <th width="150" rowspan="2"><p>Order No</p></th>
	                    <th width="100" rowspan="2"><p>Internal Ref.</p></th>
	                    <th width="130" rowspan="2"><p>Order Qty</p></th>
	                    <th width="130" colspan="<? echo $colspan;?>"><p>Name of all Defects</p></th>
	                    <th width="100" rowspan="2"><p>Total Defected pannels</p></th>
	                    <th width="100" rowspan="2"><p>Order defect % to total defect</p></th>
	                    <th width="100" rowspan="2"><p>Total Gmts Reject</p></th>
	                    
	                    <th width="100" rowspan="2"><p>Reject% To Total QC</p></th>
	                    <th width="100" rowspan="2"><p>Total Gmts Replace</p></th>
	                    <th width="100" rowspan="2"><p>Total Qc Pass Qty</p></th>
	                    <th rowspan="2"><p>Total Check Qty</p></th>
	                </tr>
	                <tr>
	                    <?php foreach($cutting_qc_reject_type_new as $sfad_id=>$sfad_name){
							echo '<th width="80"><p>'.$sfad_name.'</p></th>';
						}?>
	                </tr>
	            </thead>
	        </table>

	        
	        <div style="max-height:350px; float:left; overflow-y:scroll;width:<? echo $tbl_width+18;?>px;" id="scroll_body" > 
			<table width="<? echo $tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="" align="left" >
			<?
			
			$i=1;

			foreach($qcDataArr as $po_id=>$rows)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
		            <td width="130"><? echo $buyerArr[$rows[buyer_name]];?></td>
		            <td width="130"><? echo $rows[style_ref_no];?></td>
		            <td width="150"><? echo $rows[po_number];?></td>
		            <td width="100"><? echo $rows[grouping];?></td>
		            <td width="130" align="right"><? echo $rows[po_quantity];$tot_order_qty+=$rows[po_quantity];?></td>
					<?php foreach($cutting_qc_reject_type_new as $sfad_id=>$sfad_name){
						$defect_qty_arr[$sfad_id]+=$prod_qty_arr[3][$po_id][$sfad_id];
						echo '<td width="80" align="right">'.$prod_qty_arr[3][$po_id][$sfad_id].'</td>';
					}?>
		            <td width="100" align="right">
					<? 
						echo array_sum($prod_qty_arr[3][$po_id]);
					?>
		            </td>
		            <td width="100" align="right">
		            <? 
						$order_def_par_sum+=array_sum($prod_qty_arr[3][$po_id])/array_sum($prod_def_qty_arr)*100;
						echo number_format(array_sum($prod_qty_arr[3][$po_id])/array_sum($prod_def_qty_arr)*100,2);
					?>
		            </td>
		            <td width="100" align="right">
					<? 
						$totalGmtsReject+=$rej_qty_arr[$po_id];
						echo $rej_qty_arr[$po_id]//$rows[reject_qnty];
					?>
		            </td>

		             


		            <td width="100" align="right"><? echo number_format($rej_qty_arr[$po_id]/$qc_check_qty_arr[$po_id]*100,2);?></td>

		            <td width="100" align="right">
					<? 
						$totalGmtsReplace+=$replace_qty_arr[$po_id];
						echo $replace_qty_arr[$po_id]//$rows[reject_qnty];
					?>
		            </td>

		            <td width="100" align="right">
					<? 
						$tot_qc_qty+=$qc_pass_qty_arr[$po_id];
						echo $qc_pass_qty_arr[$po_id]//$rows[qc_pass_qty];
					?>
		            </td>
		            <td align="right">
					<? 
						$tot_qc_check_qty+=$qc_check_qty_arr[$po_id];
						echo $qc_check_qty_arr[$po_id];
					?>
		            </td>
				</tr>
				<?
				$i++;
			}

			?>
			</table>
	        </div>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tfoot>
	                <tr>
	                    <th width="30"></th>
	                    <th width="130"></th>
	                    <th width="130"></th>
	                    <th width="150"></th>
	                    <th width="100">Total:</th>
	                    <th width="130" align="right"><? echo $tot_order_qty;?></th>
	                    <?php foreach($cutting_qc_reject_type_new as $sfad_id=>$sfad_name){
							echo '<th width="80" align="right">'.$defect_qty_arr[$sfad_id].'</th>';
	                    }?>
	                    <th width="100"><? echo array_sum($prod_def_qty_arr);?></th>
	                    <th width="100"><? echo number_format($order_def_par_sum,2);?></th>
	                    <th width="100"><? echo $totalGmtsReject;?></th>

	                    <th width="100"><? echo number_format($totalGmtsReject/$tot_qc_check_qty*100,2);?></th>
	                    <th width="100"><? echo $totalGmtsReplace;?></th>
	                    <th width="100"><? echo $tot_qc_qty;?></th>
	                    <th><? echo $tot_qc_check_qty;?></th>
	                </tr>
	                <tr>
	                    <th width="130" colspan="7">Defect % against Defect Qty</th>
	                    <?php foreach($cutting_qc_reject_type_new as $sfad_id=>$sfad_name){
	                        $def_par_sum_arr[$sfad_id]=$defect_qty_arr[$sfad_id]/array_sum($prod_def_qty_arr)*100;
	                        $def_par_sum+=$defect_qty_arr[$sfad_id]/array_sum($prod_def_qty_arr)*100;
							echo '<th width="80" align="right">'.number_format($defect_qty_arr[$sfad_id]/array_sum($prod_def_qty_arr)*100,2).'</th>';
	                    }?>
	                    <th width="100"><? echo number_format($def_par_sum,2);?></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    
	                    <th></th>
	                </tr>
	                
	            </tfoot>
			</table>
	        
	        
	        </div>
			
	        
	        
	        &nbsp;
	        
			<div style="width:100%; float:left; margin-top:25px;">
				<div align="center" style="width:850px; height:500px;  margin-left:20px; border:solid 1px">
					<table style="margin-left:60px; font-size:12px" align="left">
					<tr>
	                    <td align="left" bgcolor="#8064a2" width="10"></td>
						<td><? echo $production_type[$prod_type]; ?> DEFECT % AGINST DEFECT QTY</td>
					</tr>
					</table>
					<div style="display: none;" id="canvas2_div"></div>
					<canvas id="canvas2" height="400" width="800"></canvas>

					<div style="display: none;" id="canvas_div"></div>
					<canvas id="canvas" height="400" width="800" style="display: none;"></canvas>
					
				</div>
			</div>
	        
	        
	        
	        
			<?
			//$reject_qty_type=array(0=>"Reject");
			$bar_arr=array();$val_arr=array();
			foreach($cutting_qc_reject_type_new as $kk=>$vv)
			{
				$bar_arr[]=$vv;
				//$val_arr[]=number_format($check_qty_per_arr[3][$kk],2);
				$val_arr2[]=number_format($def_par_sum_arr[$kk],2);
			}
			
			/*foreach($sew_fin_spot_defect_type as $key=>$val)
			{
				$bar_arr[]=$val;
				//$val_arr[]=number_format($check_qty_per_arr[4][$key],2);
				//$val_arr2[]=number_format($def_qty_per_arr[4][$key],2);
			}*/
			
			
			$bar_arr= json_encode($bar_arr);
			$val_arr= json_encode($val_arr);
			$val_arr2= json_encode($val_arr2);
			?>
		   <script>
				
				//----------------------------------------------------------------------------------------
				var barChartData2 = {
				labels : <? echo $bar_arr; ?>,
				datasets : [
						{
							fillColor : "#8064a2",
							strokeColor : "rgba(220,220,220,0.8)",
							highlightFill: "rgb(255,99,71)",
							highlightStroke: "rgba(220,220,220,1)",
							data : <? echo $val_arr2; ?>
						}
						
					]
				}
				
				var ctx = document.getElementById("canvas2").getContext("2d");
				window.myBar = new Chart(ctx).Bar(barChartData2, {
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

}






