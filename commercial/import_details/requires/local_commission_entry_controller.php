<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

if ($action=="load_drop_down_buyer")
{  $data=explode("_",$data);
    $ex_factory_date = " and b.ex_factory_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
    $sql_buyer_id=sql_select("SELECT DISTINCT a.BUYER_ID FROM pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where b.delivery_mst_id=a.id and a.COMPANY_ID=$data[0]  $ex_factory_date");
    
    $buyer_id_arr=array();
    foreach($sql_buyer_id as $row){
      $buyer_id.=$row["BUYER_ID"].",";
    }
    $job_dtls_ids=implode(",",array_unique(explode(",",(chop($buyer_id,',')))));

	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] and buy.id in($job_dtls_ids) $buyer_cond  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "fnc_delevery_entry_data()",0 );
	exit();
}

if ($action=="load_drop_down_buyer2")
{  $data=explode("_",$data);
  
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] and buy.id in($data[1]) $buyer_cond  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "fnc_delevery_entry_data()",0 );
	exit();
}

if ($action=="load_drop_down_comission_buyer")
{
	//echo create_drop_down( "cbo_commison_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,20))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
     
    echo create_drop_down( "cbo_commison_name", 150, "select c.id,c.supplier_name from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name,c.short_name order by c.supplier_name","id,supplier_name", 1, "-- All Supplier --", $selected, "",0 );
	exit();
}

if($action == 'system_id_popup') {
    echo load_html_head_contents('Search Yarn Dyeing Sales Order', '../../../', 1, 0, $unicode);
    extract($_REQUEST);
    ?>
        <style>
            table.rpt_table tbody td input {
                width: 90%;
            }
        </style>
        <script>
            permission="<?php echo $permission; ?>";

            function js_set_value(id) {
                document.getElementById('selected_mst_id').value = id;
                parent.deliveryPopup.hide();
            }
        </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
                <thead>
                    <tr>
                        <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                    </tr>
                    <tr>
                        <th style="width:20%;">Company Name</th>
                        <th style="width:20%;">Job No</th>
                        <th style="width:25%;">System ID</th>
                        <th style="width:25%;">Debit Date</th>
                        <th style="width:20%;">
                            <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $data, '', 1); ?>
                        </td>
                        <td >
                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                        </td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_system_id" id="txt_system_id" />
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td>
                        <td align="center">
                            <input type="hidden" id="selected_mst_id">
                            <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_system_popup_list_view', 'search_div', 'local_commission_entry_controller', '')" />
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" colspan="8" height="40" valign="middle">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                
                </tbody>
            </table>
        </form>  
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?php
    exit();
}

if($action == 'create_system_popup_list_view') {
    $data=explode('_', $data);
    // echo $data;die;
    // print_r($data);
    $search_type = $data[0];
    $company_id = $data[1];
    $job_no = $data[2];
    $system_id = $data[3];
    $condition = '';

    if($company_id) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($search_type==0 || $search_type==4) { // no searching type or contents
        //if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job%'";
        if ($job_no!="") $condition.=" and b.job_no like '%$job_no%'";
        if ($system_id!="") $condition.=" and a.sys_number like '%$system_id%'";
    } else if($search_type==1) { // exact
        //if ($yd_job!="") $condition.=" and c.yd_job = '$yd_job'";
        if ($job_no!="") $condition.=" and b.job_no ='$job_no'";
        if ($system_id!="") $condition.=" and a.sys_number = '$system_id'";
    } else if($search_type==2) { // Starts with
        //if ($yd_job!="") $condition.=" and c.yd_job like '$yd_job%'";
        if ($job_no!="") $condition.=" and b.job_no like '$job_no%'";
        if ($system_id!="") $condition.=" and a.sys_number like '$system_id%'";
    } else if($search_type==3) { // Ends with
       // if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job'";
        if ($job_no!="") $condition.=" and b.job_no like '%$job_no'";
        if ($system_id!="") $condition.=" and a.sys_number like '%$system_id'";
    }

  
    if ($data[4]!="" &&  $data[5]!="") $date_con  = "and a.insert_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $date_con ="";


    if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$data[6].""; }
	else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$data[6].""; }

    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');

    $sql = "SELECT a.id, a.sys_number,a.local_date, a.remarks, b.job_no from local_commission_entry_mst a, local_commission_entry_dtls b where a.id=b.mst_id and a.status_active=1  $condition $date_con ";

   $sql_view=sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1"  rules="all"  align="center" style="width: 600;" >
        <thead> 
            <tr>
                <th width="40">SL</th>
                <th width="150">System ID</th>
                <th width="150"> Job No</th>
                <th width="150">Local Date</th>
            </tr>
        </thead>
        <tbody style="background-color: #FFFFFF;">
           <?$i=1;
            foreach($sql_view as $row){ ?>
            <tr onclick="js_set_value(<?=$row['ID'] ?>)" >
                <td align="center"><?=$i?></td>
                <td align="center"><?=$row["SYS_NUMBER"]?></td>
                <td align="center"><?=$row["JOB_NO"]?></td>
                <td align="center"><?=$row["LOCAL_DATE"]?></td>
            </tr>
            <?$i++;
          }?>
        </tbody>
    </table>
    <?
    exit();
}

