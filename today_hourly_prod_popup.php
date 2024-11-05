<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$lineArr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');

if($action=="today_hourly_prod_popup")
{
	echo load_html_head_contents("Popup Info","", 1, 1, $unicode,1,1);
	extract($_REQUEST);
	if($db_type==0)
	{
		$today=date('Y-m-d');
		$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
		
		 $dataArray=sql_select("select TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time, TIME_FORMAT( d.prod_start_time, '%H' ) as start_hour, TIME_FORMAT( d.prod_start_time, '%i' ) as start_min, TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id in ($manufacturing_company) and d.shift_id=1 and b.pr_date='$today' and a.id='$line_id'");
	}
	else
	{
		$today=date('d-M-Y');
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
				$sewInData=sql_select("select po_break_down_id, item_number_id, 
										min(CASE WHEN production_type=4 THEN production_date END) AS frstinput_date,
										sum(CASE WHEN production_type=4 THEN production_quantity END) as inputqty,
										sum(CASE WHEN production_type=5 THEN production_quantity END) as outqty
										from pro_garments_production_mst where status_active=1 and is_deleted=0 and sewing_line='$line_id' and production_type in(4,5) group by po_break_down_id, item_number_id");
				foreach($sewInData as $inRow)
				{
					$sewInDataArr[$inRow[csf('po_break_down_id')]][$inRow[csf('item_number_id')]]['fid']=$inRow[csf('frstinput_date')];
					$sewInDataArr[$inRow[csf('po_break_down_id')]][$inRow[csf('item_number_id')]]['qty']=$inRow[csf('inputqty')];
					$sewInDataArr[$inRow[csf('po_break_down_id')]][$inRow[csf('item_number_id')]]['outqty']=$inRow[csf('outqty')];
				}
				
                $i=1; $total_smv_achv=0; $job_no='';// and c.production_date='$today'
				$sql="select a.id, a.po_number, a.job_no_mst, b.buyer_name, b.style_ref_no, c.item_number_id, sum(c.production_quantity) as outqty from wo_po_details_master b, wo_po_break_down a, pro_garments_production_mst c where a.job_no_mst=b.job_no and c.po_break_down_id=a.id and c.company_id in($manufacturing_company) and c.sewing_line='$line_id' and c.production_date='$today' and c.production_type=5 and c.status_active=1 and c.is_deleted=0 group by a.id, a.po_number, a.job_no_mst, b.buyer_name, b.style_ref_no, c.item_number_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$inputqty=$sewInDataArr[$row[csf('id')]][$row[csf('item_number_id')]]['qty'];
					//$outqty=$row[csf('outqty')];
					$outqty=$sewInDataArr[$row[csf('id')]][$row[csf('item_number_id')]]['outqty'];
					$sewing_wip=$inputqty-$outqty;
					
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
				
				$sql_subconProd="select distinct a.party_id, a.subcon_job, b.order_no, b.smv, c.gmts_item_id, b.cust_style_ref FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_gmts_prod_dtls c WHERE a.subcon_job=b.job_no_mst and c.order_id=b.id and a.company_id in($manufacturing_company) and c.production_type=2 and c.line_id='$line_id' and c.production_date='$today' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
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
                        <th width="140">Current Working Hour</th>
                        <th width="140">Line Chief</th>
                        <th>Eff. %</th>
                    </tr>
				</thead>
                <?
                $i=1; $mast_dtl_id='';
				$start_hour=$dataArray[0][csf('start_hour')]; $start_min=$dataArray[0][csf('start_min')]; 
				$curr_hour=date("H"); $curr_min=date("i");
				$start_time=date("Y-m-d ").$start_hour.":00"; $launch_start_time=date("Y-m-d ").$dataArray[0][csf('lunch_start_time')]; $curr_time=date("Y-m-d H:00");
				$sql="select b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, (b.working_hour*man_power*60) as tsmv, b.working_hour, b.mast_dtl_id, b.smv_adjust, b.smv_adjust_type from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id in($manufacturing_company) and a.id='$line_id' and pr_date='$today' order by a.location_id, a.floor_id, a.line_number";
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
                            <img src="<? echo $inf[csf("image_location")]; ?>" height="97" width="89" />
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






?>