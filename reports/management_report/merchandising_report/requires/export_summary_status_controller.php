<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{  
	echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);

    $buyer_arr=return_library_array("select id,short_name from lib_buyer","id","short_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    if($reportType==1)
    {
        // =========================== MAKING QUERY COND ============================
        if($company_name!=0){ $search_cond .=" and a.company_id= $company_name";}
        if($buyer_name!=0){	$search_cond =" and a.buyer_id = $buyer_name" ;}
       // echo "<br>".$buyer_name; //die;	
        if($date_from!="" && $date_to!="")
        {
            if($db_type==0)
            {
                $date_cond = "and b.ex_factory_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; 
                $date_cond_1 = "and e.ex_factory_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; 
            }
            if($db_type==2)
            {
                $date_cond = "and b.ex_factory_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                $date_cond_1 = "and e.ex_factory_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
            }
        }

        // =============================================== MAIN QUERY =========================================
        $sql = "SELECT a.buyer_id as BUYER_ID, b.invoice_no as INVOICE_ID, sum(b.ex_factory_qnty*(c.unit_price/d.total_set_qnty)) as EX_FACT_VAL, sum(b.ex_factory_qnty) as ex_factory_qnty 
        from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c, wo_po_details_master d
        where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_id=d.id and a.entry_form !=85 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $search_cond $date_cond 
        group by a.buyer_id, b.invoice_no";
         //echo $sql; //die;	
        
        $sql_res = sql_select($sql);
        $main_array=$invoice_id_all= array();
        foreach($sql_res as $row)
        {
            if($row['INVOICE_ID']){$invoice_id_all[$row['INVOICE_ID']]=$row['INVOICE_ID'];}
            $main_array[$row['BUYER_ID']]['buyer_id']=$row['BUYER_ID'];
            $main_array[$row['BUYER_ID']]['ex_factory_val_cln']+=$row['EX_FACT_VAL'];
            $main_array[$row['BUYER_ID']]['EX_FACTORY_QNTY']+=$row['EX_FACTORY_QNTY'];
        }

        $invoice_id_in=where_con_using_array($invoice_id_all,0,'e.id');
        $invoice_sql = "SELECT sum(b.ex_factory_qnty*(c.unit_price/d.total_set_qnty)) as EX_FACT_VAL, e.id as INVOICE_ID, e.buyer_id, e.invoice_value as INVOICE_VALUE, e.net_invo_value as NET_INVO_VALUE
        from pro_ex_factory_mst b, wo_po_break_down c, wo_po_details_master d, com_export_invoice_ship_mst e
        where b.po_break_down_id=c.id and c.job_id=d.id and b.invoice_no=e.id and b.entry_form !=85 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $invoice_id_in $date_cond_1 
        group by e.id,e.buyer_id,e.invoice_value,e.net_invo_value";
        // echo $invoice_sql;die;

        $invoice_sql_res = sql_select($invoice_sql);
        foreach ($invoice_sql_res as $row) 
        {
            $main_array[$row['BUYER_ID']]['ex_factory_val_com']+=$row['EX_FACT_VAL'];
            $main_array[$row['BUYER_ID']]['invoice_value']+=$row['INVOICE_VALUE'];
            $main_array[$row['BUYER_ID']]['net_invo_value']+=$row['NET_INVO_VALUE'];
        }

        ob_start();			    				
        ?>
        <style>
            .wrd_brk{word-break: break-all;}
            .center{text-align: center;}
            .right{text-align: right;}
        </style>

        <div style="width:700x;">
            <div style="width:700px" >
                <table width="700"  cellspacing="0"  align="center">
                    <tr>
                        <td class="center" width="700">
                            <strong style="font-size:20px;"><? echo $company_library[$company_name]; ?> </strong>
                        </td>
                    </tr>
                    <tr >
                        <td class="center" > <strong style="font-size:16px;">Export Summary Status</strong></td>
                    </tr>
                    <tr >
                        <td class="center" > <strong style="font-size:14px;">Date Range: <?=date("d M, Y",strtotime($date_from)).' to '.date("d M, Y",strtotime($date_to));?> Report Generate: <? echo date("h:i:s a",time());?></strong></td>
                    </tr>
                </table>
            </div>
            <br />
            <div>
                <!-- ===================================== START DETAILS PART ===================================== -->
                <table width="650" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                    <thead>
                        <th width="150">Buyer Name</th>
                        <th width="100">Exfactory Qty[pcs] challan</th>
                        <th width="100">Ex-Factory Value ($) (Challan)</th>
                        <th width="100">Ex-Factory Value ($) (Commercial)</th>
                        <th width="100">Invoice Value ($)</th>
                        <th >Net Invoice Value ($)</th>
                    </thead>
                    <tbody>
                        <?
                        $chart_line_arr=$chart_ex_factory_val_cln_arr=$chart_ex_factory_val_com_arr=$chart_invoice_value_arr=$chart_net_invo_value_arr=array();
                        $i=1;
                        foreach($main_array as $buyer_id=>$row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="150" class="wrd_brk"><? echo $buyer_arr[$buyer_id]; ?></td>
                                <td width="100" class="right"><? echo number_format($row['EX_FACTORY_QNTY'],2); ?></td>
                                <td width="100" class="right"><? echo number_format($row['ex_factory_val_cln'],2); ?></td>
                                <td width="100" class="right"><? echo number_format($row['ex_factory_val_com'],2); ?></td>
                                <td width="100" class="right"><? echo number_format($row['invoice_value'],2);?></td>
                                <td class="right"><? echo number_format($row['net_invo_value'],2);?></td>
                            </tr>
                            <?
                            $i++;
                            $total_ex_factory_qnty+=$row['EX_FACTORY_QNTY'];
                            $total_ex_factory_val_cln+=$row['ex_factory_val_cln'];
                            $total_ex_factory_val_com+=$row['ex_factory_val_com'];
                            $total_invoice_value+=$row['invoice_value'];
                            $total_net_invo_value+=$row['net_invo_value'];

                            $chart_line_arr[]=$buyer_arr[$buyer_id];
                            $chart_ex_factory_val_cln_arr[]=$row['ex_factory_val_cln'];
                            $chart_ex_factory_val_com_arr[]=$row['ex_factory_val_com'];
                            $chart_invoice_value_arr[]=$row['invoice_value'];
                            $chart_net_invo_value_arr[]=$row['net_invo_value'];
                            $chart_ex_factory_qnty_arr[]=$row['EX_FACTORY_QNTY'];
                            
                        }
                            $chart_line_arr= json_encode($chart_line_arr);
                            $chart_ex_factory_val_cln_arr= json_encode($chart_ex_factory_val_cln_arr);
                            $chart_ex_factory_val_com_arr= json_encode($chart_ex_factory_val_com_arr);
                            $chart_invoice_value_arr= json_encode($chart_invoice_value_arr);
                            $chart_net_invo_value_arr= json_encode($chart_net_invo_value_arr);
                            $chart_ex_factory_qnty_arr= json_encode($chart_ex_factory_qnty_arr);
                        ?>
                        
                    </tbody>
                    <tfoot>
                        <tr >
                            <th width="150" class="right">Total</th>
                            <th width="100" class="right"><? echo number_format($total_ex_factory_qnty,2); ?></th>
                            <th width="100" class="right"><? echo number_format($total_ex_factory_val_cln,2); ?></th>
                            <th width="100" class="right"><? echo number_format($total_ex_factory_val_com,2); ?></th>
                            <th width="100" class="right"><? echo number_format($total_invoice_value,2); ?></th>
                            <th class="right"><? echo number_format($total_net_invo_value,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
            
        </div> 
        <br>
        <div style="width:550px; height:450px;  margin-left:10px; border:solid 1px;  float:left">
            <table style="min-width:500px; font-size:12px" align="center">
                <tr>
                    <td colspan="4" class="center"><b><? echo $company_library[$company_name]; ?></br>Export Status Summary </b> </td>
                </tr>
                <tr>
                    <td bgcolor="#228C22" width="16"></td>
                    <td  align="left"><b>Exfactory Qty[pcs] challan</b></td>
                    <td bgcolor="#4472C4" width="16"></td>
                    <td ><b>Ex-Factory Value ($) (Challan)</b></td>
                </tr>
                <tr>
                    <td bgcolor="#ED7D31" width="16"></td>
                    <td ><b>Ex-Factory Value ($) (Commercial)</b></td>
                    <td bgcolor="#A5A5A5" width="16"></td>
                    <td ><b>Invoice Value ($)</b></td>
                </tr>
                <tr>
                    <td bgcolor="#FFC000" width="16"></td>
                    <td ><b>Net Invoice Value ($)</b></td>
                </tr>
            </table>
            <canvas id="canvas1" height="380" width="550" ></canvas>
        </div>
        <style>
			#canvas {
				font-size : 11px;
			}					
		</style>
        <script src="../../../Chart.js-master/Chart.js"></script>
        <script>
            var barChartData = {
			labels : <?php echo $chart_line_arr; ?>,
			barPercentage: 0.5,
			datasets : [
                    {
						fillColor : "#228C22",
						data : <?php echo $chart_ex_factory_qnty_arr; ?>
					},
					{
						fillColor : "#4472C4",
						data : <?php echo $chart_ex_factory_val_cln_arr; ?>
					},
					{
						fillColor : "#ED7D31",
						data : <?php echo $chart_ex_factory_val_com_arr; ?>
					},
					{
						fillColor : "#A5A5A5",
						data : <?php echo $chart_invoice_value_arr; ?>
					},
					{
						fillColor : "#FFC000",
						data : <?php echo $chart_net_invo_value_arr; ?>
					}
                  
				]
			}
			
			var ctx = document.getElementById("canvas1").getContext("2d");
			window.myBar = new Chart(ctx).Bar(barChartData, {
				responsive : true
			});	
        </script>
        <?
    }

	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType";
	exit();
}
disconnect($con);
?>
