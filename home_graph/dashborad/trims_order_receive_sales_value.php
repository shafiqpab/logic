<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	16-01-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../includes/common.php');
//echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);
//--------------------------------------------------------------------------------------------------------------------

?>	

<script src="../../Chart.js-master/Chart.min.js"></script>


	<?
        list($lcCompany,$location,$floor,$workingCompany)=explode('__',$_REQUEST['cp']);
		if($workingCompany){$company_cond=" and comp.id=$workingCompany";}
		else if($lcCompany){$company_cond=" and comp.id=$lcCompany";}
		
		
        $company_id=($workingCompany)?$workingCompany:$lcCompany;
		$sales_year_start_month=return_field_value("SALES_YEAR_STARTED","VARIABLE_ORDER_TRACKING", "VARIABLE_LIST=12 and COMPANY_NAME=$company_id","SALES_YEAR_STARTED");
		
		$conversion_rate=return_field_value("CONVERSION_RATE","CURRENCY_CONVERSION_RATE", "id=(select max(id) from CURRENCY_CONVERSION_RATE where CURRENCY=2)","CONVERSION_RATE");
		
		
		$current_date=date('Y-m-d',time());
		$current_month_number=date('m',time());
		
		if($current_month_number >= $sales_year_start_month){
			$start_date=date('Y',time())."-$sales_year_start_month-1";
		}
		else
		{
			$start_date=(date('Y')-1)."-$sales_year_start_month-1";
		}
		
		$end_month=add_month($start_date,11);
		$last_day_of_end_month = cal_days_in_month(CAL_GREGORIAN, date('m',strtotime($end_month)), date('Y',strtotime($end_month)));
		
		$end_date = date('Y',strtotime($end_month)).'-'.date('m',strtotime($end_month)).'-'.$last_day_of_end_month;
		
		 //echo $start_date;die;
		
		
		
		//Qtr date............................................................
		$qtrDataArr[1][add_month(date("Y-m-d",strtotime($start_date)),0)]=1;
		$qtrDataArr[1][add_month(date("Y-m-d",strtotime($start_date)),1)]=1;
		$qtrDataArr[1][add_month(date("Y-m-d",strtotime($start_date)),2)]=1;
		
		$qtrDataArr[2][add_month(date("Y-m-d",strtotime($start_date)),3)]=2;
		$qtrDataArr[2][add_month(date("Y-m-d",strtotime($start_date)),4)]=2;
		$qtrDataArr[2][add_month(date("Y-m-d",strtotime($start_date)),5)]=2;

		$qtrDataArr[3][add_month(date("Y-m-d",strtotime($start_date)),6)]=3;
		$qtrDataArr[3][add_month(date("Y-m-d",strtotime($start_date)),7)]=3;
		$qtrDataArr[3][add_month(date("Y-m-d",strtotime($start_date)),8)]=3;

		$qtrDataArr[4][add_month(date("Y-m-d",strtotime($start_date)),9)]=4;
		$qtrDataArr[4][add_month(date("Y-m-d",strtotime($start_date)),10)]=4;
		$qtrDataArr[4][add_month(date("Y-m-d",strtotime($start_date)),11)]=4;


		 for($i=1;$i<=4;$i++){
			
			 if($qtrDataArr[$i][date('Y-m-01',time())]){
				$qutKey = $qtrDataArr[$i][date('Y-m-01',time())];
					$currentQtrDataArr=array();
					foreach($qtrDataArr[$qutKey] as $month=>$qtr){
						$currentQtrDataArr[date('Y-m',strtotime($month))]=$qtr;
					}
				break; 
			 }
			 
		 }
		
		if($db_type==0)
		{
			$start_date=change_date_format($start_date,'yyyy-mm-dd');
			$end_date=change_date_format($end_date,'yyyy-mm-dd');
		}
		else
		{
			$start_date=change_date_format($start_date, "", "",1);
			$end_date=change_date_format($end_date, "", "",1);
		}
		
		
		
		
		//-----------------------------------------------------------
		
	$trims_order_rev_sql="select a.EXCHANGE_RATE,a.WITHIN_GROUP,a.CURRENCY_ID,a.RECEIVE_DATE,b.ID as ORDER_REV_DTSL_ID,b.SECTION,b.RATE,b.ORDER_QUANTITY,b.ORDER_UOM,b.AMOUNT from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b where a.id=b.MST_ID  and a.ENTRY_FORM=255 and a.COMPANY_ID=$company_id and a.RECEIVE_DATE between '$start_date' and '$end_date' and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 order by b.SECTION" ;
	
	//echo $trims_order_rev_sql;
	
	
	$trims_order_rev_sql_array=sql_select($trims_order_rev_sql);
	foreach($trims_order_rev_sql_array as $row)
	{  
		if($row[CURRENCY_ID]!=2){$row[AMOUNT]=($row[AMOUNT]/$conversion_rate);}
		
		$order_receiv_data_arr[$row[ORDER_REV_DTSL_ID]][RATE]=$row[RATE];
		$order_receiv_data_arr[$row[ORDER_REV_DTSL_ID]][EXCHANGE_RATE]=$row[EXCHANGE_RATE];
		$order_receiv_data_arr[$row[ORDER_REV_DTSL_ID]][CURRENCY_ID]=$row[CURRENCY_ID];
		
		if($current_date==date('Y-m-d',strtotime($row[RECEIVE_DATE]))){
			$receivDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['TODAY']+=$row[AMOUNT];
			$totalReceivDataArr[$row[WITHIN_GROUP]]['TODAY']+=$row[AMOUNT];
		}
		
		if(date('Y-m',strtotime($current_date))==date('Y-m',strtotime($row[RECEIVE_DATE]))){
			$receivDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['MTD']+=$row[AMOUNT];
			$totalReceivDataArr[$row[WITHIN_GROUP]]['MTD']+=$row[AMOUNT];
		}
		if($currentQtrDataArr[date('Y-m',strtotime($row[RECEIVE_DATE]))]){
			$receivDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['QTD']+=$row[AMOUNT];
			$totalReceivDataArr[$row[WITHIN_GROUP]]['QTD']+=$row[AMOUNT];
		}
		$receivDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['YTD']+=$row[AMOUNT];
		$totalReceivDataArr[$row[WITHIN_GROUP]]['YTD']+=$row[AMOUNT];
		
		$sectionArr[$row[WITHIN_GROUP]][$row[SECTION]]=$row[SECTION];
		
		//graph...............
		$graphSectionArr[$row[SECTION]]=$trims_section[$row[SECTION]];
	}
	//graph...............
	foreach($graphSectionArr as $secKey => $val){
		$graphReceivDataArr[1]['YTD'][$secKey]+=round($receivDataArr[1][$secKey]['YTD']);
		$graphReceivDataArr[2]['YTD'][$secKey]+=round($receivDataArr[2][$secKey]['YTD']);
	}
	
 		
	
	$trims_delivery_sql ="select a.WITHIN_GROUP,a.DELIVERY_DATE,a.CURRENCY_ID,b.SECTION,b.DELEVERY_QTY,b.RECEIVE_DTLS_ID,b.ORDER_RECEIVE_RATE from TRIMS_DELIVERY_MST a,TRIMS_DELIVERY_DTLS b where a.id=b.mst_id  and a.ENTRY_FORM=208 and a.COMPANY_ID=$company_id and a.DELIVERY_DATE between '$start_date' and '$end_date' and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 order by b.SECTION";
	
	$trims_delivery_sql_array=sql_select($trims_delivery_sql);
	foreach($trims_delivery_sql_array as $row)
	{  
		$row[AMOUNT]=$row[DELEVERY_QTY]*$row[ORDER_RECEIVE_RATE];
		
		if($row[CURRENCY_ID]!=2){$row[AMOUNT]=($row[AMOUNT]/$conversion_rate);}
		
		
		if($current_date==date('Y-m-d',strtotime($row[DELIVERY_DATE]))){
			$deliveryDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['TODAY']+=$row[AMOUNT];
			$totalDeliveryDataArr[$row[WITHIN_GROUP]]['TODAY']+=$row[AMOUNT];
		}
		
		if(date('Y-m',strtotime($current_date))==date('Y-m',strtotime($row[DELIVERY_DATE]))){
			$deliveryDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['MTD']+=$row[AMOUNT];
			$totalDeliveryDataArr[$row[WITHIN_GROUP]]['MTD']+=$row[AMOUNT];
		}
		if($currentQtrDataArr[date('Y-m',strtotime($row[DELIVERY_DATE]))]){
			$deliveryDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['QTD']+=$row[AMOUNT];
			$totalDeliveryDataArr[$row[WITHIN_GROUP]]['QTD']+=$row[AMOUNT];
		}
		$deliveryDataArr[$row[WITHIN_GROUP]][$row[SECTION]]['YTD']+=$row[AMOUNT];
		$totalDeliveryDataArr[$row[WITHIN_GROUP]]['YTD']+=$row[AMOUNT];
		
		$deliverySectionArr[$row[WITHIN_GROUP]][$row[SECTION]]=$row[SECTION];
		
		//graph...............
		$graphDeliverySectionArr[$row[SECTION]]=$trims_section[$row[SECTION]];
		
	}

	//graph...............
	foreach($graphDeliverySectionArr as $secKey => $val){
		$graphDeliveryDataArr[1]['YTD'][$secKey]+=round($deliveryDataArr[1][$secKey]['YTD']);
		$graphDeliveryDataArr[2]['YTD'][$secKey]+=round($deliveryDataArr[2][$secKey]['YTD']);
	}
	
	
	
	
	//Trims Rec.....................................................
	$htmlRecInternal="
    <table border='1' rules='all' cellpadding='2' width='100%'>
		<tr>
			<th colspan='5'>Order Intake (Internal)</th>
		</tr>
		<tr>
			<th colspan='5'>Order Value/$</th>
		</tr>
		<tr>
			<th>Section</th>
			<th>".date('d-M-Y',time())."</th>
			<th>MTD</th>
			<th>QTD</th>
			<th>YTD</th>
		</tr>
		";
	$internal=1;
	foreach($sectionArr[$internal] as $sectio_id){
	$htmlRecInternal.="<tr>
			<td>".$trims_section[$sectio_id]."</td>
			<td align='right'>". number_format($receivDataArr[$internal][$sectio_id][TODAY],0) ."</td>
			<td align='right'>". number_format($receivDataArr[$internal][$sectio_id][MTD],0) ."</td>
			<td align='right'>". number_format($receivDataArr[$internal][$sectio_id][QTD],0) ."</td>
			<td align='right'>". number_format($receivDataArr[$internal][$sectio_id][YTD],0) ."</td>
		</tr>";
	}
		
	$htmlRecInternal.="
		<tr bgcolor='#999999'>
			<td>Total</td>
			<td align='right'>". number_format($totalReceivDataArr[$internal][TODAY],0) ."</td>
			<td align='right'>". number_format($totalReceivDataArr[$internal][MTD],0) ."</td>
			<td align='right'>". number_format($totalReceivDataArr[$internal][QTD],0) ."</td>
			<td align='right'>". number_format($totalReceivDataArr[$internal][YTD],0) ."</td>
		</tr>		
		</table>
		";
		
	
	
	$htmlRecExternal="
    <table border='1' rules='all' cellpadding='2' width='100%'>
		<tr>
			<th colspan='5'>Order Intake (External)</th>
		</tr>
		<tr>
			<th colspan='5'>Order Value/$</th>
		</tr>
		<tr>
			<th>Section</th>
			<th>".date('d-M-Y',time())."</th>
			<th>MTD</th>
			<th>QTD</th>
			<th>YTD</th>
		</tr>
		";
	$external=2;
	foreach($sectionArr[$external] as $sectio_id){
	$htmlRecExternal.="<tr>
			<td>".$trims_section[$sectio_id]."</td>
			<td align='right'>". number_format($receivDataArr[$external][$sectio_id][TODAY],0) ."</td>
			<td align='right'>". number_format($receivDataArr[$external][$sectio_id][MTD],0) ."</td>
			<td align='right'>". number_format($receivDataArr[$external][$sectio_id][QTD],0) ."</td>
			<td align='right'>". number_format($receivDataArr[$external][$sectio_id][YTD],0) ."</td>
		</tr>";
	}
		
	$htmlRecExternal.="
		<tr bgcolor='#999999'>
			<td>Total</td>
			<td align='right'>". number_format($totalReceivDataArr[$external][TODAY],0) ."</td>
			<td align='right'>". number_format($totalReceivDataArr[$external][MTD],0) ."</td>
			<td align='right'>". number_format($totalReceivDataArr[$external][QTD],0) ."</td>
			<td align='right'>". number_format($totalReceivDataArr[$external][YTD],0) ."</td>
		</tr>		
		<tr bgcolor='#DDEBF7'>
			<td><b>Grand Total</b></td>
			<td align='right'><b>". number_format($totalReceivDataArr[$external][TODAY]+$totalReceivDataArr[$internal][TODAY],0) ."</b></td>
			<td align='right'><b>". number_format($totalReceivDataArr[$external][MTD]+$totalReceivDataArr[$internal][MTD],0) ."</b></td>
			<td align='right'><b>". number_format($totalReceivDataArr[$external][QTD]+$totalReceivDataArr[$internal][QTD],0) ."</b></td>
			<td align='right'><b>". number_format($totalReceivDataArr[$external][YTD]+$totalReceivDataArr[$internal][YTD],0) ."</b></td>
		</tr>		
		</table>";
		
		
		
		
		
	//Trims Delivery.....................................................
	$htmlDeliveryInternal="
    <table border='1' rules='all' cellpadding='2' width='100%'>
		<tr>
			<th colspan='5'>Sales (Internal)</th>
		</tr>
		<tr>
			<th colspan='5'>Sales Value/$</th>
		</tr>
		<tr>
			<th>Section</th>
			<th>".date('d-M-Y',time())."</th>
			<th>MTD</th>
			<th>QTD</th>
			<th>YTD</th>
		</tr>
		";
	$internal=1;
	foreach($deliverySectionArr[$internal] as $sectio_id){
	$htmlDeliveryInternal.="<tr>
			<td>".$trims_section[$sectio_id]."</td>
			<td align='right'>". number_format($deliveryDataArr[$internal][$sectio_id][TODAY],0) ."</td>
			<td align='right'>". number_format($deliveryDataArr[$internal][$sectio_id][MTD],0) ."</td>
			<td align='right'>". number_format($deliveryDataArr[$internal][$sectio_id][QTD],0) ."</td>
			<td align='right'>". number_format($deliveryDataArr[$internal][$sectio_id][YTD],0) ."</td>
		</tr>";
	}
		
	$htmlDeliveryInternal.="
		<tr bgcolor='#999999'>
			<td>Total</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$internal][TODAY],0) ."</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$internal][MTD],0) ."</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$internal][QTD],0) ."</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$internal][YTD],0) ."</td>
		</tr>		
		</table>
		";
		
	
	
	$htmlDeliveryExternal="
    <table border='1' rules='all' cellpadding='2' width='100%'>
		<tr>
			<th colspan='5'>Sales (External)</th>
		</tr>
		<tr>
			<th colspan='5'>Sales Value/$</th>
		</tr>
		<tr>
			<th>Section</th>
			<th>".date('d-M-Y',time())."</th>
			<th>MTD</th>
			<th>QTD</th>
			<th>YTD</th>
		</tr>
		";
	$external=2;
	foreach($deliverySectionArr[$external] as $sectio_id){
	$htmlDeliveryExternal.="<tr>
			<td>".$trims_section[$sectio_id]."</td>
			<td align='right'>". number_format($deliveryDataArr[$external][$sectio_id][TODAY],0) ."</td>
			<td align='right'>". number_format($deliveryDataArr[$external][$sectio_id][MTD],0) ."</td>
			<td align='right'>". number_format($deliveryDataArr[$external][$sectio_id][QTD],0) ."</td>
			<td align='right'>". number_format($deliveryDataArr[$external][$sectio_id][YTD],0) ."</td>
		</tr>";
	}
		
	$htmlDeliveryExternal.="
		<tr bgcolor='#999'>
			<td>Total</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$external][TODAY],0) ."</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$external][MTD],0) ."</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$external][QTD],0) ."</td>
			<td align='right'>". number_format($totalDeliveryDataArr[$external][YTD],0) ."</td>
		</tr>		
		<tr bgcolor='#DDEBF7'>
			<td><b>Grand Total</b></td>
			<td align='right'><b>". number_format($totalDeliveryDataArr[$external][TODAY]+$totalDeliveryDataArr[$internal][TODAY],0) ."</td>
			<td align='right'><b>". number_format($totalDeliveryDataArr[$external][MTD]+$totalDeliveryDataArr[$internal][MTD],0) ."</b></td>
			<td align='right'><b>". number_format($totalDeliveryDataArr[$external][QTD]+$totalDeliveryDataArr[$internal][QTD],0) ."</b></td>
			<td align='right'><b>". number_format($totalDeliveryDataArr[$external][YTD]+$totalDeliveryDataArr[$internal][YTD],0) ."</b></td>
		</tr>		
		</table>";
	
