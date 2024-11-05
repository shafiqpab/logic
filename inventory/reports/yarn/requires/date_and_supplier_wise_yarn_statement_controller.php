<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_supplier") {
    if($data) $cond=  "and sc.tag_company in ($data)"; else $cond = "";
    echo create_drop_down("cbo_supplier_name", 140, "select s.id, s.supplier_name from LIB_SUPPLIER s, LIB_SUPPLIER_PARTY_TYPE sp, LIB_SUPPLIER_TAG_COMPANY sc where s.id = sp.supplier_id and s.id = sc.supplier_id and sp.party_type = 2 $cond and s.is_deleted = 0 and s.status_active = 1 group by s.id, s.supplier_name order by s.supplier_name", "id,supplier_name", 0, "-- Select Supplier --", 0, "");
    exit();
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_supplier_name','0','0','','0');\n";
    exit();
}
if ($action == "lot_no_search") {
    echo load_html_head_contents("Lot No Info", "../../../../", 1, 1, $unicode, "");
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(str) {

            var splitData = str.split("_");
            $("#hidden_product").val(splitData[0]); // wo/pi id
            $("#hidden_lot").val(splitData[1]);

            parent.emailwindow.hide();
        }
    </script> 
    <input type="hidden" value="" id="hidden_product">
    <input type="hidden" value="" id="hidden_lot">
    <?
    $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$cbo_company_name and item_category_id=1 and supplier_id = $cbo_supplier_name";
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $arr = array(1 => $supplier_arr);
    echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description", "70,160,70", "600", "260", 0, $sql, "js_set_value", "id,lot", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "", "setFilterGrid('list_view',-1)", "0", "", "");
    ?> 

    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 

    <?
}

