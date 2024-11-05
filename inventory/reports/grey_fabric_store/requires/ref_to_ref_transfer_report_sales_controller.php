<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
    //$data=explode('_',$data);
    echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
    exit();
}


if($action=="generate_report")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    $cbo_company=str_replace("'","",$cbo_company_id);
    $cbo_report=str_replace("'","",$cbo_report_type);
    $cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
    $txt_sales_order_no=str_replace("'","",$txt_sales_order_no);
    $txt_file_no=str_replace("'","",$txt_file_no);
    $txt_ref_no=str_replace("'","",$txt_ref_no);
    $txt_order_no=str_replace("'","",$txt_order_no);
    $txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
    $txt_batch_no=str_replace("'","",$txt_batch_no);
    $date_from=str_replace("'","",$from_date);
    $date_to=str_replace("'","",$to_date);
    
    if($db_type==0)
    {
        if($date_from!="") $date_from=change_date_format($date_from,'yyyy-mm-dd');
        if($date_to!="") $date_to=change_date_format($date_to,'yyyy-mm-dd');
    }
    else
    {
        if($date_from!="") $date_from=change_date_format($date_from,'','',1);
        if($date_to!="") $date_to=change_date_format($date_to,'','',1);
    }
    //echo $date_from."==".$date_to;die;

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $buyer_name_arr = return_library_array( "select style_ref_no, buyer_name from wo_po_details_master",'style_ref_no','buyer_name');
    
    if ($cbo_report==1) // Knit Grey Fabric start
    {
        $job_order_cond="";    
        if($cbo_buyer_id>0) $job_order_cond.=" and e.buyer_id=$cbo_buyer_id";
        if($txt_style_ref_no!="") $job_order_cond.=" and  e.style_ref_no='$txt_style_ref_no'";
        if($txt_sales_order_no!="") $job_order_cond.=" and  e.job_no_prefix_num='$txt_sales_order_no'";

        //if($job_order_cond!='') oci_commit($con); else oci_rollback($con);
        ob_start(); 
        //echo "tofa";die;
        ?>
        <div>
        <table width="1680" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:22px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:16px">Ref. To Ref. Transfer Report</strong></td>
            </tr>         
        </table>
        <br />
        <table width="1680" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
                <tr>
                    <th width="35" rowspan="2">SL</th>
                    <th colspan="8"><strong>From Order</strong></th>
                    <th width="20"></th>
                    <th colspan="5"><strong>To Order</strong></th>
                </tr>
                <tr>
                    <th width="120">Buyer</th>
                    <th width="80">Sales Order No</th>
                    <th width="80">Style Ref</th>
                    <th width="100">Fabric Booking No</th>
                    <th width="120">Fabric Desc</th>
                    <th width="80">QTY</th>
                    <th width="120">Transfer ID</th>
                    <th width="80">Transfer Date</th>
                    <th width="20"></th>

                    <th width="120">Buyer</th>
                    <th width="80">Sales Order No</th>
                    <th width="80">Style Ref</th>
                    <th width="80">Fabric Booking No</th>
                    <th width="80">QTY</th>
                </tr>
            </thead>
        </table>
        <div style="width:1700px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table" id="tbl_issue_status" >
            <?
                
            $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
            sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, 
            sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty, d.item_description, e.buyer_id, e.job_no as sales_order_no, e.style_ref_no, e.sales_booking_no, e.within_group
            from inv_item_transfer_mst a, inv_transaction b, product_details_master d, fabric_sales_order_mst e
            where a.id=b.mst_id and b.prod_id=d.id and a.from_order_id=e.id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' $job_order_cond and a.entry_form=133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
            group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, d.item_description, e.buyer_id, e.job_no , e.style_ref_no, e.sales_booking_no, e.within_group";

            //echo $sql_transfer;//die;
            $sql_transfer_result=sql_select($sql_transfer);
            $to_order_arr=array();
            foreach($sql_transfer_result as $rows)
            {
                $to_order_arr[$rows[csf('to_order_id')]]=$rows[csf('to_order_id')];
            }
            if(!empty($to_order_arr))
            {
                $to_order_ids="'".implode("','",$to_order_arr)."'";
                $to_order_arr = explode(",", $to_order_ids);

                $all_to_order_cond=""; $toOrderCond="";
                if($db_type==2 && count($to_order_arr)>999)
                {
                    $all_to_order_arr_chunk=array_chunk($to_order_arr,999) ;
                    foreach($all_to_order_arr_chunk as $chunk_arr)
                    {
                        $chunk_arr_value=implode(",",$chunk_arr);
                        $toOrderCond.="  id in($chunk_arr_value) or ";
                    }

                    $all_to_order_cond.=" and (".chop($toOrderCond,'or ').")";
                }
                else
                {
                    $all_to_order_cond=" and id in($to_order_ids)";
                }
            }
            $to_order_info="SELECT id, buyer_id, job_no as sales_order_no, style_ref_no, sales_booking_no, within_group from fabric_sales_order_mst where status_active=1 and is_deleted=0 $all_to_order_cond";
            $to_order_data_arr=array();
            $order_info_result=sql_select($to_order_info);
            foreach ($order_info_result as $key => $value) 
            {
                if ($value[csf("within_group")]==1) 
                {
                    $to_order_data_arr[$value[csf("id")]]['buyer_id']=$company_library[$value[csf("buyer_id")]];
                }
                else
                {
                    $to_order_data_arr[$value[csf("id")]]['buyer_id']=$buyer_arr[$value[csf("buyer_id")]];
                }

                $to_order_data_arr[$value[csf("id")]]['sales_order_no']=$value[csf("sales_order_no")];
                $to_order_data_arr[$value[csf("id")]]['style_ref_no']=$value[csf("style_ref_no")];
                $to_order_data_arr[$value[csf("id")]]['sales_booking_no']=$value[csf("sales_booking_no")];
            }

            $i=1;            
            $sql_transfer_result=sql_select($sql_transfer);
            foreach($sql_transfer_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="35"><p><? echo $i; ?></p></td>
                    <td width="120"><? 
                    /*if ($row[csf("within_group")]==1) 
                    {
                       echo $company_library[$row[csf("buyer_id")]];
                    }
                    else
                    {
                        echo $buyer_arr[$row[csf("buyer_id")]]; 
                    }*/ 
                    echo $buyer_arr[$buyer_name_arr[$row[csf("style_ref_no")]]];
                    ?>
                    </td>
                    <td width="80" title="<? echo $row[csf("from_order_id")]; ?>"><? echo $row[csf("sales_order_no")]; ?></td>
                    <td width="80"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td width="100"><? echo $row[csf("sales_booking_no")]; ?></td>
                    <td width="120"><? echo $row[csf("item_description")]; ?></td>                    
                    <td width="80" align="right"><? echo number_format($row[csf("from_order_qnty")],2); ?></td>
                    <td width="120" align="center"><? echo $row[csf("transfer_system_id")]; ?></td>
                    <td width="80" align="center"><? echo $row[csf("transfer_date")]; ?></td>

                    <td width="20"><? //echo ; ?></td>

                    <td width="120"><? echo $buyer_arr[$buyer_name_arr[$to_order_data_arr[$row[csf("to_order_id")]]['style_ref_no']]];
                    //echo $to_order_data_arr[$row[csf("to_order_id")]]['buyer_id']; ?></td>
                    <td width="80" title="<? echo $row[csf("to_order_id")]; ?>"><? echo $to_order_data_arr[$row[csf("to_order_id")]]['sales_order_no']; ?></td>
                    <td width="80"><? echo $to_order_data_arr[$row[csf("to_order_id")]]['style_ref_no']; ?></td>
                    <td width="80"><? echo $to_order_data_arr[$row[csf("to_order_id")]]['sales_booking_no']; ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf("to_order_qnty")],2); ?></td>
                 </tr>
                <?
                $from_order_qnty+=$row[csf("from_order_qnty")];
                $to_order_qnty  +=$row[csf("to_order_qnty")];
                $i++;
            }
            ?>
            </table>
            <table width="1680 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                <tfoot>
                    <th width="35">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100" title="From Booking">&nbsp;</th>
                    <th width="120" title="Feb Desc">&nbsp; Total:</th>
                    <th width="80" align="right" id="tot_qnty"><? echo number_format($from_order_qnty,2); ?></th>
                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="20">&nbsp;</th>

                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp; Total:</th>
                    <th width="80" align="right" id="tot_qnty"><? echo number_format($to_order_qnty,2); ?></th>
                </tfoot>
            </table>
        </div>
        </div>
        <?
    } // Knit Grey Fabric end

    if ($cbo_report==2) // Knit Finish Fabric Textile start
    {
        $job_order_cond="";    
        if($cbo_buyer_id>0) $job_order_cond.=" and e.buyer_id=$cbo_buyer_id";
        if($txt_style_ref_no!="") $job_order_cond.=" and  e.style_ref_no='$txt_style_ref_no'";
        if($txt_sales_order_no!="") $job_order_cond.=" and  e.job_no_prefix_num='$txt_sales_order_no'";

        //if($job_order_cond!='') oci_commit($con); else oci_rollback($con);
        ob_start(); 
        //echo "tofa";die;
        ?>
        <div>
        <table width="1680" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:22px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:16px">Ref. To Ref. Transfer Report</strong></td>
            </tr>         
        </table>
        <br />
        <table width="1680" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
                <tr>
                    <th width="35" rowspan="2">SL</th>
                    <th colspan="8"><strong>From Order</strong></th>
                    <th width="20"></th>
                    <th colspan="5"><strong>To Order</strong></th>
                </tr>
                <tr>
                    <th width="120">Buyer</th>
                    <th width="80">Sales Order No</th>
                    <th width="80">Style Ref</th>
                    <th width="100">Fabric Booking No</th>
                    <th width="120">Fabric Desc</th>
                    <th width="80">QTY</th>
                    <th width="120">Transfer ID</th>
                    <th width="80">Transfer Date</th>
                    <th width="20"></th>

                    <th width="120">Buyer</th>
                    <th width="80">Sales Order No</th>
                    <th width="80">Style Ref</th>
                    <th width="80">Fabric Booking No</th>
                    <th width="80">QTY</th>
                </tr>
            </thead>
        </table>
        <div style="width:1700px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table" id="tbl_issue_status" >
            <?
                
            $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
            sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, 
            sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty, d.item_description, e.buyer_id, e.job_no as sales_order_no, e.style_ref_no, e.sales_booking_no, e.within_group
            from inv_item_transfer_mst a, inv_transaction b, product_details_master d, fabric_sales_order_mst e
            where a.id=b.mst_id and b.prod_id=d.id and a.from_order_id=e.id and a.transfer_criteria=4 and a.item_category=2 and b.item_category=2 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' $job_order_cond and a.entry_form=230 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
            group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, d.item_description, e.buyer_id, e.job_no , e.style_ref_no, e.sales_booking_no, e.within_group";

            //echo $sql_transfer;//die;
            $sql_transfer_result=sql_select($sql_transfer);
            $to_order_arr=array();
            foreach($sql_transfer_result as $rows)
            {
                $to_order_arr[$rows[csf('to_order_id')]]=$rows[csf('to_order_id')];
            }
            if(!empty($to_order_arr))
            {
                $to_order_ids="'".implode("','",$to_order_arr)."'";
                $to_order_arr = explode(",", $to_order_ids);

                $all_to_order_cond=""; $toOrderCond="";
                if($db_type==2 && count($to_order_arr)>999)
                {
                    $all_to_order_arr_chunk=array_chunk($to_order_arr,999) ;
                    foreach($all_to_order_arr_chunk as $chunk_arr)
                    {
                        $chunk_arr_value=implode(",",$chunk_arr);
                        $toOrderCond.="  id in($chunk_arr_value) or ";
                    }

                    $all_to_order_cond.=" and (".chop($toOrderCond,'or ').")";
                }
                else
                {
                    $all_to_order_cond=" and id in($to_order_ids)";
                }
            }
            $to_order_info="SELECT id, buyer_id, job_no as sales_order_no, style_ref_no, sales_booking_no, within_group from fabric_sales_order_mst where status_active=1 and is_deleted=0 $all_to_order_cond";
            $to_order_data_arr=array();
            $order_info_result=sql_select($to_order_info);
            foreach ($order_info_result as $key => $value) 
            {
                if ($value[csf("within_group")]==1) 
                {
                    $to_order_data_arr[$value[csf("id")]]['buyer_id']=$company_library[$value[csf("buyer_id")]];
                }
                else
                {
                    $to_order_data_arr[$value[csf("id")]]['buyer_id']=$buyer_arr[$value[csf("buyer_id")]];
                }

                $to_order_data_arr[$value[csf("id")]]['sales_order_no']=$value[csf("sales_order_no")];
                $to_order_data_arr[$value[csf("id")]]['style_ref_no']=$value[csf("style_ref_no")];
                $to_order_data_arr[$value[csf("id")]]['sales_booking_no']=$value[csf("sales_booking_no")];
            }

            $i=1;            
            $sql_transfer_result=sql_select($sql_transfer);
            foreach($sql_transfer_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="35"><p><? echo $i; ?></p></td>
                    <td width="120"><? 
                    /*if ($row[csf("within_group")]==1) 
                    {
                       echo $company_library[$row[csf("buyer_id")]];
                    }
                    else
                    {
                        echo $buyer_arr[$row[csf("buyer_id")]]; 
                    }*/
                    echo $buyer_arr[$buyer_name_arr[$row[csf("style_ref_no")]]];
                    ?>
                    </td>
                    <td width="80" title="<? echo $row[csf("from_order_id")]; ?>"><? echo $row[csf("sales_order_no")]; ?></td>
                    <td width="80"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td width="100"><? echo $row[csf("sales_booking_no")]; ?></td>
                    <td width="120"><? echo $row[csf("item_description")]; ?></td>                    
                    <td width="80" align="right"><? echo number_format($row[csf("from_order_qnty")],2); ?></td>
                    <td width="120" align="center"><? echo $row[csf("transfer_system_id")]; ?></td>
                    <td width="80" align="center"><? echo $row[csf("transfer_date")]; ?></td>

                    <td width="20"><? //echo ; ?></td>

                    <td width="120"><? echo $buyer_arr[$buyer_name_arr[$to_order_data_arr[$row[csf("to_order_id")]]['style_ref_no']]]; //$to_order_data_arr[$row[csf("to_order_id")]]['buyer_id']; ?></td>
                    <td width="80" title="<? echo $row[csf("to_order_id")]; ?>"><? echo $to_order_data_arr[$row[csf("to_order_id")]]['sales_order_no']; ?></td>
                    <td width="80"><? echo $to_order_data_arr[$row[csf("to_order_id")]]['style_ref_no']; ?></td>
                    <td width="80"><? echo $to_order_data_arr[$row[csf("to_order_id")]]['sales_booking_no']; ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf("to_order_qnty")],2); ?></td>
                 </tr>
                <?
                $from_order_qnty+=$row[csf("from_order_qnty")];
                $to_order_qnty  +=$row[csf("to_order_qnty")];
                $i++;
            }
            ?>
            </table>
            <table width="1680 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                <tfoot>
                    <th width="35">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100" title="From Booking">&nbsp;</th>
                    <th width="120" title="Feb Desc">&nbsp; Total:</th>
                    <th width="80" align="right" id="tot_qnty"><? echo number_format($from_order_qnty,2); ?></th>
                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="20">&nbsp;</th>

                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp; Total:</th>
                    <th width="80" align="right" id="tot_qnty"><? echo number_format($to_order_qnty,2); ?></th>
                </tfoot>
            </table>
        </div>
        </div>
        <?
    } // Knit Finish Fabric Textile end

    if ($cbo_report==3) // Knit Finish Fabric Garments start
    {
        $job_order_cond="";    
        if($cbo_buyer_id>0) $job_order_cond.=" and a.buyer_name=$cbo_buyer_id";
        if($txt_style_ref_no!="") $job_order_cond.=" and  a.style_ref_no='$txt_style_ref_no'";
        if($txt_order_no!="") $job_order_cond.=" and  b.po_number='$txt_order_no'";
        if($txt_file_no!="") $job_order_cond.=" and  b.file_no='$txt_file_no'";
        if($txt_ref_no!="") $job_order_cond.=" and b.grouping='$txt_ref_no'";
       
        $job_ord_sql="select a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.remarks, b.id as po_id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_order_cond";
        // echo $job_order_cond;die;
        $con=connect();
        execute_query("delete from GBL_TEMP_REPORT_ID where user_id=$user_id",1);
        oci_commit($con);
        
        $job_ord_result=sql_select($job_ord_sql);
        $all_order_id=""; $job_order_data=array();    
        foreach($job_ord_result as $row)
        {
            //$all_order_id.=$row[csf("po_id")].",";
            $job_order_data[$row[csf("po_id")]]["job_id"]=$row[csf("job_id")];
            $job_order_data[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
            $job_order_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
            $job_order_data[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
            $job_order_data[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
            $job_order_data[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
            $job_order_data[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
            $job_order_data[$row[csf("po_id")]]["file_no"]=$row[csf("file_no")];
            $job_order_data[$row[csf("po_id")]]["ref_no"]=$row[csf("ref_no")];
            $job_order_data[$row[csf("po_id")]]["remarks"]=$row[csf("remarks")];
            if($job_order_cond!='') execute_query("insert into GBL_TEMP_REPORT_ID(ref_val,user_id) values(".$row[csf("po_id")].",".$user_id.")",0);
        }  
        //$all_order_id=chop($all_order_id,",");
        //echo $all_order_id;
        oci_commit($con);
        disconnect($con);
    
    	//if($job_order_cond!='') oci_commit($con); else oci_rollback($con);
        ob_start(); 
        //echo "tofa";die;
        ?>
        <div>
        <table width="1920" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:22px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:16px">Ref. To Ref. Transfer Report</strong></td>
            </tr>         
        </table>
        <br />
        <table width="1920" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
                <tr>
                    <th width="35" rowspan="2">SL</th>
                    <th colspan="11"><strong>From Order</strong></th>
                    <th width="20"></th>
                    <th colspan="5"><strong>To Order</strong></th>
                </tr>
                <tr>
                    <th width="120">Buyer</th>
                    <th width="80">Order No</th>
                    <th width="80">Style Ref</th>
                    <th width="100">Fabric Booking No</th>
                    <th width="120">Fabric Desc.</th>
                    <th width="80">Body Part</th>
                    <th width="80">Color</th>
                    <th width="80">Batch No</th>
                    <th width="80">QTY</th>
                    <th width="120">Transfer ID</th>
                    <th width="80">Transfer Date</th>
                    <th width="20"></th>

                    <th width="120">Buyer</th>
                    <th width="80">Order No</th>
                    <th width="80">Style Ref</th>
                    <th width="80">Body Part</th>
                    <th width="80">QTY</th>
                </tr>
            </thead>
        </table>
        <div style="width:1940px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1920" class="rpt_table" id="tbl_issue_status" >
            <?

    		if($job_order_cond=="") 
            {
    			$sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
                sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty, d.color, d.item_description, e.batch_id
                from inv_item_transfer_mst a, inv_transaction b, product_details_master d , inv_item_transfer_dtls e 
                where a.id=b.mst_id and b.prod_id=d.id and a.id=e.mst_id and b.mst_id=e.mst_id and a.transfer_criteria=4 and a.item_category=2 and b.item_category=2 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' and a.entry_form=14
                group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, d.color, d.item_description, e.batch_id";
            }
            else //, b.body_part_id
            {
                $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
                sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty, d.color, d.item_description , e.batch_id
                from inv_item_transfer_mst a, inv_transaction b, gbl_temp_report_id c, product_details_master d, inv_item_transfer_dtls e
                where a.id=b.mst_id and b.prod_id=d.id and a.id=e.mst_id and b.mst_id=e.mst_id and a.transfer_criteria=4 and a.item_category=2 and b.item_category=2 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' and a.entry_form=14 and ( a.from_order_id=c.ref_val or a.to_order_id=c.ref_val )
                group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, d.color, d.item_description, e.batch_id";
            }
            // echo $sql_transfer;die;

            $batch_no_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no"  );
            $booking_no_arr=return_library_array( "select id, booking_no from pro_batch_create_mst", "id", "booking_no"  );
            $color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

            $sql_transfer_result=sql_select($sql_transfer);
            $to_order_arr=array(); $from_order_arr=array();
            foreach($sql_transfer_result as $rows)
            {
                $from_order_arr[$rows[csf('from_order_id')]]=$rows[csf('from_order_id')];
                $to_order_arr[$rows[csf('to_order_id')]]=$rows[csf('to_order_id')];
            }
            if(!empty($from_order_arr))
            {
                $from_order_ids="'".implode("','",$from_order_arr)."'";
                $from_order_arr = explode(",", $from_order_ids);

                $all_from_order_cond=""; $fromOrderCond="";
                if($db_type==2 && count($from_order_arr)>999)
                {
                    $all_from_order_arr_chunk=array_chunk($from_order_arr,999) ;
                    foreach($all_from_order_arr_chunk as $chunk_arr)
                    {
                        $chunk_arr_value=implode(",",$chunk_arr);
                        $fromOrderCond.="  a.from_order_id in($chunk_arr_value) or ";
                    }

                    $all_from_order_cond.=" and (".chop($fromOrderCond,'or ').")";
                }
                else
                {
                    $all_from_order_cond=" and a.from_order_id in($from_order_ids)";
                }
            }
            if(!empty($to_order_arr))
            {
                $to_order_ids="'".implode("','",$to_order_arr)."'";
                $to_order_arr = explode(",", $to_order_ids);

                $all_to_order_cond=""; $toOrderCond="";
                if($db_type==2 && count($to_order_arr)>999)
                {
                    $all_to_order_arr_chunk=array_chunk($to_order_arr,999) ;
                    foreach($all_to_order_arr_chunk as $chunk_arr)
                    {
                        $chunk_arr_value=implode(",",$chunk_arr);
                        $toOrderCond.="  a.to_order_id in($chunk_arr_value) or ";
                    }

                    $all_to_order_cond.=" and (".chop($toOrderCond,'or ').")";
                }
                else
                {
                    $all_to_order_cond=" and a.to_order_id in($to_order_ids)";
                }
            }

            $to_body_part_sql="SELECT a.from_order_id,a.to_order_id, b.body_part_id, b.transaction_type
            from inv_item_transfer_mst a, inv_transaction b 
            where a.id=b.mst_id and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' and a.transfer_criteria=4 and a.item_category=2 and b.item_category=2 $all_from_order_cond $all_to_order_cond group by a.from_order_id,a.to_order_id, b.body_part_id, b.transaction_type"; // and b.transaction_type=5
            $to_body_part_data=sql_select($to_body_part_sql);
            $to_body_part_data_arr=array();
            $from_body_part_data_arr=array();
            foreach ($to_body_part_data as $key => $value) 
            {
                if ($value[csf('transaction_type')]==6) 
                {
                    $from_body_part_data_arr[$value[csf('from_order_id')]]['from_body_part']=$value[csf('body_part_id')];
                }
                else{
                    $to_body_part_data_arr[$value[csf('to_order_id')]]['to_body_part']=$value[csf('body_part_id')];
                }
            }
            //echo "<pre>";print_r($to_body_part_data_arr);
            $i=1;
            
            foreach($sql_transfer_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="35"><p><? echo $i; ?></p></td>
                    <td width="120"><? echo $buyer_arr[$job_order_data[$row[csf("from_order_id")]]["buyer_name"]]; ?></td>
                    <td width="80" title="<? echo $row[csf("from_order_id")]; ?>"><? echo $job_order_data[$row[csf("from_order_id")]]["po_number"]; ?></td>
                    <td width="80"><? echo $job_order_data[$row[csf("from_order_id")]]["style_ref_no"]; ?></td>
                    <td width="100"><? echo $booking_no_arr[$row[csf("batch_id")]];; ?></td>
                    <td width="120"><? echo $row[csf("item_description")]; ?></td>
                    <td width="80" title="body part"><? echo $body_part[$from_body_part_data_arr[$row[csf("from_order_id")]]["from_body_part"]]; ?></td>
                    <td width="80" title="Color"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
                    <td width="80" title="Batch"><? echo $batch_no_arr[$row[csf("batch_id")]]; ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf("from_order_qnty")],2); ?></td>
                    <td width="120" align="center"><? echo $row[csf("transfer_system_id")]; ?></td>
                    <td width="80" align="center"><? echo $row[csf("transfer_date")]; ?></td>

                    <td width="20"><? //echo ; ?></td>

                    <td width="120"><? echo  $buyer_arr[$job_order_data[$row[csf("to_order_id")]]["buyer_name"]]; ?></td>
                    <td width="80" title="<? echo $row[csf("to_order_id")]; ?>"><? echo $job_order_data[$row[csf("to_order_id")]]["po_number"]; ?></td>
                    <td width="80"><? echo $job_order_data[$row[csf("to_order_id")]]["style_ref_no"]; ?></td>
                    <td width="80"><? echo $body_part[$to_body_part_data_arr[$row[csf("to_order_id")]]["to_body_part"]]; ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf("to_order_qnty")],2); ?></td>
                 </tr>
                <?
                $from_order_qnty+=$row[csf("from_order_qnty")];
                $to_order_qnty  +=$row[csf("to_order_qnty")];
                $i++;
            }
            ?>
            </table>
            <table width="1920 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                <tfoot>
                    <th width="35">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100" title="From Booking">&nbsp;</th>
                    <th width="120" title="Feb Desc">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80" title="Color">&nbsp;</th>
                    <th width="80">&nbsp; Total:</th>
                    <th width="80" align="right" id="tot_qnty"><? echo number_format($from_order_qnty,2); ?></th>
                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="20">&nbsp;</th>

                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp; Total:</th>
                    <th width="80" align="right" id="tot_qnty"><? echo number_format($to_order_qnty,2); ?></th>
                </tfoot>
            </table>
        </div>
        </div>
        <?
    } // Knit Finish Fabric Garments end
    ?>
    <?
     //echo "<br />Execution Time: " . (microtime(true) - $started).'S';
     foreach (glob("$user_id*.xls") as $filename)
     {
         if (@filemtime($filename) < (time() - $seconds_old))
         @unlink($filename);
     }
     //---------end------------//
     $html =ob_get_contents();
     ob_clean();
     $total_data=$html;
     $html = strip_tags($html, '<table><thead><tbody><tfoot><tr><td><th>');
     $name = time();
     $filename = $user_id . "_" . $name . ".xls";
     $create_new_doc = fopen($filename, 'w');
     $is_created = fwrite($create_new_doc, $html);
     $filename = "requires/" . $user_id . "_" . $name . ".xls";
     echo "$total_data####$filename";
     exit();

    // foreach (glob("$user_id*.xls") as $filename) 
    // {
    //     if( @filemtime($filename) < (time()-$seconds_old) )
    //     @unlink($filename);
    // }
    //---------end------------//
    // $name=time();
    // $filename=$user_id."_".$name.".xls";
    // $create_new_doc = fopen($filename, 'w');
    // $is_created = fwrite($create_new_doc,ob_get_contents());
    // $filename=$user_id."_".$name.".xls";
    // echo "$total_data####$filename";
    // exit();      
    
}
?>