if($action == 'populate_mst_data_from_search_popup') {
   // $data = explode('**', $data);
   $sql = "SELECT a.id, a.company_id, a.local_date, a.buyer_id, a.sys_number, a.remarks, a.commission_party from local_commission_entry_mst a where a.status_active=1 and a.id=$data";

    $result = sql_select($sql);
    echo "document.getElementById('txt_system_id').value = '".$result[0][csf('sys_number')]."';\n";
    echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
    echo "document.getElementById('cbo_commison_name').value = '".$result[0][csf('commission_party')]."';\n";
    echo "document.getElementById('txt_local_date').value = '".$result[0][csf('local_date')]."';\n";
    echo " load_drop_down( 'requires/local_commission_entry_controller',".$result[0][csf('company_id')]."+'_'+".$result[0][csf('buyer_id')].", 'load_drop_down_buyer2', 'buyer_td' );\n";
	echo "document.getElementById('cbo_buyer_name').value = '".$result[0][csf('buyer_id')]."';\n"; 
	echo "document.getElementById('cbo_buyer_id').value = '".$result[0][csf('buyer_id')]."';\n"; 
    echo "document.getElementById('txt_remark').value = '".$result[0][csf('remarks')]."';\n";
    echo "document.getElementById('hdnupdateid').value = '".$result[0][csf('id')]."';\n";
    echo "set_button_status(1, permission, 'fnc_inspection',1,1);\n";
}