if ($action == "generate_report") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $cbo_company_name = str_replace("'", "", trim($cbo_company_name));
    $cbo_supplier_name = str_replace("'", "", trim($cbo_supplier_name)); 

    $search_cond = "";
    if ($cbo_company_name) $search_cond .= " and a.company_id in ($cbo_company_name)"; 
    if ($db_type == 0) {
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.receive_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
    }
    else {
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.receive_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
    }

    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");

    if($rpt_type==2)
    {
        $order_by_cond = "a.company_id,a.receive_date,";
    }
    else
    {
        $order_by_cond = "a.supplier_id,";
    }

    $sql = "select a.id as sys_id,a.supplier_id, a.receive_date,b.transaction_date, a.recv_number,a.recv_number_prefix_num,a.challan_no, b.cons_quantity, c.yarn_count_id, d.yarn_count, c.yarn_comp_type1st, c.yarn_type, a.company_id
    from inv_receive_master a, inv_transaction b,product_details_master c, lib_yarn_count d
    where a.id = b.mst_id and b.prod_id = c.id and c.yarn_count_id= d.id and a.entry_form = 1 and b.transaction_type in (1)
    and b.item_category=1 and a.supplier_id in ($cbo_supplier_name) $search_cond and a.status_active =1 and b.status_active=1 and c.status_active=1 
    order by $order_by_cond d.sequence_no, c.yarn_comp_type1st , c.yarn_type"; //and a.recv_number = 'FAL-YRV-18-00029'

    $result = sql_select($sql);
    
    ob_start();

    if($rpt_type == 1)
    {
        foreach ($result as $val) 
        {
            $prod_des_key = $val[csf("yarn_count")]."*".$val[csf("yarn_comp_type1st")]."*".$val[csf("yarn_type")];

            $data_array[$val[csf("supplier_id")]][$prod_des_key][$val[csf("company_id")]] += $val[csf("cons_quantity")];

            
            if($comp_count_arr[$val[csf("supplier_id")]][$val[csf("company_id")]] =="")
            {
                $comp_count_arr[$val[csf("supplier_id")]][$val[csf("company_id")]] = $val[csf("company_id")];
                $comp_count[$val[csf("supplier_id")]]++;
            }
        }
    }
    else if($rpt_type ==2)
    {
        foreach ($result as $val) 
        {
            $data_array[$val[csf("company_id")]][$val[csf("receive_date")]."*".$val[csf("recv_number_prefix_num")]."*".$val[csf("challan_no")]][$val[csf("yarn_count")]] += $val[csf("cons_quantity")];

            if($ycount_count_arr[$val[csf("company_id")]][$val[csf("yarn_count")]] =="")
            {
                $ycount_count_arr[$val[csf("company_id")]][$val[csf("yarn_count")]] = $val[csf("yarn_count")];
                $y_count_count[$val[csf("company_id")]]++;
            }
        }
    }
    else
    {
        foreach ($result as $val) 
        {
            $data_array[$val[csf("supplier_id")]][$val[csf("company_id")]] += $val[csf("cons_quantity")];

            if($comp_count_arr[$val[csf("company_id")]] =="")
            {
                $comp_count_arr[$val[csf("company_id")]] = $val[csf("company_id")];
                
            }
        }
    }

    //echo "<pre>";
    //print_r($comp_count_arr);die;
    //echo count($comp_count_arr);die;

    if($rpt_type==1)
    {
        foreach ($data_array as $supplier_id => $supplier_data) 
        {
            ?>
            <div style="float:left; padding-right: 300px;margin: 5px 15px;"> 
                <fieldset  style="width:<? echo 500+$comp_count[$supplier_id]*150;?>px; float:left;">
                    <table style="width:<? echo 500+$comp_count[$supplier_id]*150;?>px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                        <thead>
                            <tr>
                                <td colspan="<? echo 5+$comp_count[$supplier_id]*1;?>" align="center" style="font-size:16px; font-weight:bold" ><? echo $supplierArr[$supplier_id];?></td> 
                            </tr>
                            <tr>
                                <td colspan="<? echo 5+$comp_count[$supplier_id]*1;?>" align="center" style="font-size:16px; font-weight:bold" >Statement of Yarn Receive</td> 
                            </tr>
                            <tr>
                                <td colspan="<? echo 5+$comp_count[$supplier_id];?>" align="center" style="font-weight: bold; border: none;">
                                    <? if ($from_date != "" && $to_date != "") echo "<br/>DATED ON - $from_date To $to_date"; ?> 
                                </td> 
                            </tr>
                        </thead>
                    </table>
                    <br/>
                    <table style="width:<? echo 500+$comp_count[$supplier_id]*150;?>px" border="1" cellpadding="0" align="left" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <tr>
                                <th width="50"> Sl</th>
                                <th width="100"> Count </th>
                                <th width="150">Composition</th>
                                <th width="100">Type</th>
                                <? foreach ($comp_count_arr[$supplier_id] as $val) 
                                {
                                    ?>
                                    <th width="150"><? echo $companyArr[$val];?></th>
                                    <?
                                } ?>

                                <th width="100">Total</th>
                            </tr>
                        </thead>
                    </table>

                    <div style="width:<? echo 520+$comp_count[$supplier_id]*150;?>px;  max-height:250px; overflow-y: scroll;" class="scroll_body" id="scroll_body" align="left"> 
                        <table style="width:<? echo 500+$comp_count[$supplier_id]*150;?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
                            <tbody>
                                <? $i=1;
                                foreach ($supplier_data as $prod_str => $prod_str_data) 
                                {
                                    if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                                    $prod_str_arr = explode("*", $prod_str);
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i.'*'.$supplier_id; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i.'*'.$supplier_id;; ?>">
                                        <td width="50" align="center"><? echo $i; ?></td>
                                        <td width="100" align="center"><? echo $prod_str_arr[0]; ?> &nbsp;</td>
                                        <td width="150" align="center"><? echo $composition[$prod_str_arr[1]] ?></td>
                                        <td width="100" align="center"><? echo $yarn_type[$prod_str_arr[2]]; ?></td>
                                        <? 
                                        $row_total=0;
                                        foreach ($comp_count_arr[$supplier_id] as $val)
                                        {
                                            ?>
                                            <td width="150" align="right"><? echo $prod_str_data[$val]; ?></td>
                                            <?
                                            $row_total +=  $prod_str_data[$val];

                                            $total_supplier_compnay_qnty[$supplier_id][$val] += $prod_str_data[$val];
                                        }
                                        ?>
                                        <td align="right" width="100" align="right"><? echo $row_total; ?></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th align="right" colspan="4">Total</th>
                                    <? 
                                    $sub_row_total=0;
                                    foreach ($comp_count_arr[$supplier_id] as $val) 
                                    {
                                        ?>
                                        <th align="right"><? echo $total_supplier_compnay_qnty[$supplier_id][$val];?></th>
                                        <?
                                        $sub_row_total += $total_supplier_compnay_qnty[$supplier_id][$val];
                                    } 
                                    ?>

                                    <th align="right"><? echo $sub_row_total;?></th>
                                </tr>
                            </tfoot>
                        </table> 
                    </div> 
                </fieldset>
            </div>
            <br>
            <?
        }
        ?>
        <div style="width: 1000px; float: left;margin: 25px 15px">
            <table style="font-weight: bold;" align="left">
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td width="100">Prepared By</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">Sr.Inch(Store)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">Manager(Store)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">AGM(Knitting)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">Sr. GM(Mkt)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">DMD</td>
                </tr>
            </table>
        </div>
        <?
    }
    else if($rpt_type==2)
    {
        foreach ($data_array as $company_id => $company_data) 
        {
            ?>
            <div style="float:left; padding-right: 300px;margin: 5px 15px;"> 
                <fieldset  style="width:<? echo 450+$y_count_count[$company_id]*70;?>px; float:left;">
                    <table style="width:<? echo 450+$y_count_count[$company_id]*70;?>px; float: left;" border="1" cellpadding="1" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                        <thead>

                            <tr style="border:none;">
                                <td colspan="<? echo 5+$y_count_count[$company_id]*1;?>" align="center" style="font-size:16px; font-weight:bold" >Count Wise Receive Details</td> 
                            </tr>
                            <tr style="border:none;">
                                <td colspan="<? echo 5+$y_count_count[$company_id];?>" align="center" style="font-weight: bold; border: none;">
                                    <? if ($from_date != "" && $to_date != "") echo "DATED ON - $from_date To $to_date"; ?> 
                                </td> 
                            </tr>
                            <tr style="border:none;">
                                <td colspan="<? echo 5+$y_count_count[$company_id]*1;?>" align="center" style="font-size:16px; font-weight:bold" ><? echo $companyArr[$company_id];?></td> 
                            </tr>
                        </thead>
                    </table>
                    <table style="width:<? echo 450+$y_count_count[$company_id]*70;?>px" border="1" cellpadding="0" align="left" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <tr>
                                <th width="50"> Sl</th>
                                <th width="100"> Date </th>
                                <th width="100">Sys.Challan</th>
                                <th width="100">Challan No</th>
                                <? foreach ($ycount_count_arr[$company_id] as $val) 
                                {
                                    ?>
                                    <th width="70"><? echo $val;?></th>
                                    <?
                                } ?>

                                <th width="100">Total</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:<? echo 470+$y_count_count[$company_id]*70;?>px;  max-height:250px; overflow-y: scroll;" class="scroll_body" id="scroll_body" align="left" > 
                        <table style="width:<? echo 450+$y_count_count[$company_id]*70;?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
                            <tbody>
                                <? $i=1; 
                                foreach ($company_data as $date_sys => $count_data) 
                                {
                                    if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                                    $date_sys_arr = explode("*", $date_sys);
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i.'*'.$company_id; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i.'*'.$company_id; ?>">
                                        <td width="50"><? echo $i;?></td>
                                        <td width="100" align="center"><? echo $date_sys_arr[0];?></td>
                                        <td width="100" align="center"><? echo $date_sys_arr[1];?></td>
                                        <td width="100" align="center"><? echo $date_sys_arr[2];?></td>
                                        <? $row_grand_tot=0;
                                        foreach ($ycount_count_arr[$company_id] as $val) 
                                        {
                                            ?>
                                            <td width="70" align="right"><p><? echo $count_data[$val];?></p></td>
                                            <?
                                            $row_grand_tot += $count_data[$val];
                                            $grand_tot_arr[$company_id][$val] += $count_data[$val];
                                        } ?>
                                        <td align="right" width="100"><? echo number_format($row_grand_tot,2);?></td>
                                    </tr>
                                    <? 
                                    $i++;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" width="350">Count Total: </th>
                                    <? 
                                    $total_grand_count=0;
                                    foreach ($ycount_count_arr[$company_id] as $val) 
                                    {
                                        ?>
                                        <th align="right" width="70"><p><? echo $grand_tot_arr[$company_id][$val];?></p></th>
                                        <?
                                        $total_grand_count +=$grand_tot_arr[$company_id][$val];
                                    } ?>

                                    <th align="right" width="100"><p><? echo number_format($total_grand_count,2);?></p></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </fieldset>
            </div>
            <br>
            <? 
        }
        ?>

        <div style="width: 1000px; float: left;margin: 25px 15px">
            <table style="font-weight: bold;" align="left">
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td width="100">Prepared By</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">Sr.Inch(Store)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">Manager(Store)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">AGM(Knitting)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">Sr. GM(Mkt)</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">DMD</td>
                </tr>
            </table>
        </div>
        <?
    }
    else if($rpt_type==3)
    {

        ?>
        <div style="width: 1000px; float: left;margin: 25px 15px">
            <fieldset  style="width:<? echo 320+count($comp_count_arr)*170;?>px; float:left;">
                <table style="width:<? echo 300+count($comp_count_arr)*170;?>px; float: left;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                    <thead>
                        <tr>
                            <td colspan="<? echo 3+count($comp_count_arr)*1;?>" align="center" style="font-size:16px; font-weight:bold" >Statement of Yarn Receive</td> 
                        </tr>
                        <tr>
                            <td colspan="<? echo 3+count($comp_count_arr);?>" align="center" style="font-weight: bold; border: none;">
                                <? if ($from_date != "" && $to_date != "") echo "DATED ON - $from_date To $to_date"; ?> 
                            </td> 
                        </tr>
                    </thead>
                </table>
                <br/>
                <table style="width:<? echo 300+count($comp_count_arr)*170;?>px" border="1" cellpadding="0" align="left" cellspacing="0" class="rpt_table" rules="all" >
                    <thead>
                        <tr>
                            <th width="50"> Sl</th>
                            <th width="120"> Supplier </th>                        
                            <? foreach ($comp_count_arr as  $val) 
                            {
                                ?>
                                <th width="170"><? echo $companyArr[$val];?></th>
                                <?
                            } 
                            ?>
                            <th width="130">Total</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:<? echo 320+count($comp_count_arr)*170;?>px"  max-height:250px" id="scroll_body" align="left"> 
                    <table style="width:<? echo 300+count($comp_count_arr)*170;?>px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left" >
                        <tbody>
                            <?
                            $i=1;
                            foreach ($data_array as $supplier_id => $row) 
                            {
                             if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                             ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i.'*'.$supplier_id; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i.'*'.$supplier_id;; ?>">
                                <td width="50"><? echo $i;?></td>
                                <td width="120"><? echo $supplierArr[$supplier_id];?></td>
                                <?
                                $row_total=0;
                                foreach  ($comp_count_arr as  $val) 
                                {
                                   ?>
                                   <td width="170" align="right"><? echo $row[$val];?></td>
                                   <?
                                   $row_total += $row[$val];
                                   $com_total_arr[$val] += $row[$val];
                               }
                               ?>
                               <td align="right" width="130"><? echo $row_total;?></td>
                           </tr>
                           <?
                           $i++;   
                       }
                       ?>
                   </tbody>
                   <tfoot>
                    <tr>
                        <th colspan="2">Grand Total:</th>
                        <?
                        foreach  ($comp_count_arr as  $val) 
                        {
                           ?>
                           <th align="right"><? echo $com_total_arr[$val];?></th>
                           <?
                           $grand_total += $com_total_arr[$val];
                       }
                       ?>
                       <th align="right" width="130"><? echo $grand_total;?></th>
                   </tr>
               </tfoot>
           </table>
       </div>
   </fieldset>
</div>
<div style="width: 1000px; float: left;margin: 25px 15px">
    <table style="font-weight: bold;" align="left">
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td width="100">Prepared By</td>
            <td width="100">&nbsp;</td>
            <td width="100">Sr.Inch(Store)</td>
            <td width="100">&nbsp;</td>
            <td width="100">Manager(Store)</td>
            <td width="100">&nbsp;</td>
            <td width="100">AGM(Knitting)</td>
            <td width="100">&nbsp;</td>
            <td width="100">Sr. GM(Mkt)</td>
            <td width="100">&nbsp;</td>
            <td width="100">DMD</td>
        </tr>
    </table>
</div>
<?
}
$html = ob_get_contents();
ob_clean();
foreach (glob("*.xls") as $filename) {
    @unlink($filename);
}
$name = time();
$filename = $user_id . "_" . $name . ".xls";
$create_new_doc = fopen($filename, 'w');
$is_created = fwrite($create_new_doc, $html);
echo "$html**$filename";
exit();
}
?>