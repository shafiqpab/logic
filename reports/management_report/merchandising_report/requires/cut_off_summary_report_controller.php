<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Cut off summary Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	05/10/2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/


session_start();
include('../../../../includes/common.php');
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/cut_off_summary_report_controller', this.value, 'load_drop_down_season_buyer', 'season_td');" );
	exit();
}

if ($action=="week_date")
{
	$data=explode("_",$data);
	$sql_week_start_end_date=sql_select("select week, min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week=$data[0] and year= $data[1] group by week");
	$week_start_day=0;
	$week_end_day=0;
	foreach ($sql_week_start_end_date as $row_week_week_start_end_date)
	{
		$week_start_day=$row_week_week_start_end_date[csf("week_start_day")];
		$week_end_day=$row_week_week_start_end_date[csf("week_end_day")];
	}
	echo change_date_format($week_start_day,"dd-mm-yyyy",'-')."_".change_date_format($week_end_day,"dd-mm-yyyy",'-');
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_id", 120, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');


if ($action=="load_drop_down_team_member")
{
echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Dealing Merchant-", $selected, "" );
}


if($action=="report_generate")
{
	//$txt_date_from=add_date(str_replace("'","",$txt_date_from),-1);
	//$txt_date_to=add_date(str_replace("'","",$txt_date_to),-1);
	
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_week=str_replace("'","",$cbo_week);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	
	if($cbo_company_name>0){$where_con.=" and a.company_name=$cbo_company_name";}
	if($cbo_buyer_name>0){$where_con.=" and a.buyer_name=$cbo_buyer_name";}
	if($cbo_season_id>0){$where_con.=" and a.season_buyer_wise=$cbo_season_id";}
	if($cbo_order_status>0){$where_con.=" and b.is_confirmed=$cbo_order_status";}	
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($txt_date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($txt_date_to,"yyyy-mm-dd","");
			
			$start_date2=change_date_format(add_date($txt_date_from,-1),"yyyy-mm-dd","");
			$end_date2=change_date_format(add_date($txt_date_to,-1),"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime($txt_date_from));
			$end_date=date("j-M-Y",strtotime($txt_date_to));
			
			$start_date2=date("j-M-Y",strtotime(add_date($txt_date_from,-1)));
			$end_date2=date("j-M-Y",strtotime(add_date($txt_date_to,-1)));
		}
		
		if($cbo_date_category==3){
			$where_con .=" and c.country_ship_date between '$start_date2' and '$end_date2'";
		}
	}

	//Order.....................................
	
	$sql=("SELECT 
	a.job_no, a.buyer_name,a.season_buyer_wise,a.style_ref_no,a.product_dept,b.po_number,b.id as po_id,c.country_ship_date,c.country_id,c.cutup, 
	 sum(c.order_quantity) as po_quantity_pcs,sum(c.plan_cut_qnty) as plan_cut_qnty,
	a.total_set_qnty,b.po_quantity
	
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $where_con
	group by a.job_no, a.buyer_name,a.season_buyer_wise,a.style_ref_no,a.product_dept,b.id,b.po_number,c.country_ship_date,c.country_id,c.cutup
	,a.total_set_qnty,b.po_quantity
	
	");
		
	
	 //echo $sql; 
	
	$dataArr=array();$po_qty_arr=array();
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		
		$key=$row[csf("product_dept")].'*'.$row[csf("season_buyer_wise")].'*'.$row[csf("job_no")].'*'.$row[csf("buyer_name")].'*'.$row[csf("po_number")].'*'.$row[csf("style_ref_no")].'*'.$row[csf("po_id")];
		$dataArr[$key][$row[csf("country_id")]]=array(
			'country_id'=>$row[csf("country_id")],
			'country_ship_date'=>$row[csf("country_ship_date")],
			'plan_cut_qnty'=>$row[csf("plan_cut_qnty")],
			'po_id'=>$row[csf("po_id")],
		);
		$po_qty_arr[$key]=($row[csf("total_set_qnty")]*$row[csf("po_quantity")]);
		$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		//$cutup_arr[date('d-m-Y',strtotime($row[csf("country_ship_date")]))]=$row[csf("cutup")];
		
		$country_ship_date=date('d-m-Y',strtotime($row[csf("country_ship_date")]));
		//$country_plan_cut_qty_arr[$key][$country_ship_date]+=$row[csf("po_quantity_pcs")];
		$country_plan_cut_qty_arr[$key][$row[csf("country_id")]][$country_ship_date]+=$row[csf("po_quantity_pcs")];
		$date_plan_cut_qty_arr[$country_ship_date]+=$row[csf("po_quantity_pcs")];
	}	
	
	
	$po_id_list_arr=array_chunk($po_id_arr,999);

	$p=1;
	foreach($po_id_list_arr as $po_id_process)
	{
		if($p==1) $sql_con .="  and (po_break_down_id in(".implode(',',$po_id_process).")"; 
		else  $sql_con .=" or b.po_break_down_id in(".implode(',',$po_id_process).")";
		$p++;
	}
	$sql_con .=")";

	//exfactory..............................................
	$ex_qty_arr=array();
	$exfactory_data=sql_select("select po_break_down_id,country_id,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
	MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where status_active=1 and is_deleted=0 $sql_con group by po_break_down_id,country_id");
	foreach($exfactory_data as $row)
	{
		$ex_qty_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		
	}
	
	//Week.......................................
	$weekDataArr=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$start_date' and  '$end_date'  order by week_date");
	foreach ($sql_week_header as $row)
	{
		$row[csf("week_date")]=date('d-m-Y',strtotime(add_date($row[csf("week_date")],-1)));
		//$row[csf("week_date")]=date('d-m-Y',strtotime($row[csf("week_date")]));
		$date_week_arr[$row[csf("week_date")]]=$row[csf("week")];
		$weekDataArr[$row[csf("week")]][$row[csf("week_date")]]=$row[csf("week_date")];
	}
	
	
$width=(count($date_week_arr)*50)+860;
ob_start();	
?>	
<div style="width:<? echo $width+20;?>px">	
<table align="left" class="rpt_table" id="report_table_header" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
    <thead>
        <tr>	
            <th width="35" rowspan="4">SL</th>
            <th rowspan="4">Prod. Dept.</th>
            <th width="60" rowspan="4">Season</th>
            <th width="70" rowspan="4">Job No</th>
            <th width="100" rowspan="4">Buyer</th>
            <th width="100" rowspan="4">Order</th>
            <th width="60" rowspan="4">Style</th>
            <th width="60" rowspan="4">Total Order Qty (Pcs)</th>
            <th width="100" rowspan="4">Country</th>
            <th width="70" rowspan="4">Country Ship date</th>
            <th width="60" rowspan="4">Ship Out Qty</th>
            <th width="60" rowspan="4">Ship Balance</th>
            <? foreach($weekDataArr as $week_no=>$row){?>
            <th colspan="<? echo count($row);?>">Week-<? echo $week_no;?></th> 
			<? } ?>
        </tr>
        
        <tr>
            <? 
			foreach($weekDataArr as $data_rows){
				foreach($data_rows as $date){
			?>
            <th width="50"><? echo date('D',strtotime($date));?></th>
            <? }
			} 
			?>
        </tr>
        <tr>
            <? 
			foreach($weekDataArr as $data_rows){
				foreach($data_rows as $date){
			?>
            <th width="50"><? echo date('d-M',strtotime($date));?></th>
            <? }
			} 
			?>
        </tr>
        <tr>
            <? foreach($weekDataArr as $row){
				
				if(count($row)<=1){echo '<th colspan="1">1st  Cut Off</th>';}
				else if(count($row)>1 && count($row)<=2){echo '<th colspan="2">1st  Cut Off</th>';}
				else if(count($row)>2 && count($row)<=3){
					echo '<th colspan="2">1st  Cut Off</th>';
					echo '<th colspan="1">2nd Cut Off</th>';
				}
				else if(count($row)>2 && count($row)<=4){
					echo '<th colspan="2">1st  Cut Off</th>';
					echo '<th colspan="2">2nd Cut Off</th>';
				}
				if(count($row)>4){
					echo '<th colspan="2">1st  Cut Off</th>';
					echo '<th colspan="2">2nd Cut Off</th>';
					echo '<th colspan="'.(count($row)-4).'">3rd Cut Off</th>';
				}
			?>
             <? } ?>
        </tr>
       
    </thead>
</table>

<div style=" max-height:400px; overflow-y:scroll; width:<? echo $width+18;?>px; float:left;"  id="scroll_body">
    <table width="<? echo $width;?>" border="1" class="rpt_table" rules="all" align="left" id="table-body">
    	
      <?
	  	$i=1;
	  
	  foreach($dataArr as $key=>$countryDataArr){
	  	list($product_dept_id,$season_buyer_wise,$job_no,$buyer_name,$po_number,$style_ref_no,$po_id)=explode('*',$key);
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	  ?>  
        
        <tr bgcolor="<? echo $bgcolor; ?>" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" >
        
        	<td width="35" rowspan="<? echo count($countryDataArr);?>" align="center"><? echo $i;?></td>
        	<td rowspan="<? echo count($countryDataArr);?>"><p><? echo $product_dept[$product_dept_id];?></p></td>
        	<td rowspan="<? echo count($countryDataArr);?>" width="60"><p><? echo $season_arr[$season_buyer_wise];?></p></td>
        	<td rowspan="<? echo count($countryDataArr);?>" width="70"><p><? echo $job_no;?></p></td>
        	<td rowspan="<? echo count($countryDataArr);?>" width="100"><p><? echo $buyer_arr[$buyer_name];?></p></td>
        	<td rowspan="<? echo count($countryDataArr);?>" width="100"><p><? echo $po_number;?></p></td>
        	<td rowspan="<? echo count($countryDataArr);?>" width="60"><p><? echo $style_ref_no;?></p></td>
        	<td rowspan="<? echo count($countryDataArr);?>" width="60" align="right"><? echo $po_qty_arr[$key];?></td>
           <?
		   $trFlag=0;
		   foreach($countryDataArr as $country_id=>$row){
			   if($trFlag>0){echo "<tr>";}
			?> 
        	<td width="100" align="center"><p><? echo $country_arr[$country_id];?></p></td>
        	<td width="70" align="center"><? echo change_date_format($row['country_ship_date']);?></td>
        	<td width="60" align="right"><? echo $ex_qty_arr[$po_id][$country_id];?></td>
        	<td width="60" align="right"><? echo $blance=round(array_sum($country_plan_cut_qty_arr[$key][$country_id])-$ex_qty_arr[$row['po_id']][$country_id]);?></td>
            
            <? 
			foreach($weekDataArr as $data_rows){
				foreach($data_rows as $date){
			?>
            	<td width="50" align="right"><? echo $country_plan_cut_qty_arr[$key][$country_id][$date];?></td>
            <? }
			} 
			?>
            
        </tr>
        
        <? 
			
			$trFlag++;
		   
		   
		   $blanceTotal+=$blance;
		   
		   
		   }
		   $i++;
		} 
		?>
        
        
    </table>
</div>
<table width="<? echo $width;?>" id="report_table_footer" border="1" class="rpt_table" rules="all" align="left">
    <tfoot>
    <tr>
            <th width="35"></th>
            <th></th>
            <th width="60"></th>
            <th width="70"></th>
            <th width="100"></th>
            <th width="100">Sub Total</th>
            <th width="60"></th>
            <th width="60" align="right"></th>
            <th width="100"></th>
            <th width="70"> </th>
            <th width="60" align="right"></th>
            <th width="60"></th>
            <? 
			foreach($weekDataArr as $week_no=>$data_rows){
				foreach($data_rows as $date){
			?>
            	<th width="50" align="right"><? echo $date_plan_cut_qty_arr[$date];?></th>
            <? 
				$weekly_cut_qty_arr[$week_no]+=$date_plan_cut_qty_arr[$date];
				}
			} 
			?>
    </tr>
    <tr>
            <th width="35"></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Grand Total</th>
            <th></th>
            <th align="right"><? echo array_sum($po_qty_arr);?></th>
            <th ></th>
            <th> </th>
            <th align="right"></th>
            <th ><? echo $blanceTotal;?></th>
            
            <? 
			foreach($weekDataArr as $week_no=>$data_rows){
			?>
            	<th colspan="<? echo count($data_rows);?>" style="text-align:center;"><? echo $weekly_cut_qty_arr[$week_no];?></th>
            <? 
			} 
			?>
    </tr>
    </tfoot>
</table>
</div>




<?


	$html = ob_get_contents();
	ob_clean();

	foreach (glob(""."*.xls") as $filename)
	{
	   @unlink($filename);
	}
	$name="cut_off_summary.xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	echo $html.'####'.$name;
	exit();

}



?>
