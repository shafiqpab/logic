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
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_qc_analysis_report_controller', $data+'_'+this.value+'_'+document.getElementById('cbo_production_type').value, 'load_drop_down_floor', 'floor_td' );",0 );
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
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	/*if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}*/
	
	
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
	$prod_qty_arr=array(); $defect_line_arr=array(); $order_colspan_arr=array();	
	
	if(str_replace("'","",$cbo_production_type)==5 || str_replace("'","",$cbo_production_type)==11)
	{

		$days_run_sql = "SELECT d.sewing_line,b.style_ref_no,c.grouping,d.production_date FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f	WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3)  $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond 
		group by d.sewing_line,b.style_ref_no,d.production_date,c.grouping	";	
		//echo $days_run_sql;die();
		foreach(sql_select($days_run_sql) as $vals)
		{
			$style_wise_days[$vals[csf('style_ref_no')]][$vals[csf('sewing_line')]]+=1;
			$int_ref_wise_days[$vals[csf('grouping')]][$vals[csf('grouping')]]+=1;
		}  
		//print_r($style_wise_days);
		// echo "<pre>";print_r($inter_wise_days);
		if(str_replace("'","",$cbo_production_type)==5)
		{
			$sql = "SELECT f.mst_id,c.grouping,b.style_ref_no, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo, 
			sum(f.production_qnty) as qc_pass_qty, 
			sum(f.alter_qty) as alter_qty,
			sum(f.reject_qty) as reject_qty,
			 sum(case when f.is_rescan>=0 then  f.reject_qty else 0 end )-sum(case when f.is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty_bk,
			sum(case when is_rescan>=1 then  f.reject_qty else 0 end )  as today_reject_qnty,
			 sum(f.spot_qty) as spot_qnty, 
			 sum(f.replace_qty) as replace_qty,
			 sum(case when f.is_rescan>=1 then f.REPLACE_QTY else 0 end ) as today_replace_qty,
			 sum(case when f.is_rescan>=1 then f.spot_qty else 0 end ) as today_spot_qty,
			 sum(case when f.is_rescan>=1 then f.alter_qty else 0 end ) as today_alter_qty,
			 sum(case when is_rescan>=1 then f.production_qnty else 0 end ) as today_rescan
			  FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst  and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,d.sewing_line, d.prod_reso_allo,c.grouping ";
			//echo $sql;die;
			$reject_sql = "SELECT f.bundle_no, f.mst_id, b.style_ref_no,c.grouping, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo,    sum(case when f.is_rescan>=0 then  f.reject_qty else 0 end ) as reject_qnty,  sum(case when f.is_rescan>=1 then  f.reject_qty else 0 end ) as today_reject_qnty,sum(case when f.is_rescan>=1 then f.production_qnty else 0 end ) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1  and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond  $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond  group by f.bundle_no, f.mst_id,b.style_ref_no,d.sewing_line, d.prod_reso_allo,c.grouping ";
			// echo $reject_sql;die;
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
					$line_def_rej_qty_arr2[$row[csf('sewing_line')]]['reject_qnty']+=$row[csf('reject_qnty')];

					$line_def_replace_qty_arr2[$row[csf('sewing_line')]]+=$row[csf('replace_qty')]; 
				}

			}
			// echo "<pre>";print_r($line_def_rej_qty_arr2);die;

		}
		elseif (str_replace("'","",$cbo_production_type)==11) 
		{
			$sql = "SELECT f.mst_id, b.style_ref_no,c.grouping, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo, sum(f.production_qnty) as qc_pass_qty, d.alter_qnty as alter_qnty, sum(case when is_rescan=0 then  f.reject_qty else 0 end )-sum(case when is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty, d.spot_qnty as spot_qnty, sum(f.replace_qty) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,c.grouping,d.sewing_line, d.prod_reso_allo,d.alter_qnty,d.spot_qnty ";
		} 
   		//echo  $sql;	
   		// echo "<pre>";print_r($line_def_rej_qty_arr2);
		$result = sql_select($sql);
		$count=0;
		$int_ref_arr = array();
		foreach($result as $row)
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
				$mst_id_arr[$row[csf('mst_id')]]=$row[csf('mst_id')];
				$prod_line_arr[$row[csf('sewing_line')]]=$line_name;			//$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
				$qc_qty_arr[$row[csf('sewing_line')]]+=(($row[csf('qc_pass_qty')]+$row[csf('reject_qty')]+$row[csf('alter_qty')]+$row[csf('spot_qty')])-$row[csf('replace_qty')]) ;
				$qc_pass_arr[$row[csf('sewing_line')]]+= $row[csf('qc_pass_qty')];
				//$qc_qty_arr[$row[csf('sewing_line')]]+=$row[csf('qc_pass_qty')];
				//$prod_def_qty_arr[$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
			 
				$line_def_rej_qty_arr[$row[csf('sewing_line')]]+=$row[csf('reject_qnty')];
				$line_def_replace_qty_arr[$row[csf('sewing_line')]]+=$row[csf('replace_qty')];
			 
			 
				$line_def_alt_qty_arr[$row[csf('sewing_line')]]+=$row[csf('alter_qnty')];
				$line_def_spot_qty_arr[$row[csf('sewing_line')]]+=$row[csf('spot_qnty')];
				
				$line_def_rescan_qty_arr[$row[csf('sewing_line')]]+=(($row[csf('today_rescan')]+$row[csf('today_reject_qnty')]+$row[csf('today_alter_qty')]+$row[csf('today_spot_qty')])-$row[csf('today_replace_qty')]);
				$style_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];	
				// $int_ref_arr[$row[csf('sewing_line')]]['int_ref'] .= $row[csf('grouping')]."**";	
				if($row[csf('grouping')]!="")
				{
					$int_ref_arr[$row[csf('sewing_line')]][$row[csf('grouping')]]=$row[csf('grouping')];
				}
				// echo $row[csf('grouping')]."<br>";			
			
			}
		
		}
		unset($result);		
		// echo "<pre>";print_r($qc_pass_arr);die;
	
		//var_dump($line_def_rej_qty_arr);
		ksort($prod_line_arr);
	
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
		
		/*$sql = "SELECT a.defect_type_id, a.defect_point_id, sum(a.defect_qty) as defect_qty,sum(d.reject_qnty) as reject_qnty, d.sewing_line 
		FROM pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE d.id=a.mst_id $pre_cost_id_cond and  a.defect_type_id in (3,4) and a.production_type=$prod_type and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 $prod_date_qc_cond
		group by d.sewing_line ,a.defect_type_id, a.defect_point_id";	*/
		$defect_type_id=" and  a.defect_type_id in (3,4) ";
		if($prod_type==11)$defect_type_id=" and  a.defect_type_id in (1,2)";
		
		$sql = "SELECT a.defect_type_id, a.defect_point_id, sum(a.defect_qty) as defect_qty,sum(d.reject_qnty) as reject_qnty, d.sewing_line 
		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE b.job_no=c.job_no_mst and d.po_break_down_id=c.id and d.id=a.mst_id $pre_cost_id_cond $defect_type_id and a.production_type=$prod_type and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and c.status_active in(1,2,3)  $prod_date_qc_cond $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond
		group by d.sewing_line ,a.defect_type_id, a.defect_point_id";
		
		// echo $sql;
		
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]=$row[csf('defect_qty')];
			//$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
			$prod_def_qty_arr[$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
			$prod_def_rej_qty_arr[$row[csf('sewing_line')]]+=$row[csf('reject_qnty')];
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
				$tbl_width=(count($prod_line_arr)*80)+460;
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
			
		
			<br>

	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" align="left">
	            <thead>
	                <tr>
	                    <th width="30" rowspan="7">SL No</th>
	                    <th width="130">Line No</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_name){echo '<th width="80">'.$line_name.'</th>';}?>
	                    <th width="100"  title="Total sewing output=(total check-total reject-total alter-total spot)">Total Check</th>
	                    <th width="100" rowspan="6">DEFECT % AGAINST CHECK QTY</th>
	                    <th rowspan="6">DEFECT % AGAINST DEFECT QTY</th>
	                </tr>
	                <tr>
	                    <th style="background:#93cAAA" title="( Qc Pass + Alter+ Reject + Spot)">Check Qty</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_id){
	                    	$tot_qc_pass= $qc_pass_arr[$line_id];
	                    	$tot_qc_pass=array_sum($qc_pass_arr);
							echo '<th width="80" style="background:#93cAAA">'.$qc_qty_arr[$line_id].'</th>';
						}?>
	                    <th title="Total Sewing Output =<? echo $tot_qc_pass;?>"><? echo array_sum($qc_qty_arr);?></th>
	                </tr>



	                <tr>
	                    <th style="background:#93cAAA"  >Good Qty</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_id){
	                    	$tot_qc_pass= $qc_pass_arr[$line_id];
	                    	$tot_qc_pass=array_sum($qc_pass_arr);
							echo '<th width="80" style="background:#93cAAA">'.$qc_pass_arr[$line_id].'</th>';
						}?>
	                    <th title="Total Sewing Output =<? echo $tot_qc_pass;?>"><? echo array_sum($qc_pass_arr);?></th>
	                </tr>
	                <tr>
	                    <th style="background:#DDD">DHU (%)</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_id){
							$dhu=($prod_def_qty_arr[$line_id]/$qc_qty_arr[$line_id])*100;
							echo '<th width="80" style="background:#DDD">'.number_format($dhu,2).'</th>';
						
						}?>
	                     <th title="Defect qty/QC qty*100"><? echo number_format(array_sum($prod_def_qty_arr)/array_sum($qc_qty_arr)*100,2);?></th>
	                </tr>

	                 <tr>
	                    <th style="background:#DDD">Days of Run</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_id)
	                    {
	                    	$styles=$style_arr[$line_id];
							$int_ref=$int_ref_arr[$line_id];
	                    	$days="";
	                    	foreach($styles as $style_id)
	                    	{
	                    		if($days=="") $days.=$style_wise_days[$style_id][$line_id];
	                    		else $days.=','.$style_wise_days[$style_id][$line_id];

	                    	}
							 
							 
							echo '<th width="80" style="background:#DDD">'.$days.'</th>';
						
						}?>
	                     <th>&nbsp;</th>
	                </tr>


	                
	                <tr>
	                    <th style="background:#F96">STYLE NO</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_id){
							echo '<th width="80" style="background:#F96"><p>'.implode(',',$style_arr[$line_id]).'</p></th>';
						}?>
	                    <th><? ?></th>
	                </tr>
					
	                <tr>
	                    <th style="background:#F96">IR/IB</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_id){
							echo '<th width="80" style="background:#F96"><p>'.implode($int_ref_arr[$line_id]).'</p></th>';
						}?>
	                    <th><? ?></th>
	                </tr>
	                
	                <tr>
	                    <th> ALL NAME OF DEFECTS</th>
	                    <th colspan="<? echo count($prod_line_arr)+3;?>"></th>
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
			$defect_top5_arr=array();

			foreach($sew_fin_alter_defect_type_new as $sfad_id=>$sfad_name){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="30" align="center"><? echo $i; ?></td>
				<td width="130"><p><? echo $sfad_name;?></p></td>
				<?php foreach($prod_line_arr as $line_id=>$line_id){
					
					$styles=array_unique(explode(",",$style_arr[$line_id]));
					$line_def_rej_qty=0;
					foreach($styles as $sid)
					{
						$line_def_rej_qty+=$line_def_rej_qty_arr[$line_id][$sid];
					}
					
					echo '<td width="80" align="right" title="'.$alter.','.$sfad_id.','.$line_id.'">'.$prod_qty_arr[$alter][$sfad_id][$line_id].'</td>';
				}?>
				<td width="100" align="right" bgcolor="#93cddd"><? echo array_sum($prod_qty_arr[$alter][$sfad_id]);?></td>
				<td width="100" align="right" bgcolor="#CCC">
				<? 
					$check_qty_per=array_sum($prod_qty_arr[$alter][$sfad_id])/array_sum($qc_qty_arr)*100;
					$check_qty_per_arr[$alter][$sfad_id]=$check_qty_per;
					$tot_check_qty_per+=$check_qty_per;
					echo number_format($check_qty_per,2);
				?>
	            </td>
				<td align="right" bgcolor="#e6b9bb">
				<? 
					$def_qty_per=array_sum($prod_qty_arr[$alter][$sfad_id])/array_sum($prod_def_qty_arr)*100;
					$def_qty_per_arr[$alter][$sfad_id]=$def_qty_per;
					$tot_def_qty_per+=$def_qty_per;
					echo number_format($def_qty_per,2); 
				?>
	            </td>
			</tr>
			<?
			$i++;
				$defect_top5_arr[$sfad_name]['qty']=array_sum($prod_qty_arr[$alter][$sfad_id]);
				$defect_top5_arr[$sfad_name]['prsnt']=$def_qty_per;
				$defect_top5_arr[$sfad_name]['chk_qty']=array_sum($qc_qty_arr);
			} 
			arsort($defect_top5_arr) ;
			//echo"<pre>";print_r($defect_top5_arr);die;
			//if($prod_type==11)$sew_fin_spot_defect_type_new=$sew_fin_spot_defect_type;
			

			foreach($sew_fin_spot_defect_type_new as $sfsd_id=>$sfsd_name)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td ><p><? echo $sfsd_name;?></p></td>
					<?php foreach($prod_line_arr as $line_id=>$line_id){
						// $qty = 0;
						echo '<td width="80" align="right">'.$prod_qty_arr[$spot][$sfsd_id][$line_id].'</td>';
						$qty = $prod_qty_arr[$spot][$sfsd_id][$line_id];
					}?>
					<td align="right" bgcolor="#93cddd"><? echo array_sum($prod_qty_arr[$spot][$sfsd_id]);?></td>
					<td align="right" bgcolor="#CCC">
					<? 
						$check_qty_per=array_sum($prod_qty_arr[$spot][$sfsd_id])/array_sum($qc_qty_arr)*100;
						$check_qty_per_arr[$spot][$sfsd_id]=$check_qty_per;
						$tot_check_qty_per+=$check_qty_per;
						echo number_format($check_qty_per,2);
					?>
					</td>
					<td align="right" bgcolor="#e6b9bb">
					<? 
						$def_qty_per=array_sum($prod_qty_arr[$spot][$sfsd_id])/array_sum($prod_def_qty_arr)*100;
						$def_qty_per_arr[$spot][$sfsd_id]=$def_qty_per;
						$tot_def_qty_per+=$def_qty_per;
						echo number_format($def_qty_per,2); 
					?>
					</td>
				</tr>
				<?
				$i++;
				$defect_top5_arr[$sfsd_name]['qty']=array_sum($prod_qty_arr[$spot][$sfsd_id]);
				$defect_top5_arr[$sfsd_name]['prsnt']=$def_qty_per;
				$defect_top5_arr[$sfsd_name]['chk_qty']=array_sum($qc_qty_arr);
			}
			arsort($defect_top5_arr) ;
			?>
			</table>
	        </div>
			<?//echo "<pre>";print_r($defect_top5_arr);?>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tfoot>
	            <tr>
	                <th width="30"></th>
	                <th width="130">Total</th>
	                <?php foreach($prod_line_arr as $line_id=>$line_id){
	                    echo '<th width="80" align="right">'.$prod_def_qty_arr[$line_id].'</th>';
	                }?>
	                <th width="100" align="right"><? echo array_sum($prod_def_qty_arr);?></th>
	                <th width="100" align="right"><? echo number_format($tot_check_qty_per,2);?></th>
	                <th align="right"><? echo number_format($tot_def_qty_per,2);?></th>
	            </tr>
	            <tr>
	                <th width="30"></th>
	                <th width="130">Total Gmts Reject</th>
	                <?php 
	                if($prod_type==5)
	                {
	                	$total_rej_qty=0;
	                	foreach($prod_line_arr as $line_id=>$line_id)
	                	{					 
	                		$tot_rej_qty=$line_def_rej_qty_arr2[$line_id]['reject_qnty'];
	                		$total_rej_qty+=$tot_rej_qty;				 

	                		echo '<th width="80" align="right">'.$tot_rej_qty.'</th>';
	                	}

	                }
	                else
	                {
	                	$total_rej_qty=0;
	                	foreach($prod_line_arr as $line_id=>$line_id)
	                	{					 
	                		$tot_rej_qty=$line_def_rej_qty_arr[$line_id];
	                		$total_rej_qty+=$tot_rej_qty;				 
	                		
	                		echo '<th width="80" align="right">'.$tot_rej_qty.'</th>';
	                	}
	                }
					
	                ?>
	                <th width="100" align="right"><? echo $total_rej_qty;?></th>
	                <th width="100" align="right"><? //echo number_format($tot_check_qty_per,2);?></th>
	                <th align="right"><? //echo number_format($tot_def_qty_per,2);?></th>
	            </tr>


	             <tr>
	                <th width="30"></th>
	                <th width="130">Total Gmts Alter</th>
	                <?php 
					$total_alt_qty=0;
					foreach($prod_line_arr as $line_id=>$line_id){
							
						$tot_rej_qty=$line_def_alt_qty_arr[$line_id];
						$total_alt_qty+=$tot_rej_qty;
	                    echo '<th width="80" align="right">'.$tot_rej_qty.'</th>';
	                }?>
	                <th width="100" align="right"><? echo $total_alt_qty;?></th>
	                <th width="100" align="right"><? //echo number_format($tot_check_qty_per,2);?></th>
	                <th align="right"><? //echo number_format($tot_def_qty_per,2);?></th>
	            </tr>

	            <tr>
	                <th width="30"></th>
	                <th width="130">Total Gmts Spot</th>
	                <?php 
					$total_spot_qty=0;
					foreach($prod_line_arr as $line_id=>$line_id){
							
						$tot_spot_qty=$line_def_spot_qty_arr[$line_id];
						$total_spot_qty+=$tot_spot_qty;
	                    echo '<th width="80" align="right">'.$tot_spot_qty.'</th>';
	                }?>
	                <th width="100" align="right"><? echo $total_spot_qty;?></th>
	                <th width="100" align="right"><? //echo number_format($tot_check_qty_per,2);?></th>
	                <th align="right"><? //echo number_format($tot_def_qty_per,2);?></th>
	            </tr>


	            <tr>
	                <th width="30"></th>
	                <th width="130">Total Gmts Replace</th>
	                <?php 
					$total_replace_qty=0;
					foreach($prod_line_arr as $line_id=>$line_id){
							
						$tot_replace_qty=$line_def_replace_qty_arr[$line_id];
						$total_replace_qty+=$tot_replace_qty;
	                    echo '<th width="80" align="right">'.$tot_replace_qty.'</th>';
	                }?>
	                <th width="100" align="right"><? echo $total_replace_qty;?></th>
	                <th width="100" align="right"><? //echo number_format($tot_check_qty_per,2);?></th>
	                <th align="right"><? //echo number_format($tot_def_qty_per,2);?></th>
	            </tr>

	            <?
	            	if(str_replace("'","",$cbo_production_type)==5)
	            	{
	            		?>
	            		<tr>
	            			<th width="30"></th>
	            			<th width="130">Total Rescan Qnty</th>
	            			<?php 
	            			$total_rescan_qty=0;
							//print_r($line_def_rescan_qty_arr);die;
	            			foreach($prod_line_arr as $line_id=>$line_id)
							{


	            				$tot_rescan_qty=$line_def_rescan_qty_arr[$line_id];
	            				$total_rescan_qty+=$tot_rescan_qty;
	            				echo '<th width="80" align="right">'.$tot_rescan_qty.'</th>';
								
	            			}?>
							<th width="100" align="right"><? echo $total_rescan_qty;?></th>
	            			<th width="100" align="right"><? //echo number_format($tot_check_qty_per,2);?></th>
	            			<th align="right"><? //echo number_format($tot_def_qty_per,2);?></th>
	            		</tr>

	            		<?
	            	}
	            ?>
	                
	            </tfoot>
				
			</table>
			<br>
			<br>
			<br>
			<table class="rpt_table" width="420" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<tr>
					<th><caption style="font-size: 25px">Top 5 Defect</caption> </th>
				</tr>

					<thead>
						<tr>
							<th>SL</th>
							<th width="150">Defect Description </th>
							<th width="100">Total Check Qty</th>
							<th width="100">Top 5 Defect</th>
							<th>%</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						  foreach($defect_top5_arr as $sfsd_name => $v)
						  {
							if($i<=5)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trtop_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trtop_<? echo $i;?>">
									<td><?=$i?></td>
									<td width="150"><?=$sfsd_name?> </td>
									<td width="100" align="right"><?=$v['chk_qty']?></td>
									<td width="100" align="right"><?=$v['qty']?></td>
									<td align="right"><?= number_format($v['prsnt'],2)?></td>

								
								</tr>
						<?
                           $i++;
							}
								
						  }
						 
						?>
						
					</tbody>
						
			</table>
	        
	        
	</div>
			
		
			
			
	        
	        
	        &nbsp;
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
			//$reject_qty_type=array(0=>"Reject");
			$bar_arr=array();$val_arr=array();
			foreach($sew_fin_alter_defect_type as $kk=>$vv)
			{
				$bar_arr[]=$vv;
				$val_arr[]=number_format($check_qty_per_arr[$alter][$kk],2);
				$val_arr2[]=number_format($def_qty_per_arr[$alter][$kk],2);
			}
			
			foreach($sew_fin_spot_defect_type as $key=>$val)
			{
				$bar_arr[]=$val;
				$val_arr[]=number_format($check_qty_per_arr[$spot][$key],2);
				$val_arr2[]=number_format($def_qty_per_arr[$spot][$key],2);
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
			foreach($result as $row)
			{
				$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('po_break_down_id')]][$row[csf('defect_point_id')]]=$row[csf('defect_qty')];
				$prod_def_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('defect_qty')];
			}
			unset($result);	
			 
			// var_dump($qcDataArr[3]);
			//echo $sql;
		
		if($type==1)
		{	
		
			if ($prod_type==1)
			{
				$colspan=count($cutting_qc_reject_type);
				$tbl_width=($colspan*80)+1370;
			}
			
			//print_r($cutting_qc_reject_type); die;
			
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
	                    <th width="30" rowspan="2">SL No</th>
	                    <th width="130" rowspan="2">Buyer</th>
	                    <th width="130" rowspan="2">Style No</th>
	                    <th width="150" rowspan="2">Order No</th>
	                    <th width="100" rowspan="2">Internal Ref.</th>
	                    <th width="130" rowspan="2">Order Qty</th>
	                    <th width="130" colspan="<? echo $colspan;?>">Name of all Defects</th>
	                    <th width="100" rowspan="2">Total Defected pannels</th>
	                    <th width="100" rowspan="2">Order defect % to total defect</th>
	                    <th width="100" rowspan="2">Total Gmts Reject</th>
	                    
	                    <th width="100" rowspan="2">Reject% To Total QC</th>
	                    <th width="100" rowspan="2">Total Gmts Replace</th>
	                    <th width="100" rowspan="2">Total Qc Pass Qty</th>
	                    <th rowspan="2">Total Check Qty</th>
	                </tr>
	                <tr>
					
	                    <? 
						
						foreach($cutting_qc_reject_type as $sfad_id=>$sfad_name){
							echo '<th width="80">'.$sfad_name.'</th>';
						}?>
	                </tr>
	            </thead>
	        </table>

	        
	        <div style="max-height:350px; float:left; overflow-y:scroll;width:<? echo $tbl_width+18;?>px;" id="scroll_body" > 
			<table width="<? echo $tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="" align="left" >
			<?
			
			$i=1;
			$defect_top5_arr=array();
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
				<?php foreach($cutting_qc_reject_type as $sfad_id=>$sfad_name)
				{
					$defect_qty_arr[$sfad_id]+=$prod_qty_arr[3][$po_id][$sfad_id];
					echo '<td width="80" align="right">'.$prod_qty_arr[3][$po_id][$sfad_id].'</td>';
					
					$defect_top5_arr[$sfad_name]['qty']=$tot_qc_check_qty;
					$defect_top5_arr[$sfad_name]['defect']=$totalGmtsReject;
					$defect_top5_arr[$sfad_name]['prsnt']=$rej_qty_arr[$po_id]*100;
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
			
			// $defect_top5_arr[$sfad_name]['qty']+=$qc_check_qty_arr[$po_id];
			// $defect_top5_arr[$sfad_name]['defect']=$rej_qty_arr[$po_id];
			// $defect_top5_arr[$sfad_name]['prsnt']=$rej_qty_arr[$po_id]*100;
			}
			//echo "<pre>";print_r($defect_top5_arr);die;
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
	                    <?php foreach($cutting_qc_reject_type as $sfad_id=>$sfad_name){
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
	                    <?php foreach($cutting_qc_reject_type as $sfad_id=>$sfad_name){
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
			<br>
			<br>
			<br>
			<table class="rpt_table" width="420" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<tr>
					<th><caption style="font-size: 25px">Top 5 Defect</caption> </th>
				</tr>

					<thead>
						<tr>
							<th>SL</th>
							<th width="150">Defect Description </th>
							<th width="100">Total Check Qty</th>
							<th width="100">Top 5 Defect</th>
							<th>%</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						  foreach($defect_top5_arr as $sfad_name => $v)
						  {
							if($i<=5)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trtop_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trtop_<? echo $i;?>">
									<td><?=$i?></td>
									<td width="150"><?=$sfad_name?> </td>
									<td width="100" align="right"><?=$v['qty']?></td>
									<td width="100" align="right"><?=$v['defect']?></td>
									<td align="right"><?=number_format($totalGmtsReject/$tot_qc_check_qty*100,2)?></td>

								
								</tr>
						<?
                           $i++;
							}
								
						  }
						 
						?>
						
					</tbody>
						
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
			foreach($cutting_qc_reject_type as $kk=>$vv)
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