$company_library 	=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    ?>
 
<table cellspacing="5">
    
    <tr>
        <td align="center" colspan="2">
			<div style="font-size:25px;"><?= $company_library[$company_id]; ?></div>
            Trims Order Receive & Sales Value($)
        </td>   
    </tr>
    
    
    <tr>
        <td valign="top"><?= $htmlRecInternal; ?></td>   
        <td valign="top"><?= $htmlDeliveryInternal; ?></td>   
    </tr>
    <tr>
        <td valign="top"><?= $htmlRecExternal; ?></td>   
        <td valign="top"><?= $htmlDeliveryExternal; ?></td>   
    </tr>
</table>    
<u>Note:</u><br /> 
a) Current USD Conversion Rate:<?= $conversion_rate;?><br />
b) Sales Year Start Month:<?= $months[$sales_year_start_month];?><br /> 
 
		
<table cellspacing="5">
    <tr>
        <td>
           <canvas style="border:solid 1px; padding:3px;" id="canvas" height="250" width="400"></canvas>
        </td>   
        <td>
           <canvas style="border:solid 1px; padding:3px;" id="canvas2" height="250" width="400"></canvas>
        </td>   
    </tr>
</table>    
        
	<script>

		var line_bar_data1= {
			type: 'bar',
			data: {
			  labels: [<?= "'".implode("','",$graphSectionArr)."'";?>] ,
			  datasets: [{
				  label: ["Internal"],
				  type: "bar",
				  backgroundColor: "#FFA500",
				  data:[<?= implode(',',$graphReceivDataArr[1]['YTD']);?>] ,
				  fill: false
				},
				{
				  label: ["External"],
				  type: "bar",
				  backgroundColor: "#FF0000",
				  data:[<?= implode(',',$graphReceivDataArr[2]['YTD']);?>] ,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Yearly Internal VS External Receive Value USD'
			  },
			  legend: { display: true }
			}
		}		
		
		var line_bar_data2= {
			type: 'bar',
			data: {
			  labels: [<?= "'".implode("','",$graphDeliverySectionArr)."'";?>] ,
			  datasets: [{
				  label: ["Internal"],
				  type: "bar",
				  backgroundColor: "#FFA500",
				  data:[<?= implode(',',$graphDeliveryDataArr[1]['YTD']);?>] ,
				  fill: false
				},
				{
				  label: ["External"],
				  type: "bar",
				  backgroundColor: "#FF0000",
				  data:[<?= implode(',',$graphDeliveryDataArr[2]['YTD']);?>] ,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'Yearly Internal VS External Sales Value USD'
			  },
			  legend: { display: true }
			}
		}		
		
		
		 new Chart(document.getElementById("canvas"),line_bar_data1);
		 new Chart(document.getElementById("canvas2"),line_bar_data2);
    </script>
 
 
 
<?
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>