if($action == 'populate_dtls_data_from_search_popup')
 {
	$data = explode('_', $data);
    //  print_r($data );die;
   
    $company_id=$data[0];
    $buyer_id=$data[1];
    $commission_id=$data[2];
    $from_date = trim($data[3]);
	$to_date = trim($data[4]);
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
    $buyer_cond = ($buyer_id!=0) ? " and a.buyer_id=$buyer_id" : ""; 
    $ex_factory_date = " and c.ex_factory_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";

    $comishion_amount_array=array();
    $sql_data=sql_select("SELECT a.particulars_id, a.commission_amount, a.job_no, a.commission_amount, b.costing_per_id, b.price_pcs_or_set from wo_pre_cost_commiss_cost_dtls a, wo_pre_cost_dtls b where a.JOB_ID=b.JOB_ID and a.STATUS_ACTIVE=1 and a.particulars_id=2");
    foreach($sql_data as $row){
 
        if($row[csf("costing_per_id")]==1 && $row[csf("price_pcs_or_set")]==2){
            $comishion_amount_array[$row[csf("job_no")]]["commission_amount"]=($row[csf("commission_amount")]/12)/2;
        }
        else if($row[csf("costing_per_id")]==1){
            $comishion_amount_array[$row[csf("job_no")]]["commission_amount"]=$row[csf("commission_amount")]/12;
        }else{
            $comishion_amount_array[$row[csf("job_no")]]["commission_amount"]=$row[csf("commission_amount")];
        }
       
    }

    $sql= "SELECT a.company_id, a.buyer_id, a.sys_number, a.delivery_date, f.invoice_no, d.job_no_mst, e.style_ref_no, d.po_number, c.ex_factory_qnty, f.is_lc, d.id as po_id, f.LC_SC_ID from pro_ex_factory_delivery_mst a, pro_ex_factory_mst c, wo_po_break_down d, wo_po_details_master e, com_export_invoice_ship_mst f, com_export_invoice_ship_dtls g where c.delivery_mst_id=a.id and d.id=c.po_break_down_id and e.id=d.job_id and f.id=c.invoice_no AND f.id = g.mst_id and a.sys_number not in(select challan_no from local_commission_entry_dtls where challan_no is not null and status_active=1) and g.po_breakdown_id=c.po_break_down_id and c.invoice_no<>0 and a.company_id=$company_id and a.status_active=1  and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $ex_factory_date $buyer_cond";
    //   echo  $sql;//die;
	$result = sql_select($sql);
    $sales_export_arr=array();
        foreach($result as $row){
            if($row["IS_LC"]==1){
                $lc_sc="SELECT a.id as lc_sc, b.attached_rate, b.wo_po_break_down_id from com_export_lc a left join com_export_lc_order_info b on a.id=b.com_export_lc_id and a.BENEFICIARY_NAME=$company_id and b.status_active=1 where a.status_active=1";
                $lc_sc_arr = sql_select($lc_sc);
                foreach($lc_sc_arr as $val){
                    $sales_export_arr[$val["LC_SC"]][$val["WO_PO_BREAK_DOWN_ID"]]["ATTACHED_RATE"]=$val["ATTACHED_RATE"];
                }
            }else{
                $lc_sc="SELECT a.id as lc_sc, b.attached_rate, b.wo_po_break_down_id from com_sales_contract a left join com_sales_contract_order_info b on a.id=b.com_sales_contract_id and a.BENEFICIARY_NAME=$company_id and b.status_active=1 where a.status_active=1";
                $lc_sc_arr = sql_select($lc_sc);
                foreach($lc_sc_arr as $val){
                    $sales_export_arr[$val["LC_SC"]][$val["WO_PO_BREAK_DOWN_ID"]]["ATTACHED_RATE"]=$val["ATTACHED_RATE"];
                }
            }
        }
    ?>
    
        <?php 
        if($buyer_id>0){
            $i=1; $sl=1;
            foreach($result as $row) {
                
                if($comishion_amount_array[$row[csf("job_no_mst")]]["commission_amount"]>0){
                    $invoice_rate= $sales_export_arr[$row["LC_SC_ID"]][$row["PO_ID"]]["ATTACHED_RATE"];
                    ?>
                    <tr>
                    <td  style="width:30px" align="center">
                            <?php echo $i; ?>
                        </td>
                        <td align="center">
                            <input name="txt_challan_no[]" id="txt_challan_no_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="write" value="<?php echo $row[csf('sys_number')]; ?>" readonly    style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_exfactory_date[]" id="txt_exfactory_date_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('delivery_date')]; ?>" readonly style="width:100px" />
                        </td>

                        <td align="center">
                            <input name="txtInvoiceNo[]" id="txtInvoiceNo_<?php echo $sl; ?>" type="text" class="text_boxes" readonly placeholder="Display" value="<?php echo $row[csf('invoice_no')] ?>" style="width:100px" />                   
                        </td>
                        <td align="center">
                            <input name="txt_buyer_name[]" id="txt_buyer_name_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo  $buyerArr[$row[csf('buyer_id')]]; ?>" readonly style="width:100px"/>
                            <input name="cbo_buyer_id[]" id="cbo_buyer_id_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('buyer_id')]; ?>" />
                        </td>
                        <td align="center">
                            <input name="txt_jobNo[]" id="txt_jobNo_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('job_no_mst')]  ?>" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_style_ref[]" id="txt_style_ref_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('style_ref_no')] ?>" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_order_no[]" id="txt_order_no_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('po_number')] ?>" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_ex_fac_qty[]" id="txt_ex_fac_qty_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('ex_factory_qnty')] ?>" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_invoice_rate[]" id="txt_invoice_rate_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $invoice_rate ?>" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_ex_fac_value[]" id="txt_ex_fac_value_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('ex_factory_qnty')]*$invoice_rate ?>" readonly style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_rate[]" id="txt_rate_<?php echo $sl; ?>" type="text" class="text_boxes" readonly placeholder="Display" value="<?php echo number_format($comishion_amount_array[$row[csf("job_no_mst")]]["commission_amount"],4);?>"  style="width:100px"/>
                        </td>
                        <td align="center">
                            <input name="txt_value_qty[]" id="txt_value_qty_<?php echo $sl; ?>" value="<?php echo number_format($row[csf('ex_factory_qnty')]*$comishion_amount_array[$row[csf("job_no_mst")]]["commission_amount"],4, '.', '')?>"  type="number" readonly class="text_boxes" placeholder="write"   style="width:100px"/>
                        </td>                         
                    </tr>
                    <?php 
                    $sl++;$i++;
                    $grand_total+=$row[csf('ex_factory_qnty')]*$comishion_amount_array[$row[csf("job_no_mst")]]["commission_amount"];
                }
            }
            ?>
            <tr>
                <td align="right" colspan="12"><b>Total</b></td>
                <td><b>&nbsp;<?=$grand_total?></b></td>
            </tr>
            <? 
        }
	exit();
}

