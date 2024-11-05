<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Last 30_days_idle_knit_mchn and 30_days_idle_dyen_mchn
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	13.10.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');

echo load_html_head_contents("30 Days Idle knit days mchn Graph", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];
$floor=$cps[2];
$pro_company=$cps[3];

if($m=="30_days_idle_knit_mchn")
{
	$caption="30 days Idle knit Machine";
	$header="Idle knit Machine";
}
else
{
	$caption="30 days Idle Dyeing Machine";	
	$header="Idle Dyeing Machine";
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
            <div style="width:950px; height:450px;  position:relative; margin-left:10px; margin-top:5px; border:solid 1px">
                <div align="center" style="width:100%; font-size:16px;"><? echo $caption; ?></div>
                <div align="center" style="width:100%; font-size:14px;"><? echo " Company : ". $comp_arr[$company?$company:$pro_company]; ?></div>
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
	//echo $firstDate."".$lastDate;die;
	
	if($pro_company){$companyCon="and company_id=$pro_company";}else{$companyCon="and company_id=$company";}
	$sql_active_mchin=sql_select("select id, machine_no, category_id FROM lib_machine_name where category_id in (1,2) $companyCon and status_active=1 and is_deleted=0 order by id");
	$active_machin_arr=array();
	foreach($sql_active_mchin as $inf)
	{	
		$active_machin_arr[$inf[csf('category_id')]][]=$inf[csf('id')];
	}
	//print_r($active_machin_arr[1]);
	
	
	if($pro_company){$companyCon="and a.knitting_company=$pro_company";}else{$companyCon="and a.company_id=$company";}

	$dataArray=sql_select("select a.receive_date,b.machine_no_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id $companyCon and a.receive_date between '$firstDate' and '$lastDate' and a.entry_form=2 and a.item_category=13 and b.machine_no_id!=0 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.receive_date,b.machine_no_id order by a.receive_date,b.machine_no_id");
	$knit_machin_arr=array();
	foreach( $dataArray as $row )
	{
		$receive_date=date("Y-m-d", strtotime($row[csf('receive_date')]));
		$knit_machin_arr[$receive_date][]=$row[csf('machine_no_id')];
	}
	//print_r($knit_machin_arr);
	
	if($pro_company){$companyCon="and service_company=$pro_company";}else{$companyCon="and company_id=$company";}
	
	$dataArraydyen=sql_select("select process_end_date,machine_id from pro_fab_subprocess where process_end_date between '$firstDate' and '$lastDate' $companyCon and entry_form=35 and load_unload_id=1 and machine_id!=0 and status_active=1 and is_deleted=0  group by process_end_date,machine_id order by process_end_date,machine_id");
	$dyen_machin_arr=array();
	foreach( $dataArraydyen as $row_info )
	{
		$dyen_date=date("Y-m-d", strtotime($row_info[csf('process_end_date')]));
		$dyen_machin_arr[$dyen_date][]=$row_info[csf('machine_id')];
	}
	//print_r($dyen_machin_arr);
	
	$idle_knit_arr=array();
	$idle_dyen_arr=array();
	for($j=0;$j<$datediff;$j++)
	{
		$cdate =add_date($firstDate,$j);
		if(count($knit_machin_arr[$cdate])==0)
		{
			$knit_machin_arr[$cdate]=array();	
		}
		
		if(count($dyen_machin_arr[$cdate])==0)
		{
			$dyen_machin_arr[$cdate]=array();	
		}
		$idle_knit_arr[$cdate]=array_diff($active_machin_arr[1],$knit_machin_arr[$cdate]);
		$idle_dyen_arr[$cdate]=array_diff($active_machin_arr[1],$dyen_machin_arr[$cdate]);
	}
	//print_r($idle_knit_arr['2015-10-13']);
	
	$idle_knit_machin_arr=array();
	$idle_dyen_machin_arr=array();
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date($firstDate,$j);
		$date_array[$j]=date("d-M", strtotime($newdate));
		
		$idle_knit_machin_arr[]=count($idle_knit_arr[$newdate]);
		$idle_dyen_machin_arr[]=count($idle_dyen_arr[$newdate]);
	} 
	//print_r($date_array);
   
	$date_array= json_encode($date_array); 
	$idle_knit_machin_arr= json_encode($idle_knit_machin_arr);
	$idle_dyen_machin_arr= json_encode($idle_dyen_machin_arr);
		
		
	?>
	<script>
	
	if(lnk=='30_days_idle_knit_mchn')
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
				data : <? echo $idle_knit_machin_arr; ?>
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
				data : <? echo $idle_dyen_machin_arr; ?>
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
        
     