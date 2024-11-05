<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Last 30 days idle lines and 30 days idle rmg worker
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	04.10.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');

echo load_html_head_contents("30 Days Idle Lines And Rmg Worker Graph", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];


if($m=="30_days_idle_lines")
{
	$caption="30 Days Idle Lines";
	$header="Idle Lines";
}
else
{
	$caption="30 Days Idle RMG Worker";	
	$header="Idle RMG Worker";
}

if($company!=0)
{
	$company=$company;
}
else
{
	$company="";
}
$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
?>
	<script>
    	var lnk='<? echo $m; ?>';
    </script>
	<script src="Chart.js-master/Chart.js"></script>
    
    <div align="center" style="width:100%;">
        <div style="margin-left:30px; margin-top:10px"><a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<br />&nbsp;&nbsp;
            <div style="width:900px; height:500px;  position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
                <div align="center" style="width:100%; font-size:16px;"><? echo $caption; ?></div>
                <div align="center" style="width:100%; font-size:14px;"><? echo " Company : ". $comp_arr[$company]; ?></div>
                <table style="margin-left:60px; font-size:12px" align="left">
                    <tr>
                        <td bgcolor="#FF3300" width="10"></td>
                        <td><? echo $header; ?></td>
                    </tr>
                </table>
                <canvas id="canvas8" height="400" width="850"></canvas>
            </div>
        </div>
    </div>
    <br />
<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=$company;
	
	$datediff=30; $today=date('Y-m-d');
	if($db_type==0)
	{
		$firstDate = date("Y-m-d", strtotime("-29 day", strtotime($today)));
		$lastDate = date("Y-m-d", strtotime($today));
	}
	else
	{
		$firstDate = date("d-M-Y", strtotime("-29 day", strtotime($today)));
		$lastDate = date("d-M-Y", strtotime($today));	
	}
	//echo $firstDate."".$lastDate;
	
	$sql_active_line=sql_select("select sewing_line,production_date,sum(production_quantity) as total_production from pro_garments_production_mst  where production_date between '$firstDate' and '$lastDate' and production_type=5 and company_id in($company) and status_active=1 and is_deleted=0 and prod_reso_allo=1 group by  sewing_line,production_date");
	$actual_line_arr=array();
	foreach($sql_active_line as $inf)
	{	
		if(str_replace("","",$inf[csf('sewing_line')])!="")
		{
			$production_date=date("Y-m-d", strtotime($inf[csf('production_date')]));
			$actual_line_arr[$production_date][]=$inf[csf('sewing_line')];
		}
	}
	ksort($actual_line_arr);
	//print_r($actual_line_arr['2015-10-11']);
	
	  
	$dataArray=sql_select("select a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour,b.pr_date from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company and b.pr_date between '$firstDate' and '$lastDate' and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour,b.pr_date order by  b.pr_date");
	$inactive_line_arr=array();
	$inactive_line_man_power_arr=array();   
	$line_count_arr=array();
	$line_arr=array();
	foreach( $dataArray as $row )
	{
		$pr_date=date("Y-m-d", strtotime($row[csf('pr_date')]));
		$line_arr[$pr_date]=$row[csf('id')];
		if(!in_array($row[csf('id')],$actual_line_arr[$pr_date]))
		{
			$inactive_line_arr[$pr_date][]=$row[csf('id')];
			$inactive_line_man_power_arr[$pr_date][]=$row[csf('man_power')];
		}
	}
	//print_r($inactive_line_arr['2015-10-11']);
	//print_r($inactive_line_man_power_arr['2015-10-11']);
	
	$sum_inactive_line_manpower=array();
	foreach( $inactive_line_man_power_arr as $key=>$val )
	{
		foreach( $val as $kk=>$vv )
		{
		 $sum_inactive_line_manpower[$key]+=$vv;
		}
	}
	//print_r($sum_inactive_line_manpower);
	
	$idle_lines_arr=array();
	$idle_rmg_worker_arr=array();
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date($firstDate,$j);
		$date_array[$j]=date("d-M", strtotime($newdate));
		$idle_lines_arr[]=count($inactive_line_arr[$newdate]);
		if($sum_inactive_line_manpower[$newdate]=="")
		{
			$idle_rmg_worker_arr[]=0;
		}
		else
		{
			$idle_rmg_worker_arr[]=$sum_inactive_line_manpower[$newdate];
		}
	} 
	//print_r($date_array);
	//print_r($idle_rmg_worker_arr);
   
	$date_array= json_encode($date_array); 
	$idle_lines_arr= json_encode($idle_lines_arr);
	$idle_rmg_worker_arr= json_encode($idle_rmg_worker_arr);
	?>
	<script>
	
	if(lnk=='30_days_idle_lines')
	{
		var lineChartData5 = {
		labels : <? echo $date_array; ?>,
			datasets : [
			{
				//label: "My First dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#FF3300",
				pointColor : "#FF3300",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#FF3300",
				data : <? echo $idle_lines_arr; ?>
			}
			
			]
		}
		window.onload = function(){
			var ctx = document.getElementById("canvas8").getContext("2d");
			window.myLine = new Chart(ctx).Line(lineChartData5, {
				responsive: true
			});
		}
	}
	else
	{
		var lineChartData6 = {
			labels : <? echo $date_array; ?>,
			datasets : [
			{
				//label: "My First dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#FF3300",
				pointColor : "#FF3300",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#FF3300",
				data : <? echo $idle_rmg_worker_arr; ?>
			}
			]
		}
		window.onload = function(){
			var ctx = document.getElementById("canvas8").getContext("2d");
			window.myLine = new Chart(ctx).Line(lineChartData6, {
			responsive: true
			});
		}
	}
	</script>
<?

function add_month($orgDate,$mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}

?>
        
     