<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Today Production Graph.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');
 
//--------------------------------------------------------------------------------------------------------------------

?>	
    <script src="Chart.js-master/Chart.js"></script>
    <div style="margin-left:15px; margin-top:10px"><a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<a href="index.php?g=2">Today Hourly Prod.</a>&nbsp;&nbsp;<a href="index.php?g=4">Trend Daily</a></div>
	<div style="margin-left:15px; margin-top:10px">
        <div style="width:950px; height:300px; float:left; position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
        	<table style="margin-left:60px; font-size:12px">
            	<tr>
                	<td colspan="4">Today Hourly Production</td>
                </tr>
                <tr>
                    <td bgcolor="#FF3300" width="10"></td>
                    <td>Target</td>
                    <td bgcolor="#0066FF" width="10"></td>
                    <td>Production</td>
                </tr>
            </table>
            <canvas id="canvas" height="240" width="900"></canvas>
		</div>
        <?
        if($db_type==0)
        {
			$today=date('Y-m-d');
            $manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
        }
        else
        {
			$today=date('d-M-Y');
			//$today="27-Oct-2014";
            $manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
        }
		
		$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name in($manufacturing_company) and variable_list=23 and is_deleted=0 and status_active=1");
		
        $tph=0; $lineWiseTph=array(); $lineArr=array();
		$lineArr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
		
		if($prod_reso_allocation==1)
		{
			$tph_sql=sql_select("select b.id, b.line_number, sum(a.target_per_hour) tph from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.company_id in($manufacturing_company) and a.pr_date='$today' and a.is_deleted=0 and b.is_deleted=0 group by b.id, b.line_number, a.pr_date");
			foreach($tph_sql as $row)
			{
				$tph+=$row[csf('tph')];
				$lineWiseTph[$row[csf('id')]]=$row[csf('tph')];
				$lineResArr[$row[csf('id')]]=$row[csf('line_number')];
			}
			$linedataArr=$lineResArr;
		}
		else
		{
			$linedataArr=$lineArr;
		}
        
		$strat_hour=9; $no_of_hour=16; $hourdiff=$strat_hour+$no_of_hour; $lineWiseProd_arr=array(); $prod_arr=array();
		
		$sql="SELECT sewing_line,
					sum(case when production_hour between 1 and 9 then production_quantity else 0 end ) AS am9,
					sum(case when production_hour> 9 and production_hour<=10 then production_quantity else 0 end ) AS am10,
					sum(case when production_hour> 10 and production_hour<=11 then production_quantity else 0 end ) AS am11,
					sum(case when production_hour> 11 and production_hour<=12 then production_quantity else 0 end ) AS am12,
					sum(case when production_hour> 12 and production_hour<=13 then production_quantity else 0 end ) AS am13,
					sum(case when production_hour> 13 and production_hour<=14 then production_quantity else 0 end ) AS am14,
					sum(case when production_hour> 14 and production_hour<=15 then production_quantity else 0 end ) AS am15,
					sum(case when production_hour> 15 and production_hour<=16 then production_quantity else 0 end ) AS am16,
					sum(case when production_hour> 16 and production_hour<=17 then production_quantity else 0 end ) AS am17,
					sum(case when production_hour> 17 and production_hour<=18 then production_quantity else 0 end ) AS am18,
					sum(case when production_hour> 18 and production_hour<=19 then production_quantity else 0 end ) AS am19,
					sum(case when production_hour> 19 and production_hour<=20 then production_quantity else 0 end ) AS am20,
					sum(case when production_hour> 20 and production_hour<=21 then production_quantity else 0 end ) AS am21,
					sum(case when production_hour> 21 and production_hour<=22 then production_quantity else 0 end ) AS am22,
					sum(case when production_hour> 22 and production_hour<=23 then production_quantity else 0 end ) AS am23,
					sum(case when production_hour> 23 and production_hour<=24 then production_quantity else 0 end ) AS am24
				 from pro_garments_production_mst 
				where production_type=5 and company_id in($manufacturing_company) and production_date='$today' and is_deleted=0 and status_active=1 group by sewing_line";
		//echo $sql;die;			
        $sew_data_arr=sql_select($sql);
		foreach($sew_data_arr as $row)
		{
			for($j=$strat_hour;$j<$hourdiff;$j++)
			{
				$lineWiseProd_arr[$row[csf('sewing_line')]]['am'.$j]=$row[csf('am'.$j)];
				$prod_arr['am'.$j]+=$row[csf('am'.$j)];
			}
		}
		
		$sql_subconProd="select line_id, 
							sum(case when hour between 1 and 9 then production_qnty else 0 end ) AS am9,
							sum(case when hour> 9 and hour<=10 then production_qnty else 0 end ) AS am10,
							sum(case when hour> 10 and hour<=11 then production_qnty else 0 end ) AS am11,
							sum(case when hour> 11 and hour<=12 then production_qnty else 0 end ) AS am12,
							sum(case when hour> 12 and hour<=13 then production_qnty else 0 end ) AS am13,
							sum(case when hour> 13 and hour<=14 then production_qnty else 0 end ) AS am14,
							sum(case when hour> 14 and hour<=15 then production_qnty else 0 end ) AS am15,
							sum(case when hour> 15 and hour<=16 then production_qnty else 0 end ) AS am16,
							sum(case when hour> 16 and hour<=17 then production_qnty else 0 end ) AS am17,
							sum(case when hour> 17 and hour<=18 then production_qnty else 0 end ) AS am18,
							sum(case when hour> 18 and hour<=19 then production_qnty else 0 end ) AS am19,
							sum(case when hour> 19 and hour<=20 then production_qnty else 0 end ) AS am20,
							sum(case when hour> 20 and hour<=21 then production_qnty else 0 end ) AS am21,
							sum(case when hour> 21 and hour<=22 then production_qnty else 0 end ) AS am22,
							sum(case when hour> 22 and hour<=23 then production_qnty else 0 end ) AS am23,
							sum(case when hour> 23 and hour<=24 then production_qnty else 0 end ) AS am24
		 			FROM subcon_gmts_prod_dtls WHERE company_id in($manufacturing_company) and production_date='$today' and production_type=2 and status_active=1 and is_deleted=0
					group by line_id";
        $subconProdData=sql_select($sql_subconProd);
		foreach($subconProdData as $subRow)
		{
			for($j=$strat_hour;$j<$hourdiff;$j++)
			{
				$lineWiseProd_arr[$subRow[csf('line_id')]]['am'.$j]+=$subRow[csf('am'.$j)];
				$prod_arr['am'.$j]+=$subRow[csf('am'.$j)];
			}
		}
		
		$hour_array=array(); $sewTphArr=array(); $lineWiseSewTphArr=array(); $sewProdArr=array(); 
        for($j=$strat_hour;$j<$hourdiff;$j++)
        {
			$hour_array[]=$j;
			$sewTphArr[]=number_format($tph,0,'.','');
			$production_quantity=$prod_arr['am'.$j];
            $sewProdArr[]=number_format($production_quantity,0,'.','');
        }
         
        $hour_array= json_encode($hour_array);
		$sewTphArr=json_encode($sewTphArr);
        $sewProdArr= json_encode($sewProdArr);
        
    ?>
    <script>
        var lineChartData = {
            labels : <? echo $hour_array; ?>,
            datasets : [
                {
                    //label: "My First dataset",
                    fillColor : "rgba(220,220,220,0.2)",
                    strokeColor : "#FF3300",
                    pointColor : "#FF3300",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "#FF3300",
                    data : <? echo $sewTphArr; ?>
                }
                ,
                {
                    //label: "My Second dataset",
                    fillColor : "rgba(151,187,205,0.2)",
                    strokeColor : "#0066FF",
                    pointColor : "#0066FF",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "#0066FF",
                    data : <? echo $sewProdArr; ?>
                }
            ]

        }
		
       // window.onload = function(){
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx).Line(lineChartData, {
                responsive: true
            });
       // }
    </script>
 	<? 
		$i=0;
		foreach($linedataArr as $line_id=>$lineName)
		{
			$i++;
			$sewing_line='';
			if($prod_reso_allocation==1)
			{
				$line_number=explode(",",$lineName);
				foreach($line_number as $val)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
				}
			}
			else
			{
				$sewing_line=$lineName;
			}
			$lineWiseSewTphArr=array(); $lineWiseSewProdArr=array(); 
			$ltph=$lineWiseTph[$line_id];
			for($j=$strat_hour;$j<$hourdiff;$j++)
			{
				$lineWiseSewTphArr[]=number_format($ltph,0,'.','');
				$lineProdQty=$lineWiseProd_arr[$line_id]['am'.$j];
				$lineWiseSewProdArr[]=number_format($lineProdQty,0,'.','');
			}
			 
			$lineWiseSewTphArr=json_encode($lineWiseSewTphArr);
			$lineWiseSewProdArr= json_encode($lineWiseSewProdArr);
		?>
        	<div style="width:469px; height:240px; float:left; position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
                <table style="margin-left:60px; font-size:12px">
                    <tr>
                        <td colspan="4">Today Hourly Production Line No- <? echo $sewing_line; ?></td>
                    </tr>
                    <tr>
                        <td bgcolor="#FF3300" width="10"></td>
                        <td>Target</td>
                        <td bgcolor="#0066FF" width="10"></td>
                        <td>Production</td>
                    </tr>
                </table>
                <canvas id="canvas<? echo $i; ?>" height="180" width="450"></canvas>
            </div>
            <script>
				var lineChartData2 = {
					labels : <? echo $hour_array; ?>,
					datasets : [
						{
							//label: "My First dataset",
							fillColor : "rgba(220,220,220,0.2)",
							strokeColor : "#FF3300",
							pointColor : "#FF3300",
							pointStrokeColor : "#fff",
							pointHighlightFill : "#fff",
							pointHighlightStroke : "#FF3300",
							data : <? echo $lineWiseSewTphArr; ?>
						}
						,
						{
							//label: "My Second dataset",
							fillColor : "rgba(151,187,205,0.2)",
							strokeColor : "#0066FF",
							pointColor : "#0066FF",
							pointStrokeColor : "#fff",
							pointHighlightFill : "#fff",
							pointHighlightStroke : "#0066FF",
							data : <? echo $lineWiseSewProdArr; ?>
						}
					]
		
				}
				
				//window.onload = function(){
					var ctx = document.getElementById("canvas<? echo $i; ?>").getContext("2d");
					window.myLine = new Chart(ctx).Line(lineChartData2, {
						responsive: true
					});
				//}
			</script>
        <?
		}
	?>
	</div>