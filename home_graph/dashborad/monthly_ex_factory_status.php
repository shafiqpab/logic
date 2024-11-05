
	


<?
session_start();
include('../../includes/common.php');
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, 0,0);
?>

<?
function add_month($orgDate,$mon){
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}
extract($_REQUEST);
$m= base64_decode($m);
list($company,$location,$floor,$working_company)=explode('__',$cp);

if($company!=0){$str_comp=" and a.company_id=$company";	$str_comp2=" and company_id=$company";$str_comp3=" and c.company_name=$company";
			};

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	

?>

<script src="../../chart/highcharts_v2.js"></script>



<?


			$tot_month = datediff( 'm', $from_date,$to_date);
			$tot_month=($tot_month)?$tot_month:11;
			
			$month_prev=add_month(date("Y-m-d",time()),-3);
			$month_next=add_month(date("Y-m-d",time()),8);
			
			$month_prev=($from_date)?$from_date:$month_prev;
			$month_next=($from_date)?$to_date:$month_next;
		
		
			// echo 	$month_prev."".$month_next;
			// echo date("j-M-Y",strtotime($month_prev));
			$date_cond=" and a.delivery_date between '".date("j-M-Y",strtotime($month_prev))."' and '".date("j-M-Y",strtotime($month_next))."'";
			$date_cond2=" and production_date between '".date("j-M-Y",strtotime($month_prev))."' and '".date("j-M-Y",strtotime($month_next))."'";
	
			$date_cond3=" and a.ex_factory_date between '".date("j-M-Y",strtotime($month_prev))."' and '".date("j-M-Y",strtotime($month_next))."'";
			
			$start_yr=date("Y",strtotime($month_prev));
			$end_yr=date("Y",strtotime($month_next));
			for($e=0;$e<=$tot_month;$e++)
			{
				$tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
				$yr_mon_part[$e]=date("Y-m",strtotime($tmp));
			}
		
			$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
			(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
			b.total_carton_qnty as carton_qnty
			from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
			where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2";
			$challan_sql_result=sql_select($challan_sql);
			foreach($challan_sql_result as $row)
			{
				$exfact_return_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("ex_factory_return_qnty")];
			}
				// print_r($packing_finish_arr);

			$i=1;
			// $sql="SELECT  a.company_id, a.delivery_date,b.po_break_down_id, sum(b.ex_factory_qnty) as ex_factory_qnty,c.po_total_price, c.unit_price , d.total_set_qnty as ratio from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b ,wo_po_break_down c ,wo_po_details_master d 	where a.id=b.delivery_mst_id and c.id=b.po_break_down_id and d.job_no=c.job_no_mst  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form!=85 $str_comp $date_cond group by  a.company_id, a.delivery_date,b.po_break_down_id,c.po_total_price, d.total_set_qnty, c.unit_price order by a.delivery_date asc";	
			
			$sql="SELECT  sum(CASE WHEN a.entry_form!=85 THEN  a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,a.ex_factory_date, (b.unit_price/c.total_set_qnty) as unit_price,	b.id as po_id, a.delivery_mst_id as challan_id from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $str_comp3 $date_cond3  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3) and a.delivery_mst_id=d.id group by a.ex_factory_date,b.unit_price, c.total_set_qnty,b.id ,a.delivery_mst_id";
			
			//   echo $sql;
		
			$result=sql_select($sql);
			$exFactoryQty=0;  $confPoVal=0; $projPoVal=0; $exFactoryQty=0; $exFactoryVal=0;
			foreach($result as $row)
			{ 

				// $unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
				
				
				$month_no=date("m",strtotime($row[csf('ex_factory_date')]));
				$year=date("y",strtotime($row[csf('ex_factory_date')]));		
				// $all_graph_data[$year][$month_no]['ex_qty']+=$row[csf('ex_factory_qnty')]; 
				// $all_graph_data[$year][$month_no]['ex_val']+=$row[csf('ex_factory_qnty')]*$unit_price;

				 $all_graph_data[$year][$month_no]['ex_qty']+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];; 
				 $all_graph_data[$year][$month_no]['ex_val']+=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
				
				
			}

			$sql_2="SELECT id, company_id, po_break_down_id, production_date, production_quantity  from pro_garments_production_mst where  production_type='8' and status_active=1 and is_deleted=0 $str_comp2 $date_cond2  order by production_date asc";
			// echo $sql_2;
			$packing_finish=sql_select($sql_2);
			$packing_finish_qty=0;
				foreach($packing_finish as $row)
				{
					
					$month_no=date("m",strtotime($row[csf('production_date')]));
					$year=date("y",strtotime($row[csf('production_date')]));		
					$all_graph_data[$year][$month_no]['packing_qty']+=$row[csf('production_quantity')];
				}
				$html='<tbody>'; 
			$totExFactoryVal=0;$totExFactoryQty=0;$totPackingQty=0;			
				foreach($yr_mon_part as $key=>$val)
				{
					$month=date("M",strtotime($val));	
					$month_num=date("m",strtotime($val));			
					$year_no=date("y",strtotime($val));	
		
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
					$html.='<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
					$html.='<td>'.date("M",strtotime($val))."'".date("y",strtotime($val)).'</td>
							<td align="right">'.number_format($all_graph_data[$year_no][$month_num]['ex_qty'],0).'</td>
							<td align="right">'.number_format($all_graph_data[$year_no][$month_num]['ex_val'],0).'</td>
							<td align="right">'.number_format($all_graph_data[$year_no][$month_num]['packing_qty'],0).'</td>';			
					$html.='</tr>';
					
			
					$totExFactoryVal+=$all_graph_data[$year_no][$month_num]['ex_val'];
					$totExFactoryQty+=$all_graph_data[$year_no][$month_num]['ex_qty'];  
					$totPackingQty +=$all_graph_data[$year_no][$month_num]['packing_qty'];	

				
					if($i==1){

						$month_list .='"'.$month."'".$year_no.'"';
						
						$ex_factory_qty.=number_format($all_graph_data[$year_no][$month_num]['ex_qty'],0,'','') ;
						$ex_factory_val.=number_format($all_graph_data[$year_no][$month_num]['ex_val'],0,'','') ;
						$packing_qty.=number_format($all_graph_data[$year_no][$month_num]['packing_qty'],0,'','') ;
						$i++;
					}else{
						$month_list .=',"'.$month."'".$year_no.'"';
						$ex_factory_qty.=",".number_format($all_graph_data[$year_no][$month_num]['ex_qty'],0,'','') ;
						$ex_factory_val.=",".number_format($all_graph_data[$year_no][$month_num]['ex_val'],0,'','') ;
						$packing_qty.=",".number_format($all_graph_data[$year_no][$month_num]['packing_qty'],0,'','') ;
					}
					$i++;
		
					}
		
					$html.='</tr></tbody><tfoot><th>Total</th>'; 
					$html.='<th align="right">'.number_format($totExFactoryQty,0).'</th>
							<th align="right">'.number_format($totExFactoryVal,0).'</th>
							<th align="right">'.number_format($totPackingQty,0).'</th>';
   
							
		
