<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
extract($_REQUEST);
if($print==1){
	$printLink="../../";
	include("../../includes/common.php");
	echo load_html_head_contents("Graph", "../../", "",'', '', '', 1);
	$width="60";
}
else
{
	$width="92";
}


 
//--------------------------------------------------------------------------------------------------------------------

?>	
<script src="<? echo $printLink;?>Chart.js-master/Chart.min.js"></script>





	<?
        list($lcCompany,$location,$floor,$workingCompany)=explode('__',$_REQUEST['cp']);
		if($workingCompany){$company_cond=" and c.company_name=$workingCompany";}
		else if($lcCompany){$company_cond=" and c.company_name=$lcCompany";}
		else{$company_cond="";}
		if($_SESSION['logic_erp'][company_id]!=''){$company_cond.=" and c.company_name in(".$_SESSION[logic_erp][company_id].")";}
		
		
		$month_arr=array();	
        $month_prev=add_month(date("Y-m-d",time()),-2);
        $month_next=add_month(date("Y-m-d",time()),9);
        
       //------------------------------------------------------

	   
		   
	if($_SESSION['logic_erp']["month_arr__"]=="")
	{
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		if($db_type==0)
        {
            $startDate = date("Y-m-d",strtotime($month_prev));
        }
        else
        {
            $startDate = date("d-M-Y", strtotime($month_prev));
        }
		 
        $sql="SELECT a.country_id,c.buyer_name,b.id as po_id,a.cutup_date,sum(a.order_quantity) AS qnty,c.product_category FROM wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.cutup_date >='$startDate' $company_cond group by a.country_id,c.buyer_name,b.id,a.cutup_date,c.product_category";
        $order_result=sql_select($sql);
        // echo $sql;die;
		$Outwears_orderQtyArr=array();
		$Lingerie_orderQtyArr=array();
		$orderIdArr=array();
		$Outwears_orderIdArr=array();
		$Lingerie_orderIdArr=array();
		foreach($order_result as $row)
		{ 
			$monthKey=date("M y",strtotime($row[csf('cutup_date')]));
			
			
			if($monthKey==date("M y",time())){
				if($row['PRODUCT_CATEGORY'] == 1){
					$buyerWisePoQtyArr[$row[csf('buyer_name')]]+=$row[csf('qnty')];
					$buyerWisePoArr[$row[csf('buyer_name')]][$row[csf('po_id')]]=$row[csf('po_id')];
					$poWisebuyerArr[$row[csf('po_id')]] = $row[csf('buyer_name')];
				}
				elseif($row['PRODUCT_CATEGORY'] == 2){
					$buyerWisePoQtyArr2[$row[csf('buyer_name')]]+=$row[csf('qnty')];
					$buyerWisePoArr2[$row[csf('buyer_name')]][$row[csf('po_id')]]=$row[csf('po_id')];
					$poWisebuyerArr2[$row[csf('po_id')]] = $row[csf('buyer_name')];
				}
				
			}
			
			$monthWisePoArr[$monthKey][$row[csf('country_id')]][$row[csf('po_id')]]=$row[csf('po_id')];

			if($row['PRODUCT_CATEGORY'] == 1){
				// $Outwears_orderQtyArr[$row[csf('po_id')]]['po_id']=$row[csf('qnty')];
				$Outwears_orderQtyArr[$monthKey]+=$row[csf('qnty')];
				$Outwears_orderIdArr[$row[csf('po_id')]] = $row[csf('po_id')];
			}
			elseif($row['PRODUCT_CATEGORY'] == 2){
				//$Lingerie_orderQtyArr[$row[csf('po_id')]]['po_id']=$row[csf('qnty')];

				$Lingerie_orderQtyArr[$monthKey]+=$row[csf('qnty')];
				//$Lingerie_orderQtyArr[$monthKey]+=$row[csf('qnty')];
				$Lingerie_orderIdArr[$row[csf('po_id')]] = $row[csf('po_id')];
			}

			// $orderIdArr[$row[csf('po_id')]]=$row[csf('po_id')];
			
			
			if(strtotime($maxDate) <= strtotime($row[csf('cutup_date')])){
				$maxDate=$row[csf('cutup_date')];
			}
		}

	//	print_r($Lingerie_orderQtyArr);die;
	 
		// Outwears
		$exFactoryQtyArr=array();
        $sql="select country_id,po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 ";
		
		$order_id_list_arr = array_chunk($Outwears_orderIdArr, 999);
		$p=1;
		foreach($order_id_list_arr as $order_id_arr)
		{
			if($p==1){$sql .=" and (po_break_down_id in(".implode(',',$order_id_arr).")";}
			else{$sql .=" or po_break_down_id in(".implode(',',$order_id_arr).")";}
			$p++;
		}
		$sql .=")";
		$sql .=" group by country_id,po_break_down_id";
		$exfactory_result=sql_select($sql);
		// echo $sql;
		foreach($exfactory_result as $row)
        {
            $exFactoryQtyArr[$row[csf('po_break_down_id')]]+=$row[csf('ex_factory_qnty')];
			$cuntryExFactoryQtyArr[$row[csf('country_id')]][$row[csf('po_break_down_id')]]+=$row[csf('ex_factory_qnty')];
        }


        // Lingerie
		$exLingerieQtyArr = array();
		$cuntryExLingerieQtyArr = array();
		$sql="select country_id,po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0";
		$order_id_list_arr = array_chunk($Lingerie_orderIdArr, 999);
		$p=1;
		foreach($order_id_list_arr as $order_id_arr)
		{
			if($p==1){$sql .=" and (po_break_down_id in(".implode(',',$order_id_arr).")";}
			else{$sql .=" or po_break_down_id in(".implode(',',$order_id_arr).")";}
			$p++;
		}
		$sql .=")";
		$sql .=" group by country_id,po_break_down_id";
		$exfactory_result=sql_select($sql);
		foreach($exfactory_result as $row)
        {
            $exLingerieQtyArr[$row[csf('po_break_down_id')]]+=$row[csf('ex_factory_qnty')];
			$cuntryExLingerieQtyArr[$row[csf('country_id')]][$row[csf('po_break_down_id')]]+=$row[csf('ex_factory_qnty')];
        }

		/*foreach($buyerWisePoArr as $buyer_id=>$poAr)
		{
			foreach($poAr as $po)
			{
				$buyerWiseExportQtyArr[$buyer_id]+=$exFactoryQtyArr[$po];
			}
		}
		*/
		//----------------------------------------------------------------------------------------
		$remain_months=datediff( "m",$month_prev,date("Y-m-d",strtotime($maxDate)));
	    for($e=0;$e<=$remain_months;$e++)
        {
            $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
            $month_arr[$e]=date("M y",strtotime($tmp));
        }
		   
        $order_qty_array=array();
        $order_lingerie_qty_array=array();
		$exFactory_qty_array=array();
		foreach($month_arr as $key=>$monthYear)
        {
            $order_qty_array[]=number_format($Outwears_orderQtyArr[$monthYear],0,'.','');
            $order_lingerie_qty_array[]=number_format($Lingerie_orderQtyArr[$monthYear],0,'.','');
           	
			$exFactory_qty=0;
			$exFactory_qty2=0;
			foreach($monthWisePoArr[$monthYear] as $country_id=>$country_data_arr){
				foreach($country_data_arr as $po){
					$exFactory_qty+=$cuntryExFactoryQtyArr[$country_id][$po];
					$exFactory_qty2+=$cuntryExLingerieQtyArr[$country_id][$po];
					 if($monthYear==date("M y",time())){
						$buyerWiseExportQtyArr[$poWisebuyerArr[$po]]+=$cuntryExFactoryQtyArr[$country_id][$po];
						$buyerWiseExportQtyArr[$poWisebuyerArr2[$po]]+=$cuntryExLingerieQtyArr[$country_id][$po];
					}
				}
			}
			
			$exFactory_qty_array[]=number_format($exFactory_qty,0,'.','');
			$exFactory_lingerie_qty[]=number_format($exFactory_qty2,0,'.','');
			$exFactoryQtyArr[$monthYear]=number_format($exFactory_qty,0,'.','');
			$exLingerieQtyArr[$monthYear]=number_format($exFactory_qty2,0,'.','');
		   
        }
		
	//	print_r($Lingerie_orderQtyArr);die;
		// var_dump($cuntryExFactoryQtyArr[16]);
			
		$_SESSION['logic_erp']["month_arr"] = $month_arr;
		$_SESSION['logic_erp']["order_qty"] = $order_qty_array;
		$_SESSION['logic_erp']["order_lingerie_qty_array"] = $order_lingerie_qty_array;
		$_SESSION['logic_erp']["exFactory_qty"] = $exFactory_qty_array;
		$_SESSION['logic_erp']["exFactory_lingerie_qty"] = $exFactory_lingerie_qty;
		 

		$_SESSION['logic_erp']["Outwears_orderQtyArr"] = $Outwears_orderQtyArr;
		$_SESSION['logic_erp']["exFactoryQtyArr"] = $exFactoryQtyArr;

		$_SESSION['logic_erp']["Lingerie_orderQtyArr"] = $Lingerie_orderQtyArr;
		$_SESSION['logic_erp']["exLingerieQtyArr"] = $exLingerieQtyArr;
		
		$_SESSION['logic_erp']["buyerWisePoQtyArr"] = $buyerWisePoQtyArr;
		$_SESSION['logic_erp']["buyer_arr"] = $buyer_arr;
		$_SESSION['logic_erp']["buyerWiseExportQtyArr"] = $buyerWiseExportQtyArr;
	}
	else
	{
		$month_arr = $_SESSION['logic_erp']["month_arr"];
		$order_qty_array = $_SESSION['logic_erp']["order_qty"];
		$_SESSION['logic_erp']["order_lingerie_qty_array"] = $order_lingerie_qty_array;
		$exFactory_qty_array = $_SESSION['logic_erp']["exFactory_qty"];
		$exFactory_lingerie_qty = $_SESSION['logic_erp']["exFactory_lingerie_qty"];
		$Outwears_orderQtyArr = $_SESSION['logic_erp']["Outwears_orderQtyArr"];
		$exFactoryQtyArr = $_SESSION['logic_erp']["exFactoryQtyArr"];
		$Lingerie_orderQtyArr = $_SESSION['logic_erp']["Lingerie_orderQtyArr"];
		$exLingerieQtyArr = $_SESSION['logic_erp']["exLingerieQtyArr"];
		$buyerWisePoQtyArr = $_SESSION['logic_erp']["buyerWisePoQtyArr"];
		$buyer_arr = $_SESSION['logic_erp']["buyer_arr"];
		$buyerWiseExportQtyArr = $_SESSION['logic_erp']["buyerWiseExportQtyArr"];
	}
	$monthArray= json_encode($month_arr); 
	$order_qty_array= json_encode($order_qty_array); 
	$order_lingerie_qty_array= json_encode($order_lingerie_qty_array); 
	$exFactory_qty_array= json_encode($exFactory_qty_array);
	$exFactory_lingerie_qty= json_encode($exFactory_lingerie_qty);
    ?>
	<div style="margin:10px;width:100%;">
        <div style="width:<? echo $width;?>%;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
            <canvas id="canvas"></canvas>
			<table id="canvas_info" class="orderExportData" border="1" rules="all">
				<tr bgcolor="#D5D9D8">
					<th colspan="2"></th>
					<? foreach($month_arr as $month){echo "<th>" . $month . "</th>";} ?>
				</tr>
				<tr>
					<th rowspan="2">Outwears</th>
					<th>Order</th>
					<?
					$total_outwears_order = array();
					foreach($month_arr as $month)
					{
					    echo "<td align='right'>" . $Outwears_orderQtyArr[$month]."</td>";
					    $total_outwears_order[$month] += $Outwears_orderQtyArr[$month];
					}
					?>
				</tr>
				<tr>
					<th>Export</th>
					<?
					$total_outwears_export = array();
					foreach($month_arr as $month)
					{
						echo "<td align='right'>" . $exFactoryQtyArr[$month]."</td>";
						$total_outwears_export[$month] += $exFactoryQtyArr[$month];
					} 
					?>
				</tr>
				<tr>
					<th rowspan="2">Lingerie</th>
					<th>Order</th>
					<?
					$total_lingerie_order = array();
					foreach($month_arr as $month)
					{
						echo "<td align='right'>" . $Lingerie_orderQtyArr[$month]."</td>";
						$total_lingerie_order[$month] += $Lingerie_orderQtyArr[$month];
					} 
					?>
				</tr>
				<tr>
					<th>Export</th>
					<?
					$total_lingerie_export = array();
					foreach($month_arr as $month)
					{
						echo "<td align='right'>" . $exLingerieQtyArr[$month]."</td>";
						$total_lingerie_export[$month] += $exLingerieQtyArr[$month];
					} 
					?>
				</tr>

		 

				<tr>
					<th rowspan="2">Total</th>
					<th>Order</th>
					<?
					foreach($month_arr as $month)
					{
						$total = $total_outwears_order[$month]+$total_lingerie_order[$month];
						echo "<th align='right'>".$total."</th>";
					} 
					?>
				</tr>
				<tr>
					<th>Export</th>
					<?
					foreach($month_arr as $month)
					{
						$total = $total_outwears_export[$month]+$total_lingerie_export[$month];
						echo "<th align='right'>".$total."</th>";
					} 
					?>
				</tr>
			</table>
        </div>
        <a href="home_graph/dashborad/monthly_order_export.php?print=1" target="_blank"><img src="img/print.jpg" height="20" alt="" /></a>
        <? if($print==1){?>
        <div style="width:35%; padding:10px; float:left;">
        
        <table class="orderExportData"  border="1" rules="all" width="80%" align="center">
            <tr>
                <td width="100">Generate Date:</td>
                <td> <? echo date("Y-m-d",time());?></td>
                <td align="right" style="border:none" rowspan="2"></td>
            </tr>
            <tr>
                <td>Generate Time:</td>
                <td> <? echo date("h:i:s a",time());?></td>
            </tr>
        </table>    
   
       <b>&nbsp; &nbsp; TOTAL EXPORT OF CURRENT MONT</b>
        <table class="orderExportData"  border="1" rules="all" width="80%" align="center">
            <tr>
                <th width="100">Month</th>
                <th>Total Order</th>
                <th>Total Export</th>
            </tr>
            <tr>
                <td align="center"> <? echo date("M-Y",time());?></td>
                <td align="right"><? echo array_sum($buyerWisePoQtyArr);?></td>
                <td align="right"><? echo array_sum($buyerWiseExportQtyArr);?></td>
            </tr>
        </table>    
   
        <b>&nbsp; &nbsp; EXPORT PROGRESS OF CURRENT MONTH'S ORDERS</b>
        <table id="buyerOrderExportData" class="orderExportData" border="1" rules="all" width="75%" align="center">
            <tr bgcolor="#D5D9D8">
                <th>Buyer</th>
                <th width="80">Order Qty</th>
                <th width="80">Export Qty</th>
                <th width="70">Export Progress (%)</th>
            </tr>
            
            <? foreach($buyerWisePoQtyArr as $buyer_id=>$poQty){?>
             <tr>
                <td><? echo $buyer_arr[$buyer_id];?></td>
                <td align="right"><? echo $poQty;?></td>
                <td align="right"><? echo $buyerWiseExportQtyArr[$buyer_id];?></td>
                <td align="right"><? echo number_format(($buyerWiseExportQtyArr[$buyer_id]*100)/$poQty,2);?></td>
            </tr>
           <? } ?>
            
		</table>        
        </div>
        <? } ?>
	</div> 
    <script>
		var line_bar_data= {
			type: 'bar',
			data: {
			  labels:<? echo $monthArray;?>,
			  datasets: [{
				  label: "Outwears Order",
				  type: "bar",
				  backgroundColor: "#70AD47", // 2E75B6
				  data:<? echo $order_qty_array;?>,
				  fill: false
				},
				{
				  label: "Outwears Export",
				  type: "bar",
				  backgroundColor: "#5B9BD5", // 9DDE58
				  data:<? echo $exFactory_qty_array;?>,
				  fill: false
				},
				{
				  label: "Lingerie Order",
				  type: "bar",
				  backgroundColor: "#FFC266",
				  data:<? echo $order_lingerie_qty_array;?>,
				  fill: false
				},
				{
				  label: "Lingerie Export",
				  type: "bar",
				  backgroundColor: "#006600",
				  data:<? echo $exFactory_lingerie_qty;?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'MONTHLY ORDER AND EXPORT'
			  },
			  legend: { display: true }
			}
		}  
  
  
  
		new Chart(document.getElementById("canvas"),line_bar_data);
    </script>
 
<style>
.orderExportData{margin:5px 0 20px 10px;}
.orderExportData td , .orderExportData th {border:1px solid #000;}
#canvas{width:100%!important;}
#canvas_info{width:95%!important; font-size:10px!important;}


#buyerOrderExportData{width:95%!important; font-size:12px!important;}



</style> 
 
<?
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>

