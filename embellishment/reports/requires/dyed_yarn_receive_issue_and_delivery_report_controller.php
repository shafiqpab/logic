<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_party")
{	
    $data=explode("_",$data);
    if($data[1]==1 && $data[0]!=0)
    {
        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --",0,"");
    }
    elseif($data[1]==2 && $data[0]!=0)
    {
		echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
    }
    else
    {
    	echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",0);
    }   
    exit();  
}
if($action=="report_generate_1")
{  
	// echo "<pre>";print_r($_POST);
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


	$cbo_pro_type=str_replace("'","", $cbo_pro_type);
	$cbo_order_type=str_replace("'","", $cbo_order_type);
	$cbo_party_name=str_replace("'","", $cbo_party_name);
	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_within_group=str_replace("'","", $cbo_within_group);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$search_by=str_replace("'","", $search_by);
	$txt_search_string=str_replace("'","", $txt_search_string);
    
    

    $condition = "";
 
				
    if($cbo_company_name!=0)
    {
        $condition .= " and a.company_id=$cbo_company_name";
    }

    if($cbo_within_group!=0)
    {
        $condition .= " and a.within_group=$cbo_within_group";
    }

    if($cbo_party_name!=0)
    {
        $condition .= " and a.party_id=$cbo_party_name";
    }

    if($cbo_pro_type!=0)
    {
        $condition .= " and a.pro_type=$cbo_pro_type";
    }

    if($cbo_order_type!=0)
    {
        $condition .= " and a.order_type=$cbo_order_type";
    }
    
    $date_con = '';
    if($db_type==0)
    { 
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
    }
    else
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
    }


    if($search_by!="")
        {
            if($search_by==1) $condition.="and a.yd_job like '%$txt_search_string%'";
            else if($search_by==2) $condition.="and a.order_no like '%$txt_search_string%'";
            else if ($search_by==3) $condition.=" and b.style_ref like  '%$txt_search_string%' ";
            else if ($search_by==4) $condition.=" and b.sales_order_no like  '%$txt_search_string%' ";
        }
        
  
            

    if($db_type==0)
    { 
        $ins_year_cond="year(a.insert_date)";
    }
    else
    {
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }

    $wo_wise_arr= array();

    $issued_sql="SELECT A.ORDER_NO AS WO_NO, C.RECEIVE_QTY as issued_qty, c.JOB_DTLS_ID FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_MATERIAL_DTLS C WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.JOB_DTLS_ID=B.ID AND C.ENTRY_FORM=388  AND c.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
    // echo $issued_sql;echo "<br>";
    $issued_result= sql_select($issued_sql);
    foreach ($issued_result as $issued => $data) {
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['ISSUED_QTY']+=$data['ISSUED_QTY'];
    }
    $batch_sql="SELECT A.ORDER_NO AS WO_NO, B.ID AS JOB_DTLS_ID, C.BATCH_NUMBER, C.YD_JOB_ID FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_BATCH_MST C WHERE  A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.YD_JOB_ID=A.ID AND C.ENTRY_FORM=398  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
    // echo $batch_sql;echo "<br>";
    $batch_result= sql_select($batch_sql);
    foreach ($batch_result as $batch => $data) {
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['BATCH_NUMBER']=$data['BATCH_NUMBER'];
    }
    $delivey_sql="SELECT A.ORDER_NO AS WO_NO, C.RECEIVE_QTY as delv_qty, C.RCV_GREY_QTY as delv_grey_qty, C.DTLS_ID as JOB_DTLS_ID FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_STORE_RECEIVE_DTLS C WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND C.DTLS_ID=B.ID  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
    // echo $delivey_sql;echo "<br>";
    $delv_result= sql_select($delivey_sql);
    foreach ($delv_result as $delv => $data) {
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['DELV_QTY']+=$data['DELV_QTY'];
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['DELV_GREY_QTY']+=$data['DELV_GREY_QTY'];
        // $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['DELV_QTY']+=$data['DELV_QTY'];
        // $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['DELV_GREY_QTY']+=$data['DELV_GREY_QTY'];
    }

    //    $sql = "SELECT A.YD_JOB, A.ORDER_ID, A.ORDER_NO, A.PARTY_ID, A.RECEIVE_DATE, A.DELIVERY_DATE, A.PRO_TYPE, A.ORDER_TYPE, A.YD_TYPE, A.YD_PROCESS, b.ORDER_QUANTITY, b.PROCESS_LOSS, B.SALES_ORDER_NO, B.COUNT_ID, B.COUNT_TYPE, B.YARN_TYPE_ID, B.YARN_COMPOSITION_ID, B.ITEM_COLOR_ID, B.YD_COLOR_ID, C.LOT, C.RECEIVE_QTY, c.entry_form, D.BATCH_NUMBER, E.RECEIVE_QTY, E.RCV_GREY_QTY FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_MATERIAL_DTLS C, YD_BATCH_MST D, YD_STORE_RECEIVE_DTLS E WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.JOB_DTLS_ID=B.ID AND C.ENTRY_FORM=387  AND c.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND d.YD_JOB_ID=A.ID AND D.ENTRY_FORM=398 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND  E.DTLS_ID=B.ID AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0";

   $sql = "SELECT A.ID AS JOB_ID, A.YD_JOB, A.ORDER_ID, A.ORDER_NO AS WO_NO, A.PARTY_ID, A.RECEIVE_DATE, A.DELIVERY_DATE, A.PRO_TYPE, A.ORDER_TYPE, A.YD_TYPE, A.YD_PROCESS,B.ID AS JOB_DTLS_ID, b.ORDER_QUANTITY, b.TOTAL_ORDER_QUANTITY, b.PROCESS_LOSS, B.SALES_ORDER_NO, B.COUNT_ID, b.buyer_buyer, B.COUNT_TYPE, B.YARN_TYPE_ID, B.YARN_COMPOSITION_ID, B.ITEM_COLOR_ID, B.YD_COLOR_ID, C.LOT, C.RECEIVE_QTY FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_MATERIAL_DTLS C WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.JOB_DTLS_ID=B.ID AND C.ENTRY_FORM=387  AND c.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
    // echo $sql;
    $result= sql_select($sql);
    // echo "<pre>";print_r($result);die;
    
    foreach ($result as $key => $data) {
        
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['YD_JOB']=$data['YD_JOB'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['WO_NO']=$data['WO_NO'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['PARTY_ID']=$data['PARTY_ID'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['RECEIVE_DATE']=$data['RECEIVE_DATE'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['DELIVERY_DATE']=$data['DELIVERY_DATE'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['PRO_TYPE']=$data['PRO_TYPE'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['ORDER_TYPE']=$data['ORDER_TYPE'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['YD_TYPE']=$data['YD_TYPE'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['YD_PROCESS']=$data['YD_PROCESS'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['ORDER_QUANTITY']=$data['ORDER_QUANTITY'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['TOTAL_ORDER_QUANTITY']=$data['TOTAL_ORDER_QUANTITY'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['PROCESS_LOSS']=$data['PROCESS_LOSS'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['SALES_ORDER_NO']=$data['SALES_ORDER_NO'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['COUNT_ID']=$data['COUNT_ID'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['COUNT_TYPE']=$data['COUNT_TYPE'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['BUYER_BUYER']=$data['BUYER_BUYER'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['YARN_TYPE_ID']=$data['YARN_TYPE_ID'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['YARN_COMPOSITION_ID']=$data['YARN_COMPOSITION_ID'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['ITEM_COLOR_ID']=$data['ITEM_COLOR_ID'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['YD_COLOR_ID']=$data['YD_COLOR_ID'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['LOT']=$data['LOT'];
        $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['RECEIVE_QTY']+=$data['RECEIVE_QTY'];
    }
    
    
    // echo "<pre>";print_r($wo_wise_arr);die;
    ob_start();
    ?>
    <style>
        table tr td{
            word-break: break-all;
            word-wrap: break-word;
        }
        
    </style>
    
    <!-- <fieldset style="width:1960px;"> -->
    <div style="width:2020px; margin:2 auto;">
        <table cellpadding="0" cellspacing="0" width="2000">
            <thead class="form_caption">
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo str_replace("'","",$report_title); ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$cbo_company_name]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo "Date &nbsp;".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy').' &nbsp; To &nbsp;'.change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </thead>
        </table>
        <table width="2000" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table" id="table_header">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="120">YD Job No</th>
                    <th width="100">WO No</th>
                    <th width="60">Buyer Order No</th>
                    <th width="100">Buyer Name</th>
                    <th width="60">Party Buyer</th>
                    <th width="80">Order Receive Date</th>
                    <th width="80">Order Delivery Date</th>
                    <th width="50">Prod. Type</th>
                    <th width="60">Y/D Type</th>
                    <th width="50">Count Type</th>
                    <th width="50">Yarn Type</th>
                    <th width="50">Count</th>
                    <th width="">Yarn Composition</th>
                    <th width="60">Color Range</th>
                    <th width="60">Color/ Shade</th>
                    <th width="60">Yarn Lot</th>
                    <th width="100">Batch No</th>
                    <th width="50">Order Qty.</th>
                    <th width="50">Finish Qty. Based on Order Qty(Kg)</th>
                    <th width="50">Yarn Received Qty.(Kg)</th>
                    <th width="50">Yarn Return Qty (kg)</th>
                    <th width="50">Total Received Qty.</th>
                    <th width="50">Finish Qty. Based on Rcv Qty(Kg)</th>
                    <th width="50">Yarn Issued Qty(Kg)</th>
                    <th width="50">Yarn Issue Balance Qty(Kg)</th>
                    <th width="50">Stock Qty.</th>
                    <th width="50">Delivery Qty.(kg)</th>
                    <th width="50">Delivery G.Qty (Kg)</th>
                    <th width="50">Delivery Balance grey Qty(kg)</th>
                    <th width="50">Delivery Balance Finish Qty</th>
                </tr>
            </thead>
            </table>
            <div style="max-height:360px; overflow-y:scroll; width:2020px" id="scroll_body">
            <table class="rpt_table" width="2000" border="1" cellpadding="0" cellspacing="0" rules="all" id="table_body">
            <?
                $i=1;
                $order_qty_tot=0;
                $order_grey_qty_tot=0;
                $rcv_qty_tot=0;
                $return_qty_tot=0;
                $tot_rcv_qty_tot=0;
                $to_be_delv_tot=0;
                $issued_qty_tot=0;
                $delv_qty_tot=0;
                $delv_grey_qty_tot=0;
                $delv_balance_tot=0;
                $delv_fin_bal_tot=0;
                foreach ($wo_wise_arr as $wo_no => $wo_data) {
                    $wo_order_qty_tot=0;
                    $wo_order_grey_qty_tot=0;
                    $wo_rcv_qty_tot=0;
                    $wo_return_qty_tot=0;
                    $wo_tot_rcv_qty_tot=0;
                    $wo_to_be_delv_tot=0;
                    $wo_issued_qty_tot=0;
                    $wo_delv_qty_tot=0;
                    $wo_delv_grey_qty_tot=0;
                    $wo_delv_balance_tot=0;
                    $wo_delv_fin_bal_tot=0;

                    foreach ($wo_data as $job_dtls => $data) {
                        
                    // echo "<pre>";print_r($data);die;
                    $bgcolor 	= ($i%2==0)?"#E9F3FF":"#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
                <td width="30" align="center"><?php echo $i++;?></td>
                <td width="120" align="center"><?php echo $data['YD_JOB'];?></td>
                <td width="100" align="center"><?php echo $data['WO_NO'];?></td>
                <td width="60" align="center"><?php echo $data['SALES_ORDER_NO'];?></td>
                <td width="100" align="center"><?php echo $party_arr[$data['PARTY_ID']];?></td>
                <td width="60" align="center"><?php echo $data['BUYER_BUYER'];?></td>
                <td width="80" align="center"><?php echo $data['RECEIVE_DATE'];?></td>
                <td width="80" align="center"><?php echo $data['DELIVERY_DATE'];?></td>
                <td width="50" align="center"><?php echo $w_pro_type_arr[$data['PRO_TYPE']];?></td>
                <td width="60" align="center"><?php echo $yd_type_arr[$data['YD_TYPE']];?></td>
                <td width="50" align="center"><?php echo $count_type_arr[$data['COUNT_TYPE']];?></td>
                <td width="50" align="center"><?php echo $yarn_type[$data['YARN_TYPE_ID']];?></td>
                <td width="50" align="center"><?php echo $count_arr[$data['COUNT_ID']];?></td>
                <td align="center"><?php echo $composition[$data['YARN_COMPOSITION_ID']];?></td>
                <td width="60" align="center"><?php echo $color_range[$data['ITEM_COLOR_ID']];?></td>
                <td width="60" align="center"><?php echo $color_arr[$data['YD_COLOR_ID']];?></td>
                <td width="60" align="center"><?php echo $data['LOT'];?></td>
                <td width="100" align="center"><?php echo $data['BATCH_NUMBER'];?></td>
                <td align="right" width="50"><?php echo number_format($data['ORDER_QUANTITY'],2,".","");?></td>
                <td width="50" title="<?php echo $data['PROCESS_LOSS']."%";?>" align="right"><?php echo number_format($data['TOTAL_ORDER_QUANTITY'],2,".","");?></td>
                <td align="right" width="50"><?php echo $rcv_qty = number_format($data['RECEIVE_QTY'],2,".","");?></td>
                <td align="right" width="50"><?php echo $return_qty = 0;?></td>
                <td align="right" width="50"><?php $total_rcv_qty = $rcv_qty-$return_qty;echo number_format($total_rcv_qty,2,".","");?></td>
                <td width="50" align="right" title="<?php echo $data['PROCESS_LOSS']."%";?>"><?php $to_be_delv=$total_rcv_qty-(($data['PROCESS_LOSS']/100)*$total_rcv_qty);echo number_format($to_be_delv,2,".","");?></td>
                <td align="right" width="50"><?php echo $issued_qty = number_format($data['ISSUED_QTY'],2,".","");?></td>
                <td align="right" width="50"><?php $issued_balance = $total_rcv_qty-$issued_qty;echo number_format($issued_balance,2,".","");?></td>
                <td align="right" width="50"><?php $stock = $total_rcv_qty-$issued_qty;echo number_format($stock,2,".","");?></td>
                <td align="right" width="50"><?php echo $delv_qty = number_format($wo_wise_delv_arr[$wo_no][$job_dtls]['DELV_QTY'],2,".","");?></td>
                <td align="right" width="50"><?php echo $delv_grey_qty = number_format($wo_wise_delv_arr[$wo_no][$job_dtls]['DELV_GREY_QTY'],2,".","");?></td>
                <td align="right" width="50"><?php $delv_balance = $total_rcv_qty-$delv_grey_qty; echo number_format($delv_balance,2,".","");?></td>
                <td align="right" width="50"><?php $delv_fin_balance = $to_be_delv-$delv_qty; echo number_format($delv_fin_balance,2,".","");?></td>
                
            </tr>
            <?
                $wo_order_qty_tot+=$data['ORDER_QUANTITY'];
                $wo_order_grey_qty_tot+=$data['TOTAL_ORDER_QUANTITY'];
                $wo_rcv_qty_tot+=$data['RECEIVE_QTY'];
                $wo_return_qty_tot+=$return_qty;
                $wo_tot_rcv_qty_tot+=$total_rcv_qty;
                $wo_to_be_delv_tot+=$to_be_delv;
                $wo_issued_qty_tot+=$issued_qty;
                $wo_delv_qty_tot+=$delv_qty;
                $wo_delv_grey_qty_tot+=$delv_grey_qty;
                $wo_delv_balance_tot+=$delv_balance;
                $wo_delv_fin_bal_tot+=$delv_fin_balance;
                }
                ?>
                    <tr bgcolor="#D3D3D3">
                        <td colspan="18" align="right"><strong>WO Wise Sub Total:</strong></td>
                        <td align="right"><strong><strong><?php echo number_format($wo_order_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_order_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_return_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_tot_rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_to_be_delv_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_issued_qty_tot,2,".",",");?></strong></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><strong><?php echo number_format($wo_delv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_delv_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_delv_balance_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_delv_fin_bal_tot,2,".",",");?></strong></td>
                    </tr>
                <?
                
                $order_qty_tot+=$wo_order_qty_tot;
                $order_grey_qty_tot+=$wo_order_grey_qty_tot;
                $rcv_qty_tot+=$wo_rcv_qty_tot;
                $return_qty_tot+=$wo_return_qty_tot;
                $tot_rcv_qty_tot+=$wo_tot_rcv_qty_tot;
                $to_be_delv_tot+=$wo_to_be_delv_tot;
                $issued_qty_tot+=$wo_issued_qty_tot;
                $delv_qty_tot+=$wo_delv_qty_tot;
                $delv_grey_qty_tot+=$wo_delv_grey_qty_tot;
                $delv_balance_tot+=$wo_delv_balance_tot;
                $delv_fin_bal_tot+=$wo_delv_fin_bal_tot;
                }
            ?>
                    <tr bgcolor="#FFA500" height="20" >
                        <td colspan="18" align="right"><strong>Total:</strong></td>
                        <td align="right"><strong><strong><?php echo number_format($order_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($order_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($return_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($tot_rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($to_be_delv_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($issued_qty_tot,2,".",",");?></strong></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><strong><?php echo number_format($delv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($delv_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($delv_balance_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($delv_fin_bal_tot,2,".",",");?></strong></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
        </table>
            </div>
    </div>
    <!-- </fieldset> -->

    <?
    
    	$html = ob_get_contents();
        ob_clean();
        //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
        foreach (glob("*.xls") as $filename) {
        //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename=$user_id."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');	
        $is_created = fwrite($create_new_doc, $html);
        echo "$html**$filename"; 
        exit();
}
if($action=="report_generate_2")
{  
	// echo "<pre>";print_r($_POST);
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');


	$cbo_pro_type=str_replace("'","", $cbo_pro_type);
	$cbo_order_type=str_replace("'","", $cbo_order_type);
	$cbo_party_name=str_replace("'","", $cbo_party_name);
	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_within_group=str_replace("'","", $cbo_within_group);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$search_by=str_replace("'","", $search_by);
	$txt_search_string=str_replace("'","", $txt_search_string);
    
    

    $condition = "";
 
				
    if($cbo_company_name!=0)
    {
        $condition .= " and a.company_id=$cbo_company_name";
    }

    if($cbo_within_group!=0)
    {
        $condition .= " and a.within_group=$cbo_within_group";
    }

    if($cbo_party_name!=0)
    {
        $condition .= " and a.party_id=$cbo_party_name";
    }

    if($cbo_pro_type!=0)
    {
        $condition .= " and a.pro_type=$cbo_pro_type";
    }

    if($cbo_order_type!=0)
    {
        $condition .= " and a.order_type=$cbo_order_type";
    }
    
    $date_con = '';
    if($db_type==0)
    { 
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
    }
    else
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
    }


    if($search_by!="")
        {
            if($search_by==1) $condition.="and a.yd_job like '%$txt_search_string%'";
            else if($search_by==2) $condition.="and a.order_no like '%$txt_search_string%'";
            else if ($search_by==3) $condition.=" and b.style_ref like  '%$txt_search_string%' ";
            else if ($search_by==4) $condition.=" and b.sales_order_no like  '%$txt_search_string%' ";
        }
        
  
            

    if($db_type==0)
    { 
        $ins_year_cond="year(a.insert_date)";
    }
    else
    {
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }

    $wo_wise_arr= array();

    $issued_sql="SELECT A.ORDER_NO AS WO_NO,c.id as rcv_dtls_id, C.RECEIVE_QTY as issued_qty, c.JOB_DTLS_ID,c.mst_id as rcv_id,c.RECEIVE_QTY FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_MATERIAL_DTLS C WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.JOB_DTLS_ID=B.ID AND C.ENTRY_FORM=388  AND c.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
    // echo $issued_sql;echo "<br>"; 
    $issued_result= sql_select($issued_sql);
    foreach ($issued_result as $issued => $data) {
        if($data['RECEIVE_QTY']>0)
        {
            $wo_wise_iss_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['ISSUED_QTY']+=$data['ISSUED_QTY'];
        }
    }
    $batch_sql="SELECT A.ORDER_NO AS WO_NO, B.ID AS JOB_DTLS_ID, C.BATCH_NUMBER, C.YD_JOB_ID, d.id as rcv_dtls_id,D.RECEIVE_QTY FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_BATCH_MST C, YD_MATERIAL_DTLS d WHERE  A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.YD_JOB_ID=A.ID AND C.ENTRY_FORM=398  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND d.JOB_DTLS_ID=B.ID AND d.ENTRY_FORM=387  AND d.STATUS_ACTIVE=1 AND d.IS_DELETED=0";
    // echo $batch_sql;echo "<br>";
    $batch_result= sql_select($batch_sql);
    foreach ($batch_result as $batch => $data) {
        if($data['RECEIVE_QTY']>0)
        {
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['BATCH_NUMBER']=$data['BATCH_NUMBER'];
        }
    }
    $delivey_sql="SELECT A.ORDER_NO AS WO_NO,c.YD_RECEIVE,c.RECEIVE_DATE,c.VEHICLE_NO, d.RECEIVE_QTY as delv_qty, d.RCV_GREY_QTY as delv_grey_qty, d.DTLS_ID as JOB_DTLS_ID , d.rcv_dtls_id, d.GRAY_LOT FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_STORE_RECEIVE_MST C, YD_STORE_RECEIVE_DTLS d WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND d.DTLS_ID=B.ID  and c.id=d.mst_id and c.entry_form=640  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND d.STATUS_ACTIVE=1 AND d.IS_DELETED=0";
    // echo $delivey_sql;echo "<br>";
    $delv_result= sql_select($delivey_sql);
    // echo "<pre>";print_r($delv_result);die;
    foreach ($delv_result as $delv => $data) {
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['DELV_QTY']+=$data['DELV_QTY'];
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['DELV_QTY_brLn'].=number_format($data['DELV_QTY'],2,".","")."<br><p class='sep-cell'></p>";
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['DELV_GREY_QTY']+=$data['DELV_GREY_QTY'];
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['DELV_GREY_QTY_brLn'].=number_format($data['DELV_GREY_QTY'],2,".","")."<br><p class='sep-cell'></p>";
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YD_RECEIVE'].=$data['YD_RECEIVE']."<br><p class='sep-cell'></p>";
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['DELV_DATE'].=$data['RECEIVE_DATE']."<br><p class='sep-cell'></p>";
        if($data['VEHICLE_NO']!=""){
        $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['VEHICLE_NO'].=$data['VEHICLE_NO']."<br><p class='sep-cell'></p>";
        }else{
            $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['VEHICLE_NO'].="N/A<br><p class='sep-cell'></p>";
        }
        if($data['GRAY_LOT']!=""){
            $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['GRAY_LOT'].=$data['GRAY_LOT'].",";
        }else{
            $wo_wise_delv_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['GRAY_LOT'].="N/A,";
        }
        // $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['DELV_QTY']+=$data['DELV_QTY'];
        // $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']]['DELV_GREY_QTY']+=$data['DELV_GREY_QTY'];$wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['YD_RECEIVE']
    }
    // echo "<pre>";print_r($wo_wise_delv_arr);die;
   $sql = "SELECT A.ID AS JOB_ID, A.YD_JOB, A.ORDER_ID, A.ORDER_NO AS WO_NO, A.PARTY_ID, A.RECEIVE_DATE, A.DELIVERY_DATE, A.PRO_TYPE, A.ORDER_TYPE, A.YD_TYPE, A.YD_PROCESS,B.ID AS JOB_DTLS_ID, b.ORDER_QUANTITY, b.TOTAL_ORDER_QUANTITY, b.PROCESS_LOSS, B.SALES_ORDER_NO, B.COUNT_ID, b.buyer_buyer, B.COUNT_TYPE, B.YARN_TYPE_ID, B.YARN_COMPOSITION_ID, B.ITEM_COLOR_ID, B.YD_COLOR_ID, c.id as rcv_id,c.RECEIVE_DATE,c.YD_TRANS_NO,c.CHALAN_NO, d.id as rcv_dtls_id, D.LOT, D.RECEIVE_QTY FROM YD_ORD_MST A, YD_ORD_DTLS B, YD_MATERIAL_MST C, YD_MATERIAL_DTLS D WHERE A.ID=B.MST_ID AND A.ENTRY_FORM=374 $condition $date_con  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 and c.id=d.mst_id AND D.JOB_DTLS_ID=B.ID AND D.ENTRY_FORM=387  AND c.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND d.STATUS_ACTIVE=1 AND d.IS_DELETED=0";
    // echo $sql;
    $result= sql_select($sql);
    
    foreach ($result as $key => $data) {
        if($data['RECEIVE_QTY']>0)
        {
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YD_JOB']=$data['YD_JOB'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['WO_NO']=$data['WO_NO'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['PARTY_ID']=$data['PARTY_ID'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['RECEIVE_DATE']=$data['RECEIVE_DATE'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['DELIVERY_DATE']=$data['DELIVERY_DATE'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['PRO_TYPE']=$data['PRO_TYPE'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['ORDER_TYPE']=$data['ORDER_TYPE'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YD_TYPE']=$data['YD_TYPE'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YD_PROCESS']=$data['YD_PROCESS'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['ORDER_QUANTITY']=$data['ORDER_QUANTITY'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['TOTAL_ORDER_QUANTITY']=$data['TOTAL_ORDER_QUANTITY'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['PROCESS_LOSS']=$data['PROCESS_LOSS'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['SALES_ORDER_NO']=$data['SALES_ORDER_NO'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['COUNT_ID']=$data['COUNT_ID'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['COUNT_TYPE']=$data['COUNT_TYPE'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['BUYER_BUYER']=$data['BUYER_BUYER'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YARN_TYPE_ID']=$data['YARN_TYPE_ID'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YARN_COMPOSITION_ID']=$data['YARN_COMPOSITION_ID'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['ITEM_COLOR_ID']=$data['ITEM_COLOR_ID'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YD_COLOR_ID']=$data['YD_COLOR_ID'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['LOT']=$data['LOT'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['RECEIVE_QTY']=$data['RECEIVE_QTY'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['YD_TRANS_NO']=$data['YD_TRANS_NO'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['CHALAN_NO']=$data['CHALAN_NO'];
            $wo_wise_arr[$data['WO_NO']][$data['JOB_DTLS_ID']][$data['RCV_DTLS_ID']]['RECEIVE_DATE']=$data['RECEIVE_DATE'];
        }
    }
    
    
    // echo "<pre>";print_r($wo_wise_arr);die;
    foreach ($wo_wise_arr as $wo_no => $wo_data) {
        foreach ($wo_data as $job_dtls_id => $job_data) {
            $job_dtls_span_no=0;
            foreach ($job_data as $rcv_dtls_id => $rcv_data) {
                $job_dtls_span_no++;
            }
                $job_wise_span[$wo_no][$job_dtls_id]=$job_dtls_span_no;
        }
    }

    // echo "<pre>";print_r($wo_wise_arr);exit;
    ob_start();
    ?>
    <style>
        table tr td{
            word-break: break-all;
            word-wrap: break-word;
        }
        .sep-cell{
            border-bottom:1px solid #8DAFDA;
        }
    </style>
    
    <!-- <fieldset style="width:1960px;"> -->
    <div style="width:2520px; margin:2 auto;">
        <table cellpadding="0" cellspacing="0" width="2500">
            <thead class="form_caption">
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo str_replace("'","",$report_title); ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$cbo_company_name]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="") echo "Date &nbsp;".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy').' &nbsp; To &nbsp;'.change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy');?>
                    </td>
                </tr>
            </thead>
        </table>
        <table width="2400" border="1" cellpadding="0" cellspacing="0" rules="all"  class="rpt_table" id="table_header">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="120">YD Job No</th>
                    <th width="100">WO No</th>
                    <th width="60">Buyer Order No</th>
                    <th width="100">Buyer Name</th>
                    <th width="60">Party Buyer</th>
                    <th width="80">Order Receive Date</th>
                    <th width="80">Order Delivery Date</th>
                    <th width="50">Prod. Type</th>
                    <th width="60">Y/D Type</th>
                    <th width="50">Count Type</th>
                    <th width="50">Yarn Type</th>
                    <th width="50">Count</th>
                    <th width="">Yarn Composition</th>
                    <th width="60">Color Range</th>
                    <th width="60">Color/ Shade</th>
                    <th width="60">Yarn Lot</th>
                    <th width="100">Mat.Rcv Date</th>
                    <th width="100">Mat.Rcv Challan</th>
                    <th width="100">Delivery Date</th>
                    <th width="100">Delivery ID</th>
                    <th width="100">Vehicle No</th>
                    <th width="100">Batch No</th>
                    <th width="50">Order Qty.</th>
                    <th width="50">Finish Qty. Based on Order Qty(Kg)</th>
                    <th width="50">Yarn Received Qty.(Kg)</th>
                    <th width="50">Yarn Return Qty (kg)</th>
                    <th width="50">Total Received Qty.</th>
                    <th width="50">Finish Qty. Based on Rcv Qty(Kg)</th>
                    <th width="50">Yarn Issued Qty(Kg)</th>
                    <th width="50">Delivery F.Qty.(kg)</th>
                    <th width="50">Delivery G.Qty (Kg)</th>
                    <th width="50">Delivery Balance grey Qty(kg)</th>
                    <th width="50">Delivery Balance Finish Qty</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:360px; overflow-y:scroll; width:2420px" id="scroll_body">
            <table class="rpt_table" width="2400" border="1" cellpadding="0" cellspacing="0" rules="all" id="table_body">
                <?
                $i=1;
                $order_qty_tot=0;
                $order_grey_qty_tot=0;
                $rcv_qty_tot=0;
                $return_qty_tot=0;
                $tot_rcv_qty_tot=0;
                $to_be_delv_tot=0;
                $issued_qty_tot=0;
                $delv_qty_tot=0;
                $delv_grey_qty_tot=0;
                $delv_balance_tot=0;
                $delv_fin_bal_tot=0;
                foreach ($wo_wise_arr as $wo_no => $wo_data) 
                {
                    $wo_order_qty_tot=0;
                    $wo_order_grey_qty_tot=0;
                    $wo_rcv_qty_tot=0;
                    $wo_return_qty_tot=0;
                    $wo_tot_rcv_qty_tot=0;
                    $wo_to_be_delv_tot=0;
                    $wo_issued_qty_tot=0;
                    $wo_delv_qty_tot=0;
                    $wo_delv_grey_qty_tot=0;
                    $wo_delv_balance_tot=0;
                    $wo_delv_fin_bal_tot=0;
                    
                    $j_sl=1;

                    foreach ($wo_data as $job_dtls_id => $job_dtls) 
                    {
                        $job_sl=0;
                        $wo_yd_odr_order_qty_tot=0;
                        $wo_yd_odr_order_grey_qty_tot=0;
                        $wo_yd_odr_rcv_qty_tot=0;
                        $wo_yd_odr_return_qty_tot=0;
                        $wo_yd_odr_tot_rcv_qty_tot=0;
                        $wo_yd_odr_to_be_delv_tot=0;
                        $wo_yd_odr_issued_qty_tot=0;
                        $wo_yd_odr_delv_qty_tot=0;
                        $wo_yd_odr_delv_grey_qty_tot=0;
                        $wo_yd_odr_delv_balance_tot=0;
                        $wo_yd_odr_delv_fin_bal_tot=0;
                        foreach ($job_dtls as $rcv_dtls_id => $data) 
                        {
                            // echo "<pre>";print_r($data);die;
                            $bgcolor 	= ($i%2==0)?"#E9F3FF":"#FFFFFF";//$job_wise_span[$wo_no][$job_dtls_id]
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <? if($job_sl==0){?>
                                <td width="30" align="center" valign="center" rowspan="<? echo $job_wise_span[$wo_no][$job_dtls_id];?>"><?php echo $j_sl;?></td>
                                <? } ?>
                                <td width="120" align="center"><?php echo $data['YD_JOB'];$i++;?></td>
                                <td width="100" align="center"><?php echo $data['WO_NO'];?></td>
                                <td width="60" align="center"><?php echo $data['SALES_ORDER_NO'];?></td>
                                <td width="100" align="center"><?php echo $party_arr[$data['PARTY_ID']];?></td>
                                <td width="60" align="center"><?php echo $data['BUYER_BUYER'];?></td>
                                <td width="80" align="center"><?php echo $data['RECEIVE_DATE'];?></td>
                                <td width="80" align="center"><?php echo $data['DELIVERY_DATE'];?></td>
                                <td width="50" align="center"><?php echo $w_pro_type_arr[$data['PRO_TYPE']];?></td>
                                <td width="60" align="center"><?php echo $yd_type_arr[$data['YD_TYPE']];?></td>
                                <td width="50" align="center"><?php echo $count_type_arr[$data['COUNT_TYPE']];?></td>
                                <td width="50" align="center"><?php echo $yarn_type[$data['YARN_TYPE_ID']];?></td>
                                <td width="50" align="center"><?php echo $count_arr[$data['COUNT_ID']];?></td>
                                <td align="center"><?php echo $composition[$data['YARN_COMPOSITION_ID']];?></td>
                                <td width="60" align="center"><?php echo $color_range[$data['ITEM_COLOR_ID']];?></td>
                                <td width="60" align="center"><?php echo $color_arr[$data['YD_COLOR_ID']];?></td>
                                <td width="60" align="center"><?php echo $data['LOT'];?></td>
                                <td width="100" align="center"><?php echo $data['RECEIVE_DATE'];?></td>
                                <td width="100" align="center"><?php echo $data['CHALAN_NO'];?></td>
                                <td width="100" align="center"><?php echo chop($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['DELV_DATE'],"<br><p class='sep-cell'></p>");?></td>
                                <td width="100" align="center"><?php echo chop($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['YD_RECEIVE'],"<br><p class='sep-cell'></p>");//$wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['YD_RECEIVE'];?></td>
                                <td width="100" align="center"><?php echo chop($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['VEHICLE_NO'],"<br><p class='sep-cell'></p>");//$wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['VEHICLE_NO'];?></td>
                                <td width="100" align="center">
                                    <?php //echo $data['BATCH_NUMBER'];
                                    $delv_yd_batch=explode(",",chop($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['GRAY_LOT'],","));
                                        $k=0;
                                        $tot_batch= count($delv_yd_batch)-1;
                                        foreach ($delv_yd_batch as  $batch_data) {
                                            if($k==$tot_batch){
                                                echo "<p>".$batch_data."</p>";
                                            }else{
                                                echo "<p class='sep-cell'>".$batch_data."</p>";
                                            }
                                           $k++;
                                        }
                                    
                                    ?>
                                </td>
                                <? if($job_sl==0){?>
                                <td align="right" width="50" valign="center"  rowspan="<? echo $job_wise_span[$wo_no][$job_dtls_id];?>"><?php echo number_format($data['ORDER_QUANTITY'],2,".","");?></td>
                                <td width="50" title="<?php echo $data['PROCESS_LOSS']."%";?>" align="right" rowspan="<? echo $job_wise_span[$wo_no][$job_dtls_id];?>" style="vertical-align: center"><?php echo number_format($data['TOTAL_ORDER_QUANTITY'],2,".","");?></td>
                                <?}?>
                                <td align="right" width="50"><?php echo $rcv_qty = number_format($data['RECEIVE_QTY'],2,".","");?></td>
                                <td align="right" width="50"><?php echo $return_qty = 0;?></td>
                                <td align="right" width="50"><?php $total_rcv_qty = $rcv_qty-$return_qty;echo number_format($total_rcv_qty,2,".","");?></td>
                                <td width="50" align="right" title="<?php echo $data['PROCESS_LOSS']."%";?>"><?php $to_be_delv=$total_rcv_qty-(($data['PROCESS_LOSS']/100)*$total_rcv_qty);echo number_format($to_be_delv,2,".","");?></td>
                                <? if($job_sl==0){?>
                                <td align="right" width="50" rowspan="<? echo $job_wise_span[$wo_no][$job_dtls_id];?>"><?php echo $issued_qty = number_format($wo_wise_iss_arr[$wo_no][$job_dtls_id]['ISSUED_QTY'],2,".","");?></td>
                                
                                <? } ?>
                                
                                <td align="right" width="50"><?php echo chop($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['DELV_QTY_brLn'],"<br><p class='sep-cell'></p>");$delv_qty = number_format($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['DELV_QTY'],2,".","");//$wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['DELV_QTY'];?></td>
                                <td align="right" width="50"><?php echo chop($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['DELV_GREY_QTY_brLn'],"<br><p class='sep-cell'></p>");$delv_grey_qty = number_format($wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['DELV_GREY_QTY'],2,".","");//$wo_wise_delv_arr[$wo_no][$job_dtls_id][$rcv_dtls_id]['DELV_GREY_QTY'];?></td>  
                                <td align="right" width="50"><?php $delv_balance = $total_rcv_qty-$delv_grey_qty; echo number_format($delv_balance,2,".","");?></td>
                                <td align="right" width="50"><?php $delv_fin_balance = $to_be_delv-$delv_qty; echo number_format($delv_fin_balance,2,".","");?></td>
                                
                            </tr>
                            <?
                            $wo_yd_odr_rcv_qty_tot+=$data['RECEIVE_QTY'];
                            $wo_yd_odr_return_qty_tot+=$return_qty;
                            $wo_yd_odr_tot_rcv_qty_tot+=$total_rcv_qty;
                            $wo_yd_odr_to_be_delv_tot+=$to_be_delv;
                            $wo_yd_odr_issued_qty_tot+=$issued_qty;
                            $wo_yd_odr_delv_qty_tot+=$delv_qty;
                            $wo_yd_odr_delv_grey_qty_tot+=$delv_grey_qty;
                            $wo_yd_odr_delv_balance_tot+=$delv_balance;
                            $wo_yd_odr_delv_fin_bal_tot+=$delv_fin_balance;
                            $job_sl++;  

                        }
                        $wo_yd_odr_order_qty_tot+=$data['ORDER_QUANTITY'];
                        $wo_yd_odr_order_grey_qty_tot+=$data['TOTAL_ORDER_QUANTITY'];


                        
                        ?>
                            <tr bgcolor="#D3D3D3">
                                <td colspan="23" align="right"><strong>Color Wise Sub Total:</strong></td>
                                <td align="right"><strong><strong><?php echo number_format($wo_yd_odr_order_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_order_grey_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_rcv_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_return_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_tot_rcv_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_to_be_delv_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_issued_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_delv_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_delv_grey_qty_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_delv_balance_tot,2,".",",");?></strong></td>
                                <td align="right"><strong><?php echo number_format($wo_yd_odr_delv_fin_bal_tot,2,".",",");?></strong></td>
                            </tr>
                        <?
                        $j_sl++;
                        $wo_order_qty_tot+=$wo_yd_odr_order_qty_tot;
                        $wo_order_grey_qty_tot+=$wo_yd_odr_order_grey_qty_tot;
                        $wo_rcv_qty_tot+=$wo_yd_odr_rcv_qty_tot;
                        $wo_return_qty_tot+=$wo_yd_odr_return_qty_tot;
                        $wo_tot_rcv_qty_tot+=$wo_yd_odr_tot_rcv_qty_tot;
                        $wo_to_be_delv_tot+=$wo_yd_odr_to_be_delv_tot;
                        $wo_issued_qty_tot+=$wo_yd_odr_issued_qty_tot;
                        $wo_delv_qty_tot+=$wo_yd_odr_delv_qty_tot;
                        $wo_delv_grey_qty_tot+=$wo_yd_odr_delv_grey_qty_tot;
                        $wo_delv_balance_tot+=$wo_yd_odr_delv_balance_tot;
                        $wo_delv_fin_bal_tot+=$wo_yd_odr_delv_fin_bal_tot;
                    }
                    ?>
                    <tr bgcolor="#728FCE" height="18">
                        <td colspan="23" align="right"><strong>WO Wise Sub Total:</strong></td>
                        <td align="right"><strong><strong><?php echo number_format($wo_order_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_order_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_return_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_tot_rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_to_be_delv_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_issued_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_delv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_delv_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_delv_balance_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($wo_delv_fin_bal_tot,2,".",",");?></strong></td>
                    </tr>
                    <?
                
                    $order_qty_tot+=$wo_order_qty_tot;
                    $order_grey_qty_tot+=$wo_order_grey_qty_tot;
                    $rcv_qty_tot+=$wo_rcv_qty_tot;
                    $return_qty_tot+=$wo_return_qty_tot;
                    $tot_rcv_qty_tot+=$wo_tot_rcv_qty_tot;
                    $to_be_delv_tot+=$wo_to_be_delv_tot;
                    $issued_qty_tot+=$wo_issued_qty_tot;
                    $delv_qty_tot+=$wo_delv_qty_tot;
                    $delv_grey_qty_tot+=$wo_delv_grey_qty_tot;
                    $delv_balance_tot+=$wo_delv_balance_tot;
                    $delv_fin_bal_tot+=$wo_delv_fin_bal_tot;
                }
                    ?>
                    <tr bgcolor="#FFA500" height="20" >
                        <td colspan="23" align="right"><strong>Total:</strong></td>
                        <td align="right"><strong><strong><?php echo number_format($order_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($order_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($return_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($tot_rcv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($to_be_delv_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($issued_qty_tot,2,".",",");?></strong></td>
                        
                        <td align="right"><strong><?php echo number_format($delv_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($delv_grey_qty_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($delv_balance_tot,2,".",",");?></strong></td>
                        <td align="right"><strong><?php echo number_format($delv_fin_bal_tot,2,".",",");?></strong></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
            </table>
        </div>
    </div>
    <!-- </fieldset> -->

    <?
    
    	$html = ob_get_contents();
        ob_clean();
        //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
        foreach (glob("*.xls") as $filename) {
        //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename=$user_id."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w');	
        $is_created = fwrite($create_new_doc, $html);
        echo "$html**$filename"; 
        exit();
}