if($action == 'populate_dtls_data_from_search_popup_update')
 {
	$data = explode('**', $data);
   //print_r($data );
    $mst_id=$data[0];
    $issue_ban_name=return_library_array( "select id, branch_name from lib_bank",'id','branch_name');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
     $sql = "SELECT id, challan_no, ex_factory_date, invoice_no, buyer_id, job_no, style_ref_no, order_no, ex_factory_qty, invoice_rate, ex_factory_value, rate, amount from local_commission_entry_dtls where mst_id=$mst_id and status_active=1 order by id desc";
      //echo  $sql;
	$result = sql_select($sql);
         $i=1; $sl=1;
        foreach($result as $row) {
            ?>
            <tr>
                 <td  style="width:30px" align="center">
                    <?php echo $i; ?>
                </td>
                <td align="center">
                    <input name="txt_challan_no[]" id="txt_challan_no_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="write" value="<?php echo $row[csf('challan_no')]; ?>" readonly    style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_exfactory_date[]" id="txt_exfactory_date_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('ex_factory_date')]; ?>" readonly style="width:100px" />
                </td>

                <td align="center">
                    <input name="txtInvoiceNo[]" id="txtInvoiceNo_<?php echo $sl; ?>" type="text" class="text_boxes" readonly placeholder="Display" value="<?php echo $row[csf('invoice_no')] ?>" style="width:100px" />                   
                </td>
                <td align="center">
                    <input name="txt_buyer_name[]" id="txt_buyer_name_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo  $buyerArr[$row[csf('buyer_id')]]; ?>" readonly style="width:100px"/>
                    <input name="cbo_buyer_id[]" id="cbo_buyer_id_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('buyer_id')]; ?>" />
                </td>
                <td align="center">
                    <input name="txt_jobNo[]" id="txt_jobNo_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('job_no')]  ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_style_ref[]" id="txt_style_ref_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('style_ref_no')] ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_order_no[]" id="txt_order_no_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('order_no')] ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_ex_fac_qty[]" id="txt_ex_fac_qty_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('ex_factory_qty')] ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_invoice_rate[]" id="txt_invoice_rate_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('invoice_rate')] ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_ex_fac_value[]" id="txt_ex_fac_value_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo number_format($row[csf('ex_factory_value')],2) ?>" readonly style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_rate[]" id="txt_rate_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('rate')] ?>" readonly  style="width:100px"/>
                </td>
                <td align="center">
                    <input name="txt_value_qty[]" id="txt_value_qty_<?php echo $sl; ?>" value="<?= $row[csf('amount')]?>"  type="text" class="text_boxes" placeholder="write" readonly   style="width:100px"/>
                    <input name="txtHiddenDtlsId[]" id="txtHiddenDtlsId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('id')]; ?>" />
                </td>                          
            </tr>
            <?php
            $sl++;$i++;
            $total+=$row[csf("amount")];
        }
        ?>
        <tr>
            <td colspan='12' align="right"><b>Total</b></td>
            <td> &nbsp;<b><input type="text" style="width:100px" readonly value='<?=$total?>'></b></td>
        </tr>
        <?

	exit();
}

