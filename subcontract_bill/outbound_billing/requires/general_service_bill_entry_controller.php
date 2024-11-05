<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
//print_r ($data[0]);
$action=$_REQUEST['action'];

$bill_process_id= 10;   // a hardcoded unique number only for this bill_entry
$entry_form = 483;      // the entry form array index of this page

if ($action=='load_drop_down_location')
{
    echo create_drop_down('cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 order by location_name", 'id,location_name', 1, '--Select Location--', $selected);
    exit();  
}

if ($action=='load_drop_down_supplier_name')
{
    echo create_drop_down('cbo_supplier_company', 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data' and sup.id in (select supplier_id from  lib_supplier_party_type where party_type in (7)) order by supplier_name", 'id,supplier_name', 1, '-- Select supplier --', $selected);
    exit();
}

if ($action=='outside_bill_popup')
{
    echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode, '', '');
    $ex_data=explode('_',$data);
    ?>
    <script>
        function js_set_value(id)
        { 
            document.getElementById('outside_bill_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="serviceBill_1" id="serviceBill_1" autocomplete="off">
        <table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th width="150">Company Name</th>
                <th width="150">Supplier Name</th>
                <th width="80">Bill ID</th>
                <th width="170">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
            </thead>
            <tbody>
                <tr>
                <td>
                    <input type="hidden" id="outside_bill_id">  
                    <?php   
                        echo create_drop_down('cbo_company_id', 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $ex_data[0], "load_drop_down( 'general_service_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td' );",1 );
                    ?>
                </td>
                <td width="140" id="supplier_td">
                    <?php
                        echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$ex_data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=7) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $ex_data[1], "","","","","","",5 );
                    ?> 
                </td>
                <td>
                    <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                </td> 
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'outside_yarn_service_bill_list_view', 'search_div', 'general_service_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                </td>
            </tr>
            <tr>
                <td colspan="5" align="center" height="40" valign="middle">
                    <?php echo load_month_buttons(1); ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
            </tr>
        </table>    
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?php
    exit();
}

if ($action=='outside_yarn_service_bill_list_view')
{
    $data=explode('_',$data);
    if ($data[0]!=0) $company_cond=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $supplier_cond=" and supplier_id='$data[1]'"; 
    
    if($db_type==0)
    { 
        if ($data[2]!="" &&  $data[3]!="") $trans_date_cond = "and bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
    }
    else
    {
        if ($data[2]!="" &&  $data[3]!="") $trans_date_cond = "and bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
    }
    if ($data[4]!='') $bill_id_cond=" and prefix_no_num='$data[4]'"; else $bill_id_cond="";
    
    $location=return_library_array('select id,location_name from lib_location', 'id', 'location_name');
    $supplier_library_arr=return_library_array('select id,supplier_name from lib_supplier', 'id', 'supplier_name');
    $arr=array(2=>$location,4=>$supplier_library_arr,5=>$yarn_issue_purpose,6=>$production_process);
    
    if($db_type==0)
    {
        $year_cond= "year(insert_date)as year";
    }
    else if($db_type==2)
    {
        $year_cond= "TO_CHAR(insert_date,'YYYY') as year";
    }
    
    $sql="select id, bill_no, prefix_no_num, $year_cond, party_bill_no, bill_date, supplier_id, bill_for
    from subcon_outbound_bill_mst
    where  entry_form=483 and status_active=1 $company_cond $supplier_cond $trans_date_cond $bill_id_cond
    order by id desc";
    // echo $sql;
    
    echo create_list_view('list_view', 'Bill No,Year,Party Bill No,Bill Date,Supplier,Bill For', '70,70,100,100,120,100', '600', '250', 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0,0,supplier_id,bill_for', $arr, 'prefix_no_num,year,party_bill_no,bill_date,supplier_id,bill_for', 'general_service_bill_entry_controller', '', '0,0,0,3,0,0');
    exit(); 
}

if ($action=='load_php_data_to_form_outside_bill')
{
    $sql="SELECT min(receive_date) as min_date, max(receive_date) as max_date from subcon_outbound_bill_dtls where mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";
    
    $sql_result_arr =sql_select($sql);
    $mindate='';  $maxdate='';
    $mindate=$sql_result_arr[0][csf('min_date')];
    $maxdate=$sql_result_arr[0][csf('max_date')];

    
    $nameArray= sql_select("SELECT id, entry_form, prefix_no, prefix_no_num, bill_no,service_wo_num, company_id, location_id, bill_date, supplier_id, pay_mode, exchange_rate, party_bill_no, trans_from_date, currency_id, wo_non_order_info_mst_id, tenor, remarks ,is_posted_account, is_approved, ready_to_approve from subcon_outbound_bill_mst where id='$data'");
      
    foreach ($nameArray as $row)
    {   
        echo "document.getElementById('txt_bill_no').value                  = '".$row[csf("bill_no")]."';\n";
        echo "document.getElementById('cbo_company_id').value               = '".$row[csf("company_id")]."';\n";
        echo "load_drop_down( 'requires/general_service_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";

        echo "document.getElementById('cbo_location_name').value            = '".$row[csf("location_id")]."';\n"; 
        echo "document.getElementById('txt_bill_date').value                = '".change_date_format($row[csf("bill_date")])."';\n";   
        echo "load_drop_down( 'requires/general_service_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier_name', 'supplier_td' );\n";
        echo "document.getElementById('cbo_supplier_company').value         = '".$row[csf("supplier_id")]."';\n"; 
        echo "document.getElementById('cbo_pay_mode').value                 = '".$row[csf("pay_mode")]."';\n"; 
        echo "document.getElementById('txt_party_bill').value               = '".$row[csf("party_bill_no")]."';\n"; 
        echo "document.getElementById('txt_bill_date').value           = '".change_date_format($row[csf("bill_date")])."';\n";  
        echo "document.getElementById('txt_tenor').value           = '".$row[csf("tenor")]."';\n";
        echo "document.getElementById('txt_remarks').value           = '".$row[csf("remarks")]."';\n";
        echo "document.getElementById('cbo_approved').value           = '".$row[csf("ready_to_approve")]."';\n";
        echo "document.getElementById('txtexchange_rate').value           = '".$row[csf("exchange_rate")]."';\n";
        echo "document.getElementById('cbo_currency_id').value           = '".$row[csf("currency_id")]."';\n";
        echo "document.getElementById('txt_wo_number').value           = '".$row[csf("service_wo_num")]."';\n";
        echo "document.getElementById('hidden_wo_update_id').value           = '".$row[csf("wo_non_order_info_mst_id")]."';\n";
        echo "document.getElementById('txt_is_posted_account').value           = '".$row[csf("is_posted_account")]."';\n";
        // echo "document.getElementById('txt_bill_no').value           = '".$row[csf("currency_id")]."';\n";
        echo "document.getElementById('update_id').value             = '".$row[csf("id")]."';\n";
        echo  "show_list_view('".$row[csf("id")]."','show_dtls_listview_update','outside_yarnservicebill_table','requires/general_service_bill_entry_controller','');";
        echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_supplier_company*cbo_pay_mode',1);\n";
        echo "document.getElementById('update_id').value                    = '".$row[csf("id")]."';\n";      
        if($row[csf("is_posted_account")]==1){ echo " $('#notice').show();"; }

        if($row[csf("is_approved")]==1)
        {
            echo "$('#approved').text('Approved');\n";
        }
        elseif($row[csf("is_approved")]==3)
        {
            echo "$('#approved').text('Partial Approved');\n";
        }else{
            echo "$('#approved').text('');\n";
        }
    }
    exit();
}

if ($action=='outside_yarn_service_info_list_view')
{
    echo load_html_head_contents('Popup Info', '../../', 1, 1, $unicode, 1, '');
    $from=1;
    $exdata=explode('***', $data);
    $cbo_company_id=$exdata[0];
    $cbo_supplier_company=$exdata[1];
    $ex_bill_for=$exdata[2];
    $date_from=$exdata[3];
    $date_to=$exdata[4];
    $manualChallan=$exdata[5];
    $variable_check=$exdata[6];
    $update_id=$exdata[7];
    $str_data=$exdata[8];
    $ex_str_data=explode('!!!!', $str_data);
    $str_arr=array();
    foreach($ex_str_data as $str)
    {
        $str_arr[]=$str;
    }
    
    if($db_type==0)
    { 
        if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
    }
    else if ($db_type==2)
    {
        if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
    }

    $color_arr=return_library_array('select id, color_name from lib_color where status_active=1', 'id', 'color_name');
    $buyer_arr=return_library_array('select id, buyer_name from lib_buyer', 'id', 'buyer_name');
    $booking_no_arr=return_library_array('select id, ydw_no from wo_yarn_dyeing_mst where entry_form not in (42,114)', 'id', 'ydw_no');
    $product_dtls_arr=array();
    
    $sql_prod= sql_select("select id, product_name_details, lot, color from product_details_master where company_id=$data[0] and item_category_id=1");
    foreach($sql_prod as $row)
    {
        $product_dtls_arr[$row[csf('id')]]['prod_name']=$row[csf('product_name_details')];
        $product_dtls_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
        $product_dtls_arr[$row[csf('id')]]['color']=$row[csf('color')];
    }

    unset($sql_prod);
    
    $bill_qty_array=array();
    $sql_bill="select receive_id, sum(receive_qty) as bill_qty from subcon_outbound_bill_dtls where status_active=1 and is_deleted=0 and process_id=$bill_process_id group by receive_id";
    $sql_bill_result =sql_select($sql_bill);
    foreach($sql_bill_result as $row)
    {
        $bill_qty_array[$row[csf('receive_id')]]['qty']=$row[csf('bill_qty')];
    }
    unset($sql_bill_result);
    
    $i=1;
    
    if($db_type==0)
    {
        $year_cond="year(a.insert_date)";
        $booking_without_order="IFNULL(d.booking_without_order,0)";
    }
    else if($db_type==2) 
    {
        $year_cond="TO_CHAR(a.insert_date,'YYYY')";
        $booking_without_order="nvl(d.booking_without_order,0)";
    }
    
    $job_arr=array();

    $sql_job="select id, job_no, style_ref_no, buyer_name from wo_po_details_master where is_deleted=0 and status_active=1";
    $sql_order_result=sql_select($sql_job);
    foreach ($sql_order_result as $row)
    {
        $job_arr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
        $job_arr[$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
    }
    unset($sql_order_result);
    $sql="select a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.receive_purpose, a.booking_id, a.receive_date, a.challan_no, a.store_id, a.currency_id, a.exchange_rate, a.remarks,  b.id as trans_id, b.no_of_bags, b.cone_per_bag, b.receive_basis, b.job_no, b.prod_id, b.brand_id, b.cons_uom, b.cons_quantity, b.cons_avg_rate, b.dye_charge, b.cons_amount, b.buyer_id, b.yarn_count
    from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c 
    where a.id=b.mst_id and a.booking_id=c.id and a.entry_form=1 and a.item_category=1 and a.item_category=b.item_category and b.transaction_type=1 and a.receive_purpose=$ex_bill_for and a.receive_basis in(2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id=$cbo_company_id and a.supplier_id=$cbo_supplier_company $date_cond order by a.id desc";
    
    $sql_result=sql_select($sql);
    
    ?>
    </head>
    <body>
    <div id="body-close-after-populate">
        <div align="center" style="width:100%;" >   
            <div style="width:100%;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040px" class="rpt_table">
                    <thead>
                        <th width="30">&nbsp;</th>
                        <th width="25">SL</th>
                        <th width="50">Sys. Challan</th>
                        <th width="60">Challan No</th>
                        <th width="65">Recive Date</th>
                        <th width="80">Color</th>
                        <th width="160">Yarn Description</th>
                        <th width="60">Receive Qty</th>
                        <th width="60">Service Charge</th>
                        <th width="120">Job No</th>
                        <th width="120">Style Ref.</th>
                        <th>Buyer</th>
                    </thead>
                </table>
            </div>
        <div style="width:1040px;max-height:180px; overflow-y:scroll" id="kintt_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020px" class="rpt_table" id="tbl_list_search">
                <tbody>
                <?php
                foreach($sql_result as $row) // for update row
                {
                    $conv_rate=sql_select("select conversion_rate from currency_conversion_rate where con_date = (select max(con_date) from currency_conversion_rate where is_deleted=0 and status_active=1 and currency=".$row[csf('currency_id')].")");
                    
                    $prod_id = $row[csf("prod_id")];
                    $all_value=$row[csf('trans_id')];
    
                    if(in_array($all_value,$str_arr))
                    {
                        $bookingNos=$booking_no_arr[$row[csf('booking_id')]];
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; //rec_qty grey_qty
                    
                        $avilable_qty=0; $rec_percent=0; $bill_qty=0;
                        $bill_qty=$bill_qty_array[$row[csf('trans_id')]]['qty'];
                        $avilable_qty=$row[csf('cons_quantity')]-$bill_qty;
                        $on_bill_qty=$row[csf('cons_quantity')];
                        $amount=$avilable_qty*$row[csf('dye_charge')];
                        $dom_currency = $amount * $convRate=  ($conv_rate[0][csf("conversion_rate")])?$conv_rate[0][csf("conversion_rate")]:1;
                        
                        $str_val=$row[csf('trans_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('prod_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']].'_'.$product_dtls_arr[$row[csf('prod_id')]]['lot'].'_'.$row[csf('booking_id')].'_'.$booking_no_arr[$row[csf('booking_id')]].'_'.$row[csf('cons_uom')].'_'.$avilable_qty.'_'.$row[csf('dye_charge')].'_'.$amount.'_'.$dom_currency.'_1_'.$row[csf('remarks')].'_0';
                                
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr id="tr_<?php echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?php echo $all_value; ?>');" >
                            <td width="30" align="center"><input type="checkbox" name="checkid<?php echo $i; ?>" id="checkid<?php echo $i; ?>" onClick="fnc_check(<?php echo $i; ?>)" value="1" checked ></td>
                            <td width="25"><?php echo $i; ?></td>
                            <td width="50"><?php echo $row[csf('recv_number_prefix_num')]; ?></td>
                            <td width="60"><?php echo $row[csf('challan_no')]; ?></td>
                            <td width="65"><p><?php echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" style="word-break: break-all;"><?php echo $color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']]; ?></td>
                            <td width="160" style="word-break: break-all;"><?php echo $product_dtls_arr[$row[csf('prod_id')]]['prod_name']; ?></td>
                            <td width="60" align="right"><?php echo number_format($avilable_qty,2); ?></td>
                            <td width="60" align="right"><?php echo number_format($row[csf('dye_charge')],2); ?></td>
                            <td width="120" style="word-break: break-all;"><?php echo $row[csf("job_no")]; ?></td>
                            <td width="120" style="word-break: break-all;"><?php echo $job_arr[$row[csf("job_no")]]['style']; ?></td>
                            <td style="word-break: break-all;"><?php echo $buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']]; ?>
                            <input type="hidden" id="strid<?php echo $i; ?>" value="<?php echo $str_val; ?>">
                            <input type="hidden" id="currid<?php echo $i; ?>" value="<?php echo $all_value; ?>"></td>
                        </tr>
                        <?
                        $i++;
                    }
                }
                
                foreach($sql_result as $row) // for new row
                {
                    $conv_rate=sql_select("select conversion_rate from currency_conversion_rate where con_date = (select max(con_date) from currency_conversion_rate where is_deleted=0 and status_active=1 and currency=".$row[csf('currency_id')].")");
                    
                    $prod_id = $row[csf("prod_id")];
                    $all_value=$row[csf('trans_id')];
    
                    $bookingNos=$booking_no_arr[$row[csf('booking_id')]];
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; //rec_qty grey_qty
                
                    $avilable_qty=0; $rec_percent=0; $bill_qty=0;
                    $bill_qty=$bill_qty_array[$row[csf('trans_id')]]['qty'];
                    $avilable_qty=$row[csf('cons_quantity')]-$bill_qty;
                    $on_bill_qty=$row[csf('cons_quantity')];
                    $amount=$avilable_qty*$row[csf('dye_charge')];
                    $dom_currency = $amount * $convRate=  ($conv_rate[0][csf("conversion_rate")])?$conv_rate[0][csf("conversion_rate")]:1;
                    
                    $str_val=$row[csf('trans_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('prod_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']].'_'.$product_dtls_arr[$row[csf('prod_id')]]['lot'].'_'.$row[csf('booking_id')].'_'.$booking_no_arr[$row[csf('booking_id')]].'_'.$row[csf('cons_uom')].'_'.$avilable_qty.'_'.$row[csf('dye_charge')].'_'.$amount.'_'.$dom_currency.'_1_'.$row[csf('remarks')].'_0';
                    if($avilable_qty>0)
                    {       
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr id="tr_<? echo $all_value; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
                            <td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
                            <td width="25"><? echo $i; ?></td>
                            <td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                            <td width="60"><? echo $row[csf('challan_no')]; ?></td>
                            <td width="65"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            
                            <td width="80" style="word-break: break-all;"><? echo $color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']]; ?></td>
                            
                            <td width="160" style="word-break: break-all;"><? echo $product_dtls_arr[$row[csf('prod_id')]]['prod_name']; ?></td>
                            <td width="60" align="right"><? echo number_format($avilable_qty,2); ?></td>
                            <td width="60" align="right"><? echo number_format($row[csf('dye_charge')],2); ?></td>
                            <td width="120" style="word-break: break-all;"><? echo $row[csf("job_no")]; ?></td>
                            <td width="120" style="word-break: break-all;"><? echo $job_arr[$row[csf("job_no")]]['style']; ?></td>
                            <td style="word-break: break-all;"><? echo $buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']]; ?>
                            <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                            <input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
                        </tr>
                        <?
                        $i++;                        
                    }
                }
                ?>
                </tbody>
            </table>
            </div>
            </div>
            <div>
                <table width="940px" >
                    <tr>
                        <td colspan="10" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </div>
    </body>           
    <script src="../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="remarks_popup")
{
    echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
    function js_set_value(val)
    {
        document.getElementById('text_new_remarks').value=val;
        parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center">
    <fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<?php echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><?php echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0) // Insert Here-------------------
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        if($db_type==0)
        {
            $year_cond=" and YEAR(insert_date)";    
        }
        else if($db_type==2)
        {
            $year_cond=" and TO_CHAR(insert_date,'YYYY')";  
        }

        $new_bill_no=explode("*",return_mrr_number( str_replace("'", "",$cbo_company_id), '', 'GSB', date("Y",time()), 5, "SELECT prefix_no, prefix_no_num from subcon_outbound_bill_mst where company_id=$cbo_company_id and  entry_form = 483  $year_cond=".date('Y',time())." order by id desc", 'prefix_no', 'prefix_no_num' ));

        $id=return_next_id('id', 'subcon_outbound_bill_mst', 1 );
        $field_array='id, entry_form, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, pay_mode, exchange_rate, party_bill_no,currency_id,service_wo_num,wo_non_order_info_mst_id,tenor,remarks,ready_to_approve,inserted_by,insert_date';
        $data_array="(".$id.",".$entry_form.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_supplier_company.",".$cbo_pay_mode.",".$txtexchange_rate.",".$txt_party_bill.",".$cbo_currency_id.",".$txt_wo_number.",".$hidden_wo_update_id.",".$txt_tenor.",".$txt_remarks.",".$cbo_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
        
        $total_row = str_replace("'","",$total_row);
        
        $id1=return_next_id('id', 'subcon_outbound_bill_dtls', 1);
        $field_array1 = 'id, mst_id, prod_mst_id, asset_no, receive_qty, rate, amount,wo_non_order_info_dtls_id,bill_status, inserted_by, insert_date';
        $add_comma=0;
           
        for($i=1; $i<=$total_row; $i++)
        {
            $prod_id='txtProduct_id_'.$i;
            $asset_no='txtAssetNo_'.$i;
            $qty='txtqnty_'.$i;              
            $rate='txtrate_'.$i; 
            $amount='txtamount_'.$i;
            $service_dtls_id='txt_service_dtls_'.$i;
            $check='check_'.$i;
            
    
            if ($add_comma!=0) $data_array1 .=",";
            $asset_no="'".$$asset_no."'";
            $data_array1 .="(".$id1.",".$id.",'".$$prod_id."',".$asset_no.",".$$qty.",".$$rate.",".$$amount.",".$$service_dtls_id.",".$$check.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
            //."',".$$curanci.",".$$subprocessId.",".$$serviceSource
            $id1=$id1+1;
            $add_comma++;               
        }
        //echo "insert into subcon_outbound_bill_mst (".$field_array.") values ".$data_array;die;
        $rID=sql_insert('subcon_outbound_bill_mst', $field_array, $data_array, 1);
        $rID1=sql_insert('subcon_outbound_bill_dtls', $field_array1, $data_array1, 1);
      
        //echo "5**".$rID."**".$dtlsrID; die;
        if($db_type==0)
        {
            if($rID && $rID1)
            {
                mysql_query("COMMIT");
                echo "0**".$new_bill_no[0]."**".$id;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID1)
            {
                oci_commit($con);
                echo "0**".$new_bill_no[0]."**".$id;
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==1) // Update Here----------------------------------------------------------
    {
        $update_id=str_replace("'","",$update_id);

        $approved_sql = "select a.is_approved from subcon_outbound_bill_mst a where a.id=$update_id  and a.entry_form=483 and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";

        $approved_arr=sql_select($approved_sql);

        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Full Approved Found";
                die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found";
                die;
            }
        }


        $sql_is_posted=sql_select("SELECT id from subcon_outbound_bill_mst where status_active=1 and entry_form=483 and id= '$update_id' and is_posted_account <> 0 ");
        if(count($sql_is_posted))
        {
            echo "12**Already Posted In Accounting. Save Update Delete Restricted";
            die;
        }
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }

       
        $total_row = str_replace("'","",$total_row);

        $field_array='location_id*bill_date*supplier_id*pay_mode*exchange_rate*party_bill_no*currency_id*service_wo_num*wo_non_order_info_mst_id*tenor*remarks*ready_to_approve*updated_by*update_date';
   
        $data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$cbo_pay_mode."*".$txtexchange_rate."*".$txt_party_bill."*".$cbo_currency_id."*".$txt_wo_number."*".$hidden_wo_update_id."*".$txt_tenor."*".$txt_remarks."*".$cbo_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


        $id1=return_next_id('id', 'subcon_outbound_bill_dtls', 1);
        $field_array1 = 'prod_mst_id*asset_no*receive_qty*rate*amount*wo_non_order_info_dtls_id*bill_status*updated_by*update_date';
        
        
        $field_newInsert = 'id, mst_id, prod_mst_id, asset_no, receive_qty, rate, amount,wo_non_order_info_dtls_id,bill_status, inserted_by, insert_date';
        $add_comma=0;
        $dtls_insert=1;
        for($i=1; $i<=$total_row; $i++)
        {
            $prod_id='txtProduct_id_'.$i;
            $asset_no='txtAssetNo_'.$i;
            $qty='txtqnty_'.$i;              
            $rate='txtrate_'.$i; 
            $amount='txtamount_'.$i;
            $dtls_id='txt_dtls_id_'.$i;
            $service_dtls_id='txt_service_dtls_'.$i;
            $check='check_'.$i;

            if(str_replace("'","",$$dtls_id)!="")
            {
                $id_arr[]=str_replace("'",'',$$dtls_id);
                $dataArrDtlsUp[str_replace("'",'',$$dtls_id)] =explode("*",("'".$$prod_id."'*'".$$asset_no."'*'".$$qty."'*'".$$rate."'*'".$$amount."'*'".$$service_dtls_id."'*'".$$check."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
            }
            else
            {
                if ($add_comma!=0) $data_array1 .=",";
                $asset_no="'".$$asset_no."'";
                $data_newInsert .="(".$id1.",".$update_id.",".$$prod_id.",".$asset_no.",".$$qty.",".$$rate.",".$$amount.",".$$service_dtls_id.",".$$check.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                //."',".$$curanci.",".$$subprocessId.",".$$serviceSource
                $id1=$id1+1;
                $add_comma++;
                $dtls_insert=2;
            }           
        }

        $rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,1);  
        if($dtls_insert==2)
        {       
            $dtlsrID = sql_update("subcon_outbound_bill_dtls",'status_active*is_deleted','0*1',"mst_id",$update_id,1);
            $rID1=sql_insert('subcon_outbound_bill_dtls', $field_newInsert, $data_newInsert, 1);
        }
        else
        {
            $rID1=execute_query(bulk_update_sql_statement("subcon_outbound_bill_dtls", "id",$field_array1,$dataArrDtlsUp,$id_arr ));
            //  echo "10**".$rID."=".bulk_update_sql_statement("subcon_outbound_bill_dtls", "id",$field_array1,$dataArrDtlsUp,$id_arr );die;
        }

        if($db_type==0)
        {
            if($rID && $rID1)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'","",$txt_bill_no)."**".$update_id;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID1)
            {
                oci_commit($con);
                echo "1**".str_replace("'","",$txt_bill_no)."**".$update_id;
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
            //echo "1**".$txt_wo_number."**".$update_check."**".$dtlsid_check;
        }
        disconnect($con);
        die;
    }
    else if ($operation==2) // Delete Here--------------------
    {
        $update_id=str_replace("'","",$update_id);

        $approved_sql = "select a.is_approved from subcon_outbound_bill_mst a where a.id=$update_id  and a.entry_form=483 and a.status_active=1 and a.is_approved!=0 and a.is_deleted=0";

        $approved_arr=sql_select($approved_sql);

        if(count($approved_arr)>0)
        {
            if($approved_arr[0][csf('is_approved')]==1)
            {
                echo "13**Update Or Delete not allowed. Full Approved Found";
                die;
            }
            else
            {
                echo "13**Update Or Delete not allowed. Partial Approved Found";
                die;
            }
        }


        $sql_is_posted=sql_select("SELECT id from subcon_outbound_bill_mst where status_active=1 and entry_form=483 and id= '$update_id' and is_posted_account <> 0 ");
        if(count($sql_is_posted))
        {
            echo "12**Already Posted In Accounting. Save Update Delete Restricted";
            die;
        }
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }

        $txt_wo_number = str_replace("'", "", $txt_wo_number);
        $mst_sql=sql_select("select id from subcon_outbound_bill_mst where status_active=1 and entry_form=483 and id= '$update_id'");
        $mst_id = $mst_sql[0][csf("id")];

        $rID = sql_update("subcon_outbound_bill_mst",'status_active*is_deleted','0*1',"id",$mst_id,0);
        $dtlsrID = sql_update("subcon_outbound_bill_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
        if($db_type==0)
        {
            if($rID && $dtlsrID)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'","",$txt_wo_number);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        //oci_commit($con); oci_rollback($con);
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $dtlsrID)
            {
                oci_commit($con);
                echo "2**".str_replace("'","",$rID);
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
            //echo "2**".$rID;
        }
        disconnect($con);
        die;
    }
}

if ($action=='load_dtls_data')
{    
    $buyer_arr=return_library_array('select id, buyer_name from lib_buyer', 'id', 'buyer_name');
    $roll_no_arr=return_library_array('select id, no_of_roll from  ro_grey_prod_entry_dtls', 'id', 'no_of_roll');
    $product_dtls_arr=return_library_array('select id,product_name_details from product_details_master', 'id','product_name_details');
    $color_arr=return_library_array('select id, color_name from lib_color', 'id', 'color_name');
    $booking_no_arr=return_library_array("select id, ydw_no from wo_yarn_dyeing_mst where entry_form not in (42,114)",'id','ydw_no');
        
    $sql_job="select id, job_no, style_ref_no, buyer_name from wo_po_details_master where is_deleted=0 and status_active=1";
    $sql_order_result=sql_select($sql_job);
    foreach ($sql_order_result as $row)
    {
        $job_arr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
        $job_arr[$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
    }
    unset($sql_order_result);
    
    $product_dtls_arr=array();
    //echo "select id, product_name_details, lot, color from product_details_master where company_id=$data[0] and item_category_id=1"; die;
    $sql_prod= sql_select("select id, product_name_details, lot, color from product_details_master where item_category_id=1");// company_id=$data[0] and
    foreach($sql_prod as $row)
    {
        $product_dtls_arr[$row[csf('id')]]['prod_name']=$row[csf('product_name_details')];
        $product_dtls_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
        $product_dtls_arr[$row[csf('id')]]['color']=$row[csf('color')];
    }
    unset($sql_prod);
    
    $bill_qty_array=array();
    $sql_bill="select receive_id, sum(receive_qty) as bill_qty
    from subcon_outbound_bill_dtls
    where status_active=1 and is_deleted=0 and process_id=$bill_process_id=";
    $sql_bill_result =sql_select($sql_bill);
    foreach($sql_bill_result as $row)
    {
        $bill_qty_array[$row[csf('receive_id')]]['qty']=$row[csf('bill_qty')];
    }
    unset($sql_bill_result);
    
    $sql="select id, mst_id, receive_id, receive_date, mrr_no, challan_no, job_no, item_id, color_id, wo_num_id, uom, receive_qty, rate, amount, remarks, process_id, currency_id, no_of_bags, cone_per_bag, domestic_currency
    from subcon_outbound_bill_dtls
    where mst_id='$data' and status_active=1 and is_deleted=0 and process_id=$bill_process_id order by id ASC";
    $sql_result_arr =sql_select($sql); $str_val="";
    foreach ($sql_result_arr as $row)
    {
        $conv_rate=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=".$row[csf('currency_id')].")");
        $bill_qty=$bill_qty_array[$row[csf('receive_id')]]['qty'];
        $avilable_qty=$row[csf('receive_qty')];//-$bill_qty;
        $on_bill_qty=$row[csf('receive_qty')];
        $amount=$row[csf('amount')];
        $dom_currency = $amount * $convRate=  ($conv_rate[0][csf("conversion_rate")])?$conv_rate[0][csf("conversion_rate")]:1;        
        
        if($str_val=="") 
        {
            $amount=$row[csf('amount')];
            $dom_currency= $row[csf('domestic_currency')];
            $str_val=$row[csf('receive_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('mrr_no')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('item_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('item_id')]]['color']].'_'.$product_dtls_arr[$row[csf('item_id')]]['lot'].'_'.$row[csf('wo_num_id')].'_'.$booking_no_arr[$row[csf('wo_num_id')]].'_'.$row[csf('uom')].'_'.$avilable_qty.'_'.$row[csf('rate')].'_'.$amount.'_'.$dom_currency.'_'.$row[csf('currency_id')].'_'.$row[csf('remarks')].'_'.$row[csf('id')];
            
        }
        else 
        {
           $str_val.="###".$row[csf('receive_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('mrr_no')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('item_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('item_id')]]['color']].'_'.$product_dtls_arr[$row[csf('item_id')]]['lot'].'_'.$row[csf('wo_num_id')].'_'.$booking_no_arr[$row[csf('wo_num_id')]].'_'.$row[csf('uom')].'_'.$avilable_qty.'_'.$row[csf('rate')].'_'.$amount.'_'.$dom_currency.'_'.$row[csf('currency_id')].'_'.$row[csf('remarks')].'_'.$row[csf('id')];
        }
    }
    
    echo $str_val;
    exit();
}

if($action=="wo_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>

    <script>
    function js_set_value(wo_number)
    {
        $("#hidden_wo_number").val(wo_number);
        // alert(wo_number);
        parent.emailwindow.hide();
    }

    </script>
    </head>

    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                            <th width="160">Item Category</th>
                            <th width="160" align="center">WO Number</th>
                            <th width="200">WO Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
                        <tr>
                            <td width="160">
                            <?
                                echo create_drop_down( "cboitem_category", 160, "select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "", "","","4,11");
                            ?>
                            </td>
                            <td width="160" align="center">
                                <input type="text" style="width:140px" class="text_boxes"  name="txt_wo" id="txt_wo" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_wo').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wo_search_list_view', 'search_div', 'general_service_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
            <tr>
            <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_wo_search_list_view")
{
    extract($_REQUEST);
    $ex_data = explode("_",$data);
    $itemCategory = $ex_data[0];
    $txt_wo_number = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];
	
	$variable_subnon_acknowldgement=return_field_value("dyeing_fin_bill","variable_settings_subcon","company_id=$company    and variable_list=20 and status_active=1 and is_deleted=0  ");
	
	$category_cond = "";
	if(trim($itemCategory)) $category_cond .= " and b.item_category_id='$itemCategory'";
    $sql_cond="";
    if ($txt_wo_number!="") $sql_cond .= " and a.wo_number like '%".trim($txt_wo_number)."'";

    if ($txt_date_from!="" &&  $txt_date_to!="")
    {
        if($db_type==0)
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }
    }

    if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
	else $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);

	// $approval_status="select id,approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=44 and status_active=1 and is_deleted=0 order by id desc ";   
    $approval_status="select id,approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=44 and status_active=1 and is_deleted=0 order by id desc ";   
	$app_need_setup=sql_select($approval_status);
	$approval_need=$app_need_setup[0][csf("approval_need")];

    if (trim($company) !="") $sql_cond .= " and a.company_name='$company'";
	//echo $variable_subnon_acknowldgement.teee;die;
    if ($approval_need==1)
	{
		
		if($variable_subnon_acknowldgement==1)
		{
			$sql = " select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b,wo_service_acknowledgement_dtls c  
			where a.id=b.mst_id and a.entry_form = 484 and  b.id=c.wo_booking_dtls_id and a.is_approved<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1  and c.entry_form_id=558 and c.is_deleted=0 $sql_cond $category_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			union all
			select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.id=b.mst_id and b.ITEM_CATEGORY_ID = 114 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			order by wo_date desc";
		}
		else
		{
			$sql = " select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.id=b.mst_id and a.entry_form = 484 and a.is_approved<>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $category_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			union all
			select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.id=b.mst_id and b.ITEM_CATEGORY_ID = 114 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			order by wo_date desc";
		}
    }
    else if ($approval_need==2)
    {
		if($variable_subnon_acknowldgement==1)
		{
			$sql = " select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b ,wo_service_acknowledgement_dtls c 
			where a.id=b.mst_id and a.entry_form = 484 and  b.id=c.wo_booking_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1  and c.entry_form_id=558 and c.is_deleted=0 $sql_cond $category_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			union all
			select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			where a.id=b.mst_id and b.ITEM_CATEGORY_ID = 114 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
			group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			order by wo_date desc";
		}
		else
		{
			  $sql = " select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			  from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			  where a.id=b.mst_id and a.entry_form = 484 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $category_cond 
			  group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix
			  union all
			  select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			  from wo_non_order_info_mst a, wo_non_order_info_dtls b 
			  where a.id=b.mst_id and b.ITEM_CATEGORY_ID = 114 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
			  group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
			  order by wo_date desc";
			  //echo $sql;
		}
		
      
    }
	else
	{
		echo "<b> Please check Approval Necessity Setup Need->Yes Or No</b>";
		die;
	}
     
    //echo $sql;die;
    $result = sql_select($sql);
	//print_r($result);
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

    $arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$source);

    echo  create_list_view("list_view", "Company, WO Number, WO Date, Pay Mode, Supplier, Source", "150,150,120,120,150,110","900","250",0, $sql, "js_set_value", "wo_number,id", "", 1, "company_name,0,0,pay_mode,supplier_id,source", $arr , "company_name,wo_number,wo_date,pay_mode,supplier_id,source", "",'','0,0,3,0,0,0,0');
    exit();
}

if($action=="populate_data_from_search_popup")
{
    $sql = "select id, company_name, wo_date, supplier_id, attention, currency_id, delivery_date, source, pay_mode, ready_to_approved, is_approved, location_id, fixed_asset, asset_no,tenor from wo_non_order_info_mst where id='$data'";
    //echo $sql;die;

    $result = sql_select($sql);
    foreach($result as $resultRow)
    {
        echo "$('#cbo_company_id').val('".$resultRow[csf("company_name")]."');\n";
        echo "$('#cbo_location_name').val('".$resultRow[csf("location_id")]."');\n";
        
        echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("wo_date")])."');\n";
        echo "$('#cbo_currency_id').val('".$resultRow[csf("currency_id")]."');\n";
        echo "check_exchange_rate();\n";
        echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";

        //echo "fnc_load_supplier('".$resultRow[csf("pay_mode")]."');\n";
        echo "load_drop_down( 'requires/general_service_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier_name', 'supplier_td' );\n";
        echo "$('#cbo_supplier_company').val('".$resultRow[csf("supplier_id")]."');\n";

        echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
        echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
        echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
        echo "$('#cbo_ready_to_approved').val('".$resultRow[csf("ready_to_approved")]."');\n";
        echo "$('#cbo_fixed_asset').val('".$resultRow[csf("fixed_asset")]."');\n";
        echo "$('#txt_entry_no').val('".$resultRow[csf("asset_no")]."');\n";
        echo "is_text_field();\n";
        //echo "$('#cbo_inco_term').val('".$resultRow[csf("inco_term_id")]."');\n";
        echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";      
    }
    exit();
}

if($action=="check_conversion_rate") 
{ 
    $data=explode("**",$data);
    if($db_type==0)
    {
        $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
    }
    else
    {
        $conversion_date=change_date_format($data[1], "d-M-y", "-",1);
    }
    $currency_rate=set_conversion_rate( $data[0], $conversion_date );
    echo "1"."_".$currency_rate;
    exit(); 
}

if ($action == "show_searh_active_listview") 
{
    $ex_data = explode("_", $data);
    $new_conn=integration_params(3);
    //echo $new_conn.test;die;
    $company_location = return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0", "id", "location_name");
    $store_library      = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id='$ex_data[1]'", "id", "store_name");
    $asset_category_result=sql_select("SELECT ID, ASSET_TYPE_ID, ASSET_CATEGORY_NAME, STATUS_ACTIVE FROM LIB_FAM_ASSET_CATEGORY_TYPE WHERE IS_DELETED=0 ORDER BY ASSET_TYPE_ID,ID",'',$new_conn);
    //echo "<pre>";print_r($asset_category_result);die;
    $fams_asset_category_arr=array();
    foreach($asset_category_result as $row){
        $fams_asset_category_arr[$row['ASSET_TYPE_ID']][$row['ID']]=$row['ASSET_CATEGORY_NAME'];
    }
    unset($asset_category_result);
    
    if ( trim($ex_data[0]) == 0)
        $asset_number = "";
    else
        $asset_number = " and c.asset_no LIKE '%" . trim($ex_data[0]) . "'";
        
    if ($ex_data[1] == 0)
        $company_id = "";
    else
        $company_id = " and a.company_id='" . $ex_data[1] . "'";
        
    if ($ex_data[2] == 0)
        $location = "";
    else
        $location = " and a.location='" . $ex_data[2] . "'";
        
    if ($ex_data[3] == 0)
        $aseet_type = "";
    else
        $aseet_type = " and a.asset_type='" . $ex_data[3] . "'";
        
    if ($ex_data[4] == 0)
        $category = "";
    else
        $category = " and a.asset_category='" . $ex_data[4] . "'";
    
    $txt_date_from = $ex_data[5];
    $txt_date_to = $ex_data[6];
    
    if ( trim($ex_data[7]," ") == "")
        $entry_no_cond = "";
    else
        $entry_no_cond = " and a.entry_no LIKE '%" . trim($ex_data[7]) . "'";
    
    if ($ex_data[1] == 0) { echo "Please Company first"; die; }
    
    if ($db_type == 0) 
    {
        if ($txt_date_from != "" || $txt_date_to != "") {
            $tran_date = " and a.purchase_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        }
        $sql = "SELECT  a.id, a.entry_no, c.asset_no, a.location, a.asset_type, a.asset_category, a.store, a.purchase_date, a.qty  FROM fam_acquisition_mst a, fam_acquisition_sl_dtls c  WHERE a.id=c.mst_id AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $category $aseet_type $location $company_id $asset_number $entry_no_cond $tran_date order by a.id,c.asset_no";
    }
    
    if ($db_type == 2) {
        if ($txt_date_from != "" && $txt_date_to != "") {
            $tran_date = " and a.purchase_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
        }
        $sql = "SELECT  a.id, a.entry_no, c.asset_no, a.location, a.asset_type, a.asset_category, a.store, a.purchase_date, a.qty  FROM fam_acquisition_mst a, fam_acquisition_sl_dtls c  WHERE a.id=c.mst_id AND a.status_active=1 AND a.is_deleted=0  AND c.status_active=1 AND c.is_deleted=0 $category $aseet_type $location $company_id $asset_number $entry_no_cond $tran_date order by a.id,c.asset_no";
    }
    $prev_asset_no=return_library_array("select raw_issue_challan from inv_transaction where status_active=1 and transaction_type=2 and item_category in(".implode(",",array_flip($general_item_category)).") and raw_issue_challan is not null","raw_issue_challan","raw_issue_challan");
    $result = sql_select($sql,'',$new_conn);
    //echo "<pre>";print_r($result);die;
    ?>
    <table class="rpt_table" rules="all" width="978" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="150">Entry No</th>
                <th width="130">Asset No</th>
                <th width="150">Location</th>
                <th width="90">Type</th>
                <th width="90">Category</th>
                <th width="120">Store</th>
                <th width="90">Purchase Date</th>
                <th>Qty</th>
            </tr>
        </thead>
    </table> 
    <div style="max-height:300px; width:976px; overflow-y:scroll">
    <table class="rpt_table" id="list_view" rules="all" width="958" height="" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <? 
        foreach($result as $row)
        {
            if($prev_asset_no[$row[csf('entry_no')]]=="")
            {
                $asset_category = $fams_asset_category_arr[$row[csf('asset_type')]];
                $i++;
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                <tr onClick="js_set_value('<? echo $row[csf('entry_no')];?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor;?>">
                    <td width="50"><? echo $i; ?></td>
                    <td width="150" align="left"><p><? echo $row[csf('entry_no')];?></p></td>
                    <td width="130" align="left"><p><? echo $row[csf('asset_no')];?></p></td>
                    <td width="150" align="left"><p><? echo $company_location[$row[csf('location')]];?></p></td>
                    <td width="90" align="left"><p><? echo $asset_type[$row[csf('asset_type')]];?></p></td>
                    <td width="90" align="left"><p><? echo $asset_category[$row[csf('asset_category')]];?></p></td>
                    <td width="120" align="left"><p><? echo $store_library[$row[csf('store')]];?></p></td>
                    <td width="90" align="left"><p><? echo change_date_format($row[csf("purchase_date")], "dd-mm-yyyy", "-");?></p></td>
                    <td align="right"><p><? echo $row[csf('qty')];?></p></td>
                </tr>
                <?
            }
        }
        ?>
    </tbody>
    </table>
    </div>
    <?
    exit;
}

if($action=="show_dtls_listview_update")
{
    $sql = "SELECT b.id, b.mst_id, b.prod_mst_id, b.asset_no, b.receive_qty, b.rate, b.amount, a.wo_non_order_info_mst_id as service_wo_id, b.wo_non_order_info_dtls_id as dtls_id, b.bill_status from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    // echo $sql;
    $result = sql_select($sql);
    foreach ($result as $val) {
        if ($val[csf('prod_mst_id')] != '')
        $prod_id.=$val[csf('prod_mst_id')].',';

        if ($val[csf('service_wo_id')] != '')
        $service_wo_id.=$val[csf('service_wo_id')].',';

        if ($val[csf('dtls_id')] != '')
        $dtls_id.=$val[csf('dtls_id')].',';
    }

    $prod_ids=rtrim($prod_id,',');
 
    if ($prod_ids != ''){
        $sql_prod=sql_select("select id, item_description, item_category_id, item_group_id,item_sub_group_id,gmts_size,order_uom from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0");
        $prod_arr=array();
        foreach ($sql_prod as $val) {
           $prod_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
           $prod_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
           $prod_arr[$val[csf('id')]]['item_group_id']=$val[csf('item_group_id')];
           $prod_arr[$val[csf('id')]]['item_sub_group_id']=$val[csf('item_sub_group_id')];
           $prod_arr[$val[csf('id')]]['gmts_size']=$val[csf('gmts_size')];
           $prod_arr[$val[csf('id')]]['order_uom']=$val[csf('order_uom')];
        }
    }

    $dtls_ids=rtrim($dtls_id,',');
    $sql_prod=sql_select("select id, service_details as item_description, item_category_id from wo_non_order_info_dtls where id in($dtls_ids) and status_active=1 and is_deleted=0");
    $service_arr=array();
    foreach ($sql_prod as $val) {
       $service_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
       $service_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
    }

    $service_wo_ids=rtrim($service_wo_id,',');
    if ($service_wo_ids != '')
    {
        $sql_wo=sql_select("select b.id,a.id as wo_id, a.company_name, b.item_id, b.supplier_order_quantity, b.rate, b.amount,  b.remarks, b.service_for, b.service_details, a.asset_no, b.uom, b.req_quantity, a.currency_id, a.pay_mode, b.mst_id from wo_non_order_info_mst a, wo_non_order_info_dtls b where  a.id=b.mst_id and a.entry_form=484 and a.id in ($service_wo_ids) and b.status_active=1 and b.is_deleted=0 order by b.id");
        $wo_arr=array();
        $wo_mst_arr=array();
        foreach($sql_wo as $val) {
           $wo_mst_arr[$val[csf('mst_id')]][$val[csf('item_id')]][$val[csf('id')]]['service_for']=$val[csf('service_for')];
           $wo_mst_arr[$val[csf('mst_id')]][$val[csf('item_id')]][$val[csf('id')]]['service_details']=$val[csf('service_details')];
           $wo_mst_arr[$val[csf('mst_id')]][$val[csf('item_id')]][$val[csf('id')]]['remarks']=$val[csf('remarks')];
          
           $wo_arr[$val[csf('wo_id')]][$val[csf('id')]]['currency_id']=$val[csf('currency_id')];
           $wo_arr[$val[csf('wo_id')]][$val[csf('id')]]['pay_mode']=$val[csf('pay_mode')];
         
        }
    }
   
    $i=1;
    foreach($result as $val)
    {
        if($prod_arr[$val[csf('prod_mst_id')]]['item_description']!=""){
            $item_description= $prod_arr[$val[csf('prod_mst_id')]]['item_description'];
        }else{
            $item_description= $service_arr[$val[csf('dtls_id')]]['item_description'];
        }

        if($prod_arr[$val[csf('prod_mst_id')]]['item_category_id']!=""){
            $itaitem_category_id =$prod_arr[$val[csf('prod_mst_id')]]['item_category_id'];
        }else{
            $itaitem_category_id= $service_arr[$val[csf('dtls_id')]]['item_category_id'];
        }

        ?>
        
        <tr class="general" id="tr_<?= $i; ?>">
            <td>
                <input  type="checkbox" name="check[]" id="check_<?= $i; ?>" class="text_boxes" style="width:30px" <? if($val[csf("bill_status")]==1) echo "checked";?> onClick="check_bill_status(<?= $i; ?>)" value="<? echo $val[csf("bill_status")];?>">    
            </td>
            <td>
                <?                   
                    echo create_drop_down( "cboServiceFor_".$i, 90,$service_for_arr, "", 1, "-- Select --",$wo_mst_arr[$val[csf('service_wo_id')]][$val[csf('prod_mst_id')]][$val[csf('dtls_id')]]['service_for'], "", 1, "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
                ?>
            </td>
            <td align="center">
                <input name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:120px" value="<? echo $wo_mst_arr[$val[csf('service_wo_id')]][$val[csf('prod_mst_id')]][$val[csf('dtls_id')]]['service_details'];?>" disabled/>                  
            </td>
            <td align="center">
                <input name="txtProduct_id[]" id="txtProduct_id<?= $i; ?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("prod_mst_id")];?>" disabled/>                  
            </td>
            <td align="center">
                <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i; ?>)" placeholder="Double Click To Search" value="<?=$item_description?>" disabled/>
                <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i; ?> " value="<? echo $val[csf("prod_mst_id")];?>"/>
                <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i; ?>" value="<? echo $i;?>" />
                <input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id_<? echo $i; ?>" value="<? echo $val[csf("id")];?>" />
                <input type="hidden" name="txt_service_dtls[]" id="txt_service_dtls_id_<? echo $i; ?>" value="<? echo $val[csf("dtls_id")];?>" />                
            </td>
            <td align="center">
                <? echo create_drop_down( "cboItemCategory_".$i, 120, $item_category,"", 1, "-- Select --", $itaitem_category_id, "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i ); ?>
            </td>
            <td align="center">
                <? echo create_drop_down( "cboItemGroup_".$i,100,"select id,item_name  from lib_item_group","id,item_name", 1,"Select",$prod_arr[$val[csf('prod_mst_id')]]['item_group_id'], "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i ); ?>
            </td>
            <td>
                <input type="text" name="txtAssetNo[]" id="txtAssetNo_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="search_asset(<?= $i; ?>)" placeholder="Double Click To Search" value="<? echo $val[csf("asset_no")];?>" />
            </td>
            <td>
                <input type="text" name="txtqnty[]" id="txtqnty_<?= $i; ?>" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_amount(1)" value="<? echo $val[csf("receive_qty")];?>" disabled/>
            </td>
            <td>
                <input type="text" name="txtrate[]" id="txtrate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_amount(1)" value="<? echo $val[csf("rate")]; ?>" />
            </td>
            <td><input type="text" name="txtamount[]" id="txtamount_<?= $i; ?>" class="text_boxes_numeric" style="width:90px;" readonly value="<? echo $val[csf("amount")]; $total+=$val[csf("amount")];?>"/></td>         
          
            <td align="center">
                <?                
                    echo create_drop_down( "cbo_pay_mode_".$i, 150, $pay_mode, "", 1, "-- Select --",$wo_arr[$val[csf('service_wo_id')]][$val[csf('dtls_id')]]['pay_mode'], "", 1, "", "", "", "", "", "", "cbo_pay_mode[]", "cbo_pay_mode_".$i );
                ?> 
            </td>
            <td><input type="text" name="txtremarks[]" id="txtremarks_<?= $i; ?>" class="text_boxes" style="width:120px;" value="<? echo  $wo_mst_arr[$val[csf('service_wo_id')]][$val[csf('prod_mst_id')]][$val[csf('dtls_id')]]['remarks'];?>" disabled/></td>
        </tr>      
        <?
        $i++;        
    }    
    ?>
    <tr class="general">
        <td colspan="9"></td>
        <td align="right"><input type="text"  name="txttotal[]" id="txttotal_<?//= $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo "Total"?>" disabled/></td>
        <td><input type="text" name="txttotalamount[]" id="txttotalamount_<?//= $i; ?>" class="text_boxes_numeric" style="width:90px;" readonly value="<? echo $total;?>"/></td>
        <td></td>
        <td></td>
    </tr>
    <?
    exit();
}

if($action=="show_service_wo_dtls_listview")
{
    $data=explode("_",$data);
    $wo_qty_data=sql_select("SELECT b.id, b.mst_id, b.prod_mst_id, b.asset_no, b.receive_qty, b.rate, b.amount,a.wo_non_order_info_mst_id as service_wo_id, b.wo_non_order_info_dtls_id as dtls_id, b.bill_status from subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b where a.id=b.mst_id and a.wo_non_order_info_mst_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

    foreach($wo_qty_data as $val)
	{       
        $wo_wise_qnty[$val[csf('service_wo_id')]][$val[csf('dtls_id')]]['qnty']+=$val[csf('receive_qty')];
    }
	
	$variable_subnon_acknowldgement=return_field_value("dyeing_fin_bill","variable_settings_subcon","company_id=$data[2]  and variable_list=20 and status_active=1 and is_deleted=0  ");
	if($variable_subnon_acknowldgement==1)
	{
		$sql="SELECT b.id as dtls_id, a.id as mst_id, a.company_name, b.item_id, c.ackn_qty as supplier_order_quantity, b.rate, b.amount, b.remarks, b.service_for, b.service_details, a.asset_no, b.uom, b.req_quantity, a.currency_id, a.pay_mode, a.net_wo_amount, b.item_category_id ,
        b.requisition_dtls_id  
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, wo_service_acknowledgement_dtls c 
		where a.id=$data[0] and a.id=b.mst_id and a.entry_form=484 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.id=c.wo_booking_dtls_id and c.entry_form_id=558 and c.is_deleted=0 
		union all
		select b.id as dtls_id, a.id as mst_id, a.company_name, b.item_id, b.supplier_order_quantity, b.rate, b.amount, b.remarks, b.service_for, b.service_details, a.asset_no, b.uom, b.req_quantity, a.currency_id, a.pay_mode, a.net_wo_amount, b.item_category_id   ,
        b.requisition_dtls_id 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=$data[0] and a.id=b.mst_id and b.ITEM_CATEGORY_ID = 114 and b.status_active=1 and b.is_deleted=0 
		order by dtls_id";
	}
	else
	{
		$sql="SELECT b.id as dtls_id, a.id as mst_id, a.company_name, b.item_id, b.supplier_order_quantity, b.rate, b.amount, b.remarks, b.service_for, b.service_details, a.asset_no, b.uom, b.req_quantity, a.currency_id, a.pay_mode, a.net_wo_amount, b.item_category_id,
        b.requisition_dtls_id  
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=$data[0] and a.id=b.mst_id and a.entry_form=484 and b.status_active=1 and b.is_deleted=0
		union all
		select b.id as dtls_id, a.id as mst_id, a.company_name, b.item_id, b.supplier_order_quantity, b.rate, b.amount, b.remarks, b.service_for, b.service_details, a.asset_no, b.uom, b.req_quantity, a.currency_id, a.pay_mode, a.net_wo_amount, b.item_category_id ,
        b.requisition_dtls_id 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=$data[0] and a.id=b.mst_id and b.ITEM_CATEGORY_ID = 114 and b.status_active=1 and b.is_deleted=0 
		order by dtls_id";
		/*union all
		select a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=b.mst_id and b.ITEM_CATEGORY_ID = 114 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
		group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode,a.wo_number_prefix*/
	}
   // echo $sql ;
    $result = sql_select($sql);
   
    foreach ($result as $val) 
	{
        if ($val[csf('item_id')] != '')
        $prod_id.=$val[csf('item_id')].',';

        if ($val[csf('dtls_id')] != '')
        $dtls_id.=$val[csf('dtls_id')].',';
    }
    $allIdArr = array();
    foreach($result as $v )
    {
        $allIdArr[$v['REQUISITION_DTLS_ID']] = $v['REQUISITION_DTLS_ID'];
    }
    $newAllId = implode(',', $allIdArr);
   
    $ServiceArrNew = array ();
    $newSqlForServic = "SELECT ID,SERVICESFOR_LIB_ID FROM  INV_PURCHASE_REQUISITION_DTLS WHERE ID IN($newAllId) ";
    foreach(sql_select($newSqlForServic) as $val)
    {
        $ServiceArrNew[$val['ID']]['SERVICES_NO'] = $val['SERVICESFOR_LIB_ID'];
      
    }
   
    
    $prod_ids=rtrim($prod_id,',');
    if ($prod_ids != '')
	{
        $sql_prod=sql_select("SELECT id, item_description, item_category_id, item_group_id, item_sub_group_id, gmts_size, order_uom from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0");
        $prod_arr=array();
        foreach ($sql_prod as $val) {
           $prod_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
           $prod_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
           $prod_arr[$val[csf('id')]]['item_group_id']=$val[csf('item_group_id')];
           $prod_arr[$val[csf('id')]]['item_sub_group_id']=$val[csf('item_sub_group_id')];
           $prod_arr[$val[csf('id')]]['gmts_size']=$val[csf('gmts_size')];
           $prod_arr[$val[csf('id')]]['order_uom']=$val[csf('order_uom')];
        }
    }

    $dtls_ids=rtrim($dtls_id,',');
    $sql_prod=sql_select("select id, service_details as item_description, item_category_id from wo_non_order_info_dtls where id in($dtls_ids) and status_active=1 and is_deleted=0");
    $service_arr=array();
    foreach ($sql_prod as $val) {
       $service_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
       $service_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
    }

    $i=1;            
    foreach($result as $val)
    {
        if($prod_arr[$val[csf('item_id')]]['item_description']!=""){
            $item_description= $prod_arr[$val[csf('item_id')]]['item_description'];
        }else{
            $item_description= $service_arr[$val[csf('dtls_id')]]['item_description'];
        }

        if ($wo_wise_qnty[$val[csf('mst_id')]][$val[csf('dtls_id')]]['qnty'] < $val[csf('supplier_order_quantity')]) 
        { 
            $qnty=$val[csf('supplier_order_quantity')]-$wo_wise_qnty[$val[csf('mst_id')]][$val[csf('dtls_id')]]['qnty'];
            ?>
            <tr class="general" id="tr_<?= $i; ?>">        
                <td>
                    <input  type="checkbox" name="check[]" id="check_<?= $i; ?>" onClick="check_bill_status(<?= $i; ?>)" class="text_boxes" style="width:30px" value="0">    
                </td>
                <td>
                    <?                    
                        echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$ServiceArrNew[$val['REQUISITION_DTLS_ID']]['SERVICES_NO'], "", 1, "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
                    ?>
                </td>
                <td align="center">
                    <input name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("service_details")];?>" disabled/>                  
                </td>
                <td align="center">
                    <input name="txtProduct_id[]" id="txtProduct_id<?= $i; ?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("item_id")];?>" disabled/>                  
                </td>

                <td align="center">
                    <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i; ?>)" placeholder="Double Click To Search" value="<? echo  $item_description;?>" disabled/>
                    <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i; ?> " value="<? echo $val[csf("item_id")];?>"/>
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i; ?>" value="<? echo $i;?>" />
                    <input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id_<? echo $i; ?>" value="" />
                    <input type="hidden" name="txt_service_dtls[]" id="txt_service_dtls_id_<? echo $i; ?>" value="<? echo $val[csf("dtls_id")];?>" />
                    <input type="hidden" name="txt_balance[]" id="txt_balance_<? echo $i; ?>" value="<? echo $qnty;?>" />
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemCategory_".$i, 120, $item_category,"", 1, "-- Select --", $val[csf('item_category_id')], "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i ); ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemGroup_".$i,100,"select id,item_name  from lib_item_group","id,item_name", 1,"Select",$prod_arr[$val[csf('item_id')]]['item_group_id'], "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i ); ?>
                </td>
                <td>
                    <input type="text" name="txtAssetNo[]" id="txtAssetNo_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="search_asset(<?= $i; ?>)" placeholder="Double Click To Search" value="<? echo $val[csf("asset_no")];?>" />
                </td>
                <td title="Balance Qnty">
                    <input type="text" name="txtqnty[]" id="txtqnty_<?= $i; ?>" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_amount(1)"  value="<? echo $qnty;?>" disabled/>
                </td>
                <td>
                    <input type="text" name="txtrate[]" id="txtrate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_amount(1)" value="<? echo $val[csf("rate")]; ?>" disabled />
                </td>
                <td><input type="text" name="txtamount[]" id="txtamount_<?= $i; ?>" class="text_boxes_numeric" style="width:90px;" readonly value="<? echo $val[csf("rate")]*$qnty; $total+=$val[csf("rate")]*$qnty;//$val[csf("amount")];?>" disabled/></td>
                <td align="center">
                    <?
                        echo create_drop_down( "cbo_pay_mode_".$i, 150, $pay_mode, "", 1, "-- Select --",$val[csf('pay_mode')], "", 1, "", "", "", "", "", "", "cbo_pay_mode[]", "cbo_pay_mode_".$i );
                    ?>
                </td>
                <td><input type="text" name="txtremarks[]" id="txtremarks_<?= $i; ?>" class="text_boxes" style="width:120px;" value="<? echo $val[csf("remarks")];?>" disabled/></td>
            </tr>
            <?
            $i++;
        }
    }
    ?>
    <tr class="general">
        <td colspan="9"></td>
        <td  align="right"><input type="text"  name="txttotal[]" id="txttotal_<?//= $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo "Total"?>" disabled/></td>
        <td><input type="text" name="txttotalamount[]" id="txttotalamount_<?//= $i; ?>" class="text_boxes_numeric" style="width:90px;" readonly value="<? echo $total;?>"/></td>
        <td></td>
        <td></td>
    </tr>
    <?
    exit();
}

if ($action == "search_asset_entry") 
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode, 1);
    extract($_REQUEST);
    $new_conn=integration_params(3);
    $asset_type = return_library_array("select id, asset_type, asset_type_rename from lib_fam_asset_type where status_active =1 and is_deleted=0 order by id", "id", "asset_type_rename",$new_conn);
    //echo "<pre>";print_r($asset_type);die;
    ?>
    <script>
        var companyName= <? echo $cbo_company_name ?>;
        function js_set_value(id) 
        {
            document.getElementById('hidden_system_number').value = id;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">
                <table width="1070" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th width="170" class="must_entry_caption">Company Name</th>
                            <th width="170">Location</th>
                            <th width="110">Asset Type</th>
                            <th width="170">Category</th>
                            <th width="80">Entry No</th>
                            <th width="80">Asset No</th> 
                            <th width="210" align="center" >Date Range</th>
                            <th width="80"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?
                                echo create_drop_down("cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_location_asetpopup', 'src_location_td');", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td id="src_location_td">
                                <?
                                echo create_drop_down("cbo_location", 170, $blank_array, "", 1, "-- Select Location --", $selected, "", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_aseet_type", 110, $asset_type, "", 1, "--- Select ---", $selected, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_category', 'src_category_td' );", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td id="src_category_td">
                                <?
                                echo create_drop_down("cbo_category", 170, $blank_array, "", 1, "--- Select ---", $selected, "", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_entry_no" id="txt_entry_no" style="width:80px;" class="text_boxes">
                            </td>
                            <td>
                                <input type="text" name="asset_number" id="asset_number" style="width:80px;" class="text_boxes">
                            </td>
                            <td align="">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:66px" placeholder="From" readonly/>-
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:66px" placeholder="To" readonly/>
                            </td>  

                            <td align="center">
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('asset_number').value + '_' + document.getElementById('cbo_company_name').value + '_' + document.getElementById('cbo_location').value + '_' + document.getElementById('cbo_aseet_type').value + '_' + document.getElementById('cbo_category').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' + document.getElementById('txt_entry_no').value, 'show_searh_active_listview', 'searh_list_view', 'general_service_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />        
                            </td>
                        </tr>
                        <tr>                  
                            <td align="center" height="40" valign="middle" colspan="8">
                                <?php echo load_month_buttons(1); ?>                               
                                <input type="hidden" id="hidden_system_number" value="" />                               
                            </td>
                        </tr>  
                    </tbody>
                </table> 
            </form>
            <div align="center" valign="top" id="searh_list_view"> </div>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        //if(companyName>0)  load_drop_down( 'asset_acquisition_unite_price_change_controller',companyName, 'load_drop_down_location', 'src_location_td');
    </script>
    </html>
    <?php
}

if($action=="general_service_bill_entry_print")
{
    extract($_REQUEST);
	//echo $data;
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r($data);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
    $item_group_arr=return_library_array("select id,item_name  from lib_item_group","id","item_name" );

	$sql_mst="SELECT id, location_id, bill_no, bill_date, supplier_id,currency_id,party_bill_no,service_wo_num,remarks from subcon_outbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:100%;" align="center">
        <table width="880" cellspacing="0" align="center" border="0">
            <tr>
                <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <?
                        $location=$data[3];
                         
                        $nameArray=sql_select( "SELECT address from lib_location where company_id=$data[0] and status_active=1 and is_deleted=0 and id=$location"); 
                        foreach ($nameArray as $result)
                        { 
                            ?>
                                <? echo $result[csf('address')]; ?> 
                            <!-- Level No: <? //echo $result[csf('level_no')]?>
                            Road No: <? //echo $result[csf('road_no')]; ?> 
                            Block No: <? //echo $result[csf('block_no')];?> 
                            City No: <? //echo $result[csf('city')];?> 
                            Zip Code: <? //echo $result[csf('zip_code')]; ?> 
                            Province No: <?php //echo $result[csf('province')];?> 
                            Country: <? //echo $country_arr[$result[csf('country_id')]]; ?><br>  -->
                            <!--Email Address: <? //echo $result[csf('email')];?> 
                            Website No: <? //echo $result[csf('website')]; ?> --> <br> 
                            <?
                        }
                    ?> 
                </td>
            </tr>           
    	    <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[4]; ?></strong></u></td>
            </tr>
            <tr>         
                <td width="140"><strong>Supplier:</strong></td><td  width="250px"style="font-size:15px"><p><? echo $party_library[$dataArray[0][csf('supplier_id')]];?></p></td>

                <td width="130"><strong><p>Bill No :<p></strong></td> <td width="175" ><p><? echo $dataArray [0][csf('bill_no')]; ?></p></td>
                <td width="130"><strong><p>Bill Date: <p></strong></td><td width="175px"><p> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></p></td>  
            
                <td width="130"><strong><p>WO No: </p></strong></td><td width="175px"><p> <? echo $dataArray[0][csf('service_wo_num')] ?></p></td>                   
            </tr>
            <tr>
                <td width="130"><strong><p>Remarks :</p></strong></td><td  width="175" style="font-size:15px;"><p><?echo $dataArray[0][csf('remarks')]?></p></td>
                <td width="175"><p><strong>Party Bill No: </strong></p></td><td width="175px"></p><? echo $dataArray[0][csf('party_bill_no')] ?></p></td>
                <td width="130"><strong><p>Currency: <p></strong></td><td width="175px"><p><?echo  $currency[$dataArray[0][csf('currency_id')]];?></p></td>                    
            </tr>            
        </table>
        <br>
	    <div style="width:100%;" align="center">
            <table align="center" cellspacing="0" width="880"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="60" align="center">Service For</th>
                    <th width="65" align="center">Service Category</th>
                     
                    <th width="70" align="center">Service Details</th>
                    <th width="70" align="center">Item Category</th> 
                    <th width="120" align="center">Item Group</th>
                    <th width="120" align="center">Asset No</th>
                    <th width="105" align="center">Qnty</th>
                    <th width="50" align="center">Service UOM</th>
                    <th width="50" align="center">Rate</th>
                    <th width="60" align="center">Amount</th>
                    <th width="60" align="center">Pay Mode</th>
                    <th width="100" align="center">Remarks</th>
                </thead>
		        <?
                $i=1;
                $sql="SELECT b.id, b.mst_id, b.prod_mst_id, b.asset_no, b.receive_qty,b.rate, b.amount,a.remarks,a.pay_mode,a.wo_non_order_info_mst_id as service_wo_id,b.wo_non_order_info_dtls_id as dtls_id,b.bill_status from subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b where a.id=b.mst_id and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $sql;
		        $sql_result =sql_select($sql);
                foreach ($sql_result as $val) {
                    if ($val[csf('prod_mst_id')] != '')
                    $prod_id.=$val[csf('prod_mst_id')].',';
            
                    if ($val[csf('service_wo_id')] != '')
                    $service_wo_id.=$val[csf('service_wo_id')].',';

                    if ($val[csf('dtls_id')] != '')
                    $dtls_id.=$val[csf('dtls_id')].',';
                }
    
                $prod_ids=rtrim($prod_id,',');
                if ($prod_ids != '')
                {
                    $sql_prod=sql_select("SELECT id, item_description, item_category_id, item_group_id,item_sub_group_id,gmts_size,order_uom from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0");
                    $prod_arr=array();
                    foreach ($sql_prod as $val) {
                    $prod_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
                    $prod_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
                    $prod_arr[$val[csf('id')]]['item_group_id']=$val[csf('item_group_id')];
                    $prod_arr[$val[csf('id')]]['item_sub_group_id']=$val[csf('item_sub_group_id')];
                    $prod_arr[$val[csf('id')]]['gmts_size']=$val[csf('gmts_size')];
                    $prod_arr[$val[csf('id')]]['order_uom']=$val[csf('order_uom')];
                    }
                }    

                $dtls_ids=rtrim($dtls_id,',');
                $sql_prod=sql_select("select id, service_details as item_description, item_category_id from wo_non_order_info_dtls where id in($dtls_ids) and status_active=1 and is_deleted=0");
                $service_arr=array();
                foreach ($sql_prod as $val) {
                   $service_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
                   $service_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
                }
    
                $service_wo_ids=rtrim($service_wo_id,',');
                if ($service_wo_ids != '')
                {
            
                
                    $sql_wo=sql_select("SELECT b.id,a.id as wo_id, a.company_name, b.item_id, b.supplier_order_quantity, b.rate, b.amount,  b.remarks, b.service_for, b.service_details,a.asset_no,b.uom,b.req_quantity,a.currency_id,a.pay_mode,b.mst_id from wo_non_order_info_mst a, wo_non_order_info_dtls b where  a.id=b.mst_id and a.entry_form=484 and a.id in ($service_wo_ids) and b.status_active=1 and b.is_deleted=0 order by b.id");                     
                    $wo_arr=array();
                    $wo_mst_arr=array();
                    foreach($sql_wo as $val) {
                    $wo_mst_arr[$val[csf('mst_id')]][$val[csf('item_id')]][$val[csf('id')]]['service_for']=$val[csf('service_for')];
                    $wo_mst_arr[$val[csf('mst_id')]][$val[csf('item_id')]][$val[csf('id')]]['service_details']=$val[csf('service_details')];
                    $wo_mst_arr[$val[csf('mst_id')]][$val[csf('item_id')]][$val[csf('id')]]['uom']=$val[csf('uom')];
                     
                    $wo_mst_arr[$val[csf('mst_id')]][$val[csf('item_id')]][$val[csf('id')]]['remarks']=$val[csf('remarks')];
                    
                    $wo_arr[$val[csf('wo_id')]][$val[csf('id')]]['currency_id']=$val[csf('currency_id')];
                    $wo_arr[$val[csf('wo_id')]][$val[csf('id')]]['pay_mode']=$val[csf('pay_mode')];

                    
                    }
                }      
 
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";		
                    
                    if($prod_arr[$row[csf('prod_mst_id')]]['item_description']!=""){
                        $item_description= $prod_arr[$row[csf('prod_mst_id')]]['item_description'];
                    }else{
                        $item_description= $service_arr[$row[csf('dtls_id')]]['item_description'];
                    }
            
                    if($prod_arr[$row[csf('prod_mst_id')]]['item_category_id']!=""){
                        $itaitem_category_id =$prod_arr[$row[csf('prod_mst_id')]]['item_category_id'];
                    }else{
                        $itaitem_category_id= $service_arr[$row[csf('dtls_id')]]['item_category_id'];
                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"> 
                        <td><? echo $i; ?></td>
                        <td><p><?echo  $service_for_arr[$wo_mst_arr[$row[csf('service_wo_id')]][$row[csf('prod_mst_id')]][$row[csf('dtls_id')]]['service_for']]; ?></p></td>
                        <!--<td><p><?//echo $wo_mst_arr[$row[csf('service_wo_id')]][$row[csf('prod_mst_id')]][$row[csf('dtls_id')]]['service_details']; ?></p></td> 
                        <td><p><? //echo  $item_description; ?></p></td>
                        -->

                        <?
                            $service_name= $wo_mst_arr[$row[csf('service_wo_id')]][$row[csf('prod_mst_id')]][$row[csf('dtls_id')]]['service_details'];
                            $category_sql="select service_category from lib_service_category where service_name='$service_name'";
                            $category_result=sql_select($category_sql);
                            $category_result=$category_result[0]['SERVICE_CATEGORY'];
                        ?>

                        <td><p><? echo $category_result ; ?></p></td>

                        <td><p><?echo $wo_mst_arr[$row[csf('service_wo_id')]][$row[csf('prod_mst_id')]][$row[csf('dtls_id')]]['service_details']; ?></p></td> 
                        
                    
                        <td><p><?echo $item_category[$itaitem_category_id]; ?></p></td>
                        <td><p><?echo  $item_group_arr[$prod_arr[$row[csf('prod_mst_id')]]['item_group_id']] ;  ?></p></td>
                        <td><p><?echo $row[csf('asset_no')] ;?></p></td>                    
                        <td align="right"><p><? echo $row[csf('receive_qty')]; $tot_receive_qty+=$row[csf('receive_qty')]; ?>&nbsp;</p></td>
                        <td align="right"><p><? echo $service_uom_arr[$wo_mst_arr[$row[csf('service_wo_id')]][$row[csf('prod_mst_id')]][$row[csf('dtls_id')]]['uom']]; ?>&nbsp;</p></td>
                        <td align="right"><p><? echo number_format($row[csf('rate')],4,'.',''); ?>&nbsp;</p></td>                        
                        <td align="right"><p><? echo number_format($row[csf('amount')],4,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                        <td><p><?echo $pay_mode[$wo_arr[$row[csf('service_wo_id')]][$row[csf('dtls_id')]]['pay_mode']];?></p></td>   
                        <td><p><? echo $wo_mst_arr[$row[csf('service_wo_id')]][$row[csf('prod_mst_id')]][$row[csf('dtls_id')]]['remarks']; ?></p></td> 
                        
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right" colspan="9"><strong>Total Amount</strong></td>
                    <!-- <td align="right"><? echo $tot_receive_qty; ?>&nbsp;</td>      -->
                    <td align="right"><? echo $format_total_amount=number_format($total_amount,4,'.',''); ?>&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    
                </tr>
                <tr>
                    <td colspan="14" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
                </tr>
            </table>	   
	        <br>
            <?
                echo signature_table(275, $data[0], "880px","",1);
            ?>
        </div>
   </div>
   <?
}

?>