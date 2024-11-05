<?php
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_buyer")
{ 
    echo create_drop_down( "cbo_party_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select Party--", $selected, "" );
    exit();      
}

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    
    $company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

    $company_id = str_replace("'","",$cbo_company_id);
    $item_category_id = str_replace("'","",$cbo_item_category);
    $party_id = str_replace("'","",$cbo_party_id);  
    $date_from = str_replace("'","",$txt_date_from);
    $date_to = str_replace("'","",$txt_date_to);

    //$batch_sql = "select a.id, a.batch_no, a.extention_no, b.id as item_id, b.fabric_from, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    //$batch_sql_result=sql_select($batch_sql);
    foreach($batch_sql_result as $row)
    {
        $batch_array[$row[csf('id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
    }
    // echo "<pre>"; print_r($batch_array); echo "</pre>";die;

    if($item_category_id == 0) $item_category_cond=""; else  $item_category_cond=" and b.item_category_id in ($item_category_id)";
    if($party_id == 0) $party_cond=""; else  $party_cond=" and a.party_id in ($party_id)";
    if($party_id == 0) $party_issue_cond=""; else  $party_issue_cond=" and d.party_id in ($party_id)";
    if($party_id == 0) $party_issue_and_batch_cond=""; else  $party_issue_and_batch_cond=" and d.party_id in ($party_id)";
    //echo $party_issue_cond.'system';

    $recv_date_cond="";
    $dates_pass_pop_up_data=" * ";
    
    if($date_from && $date_to)
    {
        $dates_pass_pop_up_data=$date_from."*".$date_to;        
        if($db_type==0)
        {
            $rcv_date_cond = " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
            $issue_date_cond = " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
            $batch_date_cond = " and a.batch_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
            $delv_date_cond = " and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
            $bill_date_cond = " and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
        }
        else
        {
            $rcv_date_cond = " and a.subcon_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
            $issue_date_cond = " and a.subcon_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
            $batch_date_cond = " and a.batch_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
            $delv_date_cond = " and a.delivery_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
            $bill_date_cond = " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
        }
    }    
    
    ob_start();
    ?>
    <fieldset style="width:900px">
        <table cellpadding="0" cellspacing="0" width="900" rules="all">         
            <tr class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="7" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="7" style="font-size:20px"><strong><? echo $company_arr[$company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td align="center" width="100%" colspan="7" style="font-size:14px">
                    <? 
                    if($txt_date_from != "" && $txt_date_to != "") echo "From: ".change_date_format($date_from,'dd-mm-yyyy')." To ".change_date_format($date_to,'dd-mm-yyyy')."";
                    ?>
                </td>
            </tr>
        </table>
        <table width="900" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all" class="table_header_1" align="left">
            <thead>
                <tr>
                    <th width="60">SL</div></th>
                    <th width="150">Party Name</th>
                    <th width="150">Receive Qnty</th>
                    <th width="150">Issue to Batch</th>
                    <th width="150">Batch Qnty</th>
                    <th width="150">Delivery Qnty</th>
                    <th width="90">Bill Qnty</th>
                </tr>
            </thead>
        </table>
    <div style="max-height:300px; overflow-y:scroll; overflow-x:none; width:920px" id="scroll_body_1">
        <table width="900" border="1" cellspacing="0" cellpadding="0" rules="all" id="table_body" class="rpt_table">
            <tbody>
                <?             

                //Receive  Quantity.........
                $party_wise_array=array();
                $sql_rcv="SELECT a.party_id, SUM(b.quantity) AS rcv_quantity FROM sub_material_mst a, sub_material_dtls b WHERE a.company_id=$company_id $rcv_date_cond $item_category_cond $party_cond AND a.id=b.mst_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0 GROUP BY a.party_id";
                //echo $sql_rcv;
                $sql_rcv_rslt = sql_select($sql_rcv);
                foreach ($sql_rcv_rslt as $row) 
                {
                    $party_wise_array[$row[csf("party_id")]]['rcv_qty'] = $row[csf("rcv_quantity")];
                }


                // Issue Quantity..........
                //$sql_issue="SELECT d.party_id, b.quantity AS issue_qty FROM sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c, subcon_ord_mst d WHERE a.id = b.mst_id AND b.order_id = c.id AND c.job_no_mst = d.subcon_job AND a.trans_type = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.company_id=$company_id $issue_date_cond";

                $sql_issue="SELECT d.party_id, b.quantity AS issue_qty FROM sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c, subcon_ord_mst d WHERE a.id = b.mst_id AND b.order_id = c.id AND c.job_no_mst = d.subcon_job AND a.trans_type = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND a.company_id=$company_id $issue_date_cond $item_category_cond $party_issue_and_batch_cond";
                //echo $sql_issue;   
                $sql_issue_rslt = sql_select($sql_issue);                
                foreach ($sql_issue_rslt as $row) 
                {
                    $party_wise_array[$row[csf("party_id")]]["issue_qty"] +=$row[csf("issue_qty")];                    
                } 


                // Batch Quantity...........
                $sql_batch="SELECT d.party_id, b.batch_qnty as batch_qty FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d WHERE a.id=b.mst_id AND b.po_id=c.id AND c.job_no_mst=d.subcon_job AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND a.company_id=$company_id $batch_date_cond $party_issue_and_batch_cond";
                //echo $sql_batch;
                $sql_batch_rslt = sql_select($sql_batch);              
                foreach ($sql_batch_rslt as $row) 
                {
                    $party_wise_array[$row[csf("party_id")]]["batch_qty"] +=$row[csf("batch_qty")];                    
                }



                // Delivery Quantity..........
                $sql_delv="SELECT a.party_id, SUM(b.delivery_qty) AS delv_qty FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.company_id=$company_id $delv_date_cond $party_cond AND a.id=b.mst_id AND a.status_active=1 AND a.is_deleted= 0 AND b.is_deleted=0 GROUP BY a.party_id";
                //echo $sql_delv;
                $sql_delv_rslt = sql_select($sql_delv);
                foreach ($sql_delv_rslt as $row) 
                {
                    $party_wise_array[$row[csf("party_id")]]["delv_qty"] =$row[csf("delv_qty")];
                }



                // Bill Quantity..........
                $sql_bill="SELECT a.party_id, SUM(b.delivery_qty) AS bill_qty FROM subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b WHERE a.company_id=$company_id $bill_date_cond $party_cond AND a.id=b.mst_id AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 GROUP BY a.party_id";
                // echo $sql_bill;
                $sql_bill_result = sql_select($sql_bill);
                foreach ($sql_bill_result as $row) 
                {
                    $party_wise_array[$row[csf("party_id")]]["bill_qty"] =$row[csf("bill_qty")];                    
                }

                //$new_array = array_merge($rcv_party_array, $del_party_array, $bill_party_array);
                //$data_array_new = array_unique($new_array);
                //print_r($data_array_new);

                $tot_rcv_qty = 0;
                $tot_issue_qty = 0;
                $tot_batch_qty = 0;
                $tot_delv_qty = 0;
                $tot_bill_qty = 0;
                $i = 1;

                foreach($party_wise_array as $party_id => $val)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $j; ?>">  
                        <td width="60" align="center"><? echo $i; ?></td>
                        <td width="150" align="center"><? echo $buyer_arr[$party_id]; ?></td>

                        <td width="150" align="center"><p><a href="##" style='color:#000' onClick="show_popup_report_details('receive_pop_up','<? echo $party_id."*".$item_category_id."*".$dates_pass_pop_up_data."*".$company_id; ?>','1050px');"><font color="blue"><b>
                            <? 
                                $rcv_qty = $val["rcv_qty"];
                                echo number_format($rcv_qty,2); 
                                $tot_rcv_qty += $rcv_qty;                             
                            ?>                          
                        </b></font></a></p></td>

                        <td width="150" align="center"><p><a href="##" style='color:#000' onClick="show_popup_report_details('issue_pop_up','<? echo $party_id."*".$item_category_id."*".$dates_pass_pop_up_data."*".$company_id; ?>','1050px');"><font color="blue"><b>
                            <? 
                                $tot_issue = $val["issue_qty"];
                                echo number_format($tot_issue,2);
                                $tot_issue_qty += $tot_issue;
                            ?>                                
                        </b></font></a></p></td>

                        <td width="150" align="center"><p><a href="##" style='color:#000' onClick="show_popup_report_details('batch_pop_up','<? echo $party_id."*".$item_category_id."*".$dates_pass_pop_up_data."*".$company_id; ?>','680px');"><font color="blue"><b>
                            <? 
                                $tot_qty = $val["batch_qty"];
                                echo number_format($tot_qty,2);
                                $tot_batch_qty += $tot_qty;                             
                            ?>
                        </b></font></a></p></td>

                        <td width="150" align="center"><p><a href="##" style='color:#000' onClick="show_popup_report_details('delivery_pop_up','<? echo $party_id."*".$item_category_id."*".$dates_pass_pop_up_data."*".$company_id; ?>','780px');"><font color="blue"><b>
                            <?
                                $delv_qty = $val["delv_qty"];
                                echo number_format($delv_qty,2);
                                $tot_delv_qty += $delv_qty;
                            ?>
                        </b></font></a></p></td>


                        <td width="90" align="center"><p><a href="##" style='color:#000' onClick="show_popup_report_details('bill_pop_up','<? echo $party_id."*".$item_category_id."*".$dates_pass_pop_up_data."*".$company_id; ?>','720px');"><font color="blue"><b>
                            <?
                                $bill_qty = $val["bill_qty"];
                                echo number_format($bill_qty,2);
                                $tot_bill_qty += $bill_qty;
                            ?>
                        </b></font></a></p></td>
                    </tr>
                    <?
                    $i++;
                }
                
            ?>              
            </tbody>
            <tfoot>
                <th colspan="2" width="210" style="text-align: right;">Total:&nbsp;</th>
                <th width="150" style="text-align: center;"><? echo number_format($tot_rcv_qty, 2); ?></th>
                <th width="150" style="text-align: center;"><? echo number_format($tot_issue_qty, 2); ?></th>
                <th width="150" style="text-align: center;"><? echo number_format($tot_batch_qty, 2); ?></th>
                <th width="150" style="text-align: center;"><? echo number_format($tot_delv_qty, 2); ?></th>
                <th width="90" style="text-align: center;"><? echo number_format($tot_bill_qty, 2); ?></th>
            </tfoot>    
        </table>
    </div>  
    </fieldset>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
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

if($action=="receive_pop_up")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    ?>
    <script type="text/javascript">
        $(function(){
            setFilterGrid("html_search",-1);
        }); 

        function print_window()
        {
           document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none"; 
            $("#html_search tr:first").hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body></html>');
            d.close();
            document.getElementById('scroll_body').style.overflow="auto"; 
            document.getElementById('scroll_body').style.maxHeight="400px";
            $("#html_search tr:first").show();
        }    
    </script>
    <?
    $data=explode('*',$datas);
    $party_id = $data[0];
    $item_category_id = $data[1];
    $popup_date_from = $data[2];
    $popup_date_to = $data[3];
    $company_id = $data[4];
    $rcv_date_cond = " AND a.subcon_date between '".$popup_date_from."' and '".$popup_date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");

    $sql_sub_ord="SELECT a.subcon_job, b.id as po_id, b.order_no, b.cust_buyer, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.party_id=$party_id  AND a.company_id=$company_id ";
    $result_sub_ord = sql_select($sql_sub_ord);

    foreach ($result_sub_ord as $row) 
    {  
        $subcon_dtls_arr[$row[csf('po_id')]]['subcon_job']=$row[csf('subcon_job')];
        $subcon_dtls_arr[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
        $subcon_dtls_arr[$row[csf('po_id')]]['cust_buyer']=$row[csf('cust_buyer')];
        $subcon_dtls_arr[$row[csf('po_id')]]['cust_style_ref']=$row[csf('cust_style_ref')]; 
    }
    //print_r($subcon_dtls_arr).'system';


    if ($db_type==0)
    {
        $sql_rcv_dtls="SELECT a.inserted_by as user_id, a.party_id, a.subcon_date as receive_date, a.chalan_no, a.sys_no as receive_id, b.order_id, b.material_description, b.gsm, b.grey_dia, b.stitch_length, b.quantity, b.mc_dia, b.mc_gauge, b.insert_date, date_format(b.insert_date, '%Y') as year FROM sub_material_mst a, sub_material_dtls b WHERE a.id=b.mst_id AND a.company_id= $company_id AND b.item_category_id=$item_category_id AND a.party_id=$party_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0 $rcv_date_cond";
    }
    else
    {        
        $sql_rcv_dtls="SELECT a.inserted_by as user_id, a.party_id, a.subcon_date as receive_date, a.chalan_no, a.sys_no as receive_id, b.order_id, b.material_description, b.gsm, b.grey_dia, b.stitch_length, b.quantity, b.mc_dia, b.mc_gauge, b.insert_date, to_char(b.insert_date,'YYYY') as year FROM sub_material_mst a, sub_material_dtls b WHERE a.id=b.mst_id AND a.company_id= $company_id AND b.item_category_id=$item_category_id AND a.party_id=$party_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0 $rcv_date_cond";
    }    
    //echo $sql_rcv_dtls;die;
    $sql_rcv_dtls_rslt = sql_select($sql_rcv_dtls);
    ?>
    
    <div style="width:670px;" style="margin: 0 auto;">
        <div style="float: left; margin-left: 400px;">
            <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        </div>            
        <div id="report_container" style="float: left;"></div>
    </div>
    <?
        ob_start();
    ?>
    <div style="width:670px;" id="report_div">

        <table width="1510" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" border="1">
            <thead>
                <tr>
                    <th width="30" style="word-break: break-all;">SL</th>
                    <th width="40" style="word-break: break-all;">Year</th>
                    <th width="120" style="word-break: break-all;">Job No</th>
                    <th width="100" style="word-break: break-all;">Order No</th>
                    <th width="80" style="word-break: break-all;">Style No</th>
                    <th width="80" style="word-break: break-all;">Cust. Buyer</th>
                    <th width="80" style="word-break: break-all;">Receive Date</th>
                    <th width="120" style="word-break: break-all;">Receive ID</th>
                    <th width="70" style="word-break: break-all;">Challan No</th>
                    <th width="100" style="word-break: break-all;">Party Name</th>
                    <th width="140" style="word-break: break-all;">Fabric Descrioption</th>
                    <th width="50" style="word-break: break-all;">GSM</th>
                    <th width="50" style="word-break: break-all;">Dia</th>
                    <th width="60" style="word-break: break-all;">S. Length</th>
                    <th width="80" style="word-break: break-all;">Receive Qty</th>
                    <th width="50" style="word-break: break-all;">MC.Dia</th>
                    <th width="50" style="word-break: break-all;">MC.GG</th>
                    <th width="130" style="word-break: break-all;">Insert Date</th>
                    <th width="80" style="word-break: break-all;">User</th>
                </tr>
            </thead>
        </table>
        <div style="width:1530px; max-height:310px;" align="left" id="scroll_body">   
            <table width="1510" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_rcv_qty = 0;
                foreach ($sql_rcv_dtls_rslt as $row) { 
                ?>    
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">
                        <td width="30" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="40" align="center" style="word-break: break-all;"><? echo $row[csf('year')]; ?></td>

                        <td width="120" align="center" style="word-break: break-all;"><? echo $subcon_dtls_arr[$row[csf('order_id')]]['subcon_job'];; ?></td>
                        <td width="100" align="center" style="word-break: break-all;"><? echo $subcon_dtls_arr[$row[csf('order_id')]]['order_no']; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo  $subcon_dtls_arr[$row[csf('order_id')]]['cust_style_ref']; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo  $subcon_dtls_arr[$row[csf('order_id')]]['cust_buyer']; ?></td>

                        <td width="80" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="120" align="center" style="word-break: break-all;"><? echo $row[csf('receive_id')]; ?></td>
                        <td width="70" align="center" style="word-break: break-all;"><? echo $row[csf('chalan_no')]; ?></td>
                        <td width="100" align="center" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('party_id')]]; ?></td>
                        <td width="140" align="center" style="word-break: break-all;"><? echo $row[csf('material_description')]; ?></td>
                        <td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('gsm')]; ?></td>
                        <td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('grey_dia')]; ?></td>
                        <td width="60" align="center" style="word-break: break-all;"><? echo $row[csf('stitch_length')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;">
                            <? 
                                $tot_rcv_qty += $row[csf('quantity')];
                                echo number_format($row[csf('quantity')],2); 
                            ?>                                
                        </td>
                        <td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('mc_dia')]; ?></td>
                        <td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('mc_gauge')]; ?></td>
                        <td width="130" align="center" style="word-break: break-all;"><? echo $row[csf('insert_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $user_arr[$row[csf('user_id')]]; ?></td>
                    </tr>
                <?
                $i++;
                }
                ?>        
                </tbody>
                <tfoot>
                    <th colspan="14" width="1100" style="text-align: right;">Total&nbsp;</th>
                    <th width="80" style="text-align: center;"><? echo number_format($tot_rcv_qty,2); ?></th>
                    <th width="50"></th>
                    <th width="50"></th>
                    <th width="130"></th>
                    <th width="80"></th>
                </tfoot>
            </table>
        </div>
    <?
    $html=ob_get_contents();
    ob_flush();
    
    foreach (glob(""."*.xls") as $filename) 
    {
       @unlink($filename);
    }
    
    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');   
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
    </div> 
    <?                              
}

