 <?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_buyer_id", 130, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (SELECT buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");  
	exit();
}

if ($action=="load_drop_down_location")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_location_id", 130, "SELECT id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/table_wise_hourly_iron_production_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id in($data) and PRODUCTION_PROCESS=10 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down('requires/table_wise_hourly_iron_production_controller', document.getElementById('cbo_working_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+this.value, 'load_drop_down_table', 'table_td' );",0 );     	 	
	exit();    	 
}

if ($action=='load_drop_down_table')
{
	list($company_id,$location_id,$floor_id)=explode("_",$data);
	echo create_drop_down( 'cbo_table_no', 100, "select id, table_name from lib_table_entry where table_type=2 and company_name=$company_id and location_name=$location_id and floor_name=$floor_id and is_deleted=0 and status_active=1 order by table_name", 'id,table_name', 1, '-- Select --', $selected, '', 0 );
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'", "",$cbo_company_id);
	$cbo_working_company_id=str_replace("'", "",$cbo_working_company_id);
	$txt_pro_date=str_replace("'", "",$txt_pro_date);
	$cbo_location_id=str_replace("'", "",$cbo_location_id);
	$cbo_floor_id=str_replace("'", "",$cbo_floor_id);
	$cbo_table_no=str_replace("'", "",$cbo_table_no);
	$cbo_buyer_id=str_replace("'", "",$cbo_buyer_id);
	$txt_style_no=str_replace("'", "",$txt_style_no);
	$txt_start_hour=str_replace("'", "",$txt_start_hour);

	if($db_type==0)
	{
		$txt_pro_date=change_date_format($txt_pro_date,"yyyy-mm-dd", "-");
	}
	else
	{
		$txt_pro_date=change_date_format($txt_pro_date,'','',1);	
	}
	
	if($cbo_company_id!=""){$where_con .=" and a.company_id in(".$cbo_company_id.")";}
	if($cbo_working_company_id!=0){ $where_con .=" and a.serving_company=".$cbo_working_company_id."";}
	if($cbo_location_id!=0){ $where_con .=" and a.location =".$cbo_location_id."";}
	if($cbo_floor_id!=0){ $where_con .=" and a.floor_id =".$cbo_floor_id."";}
	if($cbo_buyer_id!=0){$where_con .=" and b.buyer_name =".$cbo_buyer_id."";}
	if($txt_style_no!=""){ $where_con .=" and b.style_ref_no ='".$txt_style_no."'";}
	if($cbo_table_no!=0){ $where_con .=" and a.table_no =".$cbo_table_no."";}
	if($txt_pro_date!=""){ $where_con.=" and a.production_date = '$txt_pro_date'";}
 
 
	$companyArr = return_library_array("SELECT id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("SELECT id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("SELECT id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("SELECT id,floor_name from lib_prod_floor","id","floor_name"); 
	$tableNameArr = return_library_array("select id, table_name from lib_table_entry where table_type=2  and is_deleted=0 and status_active=1","id","table_name"); 
 
 
	$prod_resource_array=array();
	$dataArray=sql_select("select a.id, a.LOCATION_ID, a.FLOOR_ID,b.TARGET_PER_HOUR,a.COMPANY_ID from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id  and b.pr_date = '$txt_pro_date' and  a.COMPANY_ID=$cbo_working_company_id");
	foreach($dataArray as $row)
	{
		$prod_resource_array['target_per_hour'][$row[LOCATION_ID].$row[FLOOR_ID]]+=$row[TARGET_PER_HOUR];
	}
	


	//Production Arr set................................start;
	
	$prod_start_hour=($txt_start_hour=='')?"09:00":$txt_start_hour;
	$start_time=explode(":",$prod_start_hour);
	$hour=$start_time[0]*1; 
	$minutes=$start_time[1]; 
	$last_hour=23;
	
	$prod_arr=array(); $start_hour_arr=array();
	$start_hour=$prod_start_hour;
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour; $j<$last_hour; $j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	$start_hour_arr[$j+1]='23:59';
	
	
	//Production Arr set................................end;
	
	if($db_type==0)
	{
		$production_hour="TIME_FORMAT( production_hour, '%H:%i' ) ";
		
		$sql_query="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.table_no, a.po_break_down_id,b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv,b.job_no, e.item_number_id, c.po_number,
		group_concat(distinct(a.supervisor)) as supervisor,a.re_production_qty as re_prod_qty,
		
			sum(d.production_qnty) as good_qnty";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="good_".substr((add_time($start_hour_arr[$h],60)),0,2);
				$reject_hour="reject_".substr((add_time($start_hour_arr[$h],60)),0,2);
				$re_pro_hour="re_pro_".substr((add_time($start_hour_arr[$h],60)),0,2);
				if($first==1)
				{
					$sql_query.=", sum(CASE WHEN $production_hour<='$end' and d.production_type=7 THEN d.production_qnty else 0 END) AS $prod_hour , sum(CASE WHEN $production_hour<='$end' and d.production_type=7 THEN d.REJECT_QTY else 0 END) AS $reject_hour , (CASE WHEN $production_hour<='$end' THEN a.re_production_qty else 0 END) AS $re_pro_hour
					";
				}
				else
				{
					$sql_query.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and d.production_type=7 THEN d.production_qnty else 0 END) AS $prod_hour , sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and d.production_type=7 THEN d.REJECT_QTY else 0 END) AS $reject_hour , (CASE WHEN $production_hour>'$bg' and $production_hour<='$end' THEN a.re_production_qty else 0 END) AS $re_pro_hour";
				}
				$first=$first+1;
			}
			$sql_query.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=7 THEN d.production_qnty else 0 END) AS good_24, sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=7 THEN d.REJECT_QTY else 0 END) AS reject_24, (CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' THEN d.re_production_qty else 0 END) AS re_pro_24 from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
			where a.production_type=7 and d.production_type=7 and a.id=d.mst_id and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and e.id=d.color_size_break_down_id and e.po_break_down_id=c.id and a.po_break_down_id=e.po_break_down_id $where_con
			group by a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.table_no, a.po_break_down_id,b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv,b.job_no, e.item_number_id, c.po_number,a.re_production_qty,production_hour order by a.location, a.floor_id,a.table_no, a.po_break_down_id";
			
			//$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
	
	}
	else if($db_type==2)
	{
		
		$production_hour="TO_CHAR(production_hour,'HH24:MI')";
		
		$sql_query="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.table_no, a.po_break_down_id,b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv,b.job_no, e.item_number_id, c.po_number,
		listagg((case when a.supervisor!='' then a.supervisor end ),',') within group (order by a.supervisor) as supervisor,a.re_production_qty as re_prod_qty,
		
			sum(d.production_qnty) as good_qnty";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="good_".substr((add_time($start_hour_arr[$h],60)),0,2);
				$reject_hour="reject_".substr((add_time($start_hour_arr[$h],60)),0,2);
				$re_pro_hour="re_pro_".substr((add_time($start_hour_arr[$h],60)),0,2);
				if($first==1)
				{
					$sql_query.=", sum(CASE WHEN $production_hour<='$end' and d.production_type=7 THEN d.production_qnty else 0 END) AS $prod_hour , sum(CASE WHEN $production_hour<='$end' and d.production_type=7 THEN d.REJECT_QTY else 0 END) AS $reject_hour , (CASE WHEN $production_hour<='$end' THEN a.re_production_qty else 0 END) AS $re_pro_hour
					";
				}
				else
				{
					$sql_query.=", sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and d.production_type=7 THEN d.production_qnty else 0 END) AS $prod_hour , sum(CASE WHEN $production_hour>'$bg' and $production_hour<='$end' and d.production_type=7 THEN d.REJECT_QTY else 0 END) AS $reject_hour , (CASE WHEN $production_hour>'$bg' and $production_hour<='$end' THEN a.re_production_qty else 0 END) AS $re_pro_hour";
				}
				$first=$first+1;
			}
			$sql_query.=", sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=7 THEN d.production_qnty else 0 END) AS good_24, sum(CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' and d.production_type=7 THEN d.REJECT_QTY else 0 END) AS reject_24, (CASE WHEN $production_hour>'$start_hour_arr[$last_hour]' and $production_hour<='$start_hour_arr[24]' THEN a.re_production_qty else 0 END) AS re_pro_24 from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
			where a.production_type=7 and d.production_type=7 and a.id=d.mst_id and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and e.id=d.color_size_break_down_id and e.po_break_down_id=c.id and a.po_break_down_id=e.po_break_down_id $where_con
			group by a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.table_no, a.po_break_down_id,b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv,b.job_no, e.item_number_id, c.po_number,a.re_production_qty,production_hour order by a.location, a.floor_id,a.table_no, a.po_break_down_id";
			
			//$production_hour_subcon="TO_CHAR(hour,'HH24:MI')";
	}
	// echo $sql_query;
	$dataArr=array();$iron_data_arr=array();$re_iron_data_arr=array();$rej_data_arr=array();
	$sql_query_result=sql_select($sql_query);
	foreach($sql_query_result as $rows)
	{		
		// $key=$rows[csf('company_id')].'**'.$rows[csf('location')].'**'.$rows[csf('floor_id')].'**'.$rows[csf('table_no')].'**'.$rows[csf('buyer_name')].'**'.$rows[csf('job_no')].'**'.$rows[csf('style_ref_no')].'**'.$rows[csf('item_number_id')].'**'.$rows[csf('po_number')].'**'.$rows[csf('po_break_down_id')];

		$key=$rows[csf('table_no')];

		for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="good_".substr((add_time($start_hour_arr[$h],60)),0,2);
			$re_pro_hour="re_pro_".substr((add_time($start_hour_arr[$h],60)),0,2);
			$reject_hour="reject_".substr((add_time($start_hour_arr[$h],60)),0,2);

			$iron_data_arr[$key][$h+1]+=$rows[csf($prod_hour)];
			$re_iron_data_arr[$key][$h+1]+=$rows[csf($re_pro_hour)];
			$rej_data_arr[$key][$h+1]+=$rows[csf($reject_hour)];
			
			//grand total.......
			$iron_data_arr['grand_total'][$h+1]+=$rows[csf($prod_hour)];
			$re_iron_data_arr['grand_total'][$h+1]+=$rows[csf($re_pro_hour)];
			$rej_data_arr['grand_total'][$h+1]+=$rows[csf($reject_hour)];
		}
		
		$prod_hour="good_24";
		$re_pro_hour="re_pro_24";
		$reject_hour="reject_24";
		
		$iron_data_arr[$key][24]+=$rows[csf($prod_hour)];
		$re_iron_data_arr[$key][24]+=$rows[csf($re_pro_hour)];
		$rej_data_arr[$key][24]+=$rows[csf($reject_hour)];
		
		$iron_data_arr['grand_total'][24]+=$rows[csf($prod_hour)];
		$re_iron_data_arr['grand_total'][24]+=$rows[csf($re_pro_hour)];
		$rej_data_arr['grand_total'][24]+=$rows[csf($reject_hour)];
		
		$dataArr[$key]['quantity']+=$rows[csf('good_qnty')];
		$dataArr[$key]['supervisor'][$rows[csf('supervisor')]]=$rows[csf('supervisor')];
		$dataArr[$key]['company_id'][$rows[csf('company_id')]]=$rows[csf('company_id')];
		$dataArr[$key]['location'][$rows[csf('location')]]=$rows[csf('location')];
		$dataArr[$key]['floor_id'][$rows[csf('floor_id')]]=$rows[csf('floor_id')];
		$dataArr[$key]['buyer_name'][$rows[csf('buyer_name')]]=$rows[csf('buyer_name')];
		$dataArr[$key]['job_no'][$rows[csf('job_no')]]=$rows[csf('job_no')];
		$dataArr[$key]['style_ref_no'][$rows[csf('style_ref_no')]]=$rows[csf('style_ref_no')];
		$dataArr[$key]['item_number_id'][$rows[csf('item_number_id')]]=$rows[csf('item_number_id')];
		$dataArr[$key]['po_number'][$rows[csf('po_number')]]=$rows[csf('po_number')];
		$dataArr[$key]['po_break_down_id'][$rows[csf('po_break_down_id')]]=$rows[csf('po_break_down_id')];
		
	}	
	
	// echo "<pre>";print_r($dataArr);die;
				
		
	$hour_count=count($start_hour_arr);
    $width=($hour_count*40)+ 1400;
	ob_start();			
	?>
	<fieldset style="width:<?= $width+40;?>px">
		<table width="<?= $width+20;?>" cellpadding="0" cellspacing="0"> 
			<tr class="form_caption">
				<td colspan="<?= $hour_count+11;?>" align="center"><strong style="font-size:18px"><? echo $report_title; ?></strong></td> 
			</tr>
			<tr class="form_caption">
				<td colspan="<?= $hour_count+11;?>" align="center"><strong style="font-size:18px"><? echo $companyArr[$cbo_working_company_id]; ?></strong></td> 
			</tr>
			<tr class="form_caption">
				<td colspan="<?= $hour_count+11;?>" align="center"><strong><? echo "Date:  ".change_date_format( $txt_pro_date); ?></strong></td> 
			</tr>
		</table>
		<br />
		
        <table id="table_header_1" width="400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
				<caption>Summary (Production-Regular Order)</caption>
				<thead>
               <th>Quality</th>
               <th>Quantity (PCS)</th>	
			</thead>
			<tbody>
                <tr>
                   <td>Total Iron</td>
                   <td align="right"><?= array_sum($iron_data_arr['grand_total']);?></td>	
                </tr>
                <tr>
                   <td>Total Re-Iron</td>
                   <td align="right"><?= array_sum($re_iron_data_arr['grand_total']);?></td>	
                </tr>
                <tr>
                   <td>Total Reject</td>
                   <td align="right"><?= array_sum($rej_data_arr['grand_total']);?></td>	
                </tr>
			</tbody>
        </table>            
        <br />
        
        <table id="table_header_1" class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<caption>Production-Regular Order</caption>
            <thead>
               <th width="40">SL</th>
               <th width="80">Com</th>	
               <th width="80">Location</th>
               <th width="80">Floor</th>	
               <th width="80">Table No</th>	
               <th width="80">Job No</th>	
               <th width="120">Style Ref.</th>	
               <th width="100">Order No</th>	
               <th width="80">Buyer</th>	
               <th width="80">Item</th>	
               <th width="80">Hourly Target</th>
               <th width="80">Quality</th>
                <? 
                foreach($start_hour_arr as $hkey=>$hval){
                    echo '<th width="40">'.$hval.'</th>';
                }
                ?>
               <th width="80">Total</th>
               <th width="80">Day Target</th>
               <th width="80">Table Achv %</th>
               <th width="80">Supervisor</th>
               <th>Remarks</th>
			</thead>
		</table>
		<div style="width:<?= $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
        <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
           <tbody>
               <?
			   $i=1;
			   foreach($dataArr as $key=>$rows)
			   {
			    	$bgcolor =($i%2==0)?"#E9F3FF":"#FFFFFF";
			   
			   	// list($company_id,$location,$floor_id,$table_no,$buyer_name,$job_no,$style_ref_no,$item_number_id,$po_number,$po_break_down_id)=explode('**',$key);
			   	?>
              	<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                   <td rowspan="3" width="40"><?= $i;?></td>
                   <td rowspan="3" width="80">
                   		<?
                   			$company_name = "";
                   			$companyIds = "";
                   		 	foreach ($rows['company_id'] as $key2 => $value) 
                   		 	{
                   		 		$company_name .= ($company_name=="") ? $companyArr[$value] : ",".$companyArr[$value];
                   		 		$companyIds .= ($companyIds=="") ? $value : ",".$value;
                   		 	}
                   		 	echo $company_name;
                   		 ?>                   		
                   	</td>	
                   <td rowspan="3" width="80">
                   	<?
               			$location_name = "";
               		 	foreach ($rows['location'] as $key2 => $value) 
               		 	{
               		 		$location_name .= ($location_name=="") ? $locationArr[$value] : ",".$locationArr[$value];
               		 	}
               		 	echo $location_name;
               		?>
                   </td>
                   <td rowspan="3" width="80">
                   	<?
               			$floor_name = "";
               		 	foreach ($rows['floor_id'] as $key2 => $value) 
               		 	{
               		 		$floor_name .= ($floor_name=="") ? $floorArr[$value] : ",".$floorArr[$value];
               		 	}
               		 	echo $floor_name;
               		?></td>	
                   <td rowspan="3" width="80"><?= $tableNameArr[$key];?></td>	
                   <td rowspan="3" width="80">
                   	<?
               			$job_no = "";
               		 	foreach ($rows['job_no'] as $key2 => $value) 
               		 	{
               		 		$job_no .= ($job_no=="") ? $value : ", ".$value;
               		 	}
               		 	echo $job_no;
               		?></td>	
                   <td rowspan="3" width="120"><p>
	                   	<?
	               			$style_ref_no = "";
	               		 	foreach ($rows['style_ref_no'] as $key2 => $value) 
	               		 	{
	               		 		$style_ref_no .= ($style_ref_no=="") ? $value : ", ".$value;
	               		 	}
	               		 	echo $style_ref_no;
	               		?></p>
               		</td>	
                   <td rowspan="3" width="100"><p>
	                   	<?
	               			$po_number = "";
	               		 	foreach ($rows['po_number'] as $key2 => $value) 
	               		 	{
	               		 		$po_number .= ($po_number=="") ? $value : ", ".$value;
	               		 	}
	               		 	echo $po_number;
	               		?>
	               			
	               	</p></td>	
                   <td rowspan="3" width="80">
	                   	<?
	               			$buyer_name = "";
	               		 	foreach ($rows['buyer_name'] as $key2 => $value) 
	               		 	{
	               		 		$buyer_name .= ($buyer_name=="") ? $buyerArr[$value] : ", ".$buyerArr[$value];
	               		 	}
	               		 	echo $buyer_name;
	               		?></td>	
                   <td rowspan="3" width="80">
	                   	<?
	               			$item_name = "";
	               		 	foreach ($rows['item_number_id'] as $key2 => $value) 
	               		 	{
	               		 		$item_name .= ($item_name=="") ? $garments_item[$value] : ", ".$garments_item[$value];
	               		 	}
	               		 	echo $item_name;
	               		?></td>	
                   <td rowspan="3" width="80" align="center"><?= $prod_resource_array['target_per_hour'][$location.$floor_id];?></td>
                   <td width="80">Iron</td>
                    <? 
                    foreach($start_hour_arr as $hkey=>$hval)
                    {
                       echo '<td width="40" align="right">'.$iron_data_arr[$key][$hkey].'</td>';
                    }
                    ?>
                   <td width="80" align="right"><?= array_sum($iron_data_arr[$key]);?></td>
                   <td rowspan="3" width="80" align="center"><?= array_sum($prod_resource_array['target_per_hour']);?></td>
                   <td rowspan="3" width="80">
                   <?
                   $line_achive=(array_sum($iron_data_arr[$key])+array_sum($re_iron_data_arr[$key]))/array_sum($prod_resource_array['target_per_hour'])*100;
				   echo number_format(',',$line_achive);
				   ?>
                   </td>
                   <td rowspan="3" width="80">
	                   	<?
	               			$supervisor = "";
	               		 	foreach ($rows['supervisor'] as $key2 => $value) 
	               		 	{
	               		 		$supervisor .= ($supervisor=="") ? $value : ", ".$value;
	               		 	}
	               		 	echo $supervisor;
	               		?>	               			
	               	</td>
                   <td rowspan="3"><a href="javascript:show_line_remarks('<? echo $key."**".$txt_pro_date."**".$companyIds; ?>','remarks_popup')">Remarks</a></td>
                   
                   
               </tr>
               <tr>
                   <td width="80">Re Iron</td>
                    <? 
                    foreach($start_hour_arr as $hkey=>$hval){
                       echo '<td width="40" align="right">'.$re_iron_data_arr[$key][$hkey].'</td>';
                    }
                    ?>
                   <td width="80" align="right"><?= array_sum($re_iron_data_arr[$key]);?></td>
               </tr>
               <tr>
                   <td width="80">Reject</td>
                    <? 
                    foreach($start_hour_arr as $hkey=>$hval){
                       echo '<td width="40" align="right">'.$rej_data_arr[$key][$hkey].'</td>';
                    }
                    ?>
                   <td width="80" align="right"><?= array_sum($rej_data_arr[$key]);?></td>
                </tr>
                <?
				$i++;
			   }
			   ?>
            </tbody>
        </table>
		</div>
            
        <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
           <tfoot>
               <tr>
                   <th rowspan="3" width="40"></th>
                   <th rowspan="3" width="80"></th>	
                   <th rowspan="3" width="80"></th>
                   <th rowspan="3" width="80"></th>	
                   <th rowspan="3" width="80"> </th>	
                   <th rowspan="3" width="80"> </th>	
                   <th rowspan="3" width="120"></th>	
                   <th rowspan="3" width="100"> </th>	
                   <th rowspan="3" width="80"></th>	
                   <th rowspan="3" width="80"></th>	
                   <th rowspan="3" width="80">Grand Total</th>
                   <td width="80">Iron</td>
                    <? 
                    foreach($start_hour_arr as $hkey=>$hval){
                        echo '<th width="40">'.$iron_data_arr['grand_total'][$hkey].'</th>';
                    }
                    ?>
                   <th width="80"><?= array_sum($iron_data_arr['grand_total']);?></th>
                   <th rowspan="3" width="80"><?= array_sum($prod_resource_array['target_per_hour']);?></th>
                   <th rowspan="3" width="80">
                   <?
                       $line_achive=(array_sum($iron_data_arr['grand_total'])+array_sum($re_iron_data_arr['grand_total']))/array_sum($prod_resource_array['target_per_hour'])*100;
				   echo number_format(',',$line_achive);
				   ?>

                   </th>
                   <th rowspan="3" width="80"></th>
                   <th rowspan="3"></th>
               </tr>
               
               
               <tr>
                   <td width="80">Re Iron</td>
                    <? 
                    foreach($start_hour_arr as $hkey=>$hval){
                        echo '<th width="40">'.$re_iron_data_arr['grand_total'][$hkey].'</th>';
                    }
                    ?>
                   <th width="80"><?= array_sum($re_iron_data_arr['grand_total']);?></th>
               </tr>
               <tr>
                   <td width="80">Reject</td>
                    <? 
                    foreach($start_hour_arr as $hkey=>$hval){
                        echo '<th width="40">'.$rej_data_arr['grand_total'][$hkey].'</th>';
                    }
                    ?>
                   <th width="80"><?= array_sum($rej_data_arr['grand_total']);?></th>
               </tr>
               
               
            </tfoot>
        </table>
	</fieldset>
	<? 
	$html=ob_get_contents();
	ob_clean();   
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$filename=$user_id."_".time().".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html####$filename";
	exit();
	
	
}

