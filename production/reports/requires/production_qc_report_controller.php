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
	echo create_drop_down( "cbo_location", 80, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[1]' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/production_qc_report_controller', $data[1]+'_'+this.value+'_'+$data[0], 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor", 70, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and production_process='$data[2]' and location_id='$data[1]' order by floor_name","id,floor_name", 1, "-- Select --", $selected,  "load_drop_down( 'requires/production_qc_report_controller', $data[0]+'_'+$data[1]+'_'+this.value, 'load_drop_down_line', 'line_td' );",0); 
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
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$sewing_floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	
	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	
	$location = str_replace("'","",$cbo_location);
	$job_no = str_replace("'","",$txt_job_no);
	$int_ref = str_replace("'","",$txt_int_ref);
	
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
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $prod_date_qc_cond="";
		else $prod_date_qc_cond=" and d.production_date between $txt_date_from and $txt_date_to";
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	if ($location==0) $location_qc_cond=""; else $location_qc_cond=" and d.location=".$location." "; 
	if(str_replace("'","",$cbo_floor)==0) $floor_name_cond="";else $floor_name_cond=" and d.floor_id=$cbo_floor";
	if(str_replace("'","",$cbo_line_id)==0) $line_cond="";else $line_cond=" and d.sewing_line in($cbo_line_id)";

	if(str_replace("'","",$txt_job_no)=="") $job_cond=""; else $job_cond=" and b.job_no_prefix_num=$txt_job_no";
	if(str_replace("'","",$txt_int_ref)=="") $int_ref_cond=""; else $int_ref_cond=" and c.grouping=$txt_int_ref";

	if(str_replace("'","",$txt_style_ref)=="") $txt_style_ref_cond=""; else $txt_style_ref_cond=" and b.style_ref_no=$txt_style_ref";
	if(str_replace("'","",$txt_order_no)=="") $txt_order_no_cond=""; else $txt_order_no_cond=" and c.po_number=$txt_order_no";
	

	
	if($type==1) // show button
	{	$colspan=36;
		if ($prod_type==5)
		{
			$defect_type=3;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1880;
		}
		elseif($prod_type==1)
		{
			$defect_type=3;
			$sew_fin_alter_defect_type=$cutting_qc_reject_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1780;
		}
		elseif($prod_type==8)
		{
			$defect_type=1;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1780;
		}
		elseif($prod_type==11)
		{
			$prod_type=8;
			$defect_type=1;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1880;
		}

		// =========================== dft qty query ========================
		$sql = "SELECT b.buyer_name, c.po_number,c.grouping, c.file_no, c.po_quantity as po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity as qs_pass_qty, d.alter_qnty as alter_qnty, d.reject_qnty as reject_qnty, d.spot_qnty as spot_qnty,d.replace_qty, b.style_ref_no ";
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
		$sql.= " FROM pro_gmts_prod_dft a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst d where d.id=a.mst_id and a.po_break_down_id=c.id and b.id=c.job_id and a.production_type='$prod_type' and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.defect_type_id in(2,4,$defect_type) $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $line_cond $prod_date_qc_cond $job_cond $int_ref_cond $txt_style_ref_cond $txt_order_no_cond group by b.buyer_name, c.po_number,c.grouping,c.file_no, c.po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity, d.alter_qnty, d.reject_qnty, d.spot_qnty,d.replace_qty, b.style_ref_no";
		  //echo $sql;
		$result = sql_select($sql); 
		$prodIds=""; 
		$prod_qty_arr=array(); 
		$defect_line_arr=array(); 
		$order_colspan_arr=array(); 
		$sewlineArr=array();
		$alter_defect_type_arr = array(); 
		$spot_defect_type_arr = array(); 
		foreach($result as $row)
		{
			$prodIds.=$row[csf("id")].",";
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['po_data']=$row[csf('buyer_name')].'**'.$row[csf('po_number')].'**'.$row[csf('po_quantity')].'**'.$row[csf('grouping')].'**'.$row[csf('file_no')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
			// $prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['qc']+=$row[csf('qs_pass_qty')];
			// $prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['alt']+=$row[csf('alter_qnty')];
			// $prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['spt']+=$row[csf('spot_qnty')];
			// $prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['rjt']+=$row[csf('reject_qnty')];
			// $prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['rpl']+=$row[csf('replace_qty')];
			
			$sewlineArr[$row[csf('id')]]=$row[csf('sewing_line')];
			
			foreach($sew_fin_alter_defect_type as $id=>$val) 
			{
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$id][1]+=$row[csf('altdefect_qty_'.$id)];
				if($row[csf('altdefect_qty_'.$id)]>0)
				{
					$alter_defect_type_arr[$id] = $id;
				}
			}
			
			foreach($sew_fin_spot_defect_type as $key=>$val) 
			{
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$key][2]+=$row[csf('sptdefect_qty_'.$key)];
				if($row[csf('sptdefect_qty_'.$key)]>0)
				{
					$spot_defect_type_arr[$key] = $key;
				}
			}
			
			$order_colspan_arr[$row[csf('buyer_name')]][$row[csf('po_break_down_id')]]+=1;
		}
		unset($result);



		// =========================== qc qnty ========================
		$sql = "SELECT b.buyer_name, c.po_number,c.grouping, c.file_no, c.po_quantity as po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, e.production_qnty as qs_pass_qty, d.alter_qnty as alter_qnty, d.reject_qnty as reject_qnty, d.spot_qnty as spot_qnty,e.replace_qty, b.style_ref_no 
			from wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown a, pro_garments_production_mst d,pro_garments_production_dtls e where d.id=e.mst_id and d.po_break_down_id=c.id and b.id=c.job_id and d.production_type='$prod_type' and a.id=e.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $line_cond $prod_date_qc_cond $job_cond $int_ref_cond $txt_style_ref_cond $txt_order_no_cond";
		// echo $sql;
		$res = sql_select($sql);
		foreach($res as $row)
		{
			$prodIds.=$row[csf("id")].",";
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['po_data']=$row[csf('buyer_name')].'**'.$row[csf('po_number')].'**'.$row[csf('po_quantity')].'**'.$row[csf('grouping')].'**'.$row[csf('file_no')].'**'.$row[csf('style_ref_no')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['qc']+=$row[csf('qs_pass_qty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['alt']+=$row[csf('alter_qnty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['spt']+=$row[csf('spot_qnty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['rjt']+=$row[csf('reject_qnty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['rpl']+=$row[csf('replace_qty')];
			
			$sewlineArr[$row[csf('id')]]=$row[csf('sewing_line')];
		}
		
		// echo "<pre>";print_r($alter_defect_type_arr);die();

		if ($prod_type==5)
		{
			// $sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($alter_defect_type_arr)*80)+1880;
		}
		elseif($prod_type==1)
		{
			// $sew_fin_alter_defect_type=$cutting_qc_reject_type;
			$tbl_width=(count($alter_defect_type_arr)*80)+1780;
		}
		elseif($prod_type==8)
		{
			// $sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($alter_defect_type_arr)*80)+1780;
		}
		elseif($prod_type==11)
		{
			// $sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($alter_defect_type_arr)*80)+1880;
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
					<th width="100" rowspan="2">Style Ref.</th>
					<th width="100" rowspan="2">Ref No</th>
					<th width="100" rowspan="2">File No</th>
					<th width="100" rowspan="2">Order Qty</th>
					<? if ($prod_type==5 || $prod_type==11) 
					{
						?>
						<th width="100" rowspan="2">Line No</th>
						<? 
					}
					?>
					<th colspan="<? echo count($alter_defect_type_arr);?>"  width="100">Defect Counting 
					<? if($defect_type==3){echo "Reject";}else{ echo "Alter";}?>
					</th>
					<? if(count($spot_defect_type_arr)>0){?>
					<th colspan="<? echo count($spot_defect_type_arr);?>" width="100">Defect Counting Spot</th>
					<?} if($prod_type!=1){?>
					
					<th width="100" rowspan="2">Total Gmts Alter</th>
					<th width="90" rowspan="2">Alter % To Total QC</th>
					<th width="90" rowspan="2">Total Gmts Spot</th>
					<th width="100" rowspan="2">Spot % To Total QC</th>
					<?}?>

					<th width="100" rowspan="2">Total Gmts Reject</th>
					<th width="90" rowspan="2">Total Replace</th>
					
					<th width="100" rowspan="2">Reject % To Total QC</th>
					<th width="90" rowspan="2">Total Gmts Defect</th>
					<th width="90" rowspan="2">Defect % To Total QC</th>
					<th width="100" rowspan="2">Total Qc Passed Pcs.</th>
					<th rowspan="2">Total QC Pcs. with Defect</th>
				</tr>
				<tr style="font-size:13px">
					<? foreach($alter_defect_type_arr as $id=>$val) { ?>
					<th width="80">
						<? echo $sew_fin_alter_defect_type[$val]; ?>
					</th>
					<? } 
					if(count($spot_defect_type_arr)>0){
					foreach($spot_defect_type_arr as $key=>$value) {?>
					<th width="80"><? echo $sew_fin_spot_defect_type[$value]; ?></th>
					<? } }?>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id=""><!--table_body-->
		<?
				
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
				$replace_qty=$line_val['rpl'];
				$prod_reso_allo=$line_val['prod_reso_allo'];
				$ex_po_data=explode('**',$prod_qty_arr[$po_id][$line_id]['po_data']);
				$buyer_id=$ex_po_data[0];
				$po_number=$ex_po_data[1];
				$po_qty=$ex_po_data[2];
				$int_ref=$ex_po_data[3];
				$file_no=$ex_po_data[4];
				$style_ref=$ex_po_data[5];

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
                	<? //if($k==1) { ?>
					<td width="30"><? echo $i; $i++; ?></td>
					<td width="100" style="word-wrap:break-word;"><? echo $buyerArr[$buyer_id]; ?></td>
					<td width="100" style="word-wrap:break-word;">&nbsp;<? echo $po_number; ?></td>
					<td width="100" style="word-wrap:break-word;">&nbsp; <? echo $style_ref ?></td>
					<td width="100" style="word-wrap:break-word;">&nbsp;<? echo $int_ref; ?></td>
					<td width="100" style="word-wrap:break-word;">&nbsp;<? echo $file_no; ?></td>
					<td width="100" align="right"><? if($po_qty>0) echo number_format($po_qty,2); $tot_po_qty+=$po_qty; ?></td>
                    <? //} else { ?>
					<!-- <td width="30">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td> -->
					<? //} 
					$k++; 
					if ($prod_type==5 || $prod_type==11) 
					{ 	?>
							<td width="100" align="center" style="word-wrap:break-word;">&nbsp;<? echo $line_name; ?></td>
						<? 
					}

					$row_tot_alt=0; $row_tot_spt=0;
					foreach($alter_defect_type_arr as $id=>$val) {
						$alt_dft_value=0;
						$alt_dft_value=$defect_line_arr[$po_id][$line_id][$id][1];
						$alt_dft_qty[$id]+=$alt_dft_value;
					?>
					<td width="100" align="right">&nbsp;<? if($alt_dft_value>0) echo number_format($alt_dft_value,2); 
					$row_tot_alt+=$alt_dft_value;
					?></td>
					<? } foreach($spot_defect_type_arr as $key=>$value) {
						$spt_dft_value=0;
						$spt_dft_value=$defect_line_arr[$po_id][$line_id][$key][2];
						$spt_dft_qty[$key]+=$spt_dft_value;
						?>
					<td width="100" align="right">&nbsp;<? if($spt_dft_value>0) echo number_format($spt_dft_value,2); $row_tot_spt+=$spt_dft_value; ?></td>
					<?} if($prod_type!=1){?>
					<td width="100" align="right">&nbsp;<?
						$pcs_inspaction=0;
						$row_tot_defect=0; 
						$row_tot_defect = $alter_qty+$spot_qty+$reject_qty;
						$pcs_inspaction = $qc_pass_qty+$row_tot_defect;
						
						if($alter_qty>0) echo number_format($alter_qty,2); 
						
						?>
					</td>
					<td width="90" align="right">&nbsp;<? $row_tot_alt_per=0; $row_tot_alt_per=($alter_qty/$pcs_inspaction)*100; if($row_tot_alt_per>0) echo number_format($row_tot_alt_per,2).'%'; ?></td>
					<td width="90" align="right">&nbsp;<? if($spot_qty>0) echo number_format($spot_qty,2); ?></td>
					<td width="100" align="right">&nbsp;<? $row_tot_spt_per=0; $row_tot_spt_per=($spot_qty/$pcs_inspaction)*100; if($row_tot_spt_per>0) echo number_format($row_tot_spt_per,2).'%'; ?></td>
					<?}
					if($prod_type==1)
					{
						$pcs_inspaction=0;
						$row_tot_defect=0; 
						$row_tot_defect = $row_tot_alt;//$alter_qty+$spot_qty+$reject_qty;
						$pcs_inspaction = $qc_pass_qty+$row_tot_defect;
					}

					?>
					<td width="100" align="right">&nbsp;<? if($reject_qty>0) echo number_format($reject_qty,2); ?></td>


					<td width="100" align="right">&nbsp;<?=number_format($replace_qty,2); ?></td>


					<td width="100" align="right">&nbsp;
						<?
						 $reject_qnty_per=0; 
						 $reject_qnty_per=($qc_pass_qty>0) ? ($reject_qty/$qc_pass_qty)*100 : 0; 
						 if($reject_qnty_per>0) echo number_format($reject_qnty_per,2).'%'; 
						 // echo $reject_qty."/".$qc_pass_qty."<br>";
						?>
						 	
					</td>
					<td width="100" align="right">&nbsp;<? echo number_format($row_tot_defect,2); ?></td>
					<td width="90" align="right">&nbsp;<? $row_tot_defect_per=0; $row_tot_defect_per=($row_tot_defect/$pcs_inspaction)*100; if($row_tot_defect_per>0) echo number_format($row_tot_defect_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<?  if($qc_pass_qty>0) echo number_format($qc_pass_qty,2); ?></td>
					<td align="right">&nbsp;<? if($pcs_inspaction>0) echo number_format($pcs_inspaction,2); ?></td>
				</tr>
				<?
				$tot_reject_qty+=$reject_qty;
				$tot_replace_qty+=$replace_qty;
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
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">Total</td>
				<td width="100" align="right"><? echo number_format($tot_po_qty,2); ?></td>
				<? if ($prod_type==5 || $prod_type==11) {?><td width="100">&nbsp;</td><? }
				foreach($alter_defect_type_arr as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty[$id],2); 
				?></td>
				<? } foreach($spot_defect_type_arr as $key=>$value) {
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty[$key],2); ?></td>
				<?} if($prod_type!=1){?>
				<td width="100" align="right"><? echo number_format($tot_alter_qty,2); ?></td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_spot_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<?}?>
				<td width="100" align="right"><? echo number_format($tot_reject_qty,2); $reject_qty_arr=array(); $reject_qty_arr[]=$tot_reject_qty; ?></td>
				<td width="100" align="right"><? echo number_format($tot_replace_qty,2); $replace_qty_arr=array(); $replace_qty_arr[]=$tot_replace_qty; ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_defect_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_qc_pass_qty,2); ?></td>
				<td align="right"><? echo number_format($tot_pcs_inspaction,2); ?></td>
			</tr>
			<tr>
				<td width="30">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="200" colspan="4" style="font-size:12px">Defect Counting % To Total QC</td>
				<? if ($prod_type==5 || $prod_type==11) {?><td width="100">&nbsp;</td><? }?>
				<? 
				$alt_dft_qty_per=array(); $spt_dft_qty_per=array();
				foreach($alter_defect_type_arr as $id=>$val) { 
				$alt_dft_qty_per[$id]=($alt_dft_qty[$id]/$tot_pcs_inspaction)*100;
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty_per[$id],2)."%"; 
				?></td>
				<? } foreach($spot_defect_type_arr as $key=>$value) {
					$spt_dft_qty_per[$key]=($spt_dft_qty[$key]/$tot_pcs_inspaction)*100;
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty_per[$key],2).'%'; ?></td>
				<?} if($prod_type!=1){?>
				<td width="100" align="right">&nbsp;</td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<?}?>
				<td width="100" align="right">&nbsp;</td>
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
		foreach($alter_defect_type_arr as $kk=>$vv)
		{
			$bar_arr[]=$sew_fin_alter_defect_type[$vv];
			$val_arr[]=$alt_dft_qty[$kk];
		}
		
		foreach($spot_defect_type_arr as $key=>$val)
		{
			$bar_arr[]=$sew_fin_spot_defect_type[$val];
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
	else if($type==2)// monthly button
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

		if(str_replace("'","",$cbo_company_name)==0) $company_qc_cond=""; else $company_qc_cond=" and d.company_id=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_cond=""; else $buyer_cond=" and b.buyer_name=$cbo_buyer_name";
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $prod_date_qc_cond="";
			else $prod_date_qc_cond=" and d.production_date between $txt_date_from and $txt_date_to";
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		if ($location==0) $location_qc_cond=""; else $location_qc_cond=" and d.location=".$location." "; 
		if(str_replace("'","",$cbo_floor)==0) $floor_name_cond="";else $floor_name_cond=" and d.floor_id=$cbo_floor";

		if(str_replace("'","",$txt_job_no)=="") $job_cond=""; else $job_cond=" and b.job_no_prefix_num=$txt_job_no";
		if(str_replace("'","",$txt_int_ref)=="") $int_ref_cond=""; else $int_ref_cond=" and c.grouping=$txt_int_ref";
		
		$defect_line_arr=array(); $prod_qty_arr=array();
		$sql = "select d.id, d.production_date, d.production_quantity as qs_pass_qty, d.alter_qnty as alter_qnty, d.reject_qnty as reject_qnty, d.spot_qnty as spot_qnty,d.replace_qty ";
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
		$sql.= " from pro_gmts_prod_dft a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst d where d.id=a.mst_id and a.po_break_down_id=c.id and b.id=c.job_id and a.production_type='$prod_type' and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 AND a.defect_type_id IN (2, 4, 3) $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond $job_cond $int_ref_cond group by d.id, d.production_date, d.production_quantity, d.alter_qnty, d.reject_qnty, d.spot_qnty,d.replace_qty";
		// echo $sql;
		$result = sql_select($sql);
		$alter_defect_type_arr = array(); 
		$spot_defect_type_arr = array(); 
		foreach($result as $row)
		{
			// $prod_qty_arr[change_date_format($row[csf('production_date')])]['qc']+=$row[csf('qs_pass_qty')];
			// $prod_qty_arr[change_date_format($row[csf('production_date')])]['alt']+=$row[csf('alter_qnty')];
			// $prod_qty_arr[change_date_format($row[csf('production_date')])]['spt']+=$row[csf('spot_qnty')];
			// $prod_qty_arr[change_date_format($row[csf('production_date')])]['rjt']+=$row[csf('reject_qnty')];
			// $prod_qty_arr[change_date_format($row[csf('production_date')])]['rpl']+=$row[csf('replace_qty')];
			foreach($sew_fin_alter_defect_type as $id=>$val) 
			{
				$defect_line_arr[change_date_format($row[csf('production_date')])][$id][1]+=$row[csf('altdefect_qty_'.$id)];
				if($row[csf('altdefect_qty_'.$id)]>0)
				{
					$alter_defect_type_arr[$id] = $id;
				}
			}
			foreach($sew_fin_spot_defect_type as $key=>$val) 
			{
				$defect_line_arr[change_date_format($row[csf('production_date')])][$key][2]+=$row[csf('sptdefect_qty_'.$key)];
				if($row[csf('sptdefect_qty_'.$key)]>0)
				{
					$spot_defect_type_arr[$key] = $key;
				}
			}
		}
		unset($result);



		// =========================== qc qnty ========================
		$sql = "SELECT d.production_date, e.production_qnty as qs_pass_qty, e.alter_qty as alter_qnty, e.reject_qty as reject_qnty, e.spot_qty as spot_qnty,e.replace_qty  
			from wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown a, pro_garments_production_mst d,pro_garments_production_dtls e where d.id=e.mst_id and d.po_break_down_id=c.id and b.id=c.job_id and d.production_type='$prod_type' and a.id=e.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond $job_cond $int_ref_cond";
		// echo $sql;	
		$res = sql_select($sql);
		foreach($res as $row)
		{
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['qc']+=$row[csf('qs_pass_qty')];
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['alt']+=$row[csf('alter_qnty')];
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['spt']+=$row[csf('spot_qnty')];
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['rjt']+=$row[csf('reject_qnty')];
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['rpl']+=$row[csf('replace_qty')];
		}
		
		
		
		$colspanm=33;
		// $tbl_widthm=1100+(80*count($sew_fin_alter_defect_type))+(80*count($sew_fin_spot_defect_type));
		$tbl_widthm=1100+(80*count($alter_defect_type_arr))+(80*count($spot_defect_type_arr));
		ob_start();
		?>
		<div>
			<style type="text/css">
			table tr th,table tr td{word-wrap: break-word;word-break: break-all;}
			</style>
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
					<th colspan="<? echo count($alter_defect_type_arr);?>">Defect Counting Alter</th>
					<? if(count($spot_defect_type_arr)>0){?>
					<th colspan="<? echo count($spot_defect_type_arr);?>">Defect Counting Spot</th>
					<? } ?>
					<? if($prod_type!=1){?>
					<th width="100" rowspan="2">Total Gmts Alter</th>
					<th width="90" rowspan="2">Alter % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Spot</th>
					<th width="90" rowspan="2">Spot % To Total QC</th>
					<?}?>
					<th width="100" rowspan="2">Total Gmts Reject</th>
					<th width="100" rowspan="2">Total Replace</th>
					
					<th width="90" rowspan="2">Reject % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Defect</th>
					<th width="90" rowspan="2">Defect % To Total QC</th>
					<th width="100" rowspan="2">Total Qc Passed Pcs.</th>
					<th width="100" rowspan="2">Total QC Pcs.</th>
				 </tr>
				 <tr>
					<? foreach($alter_defect_type_arr as $id=>$val) 
					{ 
						?>
						<th width="80"><p><? echo $sew_fin_alter_defect_type[$val]; ?></p></th>
						<? 
					} 
					if(count($spot_defect_type_arr)>0)
					{
						foreach($spot_defect_type_arr as $key=>$value) 
						{
							?>
							<th width="80"><p><? echo $sew_fin_spot_defect_type[$value]; ?></p></th>
							<? 
						}
					}?>
				 </tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_widthm+17; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
		<table width="<? echo $tbl_widthm; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body"><!--table_body-->
		<?
		
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
			$replace_qty=$prod_qty_arr[$newdate]['rpl'];
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $newdate; ?></div></td>
				<? 
				$row_tot_alt=0; $row_tot_spt=0; 
				foreach($alter_defect_type_arr as $id=>$val) 
				{ 
					$alter_defect_val=0;
					$alter_defect_val=$defect_line_arr[$newdate][$id][1];
					$alt_dft_qty[$id]+=$alter_defect_val;
				?>
				<td width="80" align="right"><? if($alter_defect_val>0) echo number_format($alter_defect_val,2); $row_tot_alt+=$alter_defect_val; ?></td>
				<? } foreach($spot_defect_type_arr as $key=>$value)
				{
					$spot_defect_val=0;
					$spot_defect_val=$defect_line_arr[$newdate][$key][2];
					$spt_dft_qty[$key]+=$spot_defect_val;
					?>
				<td width="80" align="right"><? if($spot_defect_val>0) echo number_format($spot_defect_val,2); $row_tot_spt+=$spot_defect_val; ?></td>
				<? }?>
				<? 
				$pcs_inspaction=0;
				$row_tot_defect=0; 
				$row_tot_defect=$row_tot_alt;//$alter_qty+$spot_qty+$reject_qty;
				$pcs_inspaction=$qc_pass_qty+$row_tot_defect;
				if($prod_type!=1){?>
					<td width="100" align="right"><?
						// $pcs_inspaction=0;
						// $row_tot_defect=0; 
						// $row_tot_defect=$alter_qty+$spot_qty+$reject_qty;
						// $pcs_inspaction=$qc_pass_qty+$row_tot_defect;
						
						if($alter_qty>0) echo number_format($alter_qty,2); ?></td>
					<td width="90" align="right"><? $row_tot_alt_per=0; $row_tot_alt_per=($alter_qty/$pcs_inspaction)*100; if($row_tot_alt_per>0) echo number_format($row_tot_alt_per,2).'%'; ?></td>
					<td width="100" align="right"><? if($spot_qty>0) echo number_format($spot_qty,2); ?></td>
					<td width="90" align="right"><?  $row_tot_spt_per=0; $row_tot_spt_per=($spot_qty/$pcs_inspaction)*100; if($row_tot_spt_per>0) echo number_format($row_tot_spt_per,2).'%'; ?></td>
				<? } ?>

				<td width="100" align="right"><? if($reject_qty>0) echo number_format($reject_qty,2); ?></td>
				<td width="100" align="right"><? if($replace_qty>0) echo number_format($replace_qty,2); ?></td>
				<td width="90" align="right"><? $reject_qnty_per=0; $reject_qnty_per=($reject_qty/$pcs_inspaction)*100; if($reject_qnty_per>0) echo number_format($reject_qnty_per,2).'%'; ?></td>
				<td width="100" align="right"><? echo number_format($row_tot_defect,2); ?></td>
				<td width="90" align="right"><? $row_tot_defect_per=0; $row_tot_defect_per=($row_tot_defect/$pcs_inspaction)*100; if($row_tot_defect_per>0) echo number_format($row_tot_defect_per,2).'%'; ?></td>
				<td width="100" align="right"><? if($qc_pass_qty>0) echo number_format($qc_pass_qty,2); ?></td>
				<td width="100" align="right"><?  if($pcs_inspaction>0) echo number_format($pcs_inspaction,2); ?></td>
			</tr>
			<?
			$tot_reject_qty+=$reject_qty;
			$tot_replace_qty+=$replace_qty;
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
				foreach($alter_defect_type_arr as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty[$id],2); ?></td>
				<? } foreach($spot_defect_type_arr as $key=>$value) {
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty[$key],2); ?></td>
				<? }?>

				<? if($prod_type!=1){?>
				<td width="100" align="right"><? echo number_format($tot_alter_qty,2); ?></td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_spot_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<?}?>

				<td width="100" align="right"><? echo number_format($tot_reject_qty,2); $reject_qty_arr=array(); $reject_qty_arr[]=$tot_reject_qty; ?></td>
				<td width="100" align="right"><? echo number_format($tot_replace_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_defect_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_qc_pass_qty,2); ?></td>
				<td width="100" align="right"><? echo number_format($tot_pcs_inspaction,2); ?></td>
			</tr>
			<tr>
				<td width="30">&nbsp;</td>
				<td width="100"><div style="word-wrap:break-word; width:100px">Defect Counting % To Total QC</div></td>
				<? 
				$alt_dft_qty_per=array(); $spt_dft_qty_per=array();
				foreach($alter_defect_type_arr as $id=>$val) { 
				$alt_dft_qty_per[$id]=($alt_dft_qty[$id]/$tot_pcs_inspaction)*100;
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty_per[$id],2)."%"; ?></td>
				<? } foreach($spot_defect_type_arr as $key=>$value) {
					$spt_dft_qty_per[$key]=($spt_dft_qty[$key]/$tot_pcs_inspaction)*100;
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty_per[$key],2).'%'; ?></td>
				<? }?>
				<? if($prod_type!=1){?>
				<td width="100" align="right">&nbsp;</td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<?}?> 
				<td width="100" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
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
		foreach($alter_defect_type_arr as $kk=>$vv)
		{
			$bar_arr[]=$sew_fin_alter_defect_type[$vv];
			$val_arr[]=$alt_dft_qty[$kk];
		}
		
		foreach($spot_defect_type_arr as $key=>$val)
		{
			$bar_arr[]=$sew_fin_spot_defect_type[$val];
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
	else if($type==3)//wvn button
	{	$colspan=36;
		// echo $prod_type;
		if ($prod_type==5)
		{
			$defect_type=3;
			$sew_fin_woven_defect_array=$sew_fin_woven_defect_array;
			$tbl_width=((3 * count($sew_fin_woven_defect_array))*80)+2460;
			// $tbl_width=(count($sew_fin_woven_defect_array)*80)+2080;
			// $tbl_width=(count($sew_fin_woven_defect_array)*80)+2360;
		}
		elseif($prod_type==1)
		{
			echo "This button only for production type Sewing"; die;
		}
		elseif($prod_type==8)
		{
			echo "This button only for production type Sewing"; die;
		}
		elseif($prod_type==13)
		{
			echo "This button only for production type Sewing"; die;
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
					<th width="100" rowspan="2">Company Name</th>
					<th width="100" rowspan="2">Working Comapny</th>

					<th width="100" rowspan="2">Buyer Name</th>
					<th width="100" rowspan="2">Master Style/ Int. Ref. No</th>
					<th width="100" rowspan="2">Merch Style</th>
					<th width="100" rowspan="2">Order No</th>
					<th width="100" rowspan="2">Order Qty</th>
					<th width="100" rowspan="2">Floor</th>
					<!-- <th width="100" rowspan="2">File No</th> -->
					
					<? if ($prod_type==5) {?><th width="100" rowspan="2">Line No</th><? }?>
					<th style="Background:#ffc299;" colspan="<? echo count($sew_fin_woven_defect_array);?>">Front Part Defect  <?// if($defect_type==3){echo "Reject";}else{ echo "Alter";}?></th>
					<th style="Background:#80bfff;" colspan="<? echo count($sew_fin_woven_defect_array);?>">Back Part Defect </th>
					<th style="Background:#a6a659;" colspan="<? echo count($sew_fin_woven_defect_array);?>">WestBand Defect</th>
					<th style="Background:#8c6699;" colspan="<? echo count($sew_fin_measurment_check_array);?>">Measurement Check  </th>
					<th style="Background:#a6a659;" colspan="<? echo count($sew_fin_spot_defect_type);?>">Defect Counting Spot</th>
					
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
					<? foreach($sew_fin_woven_defect_array as $id=>$val) { ?>
					<th width="80"><? echo $val; ?></th>
					<? } foreach($sew_fin_woven_defect_array as $key=>$value) {?>
					<th width="80"><? echo $value; ?></th>
					<? } foreach($sew_fin_woven_defect_array as $key=>$value) {?>
					<th width="80"><? echo $value; ?></th>
					<? } foreach($sew_fin_measurment_check_array as $key=>$value) {?>
					<th width="80"><? echo $value; ?></th>
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
		
		

		$sql = "select b.buyer_name, c.po_number,c.grouping, c.file_no, c.po_quantity as po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity as qs_pass_qty, d.alter_qnty as alter_qnty, d.reject_qnty as reject_qnty, d.spot_qnty as spot_qnty, d.company_id, d.serving_company,d.floor_id,b.style_ref_no ";
		foreach($sew_fin_woven_defect_array as $id=>$val) {
			$sql.= ", sum(case when a.defect_type_id in (4) and a.defect_point_id=$id then a.defect_qty else 0 end) as altdefect_qty_$id";
		}
		foreach($sew_fin_woven_defect_array as $id=>$val) {
			$sql.= ", sum(case when a.defect_type_id in (5) and a.defect_point_id=$id then a.defect_qty else 0 end) as backdefect_qty_$id";
		}
		foreach($sew_fin_woven_defect_array as $id=>$val) {
			$sql.= ", sum(case when a.defect_type_id in (6) and a.defect_point_id=$id then a.defect_qty else 0 end) as westbanddefect_qty_$id";
		}
		foreach($sew_fin_woven_defect_array as $id=>$val) {
			$sql.= ", sum(case when a.defect_type_id in (7) and a.defect_point_id=$id then a.defect_qty else 0 end) as measurmentdefect_qty_$id";
		}
		foreach($sew_fin_spot_defect_type as $key=>$val) {
			if($prod_type==5){
				$sql.= ", sum(case when a.defect_type_id in(2) and a.defect_point_id=$key then a.defect_qty else 0 end) as sptdefect_qty_$key";
			}
			else
			{
				$sql.= ", sum(case when a.defect_type_id=2 and a.defect_point_id=$key then a.defect_qty else 0 end) as sptdefect_qty_$key";
			}
		}
		$sql.= " from pro_gmts_prod_dft a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst d where d.id=a.mst_id and a.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.production_type='$prod_type' and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by b.buyer_name, c.po_number,c.grouping,c.file_no, c.po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity, d.alter_qnty, d.reject_qnty, d.spot_qnty, d.company_id, d.serving_company,d.floor_id, b.style_ref_no";
		/*$sql = "select b.buyer_name, c.po_number, c.grouping, c.file_no, c.po_quantity as po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity as qs_pass_qty, d.alter_qnty as alter_qnty, d.reject_qnty as reject_qnty, d.spot_qnty as spot_qnty from wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst d where b.job_no=c.job_no_mst and d.production_type='$prod_type' and d.status_active=1 and d.is_deleted=0 $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by b.buyer_name, c.po_number, c.grouping, c.file_no, c.po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity, d.alter_qnty, d.reject_qnty, d.spot_qnty";*/
		// echo $sql;
		$result = sql_select($sql); $prodIds=""; $prod_qty_arr=array(); $defect_line_arr=array(); $order_colspan_arr=array(); $sewlineArr=array();
		foreach($result as $row)
		{
			$prodIds.=$row[csf("id")].",";
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['po_data']=$row[csf('buyer_name')].'**'.$row[csf('po_number')].'**'.$row[csf('po_quantity')].'**'.$row[csf('grouping')].'**'.$row[csf('file_no')].'**'.$row[csf('company_id')].'**'.$row[csf('serving_company')].'**'.$row[csf('style_ref_no')].'**'.$row[csf('floor_id')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['qc']+=$row[csf('qs_pass_qty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['alt']+=$row[csf('alter_qnty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['spt']+=$row[csf('spot_qnty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['rjt']+=$row[csf('reject_qnty')];
			
			$sewlineArr[$row[csf('id')]]=$row[csf('sewing_line')];
			
			foreach($sew_fin_woven_defect_array as $id=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$id][1]+=$row[csf('altdefect_qty_'.$id)];
			}

			foreach($sew_fin_woven_defect_array as $id=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$id][4]+=$row[csf('backdefect_qty_'.$id)];
			}

			foreach($sew_fin_woven_defect_array as $id=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$id][5]+=$row[csf('westbanddefect_qty_'.$id)];
			}

			foreach($sew_fin_measurment_check_array as $id=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$id][3]+=$row[csf('measurmentdefect_qty_'.$id)];
			}
			
			foreach($sew_fin_spot_defect_type as $key=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$key][2]+=$row[csf('sptdefect_qty_'.$key)];
			}
			
			$order_colspan_arr[$row[csf('buyer_name')]][$row[csf('po_break_down_id')]]+=1;
		}
		unset($result);
		
		/*$exprodid=array_filter(array_unique(explode(",",$prodIds)));
		$tot_rows=count($exprodid);
		$prodIds=implode(",",$exprodid);
		$mstIdCond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$mstIdCond=" and (";
			$prodIdsArr=array_chunk(explode(",",$exprodid),999);
			foreach($prodIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$mstIdCond.=" mst_id in($ids) or";
			}
			$mstIdCond=chop($mstIdCond,'or ');
			$mstIdCond.=")";
		}
		else
		{
			$mstIdCond=" and mst_id in($prodIds)";
		}
		
		$sqlDft = "select mst_id, po_break_down_id ";
		foreach($sew_fin_alter_defect_type as $id=>$val) {
			$sqlDft.= ", sum(case when defect_type_id=$defect_type and defect_point_id=$id then defect_qty else 0 end) as altdefect_qty_$id";
		}
		foreach($sew_fin_spot_defect_type as $key=>$val) {
			if($prod_type==5){
				$sqlDft.= ", sum(case when defect_type_id in(2,4) and defect_point_id=$key then defect_qty else 0 end) as sptdefect_qty_$key";
			}
			else
			{
				$sqlDft.= ", sum(case when defect_type_id=2 and defect_point_id=$key then defect_qty else 0 end) as sptdefect_qty_$key";
			}
		}
		$sqlDft.= " from pro_gmts_prod_dft where status_active=1 and is_deleted=0 $mstIdCond group by mst_id, po_break_down_id";
		
		$sqlDftData = sql_select($sqlDft);
		foreach($sqlDftData as $row)
		{
			$sewing_line=$sewlineArr[$row[csf('mst_id')]];
			foreach($sew_fin_alter_defect_type as $id=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$sewing_line][$id][1]+=$row[csf('altdefect_qty_'.$id)];
			}
			
			foreach($sew_fin_spot_defect_type as $key=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$sewing_line][$key][2]+=$row[csf('sptdefect_qty_'.$key)];
			}
		}
		unset($sqlDftData);*/
		
		//var_dump($sew_fin_alter_defect_type);
		
		
		
		$i=1;
		$alt_dft_qty=array(); $spt_dft_qty=array(); $back_dft_qty=array(); $westband_dft_qty=array();  $measurment_dft_qty=array(); 
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
				$int_ref=$ex_po_data[3];
				$file_no=$ex_po_data[4];
				$company_id=$ex_po_data[5];
				$serving_company=$ex_po_data[6];
				$style_ref_no=$ex_po_data[7];
				$floor_id=$ex_po_data[8];
				
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
                	<? //if($k==1) { ?>
					<td width="30"><? echo $i; $i++; ?></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $company_library[$company_id]; ?></div></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $company_library[$serving_company]; ?></div></td>

					<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyerArr[$buyer_id]; ?></div></td>
					<td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $int_ref; ?></div></td>
					<td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $style_ref_no; ?></div></td>
					<td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $po_number; ?></div></td>
					<td width="100" align="right"><? if($po_qty>0) echo number_format($po_qty,2); $tot_po_qty+=$po_qty; ?></td>
					<td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $sewing_floor_arr[$floor_id]; ?></div></td>
					<!-- <td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<?// echo $file_no; ?></div></td> -->
					
                    <? //} else { ?>
					<!-- <td width="30">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td> -->
					<? //} 
					$k++; if ($prod_type==5) {?><td width="100" align="center"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $line_name; ?></div></td><? }?>
					<? 
					$row_tot_alt=0; $row_tot_spt=0;
					foreach($sew_fin_woven_defect_array as $id=>$val) {
						$alt_dft_value=0;
						$alt_dft_value=$defect_line_arr[$po_id][$line_id][$id][1];
						$alt_dft_qty[$id]+=$alt_dft_value;
					?>
					<td width="80" align="right">&nbsp;<? if($alt_dft_value>0) echo number_format($alt_dft_value,2); 
					$row_tot_alt+=$alt_dft_value;
					?></td><? } ?>

                    <? 
					    $row_tot_back=0;
					    foreach($sew_fin_woven_defect_array as $id=>$val) {
						$back_dft_value=0;
						$back_dft_value=$defect_line_arr[$po_id][$line_id][$id][4];
						$back_dft_qty[$id]+=$back_dft_value;
					?>
					<td width="80" align="right">&nbsp;<? if($back_dft_value>0) echo number_format($back_dft_value,2); 
					    $row_tot_back+=$back_dft_value;
					?></td> <? } ?>

                    <? 
					    $row_tot_westband=0;
					    foreach($sew_fin_woven_defect_array as $id=>$val) {
						$westband_dft_value=0;
						$westband_dft_value=$defect_line_arr[$po_id][$line_id][$id][5];
						$westband_dft_qty[$id]+=$westband_dft_value;
						// echo "<pre>";
						// print_r($westband_dft_qty);
					?>
					<td width="80" align="right">&nbsp;<? if($westband_dft_value>0) echo number_format($westband_dft_value,2); 
					    $row_tot_westband+=$westband_dft_value;
					?></td> <? } ?>

                    <? 
					    $row_tot_measurment=0;
					    foreach($sew_fin_measurment_check_array as $id=>$val) {
						$measurment_dft_value=0;
						$measurment_dft_value=$defect_line_arr[$po_id][$line_id][$id][3];
						// echo $measurment_dft_value;die;
						$measurment_dft_qty[$id]+=$measurment_dft_value;
					?>
					<td width="80" align="right">&nbsp;<? if($measurment_dft_value>0) echo number_format($measurment_dft_value,2); 
					 $row_tot_measurment+=$measurment_dft_value;
					?></td> <? } ?>

					<? foreach($sew_fin_spot_defect_type as $key=>$value) {
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
					<td title="Alter % To Total QC = (Total Gmts Alter / Total QC Pcs.)*100" width="90" align="right">&nbsp;<? $row_tot_alt_per=0; $row_tot_alt_per=($alter_qty/$pcs_inspaction)*100; if($row_tot_alt_per>0) echo number_format($row_tot_alt_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<? if($spot_qty>0) echo number_format($spot_qty,2); ?></td>
					<td title="Total Gmts Spot = (Total Gmts Spot / Total QC Pcs.)*100" width="90" align="right">&nbsp;<? $row_tot_spt_per=0; $row_tot_spt_per=($spot_qty/$pcs_inspaction)*100; if($row_tot_spt_per>0) echo number_format($row_tot_spt_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<? if($reject_qty>0) echo number_format($reject_qty,2); ?></td>
					<td title="Total Gmts Reject = (Total Gmts Reject / Total QC Pcs.)*100" width="90" align="right">&nbsp;<? $reject_qnty_per=0; $reject_qnty_per=($reject_qty/$pcs_inspaction)*100; if($reject_qnty_per>0) echo number_format($reject_qnty_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<? echo number_format($row_tot_defect,2); ?></td>
					<td title="Total Gmts Defect = (Total Gmts Defect / Total QC Pcs.)*100" width="90" align="right">&nbsp;<? $row_tot_defect_per=0; $row_tot_defect_per=($row_tot_defect/$pcs_inspaction)*100; if($row_tot_defect_per>0) echo number_format($row_tot_defect_per,2).'%'; ?></td>
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
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">Total</td>
				<td width="100" align="right"><? echo number_format($tot_po_qty,2); ?></td>
				<? if ($prod_type==5) {?><td width="100">&nbsp;</td><? } ?>
				<td width="100">&nbsp;</td>
				<?
				foreach($sew_fin_woven_defect_array as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty[$id],2); 
				?></td> <? } ?>

                <?
				foreach($sew_fin_woven_defect_array as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($back_dft_qty[$id],2); 
				?></td><? } ?>

                <?
				foreach($sew_fin_woven_defect_array as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($westband_dft_qty[$id],2); 
				?></td><? } ?>

                <?
				foreach($sew_fin_measurment_check_array as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($measurment_dft_qty[$id],2); 
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
				<td width="200" colspan="6" style="font-size:12px">Defect Counting % To Total QC</td>
				<? if ($prod_type==5) {?><td width="100">&nbsp;</td><? }?>
				<td width="100">&nbsp;</td>
				<? 
				$alt_dft_qty_per=array(); $spt_dft_qty_per=array(); $measurment_dft_qty_per=array(); $back_dft_qty_per=array();
				foreach($sew_fin_woven_defect_array as $id=>$val) { 
				$alt_dft_qty_per[$id]=($alt_dft_qty[$id]/$tot_pcs_inspaction)*100;
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty_per[$id],2)."%"; 
				?></td>

                <? } foreach($sew_fin_woven_defect_array as $key=>$value) {
					$back_dft_qty_per[$key]=($back_dft_qty[$key]/$tot_pcs_inspaction)*100;
					?>
				<td width="80" align="right"><? echo number_format($back_dft_qty_per[$key],2).'%'; ?></td>

                <? } foreach($sew_fin_woven_defect_array as $key=>$value) {
					$westband_dft_qty_per[$key]=($westband_dft_qty[$key]/$tot_pcs_inspaction)*100;
					?>
				<td width="80" align="right"><? echo number_format($westband_dft_qty_per[$key],2).'%'; ?></td>

				<? } foreach($sew_fin_measurment_check_array as $key=>$value) {
					$measurment_dft_qty_per[$key]=($measurment_dft_qty[$key]/$tot_pcs_inspaction)*100;
					?>
				<td width="80" align="right"><? echo number_format($measurment_dft_qty_per[$key],2).'%'; ?></td>

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
			<div align="center" style="width:2850px; height:500px;  margin-left:20px; border:solid 1px">
				<table style="margin-left:60px; font-size:12px" align="left">
				<tr>
					<td align="left" bgcolor="red" width="10"></td>
					<td><? echo $production_type[$prod_type]; ?> QC CHART</td>
				</tr>
				</table>
				<canvas id="canvas" height="400" width="2800"></canvas>
			</div>
		</div>
		<?
		$reject_qty_type=array(0=>"Reject");
		$bar_arr=array();$val_arr=array();
		foreach($sew_fin_woven_defect_array as $kk=>$vv)
		{
			$bar_arr[]=$vv;
			$val_arr[]=$alt_dft_qty[$kk];
		}

		foreach($sew_fin_woven_defect_array as $kk=>$vv)
		{
			$bar_arr[]=$vv;
			$val_arr[]=$back_dft_qty[$kk];
		}

		foreach($sew_fin_woven_defect_array as $kk=>$vv)
		{
			$bar_arr[]=$vv;
			$val_arr[]=$westband_dft_qty_per[$kk];
		}

		foreach($sew_fin_measurment_check_array as $key=>$val)
		{
			$bar_arr[]=$val;
			$val_arr[]=$measurment_dft_qty[$key];
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
    foreach (glob("$user_id*.xls") as $filename) {
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