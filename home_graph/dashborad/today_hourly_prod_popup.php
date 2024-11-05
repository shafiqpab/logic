<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$lineArr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');

if($action=="today_hourly_prod_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	list($company,$pro_company,$location,$floor)=explode("__",$cp);
	
	if($db_type==0)
	{
		$today=date('Y-m-d');
	}
	else
	{
		$today=date('d-M-Y');
	}
		 
	
	if( $location!=0 )
	{
		$location_con="and a.location_id=$location";
	}
	else
	{
		$location_con="";
	}
	
	
	if( $pro_company!=0 )
	{
		$company_cond=" and comp.id=$company";
		$company_field=" c.serving_company";
		$company_field_1=" serving_company";
	}
	elseif( $company!=0 )
	{
		$company_cond=" and comp.id=$company";
		$company_field=" c.company_id";
		$company_field_1=" company_id";
	}
	else
	{
		$company_cond="";
	}

	
	
	if($db_type==0)
	{
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		
		 $dataArray=sql_select("select TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( d.prod_start_time, '%H' ) as start_hour, TIME_FORMAT( d.prod_start_time, '%i' ) as start_min, TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in ($manufacturing_company) and d.shift_id=1 and b.pr_date='$today' and a.id='$line_id'");
	}
	else
	{
		
		$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		
		$dataArray=sql_select("select TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(d.prod_start_time,'HH24') as start_hour, TO_CHAR(d.prod_start_time,'MI') as start_min, TO_CHAR(d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in ($manufacturing_company) and d.shift_id=1 and b.pr_date='$today' and a.id='$line_id'");
	}
	
	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and status_active=1 and is_deleted=0");
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		
	$item_smv_array=array();
	//$smv_source=2;
	if($smv_source==3)
	{
		$sql_item="select po_job_no, sam_style, gmts_item_id from ppl_gsd_entry_mst where status_active=1 and is_deleted=0";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('po_job_no')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		$sql_item="select job_no, gmts_item_id, smv_pcs, smv_pcs_precost from wo_po_details_mas_set_details";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('job_no')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
			$item_smv_array[$itemData[csf('job_no')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
		}
	}
		
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<fieldset style="width:880px; margin-left:4px; margin-top:2px">
    	<div style="width:870px;" align="center"><b>Line No- <? echo $line_name; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" /></div>
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="870" cellpadding="0" cellspacing="0">
				<thead>
                    <tr>
                    	<th width="120">Buyer</th>
                        <th width="100">Job No</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">Order No</th>
                        <th width="150">Item</th>
                        <th width="70">SMV</th>
                        <th width="70">Sewing WIP</th>
                        <th width="70">Days Run</th>
                        <th>Order Type</th>
                    </tr>
				</thead>
                <?
				$sewInDataArr=array();
				$sql="select po_break_down_id, item_number_id, 
										min(CASE WHEN production_type=4 THEN production_date END) AS frstinput_date,
										sum(CASE WHEN production_type=4 THEN production_quantity END) as inputqty,
										sum(CASE WHEN production_type=5 THEN production_quantity END) as outqty
										from pro_garments_production_mst 
										where status_active=1 and is_deleted=0 and location=$location and floor_id=$floor and sewing_line='$line_id' and production_type in(4,5) and $company_field_1 in($manufacturing_company) and production_date='$today' group by po_break_down_id, item_number_id";
				$sewInData=sql_select($sql);
				//echo $sql;
				
				foreach($sewInData as $inRow)
				{
					$sewInDataArr[$inRow[csf('po_break_down_id')]][$inRow[csf('item_number_id')]]['fid']=$inRow[csf('frstinput_date')];
					$sewInDataArr[$inRow[csf('po_break_down_id')]][$inRow[csf('item_number_id')]]['qty']=$inRow[csf('inputqty')];
					$sewInDataArr[$inRow[csf('po_break_down_id')]][$inRow[csf('item_number_id')]]['outqty']=$inRow[csf('outqty')];
				}
				
                $i=1; $total_smv_achv=0; $job_no='';// and c.production_date='$today'
				$sql="select a.id, a.po_number, a.job_no_mst, b.buyer_name, b.style_ref_no, c.item_number_id, sum(c.production_quantity) as outqty from wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c where a.job_no_mst=b.job_no and c.po_break_down_id=a.id and $company_field in($manufacturing_company) and c.sewing_line='$line_id' and c.production_date='$today' and c.production_type=5 and c.status_active=1 and c.is_deleted=0 group by a.id, a.po_number, a.job_no_mst, b.buyer_name, b.style_ref_no, c.item_number_id";
                
				//echo $sql;
				
				$result=sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$inputqty=$sewInDataArr[$row[csf('id')]][$row[csf('item_number_id')]]['qty'];
					//$outqty=$row[csf('outqty')];
					$outqty=$sewInDataArr[$row[csf('id')]][$row[csf('item_number_id')]]['outqty'];
					$sewing_wip=$inputqty-$outqty;
					$productioQty+=$outqty;
					$item_smv=0;
					if($smv_source==2)
					{
						$item_smv=$item_smv_array[$row[csf('job_no_mst')]][$row[csf('item_number_id')]]['smv_pcs_precost'];
					}
					else if($smv_source==3)
					{
						$item_smv=$item_smv_array[$row[csf('job_no_mst')]][$row[csf('item_number_id')]];	
					}
					else
					{
						$item_smv=$item_smv_array[$row[csf('job_no_mst')]][$row[csf('item_number_id')]]['smv_pcs'];	
					}
					
					$job_no.=$row[csf('job_no_mst')].",";
					
					$fstinput_date=$sewInDataArr[$row[csf('id')]][$row[csf('item_number_id')]]['fid'];
					$days_run=return_field_value("count(b.id) as days","lib_capacity_calc_mst a, lib_capacity_calc_dtls b", "a.id=b.mst_id and a.comapny_id in($manufacturing_company) and b.date_calc between '$fstinput_date' and '$today' and day_status=1","days");
					$smv_achv=$item_smv*$row[csf('outqty')];
					$total_smv_achv+=$smv_achv;
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                        <td><p><? echo $row[csf('job_no_mst')]; ?></p></td>
                        <td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td align="right">&nbsp;<? echo $item_smv; ?></p></td>
                        <td align="right">&nbsp;<? echo $sewing_wip; ?></td>
                        <td align="right">&nbsp;<? echo $days_run; ?></td>
                        <td align="center">Self</td>
                    </tr>
                <?
                	$i++;
                }
				
				$sql_subconProd="select distinct a.party_id, a.subcon_job, b.order_no, b.smv, c.gmts_item_id, b.cust_style_ref FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_gmts_prod_dtls c WHERE a.subcon_job=b.job_no_mst and c.order_id=b.id and a.company_id in($manufacturing_company) and c.production_type=2  and c.line_id='$line_id' and c.production_date='$today' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				$result=sql_select($sql_subconProd);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					
					$item_smv=$row[csf('smv')];
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td><p><? echo $buyer_library[$row[csf('party_id')]]; ?></p></td>
                        <td><p><? echo $row[csf('subcon_job')]; ?></p></td>
                        <td><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
                        <td><p><? echo $row[csf('order_no')]; ?></p></td>
                        <td><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                        <td align="right">&nbsp;<? echo $item_smv; ?></p></td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
                        <td align="center">Subcontract</td>
                    </tr>
                <?
                	$i++;
                }	
                ?>
            </table>
            <table style="margin-top:5px" border="1" class="rpt_table" rules="all" width="870" cellpadding="0" cellspacing="0">
				<thead>
                    <tr>
                    	<th width="100">Man Power</th>
                        <th width="100">Operator</th>
                        <th width="100">Helper</th>
                        <th width="90">Machine</th>
                        <th width="90">Working Hour</th>
                        <th width="70">Current Working Hour</th>
                        <th width="70">Production Qty</th>
                        <th width="140">Line Chief</th>
                        <th>Eff. %</th>
                    </tr>
				</thead>
                <?
                $i=1; $mast_dtl_id='';
				$start_hour=$dataArray[0][csf('start_hour')]; $start_min=$dataArray[0][csf('start_min')]; 
				$curr_hour=date("H"); $curr_min=date("i");
				$start_time=date("Y-m-d ").$start_hour.":00"; $launch_start_time=date("Y-m-d ").$dataArray[0][csf('lunch_start_time')]; $curr_time=date("Y-m-d H:00");
				$sql="select b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, (b.working_hour*man_power*60) as tsmv, b.working_hour, b.mast_dtl_id, b.smv_adjust, b.smv_adjust_type from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id in($manufacturing_company) and a.id='$line_id' and pr_date='$today' $location_con and a.floor_id=$floor order by a.location_id, a.floor_id, a.line_number";
                //echo $manufacturing_company;
				 //echo $sql;
				$result=sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					
					$mast_dtl_id=$row[csf('mast_dtl_id')];
					
					//$curr_working_hour=(strtotime($curr_hour.":00")-strtotime($start_hour.":00"))/(60*60);
					$curr_working_hour=datediff( h, $start_time, $curr_time);
					
					if($start_min>$curr_min)
					{
						$curr_working_hour=$curr_working_hour-1;
					}
					
					if($curr_time>$launch_start_time)
					{
						$curr_working_hour=$curr_working_hour-1;
					}
					
					if($curr_working_hour>$row[csf('working_hour')]) 
					{
						$curr_working_hour=$row[csf('working_hour')];
					}

					//$smv_used=$row[csf('tsmv')];
					$smv_used=$curr_working_hour*$row[csf('man_power')]*60;
					if($curr_working_hour>=$row[csf('working_hour')]) 
					{
						if($row[csf('smv_adjust_type')]==1) $smv_used+=$row[csf('smv_adjust')];
						else if($row[csf('smv_adjust_type')]==2) $smv_used-=$row[csf('smv_adjust')];
					}
					
					$avg_aff_perc=$total_smv_achv/$smv_used*100;
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr2_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $i;?>">
                    	<td align="right" style="padding-right:2px">&nbsp;<? echo $row[csf('man_power')]; ?></td>
                    	<td align="right" style="padding-right:2px">&nbsp;<? echo $row[csf('operator')]; ?></td>
                        <td align="right" style="padding-right:2px">&nbsp;<? echo $row[csf('helper')]; ?></td>
                        <td align="right" style="padding-right:2px">&nbsp;<? echo $row[csf('active_machine')]; ?></td>
                        <td align="right" style="padding-right:2px">&nbsp;<? echo $row[csf('working_hour')]; ?></td>
                        <td align="right" style="padding-right:2px">&nbsp;<? echo $curr_working_hour; ?></td>
                        <td align="right" style="padding-right:2px">&nbsp;<? echo $productioQty; ?></td>
                        <td><p><? echo $row[csf('line_chief')]; ?>&nbsp;</p></td>
                        <td align="right" style="padding-right:2px">&nbsp;<? echo number_format($avg_aff_perc,2); ?></td>
                    </tr>
                <?
                	$i++;
                }
                ?>
            </table>
            <table style="margin-top:5px">
				<?
                    $x=1;
                    $job_no="'".implode("','",array_unique(explode(",",substr($job_no,0,-1))))."'";
                    $nameArray=sql_select( "select master_tble_id, image_location from common_photo_library where master_tble_id in($job_no) and form_name='knit_order_entry' and file_type=1 order by master_tble_id" );
                    if($nameArray>0) echo "<tr>";
                    foreach ($nameArray as $inf)
                    {
                    ?>
                        <td width="100">
                            <img src="../../<? echo $inf[csf("image_location")]; ?>" height="97" width="89" />
                        </td>
                    <?	
                        if($x%8==0){ echo "</tr><tr>"; }
                        $x++;
                    }
                    echo "</tr>";
                ?>	
            </table>
            <div style="width:100%;" align="left"><b>Remarks:</b></div>
            <div style="margin-top:5px;width:50%;float:left; position:relative;">
				<?
					$x=1;
					$line_remarks=return_field_value("remarks","prod_resource_dtls_time", "mast_dtl_id='$mast_dtl_id' and shift_id=1");
                    if($line_remarks!="") 
					{ 
						echo $x.". ".$line_remarks.'<br>'; $x++;
					}
					
                    if($db_type==2)
                    {
                        $sql_remarks=sql_select("select distinct(remarks) as remarks from pro_garments_production_mst where sewing_line='$line_id' and production_date='$today' and status_active=1 and is_deleted=0 and remarks is not null union select distinct(remarks) as remarks from subcon_gmts_prod_dtls where line_id='$line_id' and production_date='$today' and status_active=1 and is_deleted=0 and production_type=2 and remarks is not null");
                    }
                    else
                    {
                        $sql_remarks=sql_select("select distinct(remarks) as remarks from pro_garments_production_mst where sewing_line='$line_id' and production_date='$today' and status_active=1 and is_deleted=0 and remarks<>'' union select distinct(remarks) as remarks from subcon_gmts_prod_dtls where line_id='$line_id' and production_date='$today' and status_active=1 and is_deleted=0 and production_type=2 and remarks<>''");
                    }
					
                    foreach($sql_remarks as $remRow)
                    {
                        echo $x.'. '.$remRow[csf('remarks')].'<br>';
						if($x%8==0){ echo '</div><div style="margin-top:5px;width:50%;float:left; position:relative;">'; }
                        $x++;
                    }
                ?>
            </div>
		</div>
	</fieldset>  
	<?
	exit();
}