if($action=="issue_pop_up")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    //print_r($_REQUEST);
    ?>
    <script type="text/javascript">
        $(function(){
            setFilterGrid("html_search",-1);
        });

        function print_window()
        {
           document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none"; 
            $("#html_search tr:first").hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body></html>');
            d.close();
            document.getElementById('scroll_body').style.overflow="auto"; 
            document.getElementById('scroll_body').style.maxHeight="400px";
            $("#html_search tr:first").show();
        }     
    </script>
    <?
    $data=explode('*',$datas);
    $party_id = $data[0];
    $item_category_id = $data[1];
    $popup_date_from = $data[2];
    $popup_date_to = $data[3];
    $company_id = $data[4];
    $issue_date_cond = " AND a.subcon_date between '".$popup_date_from."' and '".$popup_date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"); 
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");   
   


    $sql_sub_ord="SELECT a.subcon_job, b.id as po_id, b.order_no, b.cust_buyer, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst AND a.company_id=$company_id ";
    $result_sub_ord = sql_select($sql_sub_ord);

    foreach ($result_sub_ord as $row) 
    {  
        $subcon_dtls_arr[$row[csf('po_id')]]['subcon_job']=$row[csf('subcon_job')];
        $subcon_dtls_arr[$row[csf('po_id')]]['order_no']=$row[csf('order_no')];
        $subcon_dtls_arr[$row[csf('po_id')]]['cust_buyer']=$row[csf('cust_buyer')];
        $subcon_dtls_arr[$row[csf('po_id')]]['cust_style_ref']=$row[csf('cust_style_ref')]; 
    }
    // print_r($subcon_dtls_arr[$row[csf('po_id')]]).'system';


    $sql_rcv="SELECT b.order_id, b.stitch_length, b.mc_dia, b.mc_gauge from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and b.status_active=2 and  a.is_deleted=0 and a.trans_type=1";
    //echo $sql_rcv;
    $sql_rcv_rslt = sql_select($sql_rcv);

    foreach ($sql_rcv_rslt as $row) 
    {  
        $subcon_rcv_dtls_arr[$row[csf('order_id')]]['stitch_length'] = $row[csf('stitch_length')];
        $subcon_rcv_dtls_arr[$row[csf('order_id')]]['mc_dia'] = $row[csf('mc_dia')];
        $subcon_rcv_dtls_arr[$row[csf('order_id')]]['mc_gauge'] = $row[csf('mc_gauge')];
    }



    if ($db_type==0)
    {
        $sql_issue_dtls= "SELECT d.party_id, a.inserted_by as user_id, a.subcon_date as issue_date, a.chalan_no, a.sys_no as issue_id, a.prod_source, b.order_id, b.material_description, b.gsm, b.grey_dia, b.quantity as issue_qty, b.insert_date, date_format(b.insert_date, '%Y') as year FROM sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c, subcon_ord_mst d WHERE a.id=b.mst_id AND b.order_id = c.id AND c.job_no_mst = d.subcon_job AND a.company_id= $company_id AND b.item_category_id=$item_category_id AND a.trans_type=2 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 and d.party_id=$party_id $issue_date_cond";
    }
    else
    {        
        //$sql_issue_dtls="SELECT a.party_id, a.subcon_date as issue_date, a.chalan_no, a.sys_no as issue_id, a.prod_source, b.order_id as order_id, b.material_description, b.gsm, b.grey_dia, b.stitch_length, b.quantity as issue_qty, b.mc_dia, b.mc_gauge, b.insert_date, to_char(b.insert_date,'YYYY') as year FROM sub_material_mst a, sub_material_dtls b WHERE a.id=b.mst_id AND a.company_id= $company_id AND b.item_category_id=$item_category_id AND a.trans_type=2 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 $issue_date_cond";
        $sql_issue_dtls= "SELECT d.party_id, a.inserted_by as user_id, a.subcon_date as issue_date, a.chalan_no, a.sys_no as issue_id, a.prod_source, a.issue_to, b.order_id, b.material_description, b.gsm, b.grey_dia, b.quantity as issue_qty, b.insert_date, to_char(b.insert_date,'YYYY') as year FROM sub_material_mst a, sub_material_dtls b, subcon_ord_dtls c, subcon_ord_mst d WHERE a.id=b.mst_id AND b.order_id = c.id AND c.job_no_mst = d.subcon_job AND a.company_id= $company_id AND b.item_category_id=$item_category_id AND a.trans_type=2 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 and d.party_id=$party_id $issue_date_cond";
    }    
    //echo $sql_issue_dtls;
    $sql_issue_dtls_rslt = sql_select($sql_issue_dtls);

    ?>
    
    <div style="width:670px;" style="margin: 0 auto;">
        <div style="float: left; margin-left: 400px;">
            <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        </div>            
        <div id="report_container" style="float: left;"></div>
    </div>
    <?
        ob_start();
    ?>
    <div style="width:670px;" id="report_div">  
        <table cellpadding="0"  cellspacing="0" width="1700" class="rpt_table" rules="all" border="1">
            <thead>
                <tr>
                    <th width="30" style="word-break: break-all;">SL</th>
                    <th width="40" style="word-break: break-all;">Year</th>
                    <th width="120" style="word-break: break-all;">Job No</th>
                    <th width="100" style="word-break: break-all;">Order No</th>
                    <th width="80" style="word-break: break-all;">Style No</th>
                    <th width="80" style="word-break: break-all;">Cust. Buyer</th>
                    <th width="80" style="word-break: break-all;">Issue Date</th>
                    <th width="120" style="word-break: break-all;">Issue ID</th>
                    <th width="70" style="word-break: break-all;">Challan No</th>
                    <th width="100" style="word-break: break-all;">Party Name</th>
                    <th width="100" style="word-break: break-all;">Prod Source</th>
                    <th width="100" style="word-break: break-all;">Issue To</th>
                    <th width="140" style="word-break: break-all;">Fabric Descrioption</th>
                    <th width="40" style="word-break: break-all;">GSM</th>
                    <th width="50" style="word-break: break-all;">Dia</th>
                    <th width="60" style="word-break: break-all;">S. Length</th>
                    <th width="80" style="word-break: break-all;">Issue Qty</th>
                    <th width="50" style="word-break: break-all;">MC.Dia</th>
                    <th width="50" style="word-break: break-all;">MC.GG</th>
                    <th width="130" style="word-break: break-all;">Insert Date</th>
                    <th width="80" style="word-break: break-all;">User</th>
                </tr>
            </thead>
        </table>
        <div style="width:1720px; max-height:310px;" align="left" id="scroll_body"> 
            <table cellpadding="0" cellspacing="0" width="1700" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_issue_qty = 0;
                $supp_inhouse=return_library_array( "select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name",'id','company_name');
                $supp_outbound=return_library_array( "select a.id, a.supplier_name from lib_supplier a where a.status_active=1 and a.is_deleted=0 order by a.supplier_name",'id','supplier_name');
                
                //print_r($supp_inhouse);
                //echo $supp_outbound[$company_id].'system';
                foreach ($sql_issue_dtls_rslt as $row) { 
                ?> 
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">
                        <td width="30" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="40" align="center" style="word-break: break-all;"><? echo $row[csf('year')]; ?></td>
                        <td width="120" align="center" style="word-break: break-all;"><? echo $subcon_dtls_arr[$row[csf('order_id')]]['subcon_job']; ?></td>
                        <td width="100" align="center" style="word-break: break-all;"><? echo $subcon_dtls_arr[$row[csf('order_id')]]['order_no']; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo  $subcon_dtls_arr[$row[csf('order_id')]]['cust_style_ref']; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo  $subcon_dtls_arr[$row[csf('order_id')]]['cust_buyer']; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="120" align="center" style="word-break: break-all;"><? echo $row[csf('issue_id')]; ?></td>
                        <td width="70" align="center" style="word-break: break-all;"><? echo $row[csf('chalan_no')]; ?></td>
                        <td width="100" align="center" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('party_id')]]; ?></td>

                        <td width="100" align="center" style="word-break: break-all;"><? echo $knitting_source[$row[csf('prod_source')]];?>
                        </td>
                        <td width="100" align="center" style="word-break: break-all;">
                            <? 
                                if($row[csf('prod_source')] == 3)
                                {
                                    echo $supp_outbound[$row[csf('party_id')]];
                                }
                                elseif ($row[csf('prod_source')] == 1) 
                                {
                                    echo $supp_inhouse[$company_id];
                                }
                                else 
                                { 
                                    echo ""; 
                                }
                            ?>
                        </td>
                        <td width="140" align="center" style="word-break: break-all;"><? echo $row[csf('material_description')]; ?></td>
                        <td width="40" align="center" style="word-break: break-all;"><? echo $row[csf('gsm')]; ?></td>
                        <td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('grey_dia')]; ?></td>
                        <td width="60" align="center" style="word-break: break-all;"><? echo $subcon_rcv_dtls_arr[$row[csf('order_id')]]['stitch_length']; ?></td>
                        <td width="80" align="center" style="word-break: break-all;">
                            <? 
                                $tot_issue_qty += $row[csf('issue_qty')];
                                echo number_format($row[csf('issue_qty')],2); 
                            ?>
                        </td>
                        <td width="50" align="center" style="word-break: break-all;"><? echo $subcon_rcv_dtls_arr[$row[csf('order_id')]]['mc_dia']; ?></td>
                        <td width="50" align="center" style="word-break: break-all;"><? echo $subcon_rcv_dtls_arr[$row[csf('order_id')]]['mc_gauge']; ?></td>
                        <td width="130" align="center" style="word-break: break-all;"><? echo $row[csf('insert_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $user_arr[$row[csf('user_id')]]; ?></td>
                    </tr>
                <?
                $i++;
                }
                ?>        
                </tbody>
                <tfoot>
                    <th colspan="16" width="1340" style="text-align: right;">Total&nbsp;</th>
                    <th width="50" style="text-align: center;"><? echo number_format($tot_issue_qty,2); ?></th>
                    <th width="50"></th>
                    <th width="50"></th>
                    <th width="130"></th>
                    <th width="80"></th>
                </tfoot>
            </table>
        </div>    
    <?
    $html=ob_get_contents();
    ob_flush();
    
    foreach (glob(""."*.xls") as $filename) 
    {
       @unlink($filename);
    }
    
    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');   
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
    </div> 
    <?  

}

