<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  ); 
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_id", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     
    exit();
}

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "load_drop_down( 'requires/sub_con_date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
    exit();          
}

if ($action=="load_drop_down_floor")
{
    echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor--", $selected, "",0 );  
    exit();      
}


if($action=="order_no_popup")
{
    echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script type="text/javascript">
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
    <?
    $buyer = str_replace("'","",$cbo_buyer_name);
    $location = str_replace("'","",$cbo_location_id);
    $cbo_floor = str_replace("'","",$cbo_floor_id);

    if($db_type==0) 
    {
        $year_field="year(a.insert_date) as year"; 
    }
    else if($db_type==2) 
    {
        $year_field="to_char(a.insert_date,'YYYY') as year";
    }
    else 
    {
        $year_field="";
    }

    if(trim($location)==0) $sub_location_name_cond=""; else $sub_location_name_cond=" and a.location_id=$location";
    if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
    
    $sql="select distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $sub_location_name_cond and a.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
            <? 
            $rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
                </tr>
                <? $i++; 
            } ?>
        </table>
    </div>
    <script> setFilterGrid("table_body2",-1); </script>
    <?
    exit();
}



if($action=="report_generate")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
       

    $txt_order_no=str_replace("'","",$txt_order_no);
    if ($txt_order_no!='') $order_no_cond=" and c.order_no like '%$txt_order_no%'"; else $order_no_cond="";

    $type = str_replace("'","",$cbo_type);
    if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";
    if(str_replace("'","",$cbo_buyer_id)==0)$buyer_name="";else $buyer_name=" and a.party_id=$cbo_buyer_id";
    
    if(str_replace("'","",$cbo_location_id)==0)$location="";else $location=" and a.location_id  =$cbo_location_id";
    if(str_replace("'","",$cbo_floor_id)==0)$floor="";else $floor=" and b.floor_id=$cbo_floor_id";
    
    if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
    else $txt_date=" and b.production_date between $txt_date_from and $txt_date_to";
    
    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
        
        
    if($type==2 || $type==4) //------------------------------------Show Date Location Floor & Line Wise $type==2
    {
        $groupByCond = "group by a.id,c.location,c.floor_id order by a.id,c.location,c.floor_id";
    }
    else //--------------------------------------------Show Order Wise  $type==1
    {
        $groupByCond = "group by a.id order by a.pub_shipment_date,a.id";
    }
    
    // ==================================== MAIN QUERY ====================================
    $sql = "SELECT a.party_id,a.job_no_prefix_num,c.order_no,b.gmts_item_id, c.id, c.cust_style_ref,c.order_id,c.delivery_date, b.remarks
    FROM subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
    WHERE    c.job_no_mst = a.subcon_job AND c.id = b.order_id AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0
        AND b.status_active = 1 AND c.is_deleted=0 
        $company_name $buyer_name $location $floor $txt_date $order_no_cond";

    $sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
            <div style="text-align: center;color: red;font-weight: bold;font-size: 18px;">Report Data Not Found.</div>
        <?
        die();
    }
    $main_data_array    = array();
    foreach ($sql_res as $key => $row) 
    {
        $order_id_array[$row[csf('id')]] = $row[csf('id')];
        $main_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['order_no']            = $row[csf('order_no')];
        $main_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['party_id']            = $row[csf('party_id')];
        $main_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['job_no']              = $row[csf('job_no_prefix_num')];
        $main_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['cust_style_ref']      = $row[csf('cust_style_ref')];
        $main_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['delivery_date']       = $row[csf('delivery_date')];
        $main_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['remarks']             = $row[csf('remarks')];
    }

    $order_id = implode(",", $order_id_array);
    // ==================================== PRODUCTION DATA QUERY ====================================
    $sql_prod = "SELECT a.party_id,b.gmts_item_id, c.id,
        SUM (CASE WHEN production_type = '1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
        SUM (CASE WHEN production_type = '7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,
        SUM (CASE WHEN production_type = '2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
        SUM (CASE WHEN production_type = '3' THEN production_qnty ELSE 0 END) AS iron_qnty,
        SUM (CASE WHEN production_type = '4' THEN production_qnty ELSE 0 END) AS finish_qnty,
        SUM (CASE WHEN production_type = '5' THEN production_qnty ELSE 0 END) AS ploy_qnty,
        SUM (CASE WHEN production_type in(1,2,3,4,5,7) THEN reject_qnty ELSE 0 END) AS reject_qnty
    FROM subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
    WHERE    c.job_no_mst = a.subcon_job AND c.id = b.order_id AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0
        AND b.status_active = 1 and c.is_deleted=0 ".where_con_using_array($order_id_array,0,'b.order_id')." $txt_date
    GROUP BY a.party_id,b.gmts_item_id, c.id";

    $sql_prod_res = sql_select($sql_prod);
    $party_data_array   = array();
    $prod_data_array    = array();
    foreach ($sql_prod_res as $key => $row) 
    {                
        // for party data
        $party_data_array[$row[csf('party_id')]]['cutting_qnty']        += $row[csf('cutting_qnty')];
        $party_data_array[$row[csf('party_id')]]['sewing_input_qnty']   += $row[csf('sewing_input_qnty')];
        $party_data_array[$row[csf('party_id')]]['sewingout_qnty']      += $row[csf('sewingout_qnty')];
        $party_data_array[$row[csf('party_id')]]['iron_qnty']           += $row[csf('iron_qnty')];
        $party_data_array[$row[csf('party_id')]]['finish_qnty']         += $row[csf('finish_qnty')];
		$party_data_array[$row[csf('party_id')]]['ploy_qnty']           += $row[csf('ploy_qnty')];

        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['cutting_qnty']        += $row[csf('cutting_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['sewing_input_qnty']   += $row[csf('sewing_input_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['sewingout_qnty']      += $row[csf('sewingout_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['iron_qnty']           += $row[csf('iron_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['ploy_qnty']           += $row[csf('ploy_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['finish_qnty']         += $row[csf('finish_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['reject_qnty']         += $row[csf('reject_qnty')];
    }

    // ======================================== FOR ORDER QNTY =================================
    $order_sql = "SELECT a.party_id, sum(d.qnty) as po_quantity, sum(d.amount) as po_total_price,c.id,d.item_id 
    from subcon_ord_mst a, subcon_ord_dtls c, subcon_ord_breakdown d 
    where c.job_no_mst=a.subcon_job and a.id=d.mst_id and c.id=d.order_id and a.status_active=1 and c.is_deleted=0 $company_name $buyer_name $location $floor  ".where_con_using_array($order_id_array,0,'d.order_id')."
    group by a.party_id,c.id,d.item_id 
    order by a.party_id ASC";
    $order_sql_res = sql_select($order_sql);
    $party_order_data   = array();
    $main_order_data    = array();
    foreach ($order_sql_res as $key => $val) 
    {
        $party_order_data[$val[csf('party_id')]]['po_quantity']     += $val[csf('po_quantity')];
        $party_order_data[$val[csf('party_id')]]['po_total_price']  += $val[csf('po_total_price')];
        // for details
        $main_order_data[$val[csf('id')]][$val[csf('item_id')]]['po_quantity']      += $val[csf('po_quantity')];
        $main_order_data[$val[csf('id')]][$val[csf('item_id')]]['po_total_price']   += $val[csf('po_total_price')];
    }

    // ======================================== FOR EXFACTORY ===================================

    $exfactory_sql="SELECT a.party_id, sum(CASE WHEN d.order_id=c.id THEN d.delivery_qty ELSE 0 END) as delivery_quantity 
    from subcon_ord_mst a, subcon_ord_dtls c, subcon_delivery_dtls d 
    where c.id=d.order_id and a.subcon_job=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_name $buyer_name $location $floor ".where_con_using_array($order_id_array,0,'c.id')."
    group by a.party_id";
    
    $exfactory_sql_result=sql_select($exfactory_sql);
    $exfactory_arr=array(); 
    foreach($exfactory_sql_result as $resRow)
    {
       // $exfactory_arr[$resRow[csf("party_id")]] = $resRow[csf("delivery_quantity")];
    }

    // ========================================== FOR EXFACT DEL. DATE ===================================
    // $sqlEx = "SELECT  b.order_id,b.item_id,MAX(a.delivery_date) AS ex_fac_date, sum(b.delivery_qty) AS ex_fac_qnty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id ".where_con_using_array($order_id_array,0,'b.order_id')." group by  b.order_id,b.item_id";
	 //  $sqlEx = "SELECT  b.order_id,b.item_id,MAX(a.delivery_date) AS ex_fac_date, sum(b.delivery_qty) AS ex_fac_qnty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id ".where_con_using_array($order_id_array,0,'b.order_id')." group by  b.order_id,b.item_id";
	   $sqlEx=" SELECT a.party_id,b.item_id,b.order_id, sum(c.delivery_qty) as delivery_qty,MAX(a.delivery_date) AS ex_fac_date from subcon_delivery_mst a, subcon_delivery_dtls b,
 subcon_gmts_delivery_dtls c,subcon_ord_breakdown d where a.id = b.mst_id and b.id = c.dtls_mst_id and a.id = c.mst_id 
 and d.id = c.breakdown_color_size_id 
 and b.order_id = b.order_id  ".where_con_using_array($order_id_array,0,'b.order_id')." and a.status_active=1 and b.status_active=1 group by a.party_id,b.order_id, b.item_id ";
    $sqlEx_res = sql_select($sqlEx);
    $sqlEx_date_data = array();
    foreach ($sqlEx_res as $key => $val) 
    {
        $sqlEx_date_data[$val[csf('order_id')]][$val[csf('item_id')]]['ex_fac_date'] = $val[csf('ex_fac_date')];
        $sqlEx_date_data[$val[csf('order_id')]][$val[csf('item_id')]]['ex_fac_qnty'] = $val[csf('delivery_qty')];
		$exfactory_arr[$val[csf("party_id")]] += $val[csf("delivery_qty")];
    }

    // ======================================== FOR BILL =====================================================
    $bill_sql = "SELECT a.bill_no,a.party_id,a.process_id,b.order_id,b.item_id,b.amount 
    from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b
    where a.id=b.mst_id ".where_con_using_array($order_id_array,0,'b.order_id');

    $bill_sql_res       = sql_select($bill_sql);
    $bill_array         = array();
    $party_bill_array   = array();
    $bill_no_array      = array();

    foreach ($bill_sql_res as $val) 
    {
        $bill_array[$val[csf('order_id')]][$val[csf('item_id')]]    += $val[csf('amount')];
        $party_bill_array[$val[csf('party_id')]]                    += $val[csf('amount')];
        $bill_no_array[$val[csf('bill_no')]]                         = $val[csf('bill_no')];
    }
    $bill_no = implode(",", $bill_no_array);
    // =========================================== FOR RECEIVE AMOUNT ==============================================
    $rcv_amnt_sql = "SELECT d.order_id,d.item_id,a.party_name,b.total_adjusted
    FROM subcon_payment_receive_mst a,
    subcon_payment_receive_dtls b,
    subcon_inbound_bill_mst c,
    subcon_inbound_bill_dtls d
     WHERE a.id = b.master_id and b.bill_no = c.BILL_NO and c.id=d.mst_id AND d.order_id  in($order_id) and a.status_active=1 and b.status_active=1";
    $rcv_amnt_sql_res = sql_select($rcv_amnt_sql);
    $party_rcv_array = array();
    $rcv_array = array();
    foreach ($rcv_amnt_sql_res as  $val) 
    {
        $rcv_array[$val[csf('order_id')]][$val[csf('item_id')]] += $val[csf('total_adjusted')];
        $party_rcv_array[$val[csf('party_name')]] += $val[csf('total_adjusted')];
    }

    $rcv_qty_data=sql_select("SELECT a.subcon_date, b.order_id, b.item_category_id, b.material_description, b.quantity  from sub_material_mst a join sub_material_dtls b on a.id=b.mst_id where b.status_active=2 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($order_id_array,0,'b.order_id'));
    //echo $rcv_qty_data; die;

    $rcv_qty_array=array();
    foreach ($rcv_qty_data as $row) {
       $rcv_qty_array[$row[csf('order_id')]]['qty'] += $row[csf('quantity')];
    }

    $issue_qty_data=sql_select("SELECT a.id, a.order_id, a.mst_id, a.item_category_id, a.material_description, a.quantity, a.subcon_uom, a.subcon_roll, a.grey_dia, a.status_active, b.order_no from sub_material_dtls a,subcon_ord_dtls b where a.order_id=b.id and a.status_active=1 ".where_con_using_array($order_id_array,0,'a.order_id'));

    $issue_qty_array=array();
    foreach ($issue_qty_data as $row) {
       $issue_qty_array[$row[csf('order_id')]]['qty'] += $row[csf('quantity')]; 
    }
    // echo '<pre>';
    // print_r($rcv_qty_array); die;
    
    ob_start();
    ?>
    <div style="width:2250px"> 
        <table width="1000"  cellspacing="0" cellpadding="0" rules="all" align="center" border="0">
            <tr class="form_caption" style="border:none;">
                <td colspan="13" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? 
                        if($type==1) echo "SubCon Date Wise Production Report";
                        else if($type==2) echo "Order Location & Floor Wise Production Report";
                        else if($type==3) echo "Order Country Wise Production Report";
                        else echo "Order Country Location & Floor Wise Production Report";
                    ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="13" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="13" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "From $fromDate To $toDate" ;?>
                </td>
            </tr>
        </table>
        <br clear="all">
        
        <div style="float:left; width:750px">
            <table width="1130" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th width="40">Sl.</th>    
                        <th width="110">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Input</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron</th>
                        <th width="80">Total Finishing</th>
                        <th width="80">Delivery Quantity</th>      
                        <th width="80">Delivery %</th>
                        <th width="80">Total Bill Amount</th>
                        <th>Payment Rcvd</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:425px; width:1150px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1130" rules="all" id="" >
                    <?
                        $total_po_quantity  =0;
                        $total_po_value     =0;
                        $total_cutt         =0;
                        $total_sew_in       =0;
                        $total_sew_out      =0;
                        $total_iron         =0;
                        $total_finish       =0;
                        $total_ex_factory   =0;
                        $total_bill_amnt    =0;
                        $total_rcv_amount   =0;
                        $i=1;
                        // ==================================== SUMMARY LOOP START 
                        foreach($party_data_array as $party_id=>$pval)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="40"><? echo $i;?></td>
                                <td width="110"><? echo $buyer_short_library[$party_id]; ?></td>
                                <td width="80" align="right"><? echo number_format($party_order_data[$party_id]["po_quantity"]);?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($party_order_data[$party_id]["po_total_price"],2);?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["cutting_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["sewing_input_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["sewingout_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["iron_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["finish_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($exfactory_arr[$party_id]); ?>&nbsp;</td>
                                <? $ex_gd_status = ($exfactory_arr[$party_id]/$party_order_data[$party_id]["po_quantity"])*100; ?>
                                <td width="80" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?>&nbsp;</td>
                                <td width="80" align="right">
                                    <? 
                                        $bill_amt = $party_bill_array[$party_id]; echo number_format($bill_amt,0);
                                    ?>                                        
                                    </td>
                                <td width="" align="right">
                                    <? 
                                        $rcv_amt = $party_rcv_array[$party_id]; echo number_format($rcv_amt,0);
                                    ?>
                                </td>
                            </tr>   
                            <?      
                                $total_po_quantity  +=  $party_order_data[$party_id]["po_quantity"];
                                $total_po_value     +=  $party_order_data[$party_id]["po_total_price"];
                                $total_cutt         +=  $pval["cutting_qnty"];
                                $total_sew_in       +=  $pval["sewing_input_qnty"];
                                $total_sew_out      +=  $pval["sewingout_qnty"];
                                $total_iron         +=  $pval["iron_qnty"];
                                $total_finish       +=  $pval["finish_qnty"];
                                $total_ex_factory   +=  $exfactory_arr[$party_id]; 
                                $total_bill_amnt    +=  $bill_amt;
                                $total_rcv_amount   +=  $rcv_amt;
                          
                            $i++;
                        }
                        ?>
                    </table>
                    <table border="1" class="tbl_bottom"  width="1130" rules="all" id="" >
                        <tr> 
                            <td width="40">&nbsp;</td> 
                            <td width="110" align="right">Total</td> 
                            <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?>&nbsp;</td> 
                            <td width="80" id="tot_po_value"><? echo number_format($total_po_value); ?>&nbsp;</td> 
                            <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?>&nbsp;</td>
                            <td width="80" id="tot_cutting"><? echo number_format($total_sew_in); ?>&nbsp;</td>
                            <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?>&nbsp;</td>   
                            <td width="80" id="tot_sew_out"><? echo number_format($total_iron); ?>&nbsp;</td>   
                            <td width="80" id="tot_sew_out"><? echo number_format($total_finish); ?>&nbsp;</td>   
                            <td width="80"><? echo number_format($total_ex_factory); ?>&nbsp;</td >
                            <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                            <td width="80"><? echo number_format($total_ex_status,2); ?>&nbsp;</td>
                            <td width="80" align="right"><? echo number_format($total_bill_amnt,0); ?></td>
                            <td align="right"><? echo number_format($total_rcv_amount,0); ?></td>
                        </tr>
                    </table>
                 </div>
                 </div>
                <div style="clear:both"></div>
                <br clear="all">
                <!-- ======================================= Details Part ================================= -->
                <div>                   

                    <table width="2470" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            <tr>
                                <th width="40">SL</th>    
                                <th width="130">Order Number</th>
                                <th width="100">Buyer Name</th>
                                <th width="100">Job Number</th>
                                <th width="100">Style Name</th>
                                <th width="150">Item Name</th>
                                <th width="80">Order Qty.</th>
                                <th width="150">Order Value</th>
                                <th width="80">Plan Delivery Date</th>
                                <th width="80">Delivery Date</th>
                                <th width="80">Delay By</th>
                                <th width="80">Early By</th>
                                <th width="80">Material receive</th>
                                <th width="80">Material Issue</th>
                                <th width="80">Total Cut Qty</th>
                                <th width="80">Actual Exc. Cut %</th>
                                <th width="80">Total Inut</th>
                                <th width="80">Total Sew Qty</th>
                                <th width="80">Total Iron</th>
                                <th width="80">Total Poly Qty</th>
                                <th width="80">Total Finishing</th>
                                <th width="80">Total Delivery</th>
                                <th width="80">Total Reject</th>
                                <th width="80">Shortage/ Excess</th>
                                <th width="80">Total Bill</th>
                                <th width="80">Payment Rcvd</th>
                                <th width="80">Status</th>
                                <th width="">Remarks</th>
                            </tr>
                        </thead>
                    </table>


                    <div style="max-height:425px; width:2410px" id="scroll_body">
                        <table border="1" class="rpt_table"  width="2470" rules="all" id="table_body" >
                            <?
                           $i=0; $k=0;
                           // initialize 
                           $total_ord_quantity      = 0;        
                           $total_order_values      = 0;        
                           $total_cutt              = 0;        
                           $total_sew_in            = 0;        
                           $total_sew               = 0;        
                           $total_iron              = 0;  
						   $total_ploy_qnty         = 0;      
                           $total_finish            = 0;        
                           $total_reject            = 0;        
                           $total_out_out           = 0;        
                           $total_shortage          = 0;    
                           $total_bill_amnt         = 0;    
                           $total_rcv_amount        = 0;    
                           // ======================= MAIN LOOP START =====================================    
                           foreach($main_data_array as $order_id=>$orderData)
                           {
                                foreach($orderData as $item_id=>$orderRes)
                                {
                                   $i++;
                                   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";?>                                   
                                    
                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>" style="height:20px">
                                        
                                        <td width="40" ><? echo $i; ?></td>
                                        <td width="130" title="<?= $order_id.'-'.$item_id ?>"><p><? echo $orderRes["order_no"]; ?></p></td>
                                        <td width="100"><? echo $buyer_short_library[$orderRes["party_id"]]; ?></td>
                                        <td width="100" align="center"><p><? echo $orderRes["job_no"];?></p></td>
                                        <td width="100"><p><? echo $orderRes["cust_style_ref"]; ?></p></td>
                                        <td width="150"><p><? echo $garments_item[$item_id]; ?></p></td>
                                        
                                        <td width="80" align="right">
                                            <? 
                                            $order_quantities = $main_order_data[$order_id][$item_id]["po_quantity"];
                                            echo number_format($order_quantities); 
                                            $total_ord_quantity+=trim($order_quantities); 
                                            ?>
                                            &nbsp;
                                        </td>

                                        <td width="150" align="right">
                                            <? 
                                            $order_values = $main_order_data[$order_id][$item_id]["po_total_price"];
                                            echo number_format($order_values); 
                                            $total_order_values+=trim($order_values); 
                                            ?>
                                            &nbsp;
                                        </td>       
                                        <td width="80" bgcolor="<? echo $color; ?>" align="center">
                                                <? echo change_date_format($orderRes["delivery_date"]);  ?>
                                            </td>                                       
                                        <?
                                        $last_delivered_date=$sqlEx_date_data[$order_id][$item_id]['ex_fac_date'];
                                        $delivered_qty      =$sqlEx_date_data[$order_id][$item_id]['ex_fac_qnty'];
                                        $date               =date("d-m-Y"); 
                                        $days_remain        =""; 
                                        $days_early         ="";

                                        if(($date < change_date_format($orderRes["delivery_date"])) || ($last_delivered_date < change_date_format($orderRes["delivery_date"])))
                                        {
                                            if($delivered_qty >= $order_quantities)
                                            {
                                                $days_early=datediff("d",change_date_format($orderRes["delivery_date"]),$last_delivered_date)-1;
                                                if($days_early<0){$days_early=$days_early*(-1);}
                                            }
                                        }
                                        //calculating Delay delivery days
                                        if($delivered_qty >= $order_quantities)
                                        {
                                            if(($orderRes["delivery_date"] == $last_delivered_date) || ($orderRes["delivery_date"] > $last_delivered_date))
                                            {
                                                $days_remain="";
                                            }
                                        }
                                        else
                                        {
                                            if($date < change_date_format($orderRes["delivery_date"]))
                                            {
                                                $days_remain="";
                                            }
                                            else
                                            {
                                                if($delivered_qty < $order_quantities)
                                                {
                                                    $days_remain=datediff("d",change_date_format($orderRes["delivery_date"]),$date)-1;
                                                }
                                                else if($delivered_qty >= $order_quantities)
                                                {
                                                    $days_remain=datediff("d",change_date_format($orderRes["delivery_date"]),$last_delivered_date)-1;
                                                }
                                            }
                                        }
                                        ?>  

                                        <td width="80" bgcolor="<? echo $color; ?>">
                                                <? 
                                                    if(!($last_delivered_date=="" || $last_delivered_date=="0000-00-00")) 
                                                    echo change_date_format($last_delivered_date);
                                                ?>
                                            </td>

                                        <td width="80" align="center" title="<? echo $days_remain; ?>">
                                                <? echo $days_remain;  ?>
                                            </td>

                                        <td width="80" align="center" title="<? echo $days_early; ?>">
                                                <? echo $days_early; ?>
                                            </td>

                                        <td width="80" align="right"><a href="javascript:void()" onclick="rcvIssueQtyPopup(<? echo $order_id;?>,1,'Material receive Quantity','rcv_issue_qty_popup');">
                                                <? echo number_format($rcv_qty_array[$order_id]['qty']); 
                                                $total_rcv_qty+=$rcv_qty_array[$order_id]['qty']; ?>
                                            </a></td>
                                        <td width="80" align="right"><a href="javascript:void()" onclick="rcvIssueQtyPopup(<? echo $order_id;?>,2,'Material Issue Quantity','rcv_issue_qty_popup');">
                                                <? echo number_format($issue_qty_array[$order_id]['qty']); 
                                                $total_issue_qty+=$issue_qty_array[$order_id]['qty']; ?>
                                            </a></td>                                            
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,1,'Cutting Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]['cutting_qnty']); $total_cutt+=$prod_data_array[$order_id][$item_id]['cutting_qnty']; ?>
                                                
                                            </a>
                                        </td>
                                        <?
                                            $actual_exces_cut = $prod_data_array[$order_id][$item_id]["cutting_qnty"];
                            
                                            if($actual_exces_cut < $order_quantities) {$actual_exces_cut=""; }
                                            else {$actual_exces_cut=number_format( (($actual_exces_cut-$order_quantities)/$order_quantities)*100,2)."%";}
                                        ?>
                                        <td width="80" align="right"><? echo $actual_exces_cut; ?></td>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,7,'Sewing Input Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["sewing_input_qnty"]); $total_sew_in+=$prod_data_array[$order_id][$item_id]["sewing_input_qnty"]; ?>
                                            </a>
                                        </td>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,2,'Sewing Output Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["sewingout_qnty"]); $total_sew+=$prod_data_array[$order_id][$item_id]["sewingout_qnty"]; ?>
                                            </a>    
                                        </td>
                                            
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,3,'Iron Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["iron_qnty"]); $total_iron+=$prod_data_array[$order_id][$item_id]["iron_qnty"]; ?>
                                            </a>    
                                        </td>
                                        <td width="80" align="right">
                                        	<? echo number_format($prod_data_array[$order_id][$item_id]["ploy_qnty"]); $total_ploy_qnty+=$prod_data_array[$order_id][$item_id]["ploy_qnty"]; ?>
                                        </td>                                        
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,4,'Finishing Quantity','production_popup');">
                                            <? echo number_format($prod_data_array[$order_id][$item_id]["finish_qnty"]); $total_finish+=$prod_data_array[$order_id][$item_id]["finish_qnty"]; ?>
                                            </a>        
                                        </td>
                                        <? 
                                            if($sqlEx_date_data[$order_id][$item_id]['ex_fac_qnty'] != "")
                                            {
                                                $tot_delivery=$sqlEx_date_data[$order_id][$item_id]['ex_fac_qnty'];
                                            }
                                            else
                                            {
                                                $tot_delivery=0;
                                            } 
                                            

                                            $total_out_out+=trim($tot_delivery);
                                        ?>
                                        <td width="80" align="right">
                                           
                                        <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.','.$item_id.','.$txt_date_from.','.$txt_date_to;?>,1,'Delivery Quantity','ex_factory_popup');"><? echo number_format($tot_delivery); ?></a> 
                                        </td>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,1,'Reject Quantity','reject_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["reject_qnty"]); $total_reject+=$prod_data_array[$order_id][$item_id]["reject_qnty"]; ?>
                                            </a>   
                                        </td>

                                        <? $shortage = $order_quantities-$tot_delivery; ?>
                                        
                                        <td width="80" align="right"><? echo number_format($shortage); $total_shortage+=$shortage; ?>&nbsp;</td>
                                                                              
                                        
                                        <td width="80" align="right">
                                            <?
                                                $bill_amt = $bill_array[$order_id][$item_id];
                                                echo number_format($bill_amt);
                                                $total_bill_amnt += $bill_amt;
                                            ?>
                                        </td>
                                        <td width="80" align="right">
                                            <?
                                                $rcv_amt = $rcv_array[$order_id][$item_id];
                                                echo number_format($rcv_amt);
                                                $total_rcv_amount += $rcv_amt;
                                            ?>
                                        </td>
                                        <td width="80">
                                            <? //echo $shipment_status[$orderRes[csf("shiping_status")]]; ?>
                                            &nbsp;
                                        </td>
                                        <td width=""><? echo $orderRes["remarks"];?></td>
                                    </tr>
                                    <?
                                }
                            }
                            ?>  
                        </table> 

                        <table border="1" class="tbl_bottom"  width="2470" rules="all" id="report_table_footer_1" >
                            <tr>
                                <td width="40"></td>
                                <td width="130"></td>
                                <td width="100"></td>
                                <td width="100"></td>
                                <td width="100"></td>
                                <td width="150">Total</td>
                                <td width="80" id="total_ord_quantity" align="right"><? echo number_format($total_ord_quantity); ?></td>
                                <td width="150" id="total_ord_values" align="right"><? echo number_format($total_order_values); ?></td>
                                <td width="80"></td>
                                <td width="80"></td>
                                <td width="80"></td>
                                <td width="80"></td>
                                <td width="80"><? echo number_format($total_rcv_qty,0) ?></td>
                                <td width="80"><? echo number_format($total_issue_qty,0) ?></td>
                                <td width="80" align="right"><? echo number_format($total_cutt,0); ?></td>
                                <td width="80"></td>
                                <td width="80" align="right"><? echo number_format($total_sew_in,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_sew,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_iron,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_ploy_qnty,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_finish,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_out_out,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_reject,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_shortage,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_bill_amnt,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_rcv_amount,0); ?></td>
                                <td width="80"></td>
                                <td width=""></td>
                            </tr>
                        </table>
                    </div>
              </div>
        <br /><br />        
        </div><!-- end main div -->
        <?
   /* $html = ob_get_contents();
    ob_clean();
    $new_link=create_delete_report_file( $html, 1, 1, "../../../" );
    
    echo "$html";
    exit();*/ 

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
    echo "$html****$filename"; 
    exit();
}

if($action=="report_generate_for_delivery")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 

    $txt_order_no=str_replace("'","",$txt_order_no);
    if ($txt_order_no!='') $order_no_cond=" and c.order_no like '%$txt_order_no%'"; else $order_no_cond="";

    $type = str_replace("'","",$cbo_type);
    if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";
    if(str_replace("'","",$cbo_buyer_id)==0)$buyer_name="";else $buyer_name=" and a.party_id=$cbo_buyer_id";
    
    if(str_replace("'","",$cbo_location_id)==0)$location="";else $location=" and a.location_id  =$cbo_location_id";
    if(str_replace("'","",$cbo_floor_id)==0)$floor="";else $floor=" and b.floor_id=$cbo_floor_id";
    
    if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
    else $txt_date=" and e.delivery_date between $txt_date_from and $txt_date_to";
    if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$date_cond="";
    else $date_cond=" AND a.delivery_date between $txt_date_from and $txt_date_to";
    
    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
        
        
    if($type==2 || $type==4) //------------------------------------Show Date Location Floor & Line Wise $type==2
    {
        $groupByCond = "group by a.id,c.location,c.floor_id order by a.id,c.location,c.floor_id";
    }
    else //--------------------------------------------Show Order Wise  $type==1
    {
        $groupByCond = "group by a.id order by a.pub_shipment_date,a.id";
    }
    
    // ==================================== MAIN QUERY ====================================
    $sql = "SELECT a.party_id,a.job_no_prefix_num, c.order_no, c.id, c.cust_style_ref,c.order_id,c.delivery_date,b.item_id,MAX(e.delivery_date) AS ex_fac_date, sum(d.delivery_qty) AS ex_fac_qnty
    FROM subcon_ord_mst a,subcon_ord_breakdown b, subcon_ord_dtls c, subcon_delivery_dtls d, subcon_delivery_mst e
    WHERE c.job_no_mst = a.subcon_job AND a.id=b.mst_id and c.id=b.order_id AND c.id=d.order_id and b.item_id=d.item_id and e.id=d.mst_id AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 AND d.is_deleted = 0 AND d.status_active = 1 AND c.is_deleted=0 AND e.is_deleted = 0 AND e.status_active = 1
    $company_name $buyer_name $location $floor $txt_date $order_no_cond group by a.party_id,a.job_no_prefix_num,b.item_id,c.order_no, c.id, c.cust_style_ref,c.order_id,c.delivery_date";
    // echo $sql;
    $sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
            <div style="text-align: center;color: red;font-weight: bold;font-size: 18px;">Report Data Not Found.</div>
        <?
        die();
    }
    $main_data_array    = array();$sqlEx_date_data = array();
    foreach ($sql_res as $key => $row) 
    {
        $order_id_array[$row[csf('id')]] = $row[csf('id')];
        $main_data_array[$row[csf('id')]][$row[csf('item_id')]]['order_no']            = $row[csf('order_no')];
        $main_data_array[$row[csf('id')]][$row[csf('item_id')]]['party_id']            = $row[csf('party_id')];
        $main_data_array[$row[csf('id')]][$row[csf('item_id')]]['job_no']              = $row[csf('job_no_prefix_num')];
        $main_data_array[$row[csf('id')]][$row[csf('item_id')]]['cust_style_ref']      = $row[csf('cust_style_ref')];
        $main_data_array[$row[csf('id')]][$row[csf('item_id')]]['delivery_date']       = $row[csf('delivery_date')];
        $main_data_array[$row[csf('id')]][$row[csf('item_id')]]['remarks']             = $row[csf('remarks')];
        $sqlEx_date_data[$row[csf('id')]][$row[csf('item_id')]]['ex_fac_date']         = $row[csf('ex_fac_date')];
        $sqlEx_date_data[$row[csf('id')]][$row[csf('item_id')]]['ex_fac_qnty']         = $row[csf('ex_fac_qnty')];
    }
    // var_dump( $sqlEx_date_data);die;
    $order_id = implode(",", $order_id_array);
    // ==================================== PRODUCTION DATA QUERY ====================================
    $sql_prod = "SELECT a.party_id,b.gmts_item_id, c.id,
        SUM (CASE WHEN production_type = '1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
        SUM (CASE WHEN production_type = '7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,
        SUM (CASE WHEN production_type = '2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
        SUM (CASE WHEN production_type = '3' THEN production_qnty ELSE 0 END) AS iron_qnty,
        SUM (CASE WHEN production_type = '4' THEN production_qnty ELSE 0 END) AS finish_qnty,
        SUM (CASE WHEN production_type = '5' THEN production_qnty ELSE 0 END) AS ploy_qnty,
        SUM (CASE WHEN production_type in(1,2,3,4,5,7) THEN reject_qnty ELSE 0 END) AS reject_qnty
    FROM subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c, subcon_delivery_dtls d, subcon_delivery_mst e
    WHERE c.job_no_mst = a.subcon_job AND c.id = b.order_id AND c.id=d.order_id and e.id=d.mst_id AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 and c.is_deleted=0 AND d.is_deleted = 0 AND d.status_active = 1 AND e.is_deleted = 0 AND e.status_active = 1 ".where_con_using_array($order_id_array,0,'b.order_id')." $txt_date
    GROUP BY a.party_id,b.gmts_item_id, c.id";
    // echo $sql_prod;
    $sql_prod_res = sql_select($sql_prod);
    $party_data_array   = array();
    $prod_data_array    = array();
    foreach ($sql_prod_res as $key => $row) 
    {                
        // for party data
        $party_data_array[$row[csf('party_id')]]['cutting_qnty']        += $row[csf('cutting_qnty')];
        $party_data_array[$row[csf('party_id')]]['sewing_input_qnty']   += $row[csf('sewing_input_qnty')];
        $party_data_array[$row[csf('party_id')]]['sewingout_qnty']      += $row[csf('sewingout_qnty')];
        $party_data_array[$row[csf('party_id')]]['iron_qnty']           += $row[csf('iron_qnty')];
        $party_data_array[$row[csf('party_id')]]['finish_qnty']         += $row[csf('finish_qnty')];
		$party_data_array[$row[csf('party_id')]]['ploy_qnty']           += $row[csf('ploy_qnty')];

        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['cutting_qnty']        += $row[csf('cutting_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['sewing_input_qnty']   += $row[csf('sewing_input_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['sewingout_qnty']      += $row[csf('sewingout_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['iron_qnty']           += $row[csf('iron_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['ploy_qnty']           += $row[csf('ploy_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['finish_qnty']         += $row[csf('finish_qnty')];
        $prod_data_array[$row[csf('id')]][$row[csf('gmts_item_id')]]['reject_qnty']         += $row[csf('reject_qnty')];
    }

    // ======================================== FOR ORDER QNTY =================================
    $order_sql = "SELECT a.party_id, sum(d.qnty) as po_quantity, sum(d.amount) as po_total_price,c.id,d.item_id 
    from subcon_ord_mst a, subcon_ord_dtls c, subcon_ord_breakdown d 
    where c.job_no_mst=a.subcon_job and a.id=d.mst_id and c.id=d.order_id and a.status_active=1 and c.is_deleted=0 $company_name $buyer_name $location $floor  ".where_con_using_array($order_id_array,0,'d.order_id')."
    group by a.party_id,c.id,d.item_id 
    order by a.party_id ASC";
    $order_sql_res = sql_select($order_sql);
    $party_order_data   = array();
    $main_order_data    = array();
    foreach ($order_sql_res as $key => $val) 
    {
        $party_order_data[$val[csf('party_id')]]['po_quantity']     += $val[csf('po_quantity')];
        $party_order_data[$val[csf('party_id')]]['po_total_price']  += $val[csf('po_total_price')];
        // for details
        $main_order_data[$val[csf('id')]][$val[csf('item_id')]]['po_quantity']      += $val[csf('po_quantity')];
        $main_order_data[$val[csf('id')]][$val[csf('item_id')]]['po_total_price']   += $val[csf('po_total_price')];
    }

    // ======================================== FOR EXFACTORY ===================================
    // $exfactory_sql="SELECT a.party_id,d.order_id,d.item_id, sum(CASE WHEN d.order_id=c.id THEN d.delivery_qty ELSE 0 END) as delivery_quantity 
    // from subcon_ord_mst a, subcon_ord_dtls c, subcon_delivery_dtls d, subcon_delivery_mst e
    // where c.id=d.order_id and a.subcon_job=c.job_no_mst and d.mst_id=e.id and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_name $buyer_name $location $floor $txt_date ".where_con_using_array($order_id_array,0,'c.id')."
    // group by a.party_id,d.order_id,d.item_id";

    $exfactory_sql="SELECT a.party_id,d.item_id,sum (c.delivery_qty) as delivery_qty ,b.order_id
    from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_gmts_delivery_dtls c,subcon_ord_breakdown d 
    where a.id = b.mst_id and b.id = c.dtls_mst_id and a.id = c.mst_id and d.id = c.breakdown_color_size_id and b.order_id = b.order_id 
         $date_cond ".where_con_using_array($order_id_array,0,'b.order_id')." AND a.status_active = 1 AND b.status_active = 1 group by a.party_id, d.item_id,b.order_id";
    // echo $exfactory_sql;
    $exfactory_sql_result=sql_select($exfactory_sql);
    $exfactory_arr=array(); 
    $exfactory_order_item_arr=array();
    foreach($exfactory_sql_result as $resRow)
    {
        $exfactory_arr[$resRow[csf("party_id")]] += $resRow[csf("delivery_qty")];
        $exfactory_order_item_arr[$resRow[csf("order_id")]][$resRow[csf("item_id")]]+= $resRow[csf("delivery_qty")];
    }
        

    ob_start();
    ?>
    <div style="width:1750px"> 
        <table width="1000"  cellspacing="0" cellpadding="0" rules="all" align="center" border="0">
            <tr class="form_caption" style="border:none;">
                <td colspan="13" align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? 
                        if($type==1) echo "SubCon Date Wise Delivery Report";
                        else if($type==2) echo "Order Location & Floor Wise Delivery Report";
                        else if($type==3) echo "Order Country Wise Delivery Report";
                        else echo "Order Country Location & Floor Wise Delivery Report";
                    ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="13" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td colspan="13" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "From $fromDate To $toDate" ;?>
                </td>
            </tr>
        </table>
        <br clear="all">
        
        <div style="float:left; width:750px">
            <table width="840" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th width="40">Sl.</th>    
                        <th width="110">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Input</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Total Iron</th>
                        <th width="80">Total Finishing</th>
                        <th >Delivery Quantity</th>      
                    </tr>
                </thead>
            </table>
            <div style="max-height:425px; width:868px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="840" rules="all" id="" >
                    <?
                        $total_po_quantity  =0;
                        $total_po_value     =0;
                        $total_cutt         =0;
                        $total_sew_in       =0;
                        $total_sew_out      =0;
                        $total_iron         =0;
                        $total_finish       =0;
                        $total_ex_factory   =0;
                        $i=1;
                        // ==================================== SUMMARY LOOP START 
                        foreach($party_data_array as $party_id=>$pval)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                <td width="40"><? echo $i;?></td>
                                <td width="110"><? echo $buyer_short_library[$party_id]; ?></td>
                                <td width="80" align="right"><? echo number_format($party_order_data[$party_id]["po_quantity"]);?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($party_order_data[$party_id]["po_total_price"],2);?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["cutting_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["sewing_input_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["sewingout_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["iron_qnty"]); ?>&nbsp;</td>
                                <td width="80" align="right"><? echo number_format($pval["finish_qnty"]); ?>&nbsp;</td>
                                <td align="right"><? echo number_format($exfactory_arr[$party_id]); ?>&nbsp;</td>
                            </tr>   
                            <?      
                                $total_po_quantity  +=  $party_order_data[$party_id]["po_quantity"];
                                $total_po_value     +=  $party_order_data[$party_id]["po_total_price"];
                                $total_cutt         +=  $pval["cutting_qnty"];
                                $total_sew_in       +=  $pval["sewing_input_qnty"];
                                $total_sew_out      +=  $pval["sewingout_qnty"];
                                $total_iron         +=  $pval["iron_qnty"];
                                $total_finish       +=  $pval["finish_qnty"];
                                $total_ex_factory   +=  $exfactory_arr[$party_id];                           
                            $i++;
                        }
                        ?>
                    </table>
                    <table border="1" class="tbl_bottom"  width="840" rules="all" id="" >
                        <tr> 
                            <td width="40">&nbsp;</td> 
                            <td width="110" align="right">Total</td> 
                            <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?>&nbsp;</td> 
                            <td width="80" id="tot_po_value"><? echo number_format($total_po_value); ?>&nbsp;</td> 
                            <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?>&nbsp;</td>
                            <td width="80" id="tot_cutting"><? echo number_format($total_sew_in); ?>&nbsp;</td>
                            <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?>&nbsp;</td>   
                            <td width="80" id="tot_sew_out"><? echo number_format($total_iron); ?>&nbsp;</td>   
                            <td width="80" id="tot_sew_out"><? echo number_format($total_finish); ?>&nbsp;</td>   
                            <td ><? echo number_format($total_ex_factory); ?>&nbsp;</td >
                        </tr>
                    </table>
                 </div>
                 </div>
                <div style="clear:both"></div>
                <br clear="all">
                <!-- ======================================= Details Part ================================= -->
                <div>                   

                    <table width="1750" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            <tr>
                                <th width="40">SL</th>    
                                <th width="130">Order Number</th>
                                <th width="100">Buyer Name</th>
                                <th width="100">Job Number</th>
                                <th width="100">Style Name</th>
                                <th width="150">Item Name</th>
                                <th width="80">Order Qty.</th>
                                <th width="150">Order Value</th>
                                <th width="80">Plan Delivery Date</th>
                                <th width="80">Delivery Date</th>
                                <th width="80">Total Cut Qty</th>
                                <th width="80">Actual Exc. Cut %</th>
                                <th width="80">Total Inut</th>
                                <th width="80">Total Sew Qty</th>
                                <th width="80">Total Iron</th>
                                <th width="80">Total Poly Qty</th>
                                <th width="80">Total Finishing</th>
                                <th width="80">Total Delivery</th>
                                <th >Total Reject</th>
                            </tr>
                        </thead>
                    </table>


                    <div style="max-height:425px; width:1768px" id="scroll_body">
                        <table border="1" class="rpt_table"  width="1750" rules="all" id="table_body" >
                            <?
                           $i=0; $k=0;
                           // initialize 
                           $total_ord_quantity      = 0;        
                           $total_order_values      = 0;        
                           $total_cutt              = 0;        
                           $total_sew_in            = 0;        
                           $total_sew               = 0;        
                           $total_iron              = 0;  
						   $total_ploy_qnty         = 0;      
                           $total_finish            = 0;        
                           $total_reject            = 0;        
                           $total_out_out           = 0;          
                           // ======================= MAIN LOOP START =====================================    
                           foreach($main_data_array as $order_id=>$orderData)
                           {
                                foreach($orderData as $item_id=>$orderRes)
                                {
                                   $i++;
                                   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";?>                                   
                                    
                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>" style="height:20px">
                                        
                                        <td width="40" ><? echo $i; ?></td>
                                        <td width="130"><p><? echo $orderRes["order_no"]; ?></p></td>
                                        <td width="100"><? echo $buyer_short_library[$orderRes["party_id"]]; ?></td>
                                        <td width="100" align="center"><p><? echo $orderRes["job_no"];?></p></td>
                                        <td width="100"><p><? echo $orderRes["cust_style_ref"]; ?></p></td>
                                        <td width="150"><p><? echo $garments_item[$item_id]; ?></p></td>
                                        
                                        <td width="80" align="right">
                                            <? 
                                            $order_quantities = $main_order_data[$order_id][$item_id]["po_quantity"];
                                            echo number_format($order_quantities); 
                                            $total_ord_quantity+=trim($order_quantities); 
                                            ?>
                                            &nbsp;
                                        </td>

                                        <td width="150" align="right">
                                            <? 
                                            $order_values = $main_order_data[$order_id][$item_id]["po_total_price"];
                                            echo number_format($order_values); 
                                            $total_order_values+=trim($order_values); 
                                            ?>
                                            &nbsp;
                                        </td>       
                                        <td width="80" bgcolor="<? echo $color; ?>" align="center">
                                                <? echo change_date_format($orderRes["delivery_date"]);  ?>
                                            </td>                                       
                                        <?
                                            $last_delivered_date=$sqlEx_date_data[$order_id][$item_id]['ex_fac_date'];  
                                        ?> 

                                        <td width="80" bgcolor="<? echo $color; ?>">
                                                <? 
                                                    if(!($last_delivered_date=="" || $last_delivered_date=="0000-00-00")) 
                                                    echo change_date_format($last_delivered_date);
                                                ?>
                                        </td>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,1,'Cutting Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]['cutting_qnty']); $total_cutt+=$prod_data_array[$order_id][$item_id]['cutting_qnty']; ?>
                                                
                                            </a>
                                        </td>
                                        <?
                                            $actual_exces_cut = $prod_data_array[$order_id][$item_id]["cutting_qnty"];
                            
                                            if($actual_exces_cut < $order_quantities) {$actual_exces_cut=""; }
                                            else {$actual_exces_cut=number_format( (($actual_exces_cut-$order_quantities)/$order_quantities)*100,2)."%";}
                                        ?>
                                        <td width="80" align="right"><? echo $actual_exces_cut; ?></td>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,7,'Sewing Input Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["sewing_input_qnty"]); $total_sew_in+=$prod_data_array[$order_id][$item_id]["sewing_input_qnty"]; ?>
                                            </a>
                                        </td>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,2,'Sewing Output Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["sewingout_qnty"]); $total_sew+=$prod_data_array[$order_id][$item_id]["sewingout_qnty"]; ?>
                                            </a>    
                                        </td>
                                            
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,3,'Iron Quantity','production_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["iron_qnty"]); $total_iron+=$prod_data_array[$order_id][$item_id]["iron_qnty"]; ?>
                                            </a>    
                                        </td>
                                        <td width="80" align="right">
                                        	<? echo number_format($prod_data_array[$order_id][$item_id]["ploy_qnty"]); $total_ploy_qnty+=$prod_data_array[$order_id][$item_id]["ploy_qnty"]; ?>
                                        </td>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,4,'Finishing Quantity','production_popup');">
                                            <? echo number_format($prod_data_array[$order_id][$item_id]["finish_qnty"]); $total_finish+=$prod_data_array[$order_id][$item_id]["finish_qnty"]; ?>
                                            </a>        
                                        </td>
                                        <? 
                                            /*
                                            if($sqlEx_date_data[$order_id][$item_id]['ex_fac_qnty'] != "")
                                            {
                                                $tot_delivery=$sqlEx_date_data[$order_id][$item_id]['ex_fac_qnty'];
                                            }
                                            else
                                            {
                                                $tot_delivery=0;
                                            } 

                                            */
                                            $tot_delivery=$exfactory_order_item_arr[$order_id][$item_id];
                                            $total_out_out+=trim($tot_delivery);
                                        ?>
                                        <td width="80" align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,1,'Delivery Quantity','ex_factory_popup');">
                                                <? echo number_format($tot_delivery); ?>
                                            </a>  
                                        </td>
                                        <td align="right">
                                            <a href="javascript:void()" onclick="prodPoppup(<? echo $order_id.",".$item_id.",'".$fromDate."','".$toDate."'";?>,1,'Reject Quantity','reject_popup');">
                                                <? echo number_format($prod_data_array[$order_id][$item_id]["reject_qnty"]); $total_reject+=$prod_data_array[$order_id][$item_id]["reject_qnty"]; ?>
                                            </a>   
                                        </td>
                                    </tr>
                                    <?
                                }
                            }
                            ?>  
                        </table> 

                        <table border="1" class="tbl_bottom"  width="1750" rules="all" id="report_table_footer_1" >
                            <tr>
                                <td width="40"></td>
                                <td width="130"></td>
                                <td width="100"></td>
                                <td width="100"></td>
                                <td width="100"></td>
                                <td width="150">Total</td>
                                <td width="80" id="total_ord_quantity" align="right"><? echo number_format($total_ord_quantity); ?></td>
                                <td width="150" id="total_ord_values" align="right"><? echo number_format($total_order_values); ?></td>
                                <td width="80"></td>
                                <td width="80"></td>
                                <td width="80" align="right"><? echo number_format($total_cutt,0); ?></td>
                                <td width="80"></td>
                                <td width="80" align="right"><? echo number_format($total_sew_in,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_sew,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_iron,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_ploy_qnty,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_finish,0); ?></td>
                                <td width="80" align="right"><? echo number_format($total_out_out,0); ?></td>
                                <td align="right"><? echo number_format($total_reject,0); ?></td>
                            </tr>
                        </table>
                    </div>
              </div>
        <br /><br />        
        </div><!-- end main div -->
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
    echo "$html****$filename"; 
    exit();
}

if($action=="production_popups")
{
    
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>
    <style type="text/css">
        .rpt_table{ border: 1px solid #345344 !important; }
        .rpt_table td{ border: 1px solid #345344 !important; }
        .rpt_table th{ border: 1px solid #345344 !important; }
    </style>
    <?
    // ================================== GETTING COLOR SIZE ==================================
    $sizearr=return_library_array("SELECT id,size_name from lib_size ","id","size_name");
    $colorarr=return_library_array("SELECT id,color_name from  lib_color ","id","color_name");
    $sizearr_order=return_library_array("select size_id from subcon_ord_breakdown where order_id in($order_id)","size_id","size_id");
    // print_r($sizearr_order);
    $sql="SELECT sum(c.prod_qnty) as prod_qnty,a.item_id,a.color_id,a.size_id
        FROM subcon_ord_breakdown a,subcon_gmts_prod_dtls b,subcon_gmts_prod_col_sz c
        WHERE a.order_id=b.order_id and c.dtls_id=b.id and a.id=c.ord_color_size_id and b.production_type=c.production_type and a.order_id=$order_id and a.item_id=$item_id and b.production_type=$type and b.status_active=1
        group by a.item_id,a.color_id,a.size_id";
    $sql_res = sql_select($sql)    ;
    $job_size_array         = array();
    $job_size_qnty_array    = array();
    $job_color_array        = array();
    $color_size_qnty_array  = array();
    $details_array          = array();

    foreach ($sql_res as $key => $val) 
    {
        $job_size_array[$val[csf('size_id')]]       = $val[csf('size_id')];
        $details_array[$val[csf('color_id')]]       = $val[csf('color_id')];
        $job_size_qnty_array[$val[csf('color_id')]][$val[csf('size_id')]]  = $val[csf('prod_qnty')];
        $color_size_qnty_array[$val[csf('color_id')]][$val[csf('size_id')]] = $val[csf('prod_qnty')];
        $job_color_qnty_array[$val[csf('color_id')]][$val[csf('size_id')]] = $val[csf('prod_qnty')];
        // $details_array[$val[csf('color_id')]][$val[csf('size_id')]] = $val[csf('size_id')];

    }
    $count_size = count($sizearr_order);
    // echo "<pre>";
    // print_r($details_array);
    // echo "</pre>";
    // die();
    $job_sql="SELECT a.party_id,a.subcon_job,b.cust_style_ref, b.order_no
            from subcon_ord_mst a, subcon_ord_dtls b 
            where a.subcon_job= b.job_no_mst and b.id=$order_id and a.status_active=1 and b.status_active=1";
    $job_sql_res = sql_select($job_sql);
    ?>
    <section>
        <div class="main">
            <div class="top_info">
                <table width="100%">
                    <tr>
                        <td width="20%">Job No.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('subcon_job')];?></td>
                        <td width="20%">Buyer Name</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $buyer_short_library[$job_sql_res[0][csf('party_id')]];?></td>
                    </tr>
                    <tr>
                        <td width="20%">Style Ref.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('cust_style_ref')];?></td>
                        <td width="20%">Order No.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('order_no')];?></td>
                    </tr>
                    <tr>
                        <td width="20%">Item Name</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $garments_item[$item_id];?></td>
                    </tr>
                </table>
            </div>   
            <?
            if($type==1) // for cutting 
            {                        
                ?>                             
                    <div class="details-info" style="margin: 20px auto">
                        <table width="<? echo 280+($counts*50); ?>" id="table_body" border="1" rules="all" class="rpt_table" align="left">
                            <tr>
                                <th width="30" rowspan="2">SI</th>
                                <th width="150" rowspan="2">Color</th>
                                <th colspan="<? echo $count_size;?>">Size</th>
                                <th width="100" rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <? 
                                    foreach ($sizearr_order as $size_id) 
                                    {
                                        ?>
                                            <th width="50"><? echo $sizearr[$size_id]; ?></th>
                                        <?  
                                    } 
                                ?>
                                
                            </tr>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $color_key => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $colorarr[$color_key];?></td>
                                    <?
                                    $color_total = 0;
                                    foreach ($sizearr_order as $size_key => $value) 
                                    {
                                        ?>
                                            <td align="right" width="100"><? echo $tot = $color_size_qnty_array[$color_key][$size_key];?></td>
                                        <?
                                        $color_total+=$tot;
                                        $size_qnty_arr[$size_key]+=$color_size_qnty_array[$color_key][$size_key];
                                    }
                                    ?>
                                    <td align="right" width="100"><? echo $color_total;?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            // print_r($size_qnty_arr);
                            ?>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <td colspan="2" width="180" align="right">Total</td>
                                <?
                                $size_total = 0;
                                $tot = 0;                                    
                                    foreach ($sizearr_order as $size_key => $vals) 
                                    {
                                        ?>
                                            <td align="right" width="50"><? echo $tot = $size_qnty_arr[$size_key];?></td>
                                        <?
                                        $size_total+=$tot;
                                    }
                                    
                                    ?>
                                <td width="100" align="right"><? echo  $size_total; ?></td>
                            </tr>
                        </table>
                    </div>
                <?
            }
            if($type==7) // for sewing input 
            {                        
                ?>                             
                    <div class="details-info" style="margin: 20px auto">
                        <table width="<? echo 280+($counts*50); ?>" id="table_body" border="1" rules="all" class="rpt_table" align="left">
                            <tr>
                                <th width="30" rowspan="2">SI</th>
                                <th width="150" rowspan="2">Color</th>
                                <th colspan="<? echo $count_size;?>">Size</th>
                                <th width="100" rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <? 
                                    foreach ($sizearr_order as $size_id) 
                                    {
                                        ?>
                                            <th width="50"><? echo $sizearr[$size_id]; ?></th>
                                        <?  
                                    } 
                                ?>
                                
                            </tr>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $color_key => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $colorarr[$color_key];?></td>
                                    <?
                                    $color_total = 0;
                                    foreach ($sizearr_order as $size_key => $value) 
                                    {
                                        ?>
                                            <td align="right" width="100"><? echo $tot = $color_size_qnty_array[$color_key][$size_key];?></td>
                                        <?
                                        $color_total+=$tot;
                                        $size_qnty_arr[$size_key]+=$tot;
                                    }
                                    ?>
                                    <td align="right" width="100"><? echo $color_total;?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <td colspan="2" width="180" align="right">Total</td>
                                <?
                                $size_total = 0;
                                $tot = 0;                                    
                                    foreach ($sizearr_order as $size_key => $vals) 
                                    {
                                        ?>
                                            <td align="right" width="50"><? echo $tot = $size_qnty_arr[$size_key];?></td>
                                        <?
                                        $size_total+=$tot;
                                    }
                                    
                                    ?>
                                <td width="100" align="right"><? echo  $size_total; ?></td>
                            </tr>
                        </table>
                    </div>
                <?
            }
            if($type==2) // for sewing output 
            {                        
                ?>                             
                    <div class="details-info" style="margin: 20px auto">
                        <table width="<? echo 280+($counts*50); ?>" id="table_body" border="1" rules="all" class="rpt_table" align="left">
                            <tr>
                                <th width="30" rowspan="2">SI</th>
                                <th width="150" rowspan="2">Color</th>
                                <th colspan="<? echo $count_size;?>">Size</th>
                                <th width="100" rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <? 
                                    foreach ($sizearr_order as $size_id) 
                                    {
                                        ?>
                                            <th width="50"><? echo $sizearr[$size_id]; ?></th>
                                        <?  
                                    } 
                                ?>
                                
                            </tr>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $color_key => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $colorarr[$color_key];?></td>
                                    <?
                                    $color_total = 0;
                                    foreach ($sizearr_order as $size_key => $value) 
                                    {
                                        ?>
                                            <td align="right" width="100"><? echo $tot = $color_size_qnty_array[$color_key][$size_key];?></td>
                                        <?
                                        $color_total+=$tot;
                                        $size_qnty_arr[$size_key]+=$tot;
                                    }
                                    ?>
                                    <td align="right" width="100"><? echo $color_total;?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <td colspan="2" width="180" align="right">Total</td>
                                <?
                                $size_total = 0;
                                $tot = 0;                                    
                                    foreach ($sizearr_order as $size_key => $vals) 
                                    {
                                        ?>
                                            <td align="right" width="50"><? echo $tot = $size_qnty_arr[$size_key];?></td>
                                        <?
                                        $size_total+=$tot;
                                    }
                                    
                                    ?>
                                <td width="100" align="right"><? echo  $size_total; ?></td>
                            </tr>
                        </table>
                    </div>
                <?
            }
            if($type==3) // for iron
            {                        
                ?>                             
                    <div class="details-info" style="margin: 20px auto">
                        <table width="<? echo 280+($counts*50); ?>" id="table_body" border="1" rules="all" class="rpt_table" align="left">
                            <tr>
                                <th width="30" rowspan="2">SI</th>
                                <th width="150" rowspan="2">Color</th>
                                <th colspan="<? echo $count_size;?>">Size</th>
                                <th width="100" rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <? 
                                    foreach ($sizearr_order as $size_id) 
                                    {
                                        ?>
                                            <th width="50"><? echo $sizearr[$size_id]; ?></th>
                                        <?  
                                    } 
                                ?>
                                
                            </tr>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $color_key => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $colorarr[$color_key];?></td>
                                    <?
                                    $color_total = 0;
                                    foreach ($sizearr_order as $size_key => $value) 
                                    {
                                        ?>
                                            <td align="right" width="100"><? echo $tot = $color_size_qnty_array[$color_key][$size_key];?></td>
                                        <?
                                        $color_total+=$tot;
                                        $size_qnty_arr[$size_key]+=$color_size_qnty_array[$color_key][$size_key];
                                    }
                                    ?>
                                    <td align="right" width="100"><? echo $color_total;?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <td colspan="2" width="180" align="right">Total</td>
                                <?
                                $size_total = 0;
                                $tot = 0;                                    
                                    foreach ($sizearr_order as $size_key => $vals) 
                                    {
                                        ?>
                                            <td align="right" width="50"><? echo $tot = $size_qnty_arr[$size_key];?></td>
                                        <?
                                        $size_total+=$tot;
                                    }
                                    
                                    ?>
                                <td width="100" align="right"><? echo  $size_total; ?></td>
                            </tr>
                        </table>
                    </div>
                <?
            }
            if($type==4) // for finishing 
            {                        
                ?>                             
                    <div class="details-info" style="margin: 20px auto">
                        <table width="<? echo 280+($counts*50); ?>" id="table_body" border="1" rules="all" class="rpt_table" align="left">
                            <tr>
                                <th width="30" rowspan="2">SI</th>
                                <th width="150" rowspan="2">Color</th>
                                <th colspan="<? echo $count_size;?>">Size</th>
                                <th width="100" rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <? 
                                    foreach ($sizearr_order as $size_id) 
                                    {
                                        ?>
                                            <th width="50"><? echo $sizearr[$size_id]; ?></th>
                                        <?  
                                    } 
                                ?>
                                
                            </tr>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $color_key => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $colorarr[$color_key];?></td>
                                    <?
                                    $color_total = 0;
                                    foreach ($sizearr_order as $size_key => $value) 
                                    {
                                        ?>
                                            <td align="right" width="100"><? echo $tot = $color_size_qnty_array[$color_key][$size_key];?></td>
                                        <?
                                        $color_total+=$tot;
                                        $size_qnty_arr[$size_key]+=$color_size_qnty_array[$color_key][$size_key];
                                    }
                                    ?>
                                    <td align="right" width="100"><? echo $color_total;?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <td colspan="2" width="180" align="right">Total</td>
                                <?
                                $size_total = 0;
                                $tot = 0;                                    
                                    foreach ($sizearr_order as $size_key => $vals) 
                                    {
                                        ?>
                                            <td align="right" width="50"><? echo $tot = $size_qnty_arr[$size_key];?></td>
                                        <?
                                        $size_total+=$tot;
                                    }
                                    
                                    ?>
                                <td width="100" align="right"><? echo  $size_total; ?></td>
                            </tr>
                        </table>
                    </div>
                <?
            }
            ?>    
        </div>
    </section>
    <?
}
// delivery popup
if($action=="ex_factory_popup")
{
    
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>
    <style type="text/css">
       /* .rpt_table{ border: 1px solid #345344 !important; }
        .rpt_table td{ border: 1px solid #345344 !important; }
        .rpt_table th{ border: 1px solid #345344 !important; }*/
    </style>
    <?
    // ================================== GETTING COLOR SIZE ==================================
    $sizearr=return_library_array("SELECT id,size_name from lib_size ","id","size_name");
    $colorarr=return_library_array("SELECT id,color_name from  lib_color ","id","color_name");
    $sizearr_order=return_library_array("select size_id from subcon_ord_breakdown where order_id in($order_id)","size_id","size_id");

    if($start_date !='' && $end_date !=''){
		if($db_type==0)
		{
			$start_date = date("Y-m-d",strtotime($start_date));
			$end_date = date("Y-m-d",strtotime($end_date));
		}
		else
		{
			$start_date = date("d-M-Y",strtotime($start_date));
			$end_date = date("d-M-Y",strtotime($end_date));
		}
		$date_con =" and a.delivery_date between '$start_date' and '$end_date'";
	}
	
    // print_r($sizearr_order);
     $sql="SELECT a.delivery_no,d.color_id,d.size_id,d.item_id,sum (c.delivery_qty) as delivery_qty
    from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_gmts_delivery_dtls c,subcon_ord_breakdown d
    where a.id = b.mst_id and b.id = c.dtls_mst_id and a.id = c.mst_id and d.id = c.breakdown_color_size_id and b.order_id = b.order_id and b.order_id =$order_id and b.item_id=$item_id AND a.status_active = 1 AND b.status_active = 1 $date_con
    group by a.delivery_no,d.color_id, d.size_id, d.item_id";
    $sql_res = sql_select($sql)    ;
    $job_size_array         = array();
    $job_size_qnty_array    = array();
    $job_color_array        = array();
    $color_size_qnty_array  = array();
    $details_array          = array();

    foreach ($sql_res as $key => $val) 
    {
        $delivery_noArr[$val[csf('delivery_no')]]=$val[csf('delivery_no')];
	    $job_size_array[$val[csf('size_id')]]       = $val[csf('size_id')];
        $details_array[$val[csf('color_id')]]       = $val[csf('color_id')];
        $job_size_qnty_array[$val[csf('color_id')]][$val[csf('size_id')]]  = $val[csf('delivery_qty')];
        $color_size_qnty_array[$val[csf('color_id')]][$val[csf('size_id')]] = $val[csf('delivery_qty')];
        $job_color_qnty_array[$val[csf('color_id')]][$val[csf('size_id')]] = $val[csf('delivery_qty')];
        // $details_array[$val[csf('color_id')]][$val[csf('size_id')]] = $val[csf('size_id')];

    }
    $count_size = count($sizearr_order);
    // echo "<pre>";
    // print_r($color_size_qnty_array);
    // echo "</pre>";
    // die();
    $job_sql="SELECT a.party_id,a.subcon_job,b.cust_style_ref, b.order_no
            from subcon_ord_mst a, subcon_ord_dtls b 
            where a.subcon_job= b.job_no_mst and b.id=$order_id and a.status_active=1 and b.status_active=1";
    $job_sql_res = sql_select($job_sql);
    ?>
    <fieldset>
        <div class="main">
            <div class="top_info">
             
                <table width="100%" cellpadding="0" cellspacing="0" border="0" rules="all" style="margin-bottom:10px; margin-top:10px;">
               
                    <tr>
                        <td width="20%">Job No.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('subcon_job')];?></td>
                        <td width="20%">Buyer Name</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $buyer_short_library[$job_sql_res[0][csf('party_id')]];?></td>
                    </tr>
                    <tr>
                        <td width="20%">Style Ref.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('cust_style_ref')];?></td>
                        <td width="20%">Order No.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('order_no')];?></td>
                    </tr>
                    <tr>
                        <td width="20%">Item Name</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $garments_item[$item_id];?></td>
                    </tr>
                </table>
            </div>   
            <?
			//echo $type.'d';
            if($type==1) // for cutting 
            {                        
                ?>                             
                    <div class="details-info" style="margin: 20px auto">
                    <div>Delivery ID: <? echo implode(",",$delivery_noArr);?></div>
                        <table width="<? echo 280+($counts*50); ?>" id="table_body" border="1" rules="all" class="rpt_table" align="left">
                            <thead>
                                <tr>
                                    <th width="30" rowspan="2">SI</th>
                                    <th width="150" rowspan="2">Color</th>
                                    <th colspan="<? echo $count_size;?>">Size</th>
                                    <th width="100" rowspan="2">Total</th>
                                </tr>
                                <tr>
                                    <? 
                                        foreach ($sizearr_order as $size_id) 
                                        {
                                            ?>
                                                <th width="50"><? echo $sizearr[$size_id]; ?></th>
                                            <?  
                                        } 
                                    ?>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                // ==================== lOOP START ===========================-
                                $sl=1;
                                foreach ($details_array as $color_key => $val) 
                                {
                                    ?>
                                    <tr>
                                        <td width="30"><? echo $sl;?></td>
                                        <td width="150"><? echo $colorarr[$color_key];?></td>
                                        <?
                                        $color_total = 0;
                                        foreach ($sizearr_order as $size_key => $value) 
                                        {
                                            ?>
                                                <td align="right" width="100"><? echo $tot = $color_size_qnty_array[$color_key][$size_key];?></td>
                                            <?
                                            $color_total+=$tot;
                                            $size_qnty_arr[$size_key]+=$color_size_qnty_array[$color_key][$size_key];
                                        }
                                        ?>
                                        <td align="right" width="100"><? echo $color_total;?></td>
                                    </tr>
                                    <?
                                    $sl++;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr><!-- ==================== SUMATION PART ===========================-->
                                    <th colspan="2" width="180" align="right">Total</th>
                                    <?
                                    $size_total = 0;
                                    $tot = 0;                                    
                                        foreach ($sizearr_order as $size_key => $vals) 
                                        {
                                            ?>
                                                <th align="right" width="50"><? echo $tot = $size_qnty_arr[$size_key];?></th>
                                            <?
                                            $size_total+=$tot;
                                        }
                                        
                                        ?>
                                    <th width="100" align="right"><? echo  $size_total; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?
            }
            ?>    
        </div>
    </fieldset>
    <?
}

// reject popup
if($action=="reject_popup")
{
    
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>
    <style type="text/css">
        /*.rpt_table{ border: 1px solid #345344 !important; }
        .rpt_table td{ border: 1px solid #345344 !important; }
        .rpt_table th{ border: 1px solid #345344 !important; }*/
        .details-info{ clear: both;padding: 20px 10px; }
    </style>
    <?
    // ================================== GETTING COLOR SIZE ==================================
    $sizearr=return_library_array("SELECT id,size_name from lib_size ","id","size_name");
    $colorarr=return_library_array("SELECT id,color_name from  lib_color ","id","color_name");
    $sizearr_order=return_library_array("select size_id from subcon_ord_breakdown where order_id in($order_id)","size_id","size_id");
    // print_r($sizearr_order);
   
    $count_size = count($sizearr_order);
    // echo "<pre>";
    // print_r($details_array);
    // echo "</pre>";
    // die();
    $job_sql="SELECT a.party_id,a.subcon_job,b.cust_style_ref, b.order_no
            from subcon_ord_mst a, subcon_ord_dtls b 
            where a.subcon_job= b.job_no_mst and b.id=$order_id and a.status_active=1 and b.status_active=1";
    $job_sql_res = sql_select($job_sql);
    ?>
    <fieldset>
        <div class="main">
            <div class="top_info">
                <table width="100%" cellpadding="0" cellspacing="0" rules="all">
                    <tr>
                        <td width="20%">Job No.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('subcon_job')];?></td>
                        <td width="20%">Buyer Name</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $buyer_short_library[$job_sql_res[0][csf('party_id')]];?></td>
                    </tr>
                    <tr>
                        <td width="20%">Style Ref.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('cust_style_ref')];?></td>
                        <td width="20%">Order No.</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $job_sql_res[0][csf('order_no')];?></td>
                    </tr>
                    <tr>
                        <td width="20%">Item Name</td>
                        <td width="5%">:</td>
                        <td width="25%"><? echo $garments_item[$item_id];?></td>
                    </tr>
                </table>
            </div>   
            <hr class="hr">
            <?
            $cutting_sql="SELECT sum(a.reject_qnty) as reject_qnty,a.gmts_item_id
            FROM subcon_gmts_prod_dtls a
            WHERE a.order_id=$order_id and a.gmts_item_id=$item_id and a.production_type=1 and a.status_active=1
            group by a.gmts_item_id";
            $cutting_sql_res = sql_select($cutting_sql);

            if(count($cutting_sql_res) > 0) // cutting reject 
            {
                $details_array = array();
                $grand_total = 0;

                foreach ($cutting_sql_res as $key => $val) 
                {
                    $details_array[$val[csf('gmts_item_id')]]['reject_qnty']       += $val[csf('reject_qnty')];
                }                        
                ?>                             
                <div class="details-info" style="margin: 20px auto">
                    <span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span><br clear="all">
                    <table width="<? echo 280; ?>" id="table_body" rules="all" class="rpt_table" align="left">
                        <thead>
                            <tr>
                                <th width="30">SI</th>
                                <th width="150">Item Name</th>
                                <th width="100">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $item_id => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $garments_item[$item_id];?></td>                                    
                                    <td align="right" width="100"><? echo $val['reject_qnty']; $grand_total +=$val['reject_qnty'];?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <th colspan="2" width="180" align="right">Total</th>                                
                                <th width="100" align="right"><? echo  $grand_total; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?
            }
            
            $sewing_in_sql="SELECT sum(a.reject_qnty) as reject_qnty,a.gmts_item_id
            FROM subcon_gmts_prod_dtls a
            WHERE a.order_id=$order_id and a.gmts_item_id=$item_id and a.production_type=7 and a.status_active=1
            group by a.gmts_item_id";
            $sewing_in_sql_res = sql_select($sewing_in_sql);

            if(count($sewing_in_sql_res) > 0) //sewing in
            {
                $details_array = array();
                $grand_total = 0;

                foreach ($sewing_in_sql_res as $key => $val) 
                {
                    $details_array[$val[csf('gmts_item_id')]]['reject_qnty'] += $val[csf('reject_qnty')];
                }                        
                ?>                             
                <div class="details-info" style="margin: 20px auto">
                    <span style="font-size:18px; font-weight:bold;">Sewing Input Reject Quantity</span><br clear="all">
                    <table width="<? echo 280; ?>" id="table_body" rules="all" class="rpt_table" align="left">
                        <thead>
                            <tr>
                                <th width="30">SI</th>
                                <th width="150">Item Name</th>
                                <th width="100">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $item_id => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $garments_item[$item_id];?></td>                                    
                                    <td align="right" width="100"><? echo $val['reject_qnty']; $grand_total +=$val['reject_qnty'];?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <th colspan="2" width="180" align="right">Total</th>                                
                                <th width="100" align="right"><? echo  $grand_total; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?
            }
            
            $sewing_out_sql="SELECT sum(a.reject_qnty) as reject_qnty,a.gmts_item_id
            FROM subcon_gmts_prod_dtls a
            WHERE a.order_id=$order_id and a.gmts_item_id=$item_id and a.production_type=2 and a.status_active=1
            group by a.gmts_item_id";
            $sewing_out_sql_res = sql_select($sewing_out_sql);

            if(count($sewing_out_sql_res) > 0) //sewing output
            {
                $details_array = array();
                $grand_total = 0;

                foreach ($sewing_out_sql_res as $key => $val) 
                {
                    $details_array[$val[csf('gmts_item_id')]]['reject_qnty'] += $val[csf('reject_qnty')];
                }                        
                ?>                             
                <div class="details-info" style="margin: 20px auto">
                    <span style="font-size:18px; font-weight:bold;">Sewing Output Reject Quantity</span><br clear="all">
                    <table width="<? echo 280; ?>" id="table_body" rules="all" class="rpt_table" align="left">
                        <thead>
                            <tr>
                                <th width="30">SI</th>
                                <th width="150">Item Name</th>
                                <th width="100">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $item_id => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $garments_item[$item_id];?></td>                                    
                                    <td align="right" width="100"><? echo $val['reject_qnty']; $grand_total +=$val['reject_qnty'];?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <th colspan="2" width="180" align="right">Total</th>                                
                                <th width="100" align="right"><? echo  $grand_total; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?
            }
            
            $iron_sql="SELECT sum(a.reject_qnty) as reject_qnty,a.gmts_item_id
            FROM subcon_gmts_prod_dtls a
            WHERE a.order_id=$order_id and a.gmts_item_id=$item_id and a.production_type=3 and a.status_active=1
            group by a.gmts_item_id";
            $iron_sql_res = sql_select($iron_sql);

            if(count($iron_sql_res) > 0) // iron reject
            {
                $details_array = array();
                $grand_total = 0;

                foreach ($iron_sql_res as $key => $val) 
                {
                    $details_array[$val[csf('gmts_item_id')]]['reject_qnty'] += $val[csf('reject_qnty')];
                }                        
                ?>                             
                <div class="details-info" style="margin: 20px auto">
                    <span style="font-size:18px; font-weight:bold;">Iron Reject Quantity</span><br clear="all">
                    <table width="<? echo 280; ?>" id="table_body"  rules="all" class="rpt_table" align="left">
                        <thead>
                            <tr>
                                <th width="30">SI</th>
                                <th width="150">Item Name</th>
                                <th width="100">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $item_id => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $garments_item[$item_id];?></td>                                    
                                    <td align="right" width="100"><? echo $val['reject_qnty']; $grand_total +=$val['reject_qnty'];?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <th colspan="2" width="180" align="right">Total</th>                                
                                <th width="100" align="right"><? echo  $grand_total; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?
            }
            
            $finish_sql="SELECT sum(a.reject_qnty) as reject_qnty,a.gmts_item_id
            FROM subcon_gmts_prod_dtls a
            WHERE a.order_id=$order_id and a.gmts_item_id=$item_id and a.production_type=4 and a.status_active=1
            group by a.gmts_item_id";
            $finish_sql_res = sql_select($finish_sql);

            if(count($finish_sql_res) > 0) // finishing reject
            {
                $details_array = array();
                $grand_total = 0;

                foreach ($finish_sql_res as $key => $val) 
                {
                    $details_array[$val[csf('gmts_item_id')]]['reject_qnty'] += $val[csf('reject_qnty')];
                }                        
                ?>                             
                <div class="details-info" style="margin: 20px auto">
                    <span style="font-size:18px; font-weight:bold;">Finishing Reject Quantity</span><br clear="all">
                    <table width="<? echo 280; ?>" id="table_body" rules="all" class="rpt_table" align="left">
                        <thead>
                            <tr>
                                <th width="30">SI</th>
                                <th width="150">Item Name</th>
                                <th width="100">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            // ==================== lOOP START ===========================-
                            $sl=1;
                            foreach ($details_array as $item_id => $val) 
                            {
                                ?>
                                <tr>
                                    <td width="30"><? echo $sl;?></td>
                                    <td width="150"><? echo $garments_item[$item_id];?></td>                                    
                                    <td align="right" width="100"><? echo $val['reject_qnty']; $grand_total +=$val['reject_qnty'];?></td>
                                </tr>
                                <?
                                $sl++;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr><!-- ==================== SUMATION PART ===========================-->
                                <th colspan="2" width="180" align="right">Total</th>                                
                                <th width="100" align="right"><? echo  $grand_total; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?
            }
            ?>    
        </div><br clear="all">
    </fieldset>
    <?
}


if($action=="production_popup")
{
    echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
    extract($_REQUEST);
    // $gmts_item_id=explode("_", $gmts_item_id);
    $sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
    $colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"); 
    $order_library=return_library_array( "select id,order_no from  subcon_ord_dtls where id=$order_id", "id", "order_no");  
    $buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
    $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
    $sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
    $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    
	
	
	
    $order_sql= "SELECT b.id, b.order_no as po_number,  b.cust_style_ref as style_ref_no, a.party_id as buyer_name,  c.item_id as gmts_item_id,b.job_no_mst as job_no,c.color_id as color_number_id ,c.size_id as size_number_id,sum(c.qnty) as  qnty,c.id 
    from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
    where a.subcon_job=b.job_no_mst and a.id=c.mst_id and b.id=c.order_id and c.order_id=b.id  and b.id=$order_id and c.item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   
    group by  b.id, b.order_no,  b.cust_style_ref, a.party_id,  c.item_id,b.job_no_mst  ,c.size_id, c.color_id,c.id order by c.id";

    $po_details_sql=sql_select($order_sql);
    $sizearr_order=array();
    foreach($po_details_sql as $val)
    {
        $sizearr_order[$val[csf("size_number_id")]]=$val[csf("size_number_id")];
        $summary_data[$val[csf('color_number_id')]]+=$val[csf('qnty')];
        $summary_data2[$val[csf('color_number_id')]][$val[csf('size_number_id')]] +=$val[csf('qnty')];
    }

 	
	if($start_date !='' && $end_date !=''){
		if($db_type==0)
		{
			$start_date = date("Y-m-d",strtotime($start_date));
			$end_date = date("Y-m-d",strtotime($end_date));
		}
		else
		{
			$start_date = date("d-M-Y",strtotime($start_date));
			$end_date = date("d-M-Y",strtotime($end_date));
		}
		$date_con =" and c.production_date between '$start_date' and '$end_date'";
	}
	
	
	
	$prod_sql= "SELECT  c.company_id,b.color_id as color_number_id,c.line_id as sewing_line,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id,
    NVL(sum(CASE WHEN c.production_type ='1' THEN  d.prod_qnty  ELSE 0 END),0) AS cutting_qnty,
    sum(CASE WHEN c.production_type ='7' THEN  d.prod_qnty  ELSE 0 END) AS sewing_input_qnty,

    NVL(sum(CASE WHEN c.production_type ='2' THEN  d.prod_qnty  ELSE 0 END),0) AS sewingout_qnty,
    NVL(sum(CASE WHEN c.production_type ='3' THEN  d.prod_qnty  ELSE 0 END),0) AS ironout_qnty,
    NVL(sum(CASE WHEN c.production_type ='4' THEN  d.prod_qnty  ELSE 0 END),0) AS gmts_fin_qnty
    from 
    subcon_gmts_prod_dtls c,subcon_gmts_prod_col_sz d,subcon_ord_breakdown b
    where c.id=d.dtls_id and b.id=d.ord_color_size_id and  c.status_active=1 and c.is_deleted=0  and b.order_id=$order_id and b.item_id='$item_id' and b.item_id=c.gmts_item_id and c.production_type ='$type' group by c.company_id,b.color_id,c.line_id,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id";//, c.production_date 


    $prod_sql2= "SELECT  c.production_date,c.company_id,b.color_id as color_number_id,b.size_id as size_number_id, c.line_id as sewing_line,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id, c.production_date,
    sum(CASE WHEN c.production_type ='$type' THEN  d.prod_qnty  ELSE 0 END) AS production_qnty 
    from 
    subcon_gmts_prod_dtls c,subcon_gmts_prod_col_sz d,subcon_ord_breakdown b
    where c.id=d.dtls_id $date_con and b.id=d.ord_color_size_id and  c.status_active=1 and c.is_deleted=0  and b.order_id=$order_id and b.item_id='$item_id' and b.item_id=c.gmts_item_id and c.production_type ='$type'  group by c.company_id,b.color_id,b.size_id, c.line_id,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id, c.production_date ";

	//echo  $prod_sql2; 
     
    
    foreach(sql_select($prod_sql2) as $row)
    {
        $production_break_qnty[$row[csf('company_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		//$production_date=$row[csf('production_date')];
    }
    $result=sql_select($prod_sql);
    
    $col_width=60*count($sizearr_order);
    //$table_width=630+$col_width;
    $table_width=630+$col_width;
    $summer_table_width=230+$col_width;
    ?>  
    <script>

        function print_window()
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
            d.close();
        }
        
        function window_close()
        {
            parent.emailwindow.hide();
        }
        
    </script>   
    <fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
            <input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
        <div style="100%" id="report_container">
        
            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
                <tr>
                    <td style="font-size:14px; font-weight:bold;">
                    Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
                    <?
                        $item_data=""; 
                        $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
                        foreach($garments_item_arr as $item_id)
                        {
                            if($item_data!="") $item_data .=", ";
                            $item_data .=$garments_item[$item_id];
                        }
                        echo $item_data;
                    ?>
                    <br />
                    Order No : <? echo $order_library[$order_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <?= $start_date .' To '.  $end_date; ?> 
                    <br />
                    Summary
                    </td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SI</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                        <th width="80" rowspan="2" >Total</th>
                    </tr>
                    <tr>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    </tr>
                </thead>
                <tbody>
                <?
                $i=1;
                //var_dump($result);die;
                foreach($summary_data as $color_id=>$row)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $i;  ?></td>
                        <td ><? echo $colorarr[$color_id];  ?></td>
                        <?
                        $summry_color_total_in =0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($summary_data2[$color_id][$size_id],0) ; $summry_color_total_in+= $summary_data2[$color_id][$size_id]; $summry_color_size_in[$size_id]+=$summary_data2[$color_id][$size_id];?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th ><? echo $summry_color_size_in[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th><? echo $grand_tot_in; ?></th>
                </tfoot>
            </table>
            <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
                <tr>
                    <td style="font-size:14px; font-weight:bold;">Details</td>
                </tr>
            </table>
            <div>
                <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                    <thead>
                        <tr>
                            <th width="40" rowspan="2">SI</th>
                            <th width="100" rowspan="2">Company Name</th>
                             
                            
                            <th width="70" rowspan="2">Challan</th>
                            <th width="90" rowspan="2">Sewing Unit</th>
                            <th width="70" rowspan="2">Sewing Line</th>
                            <th width="100" rowspan="2">Color</th>
                            <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                            <th width="80" rowspan="2" >Total</th>
                        </tr>
                        <tr>
                        <?
                        $grand_tot_in=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $sizearr[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        </tr>
                        
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $k=1;
                    //var_dump($result);die;
                    foreach($result as $row)
                    {
                        if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
                        {
                            $temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
                            if($k!=1)
                            {
                                ?>
                                <tr bgcolor="#CCCCCC">
                                    <td >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    
                                    <td >&nbsp;</td>
                                    <td >&nbsp;</td>
                                    <td >&nbsp;</td>
                                    <td >&nbsp;</td>
                                    <?
                                    foreach($sizearr_order as $size_id)
                                    {
                                        ?>
                                        <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                                        <?
                                    }
                                    ?>
                                    <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                                </tr>
                                <?
                                $line_color_size_in = $line_color_total_in ="";
                            }
                            $k++;
                        }
                        
                        $sewing_line='';
                        if($row[csf('prod_reso_allo')]==1)
                        {
                            $line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
                            foreach($line_number as $val)
                            {
                                if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
                            }
                        }
                        else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];
                        
                        ?>
                        <tr>
                            <td align="center"><? echo $i;  ?></td>
                            <td ><p><? echo $company_library[$row[csf("company_id")]];  ?></p></td>
                            
                            <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
                            <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                            <td align="center"><p><? echo $sewing_line;  ?></p></td>
                            <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                            <?
                            $color_total_in=0;
                            foreach($sizearr_order as $size_id)
                            {
                                $Production_qty=0;
                                ?>
                                <td align="right"><p>
                                <?
                                    $Production_qty=$production_break_qnty[$row[csf('company_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('color_number_id')]][$size_id];
                                    echo number_format($Production_qty,0);
                                     $color_total_in+=$Production_qty; $color_size_in [$size_id]+=$Production_qty; $line_color_total_in+=$Production_qty; $line_color_size_in [$size_id]+=$Production_qty;
                                 ?>
                                </p></td>
                                <?
                            }
                            ?>
                            <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    <tr bgcolor="#CCCCCC">
                        <td >&nbsp;</td>
                        <td>&nbsp;</td>
                        
                        <td >&nbsp;</td>
                        <td >&nbsp;</td>
                        <td >&nbsp;</td>
                        <td >&nbsp;</td>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                    </tr>
                    </tbody>
                    <tfoot>
                        <th >&nbsp;</th>
                        <th>&nbsp;</th>
                        
                        <th >&nbsp;</th>
                        <th >&nbsp;</th>
                        <th >&nbsp;</th>
                        <th >&nbsp;</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th ><? echo $color_size_in[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th ><? echo $grand_tot_in; ?></th>
                    </tfoot>
                </table>
            </div>
            </div>
        </fieldset>
    <?
    exit(); 
}
if($action == 'rcv_issue_qty_popup'){
    echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
    extract($_REQUEST);
    if($type==1){
        $sql_data=sql_select("SELECT a.sys_no, a.subcon_date, b.order_id, b.item_category_id, b.material_description, b.quantity  from sub_material_mst a join sub_material_dtls b on a.id=b.mst_id where b.status_active=2 and a.status_active=1 and a.is_deleted=0 and b.order_id=$order_id");
        ?>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="350">
            <thead> 
                <tr>
                    <th width="80">Receive date</th>
                    <th width="120">Sys ID</th>
                    <th width="100">Item Category</th>
                    <th width="80">Receive Qty</th>
                </tr>
            </thead>
            <? foreach ($sql_data as $row) { ?>
                <tr>
                    <td><? echo change_date_format($row[csf('subcon_date')],'yyyy-mm-dd') ?></td>
                    <td><? echo $row[csf('sys_no')] ?></td>
                    <td><? echo $item_category[$row[csf('item_category_id')]] ?></td>
                    <td><? echo $row[csf('quantity')] ?></td>
                </tr>
            <? } ?>
            
        </table>
    <?
    }
    if($type==2){
        $sql_data =sql_select("SELECT c.sys_no, a.id, a.mst_id, a.item_category_id, a.material_description, a.quantity, a.subcon_uom, a.subcon_roll, a.grey_dia, a.status_active, b.order_no, c.subcon_date from sub_material_dtls a,subcon_ord_dtls b, sub_material_mst c  where c.id=a.mst_id and a.order_id=b.id and a.status_active=1 and a.order_id=$order_id");
        ?>
    <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="350">
        <thead> 
            <tr>
                <th width="80">Issue date</th>
                <th width="120">Sys ID</th>
                <th width="100">Item Category</th>
                <th width="80">Receive Qty</th>
            </tr>
        </thead>
        <? foreach ($sql_data as $row) { ?>
            <tr>
                <td><? echo change_date_format($row[csf('subcon_date')],'yyyy-mm-dd') ?></td>
                <td><? echo $row[csf('sys_no')] ?></td>
                <td><? echo $item_category[$row[csf('item_category_id')]] ?></td>
                <td><? echo $row[csf('quantity')] ?></td>
            </tr>
        <? } ?>            
    </table>
    <?
    }   

}
?>