if($action=="today_dhu_prod_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	//list($company,$pro_company,$location,$floor)=explode("__",$cp);

	$cbo_company_name=$company_id;
	$cbo_working_company_id=$pro_company_id;
	$cbo_location=$location_id;
	$cbo_floor=$floor_id;
	//echo $cbo_production_type;die;
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	

	
	if($db_type==2) $production_date=date("d-M-Y");
	else 			$production_date=date("Y-m-d");
	$location = str_replace("'","",$cbo_location);
	
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_name";

	$txt_date=" and a.production_date='".$production_date."'";

	if ($location==0) $location_cond=""; else $location_cond=" and a.location=".$location." "; 
	if(str_replace("'","",$cbo_floor)==0) $floor_name="";else $floor_name=" and a.floor_id=$cbo_floor";
	$prod_type=str_replace("'","",$cbo_production_type);
	
	
	if(str_replace("'","",$cbo_company_name)==0) $company_qc_cond=""; else $company_qc_cond=" and d.company_id=$cbo_company_name";
		
	if(str_replace("'","",$cbo_working_company_id)==0) $company_working_cond=""; else $company_working_cond=" and d.serving_company=$cbo_working_company_id";
		
	$prod_date_qc_cond=" and d.production_date='".$production_date."'";


	if ($location==0) $location_qc_cond=""; else $location_qc_cond=" and d.location=".$location." "; 
	if(str_replace("'","",$cbo_floor)==0) $floor_name_cond="";else $floor_name_cond=" and d.floor_id=$cbo_floor";
	$prod_qty_arr=array(); $defect_line_arr=array(); $order_colspan_arr=array();
	
	
	
	

		$days_run_sql = "SELECT 	d.sewing_line,b.style_ref_no,d.production_date		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f	WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3)  	$company_qc_cond $company_working_cond   $location_qc_cond $floor_name_cond 
		group by d.sewing_line,b.style_ref_no,d.production_date	";	

		foreach(sql_select($days_run_sql) as $vals)
		{
			$style_wise_days[$vals[csf('style_ref_no')]][$vals[csf('sewing_line')]]+=1;
		}  


		if(str_replace("'","",$cbo_production_type)==5)
		{
			$sql = "SELECT f.mst_id, b.style_ref_no, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo, sum(f.production_qnty) as qc_pass_qty, sum(f.alter_qty) as alter_qnty, sum(case when is_rescan=0 then  f.reject_qty else 0 end )-sum(case when is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty_bk,sum(case when is_rescan=0 then  f.reject_qty else 0 end )  as reject_qnty,  sum(f.spot_qty) as spot_qnty, sum(f.replace_qty) as replace_qty,sum(case when is_rescan=1 then f.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst  and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond $company_working_cond   $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,d.sewing_line, d.prod_reso_allo ";

			 $reject_sql = "SELECT f.bundle_no, f.mst_id, b.style_ref_no, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo,    sum(case when is_rescan=0 then  f.reject_qty else 0 end ) as reject_qnty,sum(case when is_rescan=1 then f.production_qnty else 0 end ) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1  and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond  $company_working_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond  group by f.bundle_no, f.mst_id,b.style_ref_no,d.sewing_line, d.prod_reso_allo ";
			//echo $sql;die;
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
					$line_def_rej_qty_arr2[$row[csf('sewing_line')]]+=$row[csf('reject_qnty')]-$row[csf('replace_qty')];

					$line_def_replace_qty_arr2[$row[csf('sewing_line')]]+=$row[csf('replace_qty')]; 
				}

			}

		}
		elseif (str_replace("'","",$cbo_production_type)==11) 
		{
			$sql = "SELECT f.mst_id, b.style_ref_no, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo, sum(f.production_qnty) as qc_pass_qty, d.alter_qnty as alter_qnty, sum(case when is_rescan=0 then  f.reject_qty else 0 end )-sum(case when is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty, d.spot_qnty as spot_qnty, sum(f.replace_qty) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_qc_cond $company_working_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,d.sewing_line, d.prod_reso_allo,d.alter_qnty,d.spot_qnty ";
		}
   
	  

   		//echo  $sql;	
		
		
		
		$result = sql_select($sql);
		$count=0;
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
			
			if($line_name!=""){
			$mst_id_arr[$row[csf('mst_id')]]=$row[csf('mst_id')];
			$prod_line_arr[$row[csf('sewing_line')]]=$line_name;			//$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
			$qc_qty_arr[$row[csf('sewing_line')]]+=($row[csf('qc_pass_qty')]+$row[csf('alter_qnty')]+$row[csf('reject_qnty')]+$row[csf('spot_qnty')]) ;
			$qc_pass_arr[$row[csf('sewing_line')]]+=($row[csf('qc_pass_qty')]);
			//$qc_qty_arr[$row[csf('sewing_line')]]+=$row[csf('qc_pass_qty')];
			//$prod_def_qty_arr[$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
			 
				$line_def_rej_qty_arr[$row[csf('sewing_line')]]+=$row[csf('reject_qnty')];
				$line_def_replace_qty_arr[$row[csf('sewing_line')]]+=$row[csf('replace_qty')];
			 
			 
			$line_def_alt_qty_arr[$row[csf('sewing_line')]]+=$row[csf('alter_qnty')];
			$line_def_spot_qty_arr[$row[csf('sewing_line')]]+=$row[csf('spot_qnty')];
			
			$line_def_rescan_qty_arr[$row[csf('sewing_line')]]+=$row[csf('today_rescan')];
			$style_arr[$row[csf('sewing_line')]][$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];
			
			
			
			
			}
		
		}
		unset($result);	
		
	
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
		

		$defect_type_id=" and  a.defect_type_id in (3,4) ";
		if($prod_type==11)$defect_type_id=" and  a.defect_type_id in (1,2)";
		
		$sql = "SELECT a.defect_type_id, a.defect_point_id, sum(a.defect_qty) as defect_qty,sum(d.reject_qnty) as reject_qnty, d.sewing_line 
		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE b.job_no=c.job_no_mst and d.po_break_down_id=c.id and d.id=a.mst_id $pre_cost_id_cond $defect_type_id and a.production_type=$prod_type and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and c.status_active in(1,2,3)  $prod_date_qc_cond $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond
		group by d.sewing_line ,a.defect_type_id, a.defect_point_id";
		
		//echo $sql;
		
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]=$row[csf('defect_qty')];
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
		$type=1;
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
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" >Defect Per Hundred Unit Report (<? echo $production_type[$prod_type]; ?>)</td>
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
					<? echo "Production Date: $production_date" ;?>
					</td>
				</tr>
			</table>
			<br /> 

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
	                     <th><? echo number_format(array_sum($prod_def_qty_arr)/array_sum($qc_qty_arr)*100,2);?></th>
	                </tr>

	                 <tr>
	                    <th style="background:#DDD">Days of Run</th>
	                    <?php foreach($prod_line_arr as $line_id=>$line_id)
	                    {
	                    	$styles=$style_arr[$line_id];
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
					
					echo '<td width="80" align="right">'.$prod_qty_arr[$alter][$sfad_id][$line_id].'</td>';
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
			} 

			foreach($sew_fin_spot_defect_type_new as $sfsd_id=>$sfsd_name){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $i; ?></td>
				<td ><p><? echo $sfsd_name;?></p></td>
				<?php foreach($prod_line_arr as $line_id=>$line_id){
					echo '<td width="80" align="right">'.$prod_qty_arr[$spot][$sfsd_id][$line_id].'</td>';
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
			}

			?>
			</table>
	        </div>
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
	                		$tot_rej_qty=$line_def_rej_qty_arr2[$line_id];
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
	            			<th width="130">Total Reject Replace</th>
	            			<?php 
	            			$total_rescan_qty=0;
	            			foreach($prod_line_arr as $line_id=>$line_id){

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
	        
	        
	        </div>
			
			<?

		}
	    exit();

}

