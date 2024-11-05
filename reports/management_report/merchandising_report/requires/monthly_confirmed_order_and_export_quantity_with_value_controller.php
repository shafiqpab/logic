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
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if($action =="load_drop_down_mul_com_cond")
{
    extract($_REQUEST);  
    $size = count(explode(",",$data));
    if ($size>1) 
    {
        echo "$('#show_button1').attr('hidden',true);\n";
    }
    else 
    {
        echo "$('#show_button1').attr('hidden',false);\n";
    }
    exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_product_department=str_replace("'","",$cbo_product_department);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

    $search_cond="";
    if($cbo_company_name){ $search_cond.=" and c.company_name in ($cbo_company_name)";}
    if($cbo_buyer_name){ $search_cond.=" and c.buyer_name=$cbo_buyer_name";}
    if($txt_job_no!=""){ $search_cond.=" and c.job_no like '%$txt_job_no'";}
    if($txt_style_ref!=""){ $search_cond.=" and c.style_ref_no like '%$txt_style_ref%'";}
    if($cbo_team_leader){ $search_cond.=" and c.team_leader=$cbo_team_leader";}
    if($cbo_product_department){ $search_cond.=" and c.product_dept=$cbo_product_department";}

    if($db_type==0)
	{
		$startDate=change_date_format($txt_date_from,'yyyy-mm-dd');
		$endDate=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$startDate=change_date_format($txt_date_from,'','',-1);
		$endDate=change_date_format($txt_date_to,'','',-1);
	}
    if($cbo_date_type==1){ $search_cond.=" and b.shipment_date between '$startDate' and '$endDate' "; }
    else if($cbo_date_type==2){ $search_cond.=" and b.pub_shipment_date between '$startDate' and '$endDate' "; }
    else if($cbo_date_type==3){ $search_cond.=" and d.country_ship_date between '$startDate' and '$endDate' "; }

    $company_array = return_library_array("select id,company_name from lib_company where is_deleted=0","id","company_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where is_deleted=0",'id','buyer_name');
    // $brand_arr=return_library_array( "select id, brand_name from lib_brand where is_deleted=0",'id','brand_name');
    $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand where is_deleted=0",'id','brand_name');
    $season_arr=return_library_array( "select id,season_name from lib_buyer_season where is_deleted=0",'id','season_name');
    $sub_dep_arr=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment where is_deleted=0",'id','sub_department_name');
    $imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
    $company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
    $company_team_leader_name_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
    $company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
    $commission_arr= return_library_array("select job_id,sum(commision_rate) as commision_rate from wo_pre_cost_commiss_cost_dtls where is_deleted=0 group by job_id","job_id","commision_rate");

	if($reportType==1)//Show Button
	{
		$sql_result="SELECT b.job_id as JOB_ID, b.id as PO_ID, b.shipment_date as SHIPMENT_DATE,  b.po_quantity as PO_QUANTITY  , sum(d.order_quantity) AS QNTY, sum(d.order_total) AS ORDER_VALUE, b.unit_price as UNIT_PRICE, b.is_confirmed as IS_CONFIRMED, c.buyer_name as BUYER_NAME, c.total_set_qnty as TOTAL_SET_QNTY, c.company_name as COMPANY_NAME 
        from wo_po_details_master c,wo_po_break_down b 
        left join wo_po_color_size_breakdown d on d.po_break_down_id=b.id and d.status_active=1
        where b.job_no_mst=c.job_no and b.status_active=1 and c.status_active=1 $search_cond 
        group by b.id,b.shipment_date,b.unit_price,b.is_confirmed,b.job_id,c.buyer_name,c.total_set_qnty,c.company_name, b.po_quantity";
 
        // echo $sql;
        $order_result=sql_select($sql_result);
        $orderQtyConArr=$orderValConArr=$orderQtyProArr=$orderValProArr=$orderIdArr=$factoryValArr=$orderQty=$orderVal=$orderNetValConfirmed=$orderNetValProjected=array();
        $company_id=$order_result[0]['COMPANY_NAME'];
        foreach($order_result as $row)
        { 
            $monthKey=date("M y",strtotime($row['SHIPMENT_DATE']));
            $commission_amt=0;
			if($commission_arr[$row['JOB_ID']]>0)
			{
				$commission_amt=(($row['ORDER_VALUE']*$commission_arr[$row['JOB_ID']])/100);
			}
            if($row['IS_CONFIRMED']==1)
            {
                $orderQtyConArr[$monthKey]+=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];
                $orderValConArr[$monthKey]+=$row['ORDER_VALUE'];   
                $buyer_conQty_Arr[$row['BUYER_NAME']]+=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];
                $buyer_conVal_Arr[$row['BUYER_NAME']]+=$row['ORDER_VALUE'];
                $monthWisePoArr[$monthKey][$row['PO_ID']]=$row['PO_ID'];
                $orderNetValConfirmed[$monthKey]+=$row['ORDER_VALUE']-$commission_amt;
            }
            if($row['IS_CONFIRMED']==2)
            {
                $orderQtyProArr[$monthKey]+=$row['QNTY'];
                $orderValProArr[$monthKey]+=$row['ORDER_VALUE'];   
                $buyer_proQty_Arr[$row['BUYER_NAME']]+=$row['QNTY'];
                $orderNetValProjected[$monthKey]+=$row['ORDER_VALUE']-$commission_amt;
            }
            
            $orderIdArr[$row['PO_ID']]=$row['PO_ID'];
            $factoryValArr[$row['PO_ID']]=$row['UNIT_PRICE']/$row['TOTAL_SET_QNTY'];
            // $monthWisePoArr[$monthKey][$row['PO_ID']]=$row['PO_ID'];
            $monthWisePoArrQty[$row['PO_ID']]=$row['QNTY'];
            $monthWisePoArrVal[$row['PO_ID']]=$row['ORDER_VALUE'];
            $monthWiseJobArr[$row['PO_ID']]=$row['JOB_ID'];

            $buyer_PoQty_Arr[$row['BUYER_NAME']]+=$row['QNTY'];
            $po_buyer_Arr[$row['PO_ID']]=$row['BUYER_NAME'];  
        }
        $exFactoryQtyArr=array();$exFactoryValArr=array();$cuntryExFactoryQtyArr=array();

        $poId_in=where_con_using_array($orderIdArr,0,'po_break_down_id');
        $sql="SELECT po_break_down_id as PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY, sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poId_in group by po_break_down_id";
        // echo $sql;die;
        $exfactory_result=sql_select($sql);
        
        foreach($exfactory_result as $row)
        {
            $exFactoryValArr[$row['PO_BREAK_DOWN_ID']]=($row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY'])*$factoryValArr[$row['PO_BREAK_DOWN_ID']];
            $cuntryExFactoryQtyArr[$row['PO_BREAK_DOWN_ID']]=$row['EX_FACTORY_QNTY']-$row['EX_FACTORY_RETURN_QNTY'];
        }

        $invoice_id_sql=sql_select("SELECT PO_BREAK_DOWN_ID, INVOICE_NO from pro_ex_factory_mst where status_active=1 and is_deleted=0 $poId_in ");
		$invoiceIdArr=$exFactoryPoIdArr=array();
		foreach($invoice_id_sql as $row)
		{
			if($row['INVOICE_NO']){$invoiceIdArr[$row['INVOICE_NO']]=$row['INVOICE_NO'];}
			$exFactoryPoIdArr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
		}

        $po_id_in=where_con_using_array($exFactoryPoIdArr,0,'b.po_breakdown_id');
		$invoice_id_in=where_con_using_array($invoiceIdArr,0,'a.id');
		$exInvoice_sql="SELECT a.id, a.INVOICE_VALUE, a.NET_INVO_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id $po_id_in $invoice_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$exInvoice_data=sql_select($exInvoice_sql);
		$exInvoiceArr=array();
		foreach($exInvoice_data as $row)
		{
			if($row['CURRENT_INVOICE_VALUE']){$exInvoiceArr[$row['PO_BREAKDOWN_ID']]+=($row['NET_INVO_VALUE']/$row['INVOICE_VALUE'])*$row['CURRENT_INVOICE_VALUE'];}
		}

        $remain_months=datediff( "m",$startDate,date("Y-m-d",strtotime($endDate)));
        for($e=0;$e<=$remain_months;$e++)
        {
            // $tmp=date('Y-m-d', mktime(0,0,0,date('m',strtotime(date("Y-m-d",strtotime($startDate))))+$e,1,date('Y',strtotime($startDate))));
            $tmp=month_add(date("Y-m-d",strtotime($startDate)), $e);
            $month_arr[$e]=date("M y",strtotime($tmp));
        }

        $confirm_order_qty_array=$projected_order_qty_array=array();$exFactory_qty_array=array();
        $shortQtyArr=$shortValArr=$overQtyArr=$overtValArr=$buyer_ExcessQty_Arr=$buyer_ShortQty_Arr=array();
        foreach($month_arr as $key=>$monthYear)
        {         
            $confirm_order_qty_array[]=number_format($orderQtyConArr[$monthYear],0,'.','');      
            $projected_order_qty_array[]=number_format($orderQtyProArr[$monthYear],0,'.','');      
            $exFactory_qty=0;$exFactory_val=0;$short_Qty_Arr=0;$short_Val_Arr=0;$over_Qty_Arr=0;$overt_Val_Arr=0;	
            foreach($monthWisePoArr[$monthYear] as $po)
            {
                $exFactory_qty+=$cuntryExFactoryQtyArr[$po];
                $exFactory_val+=$exFactoryValArr[$po];
                $exFactoryNetValArr[$monthYear]+=$exInvoiceArr[$po];

                $all_pcs_rate=$monthWisePoArrVal[$po]/$monthWisePoArrQty[$po];
                $order_qty_fnc=$monthWisePoArrQty[$po];
                $exFactory_qty_fnc=$cuntryExFactoryQtyArr[$po];

                if($commission_arr[$monthWiseJobArr[$po]]>0)
				{
					$all_pcs_rate_after_commission=$all_pcs_rate-(($all_pcs_rate*$commission_arr[$monthWiseJobArr[$po]])/100);
				}
				else
				{
					$all_pcs_rate_after_commission=$all_pcs_rate;
				}
                
                if($order_qty_fnc>$exFactory_qty_fnc){
                    $short_Qty_Arr+=($order_qty_fnc-$exFactory_qty_fnc);
                    $short_Val_Arr+=(($order_qty_fnc-$exFactory_qty_fnc)*$all_pcs_rate);
                    $buyer_ShortQty_Arr[$po_buyer_Arr[$po]]+=$order_qty_fnc-$exFactory_qty_fnc;  
                    $short_Net_Val_Arr[$monthYear]+=(($order_qty_fnc-$exFactory_qty_fnc)*$all_pcs_rate_after_commission); 
                }
                if($exFactory_qty_fnc>$order_qty_fnc){
                    $over_Qty_Arr+=($exFactory_qty_fnc-$order_qty_fnc);
                    $overt_Val_Arr+=(($exFactory_qty_fnc-$order_qty_fnc)*$all_pcs_rate);
                    $buyer_ExcessQty_Arr[$po_buyer_Arr[$po]]+=$exFactory_qty_fnc-$order_qty_fnc; 
                    $over_Net_Val_Arr[$monthYear]+=($exInvoiceArr[$po]/$exFactoryValArr[$po])*($exFactory_qty_fnc-$order_qty_fnc)*$all_pcs_rate;
                }
                $buyer_ExportQty_Arr[$po_buyer_Arr[$po]]+=$cuntryExFactoryQtyArr[$po];            
                $buyer_ExportVal_Arr[$po_buyer_Arr[$po]]+=$exFactoryValArr[$po];            
            }

            $exFactory_qty_array[]=number_format($exFactory_qty,0,'.','');
            $short_qty_Array[]=number_format($short_Qty_Arr,0,'.','');
            $exFactoryQtyArr[$monthYear]=number_format($exFactory_qty,0,'.','');
            $shortQtyArr[$monthYear]=number_format($short_Qty_Arr,0,'.','');
            $shortValArr[$monthYear]=number_format($short_Val_Arr,0,'.','');
            $overQtyArr[$monthYear]=number_format($over_Qty_Arr,0,'.','');
            $overtValArr[$monthYear]=number_format($overt_Val_Arr,0,'.','');
            // $exFactoryValArr[$monthYear]=number_format($exFactory_val,2,'.','');
            if($exFactory_val!=''){$exFactoryValArr[$monthYear]=number_format($exFactory_val,2,'.','');}

        }
        $tbl_with=520+$remain_months*60;
        $tbl_with_canvas=100+$remain_months*60;
        ob_start();
        ?>
            <style>td{word-break:break-word;}</style>
            <div style="margin:5px;width:100%;" >
            <div style="width:<?if($tbl_with>1210) {echo $tbl_with;}else{echo 1210;}?>px;">
                <div style="width:60%; margin-left:10px; float: left;" align='left' >
                    <table cellpadding="0" width="<?=$tbl_with_canvas;?>" cellspacing="0" border="1" class="rpt_table" rules="all" align='left' id="tbl_details">
                        <thead>
                        <tr ><td colspan="<?=count($month_arr)+1;?>"><canvas id="canvas"></canvas></td></tr>
                            <tr>
                                <th width="100"><?= $company_array[$company_id];?></th>
                                <? foreach($month_arr as $month){echo "<th width='60'>" . $month . "</th>";} ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Projected Order Qty(pcs)</td>
                                <? foreach($month_arr as $month){echo "<td align='right'>" ;if($orderQtyProArr[$month]){echo number_format($orderQtyProArr[$month]);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Projected Order Gross Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right'>" ;if($orderValProArr[$month]){echo number_format($orderValProArr[$month],2) ;}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Projected Order Net Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right'>" ;if($orderNetValProjected[$month]){echo number_format($orderNetValProjected[$month],2) ;}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Confirmed Order Qty(pcs)</td>
                                <? foreach($month_arr as $month){echo "<td align='right'>" ;if($orderQtyConArr[$month]){echo number_format($orderQtyConArr[$month]);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Confirmed Order Gross Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right'>" ;if($orderValConArr[$month]){echo number_format($orderValConArr[$month],2) ;}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Confirmed Order Net Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right'>" ;if($orderNetValConfirmed[$month]){echo number_format($orderNetValConfirmed[$month],2) ;}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Export Qty (pcs)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if($exFactoryQtyArr[$month]){echo number_format($exFactoryQtyArr[$month]);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Export Gross Values ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if($exFactoryValArr[$month]){echo number_format($exFactoryValArr[$month],2);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Export Net Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if($exFactoryNetValArr[$month]){echo number_format($exFactoryNetValArr[$month],2);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Excess Export Qty (pcs)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if($overQtyArr[$month]){echo number_format($overQtyArr[$month]);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Excess Export Gross Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if($overtValArr[$month]){echo number_format($overtValArr[$month],2);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Excess Export Net Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:green;'>" ;if($over_Net_Val_Arr[$month]){echo number_format($over_Net_Val_Arr[$month],2);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Short Export Qty (pcs)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if($shortQtyArr[$month]){echo number_format($shortQtyArr[$month]);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Short Export Gross Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if($shortValArr[$month]){echo number_format($shortValArr[$month],2);}echo "&nbsp;</td>";} ?>
                            </tr>
                            <tr>
                                <td>Short Export Net Value ($)</td>
                                <? foreach($month_arr as $month){echo "<td align='right' style='color:red;'>" ;if($short_Net_Val_Arr[$month]){echo number_format($short_Net_Val_Arr[$month],2);}echo "&nbsp;</td>";} ?>
                            </tr>
                        </tbody>
                    </table>
                </div>   
                <div style="width:30%; float:right;">
                    <table border="1" rules="all" width="450" align="center">
                        <tr>
                            <td width="100">Generate Date:</td>
                            <td> <? echo date("Y-m-d",time());?></td>
                        </tr>
                        <tr>
                            <td>Generate Time:</td>
                            <td> <? echo date("h:i:s a",time());?></td>
                        </tr>
                    </table>  
                    <br>
                    <b>TOTAL EXPORT</b>
                    <table class="rpt_table"  border="1" rules="all" width="450" align="center">
                        <thead>
                            <tr>
                                <th width="100">Month</th>
                                <th width="100">Total Projected Order</th>
                                <th width="100">Total Confirmed Order</th>
                                <th width="50">Total Export</th>
                                <th width="50">Ex. Exp.</th>
                                <th>Sh. Exp.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center"> 
                                    <? 
                                        foreach($month_arr as $key => $element) {
                                            reset($month_arr);
                                            if ($key === key($month_arr))
                                                echo $element." - ";
                                        
                                            end($month_arr);
                                            if ($key === key($month_arr))
                                                echo $element;
                                        }
                                    ?>
                                </td>
                                <td align="right"><? echo number_format(array_sum($buyer_proQty_Arr));?></td>
                                <td align="right"><? echo number_format(array_sum($buyer_conQty_Arr));?></td>
                                <td align="right"><? echo number_format(array_sum($buyer_ExportQty_Arr));?></td>
                                <td align="right"><? echo number_format(array_sum($buyer_ExcessQty_Arr));?></td>
                                <td align="right"><? echo number_format(array_sum($buyer_ShortQty_Arr));?></td>
                            </tr>
                        </tbody>
                    </table> 
                    <br>
                    <b>EXPORT PROGRESS OF ORDERS--x--</b>
                    <table class="rpt_table" border="1" rules="all" width="450" align="center">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th width="50">Projected Order Qty</th>
                                <th width="50">Confirmed Order Qty-W</th>
                                <th width="50">Confirmed Order value-W</th>
                                <th width="50">Export Qty</th>
                                <th width="50">Export value</th>
                                <th width="50">Export Progress (%)</th>
                                <th width="40">Ex. Exp.</th>
                                <th width="40">Sh. Exp.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <? foreach($buyer_PoQty_Arr as $buyer_id=>$poQty){?>
                            <tr>
                                <td><? echo $buyer_arr[$buyer_id];?></td>
                                <td align="right"><? echo number_format($buyer_proQty_Arr[$buyer_id]);?></td>
                                <td align="right"><? echo number_format($buyer_conQty_Arr[$buyer_id]);?></td>
                                <td align="right"><? echo number_format($buyer_conVal_Arr[$buyer_id]);?></td>
                                <td align="right"><? echo number_format($buyer_ExportQty_Arr[$buyer_id]);?></td>
                                <td align="right"><? echo number_format($buyer_ExportVal_Arr[$buyer_id]);?></td>
                                <td align="right"><? echo number_format(($buyer_ExportQty_Arr[$buyer_id]*100)/$poQty,2);?></td>
                                <td align="right"><? echo number_format($buyer_ExcessQty_Arr[$buyer_id]);?></td>
                                <td align="right"><? echo number_format($buyer_ShortQty_Arr[$buyer_id]);?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table> 
                </div> 
            </div>
            </div>
        <?
        $monthArray= json_encode($month_arr); 
        $confirm_order_qty_array= json_encode($confirm_order_qty_array); 
        $projected_order_qty_array= json_encode($projected_order_qty_array); 
        $exFactory_qty_array= json_encode($exFactory_qty_array); 
        $short_qty_Array= json_encode($short_qty_Array); 
	}

	if($reportType==2)//Details Button
    {
        $date=date('d-m-Y');
		$sql_result="SELECT b.id as PO_ID, b.is_confirmed as IS_CONFIRMED, b.po_number as PO_NUMBER, b.pub_shipment_date as PUB_SHIPMENT_DATE, b.shipment_date as SHIPMENT_DATE, b.po_total_price as ORDER_VALUE ,b.unit_price as UNIT_PRICE, b.po_quantity as PO_QUANTITY, b.shiping_status as SHIPING_STATUS, b.po_received_date as PO_RECEIVED_DATE, b.details_remarks as DETAILS_REMARKS, b.delay_for as DELAY_FOR,b.grouping as INTERNAL_REF,b.file_no as FILE_NO, b.t_year as T_YEAR, b.t_month as T_MONTH, b.po_received_date, min(TRUNC(b.insert_date)) as INSERT_DATE,c.id as ID, c.buyer_name as BUYER_NAME, c.season_buyer_wise as SEASON_BUYER_WISE,c.pro_sub_dep as PRO_SUB_DEP,c.product_dept as PRODUCT_DEPT, c.ship_mode as SHIP_MODE, c.total_set_qnty as TOTAL_SET_QNTY, c.company_name as COMPANY_NAME, c.job_no as JOB_NO, c.agent_name as AGENT_NAME, c.style_ref_no as STYLE_REF_NO, c.style_description as STYLE_DESCRIPTION, c.job_quantity as JOB_QUANTITY, c.product_category as PRODUCT_CATEGORY, c.location_name as LOCATION_NAME, c.gmts_item_id as GMTS_ITEM_ID, c.order_uom as ORDER_UOM, c.team_leader as TEAM_LEADER, c.dealing_marchant as DEALING_MARCHANT, c.factory_marchant as FACTORY_MARCHANT, c.product_code as PRODUCT_CODE, c.brand_id as BRAND_ID, c.set_break_down as SET_BREAK_DOWN, sum(d.order_quantity) as ORDER_QUANTITY_PCS, (b.shipment_date - to_date('$date','dd-mm-yyyy')) as DATE_DIFF_1
        from wo_po_details_master c,wo_po_break_down b
        left join wo_po_color_size_breakdown d on d.po_break_down_id=b.id and d.status_active=1
        where b.job_no_mst=c.job_no and b.status_active=1 and c.status_active=1 and b.is_confirmed=1 $search_cond 
        group by  b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_total_price, b.unit_price, b.po_quantity, b.shiping_status, b.po_received_date, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.t_year, b.t_month,c.id , c.buyer_name, c.season_buyer_wise,c.pro_sub_dep,c.product_dept, c.ship_mode, c.total_set_qnty, c.company_name, c.job_no, c.agent_name, c.style_ref_no, c.style_description, c.job_quantity, c.product_category, c.location_name, c.gmts_item_id, c.order_uom, c.team_leader, c.dealing_marchant, c.factory_marchant, c.product_code, c.brand_id, c.set_break_down order by c.id";

        //echo $sql_result;

        $order_result=sql_select($sql_result);
        $po_array_for_cond=array();
        $job_array_for_cond=array();
        foreach ($order_result as $row_po_job)
        {
            $po_array_for_cond[$row_po_job['PO_ID']]=$row_po_job['PO_ID'];
            $job_array_for_cond[$row_po_job['JOB_NO']]="'".$row_po_job['JOB_NO']."'";
        }

        $job_cond_for_in=where_con_using_array($job_array_for_cond,0,'job_no');
        $job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
        $job_smv_eff_arr=return_library_array( "select job_no, sew_effi_percent from wo_pre_cost_mst where 1=1 $job_cond_for_in",'job_no','sew_effi_percent');
        
        $po_cond_for_in=where_con_using_array($po_array_for_cond,0,'po_break_down_id');
        $exfactory_data=sql_select("SELECT po_break_down_id as PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY, MAX(ex_factory_date) as EX_FACTORY_DATE
		from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row)
		{
			$exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_qnty']=$exfatory_row['EX_FACTORY_QNTY']-$exfatory_row['EX_FACTORY_RETURN_QNTY'];
            $exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_date']=$exfatory_row['EX_FACTORY_DATE'];
		}
        unset($exfactory_data);

        $ac_po_sql=sql_select("SELECT id, po_break_down_id, acc_po_no from wo_po_acc_po_info where is_deleted=0 $po_cond_for_in $job_cond_for_in");

        $ac_po_arr=array();
        foreach($ac_po_sql as $row)
        {
            $ac_po_arr[$row["PO_BREAK_DOWN_ID"]].=$row["ACC_PO_NO"].", ";
        }
        unset($ac_po_sql);

        $invoice_id_sql=sql_select("SELECT po_break_down_id, invoice_no as INVOICE_NO from pro_ex_factory_mst where status_active=1 and is_deleted=0 $po_cond_for_in ");
		$invoiceIdArr=$exFactoryPoIdArr=array();
		foreach($invoice_id_sql as $row)
		{
			if($row['INVOICE_NO']){$invoiceIdArr[$row['INVOICE_NO']]=$row['INVOICE_NO'];}
			$exFactoryPoIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
        unset($invoice_id_sql);

		$po_id_in=where_con_using_array($exFactoryPoIdArr,0,'b.po_breakdown_id');
		$invoice_id_in=where_con_using_array($invoiceIdArr,0,'a.id');
		$exInvoice_sql="SELECT a.id, a.INVOICE_VALUE, a.NET_INVO_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id $po_id_in $invoice_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$exInvoice_data=sql_select($exInvoice_sql);
		$exInvoiceArr=array();
		foreach($exInvoice_data as $row)
		{
			if($row['CURRENT_INVOICE_VALUE']){$exInvoiceArr[$row['PO_BREAKDOWN_ID']]+=($row['NET_INVO_VALUE']/$row['INVOICE_VALUE'])*$row['CURRENT_INVOICE_VALUE'];}
		}
        unset($exInvoice_data);
        ob_start();
        ?>
        <style>
        table tbody tr td
        {
            word-break: break-word;
        }
        </style>
        <div style="margin:5px;width:100%;" >
            <table cellpadding="0" width="4670" cellspacing="0" border="1" rules="all" align='left' id="tbl_details">
                <tr>
                    <td align='center'><strong><?=$company_array[$cbo_company_name];?></strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Monthly Confirmed Order and Export Quantity with Value</strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Export Details</strong></td>
                </tr>
                <tr>
                    <td align='center'>Date Range : <?=$startDate.' - '.$endDate;?>&nbsp;&nbsp;&nbsp; Report Generate Time:<?=date("h:i:s a",time());?></td>
                </tr>
            </table>
            <br>
            <table width="4670" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Company Name</th>
                        <th width="100">Job No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Agent</th>
                        <th width="100">Order No</th>
                        <th width="100">Actual PO</th>
                        <th width="100">Season</th>
                        <th width="100">Pord. Dept.</th>
                        <th width="100">Sub. Dept</th>
                        <th width="100">Pord. Dept Code</th>
                        <th width="100">Brand</th>
                        <th width="100">Img</th>
                        <th width="100">Item</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Style Des</th>
                        <th width="100">PO Insert Date</th>
                        <th width="100">Pub Ship Date</th>
                        <th width="100">Original Ship Date</th>
                        <th width="60" >Lead Time</th>
                        <th width="100">SMV</th>
                        <th width="60" >Eff. %</th>
                        <th width="80" >Total SMV</th>
                        <th width="80" >Order Qnty</th>
                        <th width="70" >Uom</th>
                        <th width="80" >Order Qnty (Pcs)</th>
                        <th width="80" >Break-down Qty (Pcs)</th>
                        <th width="80" >Per Unit Price</th>
                        <th width="80" >Gross Order Value ($)</th>
                        <th width="80" >Net Order Value ($)</th>
                        <th width="80" >Ex-Fac Qnty (Pcs)</th>
                        <th width="80" >Gross Ex-Fac Value ($)</th>
                        <th width="80" >Net Ex-Fac Value ($)</th>
                        <th width="80" >Ex-factory Bal. (Pcs)</th>
                        <th width="80" >Ex-factory Over (Pcs)</th>
                        <th width="80" >Gross Ex-factory Bal. Value ($)</th>
                        <th width="80" >Net Ex-factory Bal. Value ($)</th>
                        <th width="80" >Gross Ex-factory Over Value ($)</th>
                        <th width="80" >Net Ex-factory Over Value ($)</th>
                        <th width="80" >Short/ Over/At Per</th>
                        <th width="80" >Order Status</th>
                        <th width="80" >Prod. Catg</th>
                        <th width="80" >PO Rec. Date</th>
                        <th width="80" >Ex-fac. Bal. on Brk-dwn Qty (Pcs)</th>
                        <th width="80" >Days in Hand</th>
                        <th width="80" >Shipping Status</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Factory Merchandiser</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Team Name</th>
                        <th width="70" >Ship Mode</th>
                        <th width="100">Int. Ref/ Grouping</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:4670px"  align="left" id="scroll_body">
                <table width="4650" border="1" class="rpt_table" rules="all" id="export_details_tbl">
                    <?
                        $i=1;
                        foreach($order_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row['GMTS_ITEM_ID']);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                            $ex_factory_qnty=$exfactory_data_array[$row["PO_ID"]]['ex_factory_qnty'];
                            $poQtyPcs=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];
                            if($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']>$row['ORDER_QUANTITY_PCS']) $bgColorBreakDown="#ff0000";
                            else $bgColorBreakDown="";
                            $ex_factory_date=$exfactory_data_array[$row["PO_ID"]]['ex_factory_date'];
							$date_diff_2=datediff( "d", $ex_factory_date , $row['SHIPMENT_DATE']);
                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                            {
                                if($row['DATE_DIFF_1']>10){ $bgColorDaysHand="";}
                                if($row['DATE_DIFF_1']<=10 && $row['DATE_DIFF_1']>=0){ $bgColorDaysHand="orange";}
                                if($row['DATE_DIFF_1']<0){ $bgColorDaysHand="#ff0000";}
                            }
                            else
                            {
                                if($row['SHIPING_STATUS']==3 && $date_diff_2 >=0 ) $bgColorDaysHand="green";
                                if($row['SHIPING_STATUS']==3 &&  $date_diff_2<0) $bgColorDaysHand="#2A9FFF";
                            }
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                    <td width="30" align='center'><?=$i;?></td>
                                    <td width="100"><?echo $company_array[$row['COMPANY_NAME']];?></td>
                                    <td width="100"><?echo $row['JOB_NO'];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['BUYER_NAME']];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['AGENT_NAME']];?></td>
                                    <td width="100"><?echo $row['PO_NUMBER'];?></td>
                                    <td width="100"><?=rtrim($ac_po_arr[$row["PO_ID"]],", ");?></td>
                                    <td width="100"><?echo $season_arr[$row['SEASON_BUYER_WISE']];?></td>
                                    <td width="100"><?echo $product_dept[$row['PRODUCT_DEPT']];?></td>
                                    <td width="100"><?echo $sub_dep_arr[$row['PRO_SUB_DEP']];?></td>
                                    <td width="100"><?echo $row['PRODUCT_CODE'];?></td>
                                    <td width="100"><?echo $brand_arr[$row['BRAND_ID']];?></td>
                                    <td width="100" align='center'><img src='../../../<? echo $imge_arr[$row['JOB_NO']]; ?>' height='25'  /></td>
                                    <td width="100"><? echo rtrim($gmts_item_name,","); ?></td>
                                    <td width="100"><?echo $row['STYLE_REF_NO'];?></td>
                                    <td width="100"><?echo $row['STYLE_DESCRIPTION'];?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['INSERT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['PUB_SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="60" align='center'><? echo datediff('d',$row['PO_RECEIVED_DATE'],$row['SHIPMENT_DATE']);?></td>
                                    <td width="100" align='center'>
                                        <?
                                            $setSmvArr=array();
                                            foreach(explode('__',$row['SET_BREAK_DOWN']) as $setBrAr){
                                                list($itemId,$setRa,$setSmv)=explode('_',$setBrAr);
                                                $setSmvArr[]=$setSmv;
                                                
                                            }
                                            echo "[ ".implode(',',$setSmvArr)." ], ";
                                            echo number_format($job_smv_arr[$row['JOB_NO']],2);
                                        ?>
                                    </td>
                                    <td width="60" align='right'><?=$job_smv_eff_arr[$row['JOB_NO']];?></td>
                                    <td width="80" align='right'>
                                        <?  $smv= ($job_smv_arr[$row['JOB_NO']])*$row['PO_QUANTITY']; $smv_tot+=$smv; echo fn_number_format($smv,2); ?>
                                    </td>
                                    <td width="80" align='right'><?echo $row['PO_QUANTITY'];?></td>
                                    <td width="70" align='center'><?echo $unit_of_measurement[$row['ORDER_UOM']];?></td>
                                    <td width="80" align='right'><?echo fn_number_format(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']),0);$order_pcs_tot+=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];?></td>
                                    <td width="80" align='right' bgcolor="<? echo $bgColorBreakDown; ?>"><?echo $row['ORDER_QUANTITY_PCS'];$break_down_pcs_tot+=$row['ORDER_QUANTITY_PCS']?></td>
                                    <td width="80" align='right'><? echo number_format($row['UNIT_PRICE'],2);?></td>
                                    <td width="80" align='right'><? echo number_format($row['ORDER_VALUE'],2);$order_value_tot+=$row['ORDER_VALUE'];?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($commission_arr[$row['ID']]>0){
                                                echo number_format($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100),2);
                                                $net_order_value_tot+=($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100));
                                            }else{
                                                echo number_format($row['ORDER_VALUE'],2);$net_order_value_tot+=$row['ORDER_VALUE'];
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'><? echo fn_number_format( $ex_factory_qnty,0);$ex_factory_qnty_tot+=$ex_factory_qnty; ?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            $ex_factory_value=$ex_factory_qnty*($row['UNIT_PRICE']/$row['TOTAL_SET_QNTY']);
                                            echo fn_number_format( $ex_factory_value,0);$ex_factory_value_tot+=$ex_factory_value; 
                                        ?>
                                    </td>
                                    <td width="80" align='right'>                                        
                                        <? 
                                            /*if($commission_arr[$row['ID']]>0){
                                                echo number_format($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100),0);
                                                $net_ex_factory_value_tot+=($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100));
                                            }else{
                                                echo number_format($ex_factory_value,0);$net_ex_factory_value_tot+=$ex_factory_value;
                                            }*/
                                            echo number_format($exInvoiceArr[$row["PO_ID"]],0);$net_ex_factory_value_tot+=$exInvoiceArr[$row["PO_ID"]];
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            $short_over_shipment="";
                                            $short_access_qnty=(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'])-$ex_factory_qnty);
                                            if($short_access_qnty>=0)
                                            {
                                                echo fn_number_format($short_access_qnty,0);
                                                $total_short_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                            echo fn_number_format(ltrim($short_access_qnty,'-'),0);
                                            $total_over_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                        if($short_access_qnty>=0)
                                        {
                                            $short_access_value=($short_access_qnty/$row['TOTAL_SET_QNTY'])*$row['UNIT_PRICE'];
                                            echo fn_number_format($short_access_value,2);
                                            $total_short_access_value+=$short_access_value;
                                        }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($short_access_qnty>=0)
                                            {
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100),2);
                                                    $total_net_short_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($short_access_value,2);$total_net_short_access_value+=$short_access_value;
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                                $short_over_value=ltrim(($short_access_qnty/$row[csf('total_set_qnty')])*$row[csf('unit_price')],'-');
                                                echo  fn_number_format($short_over_value,2);
                                                $total_over_access_value+=$short_over_value;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            /*if($short_access_qnty<0)
                                            {
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($short_over_value-(($short_over_value*$commission_arr[$row['ID']])/100),2);
                                                    $total_net_over_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($short_over_value,2);$total_net_over_access_value+=$short_over_value;
                                                }
                                            }*/
                                            if($short_access_qnty<0)
                                            {
                                                echo number_format(($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value,2);
                                                $total_net_over_access_value+=($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value;
                                            }

                                        ?>
                                    </td>
                                    <td width="80" align='center'>
                                        <?
                                            $short_over_shipment="";
                                            if(($poQtyPcs-$ex_factory_qnty)==0){$short_over_shipment="At Per";}
                                            else if($poQtyPcs<$ex_factory_qnty){$short_over_shipment= "Over Shipment";}
                                            else if($poQtyPcs>$ex_factory_qnty){$short_over_shipment="Short Shipment";}
                                            echo $short_over_shipment;
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $order_status[$row['IS_CONFIRMED']];?></td>
                                    <td width="80" align='center'><? echo $product_category[$row['PRODUCT_CATEGORY']];?></td>
                                    <td width="80" align='center'><? echo change_date_format($row['PO_RECEIVED_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="80" align='center'><? echo fn_number_format($row['ORDER_QUANTITY_PCS']-$ex_factory_qnty);$total_ex_factory_brk_dwn_qty+=$row['ORDER_QUANTITY_PCS']-$ex_factory_qnty;?></td>
                                    <td width="80" align='center'  bgcolor="<? echo $bgColorDaysHand; ?>">
                                        <?
                                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                                            {
                                            echo $row['DATE_DIFF_1'];
                                            }
                                            if($row['SHIPING_STATUS']==3)
                                            {
                                            echo $date_diff_2;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $shipment_status[$row['SHIPING_STATUS']]; ?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['DEALING_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['FACTORY_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_leader_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="100"><? echo $company_team_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="70" align='center'><? echo $shipment_mode[$row['SHIP_MODE']]; ?></td>
                                    <td width="100"><? echo $row['INTERNAL_REF']; ?></td>
                                    <td ><? echo $row['DETAILS_REMARKS']; ?></td>
                                </tr>
                            <?
                            $i++;
                        }
                    ?>
                </table>
            </div>
            <table width="4670" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="60" ></th>
                        <th width="100" ></th>
                        <th width="60" ></th>
                        <th width="80" id="smv_tot"><?=number_format($smv_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="70" ></th>
                        <th width="80" id="order_pcs_tot"><?=number_format($order_pcs_tot,2);?></th>
                        <th width="80" id="break_down_pcs_tot"><?=number_format($break_down_pcs_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="80" id="order_value_tot"><?=number_format($order_value_tot,2);?></th>
                        <th width="80" id="net_order_value_tot"><?=number_format($net_order_value_tot,2);?></th>
                        <th width="80" id="ex_factory_qnty_tot"><?=number_format($ex_factory_qnty_tot,2);?></th>
                        <th width="80" id="ex_factory_value_tot"><?=number_format($ex_factory_value_tot,2);?></th>
                        <th width="80" id="net_ex_factory_value_tot"><?=number_format($net_ex_factory_value_tot,2);?></th>
                        <th width="80" id="total_short_access_qnty"><?=number_format($total_short_access_qnty,2);?></th>
                        <th width="80" id="total_over_access_qnty"><?=number_format($total_over_access_qnty,2);?></th>
                        <th width="80" id="total_short_access_value"><?=number_format($total_short_access_value,2);?></th>
                        <th width="80" id="total_net_short_access_value"><?=number_format($total_net_short_access_value,2);?></th>
                        <th width="80" id="total_over_access_value"><?=number_format($total_over_access_value,2);?></th>
                        <th width="80" id="total_net_over_access_value"><?=number_format($total_net_over_access_value,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" id="total_ex_factory_brk_dwn_qty"><?=number_format($total_ex_factory_brk_dwn_qty,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" ></th>
                        <th width="100"></th>
                        <th ></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?
    }

	if($reportType==3)//Short Button
    {
        $date=date('d-m-Y');
		$sql_result="SELECT b.id as PO_ID, b.is_confirmed as IS_CONFIRMED, b.po_number as PO_NUMBER, b.pub_shipment_date as PUB_SHIPMENT_DATE,b.shipment_date as SHIPMENT_DATE, b.po_total_price as ORDER_VALUE ,b.unit_price as UNIT_PRICE, b.po_quantity as PO_QUANTITY, b.shiping_status as SHIPING_STATUS, b.po_received_date as PO_RECEIVED_DATE, b.details_remarks as DETAILS_REMARKS, b.delay_for as DELAY_FOR,b.grouping as INTERNAL_REF,b.file_no as FILE_NO, b.t_year as T_YEAR, b.t_month as T_MONTH, b.po_received_date, min(TRUNC(b.insert_date)) as INSERT_DATE,c.id as ID, c.buyer_name as BUYER_NAME, c.season_buyer_wise as SEASON_BUYER_WISE,c.pro_sub_dep as PRO_SUB_DEP,c.product_dept as PRODUCT_DEPT, c.ship_mode as SHIP_MODE, c.total_set_qnty as TOTAL_SET_QNTY, c.company_name as COMPANY_NAME, c.job_no as JOB_NO, c.agent_name as AGENT_NAME, c.style_ref_no as STYLE_REF_NO, c.style_description as STYLE_DESCRIPTION, c.job_quantity as JOB_QUANTITY, c.product_category as PRODUCT_CATEGORY, c.location_name as LOCATION_NAME, c.gmts_item_id as GMTS_ITEM_ID, c.order_uom as ORDER_UOM, c.team_leader as TEAM_LEADER, c.dealing_marchant as DEALING_MARCHANT, c.factory_marchant as FACTORY_MARCHANT, c.product_code as PRODUCT_CODE, c.brand_id as BRAND_ID, c.set_break_down as SET_BREAK_DOWN, sum(d.order_quantity) as ORDER_QUANTITY_PCS, (b.shipment_date - to_date('$date','dd-mm-yyyy')) as DATE_DIFF_1
        from wo_po_details_master c,wo_po_break_down b
        left join wo_po_color_size_breakdown d on d.po_break_down_id=b.id and d.status_active=1
        where b.job_no_mst=c.job_no and b.status_active=1 and c.status_active=1 and b.is_confirmed=1 $search_cond 
        group by  b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_total_price, b.unit_price, b.po_quantity, b.shiping_status, b.po_received_date, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.t_year, b.t_month,c.id , c.buyer_name, c.season_buyer_wise,c.pro_sub_dep,c.product_dept, c.ship_mode, c.total_set_qnty, c.company_name, c.job_no, c.agent_name, c.style_ref_no, c.style_description, c.job_quantity, c.product_category, c.location_name, c.gmts_item_id, c.order_uom, c.team_leader, c.dealing_marchant, c.factory_marchant, c.product_code, c.brand_id, c.set_break_down order by c.id";

        // echo $sql_result;

        $order_result=sql_select($sql_result);
        $po_array_for_cond=array();
        $job_array_for_cond=array();
        foreach ($order_result as $row_po_job)
        {
            $po_array_for_cond[$row_po_job["PO_ID"]]=$row_po_job["PO_ID"];
            $job_array_for_cond[$row_po_job["JOB_NO"]]="'".$row_po_job["JOB_NO"]."'";
        }

        $job_cond_for_in=where_con_using_array($job_array_for_cond,0,'job_no');
        $job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
        $job_smv_eff_arr=return_library_array( "select job_no, sew_effi_percent from wo_pre_cost_mst where 1=1 $job_cond_for_in",'job_no','sew_effi_percent');

        $po_cond_for_in=where_con_using_array($po_array_for_cond,0,'po_break_down_id');
        $exfactory_data=sql_select("SELECT po_break_down_id as PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY, MAX(ex_factory_date) as EX_FACTORY_DATE
		from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row)
		{
			$exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_qnty']=$exfatory_row['EX_FACTORY_QNTY']-$exfatory_row['EX_FACTORY_RETURN_QNTY'];
            $exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_date']=$exfatory_row['EX_FACTORY_DATE'];
		}
        unset($exfactory_data);

        $ac_po_sql=sql_select("SELECT id, po_break_down_id, acc_po_no from wo_po_acc_po_info where is_deleted=0 $po_cond_for_in $job_cond_for_in");
        $ac_po_arr=array();
        foreach($ac_po_sql as $row)
        {
            $ac_po_arr[$row["PO_BREAK_DOWN_ID"]].=$row["ACC_PO_NO"].", ";
        }
        unset($ac_po_sql);
        ob_start();
        ?>
        <style>
        table tbody tr td
        {
            word-break: break-word;
        }
        </style>
        <div style="margin:5px;width:100%;" >
            <table cellpadding="0" width="4490" cellspacing="0" border="1" rules="all" align='left' id="tbl_details">
                <tr>
                    <td align='center'><strong><?=$company_array[$cbo_company_name];?></strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Monthly Confirmed Order and Export Quantity with Value</strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Export Short</strong></td>
                </tr>
                <tr>
                    <td align='center'>Date Range : <?=$startDate.' - '.$endDate;?>&nbsp;&nbsp;&nbsp; Report Generate Time:<?=date("h:i:s a",time());?></td>
                </tr>
            </table>
            <br>
            <table cellpadding="0" width="4490" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">
                <thead >
                    <tr>
                        <th width="30" >SL</th>
                        <th width="100">Company Name</th>
                        <th width="100">Job No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Agent</th>
                        <th width="100">Order No</th>
                        <th width="100">Actual PO</th>
                        <th width="100">Season</th>
                        <th width="100">Pord. Dept.</th>
                        <th width="100">Sub. Dept</th>
                        <th width="100">Pord. Dept Code</th>
                        <th width="100">Brand</th>
                        <th width="100">Img</th>
                        <th width="100">Item</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Style Des</th>
                        <th width="100">PO Insert Date</th>
                        <th width="100">Pub Ship Date</th>
                        <th width="100">Original Ship Date</th>
                        <th width="60" >Lead Time</th>
                        <th width="100" >SMV</th>
                        <th width="60" >Eff. %</th>
                        <th width="80" >Total SMV</th>
                        <th width="80" >Order Qnty</th>
                        <th width="60" >Uom</th>
                        <th width="80" >Order Qnty (Pcs)</th>
                        <th width="80" >Break-down Qty (Pcs)</th>
                        <th width="80" >Per Unit Price</th>
                        <th width="80" >Gross Order Value ($)</th>
                        <th width="80" >Net Order Value ($)</th>
                        <th width="80" >Ex-Fac Qnty (Pcs)</th>
                        <th width="80" >Gross Ex-Fac Value ($)</th>
                        <th width="80" >Net Ex-Fac Value ($)</th>
                        <th width="80" >Ex-factory Bal. (Pcs)</th>
                        <th width="80" >Gross Ex-factory Bal. Value ($)</th>
                        <th width="80" >Net Ex-factory Bal. Value ($)</th>
                        <th width="80" >Short/ Over/At Per</th>
                        <th width="80" >Order Status</th>
                        <th width="80" >Prod. Catg</th>
                        <th width="80" >PO Rec. Date</th>
                        <th width="80" >Ex-fac. Bal. on Brk-dwn Qty (Pcs)</th>
                        <th width="80" >Days in Hand</th>
                        <th width="80" >Shipping Status</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Factory Merchandiser</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Team Name</th>
                        <th width="70" >Ship Mode</th>
                        <th width="100">Int. Ref/ Grouping</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:4490px"  align="left" id="scroll_body">
                <table width="4470" border="1" class="rpt_table" rules="all" id="export_details_tbl">
                    <?
                        $i=1;
                        foreach($order_result as $row)
                        {
                            $ex_factory_qnty=$exfactory_data_array[$row["PO_ID"]]['ex_factory_qnty'];
                            $short_access_qnty=(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'])-$ex_factory_qnty);
                            if($short_access_qnty>0)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF";
                                else $bgcolor="#FFFFFF";
                                $gmts_item_name="";
                                $gmts_item_id=explode(',',$row['GMTS_ITEM_ID']);
                                for($j=0; $j<count($gmts_item_id); $j++)
                                {
                                $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                                }
                                $poQtyPcs=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];
                                if($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']>$row['ORDER_QUANTITY_PCS']) $bgColorBreakDown="#ff0000";
                                else $bgColorBreakDown="";
                                $ex_factory_date=$exfactory_data_array[$row["PO_ID"]]['ex_factory_date'];
                                $date_diff_2=datediff( "d", $ex_factory_date , $row['SHIPMENT_DATE']);
                                if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                                {
                                    if($row['DATE_DIFF_1']>10){ $bgColorDaysHand="";}
                                    if($row['DATE_DIFF_1']<=10 && $row['DATE_DIFF_1']>=0){ $bgColorDaysHand="orange";}
                                    if($row['DATE_DIFF_1']<0){ $bgColorDaysHand="#ff0000";}
                                }
                                else
                                {
                                    if($row['SHIPING_STATUS']==3 && $date_diff_2 >=0 ) {$bgColorDaysHand="green";}
                                    if($row['SHIPING_STATUS']==3 &&  $date_diff_2<0) {$bgColorDaysHand="#2A9FFF";}
                                }
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                        <td width="30" align='center'><?=$i;?></td>
                                        <td width="100"><?echo $company_array[$row['COMPANY_NAME']];?></td>
                                        <td width="100"><?echo $row['JOB_NO'];?></td>
                                        <td width="100"><?echo $buyer_arr[$row['BUYER_NAME']];?></td>
                                        <td width="100"><?echo $buyer_arr[$row['AGENT_NAME']];?></td>
                                        <td width="100"><?echo $row['PO_NUMBER'];?></td>
                                        <td width="100"><?=rtrim($ac_po_arr[$row["PO_ID"]],", ");?></td>
                                        <td width="100"><?echo $season_arr[$row['SEASON_BUYER_WISE']];?></td>
                                        <td width="100"><?echo $product_dept[$row['PRODUCT_DEPT']];?></td>
                                        <td width="100"><?echo $sub_dep_arr[$row['PRO_SUB_DEP']];?></td>
                                        <td width="100"><?echo $row['PRODUCT_CODE'];?></td>
                                        <td width="100"><?echo $brand_arr[$row['BRAND_ID']];?></td>
                                        <td width="100" align='center'><img src='../../../<? echo $imge_arr[$row['JOB_NO']]; ?>' height='25'  /></td>
                                        <td width="100"><? echo rtrim($gmts_item_name,","); ?></td>
                                        <td width="100"><?echo $row['STYLE_REF_NO'];?></td>
                                        <td width="100"><?echo $row['STYLE_DESCRIPTION'];?></td>
                                        <td width="100" align='center'><? echo change_date_format($row['INSERT_DATE'],'dd-mm-yyyy','-');?></td>
                                        <td width="100" align='center'><? echo change_date_format($row['PUB_SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                        <td width="100" align='center'><? echo change_date_format($row['SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                        <td width="60" align='center'><? echo datediff('d',$row['PO_RECEIVED_DATE'],$row['SHIPMENT_DATE']);?></td>
                                        <td width="100" align='center'>
                                            <?
                                                $setSmvArr=array();
                                                foreach(explode('__',$row['SET_BREAK_DOWN']) as $setBrAr){
                                                    list($itemId,$setRa,$setSmv)=explode('_',$setBrAr);
                                                    $setSmvArr[]=$setSmv;
                                                    
                                                }
                                                echo "[ ".implode(',',$setSmvArr)." ], ";
                                                echo number_format($job_smv_arr[$row['JOB_NO']],2);
                                            ?>
                                        </td>
                                        <td width="60" align='right'><?=$job_smv_eff_arr[$row['JOB_NO']];?></td>
                                        <td width="80" align='right'>
                                            <?  $smv= ($job_smv_arr[$row['JOB_NO']])*$row['PO_QUANTITY']; $smv_tot+=$smv; echo fn_number_format($smv,2); ?>
                                        </td>
                                        <td width="80" align='right'><?echo $row['PO_QUANTITY'];?></td>
                                        <td width="60" align='center'><?echo $unit_of_measurement[$row['ORDER_UOM']];?></td>
                                        <td width="80" align='right'><?echo fn_number_format(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']),0);$order_pcs_tot+=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];?></td>
                                        <td width="80" align='right' bgcolor="<? echo $bgColorBreakDown; ?>"><?echo $row['ORDER_QUANTITY_PCS'];$break_down_pcs_tot+=$row['ORDER_QUANTITY_PCS']?></td>
                                        <td width="80" align='right'><? echo number_format($row['UNIT_PRICE'],2);?></td>
                                        <td width="80" align='right'><? echo number_format($row['ORDER_VALUE'],2);$order_value_tot+=$row['ORDER_VALUE'];?></td>
                                        <td width="80" align='right'>
                                            <? 
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100),2);$net_order_value_tot+=($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($row['ORDER_VALUE'],2);$net_order_value_tot+=$row['ORDER_VALUE'];
                                                }
                                            ?>
                                        </td>
                                        <td width="80" align='right'><? echo fn_number_format( $ex_factory_qnty,0);$ex_factory_qnty_tot+=$ex_factory_qnty; ?></td>
                                        <td width="80" align='right'>
                                            <? 
                                                $ex_factory_value=$ex_factory_qnty*($row['UNIT_PRICE']/$row['TOTAL_SET_QNTY']);
                                                echo fn_number_format( $ex_factory_value,2);$ex_factory_value_tot+=$ex_factory_value; 
                                            ?>
                                        </td>
                                        <td width="80" align='right'>                                        
                                            <? 
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100),2);$net_ex_factory_value_tot+=($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($ex_factory_value,2);$net_ex_factory_value_tot+=$ex_factory_value;
                                                }
                                            ?>
                                        </td>
                                        <td width="80" align='right'>
                                            <?
                                                $short_access_qnty=(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'])-$ex_factory_qnty);
                                                if($short_access_qnty>=0)
                                                {
                                                    echo fn_number_format($short_access_qnty,0);
                                                    $total_short_access_qnty+=$short_access_qnty;
                                                }
                                            ?>
                                        </td>
                                        <td width="80" align='right'>
                                            <?
                                            if($short_access_qnty>=0)
                                            {
                                                $short_access_value=($short_access_qnty/$row['TOTAL_SET_QNTY'])*$row['UNIT_PRICE'];
                                                echo fn_number_format($short_access_value,2);
                                                $total_short_access_value+=$short_access_value;
                                            }
                                            ?>
                                        </td>
                                        <td width="80" align='right'>
                                            <? 
                                                if($short_access_qnty>=0)
                                                {
                                                    if($commission_arr[$row['ID']]>0){
                                                        echo number_format($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100),2);$total_net_short_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                    }else{
                                                        echo number_format($short_access_value,2);$total_net_short_access_value+=$short_access_value;
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td width="80" align='center'>
                                            <?
                                                $short_over_shipment="";
                                                if(($poQtyPcs-$ex_factory_qnty)==0){$short_over_shipment="At Per";}
                                                else if($poQtyPcs<$ex_factory_qnty){$short_over_shipment= "Over Shipment";}
                                                else if($poQtyPcs>$ex_factory_qnty){$short_over_shipment="Short Shipment";}
                                                echo $short_over_shipment;
                                            ?>
                                        </td>
                                        <td width="80" align='center'><? echo $order_status[$row['IS_CONFIRMED']];?></td>
                                        <td width="80" align='center'><? echo $product_category[$row['PRODUCT_CATEGORY']];?></td>
                                        <td width="80" align='center'><? echo change_date_format($row['PO_RECEIVED_DATE'],'dd-mm-yyyy','-');?></td>
                                        <td width="80" align='center'><? echo fn_number_format($row['ORDER_QUANTITY_PCS']-$ex_factory_qnty);$total_ex_factory_brk_dwn_qty+=$row['ORDER_QUANTITY_PCS']-$ex_factory_qnty;?></td>
                                        <td width="80" align='center'  bgcolor="<? echo $bgColorDaysHand; ?>">
                                        <?
                                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                                            {
                                            echo $row['DATE_DIFF_1'];
                                            }
                                            if($row['SHIPING_STATUS']==3)
                                            {
                                            echo $date_diff_2;
                                            }
                                        ?>
                                        </td>
                                        <td width="80" align='center'><? echo $shipment_status[$row['SHIPING_STATUS']]; ?></td>
                                        <td width="100"><? echo $company_team_member_name_arr[$row['DEALING_MARCHANT']];?></td>
                                        <td width="100"><? echo $company_team_member_name_arr[$row['FACTORY_MARCHANT']];?></td>
                                        <td width="100"><? echo $company_team_leader_name_arr[$row['TEAM_LEADER']];?></td>
                                        <td width="100"><? echo $company_team_name_arr[$row['TEAM_LEADER']];?></td>
                                        <td width="70" align='center'><? echo $shipment_mode[$row['SHIP_MODE']]; ?></td>
                                        <td width="100"><? echo $row['INTERNAL_REF']; ?></td>
                                        <td ><? echo $row['DETAILS_REMARKS']; ?></td>
                                    </tr>
                                <?
                                $i++;
                            }
                        }
                    ?>
                </table>
            </div>
            <table width="4490" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot width="4490">
                    <tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="60" ></th>
                        <th width="100" ></th>
                        <th width="60" ></th>
                        <th width="80"  id="smv_tot"><?=number_format($smv_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="60" ></th>
                        <th width="80"  id="order_pcs_tot"><?=number_format($order_pcs_tot,2);?></th>
                        <th width="80"  id="break_down_pcs_tot"><?=number_format($break_down_pcs_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="80"  id="order_value_tot"><?=number_format($order_value_tot,2);?></th>
                        <th width="80"  id="order_value_tot"><?=number_format($net_order_value_tot,2);?></th>
                        <th width="80"  id="ex_factory_qnty_tot"><?=number_format($ex_factory_qnty_tot,2);?></th>
                        <th width="80"  id="ex_factory_value_tot"><?=number_format($ex_factory_value_tot,2);?></th>
                        <th width="80"  id="order_value_tot"><?=number_format($net_ex_factory_value_tot,2);?></th>
                        <th width="80"  id="total_short_access_qnty"><?=number_format($total_short_access_qnty,2);?></th>
                        <th width="80"  id="total_short_access_value"><?=number_format($total_short_access_value,2);?></th>
                        <th width="80"  id="order_value_tot"><?=number_format($total_net_short_access_value,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" id="total_ex_factory_brk_dwn_qty"><?=number_format($total_ex_factory_brk_dwn_qty,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" ></th>
                        <th width="100"></th>
                        <th ></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?
    }

    if($reportType==4)//Carried From Job
    {
        $date=date('d-m-Y');
		$sql_result="SELECT b.id as PO_ID, b.is_confirmed as IS_CONFIRMED, b.po_number as PO_NUMBER,b.pub_shipment_date as PUB_SHIPMENT_DATE, b.shipment_date as SHIPMENT_DATE, b.pub_shipment_date_prev as PUB_SHIPMENT_DATE_PREV,b.po_total_price as ORDER_VALUE ,b.unit_price as UNIT_PRICE, b.po_quantity as PO_QUANTITY, b.shiping_status as SHIPING_STATUS, b.po_received_date as PO_RECEIVED_DATE, b.details_remarks as DETAILS_REMARKS, b.delay_for as DELAY_FOR,b.grouping as INTERNAL_REF,b.file_no as FILE_NO, b.t_year as T_YEAR, b.t_month as T_MONTH, b.po_received_date, min(TRUNC(b.insert_date)) as INSERT_DATE,c.id as ID, c.buyer_name as BUYER_NAME, c.season_buyer_wise as SEASON_BUYER_WISE,c.pro_sub_dep as PRO_SUB_DEP,c.product_dept as PRODUCT_DEPT, c.ship_mode as SHIP_MODE, c.total_set_qnty as TOTAL_SET_QNTY, c.company_name as COMPANY_NAME, c.job_no as JOB_NO, c.agent_name as AGENT_NAME, c.style_ref_no as STYLE_REF_NO, c.style_description as STYLE_DESCRIPTION, c.job_quantity as JOB_QUANTITY, c.product_category as PRODUCT_CATEGORY, c.location_name as LOCATION_NAME, c.gmts_item_id as GMTS_ITEM_ID, c.order_uom as ORDER_UOM, c.team_leader as TEAM_LEADER, c.dealing_marchant as DEALING_MARCHANT, c.factory_marchant as FACTORY_MARCHANT, c.product_code as PRODUCT_CODE, c.brand_id as BRAND_ID, c.set_break_down as SET_BREAK_DOWN, sum(d.order_quantity) as ORDER_QUANTITY_PCS, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) as DATE_DIFF_1
        from wo_po_details_master c,wo_po_break_down b
        left join wo_po_color_size_breakdown d on d.po_break_down_id=b.id and d.status_active=1
        where b.job_no_mst=c.job_no and b.status_active=1 and c.status_active=1 and b.is_confirmed=1 $search_cond and b.pub_shipment_date_prev is not null
        group by  b.id, b.is_confirmed, b.po_number, b.shipment_date, b.pub_shipment_date,b.pub_shipment_date_prev, b.po_total_price, b.unit_price, b.po_quantity, b.shiping_status, b.po_received_date, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.t_year, b.t_month,c.id , c.buyer_name, c.season_buyer_wise,c.pro_sub_dep,c.product_dept, c.ship_mode, c.total_set_qnty, c.company_name, c.job_no, c.agent_name, c.style_ref_no, c.style_description, c.job_quantity, c.product_category, c.location_name, c.gmts_item_id, c.order_uom, c.team_leader, c.dealing_marchant, c.factory_marchant, c.product_code, c.brand_id, c.set_break_down order by b.pub_shipment_date";

        // echo $sql_result;die;
        $order_result=sql_select($sql_result);
        $po_array_for_cond=array();
        $job_array_for_cond=array();
        foreach ($order_result as $row_po_job)
        {
            $po_array_for_cond[$row_po_job['PO_ID']]=$row_po_job['PO_ID'];
            $job_array_for_cond[$row_po_job['JOB_NO']]="'".$row_po_job['JOB_NO']."'";
        }

        $job_cond_for_in=where_con_using_array($job_array_for_cond,0,'job_no');
        $job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
        $job_smv_eff_arr=return_library_array( "select job_no, sew_effi_percent from wo_pre_cost_mst where 1=1 $job_cond_for_in",'job_no','sew_effi_percent');

        $po_cond_for_in=where_con_using_array($po_array_for_cond,0,'po_break_down_id');
        $exfactory_data=sql_select("SELECT po_break_down_id as PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY, MAX(ex_factory_date) as EX_FACTORY_DATE
		from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row)
		{
			$exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_qnty']=$exfatory_row['EX_FACTORY_QNTY']-$exfatory_row['EX_FACTORY_RETURN_QNTY'];
            $exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_date']=$exfatory_row['EX_FACTORY_DATE'];
		}
        unset($exfactory_data);

        $ac_po_sql=sql_select("SELECT id, po_break_down_id, acc_po_no from wo_po_acc_po_info where is_deleted=0 $po_cond_for_in $job_cond_for_in");
        $ac_po_arr=array();
        foreach($ac_po_sql as $row)
        {
            $ac_po_arr[$row["PO_BREAK_DOWN_ID"]].=$row["ACC_PO_NO"].", ";
        }
        unset($ac_po_sql);

        $invoice_id_sql=sql_select("SELECT po_break_down_id, invoice_no as INVOICE_NO from pro_ex_factory_mst where status_active=1 and is_deleted=0 $po_cond_for_in ");
		$invoiceIdArr=$exFactoryPoIdArr=array();
		foreach($invoice_id_sql as $row)
		{
			if($row['INVOICE_NO']){$invoiceIdArr[$row['INVOICE_NO']]=$row['INVOICE_NO'];}
			$exFactoryPoIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
        unset($invoice_id_sql);

		$po_id_in=where_con_using_array($exFactoryPoIdArr,0,'b.po_breakdown_id');
		$invoice_id_in=where_con_using_array($invoiceIdArr,0,'a.id');
		$exInvoice_sql="SELECT a.id, a.INVOICE_VALUE, a.NET_INVO_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id $po_id_in $invoice_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$exInvoice_data=sql_select($exInvoice_sql);
		$exInvoiceArr=array();
		foreach($exInvoice_data as $row)
		{
			if($row['CURRENT_INVOICE_VALUE']){$exInvoiceArr[$row['PO_BREAKDOWN_ID']]+=($row['NET_INVO_VALUE']/$row['INVOICE_VALUE'])*$row['CURRENT_INVOICE_VALUE'];}
		}
        $div_width="4768";
        $tbl_width="4750";
        ob_start();
        ?>
        <style>
        table tbody tr td
        {
            word-break: break-word;
        }
        </style>
        <div style="margin:5px;width:100%;" >
            <table cellpadding="0" width="<?=$div_width;?>" cellspacing="0" border="1" rules="all" align='left' id="tbl_details">
                <tr>
                    <td align='center'><strong><?=$company_array[$cbo_company_name];?></strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Monthly Confirmed Order and Export Quantity with Value</strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Export Details</strong></td>
                </tr>
                <tr>
                    <td align='center'>Date Range : <?=$startDate.' - '.$endDate;?>&nbsp;&nbsp;&nbsp; Report Generate Time:<?=date("h:i:s a",time());?></td>
                </tr>
            </table>
            <br>
            <table width="<?=$div_width;?>" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">
                <thead>
                    <tr>
                        <th colspan="53" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Company Name</th>
                        <th width="100">Job No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Agent</th>
                        <th width="100">Order No</th>
                        <th width="100">Actual PO</th>
                        <th width="100">Season</th>
                        <th width="100">Pord. Dept.</th>
                        <th width="100">Sub. Dept</th>
                        <th width="100">Pord. Dept Code</th>
                        <th width="100">Brand</th>
                        <th width="100">Img</th>
                        <th width="100">Item</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Style Des</th>
                        <th width="100">PO Insert Date</th>
                        <th width="100">Pub Ship Date</th>
                        <th width="100">Original Ship Date</th>
                        <th width="100">Previous Ship Date</th>
                        <th width="60" >Lead Time</th>
                        <th width="100" >SMV</th>
                        <th width="60" >Eff. %</th>
                        <th width="80" >Total SMV</th>
                        <th width="80" >Order Qnty</th>
                        <th width="70" >Uom</th>
                        <th width="80" >Order Qnty (Pcs)</th>
                        <th width="80" >Break-down Qty (Pcs)</th>
                        <th width="80" >Per Unit Price</th>
                        <th width="80" >Gross Order Value ($)</th>
                        <th width="80" >Net Order Value ($)</th>
                        <th width="80" >Ex-Fac Qnty (Pcs)</th>
                        <th width="80" >Gross Ex-Fac Value ($)</th>
                        <th width="80" >Net Ex-Fac Value ($)</th>
                        <th width="80" >Ex-factory Bal. (Pcs)</th>
                        <th width="80" >Ex-factory Over (Pcs)</th>
                        <th width="80" >Gross Ex-factory Bal. Value ($)</th>
                        <th width="80" >Net Ex-factory Bal. Value ($)</th>
                        <th width="80" >Gross Ex-factory Over Value ($)</th>
                        <th width="80" >Net Ex-factory Over Value ($)</th>
                        <th width="80" >Short/ Over/At Per</th>
                        <th width="80" >Order Status</th>
                        <th width="80" >Prod. Catg</th>
                        <th width="80" >PO Rec. Date</th>
                        <th width="80" >Ex-fac. Bal. on Brk-dwn Qty (Pcs)</th>
                        <th width="80" >Days in Hand</th>
                        <th width="80" >Shipping Status</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Factory Merchandiser</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Team Name</th>
                        <th width="70" >Ship Mode</th>
                        <th width="100">Int. Ref/ Grouping</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:<?=$div_width;?>px"  align="left" id="scroll_body">
                <table width="<?=$tbl_width;?>" border="1" class="rpt_table" rules="all" id="export_details_tbl">
                    <?
                        $i=1;
                        foreach($order_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row['GMTS_ITEM_ID']);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                            $ex_factory_qnty=$exfactory_data_array[$row["PO_ID"]]['ex_factory_qnty'];
                            $poQtyPcs=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];
                            if($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']>$row['ORDER_QUANTITY_PCS']) $bgColorBreakDown="#ff0000";
                            else $bgColorBreakDown="";
                            $ex_factory_date=$exfactory_data_array[$row["PO_ID"]]['ex_factory_date'];
							$date_diff_2=datediff( "d", $ex_factory_date , $row['PUB_SHIPMENT_DATE']);
                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                            {
                                if($row['DATE_DIFF_1']>10){ $bgColorDaysHand="";}
                                if($row['DATE_DIFF_1']<=10 && $row['DATE_DIFF_1']>=0){ $bgColorDaysHand="orange";}
                                if($row['DATE_DIFF_1']<0){ $bgColorDaysHand="#ff0000";}
                            }
                            else
                            {
                                if($row['SHIPING_STATUS']==3 && $date_diff_2 >=0 ) $bgColorDaysHand="green";
                                if($row['SHIPING_STATUS']==3 &&  $date_diff_2<0) $bgColorDaysHand="#2A9FFF";
                            }
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                    <td width="30" align='center'><?=$i;?></td>
                                    <td width="100"><?echo $company_array[$row['COMPANY_NAME']];?></td>
                                    <td width="100"><?echo $row['JOB_NO'];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['BUYER_NAME']];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['AGENT_NAME']];?></td>
                                    <td width="100"><?echo $row['PO_NUMBER'];?></td>
                                    <td width="100"><?=rtrim($ac_po_arr[$row["PO_ID"]],", ");?></td>
                                    <td width="100"><?echo $season_arr[$row['SEASON_BUYER_WISE']];?></td>
                                    <td width="100"><?echo $product_dept[$row['PRODUCT_DEPT']];?></td>
                                    <td width="100"><?echo $sub_dep_arr[$row['PRO_SUB_DEP']];?></td>
                                    <td width="100"><?echo $row['PRODUCT_CODE'];?></td>
                                    <td width="100"><?echo $brand_arr[$row['BRAND_ID']];?></td>
                                    <td width="100" align='center'><img src='../../../<? echo $imge_arr[$row['JOB_NO']]; ?>' height='25'  /></td>
                                    <td width="100"><? echo rtrim($gmts_item_name,","); ?></td>
                                    <td width="100"><?echo $row['STYLE_REF_NO'];?></td>
                                    <td width="100"><?echo $row['STYLE_DESCRIPTION'];?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['INSERT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['PUB_SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['PUB_SHIPMENT_DATE_PREV'],'dd-mm-yyyy','-');?></td>
                                    <td width="60" align='center'><? echo datediff('d',$row['PO_RECEIVED_DATE'],$row['PUB_SHIPMENT_DATE']);?></td>
                                    <td width="100" align='center'>
                                        <?
                                            $setSmvArr=array();
                                            foreach(explode('__',$row['SET_BREAK_DOWN']) as $setBrAr){
                                                list($itemId,$setRa,$setSmv)=explode('_',$setBrAr);
                                                $setSmvArr[]=$setSmv;
                                                
                                            }
                                            echo "[ ".implode(',',$setSmvArr)." ], ";
                                            echo number_format($job_smv_arr[$row['JOB_NO']],2);
                                        ?>
                                    </td>
                                    <td width="60" align='right'><?=$job_smv_eff_arr[$row['JOB_NO']];?></td>
                                    <td width="80" align='right'>
                                        <?  $smv= ($job_smv_arr[$row['JOB_NO']])*$row['PO_QUANTITY']; $smv_tot+=$smv; echo fn_number_format($smv,2); ?>
                                    </td>
                                    <td width="80" align='right'><?echo $row['PO_QUANTITY'];?></td>
                                    <td width="70" align='center'><?echo $unit_of_measurement[$row['ORDER_UOM']];?></td>
                                    <td width="80" align='right'><?echo fn_number_format(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']),0);$order_pcs_tot+=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];?></td>
                                    <td width="80" align='right' bgcolor="<? echo $bgColorBreakDown; ?>"><?echo $row['ORDER_QUANTITY_PCS'];$break_down_pcs_tot+=$row['ORDER_QUANTITY_PCS']?></td>
                                    <td width="80" align='right'><? echo number_format($row['UNIT_PRICE'],2);?></td>
                                    <td width="80" align='right'><? echo number_format($row['ORDER_VALUE'],2);$order_value_tot+=$row['ORDER_VALUE'];?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($commission_arr[$row['ID']]>0){
                                                echo number_format($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100),2);
                                                $net_order_value_tot+=($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100));
                                            }else{
                                                echo number_format($row['ORDER_VALUE'],2);$net_order_value_tot+=$row['ORDER_VALUE'];
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'><? echo fn_number_format( $ex_factory_qnty,0);$ex_factory_qnty_tot+=$ex_factory_qnty; ?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            $ex_factory_value=$ex_factory_qnty*($row['UNIT_PRICE']/$row['TOTAL_SET_QNTY']);
                                            echo fn_number_format( $ex_factory_value,0);$ex_factory_value_tot+=$ex_factory_value; 
                                        ?>
                                    </td>
                                    <td width="80" align='right'>                                        
                                        <? 
                                            echo number_format($exInvoiceArr[$row["PO_ID"]],0);$net_ex_factory_value_tot+=$exInvoiceArr[$row["PO_ID"]];
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            $short_over_shipment="";
                                            $short_access_qnty=(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'])-$ex_factory_qnty);
                                            if($short_access_qnty>=0)
                                            {
                                                echo fn_number_format($short_access_qnty,0);
                                                $total_short_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                            echo fn_number_format(ltrim($short_access_qnty,'-'),0);
                                            $total_over_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                        if($short_access_qnty>=0)
                                        {
                                            $short_access_value=($short_access_qnty/$row['TOTAL_SET_QNTY'])*$row['UNIT_PRICE'];
                                            echo fn_number_format($short_access_value,2);
                                            $total_short_access_value+=$short_access_value;
                                        }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($short_access_qnty>=0)
                                            {
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100),2);
                                                    $total_net_short_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($short_access_value,2);$total_net_short_access_value+=$short_access_value;
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                                $short_over_value=ltrim(($short_access_qnty/$row[csf('total_set_qnty')])*$row[csf('unit_price')],'-');
                                                echo  fn_number_format($short_over_value,2);
                                                $total_over_access_value+=$short_over_value;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($short_access_qnty<0)
                                            {
                                                echo number_format(($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value,2);
                                                $total_net_over_access_value+=($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value;
                                            }

                                        ?>
                                    </td>
                                    <td width="80" align='center'>
                                        <?
                                            $short_over_shipment="";
                                            if(($poQtyPcs-$ex_factory_qnty)==0){$short_over_shipment="At Per";}
                                            else if($poQtyPcs<$ex_factory_qnty){$short_over_shipment= "Over Shipment";}
                                            else if($poQtyPcs>$ex_factory_qnty){$short_over_shipment="Short Shipment";}
                                            echo $short_over_shipment;
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $order_status[$row['IS_CONFIRMED']];?></td>
                                    <td width="80" align='center'><? echo $product_category[$row['PRODUCT_CATEGORY']];?></td>
                                    <td width="80" align='center'><? echo change_date_format($row['PO_RECEIVED_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="80" align='center'><? echo fn_number_format($row['ORDER_QUANTITY_PCS']-$ex_factory_qnty);$total_ex_factory_brk_dwn_qty+=$row['ORDER_QUANTITY_PCS']-$ex_factory_qnty;?></td>
                                    <td width="80" align='center'  bgcolor="<? echo $bgColorDaysHand; ?>">
                                        <?
                                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                                            {
                                            echo $row['DATE_DIFF_1'];
                                            }
                                            if($row['SHIPING_STATUS']==3)
                                            {
                                            echo $date_diff_2;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $shipment_status[$row['SHIPING_STATUS']]; ?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['DEALING_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['FACTORY_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_leader_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="100"><? echo $company_team_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="70" align='center'><? echo $shipment_mode[$row['SHIP_MODE']]; ?></td>
                                    <td width="100"><? echo $row['INTERNAL_REF']; ?></td>
                                    <td ><? echo $row['DETAILS_REMARKS']; ?></td>
                                </tr>
                            <?
                            $i++;
                        }
                    ?>
                </table>
            </div>
            <table width="<?=$div_width;?>" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="60" ></th>
                        <th width="100" ></th>
                        <th width="60" ></th>
                        <th width="80" id="smv_tot"><?=number_format($smv_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="70" ></th>
                        <th width="80" id="order_pcs_tot"><?=number_format($order_pcs_tot,2);?></th>
                        <th width="80" id="break_down_pcs_tot"><?=number_format($break_down_pcs_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="80" id="order_value_tot"><?=number_format($order_value_tot,2);?></th>
                        <th width="80" id="net_order_value_tot"><?=number_format($net_order_value_tot,2);?></th>
                        <th width="80" id="ex_factory_qnty_tot"><?=number_format($ex_factory_qnty_tot,2);?></th>
                        <th width="80" id="ex_factory_value_tot"><?=number_format($ex_factory_value_tot,2);?></th>
                        <th width="80" id="net_ex_factory_value_tot"><?=number_format($net_ex_factory_value_tot,2);?></th>
                        <th width="80" id="total_short_access_qnty"><?=number_format($total_short_access_qnty,2);?></th>
                        <th width="80" id="total_over_access_qnty"><?=number_format($total_over_access_qnty,2);?></th>
                        <th width="80" id="total_short_access_value"><?=number_format($total_short_access_value,2);?></th>
                        <th width="80" id="total_net_short_access_value"><?=number_format($total_net_short_access_value,2);?></th>
                        <th width="80" id="total_over_access_value"><?=number_format($total_over_access_value,2);?></th>
                        <th width="80" id="total_net_over_access_value"><?=number_format($total_net_over_access_value,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" id="total_ex_factory_brk_dwn_qty"><?=number_format($total_ex_factory_brk_dwn_qty,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" ></th>
                        <th width="100"></th>
                        <th ></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?
    }

    if($reportType==5)//Excluding Act. PO Button
    {
        $date=date('d-m-Y');
		$sql_result="SELECT b.id as PO_ID, b.is_confirmed as IS_CONFIRMED, b.po_number as PO_NUMBER, b.pub_shipment_date as PUB_SHIPMENT_DATE, b.shipment_date as SHIPMENT_DATE, b.po_total_price as ORDER_VALUE ,b.unit_price as UNIT_PRICE, b.po_quantity as PO_QUANTITY, b.shiping_status as SHIPING_STATUS, b.po_received_date as PO_RECEIVED_DATE, b.details_remarks as DETAILS_REMARKS, b.delay_for as DELAY_FOR,b.grouping as INTERNAL_REF,b.file_no as FILE_NO, b.t_year as T_YEAR, b.t_month as T_MONTH, b.po_received_date, min(TRUNC(b.insert_date)) as INSERT_DATE,c.id as ID, c.buyer_name as BUYER_NAME, c.season_buyer_wise as SEASON_BUYER_WISE,c.pro_sub_dep as PRO_SUB_DEP,c.product_dept as PRODUCT_DEPT, c.ship_mode as SHIP_MODE, c.total_set_qnty as TOTAL_SET_QNTY, c.company_name as COMPANY_NAME, c.job_no as JOB_NO, c.agent_name as AGENT_NAME, c.style_ref_no as STYLE_REF_NO, c.style_description as STYLE_DESCRIPTION, c.job_quantity as JOB_QUANTITY, c.product_category as PRODUCT_CATEGORY, c.location_name as LOCATION_NAME, c.gmts_item_id as GMTS_ITEM_ID, c.order_uom as ORDER_UOM, c.team_leader as TEAM_LEADER, c.dealing_marchant as DEALING_MARCHANT, c.factory_marchant as FACTORY_MARCHANT, c.product_code as PRODUCT_CODE, c.brand_id as BRAND_ID, c.set_break_down as SET_BREAK_DOWN, sum(d.order_quantity) as ORDER_QUANTITY_PCS, (b.shipment_date - to_date('$date','dd-mm-yyyy')) as DATE_DIFF_1
        from wo_po_details_master c,wo_po_break_down b
        left join wo_po_color_size_breakdown d on d.po_break_down_id=b.id and d.status_active=1
        where b.job_no_mst=c.job_no and b.status_active=1 and c.status_active=1 
        and b.id not in (SELECT PO_BREAK_DOWN_ID FROM wo_po_acc_po_info WHERE ACC_PO_NO IS NOT NULL)
        and b.is_confirmed=1 $search_cond 
        group by  b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_total_price, b.unit_price, b.po_quantity, b.shiping_status, b.po_received_date, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.t_year, b.t_month,c.id , c.buyer_name, c.season_buyer_wise,c.pro_sub_dep,c.product_dept, c.ship_mode, c.total_set_qnty, c.company_name, c.job_no, c.agent_name, c.style_ref_no, c.style_description, c.job_quantity, c.product_category, c.location_name, c.gmts_item_id, c.order_uom, c.team_leader, c.dealing_marchant, c.factory_marchant, c.product_code, c.brand_id, c.set_break_down order by c.id";

       //  echo $sql_result;

        $order_result=sql_select($sql_result);
        $po_array_for_cond=array();
        $job_array_for_cond=array();
        foreach ($order_result as $row_po_job)
        {
            $po_array_for_cond[$row_po_job['PO_ID']]=$row_po_job['PO_ID'];
            $job_array_for_cond[$row_po_job['JOB_NO']]="'".$row_po_job['JOB_NO']."'";
        }

        $job_cond_for_in=where_con_using_array($job_array_for_cond,0,'job_no');
        $job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
        $job_smv_eff_arr=return_library_array( "select job_no, sew_effi_percent from wo_pre_cost_mst where 1=1 $job_cond_for_in",'job_no','sew_effi_percent');
        
        $po_cond_for_in=where_con_using_array($po_array_for_cond,0,'po_break_down_id');
        $exfactory_data=sql_select("SELECT po_break_down_id as PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY, MAX(ex_factory_date) as EX_FACTORY_DATE
		from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row)
		{
			$exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_qnty']=$exfatory_row['EX_FACTORY_QNTY']-$exfatory_row['EX_FACTORY_RETURN_QNTY'];
            $exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_date']=$exfatory_row['EX_FACTORY_DATE'];
		}
        unset($exfactory_data);

        $ac_po_sql=sql_select("SELECT id, po_break_down_id, acc_po_no from wo_po_acc_po_info where is_deleted=0 $po_cond_for_in $job_cond_for_in");

        $ac_po_arr=array();
        foreach($ac_po_sql as $row)
        {
            $ac_po_arr[$row["PO_BREAK_DOWN_ID"]].=$row["ACC_PO_NO"].", ";
        }
        unset($ac_po_sql);

        $invoice_id_sql=sql_select("SELECT po_break_down_id, invoice_no as INVOICE_NO from pro_ex_factory_mst where status_active=1 and is_deleted=0 $po_cond_for_in ");
		$invoiceIdArr=$exFactoryPoIdArr=array();
		foreach($invoice_id_sql as $row)
		{
			if($row['INVOICE_NO']){$invoiceIdArr[$row['INVOICE_NO']]=$row['INVOICE_NO'];}
			$exFactoryPoIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
        unset($invoice_id_sql);

		$po_id_in=where_con_using_array($exFactoryPoIdArr,0,'b.po_breakdown_id');
		$invoice_id_in=where_con_using_array($invoiceIdArr,0,'a.id');
		$exInvoice_sql="SELECT a.id, a.INVOICE_VALUE, a.NET_INVO_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id $po_id_in $invoice_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$exInvoice_data=sql_select($exInvoice_sql);
		$exInvoiceArr=array();
		foreach($exInvoice_data as $row)
		{
			if($row['CURRENT_INVOICE_VALUE']){$exInvoiceArr[$row['PO_BREAKDOWN_ID']]+=($row['NET_INVO_VALUE']/$row['INVOICE_VALUE'])*$row['CURRENT_INVOICE_VALUE'];}
		}
        unset($exInvoice_data);
        ob_start();
        ?>
        <style>
        table tbody tr td
        {
            word-break: break-word;
        }
        </style>
        <div style="margin:5px;width:100%;" >
            <table cellpadding="0" width="4570" cellspacing="0" border="1" rules="all" align='left' id="tbl_details">
                <tr>
                    <td align='center'><strong><?=$company_array[$cbo_company_name];?></strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Monthly Confirmed Order and Export Quantity with Value</strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Export Details</strong></td>
                </tr>
                <tr>
                    <td align='center'>Date Range : <?=$startDate.' - '.$endDate;?>&nbsp;&nbsp;&nbsp; Report Generate Time:<?=date("h:i:s a",time());?></td>
                </tr>
            </table>
            <br>
            <table width="4570" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Company Name</th>
                        <th width="100">Job No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Agent</th>
                        <th width="100">Order No</th>
                        <!-- <th width="100">Actual PO</th> -->
                        <th width="100">Season</th>
                        <th width="100">Pord. Dept.</th>
                        <th width="100">Sub. Dept</th>
                        <th width="100">Pord. Dept Code</th>
                        <th width="100">Brand</th>
                        <th width="100">Img</th>
                        <th width="100">Item</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Style Des</th>
                        <th width="100">PO Insert Date</th>
                        <th width="100">Pub Ship Date</th>
                        <th width="100">Original Ship Date</th>
                        <th width="60" >Lead Time</th>
                        <th width="100">SMV</th>
                        <th width="60" >Eff. %</th>
                        <th width="80" >Total SMV</th>
                        <th width="80" >Order Qnty</th>
                        <th width="70" >Uom</th>
                        <th width="80" >Order Qnty (Pcs)</th>
                        <th width="80" >Break-down Qty (Pcs)</th>
                        <th width="80" >Per Unit Price</th>
                        <th width="80" >Gross Order Value ($)</th>
                        <th width="80" >Net Order Value ($)</th>
                        <th width="80" >Ex-Fac Qnty (Pcs)</th>
                        <th width="80" >Gross Ex-Fac Value ($)</th>
                        <th width="80" >Net Ex-Fac Value ($)</th>
                        <th width="80" >Ex-factory Bal. (Pcs)</th>
                        <th width="80" >Ex-factory Over (Pcs)</th>
                        <th width="80" >Gross Ex-factory Bal. Value ($)</th>
                        <th width="80" >Net Ex-factory Bal. Value ($)</th>
                        <th width="80" >Gross Ex-factory Over Value ($)</th>
                        <th width="80" >Net Ex-factory Over Value ($)</th>
                        <th width="80" >Short/ Over/At Per</th>
                        <th width="80" >Order Status</th>
                        <th width="80" >Prod. Catg</th>
                        <th width="80" >PO Rec. Date</th>
                        <th width="80" >Ex-fac. Bal. on Brk-dwn Qty (Pcs)</th>
                        <th width="80" >Days in Hand</th>
                        <th width="80" >Shipping Status</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Factory Merchandiser</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Team Name</th>
                        <th width="70" >Ship Mode</th>
                        <th width="100">Int. Ref/ Grouping</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:4570px"  align="left" id="scroll_body">
                <table width="4550" border="1" class="rpt_table" rules="all" id="export_details_tbl">
                    <?
                        $i=1;
                        foreach($order_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row['GMTS_ITEM_ID']);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                            $ex_factory_qnty=$exfactory_data_array[$row["PO_ID"]]['ex_factory_qnty'];
                            $poQtyPcs=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];
                            if($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']>$row['ORDER_QUANTITY_PCS']) $bgColorBreakDown="#ff0000";
                            else $bgColorBreakDown="";
                            $ex_factory_date=$exfactory_data_array[$row["PO_ID"]]['ex_factory_date'];
							$date_diff_2=datediff( "d", $ex_factory_date , $row['SHIPMENT_DATE']);
                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                            {
                                if($row['DATE_DIFF_1']>10){ $bgColorDaysHand="";}
                                if($row['DATE_DIFF_1']<=10 && $row['DATE_DIFF_1']>=0){ $bgColorDaysHand="orange";}
                                if($row['DATE_DIFF_1']<0){ $bgColorDaysHand="#ff0000";}
                            }
                            else
                            {
                                if($row['SHIPING_STATUS']==3 && $date_diff_2 >=0 ) $bgColorDaysHand="green";
                                if($row['SHIPING_STATUS']==3 &&  $date_diff_2<0) $bgColorDaysHand="#2A9FFF";
                            }

                            print_r($$ac_po_arr[$row["PO_ID"]]);
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                    <td width="30" align='center'><?=$i;?></td>
                                    <td width="100"><?echo $company_array[$row['COMPANY_NAME']];?></td>
                                    <td width="100"><?echo $row['JOB_NO'];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['BUYER_NAME']];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['AGENT_NAME']];?></td>
                                    <td width="100"><?echo $row['PO_NUMBER'];?></td>
                                    <!-- <td width="100"><?//=rtrim($ac_po_arr[$row["PO_ID"]],", ");?></td> -->
                                    <td width="100"><?echo $season_arr[$row['SEASON_BUYER_WISE']];?></td>
                                    <td width="100"><?echo $product_dept[$row['PRODUCT_DEPT']];?></td>
                                    <td width="100"><?echo $sub_dep_arr[$row['PRO_SUB_DEP']];?></td>
                                    <td width="100"><?echo $row['PRODUCT_CODE'];?></td>
                                    <td width="100"><?echo $brand_arr[$row['BRAND_ID']];?></td>
                                    <td width="100" align='center'><img src='../../../<? echo $imge_arr[$row['JOB_NO']]; ?>' height='25'  /></td>
                                    <td width="100"><? echo rtrim($gmts_item_name,","); ?></td>
                                    <td width="100"><?echo $row['STYLE_REF_NO'];?></td>
                                    <td width="100"><?echo $row['STYLE_DESCRIPTION'];?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['INSERT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['PUB_SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="60" align='center'><? echo datediff('d',$row['PO_RECEIVED_DATE'],$row['SHIPMENT_DATE']);?></td>
                                    <td width="100" align='center'>
                                        <?
                                            $setSmvArr=array();
                                            foreach(explode('__',$row['SET_BREAK_DOWN']) as $setBrAr){
                                                list($itemId,$setRa,$setSmv)=explode('_',$setBrAr);
                                                $setSmvArr[]=$setSmv;
                                                
                                            }
                                            echo "[ ".implode(',',$setSmvArr)." ], ";
                                            echo number_format($job_smv_arr[$row['JOB_NO']],2);
                                        ?>
                                    </td>
                                    <td width="60" align='right'><?=$job_smv_eff_arr[$row['JOB_NO']];?></td>
                                    <td width="80" align='right'>
                                        <?  $smv= ($job_smv_arr[$row['JOB_NO']])*$row['PO_QUANTITY']; $smv_tot+=$smv; echo fn_number_format($smv,2); ?>
                                    </td>
                                    <td width="80" align='right'><?echo $row['PO_QUANTITY'];?></td>
                                    <td width="70" align='center'><?echo $unit_of_measurement[$row['ORDER_UOM']];?></td>
                                    <td width="80" align='right'><?echo fn_number_format(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY']),0);$order_pcs_tot+=$row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'];?></td>
                                    <td width="80" align='right' bgcolor="<? echo $bgColorBreakDown; ?>"><?echo $row['ORDER_QUANTITY_PCS'];$break_down_pcs_tot+=$row['ORDER_QUANTITY_PCS']?></td>
                                    <td width="80" align='right'><? echo number_format($row['UNIT_PRICE'],2);?></td>
                                    <td width="80" align='right'><? echo number_format($row['ORDER_VALUE'],2);$order_value_tot+=$row['ORDER_VALUE'];?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($commission_arr[$row['ID']]>0){
                                                echo number_format($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100),2);
                                                $net_order_value_tot+=($row['ORDER_VALUE']-(($row['ORDER_VALUE']*$commission_arr[$row['ID']])/100));
                                            }else{
                                                echo number_format($row['ORDER_VALUE'],2);$net_order_value_tot+=$row['ORDER_VALUE'];
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'><? echo fn_number_format( $ex_factory_qnty,0);$ex_factory_qnty_tot+=$ex_factory_qnty; ?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            $ex_factory_value=$ex_factory_qnty*($row['UNIT_PRICE']/$row['TOTAL_SET_QNTY']);
                                            echo fn_number_format( $ex_factory_value,0);$ex_factory_value_tot+=$ex_factory_value; 
                                        ?>
                                    </td>
                                    <td width="80" align='right'>                                        
                                        <? 
                                            /*if($commission_arr[$row['ID']]>0){
                                                echo number_format($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100),0);
                                                $net_ex_factory_value_tot+=($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100));
                                            }else{
                                                echo number_format($ex_factory_value,0);$net_ex_factory_value_tot+=$ex_factory_value;
                                            }*/
                                            echo number_format($exInvoiceArr[$row["PO_ID"]],0);$net_ex_factory_value_tot+=$exInvoiceArr[$row["PO_ID"]];
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            $short_over_shipment="";
                                            $short_access_qnty=(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'])-$ex_factory_qnty);
                                            if($short_access_qnty>=0)
                                            {
                                                echo fn_number_format($short_access_qnty,0);
                                                $total_short_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                            echo fn_number_format(ltrim($short_access_qnty,'-'),0);
                                            $total_over_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                        if($short_access_qnty>=0)
                                        {
                                            $short_access_value=($short_access_qnty/$row['TOTAL_SET_QNTY'])*$row['UNIT_PRICE'];
                                            echo fn_number_format($short_access_value,2);
                                            $total_short_access_value+=$short_access_value;
                                        }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($short_access_qnty>=0)
                                            {
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100),2);
                                                    $total_net_short_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($short_access_value,2);$total_net_short_access_value+=$short_access_value;
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                                $short_over_value=ltrim(($short_access_qnty/$row[csf('total_set_qnty')])*$row[csf('unit_price')],'-');
                                                echo  fn_number_format($short_over_value,2);
                                                $total_over_access_value+=$short_over_value;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            /*if($short_access_qnty<0)
                                            {
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($short_over_value-(($short_over_value*$commission_arr[$row['ID']])/100),2);
                                                    $total_net_over_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($short_over_value,2);$total_net_over_access_value+=$short_over_value;
                                                }
                                            }*/
                                            if($short_access_qnty<0)
                                            {
                                                echo number_format(($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value,2);
                                                $total_net_over_access_value+=($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value;
                                            }

                                        ?>
                                    </td>
                                    <td width="80" align='center'>
                                        <?
                                            $short_over_shipment="";
                                            if(($poQtyPcs-$ex_factory_qnty)==0){$short_over_shipment="At Per";}
                                            else if($poQtyPcs<$ex_factory_qnty){$short_over_shipment= "Over Shipment";}
                                            else if($poQtyPcs>$ex_factory_qnty){$short_over_shipment="Short Shipment";}
                                            echo $short_over_shipment;
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $order_status[$row['IS_CONFIRMED']];?></td>
                                    <td width="80" align='center'><? echo $product_category[$row['PRODUCT_CATEGORY']];?></td>
                                    <td width="80" align='center'><? echo change_date_format($row['PO_RECEIVED_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="80" align='center'><? echo fn_number_format($row['ORDER_QUANTITY_PCS']-$ex_factory_qnty);$total_ex_factory_brk_dwn_qty+=$row['ORDER_QUANTITY_PCS']-$ex_factory_qnty;?></td>
                                    <td width="80" align='center'  bgcolor="<? echo $bgColorDaysHand; ?>">
                                        <?
                                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                                            {
                                            echo $row['DATE_DIFF_1'];
                                            }
                                            if($row['SHIPING_STATUS']==3)
                                            {
                                            echo $date_diff_2;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $shipment_status[$row['SHIPING_STATUS']]; ?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['DEALING_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['FACTORY_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_leader_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="100"><? echo $company_team_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="70" align='center'><? echo $shipment_mode[$row['SHIP_MODE']]; ?></td>
                                    <td width="100"><? echo $row['INTERNAL_REF']; ?></td>
                                    <td ><? echo $row['DETAILS_REMARKS']; ?></td>
                                </tr>
                            <?
                            $i++;
                        }
                    ?>
                </table>
            </div>
            <table width="4570" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <!-- <th width="100"></th> -->
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="60" ></th>
                        <th width="100" ></th>
                        <th width="60" ></th>
                        <th width="80" id="smv_tot"><?=number_format($smv_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="70" ></th>
                        <th width="80" id="order_pcs_tot"><?=number_format($order_pcs_tot,2);?></th>
                        <th width="80" id="break_down_pcs_tot"><?=number_format($break_down_pcs_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="80" id="order_value_tot"><?=number_format($order_value_tot,2);?></th>
                        <th width="80" id="net_order_value_tot"><?=number_format($net_order_value_tot,2);?></th>
                        <th width="80" id="ex_factory_qnty_tot"><?=number_format($ex_factory_qnty_tot,2);?></th>
                        <th width="80" id="ex_factory_value_tot"><?=number_format($ex_factory_value_tot,2);?></th>
                        <th width="80" id="net_ex_factory_value_tot"><?=number_format($net_ex_factory_value_tot,2);?></th>
                        <th width="80" id="total_short_access_qnty"><?=number_format($total_short_access_qnty,2);?></th>
                        <th width="80" id="total_over_access_qnty"><?=number_format($total_over_access_qnty,2);?></th>
                        <th width="80" id="total_short_access_value"><?=number_format($total_short_access_value,2);?></th>
                        <th width="80" id="total_net_short_access_value"><?=number_format($total_net_short_access_value,2);?></th>
                        <th width="80" id="total_over_access_value"><?=number_format($total_over_access_value,2);?></th>
                        <th width="80" id="total_net_over_access_value"><?=number_format($total_net_over_access_value,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" id="total_ex_factory_brk_dwn_qty"><?=number_format($total_ex_factory_brk_dwn_qty,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" ></th>
                        <th width="100"></th>
                        <th ></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?
    }

    if($reportType==6)//INcluding Act. PO Button
    {
        $date=date('d-m-Y');
		// $sql_result="SELECT b.id as PO_ID, b.is_confirmed as IS_CONFIRMED, b.po_number as PO_NUMBER, b.pub_shipment_date as PUB_SHIPMENT_DATE, b.shipment_date as SHIPMENT_DATE, b.po_total_price as ORDER_VALUE ,b.unit_price as UNIT_PRICE, b.po_quantity as PO_QUANTITY, b.shiping_status as SHIPING_STATUS, b.po_received_date as PO_RECEIVED_DATE, b.details_remarks as DETAILS_REMARKS, b.delay_for as DELAY_FOR,b.grouping as INTERNAL_REF,b.file_no as FILE_NO, b.t_year as T_YEAR, b.t_month as T_MONTH, b.po_received_date, min(TRUNC(b.insert_date)) as INSERT_DATE,c.id as ID, c.buyer_name as BUYER_NAME, c.season_buyer_wise as SEASON_BUYER_WISE,c.pro_sub_dep as PRO_SUB_DEP,c.product_dept as PRODUCT_DEPT, c.ship_mode as SHIP_MODE, c.total_set_qnty as TOTAL_SET_QNTY, c.company_name as COMPANY_NAME, c.job_no as JOB_NO, c.agent_name as AGENT_NAME, c.style_ref_no as STYLE_REF_NO, c.style_description as STYLE_DESCRIPTION, c.job_quantity as JOB_QUANTITY, c.product_category as PRODUCT_CATEGORY, c.location_name as LOCATION_NAME, c.gmts_item_id as GMTS_ITEM_ID, c.order_uom as ORDER_UOM, c.team_leader as TEAM_LEADER, c.dealing_marchant as DEALING_MARCHANT, c.factory_marchant as FACTORY_MARCHANT, c.product_code as PRODUCT_CODE, c.brand_id as BRAND_ID, c.set_break_down as SET_BREAK_DOWN, sum(d.order_quantity) as ORDER_QUANTITY_PCS, (b.shipment_date - to_date('$date','dd-mm-yyyy')) as DATE_DIFF_1
        // from wo_po_details_master c,wo_po_break_down b
        // left join wo_po_color_size_breakdown d on d.po_break_down_id=b.id and d.status_active=1
        // where b.job_no_mst=c.job_no and b.status_active=1 and c.status_active=1 
        // and b.id  in (SELECT PO_BREAK_DOWN_ID FROM wo_po_acc_po_info WHERE ACC_PO_NO IS NOT NULL)
        // and b.is_confirmed=1 $search_cond 
        // group by  b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_total_price, b.unit_price, b.po_quantity, b.shiping_status, b.po_received_date, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.t_year, b.t_month,c.id , c.buyer_name, c.season_buyer_wise,c.pro_sub_dep,c.product_dept, c.ship_mode, c.total_set_qnty, c.company_name, c.job_no, c.agent_name, c.style_ref_no, c.style_description, c.job_quantity, c.product_category, c.location_name, c.gmts_item_id, c.order_uom, c.team_leader, c.dealing_marchant, c.factory_marchant, c.product_code, c.brand_id, c.set_break_down order by c.id";

         $sql_result="SELECT e.acc_po_no ,e.acc_po_qty,e.acc_ship_date,e.acc_po_value,e.acc_rcv_date, b.id as PO_ID, b.is_confirmed as IS_CONFIRMED, b.po_number as PO_NUMBER, b.pub_shipment_date as PUB_SHIPMENT_DATE, b.shipment_date as SHIPMENT_DATE, b.po_total_price as ORDER_VALUE ,b.unit_price as UNIT_PRICE, b.po_quantity as PO_QUANTITY, b.shiping_status as SHIPING_STATUS, b.po_received_date as PO_RECEIVED_DATE, b.details_remarks as DETAILS_REMARKS, b.delay_for as DELAY_FOR,b.grouping as INTERNAL_REF,b.file_no as FILE_NO, b.t_year as T_YEAR, b.t_month as T_MONTH, b.po_received_date, min(TRUNC(e.insert_date)) as INSERT_DATE,c.id as ID, c.buyer_name as BUYER_NAME, c.season_buyer_wise as SEASON_BUYER_WISE,c.pro_sub_dep as PRO_SUB_DEP,c.product_dept as PRODUCT_DEPT, c.ship_mode as SHIP_MODE, c.total_set_qnty as TOTAL_SET_QNTY, c.company_name as COMPANY_NAME, c.job_no as JOB_NO, c.agent_name as AGENT_NAME, c.style_ref_no as STYLE_REF_NO, c.style_description as STYLE_DESCRIPTION, c.job_quantity as JOB_QUANTITY, c.product_category as PRODUCT_CATEGORY, c.location_name as LOCATION_NAME, c.gmts_item_id as GMTS_ITEM_ID, c.order_uom as ORDER_UOM, c.team_leader as TEAM_LEADER, c.dealing_marchant as DEALING_MARCHANT, c.factory_marchant as FACTORY_MARCHANT, c.product_code as PRODUCT_CODE, c.brand_id as BRAND_ID, c.set_break_down as SET_BREAK_DOWN, sum(d.po_qty) as ORDER_QUANTITY_PCS, (e.acc_ship_date - to_date('$date','dd-mm-yyyy')) as DATE_DIFF_1
        from wo_po_details_master c,wo_po_break_down b , wo_po_acc_po_info  e
        left join wo_po_acc_po_info_dtls d on d.mst_id=e.id and d.status_active=1
        where b.id = e.PO_BREAK_DOWN_ID
        AND c.status_active=1 
        AND e.ACC_PO_NO IS NOT NULL and b.job_no_mst=c.job_no and b.status_active=1 and c.status_active=1 and  e.status_active=1
        and b.is_confirmed=1 $search_cond 
        group by e.acc_po_no,e.acc_po_qty,b.id,e.acc_ship_date,e.acc_po_value,e.acc_rcv_date, b.is_confirmed, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_total_price, b.unit_price, b.po_quantity, b.shiping_status, b.po_received_date, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.t_year, b.t_month,c.id , c.buyer_name, c.season_buyer_wise,c.pro_sub_dep,c.product_dept, c.ship_mode, c.total_set_qnty, c.company_name, c.job_no, c.agent_name, c.style_ref_no, c.style_description, c.job_quantity, c.product_category, c.location_name, c.gmts_item_id, c.order_uom, c.team_leader, c.dealing_marchant, c.factory_marchant, c.product_code, c.brand_id, c.set_break_down order by c.id";


        // echo $sql_result;

        $order_result=sql_select($sql_result);
        $po_array_for_cond=array();
        $job_array_for_cond=array();
        foreach ($order_result as $row_po_job)
        {
            $po_array_for_cond[$row_po_job['PO_ID']]=$row_po_job['PO_ID'];
            $job_array_for_cond[$row_po_job['JOB_NO']]="'".$row_po_job['JOB_NO']."'";
        }

        $job_cond_for_in=where_con_using_array($job_array_for_cond,0,'job_no');
        $job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
        $job_smv_eff_arr=return_library_array( "select job_no, sew_effi_percent from wo_pre_cost_mst where 1=1 $job_cond_for_in",'job_no','sew_effi_percent');
        
        $po_cond_for_in=where_con_using_array($po_array_for_cond,0,'po_break_down_id');
        $exfactory_data=sql_select("SELECT po_break_down_id as PO_BREAK_DOWN_ID, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_QNTY,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as EX_FACTORY_RETURN_QNTY, MAX(ex_factory_date) as EX_FACTORY_DATE
		from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($exfactory_data as $exfatory_row)
		{
			$exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_qnty']=$exfatory_row['EX_FACTORY_QNTY']-$exfatory_row['EX_FACTORY_RETURN_QNTY'];
            $exfactory_data_array[$exfatory_row['PO_BREAK_DOWN_ID']]['ex_factory_date']=$exfatory_row['EX_FACTORY_DATE'];
		}
        unset($exfactory_data);

        $ac_po_sql=sql_select("SELECT id, po_break_down_id, acc_po_no from wo_po_acc_po_info where is_deleted=0 $po_cond_for_in $job_cond_for_in");

        $ac_po_arr=array();
        foreach($ac_po_sql as $row)
        {
            $ac_po_arr[$row["PO_BREAK_DOWN_ID"]].=$row["ACC_PO_NO"].", ";
        }
        unset($ac_po_sql);

        $invoice_id_sql=sql_select("SELECT po_break_down_id, invoice_no as INVOICE_NO from pro_ex_factory_mst where status_active=1 and is_deleted=0 $po_cond_for_in ");
		$invoiceIdArr=$exFactoryPoIdArr=array();
		foreach($invoice_id_sql as $row)
		{
			if($row['INVOICE_NO']){$invoiceIdArr[$row['INVOICE_NO']]=$row['INVOICE_NO'];}
			$exFactoryPoIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
        unset($invoice_id_sql);

		$po_id_in=where_con_using_array($exFactoryPoIdArr,0,'b.po_breakdown_id');
		$invoice_id_in=where_con_using_array($invoiceIdArr,0,'a.id');
		$exInvoice_sql="SELECT a.id, a.INVOICE_VALUE, a.NET_INVO_VALUE, b.PO_BREAKDOWN_ID, b.CURRENT_INVOICE_VALUE from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id $po_id_in $invoice_id_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$exInvoice_data=sql_select($exInvoice_sql);
		$exInvoiceArr=array();
		foreach($exInvoice_data as $row)
		{
			if($row['CURRENT_INVOICE_VALUE']){$exInvoiceArr[$row['PO_BREAKDOWN_ID']]+=($row['NET_INVO_VALUE']/$row['INVOICE_VALUE'])*$row['CURRENT_INVOICE_VALUE'];}
		}
        unset($exInvoice_data);
        ob_start();
        ?>
        <style>
        table tbody tr td
        {
            word-break: break-word;
        }
        </style>
        <div style="margin:5px;width:100%;" >
            <table cellpadding="0" width="4670" cellspacing="0" border="1" rules="all" align='left' id="tbl_details">
                <tr>
                    <td align='center'><strong><?=$company_array[$cbo_company_name];?></strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Monthly Confirmed Order and Export Quantity with Value</strong></td>
                </tr>
                <tr>
                    <td align='center'><strong>Export Details</strong></td>
                </tr>
                <tr>
                    <td align='center'>Date Range : <?=$startDate.' - '.$endDate;?>&nbsp;&nbsp;&nbsp; Report Generate Time:<?=date("h:i:s a",time());?></td>
                </tr>
            </table>
            <br>
            <table width="4670" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Company Name</th>
                        <th width="100">Job No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Agent</th>
                        <th width="100">Actual PO</th>
                        <th width="100">Order No</th>
                        <th width="100">Season</th>
                        <th width="100">Pord. Dept.</th>
                        <th width="100">Sub. Dept</th>
                        <th width="100">Pord. Dept Code</th>
                        <th width="100">Brand</th>
                        <th width="100">Img</th>
                        <th width="100">Item</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Style Des</th>
                        <th width="100">PO Insert Date</th>
                        <th width="100">Pub Ship Date</th>
                        <th width="100">Original Ship Date</th>
                        <th width="60" >Lead Time</th>
                        <th width="100">SMV</th>
                        <th width="60" >Eff. %</th>
                        <th width="80" >Total SMV</th>
                        <th width="80" >Order Qnty</th>
                        <th width="70" >Uom</th>
                        <th width="80" >Order Qnty (Pcs)</th>
                        <th width="80" >Break-down Qty (Pcs)</th>
                        <th width="80" >Per Unit Price</th>
                        <th width="80" >Gross Order Value ($)</th>
                        <th width="80" >Net Order Value ($)</th>
                        <th width="80" >Ex-Fac Qnty (Pcs)</th>
                        <th width="80" >Gross Ex-Fac Value ($)</th>
                        <th width="80" >Net Ex-Fac Value ($)</th>
                        <th width="80" >Ex-factory Bal. (Pcs)</th>
                        <th width="80" >Ex-factory Over (Pcs)</th>
                        <th width="80" >Gross Ex-factory Bal. Value ($)</th>
                        <th width="80" >Net Ex-factory Bal. Value ($)</th>
                        <th width="80" >Gross Ex-factory Over Value ($)</th>
                        <th width="80" >Net Ex-factory Over Value ($)</th>
                        <th width="80" >Short/ Over/At Per</th>
                        <th width="80" >Order Status</th>
                        <th width="80" >Prod. Catg</th>
                        <th width="80" >PO Rec. Date</th>
                        <th width="80" >Ex-fac. Bal. on Brk-dwn Qty (Pcs)</th>
                        <th width="80" >Days in Hand</th>
                        <th width="80" >Shipping Status</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Factory Merchandiser</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Team Name</th>
                        <th width="70" >Ship Mode</th>
                        <th width="100">Int. Ref/ Grouping</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style=" max-height:400px; overflow-y:scroll; width:4670px"  align="left" id="scroll_body">
                <table width="4650" border="1" class="rpt_table" rules="all" id="export_details_tbl">
                    <?
                        $i=1;
                        foreach($order_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row['GMTS_ITEM_ID']);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                            $ex_factory_qnty=$exfactory_data_array[$row["PO_ID"]]['ex_factory_qnty'];
                            $poQtyPcs=$row['ACC_PO_QTY']*$row['TOTAL_SET_QNTY'];
                            if($row['ACC_PO_QTY']*$row['TOTAL_SET_QNTY']>$row['ORDER_QUANTITY_PCS']) $bgColorBreakDown="#ff0000";
                            else $bgColorBreakDown="";
                            $ex_factory_date=$exfactory_data_array[$row["PO_ID"]]['ex_factory_date'];
							$date_diff_2=datediff( "d", $ex_factory_date , $row['SHIPMENT_DATE']);
                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                            {
                                if($row['DATE_DIFF_1']>10){ $bgColorDaysHand="";}
                                if($row['DATE_DIFF_1']<=10 && $row['DATE_DIFF_1']>=0){ $bgColorDaysHand="orange";}
                                if($row['DATE_DIFF_1']<0){ $bgColorDaysHand="#ff0000";}
                            }
                            else
                            {
                                if($row['SHIPING_STATUS']==3 && $date_diff_2 >=0 ) $bgColorDaysHand="green";
                                if($row['SHIPING_STATUS']==3 &&  $date_diff_2<0) $bgColorDaysHand="#2A9FFF";
                            }
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                                    <td width="30" align='center'><?=$i;?></td>
                                    <td width="100"><?echo $company_array[$row['COMPANY_NAME']];?></td>
                                    <td width="100"><?echo $row['JOB_NO'];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['BUYER_NAME']];?></td>
                                    <td width="100"><?echo $buyer_arr[$row['AGENT_NAME']];?></td>
                                    <!-- <td width="100"><?//=rtrim($ac_po_arr[$row["PO_ID"]],", ");?></td> -->
                                    <td width="100"><?echo $row['ACC_PO_NO'];?></td>
                                    <td width="100"><?echo $row['PO_NUMBER'];?></td>
                                    <td width="100"><?echo $season_arr[$row['SEASON_BUYER_WISE']];?></td>
                                    <td width="100"><?echo $product_dept[$row['PRODUCT_DEPT']];?></td>
                                    <td width="100"><?echo $sub_dep_arr[$row['PRO_SUB_DEP']];?></td>
                                    <td width="100"><?echo $row['PRODUCT_CODE'];?></td>
                                    <td width="100"><?echo $brand_arr[$row['BRAND_ID']];?></td>
                                    <td width="100" align='center'><img src='../../../<? echo $imge_arr[$row['JOB_NO']]; ?>' height='25'  /></td>
                                    <td width="100"><? echo rtrim($gmts_item_name,","); ?></td>
                                    <td width="100"><?echo $row['STYLE_REF_NO'];?></td>
                                    <td width="100"><?echo $row['STYLE_DESCRIPTION'];?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['INSERT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['PUB_SHIPMENT_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="100" align='center'><? echo change_date_format($row['ACC_SHIP_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="60" align='center'><? echo datediff('d',$row['ACC_RCV_DATE'],$row['ACC_SHIP_DATE']);?></td>
                                    <td width="100" align='center'>
                                        <?
                                            $setSmvArr=array();
                                            foreach(explode('__',$row['SET_BREAK_DOWN']) as $setBrAr){
                                                list($itemId,$setRa,$setSmv)=explode('_',$setBrAr);
                                                $setSmvArr[]=$setSmv;
                                                
                                            }
                                            echo "[ ".implode(',',$setSmvArr)." ], ";
                                            echo number_format($job_smv_arr[$row['JOB_NO']],2);
                                        ?>
                                    </td>
                                    <td width="60" align='right'><?=$job_smv_eff_arr[$row['JOB_NO']];?></td>
                                    <td width="80" align='right'>
                                        <?  $smv= ($job_smv_arr[$row['JOB_NO']])*$row['ACC_PO_QTY']; $smv_tot+=$smv; echo fn_number_format($smv,2); ?>
                                    </td>
                                    <td width="80" align='right'><?echo $row['ACC_PO_QTY'];?></td>
                                    <td width="70" align='center'><?echo $unit_of_measurement[$row['ORDER_UOM']];?></td>
                                    <td width="80" align='right'><?echo fn_number_format(($row['ACC_PO_QTY']*$row['TOTAL_SET_QNTY']),0);$order_pcs_tot+=$row['ACC_PO_QTY']*$row['TOTAL_SET_QNTY'];?></td>
                                    <td width="80" align='right' bgcolor="<? echo $bgColorBreakDown; ?>"><?echo  $row['ORDER_QUANTITY_PCS'];$break_down_pcs_tot+=$row['ORDER_QUANTITY_PCS']?></td>
                                    <td width="80" align='right'><? echo number_format($row['UNIT_PRICE'],2);?></td>
                                    <td width="80" align='right'><? echo number_format($row['ACC_PO_VALUE'],2);$order_value_tot+=$row['ACC_PO_VALUE'];?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($commission_arr[$row['ID']]>0){
                                                echo number_format($row['ACC_PO_VALUE']-(($row['ACC_PO_VALUE']*$commission_arr[$row['ID']])/100),2);
                                                $net_order_value_tot+=($row['ACC_PO_VALUE']-(($row['ACC_PO_VALUE']*$commission_arr[$row['ID']])/100));
                                            }else{
                                                echo number_format($row['ACC_PO_VALUE'],2);$net_order_value_tot+=$row['ACC_PO_VALUE'];
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'><? echo fn_number_format( $ex_factory_qnty,0);$ex_factory_qnty_tot+=$ex_factory_qnty; ?></td>
                                    <td width="80" align='right'>
                                        <? 
                                            $ex_factory_value=$ex_factory_qnty*($row['UNIT_PRICE']/$row['TOTAL_SET_QNTY']);
                                            echo fn_number_format( $ex_factory_value,0);$ex_factory_value_tot+=$ex_factory_value; 
                                        ?>
                                    </td>
                                    <td width="80" align='right'>                                        
                                        <? 
                                            /*if($commission_arr[$row['ID']]>0){
                                                echo number_format($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100),0);
                                                $net_ex_factory_value_tot+=($ex_factory_value-(($ex_factory_value*$commission_arr[$row['ID']])/100));
                                            }else{
                                                echo number_format($ex_factory_value,0);$net_ex_factory_value_tot+=$ex_factory_value;
                                            }*/
                                            echo number_format($exInvoiceArr[$row["PO_ID"]],0);$net_ex_factory_value_tot+=$exInvoiceArr[$row["PO_ID"]];
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            $short_over_shipment="";
                                            $short_access_qnty=(($row['PO_QUANTITY']*$row['TOTAL_SET_QNTY'])-$ex_factory_qnty);
                                            if($short_access_qnty>=0)
                                            {
                                                echo fn_number_format($short_access_qnty,0);
                                                $total_short_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                            echo fn_number_format(ltrim($short_access_qnty,'-'),0);
                                            $total_over_access_qnty+=$short_access_qnty;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                        if($short_access_qnty>=0)
                                        {
                                            $short_access_value=($short_access_qnty/$row['TOTAL_SET_QNTY'])*$row['UNIT_PRICE'];
                                            echo fn_number_format($short_access_value,2);
                                            $total_short_access_value+=$short_access_value;
                                        }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            if($short_access_qnty>=0)
                                            {
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100),2);
                                                    $total_net_short_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($short_access_value,2);$total_net_short_access_value+=$short_access_value;
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <?
                                            if($short_access_qnty<0)
                                            {
                                                $short_over_value=ltrim(($short_access_qnty/$row[csf('total_set_qnty')])*$row[csf('unit_price')],'-');
                                                echo  fn_number_format($short_over_value,2);
                                                $total_over_access_value+=$short_over_value;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='right'>
                                        <? 
                                            /*if($short_access_qnty<0)
                                            {
                                                if($commission_arr[$row['ID']]>0){
                                                    echo number_format($short_over_value-(($short_over_value*$commission_arr[$row['ID']])/100),2);
                                                    $total_net_over_access_value+=($short_access_value-(($short_access_value*$commission_arr[$row['ID']])/100));
                                                }else{
                                                    echo number_format($short_over_value,2);$total_net_over_access_value+=$short_over_value;
                                                }
                                            }*/
                                            if($short_access_qnty<0)
                                            {
                                                echo number_format(($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value,2);
                                                $total_net_over_access_value+=($exInvoiceArr[$row["PO_ID"]]/$ex_factory_value)*$short_over_value;
                                            }

                                        ?>
                                    </td>
                                    <td width="80" align='center'>
                                        <?
                                            $short_over_shipment="";
                                            if(($poQtyPcs-$ex_factory_qnty)==0){$short_over_shipment="At Per";}
                                            else if($poQtyPcs<$ex_factory_qnty){$short_over_shipment= "Over Shipment";}
                                            else if($poQtyPcs>$ex_factory_qnty){$short_over_shipment="Short Shipment";}
                                            echo $short_over_shipment;
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $order_status[$row['IS_CONFIRMED']];?></td>
                                    <td width="80" align='center'><? echo $product_category[$row['PRODUCT_CATEGORY']];?></td>
                                    <td width="80" align='center'><? echo change_date_format($row['ACC_RCV_DATE'],'dd-mm-yyyy','-');?></td>
                                    <td width="80" align='center'><? echo fn_number_format($row['ORDER_QUANTITY_PCS']-$ex_factory_qnty);$total_ex_factory_brk_dwn_qty+=$row['ORDER_QUANTITY_PCS']-$ex_factory_qnty;?></td>
                                    <td width="80" align='center'  bgcolor="<? echo $bgColorDaysHand; ?>">
                                        <?
                                            if($row['SHIPING_STATUS']==1 || $row['SHIPING_STATUS']==2)
                                            {
                                            echo $row['DATE_DIFF_1'];
                                            }
                                            if($row['SHIPING_STATUS']==3)
                                            {
                                            echo $date_diff_2;
                                            }
                                        ?>
                                    </td>
                                    <td width="80" align='center'><? echo $shipment_status[$row['SHIPING_STATUS']]; ?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['DEALING_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_member_name_arr[$row['FACTORY_MARCHANT']];?></td>
                                    <td width="100"><? echo $company_team_leader_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="100"><? echo $company_team_name_arr[$row['TEAM_LEADER']];?></td>
                                    <td width="70" align='center'><? echo $shipment_mode[$row['SHIP_MODE']]; ?></td>
                                    <td width="100"><? echo $row['INTERNAL_REF']; ?></td>
                                    <td ><? echo $row['DETAILS_REMARKS']; ?></td>
                                </tr>
                            <?
                            $i++;
                        }
                    ?>
                </table>
            </div>
            <table width="4670" id="report_table_footer" border="1" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="60" ></th>
                        <th width="100" ></th>
                        <th width="60" ></th>
                        <th width="80" id="smv_tot"><?=number_format($smv_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="70" ></th>
                        <th width="80" id="order_pcs_tot"><?=number_format($order_pcs_tot,2);?></th>
                        <th width="80" id="break_down_pcs_tot"><?=number_format($break_down_pcs_tot,2);?></th>
                        <th width="80" ></th>
                        <th width="80" id="order_value_tot"><?=number_format($order_value_tot,2);?></th>
                        <th width="80" id="net_order_value_tot"><?=number_format($net_order_value_tot,2);?></th>
                        <th width="80" id="ex_factory_qnty_tot"><?=number_format($ex_factory_qnty_tot,2);?></th>
                        <th width="80" id="ex_factory_value_tot"><?=number_format($ex_factory_value_tot,2);?></th>
                        <th width="80" id="net_ex_factory_value_tot"><?=number_format($net_ex_factory_value_tot,2);?></th>
                        <th width="80" id="total_short_access_qnty"><?=number_format($total_short_access_qnty,2);?></th>
                        <th width="80" id="total_over_access_qnty"><?=number_format($total_over_access_qnty,2);?></th>
                        <th width="80" id="total_short_access_value"><?=number_format($total_short_access_value,2);?></th>
                        <th width="80" id="total_net_short_access_value"><?=number_format($total_net_short_access_value,2);?></th>
                        <th width="80" id="total_over_access_value"><?=number_format($total_over_access_value,2);?></th>
                        <th width="80" id="total_net_over_access_value"><?=number_format($total_net_over_access_value,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="80" id="total_ex_factory_brk_dwn_qty"><?=number_format($total_ex_factory_brk_dwn_qty,2);?></th>
                        <th width="80" ></th>
                        <th width="80" ></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70" ></th>
                        <th width="100"></th>
                        <th ></th>
                    </tr>
                </tfoot>
            </table>
        </div>
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
	// echo "$total_data####$filename####$reportType";
	echo "$total_data####$filename####$reportType####$monthArray####$confirm_order_qty_array####$projected_order_qty_array####$exFactory_qty_array####$short_qty_Array";

	exit();
}

disconnect($con);
?>
