<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create order_forecast_graph.
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	16.09.2015	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');

echo load_html_head_contents("Order Forecasting Graph", "", "", $popup, $unicode, $multi_select, 1);
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);
$date_data_arr=explode("**",$ddate);
//print_r($date_data_arr);
$company=$cps[0];
$location=$cps[1];

if($m=="sales_forecast_value")
{
	$caption="Sales Forecast Value";	
}
else
{
	$caption="Sales Forecast Qnty";	
}

if($company!=0)
{
	$company=$company;
}
else
{
	$company="";
	$caption="Please Select Company Name";
}

if($location!="") $location_cond= "and a.location_name=$location "; else $location_cond="";

$company_name=$company;
$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team", "id","team_leader_name");
$buyer_name_arr=return_library_array("select id,buyer_name from lib_buyer", "id","buyer_name");
$process = array( &$_POST );
extract(check_magic_quote_gpc( $process ));
//--------------------------------------------------------------------------------------------------------------------

?>
	<script>
    	var lnk='<? echo $m; ?>';
    </script>
    <script src="Chart.js-master/Chart.js"></script>
<?

$g=1;
$buyer_tem_arr=array();
$date_arr=array();
foreach($date_data_arr as $date_data)
{
	$date_data_exp=explode("*",$date_data);
	$start_date=date("Y-m-d",strtotime($date_data_exp[0]));
	$end_date=date("Y-m-d",strtotime($date_data_exp[1]));
	$team_leader=$date_data_exp[2];
	$agent_name=$date_data_exp[3];
	$buyer_name=$date_data_exp[4];
	
	if($db_type==0) 
	{
		$date_cond_sales=" and b.sales_target_date between '$start_date' and '$end_date'";
		$date_cond_order=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	if($db_type==2) 
	{
		$date_cond_sales=" and b.sales_target_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$date_cond_order=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
	}
	//echo $date_cond_order;
	
	if($team_leader==0)
	{
		$team_leader_cond="";
	}
	else
	{
		$team_leader_cond=" and a.team_leader=$team_leader";
	}
	
	if($agent_name==0)
	{
	 	$agent_cond="";
	 	$agent_cond_order="";
	}
	else
	{
		$agent_cond=" and a.agent=$agent_name";
		$agent_cond_order=" and a.agent_name=$agent_name";
	}
	
	if($buyer_name==0)
	{
		$buyer_cond="";
		$buyer_cond_2="";
	}
	else
	{
		$buyer_cond=" and a.buyer_id=$buyer_name";
		$buyer_cond_2=" and a.buyer_name=$buyer_name";
	}
	
	//echo "select a.buyer_name,a.agent_name,a.team_leader, sum(b.po_quantity*a.total_set_qnty) as po_quantity,b.pub_shipment_date, sum(b.po_total_price/a.total_set_qnty) as amount,b.is_confirmed from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$company_name' $date_cond_order $buyer_cond_2 $agent_cond_order $team_leader_cond GROUP BY b.is_confirmed,a.buyer_name,a.agent_name,a.team_leader,b.pub_shipment_date order by a.buyer_name";
	
	$sql_order= sql_select("select a.buyer_name,a.agent_name,a.team_leader, sum(b.po_quantity*a.total_set_qnty) as po_quantity,b.pub_shipment_date, sum(b.po_total_price/a.total_set_qnty) as amount,b.is_confirmed from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name='$company_name' $date_cond_order $buyer_cond_2 $agent_cond_order $team_leader_cond GROUP BY b.is_confirmed,a.buyer_name,a.agent_name,a.team_leader,b.pub_shipment_date order by a.buyer_name");
	$order_row=count($sql_order); 
	foreach ($sql_order as $row)
	{
		$key=$row[csf("buyer_name")].$row[csf("agent_name")];
		if($row[csf("is_confirmed")]==1)
		{
			$order_data_arr[$g][date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$key]['confirmqty']+=$row[csf("po_quantity")];
			$order_data_arr[$g][date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$key]['confirmamount']+=$row[csf("amount")];
		}
		else
		{
			$order_data_arr[$g][date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$key]['projectqty']+=$row[csf("po_quantity")];
			$order_data_arr[$g][date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$key]['projectamount']+=$row[csf("amount")];
		}
		$buyer_tem_arr[$g][$key]=$row[csf("buyer_name")];
		$date_arr[$g][date("Y-m",strtotime($row[csf("pub_shipment_date")]))]=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
	}
	
	//echo "select a.buyer_id,a.agent,a.team_leader, b.sales_target_date ,a.agent,b.sales_target_qty as sales_target_qty,b.sales_target_value from wo_sales_target_mst  a,wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_name  $date_cond_sales $buyer_cond $agent_cond $team_leader_cond order by a.buyer_id";
	
	$sql_sales=sql_select("select a.buyer_id,a.agent,a.team_leader, b.sales_target_date ,a.agent,b.sales_target_qty as sales_target_qty,b.sales_target_value from wo_sales_target_mst  a,wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_name  $date_cond_sales $buyer_cond $agent_cond $team_leader_cond order by a.buyer_id");
	$sales_row=count($sql_sales);
	//$sale_data_arr=array();
	foreach($sql_sales as $row)
	{
		$key=$row[csf("buyer_id")].$row[csf("agent")];
		$sale_data_arr[$g][date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['target_qty']+=$row[csf("sales_target_qty")];
		$sale_data_arr[$g][date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['target_val']+=$row[csf("sales_target_value")];
		
		$date_arr[$g][date("Y-m",strtotime($row[csf("sales_target_date")]))]=date("Y-m",strtotime($row[csf("sales_target_date")]));
		$buyer_tem_arr[$g][$key]=$row[csf("buyer_id")];
	} 
	asort($date_arr[$g]);
	asort($buyer_tem_arr[$g]);
	$g++;	
}
	
$month_array=array();
$tot_sales_qnty_val_month=array();
$tot_projectamount_month=array();
$tot_confirmamount_month=array();

$tot_sales_qty_month=array();
$tot_projectqty_month=array();
$tot_confirmqty_month=array();

$sales_qnty=array();
$projectqty=array();
$confirmqty=array();
$sales_qnty_val=array();
$projectamount=array();
$confirmamount=array();
$k=1;


foreach($date_data_arr as $date_data)
{
	 foreach($buyer_tem_arr[$k] as $key=>$buyer_id)
	 {
		foreach($date_arr[$k] as $month_id=>$result)
		{
			$month_array[$k][$month_id]=$month_id;
			
			$sales_qnty[$k]=$sale_data_arr[$k][$month_id][$key]['target_qty'];
			$sales_qnty_val[$k]=$sale_data_arr[$k][$month_id][$key]['target_val'];
			$tot_sales_qty_month[$k][$month_id]+=$sales_qnty[$k];	
			$tot_sales_qnty_val_month[$k][$month_id]+=$sales_qnty_val[$k];	
			
			$projectqty[$k]=$order_data_arr[$k][$month_id][$key]['projectqty'];
			$projectamount[$k]=$order_data_arr[$k][$month_id][$key]['projectamount'];
			$tot_projectqty_month[$k][$month_id]+=$projectqty[$k];	
			$tot_projectamount_month[$k][$month_id]+=$projectamount[$k];
			
			$confirmqty[$k]=$order_data_arr[$k][$month_id][$key]['confirmqty'];
			$confirmamount[$k]=$order_data_arr[$k][$month_id][$key]['confirmamount'];
			$tot_confirmqty_month[$k][$month_id]+=$confirmqty[$k];	
			$tot_confirmamount_month[$k][$month_id]+=$confirmamount[$k];
		}
	 }
	ksort($month_array[$k]); 
	ksort($tot_sales_qty_month[$k]);
	ksort($tot_projectqty_month[$k]);
	ksort($tot_confirmqty_month[$k]);
	
	ksort($tot_sales_qnty_val_month[$k]);
	ksort($tot_projectamount_month[$k]);
	ksort($tot_confirmamount_month[$k]);
	$k++;
}
//print_r($month_array);
//print_r($tot_sales_qty_month);

$month_arr=array();
$order_forecasting_qnty=array();
$tot_project_confirm_qnty=array();

$order_forecasting_value=array();
$tot_project_confirm_amount=array();
$k=1;
foreach($date_data_arr as $date_data)
{
	
	foreach($month_array[$k] as $month=>$res)
	{
		$month_arr[$k][]=date("M",strtotime($month))." '".date("y",strtotime($month));
		
		$order_forecasting_qnty[$k][]=$tot_sales_qty_month[$k][$month];
		$tot_project_confirm_qnty[$k][]=($tot_projectqty_month[$k][$month]+$tot_confirmqty_month[$k][$month]);
		
		$order_forecasting_value[$k][]=$tot_sales_qnty_val_month[$k][$month];
		$tot_project_confirm_amount[$k][]=($tot_projectamount_month[$k][$month]+$tot_confirmamount_month[$k][$month]);
	}
$k++;
}

/*print_r($month_arr);

echo "<br>";

print_r($order_forecasting_value);

echo "<br>";

print_r($tot_project_confirm_amount);*/

?>
<div align="center" style="width:100%;">
<div style="margin-left:30px; margin-top:10px">
<!--<a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<br />&nbsp;&nbsp;-->
</div>
<?

$h=1;
foreach($date_data_arr as $date_data)
{
	if (empty($month_arr[$h]))//blank chk
	{
		echo "<font size=+2 color='#FF80FF'>No Data Found</font>";
		$month_arr[$h]=array();
		$order_forecasting_qnty[$h]=array();
		$tot_project_confirm_qnty[$h]=array();
		
		$order_forecasting_value[$h]=array();
		$tot_project_confirm_amount[$h]=array();
	}
	
	$exp_Date=explode("*",$date_data);
	$team_leader=$exp_Date[2];
	$agent_name=$exp_Date[3];
	$buyer_name=$exp_Date[4];
	
	$month_arr_show= json_encode($month_arr[$h]); 
	$order_forecasting_qnty_show= json_encode($order_forecasting_qnty[$h]);
	$tot_project_confirm_qnty_show= json_encode($tot_project_confirm_qnty[$h]);
	
	$order_forecasting_value_show= json_encode($order_forecasting_value[$h]);
	$tot_project_confirm_amount_show= json_encode($tot_project_confirm_amount[$h]);
	
	
	?>
  
    <div align="center" style="width:950px; height:500px;  margin-left:20px; border:solid 1px">
        <div align="center" style="width:100%; font-size:16px;"><? echo $caption." - ".date("M-y",strtotime($exp_Date[0])) . " TO " .date("M-y",strtotime($exp_Date[1])); ?></div>
        <div align="center" style="width:100%; font-size:14px;"><? echo " <b>Company </b>: ". $comp_arr[$company]; if($team_leader!=0) echo " , <b> Team Leader </b> : ".$team_leader_arr[$team_leader]; if($agent_name!=0) echo " , <b> Agent Name </b> : ".$buyer_name_arr[$agent_name]; if($buyer_name!=0) echo " , <b> Buyer Name </b>: ".$buyer_name_arr[$buyer_name];?></div>
        <table style="margin-left:60px; font-size:12px" align="left">
        <tr>
            <td align="left" bgcolor="red" width="10"></td>
            <td>Forecast</td>
            <td align="left" bgcolor="green" width="10"></td>
            <td>Projection+Confirm</td>
        </tr>
        </table>
        <canvas id="canvas<? echo $h; ?>" height="400" width="900"></canvas>
    </div>
    <br />   
  
    
    <script>
	if(lnk=='sales_forecast_qnty')
	{		
		var barChartData = {
        labels : <? echo $month_arr_show; ?>,
        datasets : [
            {
                fillColor : "red",
                strokeColor : "rgba(220,220,220,0.8)",
                highlightFill: "rgb(255,99,71)",
                highlightStroke: "rgba(220,220,220,1)",
                data : <? echo $order_forecasting_qnty_show; ?>
            },
			{
				fillColor : "green",
				strokeColor : "rgba(151,187,205,0.8)",
				highlightFill : "rgb(50,205,50)",
				highlightStroke : "rgba(151,187,205,1)",
				data : <? echo $tot_project_confirm_qnty_show; ?>
			}
        ]
        }
		
		//window.onload = function(){	
			var ctx = document.getElementById("canvas<? echo $h; ?>").getContext("2d");
			window.myBar = new Chart(ctx).Bar(barChartData, {
			responsive : true
			});
		//}
	}
	if(lnk=='sales_forecast_value')
	{		
		var barChartData = {
        labels : <? echo $month_arr_show; ?>,
        datasets : [
            {
                fillColor : "red",
                strokeColor : "rgba(220,220,220,0.8)",
                highlightFill: "rgb(255,99,71)",
                highlightStroke: "rgba(220,220,220,1)",
                data : <? echo $order_forecasting_value_show; ?>
            },
			{
				fillColor : "green",
				strokeColor : "rgba(151,187,205,0.8)",
				highlightFill : "rgb(50,205,50)",
				highlightStroke : "rgba(151,187,205,1)",
				data : <? echo $tot_project_confirm_amount_show; ?>
			}
        ]
        }
		
		//window.onload = function(){	
			var ctx = document.getElementById("canvas<? echo $h; ?>").getContext("2d");
			window.myBar = new Chart(ctx).Bar(barChartData, {
			responsive : true
			});
		//}
	}
	</script>
    
    <?
	$h++;
}


?>
</div>

        
     