?>
	<table width="1050" cellpadding="0" cellspacing="0">
	<tr>
		<td height="30" valign="middle" align="center" colspan="2">
			<font size="2" color="#4D4D4D"> <strong><span id="caption_text"></span></strong></font>
		</td>
		<td colspan="2" rowspan="2" valign="top" align="center"> 
			<div style="margin-left:5px; margin-top:45px">
				<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="360" id="tableQty">
					<thead>
						<th width="55">Month</th>
						<th>Ex-Qty.</th>
						<th>Ex-Value</th>
						<th>Fin Prod. Qty.</th>
					   
					</thead>
					

					<? echo $html; ?>
				</table>                  
			</div>
		</td>
	</tr>
	<tr>
		<td width="8" bgcolor=" "></td>
			<!-- chart er Image -->
		<td align="center" height="400" width="750">
			<!-- <div id="chartdiv" style="width:750px; height:400px; background-color:#FFFFFF">
			  <canvas id="myChart" style="width:100%;max-width:600px"></canvas> -->
			  <h2><b><? if($company!=0){echo $companyArr[$company];}else{ echo "All Company";}
					?> </b></h2>
			  <figure class="highcharts-figure">
			  	<div id="container"  style="width:750px; height:400px; background-color:#FFFFFF"></div>
				</figure>
				<!-- </div> -->


		</td>
		 
	</tr>
	<tr>
		<td height="8" colspan="2" bgcolor=" "></td>
		<td width="8" bgcolor=""></td>
		<td></td>
	</tr>
	<!-- <tr>
		<td colspan="2">
			<table width="100%">				
				<tr>
					<td width="150"></td>
					<td  align="right" valign="top">Copyright</td>
					<td align="right" valign="top" width="310"><img src="../../images/logic/logic_bottom_logo.png" height="65" width="300" /> 
					</td>
				</tr>
			</table>
		</td>
		 <td colspan="7" ></td>
	</tr> -->
</table>
<div>
<!-- <canvas id="myChart" style="width:100%;max-width:600px"></canvas> -->
</div>

  

<script>
Highcharts.chart('container', {
  title: {
    text: 'Monthly Ex-Factory Status'
  },
  tooltip: {
   formatter: function() {
      return '<b>'+ this.series.name +'</b>: '+ this.point.y ;
   }
	},
  xAxis: {
	gridLineWidth: 1,
	alternateGridColor: '#F7F7F7',
    categories: [<?=$month_list;?>]
  },
  labels: {
    items: [{
      html: '',
      style: {
        left: '20px',
        top: '8px',
        color: ( // theme
          Highcharts.defaultOptions.title.style &&
          Highcharts.defaultOptions.title.style.color
        ) 
      }
    }]
  },
  series: [{
    type: 'column',
    name: 'Ex-Factory Qty.',
     data: [<?=$ex_factory_qty;?>],
	 color: '#4d88ff'
  }, {
    type: 'column',
    name: 'Ex-Factory Val.',
     data: [<?=$ex_factory_val;?>],
	 color: '#cc3300'
	
  }, {
    type: 'spline',
    name: 'Finishing Production',
     data: [<?=$packing_qty;?>],
	 color: '#42f554',
    marker: {
      lineWidth: 2,
      lineColor: Highcharts.getOptions().colors[3],
      fillColor: 'white'
    }
  }]
});
	</script>
