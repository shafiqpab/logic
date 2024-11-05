<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	09-09-2019
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
	$width="96";
}


 
//--------------------------------------------------------------------------------------------------------------------

?>	
<script src="<? echo $printLink;?>Chart.js-master/Chart.min.js"></script>

<?
        list($lcCompany,$location,$floor,$workingCompany)=explode('__',$_REQUEST['cp']);
		if($workingCompany){$company_cond=" and c.company_name=$workingCompany";}
		else if($lcCompany){$company_cond=" and c.company_name=$lcCompany";}
		else{$company_cond="";}
		
		if($_SESSION[logic_erp][company_id]!=''){$company_cond.=" and c.company_name in(".$_SESSION[logic_erp][company_id].")";}
		
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
		
           $sql="SELECT a.country_id,c.buyer_name,b.id as po_id,a.cutup_date,sum(a.order_quantity) AS qnty,sum(a.order_total) AS order_value from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.cutup_date >='$startDate' $company_cond group by a.country_id,c.buyer_name,b.id,a.cutup_date";
          $order_result=sql_select($sql);
              //echo $sql;die;
			
			$orderQtyArr=array();$orderValArr=array();$orderIdArr=array();
            foreach($order_result as $row)
            { 
				$monthKey=date("M y",strtotime($row[csf('cutup_date')]));
				$orderQtyArr[$monthKey]+=$row[csf('qnty')];
				$orderValArr[$monthKey]+=$row[csf('order_value')];
				
				if($monthKey==date("M y",time())){
					$buyerWisePoQtyArr[$row[csf('buyer_name')]]+=$row[csf('qnty')];
					$buyerWisePoValArr[$row[csf('buyer_name')]]+=$row[csf('order_value')];
					
					$buyerWisePoArr[$row[csf('buyer_name')]][$row[csf('po_id')]]=$row[csf('po_id')];
					$poWisebuyerArr[$row[csf('po_id')]]=$row[csf('buyer_name')];
				}
				
				$monthWisePoArr[$monthKey][$row[csf('country_id')]][$row[csf('po_id')]]=$row[csf('po_id')];
				$orderIdArr[$row[csf('po_id')]]=$row[csf('po_id')];
				
				if(strtotime($maxDate) <= strtotime($row[csf('cutup_date')])){
					$maxDate=$row[csf('cutup_date')];
				}
            }
			
			
            
		//----------------------------------------------------------------------------------------
		$remain_months=datediff( "m",$month_prev,date("Y-m-d",strtotime($maxDate)));
	    for($e=0;$e<=$remain_months;$e++)
        {
            $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
            $month_arr[$e]=date("M y",strtotime($tmp));
        }
		   
        $order_qty_array=array();$order_val_array=array();
		foreach($month_arr as $key=>$monthYear)
        {
            $order_qty_array[]=number_format($orderQtyArr[$monthYear],0,'.','');
            $order_val_array[]=number_format($orderValArr[$monthYear],0,'.','');
        }
		
		
		
		$_SESSION['logic_erp']["month_arr"]=$month_arr;	
		$_SESSION['logic_erp']["order_qty"]=$order_qty_array;	
		$_SESSION['logic_erp']["order_val"]=$order_val_array;
		$_SESSION['logic_erp']["orderQtyArr"]=$orderQtyArr;	
		$_SESSION['logic_erp']["orderValArr"]=$orderValArr;
		
		$_SESSION['logic_erp']["buyerWisePoQtyArr"]=$buyerWisePoQtyArr;
		$_SESSION['logic_erp']["buyer_arr"]=$buyer_arr;
		$_SESSION['logic_erp']["buyerWisePoValArr"]=$buyerWisePoValArr;
	
	}
	else
	{
		$month_arr=$_SESSION['logic_erp']["month_arr"];	
		$order_qty_array=$_SESSION['logic_erp']["order_qty"];	
		$order_val_array=$_SESSION['logic_erp']["order_val"];
		
		$orderQtyArr=$_SESSION['logic_erp']["orderQtyArr"];	
		$orderValArr=$_SESSION['logic_erp']["orderValArr"];
		$buyerWisePoQtyArr=$_SESSION['logic_erp']["buyerWisePoQtyArr"];
		$buyer_arr=$_SESSION['logic_erp']["buyer_arr"];
		$buyerWisePoValArr=$_SESSION['logic_erp']["buyerWisePoValArr"];
	}
	
	$monthArray= json_encode($month_arr); 
	$order_qty_array= json_encode($order_qty_array); 
	$order_val_array= json_encode($order_val_array); 
	   

    ?>
    
 
    
	<div style="margin:10px;width:100%;" >
    
        <div style="width:<? echo $width;?>%;float:left; overflow:hidden; margin-left:10px; margin-bottom:10px; border:solid 1px;">
            <canvas id="canvas"></canvas>
		
            <table id="canvas_info" class="orderExportData" border="1" rules="all">
                <tr bgcolor="#D5D9D8">
                    <th width="50"></th>
                    <? foreach($month_arr as $month){echo "<th>" . $month . "</th>";} ?>
                </tr>
                <tr>
                    <td>Order</td>
                    <? foreach($month_arr as $month){echo "<td align='right'>" . $orderQtyArr[$month] . "&nbsp;</td>";} ?>
                </tr>
                <tr>
                    <td>Value</td>
                    <? foreach($month_arr as $month){echo "<td align='right'>" . number_format($orderValArr[$month],2) . "&nbsp;</td>";} ?>
                </tr>
            </table>
        
        
        </div>
        <? if($print!=1){ ?>
        <a href="home_graph/dashborad/monthly_order_value.php?print=1" target="_blank"><img src="img/print.jpg" height="20" alt="" /></a>
        <? } ?>
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
   
       <b>&nbsp; &nbsp; TOTAL ORDER VALUE OF CURRENT MONT</b>
        <table class="orderExportData"  border="1" rules="all" width="80%" align="center">
            <tr>
                <th width="100">Month</th>
                <th>Total Order</th>
                <th>Total Value</th>
            </tr>
            <tr>
                <td align="center"> <? echo date("M-Y",time());?></td>
                <td align="right"><? echo array_sum($buyerWisePoQtyArr);?></td>
                <td align="right"><? echo number_format(array_sum($buyerWisePoValArr),2);?></td>
            </tr>
        </table>    
   
        
        
        
        <b>&nbsp; &nbsp; ORDER VALUE OF CURRENT MONTH'S ORDERS</b>
        <table id="buyerOrderExportData" class="orderExportData" border="1" rules="all" width="80%" align="center">
            <tr bgcolor="#D5D9D8">
                <th>Buyer</th>
                <th width="80">Order Qty</th>
                <th width="80">Order Val</th>
            </tr>
            
            <? foreach($buyerWisePoQtyArr as $buyer_id=>$poQty){?>
             <tr>
                <td><? echo $buyer_arr[$buyer_id];?></td>
                <td align="right"><? echo $poQty;?></td>
                <td align="right"><? echo number_format($buyerWisePoValArr[$buyer_id],2);?></td>
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
				  label: "ORDER QTY",
				  type: "bar",
				  backgroundColor: "#2E75B6",
				  data:<? echo $order_qty_array;?>,
				  fill: false
				},
				{
				  label: "ORDER VAL",
				  type: "bar",
				  backgroundColor: "#9DDE58",
				  data:<? echo $order_val_array;?>,
				  fill: false
				}
			  ]
			},
			options: {
			  title: {
				display: true,
				text: 'MONTHLY ORDER AND VALUE'
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


#buyerOrderExportData{font-size:12px!important;}



</style> 
 
<?
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>

