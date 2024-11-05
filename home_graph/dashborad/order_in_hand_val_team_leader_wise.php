<?
session_start();
include('../../includes/common.php');
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);

function add_month($effectiveDate,$totalMonth){
	return date('Y-m-d', strtotime($effectiveDate . " $totalMonth months") );
}
function days_in_month($effectiveDate){
	return cal_days_in_month(CAL_GREGORIAN, date("m", strtotime($effectiveDate)), date("Y", strtotime($effectiveDate)));
}

extract($_REQUEST);
$m= base64_decode($m);
list($company,$location,$floor,$working_company)=explode('__',$cp);
$type=0;


if($company!=0)$str_comp=" and comp.id=$company";
else if($working_company!=0)$str_comp=" and comp.id=$working_company";

if($type==1){$dateField=" b.pub_shipment_date";}
else{$dateField=" a.country_ship_date";}

 
if($type==1){
	if($db_type==0) $year_field="b.pub_shipment_date"; else $year_field="to_char(b.pub_shipment_date,'YYYY-MM-DD')";
}
else{
	if($db_type==0) $year_field="a.country_ship_date"; else $year_field="to_char(a.country_ship_date,'YYYY-MM-DD')";
}


	if($db_type==0) 
	{
		$manufacturing_company=return_field_value("group_concat(comp.id)","lib_company as comp","comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond");
	}
	else
	{
		$manufacturing_company= return_field_value("LISTAGG(comp.id, ', ') WITHIN GROUP (ORDER BY comp.id) company","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $str_comp $company_cond","company");
	}
	$no_of_company=count(explode(",",$manufacturing_company));


	$team_leader_arr=return_library_array("select id,team_leader_name from  lib_marketing_team where status_active=1 and is_deleted=0","id","team_leader_name");



	
	if( $_SESSION['logic_erp']["data__"]=="")
	{
		$tot_month = datediff( 'm', $from_date,$to_date);
		$tot_month=($tot_month)?$tot_month:11;
		
		$month_prev=add_month(date("Y-m-d",time()),-3);
		$month_next=add_month(date("Y-m-d",time()),8);
		$month_prev=($from_date)?$from_date:$month_prev;
		$month_next=($from_date)?$to_date:$month_next;
		$lastMonthTotalDay = days_in_month($month_next);
				
		if($db_type==0)
		{
			$month_prev=date("Y-m-1", strtotime($month_prev));
			$month_next=date("Y-m-".$lastMonthTotalDay, strtotime($month_next));
		}
		else 
		{
			$month_prev=date("1-M-Y", strtotime($month_prev));
			$month_next=date($lastMonthTotalDay."-M-Y", strtotime($month_next));
		}
		
		
		$start_yr=date("Y",strtotime($month_prev));
		$end_yr=date("Y",strtotime($month_next));
		for($e=0;$e<=$tot_month;$e++)
		{
			$tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
			$yr_mon_part[$e]=date("Y-m",strtotime($tmp));
			$monthArr[$e]=date("M",strtotime($tmp));
		}
		
		
			
			
			
			if($type==1){
				if($db_type==0) 
				{
					$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price,c.team_leader, 
					sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS 'confpoval', 
					sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS 'projpoval' ,
					sum(CASE WHEN b.is_confirmed=1 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS 'confpoqty', 
					sum(CASE WHEN b.is_confirmed=2 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS 'projpoqty' 
					from wo_po_break_down as b, wo_po_details_master as c 
					where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $dateField between '$month_prev' and '$month_next' group by b.id,c.team_leader";
				}
				else
				{
					$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price,c.team_leader,
					sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS confpoval, 
					sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS projpoval ,
					sum(CASE WHEN b.is_confirmed=1 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS confpoqty, 
					sum(CASE WHEN b.is_confirmed=2 THEN (b.po_quantity*c.total_set_qnty) ELSE 0 END) AS projpoqty 
					from wo_po_break_down b, wo_po_details_master c 
					where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $dateField between '$month_prev' and '$month_next' group by b.id, c.total_set_qnty,b.unit_price,c.team_leader";	 
				}
			}
			else
			{
				if($db_type==0) 
				{
					$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id,c.team_leader,$dateField as select_date , 
					sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS 'confpoval', 
					sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS 'projpoval' ,
					sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS 'confpoqty', 
					sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS 'projpoqty' 
					from wo_po_color_size_breakdown as a, wo_po_break_down as b, wo_po_details_master as c 
					where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $dateField between '$month_prev' and '$month_next' group by b.id, a.country_id,c.team_leader,$dateField";
				}
				else
				{
					$sql="select b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id,c.team_leader,$dateField as select_date, 
					sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS confpoval, 
					sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS projpoval ,
					sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS confpoqty, 
					sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS projpoqty 
					from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c 
					where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $dateField between '$month_prev' and '$month_next' group by b.id, a.country_id,c.total_set_qnty,b.unit_price,c.team_leader,$dateField ";	 
				}
			}
			//echo $sql;
			$result=sql_select($sql);		
			foreach($result as $row)
			{
				$ym=date("Y-m",strtotime($row[csf('select_date')]));
				
				$temLeaderArr[$row[csf('team_leader')]]=$row[csf('team_leader')];
				//$dataArr['conf_qty'][$row[csf('team_leader')]][$ym]+=$row[csf('confpoqty')];
				//$dataArr['proj_qty'][$row[csf('team_leader')]][$ym]+=$row[csf('projpoqty')];
				$dataArr['conf_val'][$row[csf('team_leader')]][$ym]+=$row[csf('confpoval')];
				$dataArr['proj_val'][$row[csf('team_leader')]][$ym]+=$row[csf('projpoval')];
				
				$poArr[$row[csf('po_id')]]=$row[csf('po_id')];
			
			}
	}
		
		
	
		$po_list_arr=array_chunk($poArr,999);
		$sql_con ="";
		foreach($po_list_arr as $po_id)
		{
			if($sql_con ==""){$sql_con =" and (po_break_down_id in(".implode(',',$po_id).")";} 
			else{$sql_con .=" or (po_break_down_id in(".implode(',',$po_id).")";} 
		}
		$sql_con .=")";		
		
		
		
		$exFactory_arr=array();
		$data_arr=sql_select( "select po_break_down_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $sql_con group by po_break_down_id, country_id");
		foreach($data_arr as $row)
		{
			$exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]+=$row[csf('ex_factory_qnty')];
		}
	
		
		
		$listDataArr=array();
		foreach($result as $row)
		{
	
			$ym=date("Y-m",strtotime($row[csf('select_date')]));
			$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
			
			if($type==1){
				//$exFactoryQty=array_sum($exFactory_arr[$row[csf('po_id')]]);
				$exFactoryVal=array_sum($exFactory_arr[$row[csf('po_id')]])*$unit_price;
			}
			else
			{
				//$exFactoryQty=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]];
				$exFactoryVal=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]]*$unit_price;
			}
			
			
			
			//$listDataArr['conf_qty'][$ym]+=$row[csf('confpoqty')];
			//$listDataArr['proj_qty'][$ym]+=$row[csf('projpoqty')];
			$listDataArr['conf_val'][$ym]+=$row[csf('confpoval')];
			$listDataArr['proj_val'][$ym]+=$row[csf('projpoval')];
			//$listDataArr['exft_qty'][$ym]+=$exFactoryQty;
			$listDataArr['exft_val'][$ym]+=$exFactoryVal;
		}
		
		
		
		
		$k=1;$subtitle_text_arr=array();
		foreach($temLeaderArr as $tem_leader_id)
		{ 
			
			$subtitle_text_arr[$tem_leader_id]=$team_leader_arr[$tem_leader_id].':'.number_format((array_sum($dataArr['conf_val'][$tem_leader_id])+array_sum($dataArr['proj_val'][$tem_leader_id])),2,'.','').' USD';
			
			$data_val .="{ name: '".$team_leader_arr[$tem_leader_id]."', data:[";
			$data_qty .="{ name: '".$team_leader_arr[$tem_leader_id]."', data:[";
			for($i=0;$i<=$tot_month;$i++)
			{
				$value=$dataArr['conf_val'][$tem_leader_id][$yr_mon_part[$i]]+$dataArr['proj_val'][$tem_leader_id][$yr_mon_part[$i]];
				$qty=$dataArr['conf_qty'][$tem_leader_id][$yr_mon_part[$i]]+$dataArr['proj_qty'][$tem_leader_id][$yr_mon_part[$i]];
				
				if( $i!=$tot_month) 
				{
					$data_val .=number_format( $value,2,'.','').",";
					$data_qty .=number_format( $qty,0,'.','').",";
				}
				else 
				{
					$data_val .=number_format( $value,2,'.','').""; 
					$data_qty .=number_format( $qty,0,'.','').""; 
				}
			}
		
			if(count($temLeaderArr)==$k){
				$data_val .="]}";
				$data_qty .="]}";
			}
			else
			{
				$data_val .="]},";
				$data_qty .="]},";
			}
			$k++;
		}
		

 	
 		
