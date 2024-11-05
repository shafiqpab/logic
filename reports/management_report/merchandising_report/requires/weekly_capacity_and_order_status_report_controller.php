<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$date=date('Y-m-d');
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );  
	exit();  	 
}

if ($action=="lc_load_drop_down_location")
{
	echo create_drop_down( "cbo_lc_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );  
	exit();  	 
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$lc_company_id=str_replace("'","",$cbo_lc_company_id);
	$lc_location_id=str_replace("'","",$cbo_lc_location_id);
	$cbo_date_cat_id=str_replace("'","",$cbo_date_cat_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	
	$companyArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
	$locationArr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	
	//$season_name_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	//$team_leader_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
	//$country_name_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	
	$dateFormat="d-M-Y";
	
	if($db_type==0)
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd','-');
		$date_to=change_date_format($date_to,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$date_to=change_date_format($date_to,'yyyy-mm-dd','-',1);
	}


	//Week..........................................................
	$weekArr=array();$dateWiseWeekArr=array();
	$week_sql="select WEEK_DATE,WEEK from week_of_year where week_date between '$date_from' and  '$date_to'  order by WEEK,WEEK_DATE";
	$week_sql_result=sql_select($week_sql);
	foreach ($week_sql_result as $rows)
	{
		$week_date=date($dateFormat,strtotime($rows[WEEK_DATE]));
		$weekArr[$rows[WEEK]]=$rows[WEEK];
		$dateWiseWeekArr[$week_date]=$rows[WEEK];
		$weeklyDateArr[$rows[WEEK]][$week_date]=$week_date;
	}
	unset($week_sql_result);	
	
	//print_r($weeklyDateArr[50]);
	
	
	//Capacity..............................................................
	if($location_id){$wc_locatin_cond=" and a.location_id in($location_id)";}else{$wc_locatin_cond="";}
	if($company_id){$company_con=" and a.comapny_id in($company_id)";}
	else{$company_con="";}

	$capacity_sql="SELECT a.COMAPNY_ID,a.LOCATION_ID,b.CAPACITY_MIN,b.CAPACITY_PCS,b.DATE_CALC from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id $company_con $wc_locatin_cond and b.date_calc between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 group by a.comapny_id,a.location_id,b.capacity_min,b.capacity_pcs,b.date_calc";
	 //echo $capacity_sql;die;
	$capacity_sql_result=sql_select($capacity_sql);
	$capacity_arr=array();
	foreach( $capacity_sql_result as $row)
	{
		$capacity_date=date($dateFormat,strtotime($row[DATE_CALC]));
		$week=$dateWiseWeekArr[$capacity_date];
		$key=$row[COMAPNY_ID].'_'.$row[LOCATION_ID];
		$capacity_arr['CAPACITY_MIN'][$key][$week]+=$row[csf("CAPACITY_MIN")];
		$capacity_arr['CAPACITY_PCS'][$key][$week]+=$row[csf("CAPACITY_PCS")];
	}
	unset($capacity_sql_result);
	 
	 // print_r($capacity_arr['CAPACITY_MIN']);die;
	 //Order data.......................................................................
	 
	if($location_id){$wc_locatin_cond=" and a.working_location_id in($location_id)";}else{$wc_locatin_cond="";}
	if($lc_location_id){$lc_locatin_cond=" and a.location_name in($lc_location_id)";}else{$lc_locatin_cond="";}
	
	if($lc_company_id){$lc_company_con=" and a.company_name in($lc_company_id)";}else{$lc_company_con="";}
	if($company_id){$wc_company_con=" and a.style_owner in($company_id)";}else{$wc_company_con="";}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	
	if($cbo_date_cat_id==1)//Pub Ship Date
	{
		$order_sql="SELECT a.COMPANY_NAME,a.WORKING_LOCATION_ID,a.STYLE_OWNER,a.BUYER_NAME,c.SMV_PCS,c.SET_ITEM_RATIO,b.IS_CONFIRMED,B.UNIT_PRICE,b.pub_shipment_date as SHIP_DATE,
		
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as CONFIRM_QTY,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as PROJECTED_QTY,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as CONFIRM_VALUE,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as PROJECTED_VALUE
		FROM wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS c
		WHERE a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND b.pub_shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.COMPANY_NAME,a.WORKING_LOCATION_ID,a.STYLE_OWNER,a.BUYER_NAME
		";
		//echo $order_sql;die;
	}
	else if($cbo_date_cat_id==3)//Actual Ship Date
	{
		$order_sql="SELECT a.COMPANY_NAME,a.WORKING_LOCATION_ID,a.STYLE_OWNER,a.BUYER_NAME,c.SMV_PCS,c.SET_ITEM_RATIO,b.IS_CONFIRMED,B.UNIT_PRICE,b.pub_shipment_date as SHIP_DATE,
		
		(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as CONFIRM_QTY,
		(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as PROJECTED_QTY,
		(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as CONFIRM_VALUE,
		(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as PROJECTED_VALUE
		FROM wo_po_details_master a, wo_po_break_down b,WO_PO_DETAILS_MAS_SET_DETAILS c
		WHERE a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id $lc_company_con $wc_company_con $lc_locatin_cond $wc_locatin_cond AND b.shipment_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.COMPANY_NAME,a.WORKING_LOCATION_ID,a.STYLE_OWNER,a.BUYER_NAME";
	}
	else //Country Ship Date
	{
		$order_sql="SELECT a.COMPANY_NAME,a.WORKING_LOCATION_ID,a.STYLE_OWNER,a.BUYER_NAME,d.SMV_PCS,d.SET_ITEM_RATIO,
		
		
		(CASE WHEN b.is_confirmed=1 THEN c.order_quantity ELSE 0 END) as CONFIRM_QTY,
		(CASE WHEN b.is_confirmed=2 THEN c.order_quantity ELSE 0 END) as PROJECTED_QTY,
		(CASE WHEN b.is_confirmed=1 THEN c.order_total ELSE 0 END) as CONFIRM_VALUE,
		(CASE WHEN b.is_confirmed=2 THEN c.order_total ELSE 0 END) as PROJECTED_VALUE
		FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,WO_PO_DETAILS_MAS_SET_DETAILS d
		WHERE a.id=b.job_id and a.id=c.job_id and a.job_id=d.job_id and b.job_id=d.job_id and c.job_id=d.job_id and b.id=c.po_break_down_id $lc_company_con $wc_company_con  $lc_locatin_cond $wc_locatin_cond AND c.country_ship_date between '$date_from' and '$date_to' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	
	$order_sql_result=sql_select($order_sql);
	$data_arr=array();$rowspanArr=array();
	foreach( $order_sql_result as $rows)
	{
		$key=$rows[STYLE_OWNER].'_'.$rows[WORKING_LOCATION_ID];
		$rowspanArr[$key][$rows[COMPANY_NAME].'_'.$rows[BUYER_NAME]]=1;
		$data_arr[$key][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]=array(BUYER_NAME=>$rows[BUYER_NAME]);
		
		
		$dataArr[$key][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]['CONFIRM_QTY_PCS']+=($rows[CONFIRM_QTY]*$rows[SET_ITEM_RATIO]);
		$dataArr[$key][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]['PROJECTED_QTY_PCS']+=($rows[PROJECTED_QTY]*$rows[SET_ITEM_RATIO]);
		
		$dataArr[$key][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]['CONFIRM_VAL']+=($rows[CONFIRM_QTY]*$rows[UNIT_PRICE]);
		$dataArr[$key][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]['PROJECTED_VAL']+=($rows[PROJECTED_QTY]*$rows[UNIT_PRICE]);
		
	
		$dataArr[$key][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]['CONFIRM_MINT']+=($rows[CONFIRM_QTY]*$rows[SET_ITEM_RATIO]*$rows[SMV_PCS]);
		$dataArr[$key][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]['PROJECTED_MINT']+=($rows[PROJECTED_QTY]*$rows[SET_ITEM_RATIO]*$rows[SMV_PCS]);
	
	
		//Week..........................
		$ship_date=date($dateFormat,strtotime($rows[SHIP_DATE]));
		$week=$dateWiseWeekArr[$ship_date];
		$capacity_arr['WEEK_QTY_PCS'][$key][$week]+=($rows[CONFIRM_QTY]+$rows[PROJECTED_QTY])*$rows[SET_ITEM_RATIO];
		$capacity_arr['WEEK_MINT'][$key][$week]+=(($rows[CONFIRM_QTY]+$rows[PROJECTED_QTY])*$rows[SET_ITEM_RATIO])*$rows[SMV_PCS];
		
		$capacity_arr['WEEK_CONF_QTY_PCS'][$key][$rows[BUYER_NAME]][$week]+=$rows[CONFIRM_QTY];
		$capacity_arr['WEEK_PROJ_QTY_PCS'][$key][$rows[BUYER_NAME]][$week]+=$rows[PROJECTED_QTY];
		
		$capacity_arr['WEEK_CONF_VAL'][$key][$rows[BUYER_NAME]][$week]+=($rows[CONFIRM_QTY]*$rows[UNIT_PRICE]);
		$capacity_arr['WEEK_PROJ_VAL'][$key][$rows[BUYER_NAME]][$week]+=($rows[PROJECTED_QTY]*$rows[UNIT_PRICE]);
		
		
		$capacity_arr['WEEK_CONF_MINT'][$key][$rows[BUYER_NAME]][$week]+=($rows[CONFIRM_QTY]*$rows[SET_ITEM_RATIO]*$rows[SMV_PCS]);
		$capacity_arr['WEEK_PROJ_MINT'][$key][$rows[BUYER_NAME]][$week]+=($rows[PROJECTED_QTY]*$rows[SET_ITEM_RATIO]*$rows[SMV_PCS]);
		


	}
	unset($order_sql_result);
	 
	//echo "<pre>";
	//print_r($capacity_arr[CAPACITY_MIN] );
	
	
	// var_dump($data_arr['3_0']);die;
	 
	 $width=(80*17*count($weekArr))+800;
	
?>

    <div style="margin:0 auto; width:<?= $width+20;?>px;">
        <table width="<?= $width;?>" border="1" rules="all" class="rpt_table" align="left">
            <thead>
            <tr>
                <th rowspan="3" width="35">SL</th>
                <th rowspan="3" width="70">Working Company</th>
                <th rowspan="3" width="100">Location</th>
                <th rowspan="3" width="60">100% Capacity (Mint)</th>
                <th rowspan="3" width="60">100% Capacity (Pcs)</th>
                <th rowspan="3" width="60">LC Company</th>
                <th rowspan="3" width="100">Buyer</th>
                <? foreach($weekArr as $week_no){?>
                <th colspan="17">Week- <?= $week_no;?>  (<?= current($weeklyDateArr[$week_no]);?> To <?= end($weeklyDateArr[$week_no]);?>)
                
                <? //print_r($weeklyDateArr[$week_no]);?>
                </th>
                <? } ?>
                <th rowspan="2" colspan="2">Total Balance</th>
            </tr>
            <tr>
            	<? foreach($weekArr as $week_no){?>
                <th colspan="10">Quantity Details (Pcs)</th>
            	<th colspan="7">Minute Details (SMV)</th>
                <? } ?>
            </tr>
            <tr>
            	<? foreach($weekArr as $week_no){?>
                <th width="80">Avg. SMV (Projected)</th>
                <th width="80">Proj. Avg.Unit Price</th>
                <th width="80">Projected Qty. Pcs</th>
                <th width="80">Projected Value $</th>
                <th width="80">Avg. SMV (Conf.)</th>
                <th width="80">Conf. Avg.Unit Price</th>
                <th width="80">Confirm Qty. Pcs</th>
                <th width="80">Confirm Value $</th>
                <th width="80">Total Proj. and Conf. Qty. (Pcs)</th>
                <th width="80">Total Proj. and Conf. Value</th> 
                
                <th width="80">Week Capacity [Mint.]</th>
                <th width="80">Week Capacity [Pcs]</th>
                <th width="80">Projected Mint.</th>
                <th width="80">Confirm Mint.</th>
                <th width="80">Total Proj. and Conf. Mint.</th>
                <th width="80">Week Qty. Balance</th>
                <th width="80">Week Mint. Balance</th>
                <? } ?>
                <th width="100">Total Balance [Pcs]</th>
                <th>Total Balance [Mint.]</th>
                
            </tr>
            </thead>
    	<!-- </table><div style="width:<?= $width+20;?>px; max-height:350px; overflow-y:scroll" id="scroll_body">
        <table width="<?= $width;?>" border="1" rules="all" class="rpt_table" id="scroll_body" align="left"> -->
            <tbody>
            <? 
			
			$i=1;
			//$total_proj_qty_pcs=0;$total_conf_qty_pcs=0;$total_proj_val=0;$total_conf_val=0;
			//$total_proj_mint=0;$total_conf_mint=0;$total_week_qty_balance_pcs=0;$total_week_mint_balance=0;
			$grand_total_balance_pcs=0;$grand_total_balance_mint=0;
			
			
			foreach($data_arr as $working_com_working_location_str=>$comapnyLocationArr){
				list($working_com_id,$working_location_id)=explode('_',$working_com_working_location_str);
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$rowspan=count($rowspanArr[$working_com_working_location_str]);
			
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td valign="middle" rowspan="<?= $rowspan;?>" width="35" align="center"><?= $i;?></td>
                <td valign="middle" align="center" rowspan="<?= $rowspan;?>" width="70"><?= $companyArr[$working_com_id];?></td>
                <td valign="middle" rowspan="<?= $rowspan;?>" width="100"><?= $locationArr[$working_location_id];?></td>
                <td valign="middle" rowspan="<?= $rowspan;?>" width="60" align="right"><?= array_sum($capacity_arr['CAPACITY_MIN'][$working_com_working_location_str]);?></td>
                <td valign="middle" rowspan="<?= $rowspan;?>" width="60" align="right"><?= array_sum($capacity_arr['CAPACITY_PCS'][$working_com_working_location_str]);?></td>
                
                <? 
				$ii=1;
				//$location_proj_qty_pcs=0;$location_conf_qty_pcs=0;$location_proj_val=0;$location_conf_val=0;
				//$location_proj_mint=0;$location_conf_mint=0;$location_week_qty_balance_pcs=0;$location_week_mint_balance=0;
				$location_total_balance_pcs=0;$location_total_balance_mint=0;
				
				
				$week_location_proj_qty_pcs_arr=array();
				$week_location_conf_qty_pcs_arr=array();
				$week_location_proj_val_arr=array();
				$week_location_conf_val_arr=array();
				$week_location_proj_mint_arr=array();
				$week_location_conf_mint_arr=array();
				
				
				foreach($comapnyLocationArr as $lc_company_id=>$lcCompanyArr){
					$rowspan_1=count($lcCompanyArr);
					$fn_onclick="change_color('tr_".$i.$ii."','".$bgcolor."')";
					if($ii!=1){echo "<tr id='tr_".$i.$ii."' onclick=$fn_onclick >";}
				?>
                <td valign="middle" rowspan="<?= $rowspan_1;?>" width="60" align="center"><?= $companyArr[$lc_company_id];?></td>
                <? 
				$iii=1;
				foreach($lcCompanyArr as $buyer_id=>$rows){
					$fn_onclick2="change_color('tr_".$i.$ii.$iii."','".$bgcolor."')";
					if($iii!=1){echo "<tr id='tr_".$i.$ii.$iii."' onclick=$fn_onclick2 >";}
				
				
				$PROJECTED_QTY_PCS=$dataArr[$working_com_working_location_str][$lc_company_id][$buyer_id]['PROJECTED_QTY_PCS'];
				$CONFIRM_QTY_PCS=$dataArr[$working_com_working_location_str][$lc_company_id][$buyer_id]['CONFIRM_QTY_PCS'];
				$PROJECTED_VAL=$dataArr[$working_com_working_location_str][$lc_company_id][$buyer_id]['PROJECTED_VAL'];
				$CONFIRM_VAL=$dataArr[$working_com_working_location_str][$lc_company_id][$buyer_id]['CONFIRM_VAL'];

				$PROJECTED_MINT=$dataArr[$working_com_working_location_str][$lc_company_id][$buyer_id]['PROJECTED_MINT'];
				$CONFIRM_MINT=$dataArr[$working_com_working_location_str][$lc_company_id][$buyer_id]['CONFIRM_MINT'];


				
				$proj_avg_unite_price=($PROJECTED_VAL/$PROJECTED_QTY_PCS);
				$conf_avg_unite_price=($CONFIRM_VAL/$CONFIRM_QTY_PCS);
				
				$total_conf_proj_qty_pcs=$CONFIRM_QTY_PCS+$PROJECTED_QTY_PCS;
				$total_conf_proj_val=$CONFIRM_VAL+$PROJECTED_VAL;
				
				$total_conf_proj_mint=$PROJECTED_MINT+$CONFIRM_MINT;
				
				
				
				$proj_avg_smv=($PROJECTED_MINT/$PROJECTED_QTY_PCS);
				$conf_avg_smv=($CONFIRM_MINT/$CONFIRM_QTY_PCS);
				
				
				
				
				?>
                
                <td width="100"><?= $buyerArr[$buyer_id];?></td>
                <? foreach($weekArr as $week_no){
					$WEEK_CAPACITY_MIN=$capacity_arr['CAPACITY_MIN'][$working_com_working_location_str][$week_no];
					$WEEK_CAPACITY_PCS=$capacity_arr['CAPACITY_PCS'][$working_com_working_location_str][$week_no];
					
					$WEEK_QTY_PCS=$capacity_arr['WEEK_QTY_PCS'][$working_com_working_location_str][$week_no];
					$WEEK_MINT=$capacity_arr['WEEK_MINT'][$working_com_working_location_str][$week_no];
						
						
					$WEEK_CONF_QTY_PCS=$capacity_arr['WEEK_CONF_QTY_PCS'][$working_com_working_location_str][$buyer_id][$week_no];
					$WEEK_PROJ_QTY_PCS=$capacity_arr['WEEK_PROJ_QTY_PCS'][$working_com_working_location_str][$buyer_id][$week_no];
					
					$WEEK_CONF_VAL=$capacity_arr['WEEK_CONF_VAL'][$working_com_working_location_str][$buyer_id][$week_no];
					$WEEK_PROJ_VAL=$capacity_arr['WEEK_PROJ_VAL'][$working_com_working_location_str][$buyer_id][$week_no];
					
					
					$WEEK_CONF_MINT=$capacity_arr['WEEK_CONF_MINT'][$working_com_working_location_str][$buyer_id][$week_no];
					$WEEK_PROJ_MINT=$capacity_arr['WEEK_PROJ_MINT'][$working_com_working_location_str][$buyer_id][$week_no];
						
					$week_proj_avg_smv=($WEEK_PROJ_MINT/$WEEK_PROJ_QTY_PCS);
					$week_conf_avg_smv=($WEEK_CONF_MINT/$WEEK_CONF_QTY_PCS);

					$week_proj_avg_unite_price=($WEEK_PROJ_VAL/$WEEK_PROJ_QTY_PCS);
					$week_conf_avg_unite_price=($WEEK_CONF_VAL/$WEEK_CONF_QTY_PCS);


					$week_total_conf_proj_qty_pcs=$WEEK_CONF_QTY_PCS+$WEEK_PROJ_QTY_PCS;
					$week_total_conf_proj_val=$WEEK_CONF_VAL+$WEEK_PROJ_VAL;
					
					$week_total_conf_proj_mint=$WEEK_CONF_MINT+$WEEK_PROJ_MINT;
					
					//week locatin total cal...............
					$week_location_proj_qty_pcs_arr[$week_no]+=$WEEK_PROJ_QTY_PCS;
					$week_location_conf_qty_pcs_arr[$week_no]+=$WEEK_CONF_QTY_PCS;
					$week_location_proj_val_arr[$week_no]+=$WEEK_PROJ_VAL;
					$week_location_conf_val_arr[$week_no]+=$WEEK_CONF_VAL;
					$week_location_proj_mint_arr[$week_no]+=$WEEK_PROJ_MINT;
					$week_location_conf_mint_arr[$week_no]+=$WEEK_CONF_MINT;
					
					$week_total_proj_qty_pcs_arr[$week_no]+=$WEEK_PROJ_QTY_PCS;
					$week_total_conf_qty_pcs_arr[$week_no]+=$WEEK_CONF_QTY_PCS;
					$week_total_proj_val_arr[$week_no]+=$WEEK_PROJ_VAL;
					$week_total_conf_val_arr[$week_no]+=$WEEK_CONF_VAL;
					$week_total_proj_mint_arr[$week_no]+=$WEEK_PROJ_MINT;
					$week_total_conf_mint_arr[$week_no]+=$WEEK_CONF_MINT;
					
					?>
                    <td width="80" align="center"><?= fn_number_format($week_proj_avg_smv,2);?></td>
                    <td width="80" align="center"><?= fn_number_format($week_proj_avg_unite_price,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($WEEK_PROJ_QTY_PCS,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($WEEK_PROJ_VAL,2);?></td>
                    <td width="80" align="center"><?= fn_number_format($week_conf_avg_smv,2);?></td>
                    <td width="80" align="center"><?= fn_number_format($week_conf_avg_unite_price,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($WEEK_CONF_QTY_PCS,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($WEEK_CONF_VAL,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($week_total_conf_proj_qty_pcs,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($week_total_conf_proj_val,2);?></td> 
                    
                    <? if($ii==1 && $iii==1){ ?>
                    <td rowspan="<?= $rowspan;?>"  align="right" valign="middle" width="80"><?= fn_number_format($WEEK_CAPACITY_MIN,2);?></td>
                    <td rowspan="<?= $rowspan;?>"  align="right" valign="middle" width="80"><?= fn_number_format($WEEK_CAPACITY_PCS,2);?></td>
                    <? } ?>
                    
                    <td width="80" align="right"><?= fn_number_format($WEEK_PROJ_MINT,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($WEEK_CONF_MINT,2);?></td>
                    <td width="80" align="right"><?= fn_number_format($week_total_conf_proj_mint,2);?></td>
                    <? if($ii==1 && $iii==1){ 
						$location_week_qty_balance_pcs=($WEEK_CAPACITY_PCS-$WEEK_QTY_PCS);
						$location_week_mint_balance=($WEEK_CAPACITY_MIN-$WEEK_MINT);
						
						$total_week_qty_balance_pcs+=($WEEK_CAPACITY_PCS-$WEEK_QTY_PCS);
						$total_week_mint_balance+=($WEEK_CAPACITY_MIN-$WEEK_MINT);
						
					?>
                    <td rowspan="<?= $rowspan;?>" align="right" valign="middle" width="80"><?= fn_number_format($WEEK_CAPACITY_PCS-$WEEK_QTY_PCS,2);?></td>
                    <td rowspan="<?= $rowspan;?>" align="right" valign="middle" width="80"><?= fn_number_format($WEEK_CAPACITY_MIN-$WEEK_MINT,2);?></td>
                    <? } ?>
                    
                	<? }//week foreach;  
				if($ii==1 && $iii==1){
					$location_total_balance_pcs=array_sum($capacity_arr['CAPACITY_PCS'][$working_com_working_location_str])-array_sum($capacity_arr['WEEK_QTY_PCS'][$working_com_working_location_str]);
					$location_total_balance_mint=array_sum($capacity_arr['CAPACITY_MIN'][$working_com_working_location_str])-array_sum($capacity_arr['WEEK_MINT'][$working_com_working_location_str]);
					
					$grand_total_balance_pcs+=$location_total_balance_pcs;
					$grand_total_balance_mint+=$location_total_balance_mint;
				?>
                <td align="right" valign="middle" rowspan="<?= $rowspan;?>" width="100"><? echo array_sum($capacity_arr['CAPACITY_PCS'][$working_com_working_location_str])-array_sum($capacity_arr['WEEK_QTY_PCS'][$working_com_working_location_str]);?></td>
                <td align="right" valign="middle" rowspan="<?= $rowspan;?>"><? echo array_sum($capacity_arr['CAPACITY_MIN'][$working_com_working_location_str])-array_sum($capacity_arr['WEEK_MINT'][$working_com_working_location_str]);?></td>
            </tr>
            <?
				  }else{echo "</tr>";}
				$iii++;
				}//3rd;
			  $ii++;
			 }//2nd;
			?>
			<tr bgcolor="#999999">
                <th colspan="7">Location Total: (<?= $locationArr[$working_location_id];?>)</th>
                <? foreach($weekArr as $week_no){
					$LOCATION_WEEK_CAPACITY_MIN=$capacity_arr['CAPACITY_MIN'][$working_com_working_location_str][$week_no];
					$LOCATION_WEEK_CAPACITY_PCS=$capacity_arr['CAPACITY_PCS'][$working_com_working_location_str][$week_no];
					
					$week_total_capacity_mint_arr[$week_no]+=$LOCATION_WEEK_CAPACITY_MIN;
					$week_total_capacity_pcs_arr[$week_no]+=$LOCATION_WEEK_CAPACITY_PCS;
	
				?>
                <th></th>
                <th></th>
                <th align="right"><?= number_format($week_location_proj_qty_pcs_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_proj_val_arr[$week_no],2);?></th>
                <th></th>
                <th></th>
                <th align="right"><?= number_format($week_location_conf_qty_pcs_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_conf_val_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_proj_qty_pcs_arr[$week_no]+$week_location_conf_qty_pcs_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_proj_val_arr[$week_no]+$week_location_conf_val_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($LOCATION_WEEK_CAPACITY_MIN,2);?></th>
                <th align="right"><?= number_format($LOCATION_WEEK_CAPACITY_PCS,2);?></th>
                <th align="right"><?= number_format($week_location_proj_mint_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_conf_mint_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_proj_mint_arr[$week_no]+$week_location_conf_mint_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_week_qty_balance_pcs_arr[$week_no],2);?></th>
                <th align="right"><?= number_format($week_location_week_mint_balance_arr[$week_no],2);?></th>
                <? } ?>
                <th align="right"><?= number_format($location_total_balance_pcs,2);?></th>
                <th align="right"><?= number_format($location_total_balance_mint,2);?></th>
			</tr>
			<?
			$i++;
			}//1st;
			
			 ?>
            </tbody><!-- 
    	</table>
        </div>
        <table width="<?= $width;?>" border="1" rules="all" class="rpt_table" align="left">
            <tfoot> -->
            <tr>
                <th width="35"></th>
                <th width="70"></th>
                <th width="100"></th>
                <th width="60"></th>
                <th width="60"></th>
                <th width="60"></th>
                <th width="100">Grand Total:</th>
                <? foreach($weekArr as $week_no){?>
                <th width="80"></th>
                <th width="80"></th>
                <th width="80" align="right"><?= number_format($week_total_proj_qty_pcs_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_proj_val_arr[$week_no],2);?></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="80" align="right"><?= number_format($week_total_conf_qty_pcs_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_conf_val_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_proj_qty_pcs_arr[$week_no]+$week_total_conf_qty_pcs_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_proj_val_arr[$week_no]+$week_total_conf_val_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_capacity_mint_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_capacity_pcs_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_proj_mint_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_conf_mint_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_proj_mint_arr[$week_no]+$week_total_conf_mint_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_week_qty_balance_pcs_arr[$week_no],2);?></th>
                <th width="80" align="right"><?= number_format($week_total_week_mint_balance_arr[$week_no],2);?></th>
                <? } ?>
                <th width="100" align="right"><?= number_format($grand_total_balance_pcs,2);?></th>
                <th align="right"><?= number_format($grand_total_balance_mint,2);?></th>
            </tr>
            </tfoot>
    	</table>
        
     </div>

 
    

<?	
	
die;	
	
	
	
	
	
	
	
		$user_id=$_SESSION['logic_erp']['user_id'];
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
		echo "$html****$filename****$report_type"; 
	exit();	
}


?>