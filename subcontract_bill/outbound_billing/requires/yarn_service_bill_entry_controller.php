<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
//print_r ($data[0]);
$action=$_REQUEST['action'];

$bill_process_id= 10;   // a hardcoded unique number only for this bill_entry
$entry_form = 448;      // the entry form array index of this page

if ($action=='load_drop_down_location')
{
    echo create_drop_down('cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 order by location_name", 'id,location_name', 1, '--Select Location--', $selected);
    exit();  
}

else if ($action=='load_drop_down_supplier_name')
{
    echo create_drop_down('cbo_supplier_company', 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data' and sup.id in (select supplier_id from  lib_supplier_party_type where party_type in (9,21)) order by supplier_name", 'id,supplier_name', 1, '-- Select supplier --', $selected);
    exit();
}

else if ($action=='outside_bill_popup')
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
                        echo create_drop_down('cbo_company_id', 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $ex_data[0], "load_drop_down( 'yarn_service_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td' );",1 );
                    ?>
                </td>
                <td width="140" id="supplier_td">
                    <?php
                        echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$ex_data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=21) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $ex_data[1], "","","","","","",5 );
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
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'outside_yarn_service_bill_list_view', 'search_div', 'yarn_service_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

else if ($action=='outside_yarn_service_bill_list_view')
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
    where process_id=$bill_process_id and status_active=1 $company_cond $supplier_cond $trans_date_cond $bill_id_cond
    order by id desc";

    // echo $sql;
    
    echo create_list_view('list_view', 'Bill No,Year,Party Bill No,Bill Date,Supplier,Bill For', '70,70,100,100,120,100', '600', '250', 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0,0,supplier_id,bill_for', $arr, 'prefix_no_num,year,party_bill_no,bill_date,supplier_id,bill_for', 'yarn_service_bill_entry_controller', '', '0,0,0,3,0,0');
    exit(); 
}

else if ($action=='load_php_data_to_form_outside_bill')
{
    $sql="select min(receive_date) as min_date, max(receive_date) as max_date
    from subcon_outbound_bill_dtls
    where mst_id='$data' and status_active=1 and is_deleted=0
    group by mst_id";
    
    $sql_result_arr =sql_select($sql);
    $mindate='';  $maxdate='';
    $mindate=$sql_result_arr[0][csf('min_date')];
    $maxdate=$sql_result_arr[0][csf('max_date')];
    
    $nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, supplier_id, bill_for, party_bill_no, manual_challan, is_posted_account
        from subcon_outbound_bill_mst
        where id='$data'");
    foreach ($nameArray as $row)
    {   
        echo "document.getElementById('txt_bill_no').value                  = '".$row[csf("bill_no")]."';\n";
        echo "document.getElementById('cbo_company_id').value               = '".$row[csf("company_id")]."';\n";
        echo "load_drop_down( 'requires/yarn_service_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
        echo "document.getElementById('cbo_location_name').value            = '".$row[csf("location_id")]."';\n"; 
        echo "document.getElementById('txt_bill_date').value                = '".change_date_format($row[csf("bill_date")])."';\n";   
        echo "load_drop_down( 'requires/yarn_service_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier_name', 'supplier_td' );\n";
        echo "document.getElementById('cbo_supplier_company').value         = '".$row[csf("supplier_id")]."';\n"; 
        echo "document.getElementById('cbo_bill_for').value                 = '".$row[csf("bill_for")]."';\n"; 
        echo "document.getElementById('txt_party_bill').value               = '".$row[csf("party_bill_no")]."';\n"; 
        echo "document.getElementById('txt_bill_from_date').value           = '".change_date_format($mindate)."';\n";  
        echo "document.getElementById('txt_bill_to_date').value             = '".change_date_format($maxdate)."';\n"; 
        echo "document.getElementById('txt_manual_challan').value           = '".$row[csf("manual_challan")]."';\n";
        echo "document.getElementById('hidden_acc_integ').value             = '".$row[csf("is_posted_account")]."';\n";
        
        
        if($row[csf("is_posted_account")]==1)
        {
            echo "$('#accounting_integration_div').text('Already Posted In Accounts.');\n"; 
        }
        else 
        {
            echo "$('#accounting_integration_div').text('');\n"; 
        }
        
        echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_supplier_company*cbo_bill_for',1);\n";
        echo "document.getElementById('update_id').value                    = '".$row[csf("id")]."';\n";
    }
    exit();
}

