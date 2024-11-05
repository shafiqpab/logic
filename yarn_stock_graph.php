<? 
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Graph", "", "", 1, $unicode, $multi_select, '');
?>
<link rel="stylesheet" href="home_css/styles.css">

<script>
	var permission = '<? echo $permission; ?>';
	var comp="";
	var locat="";
	var lnk="";
		
</script>
<?
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	
if($_SESSION['logic_erp']["data___"]=="" || $empty=="")//session off
{
		
$company_id=$cbo_company_name;	
//------------------------------------
?>
<table width="100%">	
    <tr><td align="center"><h1><? echo $company_library[$company_id];?></h1></td></tr>
</table>

<?	
		$issue_array=array();
		$sql_issue="select a.prod_id,
			sum(case when a.transaction_type in (2,3) then a.cons_quantity else 0 end) as issue_total_opening,
			sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 then a.cons_quantity else 0 end) as issue_inside,
			sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5  then a.cons_quantity else 0 end) as issue_outside,
			sum(case when a.transaction_type=3 and c.entry_form=8  then a.cons_quantity else 0 end) as rcv_return,
			sum(case when a.transaction_type=2 and c.issue_purpose=5  then a.cons_quantity else 0 end) as issue_loan		
			from inv_transaction a, inv_issue_master c
			where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.prod_id";
		$result_sql_issue=sql_select($sql_issue);
		foreach($result_sql_issue as $row)
		{
			//$issue_array[$row[csf("prod_id")]]['issue_total_opening']=$row[csf("issue_total_opening")];
			$issue_array[$row[csf("prod_id")]]['issue_inside']=$row[csf("issue_inside")];
			$issue_array[$row[csf("prod_id")]]['issue_outside']=$row[csf("issue_outside")];
			$issue_array[$row[csf("prod_id")]]['rcv_return']=$row[csf("rcv_return")];
			$issue_array[$row[csf("prod_id")]]['issue_loan']=$row[csf("issue_loan")];
		}
		unset($result_sql_issue);
						
		$receive_array=array();
		$sql_receive="Select a.prod_id,  max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
			sum(case when a.transaction_type in (1,4)  then a.cons_quantity else 0 end) as rcv_total_opening,
			
			sum(case when a.transaction_type in (1) and c.receive_purpose<>5  then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type in (1) and c.receive_purpose=5  then a.cons_quantity else 0 end) as rcv_loan,
			sum(case when a.transaction_type=4 and c.knitting_source=1  then a.cons_quantity else 0 end) as rcv_inside_return,
			sum(case when a.transaction_type=4 and c.knitting_source!=1 then a.cons_quantity else 0 end) as rcv_outside_return 
			from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id group by a.prod_id";
		$result_sql_receive = sql_select($sql_receive);
		foreach($result_sql_receive as $row)
		{
			//$receive_array[$row[csf("prod_id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
			$receive_array[$row[csf("prod_id")]]['purchase']=$row[csf("purchase")];
			$receive_array[$row[csf("prod_id")]]['rcv_loan']=$row[csf("rcv_loan")];
			$receive_array[$row[csf("prod_id")]]['rcv_inside_return']=$row[csf("rcv_inside_return")];
			$receive_array[$row[csf("prod_id")]]['rcv_outside_return']=$row[csf("rcv_outside_return")];
		}
		unset($result_sql_receive);
						
		$transfer_qty_array=array();
		$sql_transfer="select a.prod_id,
			sum(case when a.transaction_type=6  then a.cons_quantity else 0 end) as trans_out_total_opening,
			sum(case when a.transaction_type=5  then a.cons_quantity else 0 end) as trans_in_total_opening,
			sum(case when a.transaction_type=6  then a.cons_quantity else 0 end) as transfer_out_qty,
			sum(case when a.transaction_type=5  then a.cons_quantity else 0 end) as transfer_in_qty 
			from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.transfer_criteria=1 and c.is_deleted=0 group by a.prod_id";
		$result_sql_transfer = sql_select($sql_transfer);
		foreach($result_sql_transfer as $transRow)
		{
			//$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty']=$transRow[csf("transfer_out_qty")];
			//$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty']=$transRow[csf("transfer_in_qty")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening']=$transRow[csf("trans_out_total_opening")];
			$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening']=$transRow[csf("trans_in_total_opening")];
		} 
						
		$sql="select a.id,d.yarn_count, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit 
		from product_details_master a, inv_transaction b, inv_receive_master c,lib_yarn_count d
		where a.id=b.prod_id and b.mst_id=c.id and a.yarn_count_id=d.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 and a.company_id=$company_id group by a.id, a.company_id, a.supplier_id, a.yarn_count_id,d.yarn_count, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by d.yarn_count desc";
	 
	$result = sql_select($sql);	
	foreach($result as $row)
	{
		$transfer_in_qty=$transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
		$transfer_out_qty=$transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];
		
		$trans_out_total_opening=$transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
		$trans_in_total_opening=$transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];
		
		$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening']+$trans_in_total_opening)-($issue_array[$row[csf("id")]]['issue_total_opening']+$trans_out_total_opening);
		
		$totalRcv = $receive_array[$row[csf("id")]]['purchase']+$receive_array[$row[csf("id")]]['rcv_inside_return']+$receive_array[$row[csf("id")]]['rcv_outside_return']+$receive_array[$row[csf("id")]]['rcv_loan']+$transfer_in_qty;
		$totalIssue = $issue_array[$row[csf("id")]]['issue_inside']+$issue_array[$row[csf("id")]]['issue_outside']+$issue_array[$row[csf("id")]]['rcv_return']+$issue_array[$row[csf("id")]]['issue_loan']+$transfer_out_qty;
		
		$stockInHand=$openingBalance+$totalRcv-$totalIssue;
		if($stockInHand>1){$count_stock_arr[$row[csf("yarn_count")]]+=$stockInHand;}
	}

$_SESSION['logic_erp']["data"]=$count_stock_arr;
}
else
{
$count_stock_arr=$_SESSION['logic_erp']["data"];
}
	
	
	
	$chart_data="[";$countHTML="<tr><th>Count</th>";$stockHTML="<tr><th>Stock</th>";
	foreach($count_stock_arr as $count_name=>$stock_qty){
		if($chart_data=="["){$chart_data.="{ name: '$count_name',y: $stock_qty}";}
		else{$chart_data.=", { name: '$count_name',y: $stock_qty}";}
		$countHTML.="<th>$count_name</th>";$stockHTML.="<td align='right'>$stock_qty</td>";
	}
	$chart_data.="]";$countHTML.="</tr>";$stockHTML.="</tr>";	
	$tableHTML="<table class='rpt_table' border='1' rules='all'><thead>".$countHTML.'</thead><tbody>'.$stockHTML."</tbody></table>";
?>


<script src="ext_resource/hschart/hschart.js"></script>
<script type="text/javascript">
$(function () {
    // Create the chart
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Count Wise Yarn Stock'
        },
	    subtitle: {
            text: ''
        },
		
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Yarn Stock Qty (KG)'
            }

        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: false,
                    format: '{point.y:.1f} KG'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f} KG</b><br/>'
        },
        series: [{
            name: '<? echo $company_library[$company_id];?>',
            colorByPoint: true,
            data:eval(<? echo $chart_data;?>)
        }],
    });
});

</script>

<script src="includes/functions_bottom.js" type="text/javascript"></script>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<div style="margin:0 5px;"><? echo $tableHTML;?></div>