if($action=="batch_pop_up")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    ?>
    <script type="text/javascript">
        $(function(){
            setFilterGrid("html_search",-1);
        }); 

        function print_window()
        {
           document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none"; 
            $("#html_search tr:first").hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body></html>');
            d.close();
            document.getElementById('scroll_body').style.overflow="auto"; 
            document.getElementById('scroll_body').style.maxHeight="400px";
            $("#html_search tr:first").show();
        }    
    </script>
    <?
    $data=explode('*',$datas);
    $party_id = $data[0];
    $item_category_id = $data[1];
    $popup_date_from = $data[2];
    $popup_date_to = $data[3];
    $company_id = $data[4];
    $batch_date_cond = " AND a.batch_date between '".$popup_date_from."' and '".$popup_date_to."'";

    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");

    
    if ($db_type==0)
    {
        $sql_batch_dtls="SELECT a.inserted_by as user_id, a.batch_no, a.batch_date, a.insert_date, a.color_id, b.batch_qnty, d.party_id , c.order_no FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d WHERE a.id = b.mst_id AND b.po_id = c.id AND c.job_no_mst = d.subcon_job AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND a.company_id=$company_id and d.party_id=$party_id $batch_date_cond";
    }
    else
    {        
        $sql_batch_dtls="SELECT a.inserted_by as user_id, a.batch_no, a.batch_date, a.insert_date, a.color_id, b.batch_qnty, d.party_id, c.order_no FROM pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d WHERE a.id = b.mst_id AND b.po_id = c.id AND c.job_no_mst = d.subcon_job AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND a.company_id=$company_id and d.party_id=$party_id $batch_date_cond";
    }    
    //echo $sql_batch_dtls;
    $sql_batch_dtls_rslt = sql_select($sql_batch_dtls);

    ?>

    <div style="width:670px;" style="margin: 0 auto;">
        <div style="float: left; margin-left: 235px;">
            <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        </div>            
        <div id="report_container" style="float: left;"></div>
    </div>
    <?
        ob_start();
    ?>
    <div style="width:670px;" id="report_div">
    <fieldset style="width:650px; margin: 0 auto;">
        <table cellpadding="0" width="650" class="rpt_table" rules="all" border="1">
            <thead>
                <tr>
                    <th width="40" style="word-break: break-all;">SL</th>
                    <th width="80" style="word-break: break-all;">Batch Date</th>
                    <th width="80" style="word-break: break-all;">Batch No</th>
                    <th width="80" style="word-break: break-all;">Order No</th>
                    <th width="80" style="word-break: break-all;">Batch Color</th>
                    <th width="80" style="word-break: break-all;">Batch Qty</th>
                    <th width="130" style="word-break: break-all;">Insert Date</th>
                    <th width="80" style="word-break: break-all;">User</th>                        
                </tr>
            </thead>
        </table>
        <div style="width:670px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="650" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_batch_qty = 0;
                foreach ($sql_batch_dtls_rslt as $row) { 
                ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $row[csf('batch_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $row[csf('batch_no')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $row[csf('order_no')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;">
                            <? 
                                echo $row[csf('batch_qnty')];
                                $tot_batch_qty += $row[csf('batch_qnty')];
                            ?>                                
                        </td>
                        <td width="130" align="center" style="word-break: break-all;"><? echo $row[csf('insert_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $user_arr[$row[csf('user_id')]]; ?></td>
                    </tr>
                <?                
                $i++;
                }
                ?>                            
                </tbody>
                <tfoot>
                    <th colspan="5" width="360" style="text-align: right;">Total&nbsp;</th>
                    <th width="80" style="text-align: center;"><? echo number_format($tot_batch_qty,2); ?></th>
                    <th width="130"></th>
                    <th width="80"></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    $html=ob_get_contents();
    ob_flush();
    
    foreach (glob(""."*.xls") as $filename) 
    {
       @unlink($filename);
    }
    
    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');   
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
    </div> 
    <?  
}

if($action=="delivery_pop_up")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    ?>
    <script type="text/javascript">
        $(function(){
            setFilterGrid("html_search",-1);
        }); 

        function print_window()
        {
           document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none"; 
            $("#html_search tr:first").hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body></html>');
            d.close();
            document.getElementById('scroll_body').style.overflow="auto"; 
            document.getElementById('scroll_body').style.maxHeight="400px";
            $("#html_search tr:first").show();
        }    
    </script>
    <?
    $data=explode('*',$datas);
    $party_id = $data[0];
    $item_category_id = $data[1];
    $popup_date_from = $data[2];
    $popup_date_to = $data[3];
    $company_id = $data[4];
    $delv_date_cond = " AND a.delivery_date between '".$popup_date_from."' and '".$popup_date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
    $item_arr=return_library_array( "select order_id, material_description from sub_material_dtls", "order_id", "material_description");

    $order_array = array();
    $order_sql = "select b.id, b.order_no from subcon_ord_dtls b, subcon_ord_mst a where a.subcon_job=b.job_no_mst and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";    
    $order_array_result=sql_select($order_sql);
    foreach($order_array_result as $row)
    {
        $order_array[$row[csf('id')]]['order_no'] = $row[csf('order_no')];
    }


    if ($db_type==0)
    {
        $sql_delv_dtls="SELECT  a.inserted_by as user_id, a.party_id, a.delivery_no, a.delivery_date, a.insert_date, b.delivery_qty, b.order_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.company_id= $company_id AND a.party_id=$party_id AND a.status_active=1 AND a.is_deleted= 0 AND b.is_deleted=0 $delv_date_cond";
    }
    else
    {        
        $sql_delv_dtls="SELECT  a.inserted_by as user_id, a.party_id, a.delivery_no, a.delivery_date, a.insert_date, b.delivery_qty, b.order_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.company_id= $company_id AND a.party_id=$party_id AND a.status_active=1 AND a.is_deleted= 0 AND b.is_deleted=0 $delv_date_cond";
    }    
    //echo $sql_delv_dtls;
    $sql_delv_dtls_rslt = sql_select($sql_delv_dtls);
    ?>

    <div style="width:670px;" style="margin: 0 auto;">
        <div style="float: left; margin-left: 275px;">
            <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        </div>            
        <div id="report_container" style="float: left;"></div>
    </div>
    <?
        ob_start();
    ?>
    <div style="width:670px;" id="report_div">
    <fieldset style="width:750px; margin: 0 auto;">
        <table cellpadding="0" width="750" class="rpt_table" rules="all" border="1">
            <thead>
                <tr>
                    <th width="40" style="word-break: break-all;">SL</th>
                    <th width="120" style="word-break: break-all;">Delivery ID</th>
                    <th width="80" style="word-break: break-all;">Delivery Date</th>
                    <th width="80" style="word-break: break-all;">Order No</th>
                    <th width="140" style="word-break: break-all;">Description</th>
                    <th width="80" style="word-break: break-all;">Delivery Qty</th>
                    <th width="130" style="word-break: break-all;">Insert Date</th>
                    <th width="80" style="word-break: break-all;">User</th>                        
                </tr>
            </thead>
        </table>
        <div style="width:770px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="750" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_delv_qty = 0;
                foreach ($sql_delv_dtls_rslt as $row) { 
                ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">        
                        <td width="40" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="center" style="word-break: break-all;"><? echo $row[csf('delivery_no')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $row[csf('delivery_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
                        <td width="140" align="center" style="word-break: break-all;"><? echo $item_arr[$row[csf('order_id')]]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;">
                            <? 
                                echo number_format($row[csf('delivery_qty')],2);
                                $tot_delv_qty += $row[csf('delivery_qty')]; 
                            ?>                                
                        </td>
                        <td width="130" align="center" style="word-break: break-all;"><? echo $row[csf('insert_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $user_arr[$row[csf('user_id')]]; ?></td> 
                    </tr>
                <?
                $i++;
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="5" width="460" style="text-align: right;">Total&nbsp;</th>
                    <th width="80" style="text-align: center;"><? echo number_format($tot_delv_qty,2); ?></th>
                    <th width="130"></th>
                    <th width="80"></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    $html=ob_get_contents();
    ob_flush();
    
    foreach (glob(""."*.xls") as $filename) 
    {
       @unlink($filename);
    }
    
    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');   
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
    </div> 
    <?  
}

if($action=="bill_pop_up")
{
    echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
    ?>
    <script type="text/javascript">
        $(function(){
            setFilterGrid("html_search",-1);
        });

        function print_window()
        {
           document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none"; 
            $("#html_search tr:first").hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body></html>');
            d.close();
            document.getElementById('scroll_body').style.overflow="auto"; 
            document.getElementById('scroll_body').style.maxHeight="400px";
            $("#html_search tr:first").show();
        }     
    </script>
    <?
    $data=explode('*',$datas);
    $party_id = $data[0];
    $item_category_id = $data[1];
    $popup_date_from = $data[2];
    $popup_date_to = $data[3];
    $company_id = $data[4];
    $bill_date_cond = " AND a.bill_date between '".$popup_date_from."' and '".$popup_date_to."'";

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
    

    $order_array = array();
    $order_sql = "select a.id, a.order_no from  subcon_ord_dtls a, subcon_ord_mst b where a.job_no_mst=b.subcon_job and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";    
    $order_array_result=sql_select($order_sql);
    foreach($order_array_result as $row)
    {
        $order_array[$row[csf('id')]]['order_no'] = $row[csf('order_no')];
    }

    if ($db_type==0)
    {
        $sql_bill_dtls="SELECT  a.inserted_by as user_id, a.party_id, a.bill_no, a.bill_date, a.insert_date, b.delivery_qty, b.order_id, b.amount FROM subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b WHERE a.id=b.mst_id AND a.company_id= $company_id AND a.party_id=$party_id AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 $bill_date_cond";
    }
    else
    {        
        $sql_bill_dtls="SELECT  a.inserted_by as user_id, a.party_id, a.bill_no, a.bill_date, a.insert_date, b.delivery_qty, b.order_id, b.amount FROM subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b WHERE a.id=b.mst_id AND a.company_id= $company_id AND a.party_id=$party_id AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 $bill_date_cond";
    }    
    //echo $sql_bill_dtls;
    $sql_bill_dtls_rslt = sql_select($sql_bill_dtls);
    ?>

    <div style="width:670px;" style="margin: 0 auto;">
        <div style="float: left; margin-left: 255px;">
            <input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp;
        </div>            
        <div id="report_container" style="float: left;"></div>
    </div>
    <?
        ob_start();
    ?>
    <div style="width:670px;" id="report_div">
    <fieldset style="width:690px; margin: 0 auto;">
        <table cellpadding="0" width="690" class="rpt_table" rules="all" border="1" align="left">
            <thead>
                <tr>
                    <th width="40" style="word-break: break-all;">SL</th>
                    <th width="120" style="word-break: break-all;">Bill ID</th>
                    <th width="80" style="word-break: break-all;">Bill Date</th>
                    <th width="80" style="word-break: break-all;">Order No</th>
                    <th width="80" style="word-break: break-all;">Bill Qty</th>
                    <th width="80" style="word-break: break-all;">Amount</th>
                    <th width="130" style="word-break: break-all;">Insert Date</th>
                    <th width="80" style="word-break: break-all;">User</th>                        
                </tr>
            </thead>
        </table>
        <div style="width:710px; max-height:310px; overflow-y:scroll" id="scroll_body" align="left">
            <table cellpadding="0" width="690" class="rpt_table" rules="all" border="1" id="html_search">
                <tbody>
                <?
                $i = 1;
                $tot_bill_qty = 0;
                $tot_amount = 0;
                foreach ($sql_bill_dtls_rslt as $row) { 
                ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">     
                        <td width="40" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                        <td width="120" align="center" style="word-break: break-all;"><? echo $row[csf('bill_no')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $row[csf('bill_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><?  echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
                        <td width="80" align="center" style="word-break: break-all;">
                            <? 
                                echo number_format($row[csf('delivery_qty')],2);
                                $tot_bill_qty += $row[csf('delivery_qty')];
                            ?>
                        </td>
                        <td width="80" align="center" style="word-break: break-all;">
                            <? 
                                echo number_format($row[csf('amount')],2);
                                $tot_amount += $row[csf('amount')]; 
                            ?>
                        </td>
                        <td width="130" align="center" style="word-break: break-all;"><? echo $row[csf('insert_date')]; ?></td>
                        <td width="80" align="center" style="word-break: break-all;"><? echo $user_arr[$row[csf('user_id')]]; ?></td> 
                    </tr>
                <?
                $i++;
                }
                ?>                           
                </tbody>
                <tfoot>
                    <th colspan="4" width="320" style="text-align: right;">Total&nbsp;</th>
                    <th width="80" style="text-align: center;"><? echo number_format($tot_bill_qty,2); ?></th>
                    <th width="80" style="text-align: center;"><? echo number_format($tot_amount,2); ?></th>
                    <th width="130"></th>
                    <th width="80"></th>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    $html=ob_get_contents();
    ob_flush();
    
    foreach (glob(""."*.xls") as $filename) 
    {
       @unlink($filename);
    }
    
    //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');   
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
    </div> 
    <?
    
}
?>  