if($action=="today_dhu_prod_per_line_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	//list($company,$pro_company,$location,$floor)=explode("__",$cp);

	$cbo_company_name=$company_id;
	$cbo_working_company_id=$pro_company_id;
	$cbo_location=$location_id;
	$cbo_floor=$floor_id;

	//echo $cbo_production_type;die;
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	

	
	if($db_type==2) $production_date=date("d-M-Y");
	else 			$production_date=date("Y-m-d");
	$location = str_replace("'","",$cbo_location);
	
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_name";

	$txt_date=" and a.production_date='".$production_date."'";

	if ($location==0) $location_cond=""; else $location_cond=" and a.location=".$location." "; 
	if(str_replace("'","",$cbo_floor)==0) $floor_name="";else $floor_name=" and a.floor_id=$cbo_floor";
	$prod_type=str_replace("'","",$cbo_production_type);
	
	
	if(str_replace("'","",$cbo_company_name)==0) $company_qc_cond=""; else $company_qc_cond=" and d.company_id=$cbo_company_name";
		
	if(str_replace("'","",$cbo_working_company_id)==0) $company_working_cond=""; else $company_working_cond=" and d.serving_company=$cbo_working_company_id";
		
	$prod_date_qc_cond=" and d.production_date='".$production_date."'";


	if ($location==0) $location_qc_cond=""; else $location_qc_cond=" and d.location=".$location." "; 
	if(str_replace("'","",$cbo_floor)==0) $floor_name_cond="";else $floor_name_cond=" and d.floor_id=$cbo_floor";
	$prod_qty_arr=array(); $defect_line_arr=array(); $order_colspan_arr=array();
	
	
	
	

		$days_run_sql = "SELECT 	d.sewing_line,b.style_ref_no,d.production_date,TO_CHAR(d.production_hour,'HH24:MI') as production_hour		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f	WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) and d.sewing_line=$line_id  	$company_qc_cond $company_working_cond   $location_qc_cond $floor_name_cond 
		group by d.sewing_line,d.production_hour,b.style_ref_no,d.production_date	";	

		foreach(sql_select($days_run_sql) as $vals)
		{
			$style_wise_days[$vals[csf('style_ref_no')]][$vals[csf('production_hour')]]+=1;
		}  


		if(str_replace("'","",$cbo_production_type)==5)
		{
			$sql = "SELECT f.mst_id, b.style_ref_no,TO_CHAR(d.production_hour,'HH24:MI') as production_hour, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo, sum(f.production_qnty) as qc_pass_qty, sum(f.alter_qty) as alter_qnty, sum(case when is_rescan=0 then  f.reject_qty else 0 end )-sum(case when is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty_bk,sum(case when is_rescan=0 then  f.reject_qty else 0 end )  as reject_qnty,  sum(f.spot_qty) as spot_qnty, sum(f.replace_qty) as replace_qty,sum(case when is_rescan=1 then f.production_qnty else 0 end ) as today_rescan FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst  and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) and d.sewing_line=$line_id  $company_qc_cond $company_working_cond   $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,d.sewing_line,d.production_hour, d.prod_reso_allo ";

			 $reject_sql = "SELECT f.bundle_no, f.mst_id, b.style_ref_no,TO_CHAR(d.production_hour,'HH24:MI') as production_hour, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo,    sum(case when is_rescan=0 then  f.reject_qty else 0 end ) as reject_qnty,sum(case when is_rescan=1 then f.production_qnty else 0 end ) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1  and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) and d.sewing_line=$line_id  $company_qc_cond  $company_working_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond  group by f.bundle_no, f.mst_id,b.style_ref_no,d.sewing_line,d.production_hour, d.prod_reso_allo ";
			//echo $sql;die;
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
					$line_def_rej_qty_arr2[$row[csf('production_hour')]]+=$row[csf('reject_qnty')]-$row[csf('replace_qty')];

					$line_def_replace_qty_arr2[$row[csf('production_hour')]]+=$row[csf('replace_qty')]; 
				}

			}

		}
		elseif (str_replace("'","",$cbo_production_type)==11) 
		{
			$sql = "SELECT f.mst_id, b.style_ref_no,TO_CHAR(d.production_hour,'HH24:MI') as production_hour, sum(c.po_quantity) as po_quantity, d.sewing_line, d.prod_reso_allo, sum(f.production_qnty) as qc_pass_qty, d.alter_qnty as alter_qnty, sum(case when is_rescan=0 then  f.reject_qty else 0 end )-sum(case when is_rescan=1 then f.production_qnty else 0 end ) as reject_qnty, d.spot_qnty as spot_qnty, sum(f.replace_qty) as replace_qty FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f WHERE d.production_type=$prod_type and f.production_type=$prod_type and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) and d.sewing_line=$line_id  $company_qc_cond $company_working_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by f.mst_id,b.style_ref_no,d.sewing_line,d.production_hour, d.prod_reso_allo,d.alter_qnty,d.spot_qnty ";
		}
   
	  

   		//echo  $sql;	
		
		
		
		$result = sql_select($sql);
		$count=0;
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
			
			if($line_name!=""){
				$mst_id_arr[$row[csf('mst_id')]]=$row[csf('mst_id')];
				$prod_line_arr[$row[csf('sewing_line')]]=$line_name;
				$prod_linehour_arr[$row[csf('production_hour')]]=$row[csf('production_hour')];			//$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
				$qc_qty_arr[$row[csf('production_hour')]]+=($row[csf('qc_pass_qty')]+$row[csf('alter_qnty')]+$row[csf('reject_qnty')]+$row[csf('spot_qnty')]) ;
				$qc_pass_arr[$row[csf('production_hour')]]+=($row[csf('qc_pass_qty')]);
				//$qc_qty_arr[$row[csf('sewing_line')]]+=$row[csf('qc_pass_qty')];
				//$prod_def_qty_arr[$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
				 
					$line_def_rej_qty_arr[$row[csf('production_hour')]]+=$row[csf('reject_qnty')];
					$line_def_replace_qty_arr[$row[csf('production_hour')]]+=$row[csf('replace_qty')];
				 
				 
				$line_def_alt_qty_arr[$row[csf('production_hour')]]+=$row[csf('alter_qnty')];
				$line_def_spot_qty_arr[$row[csf('production_hour')]]+=$row[csf('spot_qnty')];
				
				$line_def_rescan_qty_arr[$row[csf('production_hour')]]+=$row[csf('today_rescan')];
				$style_arr[$row[csf('production_hour')]][$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];
			
			}
		
		}
		unset($result);	
		
	
		
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
		

		$defect_type_id=" and  a.defect_type_id in (3,4) ";
		if($prod_type==11)$defect_type_id=" and  a.defect_type_id in (1,2)";
		
		$sql = "SELECT a.defect_type_id, a.defect_point_id,TO_CHAR(d.production_hour,'HH24:MI') as production_hour, sum(a.defect_qty) as defect_qty,sum(d.reject_qnty) as reject_qnty, d.sewing_line 
		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE b.job_no=c.job_no_mst and d.po_break_down_id=c.id and d.id=a.mst_id $pre_cost_id_cond $defect_type_id and a.production_type=$prod_type and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and c.status_active in(1,2,3) and d.sewing_line=$line_id   $prod_date_qc_cond $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond
		group by d.sewing_line ,d.production_hour,a.defect_type_id, a.defect_point_id";
		
		//echo $sql;
		
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$prod_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]][$row[csf('production_hour')]]=$row[csf('defect_qty')];
			$prod_def_qty_arr[$row[csf('production_hour')]]+=$row[csf('defect_qty')];
			$prod_def_rej_qty_arr[$row[csf('production_hour')]]+=$row[csf('reject_qnty')];
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
		$type=1;
		if($type==1)
		{	
		
			if ($prod_type==5 || $prod_type==11)
			{
				$tbl_width=(count($prod_line_arr)*80)+460;
			}
			
			
			?>
			<div style="width:<? echo $tbl_width+18;?>px;">
			<table cellspacing="0" width="<? echo $tbl_width;?>">
				<tr class="form_caption" style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" >Defect Per Hundred Unit Report (<? echo $production_type[$prod_type]; ?>)</td>
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
					<? echo "Production Date: $production_date" ;?>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
					<? echo "Line: $prod_line_arr[$line_id]" ;?>
					</td>
				</tr>
			</table>
			<br /> 

	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" align="left">
	            <thead>
	                <tr>
	                    <th width="30" rowspan="7">SL No</th>
	                    <th width="130">Hourly</th>
	                    <?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){echo '<th width="80">'.$line_hour.'</th>';}?>
	                    <th width="100"  title="Total sewing output=(total check-total reject-total alter-total spot)">Total Check</th>
	                    <th width="100" rowspan="6">DEFECT % AGAINST CHECK QTY</th>
	                    <th rowspan="6">DEFECT % AGAINST DEFECT QTY</th>
	                </tr>
	                <tr>
	                    <th style="background:#93cAAA" title="( Qc Pass + Alter+ Reject + Spot)">Check Qty</th>
	                    <?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
	                    	$tot_qc_pass= $qc_pass_arr[$line_hour];
	                    	$tot_qc_pass=array_sum($qc_pass_arr);
							echo '<th width="80" style="background:#93cAAA">'.$qc_qty_arr[$line_hour].'</th>';
						}?>
	                    <th title="Total Sewing Output =<? echo $tot_qc_pass;?>"><? echo array_sum($qc_qty_arr);?></th>
	                </tr>



	                 <tr>
	                    <th style="background:#93cAAA"  >Good Qty</th>
	                    <?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
	                    	$tot_qc_pass= $qc_pass_arr[$line_hour];
	                    	$tot_qc_pass=array_sum($qc_pass_arr);
							echo '<th width="80" style="background:#93cAAA">'.$qc_pass_arr[$line_hour].'</th>';
						}?>
	                    <th title="Total Sewing Output =<? echo $tot_qc_pass;?>"><? echo array_sum($qc_pass_arr);?></th>
	                </tr>
	                <tr>
	                    <th style="background:#DDD">DHU (%)</th>
	                    <?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
							$dhu=($prod_def_qty_arr[$line_hour]/$qc_qty_arr[$line_hour])*100;
							echo '<th width="80" style="background:#DDD">'.number_format($dhu,2).'</th>';
						
						}?>
	                     <th><? echo number_format(array_sum($prod_def_qty_arr)/array_sum($qc_qty_arr)*100,2);?></th>
	                </tr>

	                 <tr>
	                    <th style="background:#DDD">Days of Run</th>
	                    <?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name)
	                    {
	                    	$styles=$style_arr[$line_hour];
	                    	$days="";
	                    	foreach($styles as $style_id)
	                    	{
	                    		if($days=="") $days.=$style_wise_days[$style_id][$line_hour];
	                    		else $days.=','.$style_wise_days[$style_id][$line_hour];

	                    	}
							 
							echo '<th width="80" style="background:#DDD">'.$days.'</th>';
						
						}?>
	                     <th>&nbsp;</th>
	                </tr>


	                
	                <tr>
	                    <th style="background:#F96">STYLE NO</th>
	                    <?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
							echo '<th width="80" style="background:#F96"><p>'.implode(',',$style_arr[$line_hour]).'</p></th>';
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

			foreach($sew_fin_alter_defect_type_new as $sfad_id=>$sfad_name){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="30" align="center"><? echo $i; ?></td>
				<td width="130"><p><? echo $sfad_name;?></p></td>
				<?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
					
					$styles=array_unique(explode(",",$style_arr[$line_hour]));
					$line_def_rej_qty=0;
					foreach($styles as $sid)
					{
						$line_def_rej_qty+=$line_def_rej_qty_arr[$line_hour][$sid];
					}
					
					echo '<td width="80" align="right">'.$prod_qty_arr[$alter][$sfad_id][$line_hour].'</td>';
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
			} 

			foreach($sew_fin_spot_defect_type_new as $sfsd_id=>$sfsd_name){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $i; ?></td>
				<td ><p><? echo $sfsd_name;?></p></td>
				<?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
					echo '<td width="80" align="right">'.$prod_qty_arr[$spot][$sfsd_id][$line_hour].'</td>';
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
			}

			?>
			</table>
	        </div>
			<table class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tfoot>
	            <tr>
	                <th width="30"></th>
	                <th width="130">Total</th>
	                <?php foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
	                    echo '<th width="80" align="right">'.$prod_def_qty_arr[$line_hour].'</th>';
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
	                	foreach($prod_linehour_arr as $line_hour=>$line_hour_name)
	                	{					 
	                		$tot_rej_qty=$line_def_rej_qty_arr2[$line_hour];
	                		$total_rej_qty+=$tot_rej_qty;				 

	                		echo '<th width="80" align="right">'.$tot_rej_qty.'</th>';
	                	}

	                }
	                else
	                {
	                	$total_rej_qty=0;
	                	foreach($prod_linehour_arr as $line_hour=>$line_hour_name)
	                	{					 
	                		$tot_rej_qty=$line_def_rej_qty_arr[$line_hour];
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
					foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
							
						$tot_rej_qty=$line_def_alt_qty_arr[$line_hour];
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
					foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
							
						$tot_spot_qty=$line_def_spot_qty_arr[$line_hour];
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
					foreach($prod_linehour_arr as $line_hour=>$line_hour_name){
							
						$tot_replace_qty=$line_def_replace_qty_arr[$line_hour];
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
	            			<th width="130">Total Reject Replace</th>
	            			<?php 
	            			$total_rescan_qty=0;
	            			foreach($prod_linehour_arr as $line_hour=>$line_hour_name){

	            				$tot_rescan_qty=$line_def_rescan_qty_arr[$line_hour];
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
	        
	        
	        </div>
			
			<?

		}
	    exit();

}

?>