if($action=="remarks_popup")
	{
		echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	    extract($_REQUEST);
		// list($company_id,$location,$floor_id,$table_no,$buyer_name,$job_no,$style_ref_no,$item_number_id,$po_number,$po_break_down_id)=explode('**',$data);
		list($table_no,$prod_date,$company_id)=explode('**',$data);
		
		if($db_type==0)
		{
			$prod_date=change_date_format($prod_date,"yyyy-mm-dd", "-");
		}
		else
		{
			$prod_date=change_date_format($prod_date,'','',1);	
		}
		
		
		$sql="SELECT remarks,production_hour from pro_garments_production_mst where company_id in($company_id) and production_date='$prod_date' and status_active=1 and is_deleted=0 order by production_hour";
		// echo $sql;
		$sql_line_remark=sql_select($sql);
		?>
		<fieldset style="width:520px;  ">
            <div id="report_container">
                    <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0" align="center">
                        <thead>
                            <th width="40">SL</th>
                            <th width="460">Remarks</th>
                        </thead>
                        <tbody>
                        <?
						$i=1;
                        foreach($sql_line_remark as $inf)
						{
						 if ($i%2==0)    $bgcolor="#E9F3FF";
                         else            $bgcolor="#FFFFFF";
						 if(trim($inf[csf('remarks')])!="")
						 {
							 ?>		
							   <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td align="left"><? echo $inf[csf('remarks')]; ?>&nbsp;</td>
	                         
								   
	                        </tr>
							<?
							$i++;
						 }
							
						}
                        
						
						?>
                        </tbody>
                        
                        
                    </table>
            </div>
        </fieldset>
           
              <?
	}

 
?>