else if ($action=='outside_yarn_service_info_list_view')
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
                        <?php
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
                            <?php
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

else if($action=="remarks_popup")
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

else if ($action=='save_update_delete')
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 

    if ($operation==0)   // Insert Here========================================================================================delivery_id
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
        
        $new_bill_no=explode("*",return_mrr_number( str_replace("'", "",$cbo_company_id), '', 'YSB', date("Y",time()), 5, "select prefix_no, prefix_no_num from subcon_outbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc", 'prefix_no', 'prefix_no_num' ));

        //  and process_id=$bill_process_id
        //  echo "10**";print_r($new_bill_no);die;
        //  print_r($new_bill_no);die;
        
        if(str_replace("'", '', $update_id)=="")
        {
            $id=return_next_id('id', 'subcon_outbound_bill_mst', 1 );
            $field_array='id, entry_form, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, bill_for, manual_challan, party_bill_no, process_id, inserted_by, insert_date';
            $data_array="(".$id.",".$entry_form.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_supplier_company.",".$cbo_bill_for.",".$txt_manual_challan.",".$txt_party_bill.",".$bill_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
            // echo "10**INSERT INTO subcon_outbound_bill_mst (".$field_array.") VALUES ".$data_array;die; 
            $rID=sql_insert('subcon_outbound_bill_mst', $field_array, $data_array, 1);
            $return_no=$new_bill_no[0]; 
        }
        else
        {
            $id=str_replace("'",'',$update_id);
            $field_array='location_id*bill_date*supplier_id*bill_for*manual_challan*party_bill_no*updated_by*update_date';
            $data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$cbo_bill_for."*".$txt_manual_challan."*".$txt_party_bill."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
            $rID=sql_update('subcon_outbound_bill_mst', $field_array, $data_array, 'id', $update_id, 0);
            $return_no=str_replace("'",'',$txt_bill_no);
        }
            
        $id1=return_next_id('id', 'subcon_outbound_bill_dtls', 1);
        //$field_array1 ="id, mst_id, receive_id, receive_date ,challan_no, order_id, item_id, febric_description_id, batch_id, dia_width_type, color_id, body_part_id, roll_no, wo_num_id, receive_qty, uom, rate, amount, remarks, process_id, inserted_by, insert_date, currency_id,sub_process_id,source";
        $field_array1 = 'id, mst_id, receive_id, receive_date, mrr_no, challan_no, job_no, item_id, wo_num_id, uom, receive_qty, rate, amount, remarks, process_id, currency_id, no_of_bags, cone_per_bag, domestic_currency, inserted_by, insert_date';
          
        $add_comma=0;
        $rID2=1;
        for($i=1; $i<=$tot_row; $i++)
        {
            $receive_date='txtReceiveDate_'.$i;
            $mrr_no='txtMrrNo_'.$i;
            $challen_no='txtChallenNo_'.$i;
            //$orderid="orderNoId_".$i;
            $order_no='txtOrderNo_'.$i;            
            
            //$style_name="txtStyleName_".$i;
            //$party_name="txtPartyName_".$i;
            $num_of_bag='txtNumberBag_'.$i;
            $num_of_cone='txtNumberCone_'.$i;
            $item_id='itemid_'.$i;
            // $colorid='colorId_'.$i;
            $wo_number='textWoNumId_'.$i;
            $cbo_uom='cboUom_'.$i;
            $yarn_qnty='txtYarnQty_'.$i;
            $rate='txtRate_'.$i;
            $amount='txtAmount_'.$i;
            $remarks='txtRemarks_'.$i;
            $recive_id='reciveId_'.$i;
            $updateid_dtls='updateIdDtls_'.$i;
            $curanci='curanci_'.$i;
            $domesticAmount_='txtDomesticAmount_'.$i;
            
            if(str_replace("'",'',$$updateid_dtls)=="0")  
            {
                if ($add_comma!=0) $data_array1 .=",";
                $data_array1 .="(".$id1.",".$id.",".$$recive_id.",".$$receive_date.",".$$mrr_no.",".$$challen_no.",".$$order_no.",".$$item_id.",".$$wo_number.",".$$cbo_uom.",".$$yarn_qnty.",".$$rate.",".$$amount.",".$$remarks.",".$bill_process_id.",".$$curanci.",".$$num_of_bag.",".$$num_of_cone.",".$$domesticAmount_.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                //."',".$$curanci.",".$$subprocessId.",".$$serviceSource
                $id1=$id1+1;
                $add_comma++;
            }
            else
            {
                $id_arr[]=str_replace("'",'',$$updateid_dtls);
                $data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$cbo_uom."*".$$yarn_qnty."*".$$rate."*".$$amount."*".$$curanci."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
                $rID2=execute_query(bulk_update_sql_statement('subcon_outbound_bill_dtls', 'id', $field_array_up, $data_array_up,$id_arr ));
            }
        }
        
        //echo "10**$data_array1"; die;
        
        if($data_array1!="")
        {
            // echo "10**insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
            $rID1=sql_insert('subcon_outbound_bill_dtls', $field_array1, $data_array1, 1);
        }
        //echo "10**".$rID."**".$rID1."**".$rID2;die;
        if($db_type==0)
        {
            if($rID && $rID1)
            {
                mysql_query("COMMIT");  
                echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
        }
        if($db_type==2)
        {
            if($rID && $rID1)
            {
                oci_commit($con);
                echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
            else
            {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
        }   
        disconnect($con);
        die;
    }

    else if ($operation==1)   // Update Here=============================================================================
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
    
        $id=str_replace("'",'',$update_id);
        $field_array="location_id*bill_date*supplier_id*bill_for*manual_challan*party_bill_no*updated_by*update_date";
        $data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$cbo_bill_for."*".$txt_manual_challan."*".$txt_party_bill."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
        $rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
        $return_no=str_replace("'",'',$txt_bill_no);
        
        $dtls_update_id_array=array();
        $sql_dtls="Select id from subcon_outbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
        $nameArray=sql_select( $sql_dtls );
        foreach($nameArray as $row)
        {
            $dtls_update_id_array[]=$row[csf('id')];
        }
        
        $id1=return_next_id( "id","subcon_outbound_bill_dtls",1);
        $field_array1 ="id, mst_id, receive_id, receive_date, mrr_no, challan_no, job_no, item_id, febric_description_id, color_id, wo_num_id, uom, receive_qty, rate, amount, remarks, process_id, currency_id, no_of_bags, cone_per_bag, inserted_by, insert_date";
        $field_array_up ="uom*receive_qty*rate*amount*currency_id*remarks*updated_by*update_date";
        $add_comma=0;
        for($i=1; $i<=$tot_row; $i++)
        {
            $receive_date="txtReceiveDate_".$i;
            $mrr_no="txtMrrNo_".$i;
            $challen_no="txtChallenNo_".$i;
            //$orderid="orderNoId_".$i;
            $order_no="txtOrderNo_".$i;
            
            
            //$style_name="txtStyleName_".$i;
            //$party_name="txtPartyName_".$i;
            $num_of_bag="txtNumberBag_".$i;
            $num_of_cone="txtNumberCone_".$i;
            $item_id="itemid_".$i;
            $colorid="colorId_".$i;
            $wo_number="textWoNumId_".$i;
            $cbo_uom="cboUom_".$i;
            $yarn_qnty="txtYarnQty_".$i;
            $rate="txtRate_".$i;
            $amount="txtAmount_".$i;
            $remarks="txtRemarks_".$i;
            $recive_id="reciveId_".$i;
            $updateid_dtls="updateIdDtls_".$i;
            $curanci="curanci_".$i;
            
            if(str_replace("'",'',$$updateid_dtls)=="0")  
            { 
                if ($add_comma!=0) $data_array1 .=",";
                $data_array1 .="(".$id1.",".$id.",".$$recive_id.",".$$receive_date.",".$$mrr_no.",".$$challen_no.",".$$order_no.",".$$item_id.",0,".$$colorid.",".$$wo_number.",".$$cbo_uom.",".$$yarn_qnty.",".$$rate.",".$$amount.",".$$remarks.",'30',".$$curanci.",".$$num_of_bag.",".$$num_of_cone.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
                $id1=$id1+1;
                $add_comma++;
                
            }
            else
            {
                $id_arr[]=str_replace("'",'',$$updateid_dtls);
                $data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$cbo_uom."*".$$yarn_qnty."*".$$rate."*".$$amount."*".$$curanci."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
            }
        }
              
        $rID1=execute_query(bulk_update_sql_statement("subcon_outbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
        if($data_array1!="")
        {
            //echo "10**insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;
            $rID1=sql_insert("subcon_outbound_bill_dtls",$field_array1,$data_array1,1);
        }
        if(!empty($id_arr))
        {
            $delete_arr=array_diff($dtls_update_id_array, $id_arr);
            $delete_id=implode(",", $delete_arr);
            if($delete_id)
            {
                $rID3=execute_query( "update subcon_outbound_bill_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($delete_id)",1);
            }
        }
        
        //echo "10**<pre>";
        //print_r($dtls_update_id_array);
        //print_r($id_arr);die;
        
        //echo "10**".$rID."**".$rID1."**".$rID3;die;
        if($db_type==0)
        {
            if($rID && $rID1)
            {
                mysql_query("COMMIT");  
                echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
        }
        if($db_type==2)
        {
            if($rID && $rID1)
            {
                oci_commit($con);
                echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
            else
            {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
            }
        }       
        disconnect($con);
        die;
    }
}
else if ($action=='load_dtls_data')
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
    // echo $sql;die;
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
if ($action == "yarn_service_bill_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
    // print_r($data);
    $company_id = $data[0];
    $sys_id = $data[1];
    $report_title = $data[3];

    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id","buyer_name");
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");


    $imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

    $sql_mst="Select id, bill_no, bill_date, supplier_id, location_id, bill_for, party_bill_no from SUBCON_OUTBOUND_BILL_MST where company_id=$company_id and id=$sys_id and status_active=1 and is_deleted=0";
	
	$dataArray=sql_select($sql_mst);
    ?>
        <div style="width:1580px; margin-left:20px;">
            <table width="1350" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="70" align="right"> 
                        <img  src='../../../<? echo $imge_arr[str_replace("'","",$company_id)]; ?>' height='70' width='200' />
                    </td>
                    <td colspan="2">
                        <table width="800" cellspacing="0" align="center">
                            <tr>
                                <td align="center" style="font-size:25px"><strong ><? echo $company_library[$company_id]; ?></strong></td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:16px"><strong>Address : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>
            <br>
            
            <table style="width: 100%; margin:auto;">
                <tr>
                    <td align="center" style="font-size:22px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <br>
            <table width="1000" cellspacing="0" align="" border="0">
                <tr>
                    <td width="120" style="height: 45px;"><strong>Bill No :</strong></td> <td width="120"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                    <td width="120"><strong>Bill Date: </strong></td><td width="120px"><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                    <td style="width: 120px;"><strong>Bill For : </strong></td><td width="120px" style="word-break:break-all"><? echo $yarn_issue_purpose[$dataArray[0][csf('bill_for')]]; ?></td>
                </tr>
                <tr>
                    <td><strong>Supplier: </strong></td><td style="word-break:break-all"><? echo $supplier_library_arr[$dataArray[0]['SUPPLIER_ID']]; ?></td>
                    <td ><strong>Party Bill No: </strong></td><td style="word-break:break-all"><? echo $dataArray[0]['PARTY_BILL_NO']; ?></td>
                </tr>
            </table>
            <br>
            <br>
            <div style="width:100%;" >
            <table cellspacing="0" width="1580"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center" style="font-size:16px">
                    <th width="30">SL</th>
                    <th width="80">Ch. Date</th>
                    <th width="150">Sys. Challan</th> 
                    <th width="120">Rec. Challan</th> 
                    <th width="80">Buyer</th>
                    <th width="80">Job</th>
                    <th width="80">Style</th>
                    <th width="80">N.O Bag</th>
                    <th width="260">Yarn Desc.</th>                  
                    <th width="80">Lot</th>
                    <th width="150">Wo Num</th>
                    <th width="80">Yarn Qty</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>               
                    <th width="80">Currency</th>
                    <th width="">Remarks</th>
                </thead>
                <?
                $sql_result =sql_select( "SELECT b.id, b.MRR_NO  AS sys_challan,  b.receive_date AS ch_date,  b.challan_no as rec_challan,  c.buyer_id, g.BUYER_NAME as buyer, f.BUYER_ID as sale_buyer,  b.job_no,  f.STYLE_REF_NO as sale_style, g.STYLE_REF_NO as style, f.JOB_NO_PREFIX_NUM as sale_pre_num, g.JOB_NO_PREFIX_NUM as pre_num, c.no_bag,  d.PRODUCT_NAME_DETAILS as yarn_desc,  d.lot,  b.WO_NUM_ID,  e.YDW_NO as wo_num,  b.receive_qty as yarn_qty,  b.rate, b.amount,  b.CURRENCY_ID,  b.remarks  FROM SUBCON_OUTBOUND_BILL_MST a, subcon_outbound_bill_dtls b  left join INV_RECEIVE_MASTER c on b.MRR_NO = RECV_NUMBER  left join PRODUCT_DETAILS_MASTER d on b.item_id = d.id  left join WO_YARN_DYEING_MST e on e.id = b.wo_num_id  LEFT JOIN FABRIC_SALES_ORDER_MST f on b.job_no = f.job_no LEFT JOIN WO_PO_DETAILS_MASTER g on b.job_no = g.job_no  WHERE a.id = b.mst_id AND a.id = $sys_id AND b.status_active = 1 AND b.is_deleted = 0 ORDER BY b.id ASC"); 
                
                // echo "<pre>";
                // print_r($sql_result);die;
                $i = 1;
                $total_qty = 0;
                $total_rate = 0;
                $total_amount = 0;
                foreach($sql_result as $row)
                {
                    if($row['BUYER']!="" && $row['BUYER']!= 0){
                        $buyer = $row['BUYER'];
                    }
                    else{
                        $buyer = $row['SALE_BUYER'];
                    }

                    if($row['PRE_NUM']!="" && $row['PRE_NUM']!= 0){
                        $job_no = $row['PRE_NUM'];
                    }
                    else{
                        $job_no = $row['SALE_PRE_NUM'];
                    }

                    if($row['STYLE']!="" && $row['STYLE']!= 0){
                        $style = $row['STYLE'];
                    }
                    else{
                        $style = $row['SALE_STYLE'];
                    }

                
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px" align="center"> 
                        <td><? echo $i; ?></td>
                        <td><p><? echo change_date_format($row['CH_DATE']); ?></p></td>
                        <td><? echo $row['SYS_CHALLAN']; ?></td>
                        <td><? echo $row['REC_CHALLAN']; ?></td>
                        <td><? echo $buyer_library[$buyer]; ?></td>
                        <td><? echo $job_no; ?></td>
                        <td><? echo $style; ?></td>
                        <td><? echo $row['NO_BAG']; ?></td>
                        <td><? echo $row['YARN_DESC']; ?></td>
                        <td><? echo $row['LOT']; ?></td>
                        <td><? echo $row['WO_NUM']; ?></td>
                        <td align="right"><? echo number_format($row['YARN_QTY'],2); ?></td>
                        <td align="right"><? echo number_format($row['RATE'],2); ?></td>
                        <td align="right"><? echo number_format($row['AMOUNT'],2); ?></td>
                        <td><? echo $currency[$row['CURRENCY_ID']]; ?></td>
                        <td><? echo $row['REMARKS']; ?></td>

                        <? 
                        $currency_id=$row['CURRENCY_ID'];
                        if($currency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
                    ?>
                    </tr>
                    <?
                    $i++;
                    $total_qty += $row['YARN_QTY'];
                    $total_rate += $row['RATE'];
                    $total_amount += $row['AMOUNT'];

                }
                ?>
                <tr> 
                    <td align="right" colspan="11"><strong>Total</strong></td>
                    <td align="right"><b><? echo number_format($total_qty,2,'.',''); ?></b></td>
                    <td align="right"><b><? echo number_format($total_rate,2,'.',''); ?></b></td>
                    <td align="right"><b><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <tr>
                <td colspan="23" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$currency_id],$paysa_sent); ?></b></td>
            </tr>
        </table>

		 <? echo signature_table(336, $data[0], "980px"); ?>
        </div>
    <?
    
	exit();
}

?>