?>

<table>
    <tr>
        <td width="73%">
            <div id="chartdiv" style="width:100%; height:400px; background-color:#FFFFFF; border:1px solid #999;"></div>
        </td>
        <td></td>
        <td valign="top">
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="360" id="tableQty">
                <thead>
                    <th width="55">Month</th>
                    <th>Proj</th>
                    <th>Conf</th>
                    <th>Total</th>
                    <th>Ship Out</th>
                    <th>%</th>
                </thead>
				<?
                $i=1;
                foreach($yr_mon_part as $key=>$ym)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td align="center"><? echo date("M",strtotime($ym))."-".date("y",strtotime($ym));?></td>
                        <td align="right"><? echo number_format($listDataArr['proj_val'][$ym],0);?></td>
                        <td align="right"><? echo number_format($listDataArr['conf_val'][$ym],0);?></td>
                        <td align="right"><? echo number_format($listDataArr['proj_val'][$ym]+$listDataArr['conf_val'][$ym],0);?></td>
                        <td align="right"><? echo number_format($listDataArr['exft_val'][$ym],0);?></td>
                        <td align="right"><? echo fn_number_format($listDataArr['exft_val'][$ym]/($listDataArr['proj_val'][$ym]+$listDataArr['conf_val'][$ym])*100,2);?></td>
                    </tr> 
                    
                    <?
                    $i++; 
                    }
                    ?> 
                    
					<tfoot>
                        <th align="center">Total</td>
                        <th align="right"><? echo number_format(array_sum($listDataArr['proj_val']),0);?></th>
                        <th align="right"><? echo number_format(array_sum($listDataArr['conf_val']),0);?></th>
                        <th align="right"><? echo number_format(array_sum($listDataArr['proj_val'])+array_sum($listDataArr['conf_val']),0);?></th>
                        <th align="right"><? echo number_format(array_sum($listDataArr['exft_val']),0);?></th>
                        <th align="right"><? echo fn_number_format(array_sum($listDataArr['exft_val'])/(array_sum($listDataArr['proj_val'])+array_sum($listDataArr['conf_val']))*100,2);?></th>
                    </tfoot>                    
                                
            </table>

        </td>
    </tr>
</table>



<script src="../ext_resource/hschart/highcharts.js"></script>
<script>

Highcharts.chart('chartdiv', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'ORDER IN HAND VALUE TEAM LEADER WISE'
  },
  subtitle: {
    text: '<? echo implode(', ',$subtitle_text_arr);?>'
  },
  xAxis: {
    categories: ['<? echo implode("','",$monthArr);?>'],
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'TOTAL VALUE'
    }
  },
  tooltip: {
    /*headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
      '<td style="padding:0"><b>{point.y:.1f} USD</b></td></tr>',
    footerFormat: '</table>',
    shared: true,
    useHTML: true*/
	formatter: function() {
            return this.x+' <em>: ' + this.y +' USD</em>';
        }
  },
  plotOptions: {
    column: {
      pointPadding: 0.2,
      borderWidth: 0
    }
  },
  
  series: [<? echo $data_val;?>]
});

</script>    