if($action=='save_update_delete') 
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0) {
        // save here
        $con = connect();
        $flag = 1;
        $add_comma = false;
        $field_array_mst = '';
        $data_array_mst = '';
        $field_array_dtls = '';
        $data_array_dtls = '';
        $entryForm = 691;
        $con = connect();
        $mstId = return_next_id('id', 'local_commission_entry_mst', 1);
        $dtls_is_first = return_next_id('id', 'local_commission_entry_dtls', 1);
       
		 if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
        else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
        //$hdnOrderId = str_replace("'", '', $hdnOrderId);
         $new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'LCOE' , date("Y",time()), 5, "select id,sys_number_prefix,sys_number_prefix_num from local_commission_entry_mst where company_id=$cbo_company_name and entry_form=691 $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
  
        //$txt_debat_note_date=change_date_format($txt_debat_note_date, "", "",1);
        $txt_local_date=change_date_format(str_replace("'",'',$txt_local_date), "", "",1);

        $field_array_mst = 'id, entry_form, sys_number, sys_number_prefix, sys_number_prefix_num, company_id, local_date, buyer_id, remarks, commission_party, inserted_by, insert_date,is_deleted, status_active';

        $data_array_mst="(".$mstId.", ".$entryForm.", '".$new_system_id[0]."', '".$new_system_id[1]."', '".$new_system_id[2]."', ".$cbo_company_name.", '".$txt_local_date."', ".$cbo_buyer_name.", '".$txt_remark."', ".$cbo_commison_name.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."','0',1)";

        $field_array_dtls = 'id, mst_id, challan_no, ex_factory_date, invoice_no, buyer_id ,job_no, style_ref_no, order_no, ex_factory_qty, invoice_rate, ex_factory_value, rate, amount, inserted_by, insert_date, is_deleted, status_active';
        for($i=1; $i<=$total_row; $i++) {
            $txt_challan_no          = 'txt_challan_no_'.$i;
            $txt_exfactory_date      = 'txt_exfactory_date_'.$i;
            $txtInvoiceNo            = 'txtInvoiceNo_'.$i;
            $cbo_buyer_id          = 'cbo_buyer_id_'.$i;
            $txt_jobNo               = 'txt_jobNo_'.$i;
            $txt_style_ref           = 'txt_style_ref_'.$i;
            $txt_order_no            = 'txt_order_no_'.$i;
            $txt_ex_fac_qty          = 'txt_ex_fac_qty_'.$i;
            $txt_invoice_rate        = 'txt_invoice_rate_'.$i;
            $txt_ex_fac_value        = 'txt_ex_fac_value_'.$i;
            $txt_rate                = 'txt_rate_'.$i;
            $txt_value_qty           = 'txt_value_qty_'.$i;

            $data_array_dtls .= $add_comma ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls
           
            if($$txt_value_qty!=''){
            $data_array_dtls .= "(".$dtls_is_first.",".$mstId.",".$$txt_challan_no.",".$$txt_exfactory_date.",".$$txtInvoiceNo.",".$$cbo_buyer_id.",".$$txt_jobNo.",".$$txt_style_ref.",".$$txt_order_no.",".$$txt_ex_fac_qty.",".$$txt_invoice_rate.",".$$txt_ex_fac_value.",".$$txt_rate.",".$$txt_value_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
            }
            $dtls_is_first++; // increment details id by 1
        }

        // echo "10**insert into supplier_debit_note_entry_mst(".$field_array_mst.") values ".$data_array_mst; die;
        $rID = sql_insert('local_commission_entry_mst', $field_array_mst, $data_array_mst, 0);

        $flag = ($flag && $rID);    // return true if $flag is true and mst table insert is successful

        // echo $flag, $rID;die;
        //  echo "10**insert into local_commission_entry_dtls(".$field_array_dtls.") values ".$data_array_dtls; die; disconnect($con);
        $rID2 = sql_insert('local_commission_entry_dtls', $field_array_dtls, $data_array_dtls, 0);

        // echo '10**'.$rID.'**'.$rID2; disconnect($con);die;

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table insert is successful

        if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '0**'.$new_system_id[0].'**'.$mstId;
            } else {
                oci_rollback($con);
                echo '10**'.$hdnOrderId;
            }
            }
        disconnect($con);
        die;
    }

    else if($operation == 1)  // update here
    {     
        $flag = 1;
        $id_arr = array();
        $con = connect();

        $txt_update_id=str_replace("'",'',$hdnupdateid);
        $txt_local_date=change_date_format($txt_local_date, "", "",1);

        $field_array_mst = 'local_date*buyer_id*remarks*commission_party*updated_by*update_date';
        $data_array_mst="'".$txt_local_date."'*".$cbo_buyer_name."*'".$txt_remark."'*".$cbo_commison_name."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
       
        $field_array_dtls = 'rate*amount*updated_by*update_date';
        for($i = 1; $i <= $total_row; $i++) {
            $txt_rate                = 'txt_rate_'.$i;
            $txt_value_qty           = 'txt_value_qty_'.$i;
            $txtHiddenDtlsId         = 'txtHiddenDtlsId_'.$i;
            if($$txt_value_qty!=''){
                $data_array_dtls[str_replace("'", '', $$txtHiddenDtlsId)] =explode("*",("".$$txt_rate."*".$$txt_value_qty."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
            $id_arr[]=str_replace("'", '', $$txtHiddenDtlsId);
            }
        }

        $rID = sql_update('local_commission_entry_mst', $field_array_mst, $data_array_mst, 'id', $txt_update_id, 0);

        // echo  "10**".$rID;die; disconnect($con);

        $flag = ($flag && $rID);   
        //   echo '10**' . bulk_update_sql_statement('local_commission_entry_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr); disconnect($con);die;
        $rID2 = execute_query(bulk_update_sql_statement('local_commission_entry_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr), 1);
        $flag = ($flag && $rID2); 
       //    echo '10**'.$rID.'**'.$rID2;die; disconnect($con);

        if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '1**'.$txt_system_id.'**'.$txt_update_id;
            } else {
                oci_rollback($con);
                echo '6**'.$txt_system_id.'**'.$txt_update_id;
            }
        }
        disconnect($con);
        die;
    }
    else if($operation == 2) // Delete here
    { 
        $con = connect();
        $txt_update_id=str_replace("'",'',$hdnupdateid);

        $field_array_mst="updated_by*update_date*status_active*is_deleted";
        $data_array_mst="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

        $field_array_dtls="updated_by*update_date*status_active*is_deleted";
        $data_array_dtls="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
        
       
        $rID2=sql_update("local_commission_entry_dtls",$field_array_dtls,$data_array_dtls,"mst_id",$txt_update_id,0);
        $rID1=sql_update("local_commission_entry_mst",$field_array_mst,$data_array_mst,"id",$txt_update_id,0);
        // echo "67**".$rID1; disconnect($con); die;
        // echo "10**".$rID1."_".$rID2; disconnect($con); die;

        if($db_type==2) {
            if($rID2 && $rID1) {
                oci_commit($con);
                echo '2**'.$txt_system_id.'**'.$txt_update_id;
            } else {
                oci_rollback($con);
                echo '10**'.$txt_system_id.'**'.$txt_update_id;
            }
        }
        disconnect($con);
        die;
    }
    exit();
}

if($action=="local_commision_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
        // print_r($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
    $buyerarr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

   
    $sql_mst="SELECT COMPANY_ID,LOCAL_DATE,SYS_NUMBER, COMMISSION_PARTY,BUYER_ID, REMARKS from local_commission_entry_mst where id=$data[1] and entry_form=691 and status_active=1";
	$dataArray=sql_select($sql_mst);
	$inserted_by=$dataArray[0]['INSERTED_BY'];
	$local_date=$dataArray[0]['LOCAL_DATE'];
	$sys_number=$dataArray[0]['SYS_NUMBER'];
	$commission_party=$dataArray[0]['COMMISSION_PARTY'];
	$buyer_id=$dataArray[0]['BUYER_ID'];
	$remarks=$dataArray[0]['REMARKS'];

	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:1100px;">
		<table width="1100" cellspacing="0" align="center" border="0">
	        <tr>
	            <td colspan="6" style="font-size: 40px;" align="center"><? echo $company_library[$data[0]]; ?> </td>
	        </tr>
            <tr>
	            <td colspan="6" style="font-size: 20px;" align="center"><? echo "Address: ".$com_dtls[1]; ?> </td>
	        </tr>
		</table>
		<br>
		<table width="1100" cellspacing="0" align="center" border="0">				
            </tr>
                <td width="120"><b>System Id: </b></td> <td align="left" width="175"><?=$sys_number;?></td>
				<td width="120"><b>Buyer:</b> </td> <td align="left" width="175"> <? echo $buyerarr[$buyer_id];?></td>
                <td width="120"><b>Remarks:</b></td> <td width="275"><? echo $remarks;?></td>
            </tr>
		</table>
        <br>

		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table"  >
				<thead>
                    <tr bgcolor="#98AFC7">
                        <th colspan="10"></th>
                        <th colspan="2">Local Commission</th>
                    </tr>
					<tr bgcolor="#98AFC7">
		        		<th rowspan="2" width="34">Sl No.</th>
		        		<th rowspan="2" width="100">Challan NO.</th>
		        		<th rowspan="2" width="100">Ex-Factory Date.</th>
		        		<th rowspan="2"width="100">Invoice No.</th>
		        		<th rowspan="2" width="100">Buyer Name.</th>
		        		<th rowspan="2"width="100">Job No.</th>
		                <th rowspan="2" width="100" >Style Ref. no.</th>
		                <th rowspan="2" width="100">Order NO</th>
		                <th rowspan="2" width="100">FOB</th>
		                <th rowspan="2" width="100">Ex-Factory Value</th>
		                <th width="100">Rate (%)</th>
		                <th width="100">Value</th>
		        	</tr>
				</thead>
				<tbody>
				<?
               $sql = "SELECT CHALLAN_NO, EX_FACTORY_DATE,INVOICE_NO,BUYER_ID, JOB_NO, STYLE_REF_NO, ORDER_NO, EX_FACTORY_VALUE, INVOICE_RATE, RATE, AMOUNT from local_commission_entry_dtls where mst_id =$data[1] and status_active=1 and is_deleted=0 order by id ASC";
               $local_result=sql_select($sql);
			        $i=1;
					foreach($local_result as $row)
					{
                        if($i%2==0) $bgcolor="#E9F3FF"; 
                        else $bgcolor="#FFFFFF"; 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  align="center">
							<td style="word-break:break-all" width="35"><?  echo $i; ?></td>
			                <td style="word-break:break-all" width="100"><?  echo $row['CHALLAN_NO'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['EX_FACTORY_DATE'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['INVOICE_NO'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['BUYER_ID'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['JOB_NO'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['STYLE_REF_NO'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['ORDER_NO'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['INVOICE_RATE'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['EX_FACTORY_VALUE'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['RATE'] ; ?></td>
			                <td style="word-break:break-all" width="100" align="right"><?  echo $row['AMOUNT'] ; ?></td>
						</tr>
						<?						
                        $total+=$row['AMOUNT'];
                        $i++;
					}
					?> 
					<tr>
						<td colspan="11" align="right"><strong>Total:</strong></td>
						<td align="right" class="make_bold"><p><?  echo number_format($total,4) ; ?></p></td>
					</tr><? 				
				?>
				</table>
			</div>
		<br>
	</div>
    <br>
</div>
<?
exit();
}
?>