<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Ex-Factory vs Commercial Activities for home page
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	24.11.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
extract($_REQUEST);
include('../includes/common.php');

echo load_html_head_contents("Ex-Factory vs Commercial Activities", "../", "",1, $unicode, $multi_select, 1);

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= $cbo_company_name;
	$location= $cbo_location_id;
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$buyer_full_name_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$f_date=$txt_year.'-01-01';
	$t_date=$txt_year.'-12-31';
	
	$from_date=date('Y-m-d', strtotime($f_date));
	$to_date=date('Y-m-d', strtotime($t_date));
	
	if($db_type==0)
	{
		$firstDate = date("Y-m-d", strtotime($from_date));
		$lastDate = date("Y-m-d", strtotime($to_date));
	}
	else
	{
		$firstDate = date("d-M-Y",strtotime($from_date));
		$lastDate = date("d-M-Y", strtotime($to_date));	
	}
	
	//echo $firstDate."==".$lastDate."==";
	
	$ex_factory_arr=return_library_array( "select po_break_down_id, sum(ex_factory_qnty) as qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "qnty");	
	
	$invoice_arr=return_library_array( "select po_breakdown_id, sum(current_invoice_value) as invoice_value from com_export_invoice_ship_dtls where status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "invoice_value");
	
	$submission_arr=return_library_array( "select a.po_breakdown_id, sum(a.current_invoice_value) as submission_value from com_export_invoice_ship_dtls a, com_export_doc_submission_invo b , com_export_doc_submission_mst c where a.mst_id=b.invoice_id and b.doc_submission_mst_id=c.id and c.entry_form=40 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_breakdown_id ", "po_breakdown_id", "submission_value");
	
	$realize_arr=return_library_array( "select a.po_breakdown_id, sum(a.current_invoice_value) as realize_value from com_export_invoice_ship_dtls a, com_export_doc_submission_invo b , com_export_proceed_realization c where a.mst_id=b.invoice_id and b.doc_submission_mst_id=c.invoice_bill_id and c.is_invoice_bill=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.po_breakdown_id ", "po_breakdown_id", "realize_value");	
	
	if($location!="" and $location!=0) $location_cond= "and a.location_name=$location "; else $location_cond="";
	$sql="select a.buyer_name, a.job_no_prefix_num, a.job_no, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.pub_shipment_date, b.unit_price,b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and b.pub_shipment_date between '$firstDate' and '$lastDate' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $location_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";	
	$nameArray=sql_select($sql);
	//and b.shiping_status!=3
	
	$buyer_wise_sum_arr=array();
	$month_wise_sum_arr=array();
	foreach($nameArray as $row)
	{
		$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
		$ex_factory_qty=$ex_factory_arr[$row[csf('po_id')]];
		$ex_factory_value=$ex_factory_qty*$unit_price;
		$invoice_value=$invoice_arr[$row[csf('po_id')]];
		$submission_value=$submission_arr[$row[csf('po_id')]];
		$document_in_hand_value=($invoice_value-$submission_value);
		$realize_value=$realize_arr[$row[csf('po_id')]];
		$un_realize_value=($submission_value-$realize_value);
		
		if(round($ex_factory_value)>0 || round($invoice_value)>0 || round($submission_value)>0 || round($realize_value)>0)
		{
			$buyer_wise_sum_arr[$row[csf('buyer_name')]][ex_factory_value]+=$ex_factory_value;
			$buyer_wise_sum_arr[$row[csf('buyer_name')]][invoice_value]+=$invoice_value;
			$buyer_wise_sum_arr[$row[csf('buyer_name')]][submission_value]+=$submission_value;
			$buyer_wise_sum_arr[$row[csf('buyer_name')]][realize_value]+=$realize_value;
			
			$shipment_date=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
			$month_wise_sum_arr[$shipment_date][$row[csf('buyer_name')]][ex_factory_value]+=$ex_factory_value;
			$month_wise_sum_arr[$shipment_date][$row[csf('buyer_name')]][invoice_value]+=$invoice_value;
			$month_wise_sum_arr[$shipment_date][$row[csf('buyer_name')]][submission_value]+=$submission_value;
			$month_wise_sum_arr[$shipment_date][$row[csf('buyer_name')]][realize_value]+=$realize_value;
		}
	}
	?>
	<style>
    hr
    {
    color:#666;
    }
    </style>
    <div align="center"> <h3 style="top:1px;width:98%; text-align:center;" id="accordion_h1" class="accordion_h" align="center"> Ex-Factory vs Commercial Activities </h3></div>
    <br />
    
    <div align="center" style="width:1100px; height:auto; margin-left:40px; ">
         <div style="float:left; height:auto; width:600px; padding-bottom:20px;">
            <table width="580" class="rpt_table" border="1" rules="all" align="left">
            <thead>
                <tr>
                    <th colspan="6">Total Summary</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="150">Buyer Name</th>
                    <th width="100">Ex-Factory Amount</th>
                    <th width="100">Invoice Amount</th>
                    <th width="100">Submitted Amount</th>
                    <th width="100">Realize Amount</th>
                </tr>
            </thead>
           
				<?
                $d=1;
				$buyer_sum_ex_factory=0;
				$buyer_sum_invoice=0;
				$buyer_sum_submission=0;
				$buyer_sum_realize=0;
                foreach( $buyer_wise_sum_arr as $buyer_id=>$value)
                {
                    if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $d; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $d; ?>">
                        <td width="30"><? echo $d; ?></td>
                        <td width="150"><? echo $buyer_full_name_library[$buyer_id]; ?></td>
                        <td width="100" align="right"><?  echo number_format($value[ex_factory_value],2); $buyer_sum_ex_factory +=$value[ex_factory_value];?></td>
                        <td width="100" align="right"><?  echo number_format($value[invoice_value],2); $buyer_sum_invoice+=$value[invoice_value]; ?></td>
                        <td width="100" align="right"><?  echo number_format($value[submission_value],2); $buyer_sum_submission+=$value[submission_value]; ?></td>
                        <td width="100" align="right"><?  echo number_format($value[realize_value],2); $buyer_sum_realize+=$value[realize_value]; ?></td>
                    </tr>
                    <?
                    $d++;
                }
				$buyer_graph_cat_arr=array(0=>"Ex-Factory Amount",1=>"Invoice Amount",2=>"Submitted Amount",3=>"Realize Amount");
				$ex_factory_graph_arr[]=round($buyer_sum_ex_factory,2);
				$invoice_graph_arr[]=round($buyer_sum_invoice,2);
				$submission_graph_arr[]=round($buyer_sum_submission,2);
				$realize_graph_arr[]=round($buyer_sum_realize,2);
				$buyer_graph_val_arr=array(0=>$ex_factory_graph_arr,1=>$invoice_graph_arr,2=>$submission_graph_arr,3=>$realize_graph_arr);
				
				$buyer_graph_cat_arr= json_encode($buyer_graph_cat_arr);
				$buyer_graph_val_arr= json_encode($buyer_graph_val_arr);
				
                ?>
           
                <tfoot>
                    <tr>
                    <td width="30">&nbsp;  </td>
                    <td width="150" align="center">Total</td>
                    <td width="100" align="right"><? echo number_format($buyer_sum_ex_factory,2); ?></td>
                    <td width="100" align="right"><? echo number_format($buyer_sum_invoice,2); ?></td>
                    <td width="100" align="right"><? echo number_format($buyer_sum_submission,2); ?></td>
                    <td width="100" align="right"><? echo number_format($buyer_sum_realize,2); ?></td>
                    </tr>
                </tfoot>
            </table>
  		 </div>
         <div style="float:right; width:490px;  border:solid 1px;">
             <canvas id="canvas_buyer" style="width:450px; "></canvas>
        </div>
    </div>
    
    
    <div align="center" style="width:1060px; height:auto; margin-left:40px;">
		<?
		$month_wise_ex_factory_graph=array();
		$month_wise_invoice_graph=array();
		$month_wise_submission_graph=array();
		$month_wise_realize_graph=array();
        $s=0;
		$kh=0;
        foreach( $month_wise_sum_arr as $month_id=>$buyer_arr)
        {
			$kh++;
			
			?>
            <div align="center" style="width:1060px; height:auto; float:left; padding-top:20px; ">
            <?
            if($kh==1)
			{
				?>
				<div align="center" style="margin-left:80px; padding-bottom:20px;"> 
				<h3 style="top:1px; width:95%; text-align:center;" id="accordion_h1" class="accordion_h" align="center"> Month Wise Summary </h3>
				</div>
            	<?
			}
			?>
                <div style="width:460px; float:left; margin-left:80px;">
                    <table width="450" class="rpt_table" border="1" rules="all" align="center">
                    <thead>
                        <tr>
                            <th colspan="6" align="center" style="font-size:16px;"> Summary - <? echo $month_name=date("F",strtotime($month_id)).", ".date("Y",strtotime($month_id)); ?> </th>
                        </tr>
                        <tr>
                            <th width="30">SL</th>
                            <th width="100">Buyer Name</th>
                            <th width="80">Ex-Factory Amount</th>
                            <th width="80">Invoice Amount</th>
                            <th width="80">Submitted Amount</th>
                            <th width="80">Realize Amount</th>
                        </tr>
                    </thead>
                        <?
                        $d=0;
                        $tot_month_ex_factory=array();
                        $tot_month_invoice=array();
                        $tot_month_submission=array();
                        $tot_month_realize=array();
                        foreach( $buyer_arr as $buyer_id=>$value)
                        {
                            $d++;
                            if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td><? echo $d; ?></td>
                                <td><? echo $buyer_short_name_library[$buyer_id]; ?></td>
                                <td align="right"><? echo number_format($value[ex_factory_value],2); $tot_month_ex_factory[$month_id]+=$value[ex_factory_value]; ?></td>
                                <td align="right"><? echo number_format($value[invoice_value],2); $tot_month_invoice[$month_id]+=$value[invoice_value]; ?></td>
                                <td align="right"><? echo number_format($value[submission_value],2); $tot_month_submission[$month_id]+=$value[submission_value]; ?></td>
                                <td align="right"><? echo number_format($value[realize_value],2); $tot_month_realize[$month_id]+=$value[realize_value]; ?></td>
                            </tr>
                            <?
                        }
						$month_wise_ex_factory_graph[$month_id]=round($tot_month_ex_factory[$month_id],2);
						$month_wise_invoice_graph[$month_id]=round($tot_month_invoice[$month_id],2);
						$month_wise_submission_graph[$month_id]=round($tot_month_submission[$month_id],2);
						$month_wise_realize_graph[$month_id]=round($tot_month_realize[$month_id],2);
                        ?>
                    <tfoot>
                        <tr>
                            <th colspan="2" align="right">Total</th>
                            <th align="right"><? echo number_format($tot_month_ex_factory[$month_id],2); ?></th>
                            <th align="right"><? echo number_format($tot_month_invoice[$month_id],2); ?></th>
                            <th align="right"><? echo number_format($tot_month_submission[$month_id],2); ?></th>
                            <th align="right"><? echo number_format($tot_month_realize[$month_id],2); ?></th>													
                        </tr>
                    </tfoot>
                    </table>
                </div>
                <div  style="width:490px; float:right; border:solid 1px;">
                	<canvas id="canvas_<? echo $month_id; ?>"  width="450"></canvas>
                </div>
            </div>
			<?
			$s++;
        }
        ?>
    </div>
    
    
    <div  style="float:left; margin-top:50px; margin-left:100px;"> <!--master report-->
     <fieldset style="width:1068px;">
     	<table align="left" class="rpt_table" border="1" rules="all" width="1050px" cellpadding="0" cellspacing="0" id="table_header_1">
            <thead>
                 <tr>
                    <td  colspan="11" align="center"><h2>Ex-Factory vs Commercial Activities</h2></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="150">Buyer Name <br><hr style="width:100%; border:1px dotted;">Job Number <br><hr style="width:100%; border:1px dotted">Order Number</th>
                    <th width="100">Order Amount</th>
                    <th width="70">Shipment Date</th>
                    <th width="100">Ex-Factory Qty</th>
                    <th width="100">Ex-Factory Amount</th>
                    <th width="100">Invoice Amount</th>
                    <th width="100">Submitted Amount</th>
                    <th width="100">Doc. In Hand Amount</th>
                    <th width="100">Realize Amount</th>
                    <th width="100">Un Realize Amount</th>
                </tr>
            </thead>
        </table>
        <div style="width:1068px; overflow-y:scroll; max-height:400px" id="scroll_body">
            <table width="1050px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$tot_order_amount=0;
				$tot_ex_factory_qty=0;
				$tot_ex_factory_value=0;
				$tot_invoice_value=0;
				$tot_submission_value=0;
				$tot_document_in_hand_value=0;
				$tot_realize_value=0;
				$tot_un_realize_value=0;
				
				$i=0;
                foreach($nameArray as $row)
                {
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$ex_factory_qty=$ex_factory_arr[$row[csf('po_id')]];
					$ex_factory_value=$ex_factory_qty*$unit_price;
					$invoice_value=$invoice_arr[$row[csf('po_id')]];
					$submission_value=$submission_arr[$row[csf('po_id')]];
					$document_in_hand_value=($invoice_value-$submission_value);
					$realize_value=$realize_arr[$row[csf('po_id')]];
					$un_realize_value=($submission_value-$realize_value);
					
					if(round($ex_factory_value)>0 || round($invoice_value)>0 || round($submission_value)>0 || round($realize_value)>0)
					{
						$i++;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" valign="middle" ><? echo $i; ?></td>
							<td width="150" align="center" valign="middle">
							<p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?> </p>
							<br>
							<p><hr style="width:100%; border:1px dotted"></p>
							<p><? echo $row[csf('job_no')];  ?></p>
							<br>
							<p><hr style="width:100%; border:1px dotted"></p>
							<p><? echo $row[csf('po_number')]; ?></p>
							</td>
							<td width="100" align="right"><p><? echo number_format($row[csf('po_total_price')],0); ?></p></td>
							<td width="70" align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($ex_factory_qty,0); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($ex_factory_value,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($invoice_value,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($submission_value,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($document_in_hand_value,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($realize_value,2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($un_realize_value,2); ?></p></td>
						</tr>
						<?
						$tot_order_amount+=$row[csf('po_total_price')];
						$tot_ex_factory_qty+=$ex_factory_qty;
						$tot_ex_factory_value+=$ex_factory_value;
						$tot_invoice_value+=$invoice_value;
						$tot_submission_value+=$submission_value;
						$tot_document_in_hand_value+=$document_in_hand_value;
						$tot_realize_value+=$realize_value;
						$tot_un_realize_value+=$un_realize_value;
					}
                }
                ?>
            </table>
        </div>
        <table align="left" width="1050px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <tr>
                    <th width="30"></th>
                    <th width="150" align="center">Total</th>
                    <th width="100" align="right"><p><? echo number_format($tot_order_amount,0); ?></th>
                    <th width="70"> </th>
                    <th width="100" align="right"><p><? echo number_format($tot_ex_factory_qty,0); ?></th>
                    <th width="100" align="right"><p><? echo number_format($tot_ex_factory_value,2); ?></th>
                    <th width="100" align="right"><p><? echo number_format($tot_invoice_value,2); ?></th>
                    <th width="100" align="right"><p><? echo number_format($tot_submission_value,2); ?></th>
                    <th width="100" align="right"><p><? echo number_format($tot_document_in_hand_value,2); ?></th>
                    <th width="100" align="right"><p><? echo number_format($tot_realize_value,2); ?></th>
                    <th width="100" align="right"><p><? echo number_format($tot_un_realize_value,2); ?></th>
                </tr>
            </tfoot>
        </table>
     </fieldset>
     <br />
    </div>
    
    <script src="../Chart.js-master/Chart.js"></script>
	<script>
		
		var barChartData = {
		labels : <? echo $buyer_graph_cat_arr; ?>,
			datasets : [
				{
					fillColor : "yellowgreen",
					//strokeColor : "rgba(240,255,240)",
					highlightFill: "rgb(173, 255, 47)",
					//highlightStroke: "rgba(240,255,240)",
					data : <? echo $buyer_graph_val_arr; ?>
				}
			]
		}
		
		var ctx = document.getElementById("canvas_buyer").getContext("2d");
		window.myBar = new Chart(ctx).Bar(barChartData, {
		responsive : true
		});
		
	</script>
    <?
	$month_ex_factory_graph_arr=array();
	$month_invoice_graph_arr=array();
	$month_submission_graph_arr=array();
	$month_realize_graph_arr=array();
	foreach($month_wise_ex_factory_graph as $key=>$val)
	{
		$month_ex_factory_graph_arr[]=$month_wise_ex_factory_graph[$key];
		$month_invoice_graph_arr[]=$month_wise_invoice_graph[$key];
		$month_submission_graph_arr[]=$month_wise_submission_graph[$key];
		$month_realize_graph_arr[]=$month_wise_realize_graph[$key];
		$month_graph_val_arr=array(0=>$month_ex_factory_graph_arr,1=>$month_invoice_graph_arr,2=>$month_submission_graph_arr,3=>$month_realize_graph_arr);
		$month_graph_val_arr_show= json_encode($month_graph_val_arr);
		$m_id=date("m", strtotime($key));
		?>
			<script>
            var barChartData<? echo $m_id; ?> = {
            labels : <? echo $buyer_graph_cat_arr; ?>,
                datasets : [
                    {
                        fillColor : "yellowgreen",
                        //strokeColor : "rgba(240,255,240)",
                        highlightFill: "rgb(173, 255, 47)",
                        //highlightStroke: "rgba(240,255,240)",
                        data : <? echo $month_graph_val_arr_show; ?>
                    }
                ]
            }
            
            var ctx<? echo $m_id; ?> = document.getElementById("canvas_<? echo $key; ?>").getContext("2d");
            window.myBar = new Chart(ctx<? echo $m_id; ?>).Bar(barChartData<? echo $m_id; ?>, {
           		responsive : true
            });
            
            </script>
        <?
		$month_ex_factory_graph_arr=array();
		$month_invoice_graph_arr=array();
		$month_submission_graph_arr=array();
		$month_realize_graph_arr=array();
	}
exit();	
}

?>