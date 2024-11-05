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

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------
//item search------------------------------//
if ($action == "item_description_search") {
    echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>

        var selected_id = new Array;
        var selected_name = new Array;
        var selected_no = new Array;

        function check_all_data() {
            var tbl_row_count = document.getElementById('list_view').rows.length;
            tbl_row_count = tbl_row_count - 1;
            for (var i = 1; i <= tbl_row_count; i++) {
                var onclickString = $('#tr_' + i).attr('onclick');
                var paramArr = onclickString.split("'");
                var functionParam = paramArr[1];
                js_set_value(functionParam);

            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(strCon)
        {
            var splitSTR = strCon.split("_");
            var str = splitSTR[0];
            var selectID = splitSTR[1];
            var selectDESC = splitSTR[2];

            toggle(document.getElementById('tr_' + str), '#FFFFCC');

            if (jQuery.inArray(selectID, selected_id) == -1) {
                selected_id.push(selectID);
                selected_name.push(selectDESC);
                selected_no.push(str);
            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == selectID)
                        break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
                selected_no.splice(i, 1);
            }
            var id = '';
            var name = '';
            var job = '';
            var num = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
                num += selected_no[i] + ',';
            }
            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);
            num = num.substr(0, num.length - 1);

            $('#txt_selected_id').val(id);
            $('#txt_selected').val(name);
            $('#txt_selected_no').val(num);
        }

        function fn_check_lot()
        {
            show_list_view(document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_' +<? echo $company; ?>, 'create_lot_search_list_view', 'search_div', 'item_ledger_report_controller', 'setFilterGrid("list_view",-1)');
        }
    </script>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>                	 
                            <th>Search By</th>
                            <th align="center" width="200" id="search_by_td_up">Enter Lot Number</th>
                            <th>
                                <input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                                <input type='hidden' id='txt_selected_id' />
                                <input type='hidden' id='txt_selected' />
                                <input type='hidden' id='txt_selected_no' />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr align="center">
                            <td align="center">
                                <?
                                $search_by = array(1 => 'Lot No', 2 => 'Item Description');
                                $dd = "change_search_event(this.value, '0*0', '0*0', '../../')";
                                echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
                                ?>
                            </td>
                            <td width="180" align="center" id="search_by_td">				
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />				
                            </td>
                        </tr>
                    </tbody>
                    </tr>         
                </table>    
                <div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
            </form>
        </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
    </html>
    <?
    /* $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$company and item_category_id=1"; 
      //echo $sql;
      $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
      $arr=array(1=>$supplier_arr);
      echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description","70,160,70","600","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "","setFilterGrid('list_view',-1)","0","",1) ;
      echo "<input type='hidden' id='txt_selected_id' />";
      echo "<input type='hidden' id='txt_selected' />";
      echo "<input type='hidden' id='txt_selected_no' />"; */
    ?>
    <script language="javascript" type="text/javascript">
                                    /*var style_no='<? echo $txt_produc_no; ?>';
                                     var style_id='<? echo $txt_produc_id; ?>';
                                     var style_des='<? echo $txt_product; ?>';
                                     //alert(style_id);
                                     if(style_no!="")
                                     {
                                     style_no_arr=style_no.split(",");
                                     style_id_arr=style_id.split(",");
                                     style_des_arr=style_des.split(",");
                                     var str_ref="";
                                     for(var k=0;k<style_no_arr.length; k++)
                                     {
                                     str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
                                     js_set_value(str_ref);
                                     }
                                     }*/
    </script>
    <?
    exit();
}

if ($action == "create_lot_search_list_view") {
    $ex_data = explode("_", $data);
    $txt_search_by = $ex_data[0];
    $txt_search_common = trim($ex_data[1]);
    $company = $ex_data[2];

    $sql_cond = "";
    if (trim($txt_search_common) != "") {
        if (trim($txt_search_by) == 1) { // for LOT NO
            $sql_cond = " and lot LIKE '%$txt_search_common%'";
        } else if (trim($txt_search_by) == 2) { // for Yarn Count
            $sql_cond = " and product_name_details LIKE '%$txt_search_common%'";
        }
    }

    $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$company and item_category_id=1 $sql_cond";
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $arr = array(1 => $supplier_arr);
    echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description", "70,160,70", "600", "260", 0, $sql, "js_set_value", "id,product_name_details", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "", "", "0", "", 1);

    exit();
}

//report generated here--------------------//
if ($action == "generate_report") {

    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    //print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
    $search_cond = "";
    if ($db_type == 0) {
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
    }
    else {
        if ($from_date != "" && $to_date != "")
            $search_cond .= " and a.transaction_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
    }

    $lot = str_replace("'", "", trim($txt_lot_no));
    if (str_replace("'", "", trim($txt_lot_no)) != "")
        $search_string = " and b.lot='$lot'";
    else
        $search_string = "";

    //library array-------------------
    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");

    // receive MRR array------------------------------------------------RECEIVE_PURPOSE
//	$sql_receive_mrr = "select a.id as trid, a.transaction_type, b.recv_number, b.knitting_source, b.knitting_company 
//			from inv_transaction a, inv_receive_master b
//			where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
    $sql_receive_mrr = "select a.id as trid, a.transaction_type, b.recv_number, b.knitting_source, b.knitting_company, b.supplier_id, b.receive_purpose
			from inv_transaction a, inv_receive_master b
			where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";



    $result_rcv = sql_select($sql_receive_mrr);
    //echo $sql_receive_mrr; die;
    $receiveMRR = array();
    $trWiseReceiveMRR = array();
    foreach ($result_rcv as $row) {
        $receiveMRR[$row[csf("trid")] . $row[csf("transaction_type")]] = $row[csf("recv_number")];
        $trWiseReceiveMRR[$row[csf("trid")]] = $row[csf("recv_number")];
        $receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_source"] = $row[csf("knitting_source")];
        $receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_company"] = $row[csf("knitting_company")];
        $receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["knitting_supplier"] = $row[csf("supplier_id")];
        $receive_source[$row[csf("trid")] . $row[csf("transaction_type")]]["receive_purpose"] = $yarn_issue_purpose[$row[csf("receive_purpose")]];
    }
    //var_dump($receive_source);die;
    // issue MRR array------------------------------------------------		
    $sql_issue_mrr = "select a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose
			from inv_transaction a, inv_issue_master b
			where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (2,3) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $result_iss = sql_select($sql_issue_mrr);
    $issueMRR = array();
    $issuePupose = array();
    foreach ($result_iss as $row) {
        $issueMRR[$row[csf("trid")] . $row[csf("transaction_type")]] = $row[csf("issue_number")];
        $issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]];
    }

    $transMrrArr = return_library_array("select id,transfer_system_id from  inv_item_transfer_mst", "id", "transfer_system_id");


    // var_dump($issueMRR);
    // var_dump($issuePupose);
    //array join or merge here ------------- do not delete or change
    $mrrArray = array();
    $mrrArray = $receiveMRR + $issueMRR;

    //var_dump($mrrArray);
    ?>
    <fieldset>
        <?
        if ($cbo_method == 0) { //average rate #########################################################################
            //Master Query---------------------------------------------------- 
            /* $sql = "select a.*, b.product_name_details,b.unit_of_measure,b.lot,c.knit_dye_source,c.knit_dye_company,c.issue_purpose
              from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3), product_details_master b
              where a.prod_id in ($txt_product_id) and a.prod_id=b.id and a.item_category=1 $search_cond order by a.transaction_date,a.prod_id ASC"; */
            if ($from_date != "" && $to_date != "") {
                if ($db_type == 2)
                    $from_date = date("j-M-Y", strtotime($from_date));
                if ($db_type == 0)
                    $from_date = change_date_format($from_date, 'yyyy-mm-dd');
                //for opening balance
                $sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
			SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
			SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_amount ELSE 0 END) as rcv_balance,
			SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_amount ELSE 0 END) as iss_balance
			from inv_transaction
			where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 group by prod_id";
                $trResult = sql_select($sqlTR);
            }
            $opning_bal_arr = array();
            foreach ($trResult as $row) {
                $opning_bal_arr[$row[csf("prod_id")]]["prod_id"] = $row[csf("prod_id")];
                $opning_bal_arr[$row[csf("prod_id")]]["receive"] = $row[csf("receive")];
                $opning_bal_arr[$row[csf("prod_id")]]["issue"] = $row[csf("issue")];
                $opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"] = $row[csf("rcv_balance")];
                $opning_bal_arr[$row[csf("prod_id")]]["iss_balance"] = $row[csf("iss_balance")];
            }
            //var_dump($opning_bal_arr);die;
//		$sql = "select a.id, a.mst_id, a.prod_id, a.transaction_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, a.cons_reject_qnty, b.product_name_details, b.unit_of_measure, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
//				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
//				where a.prod_id in ($txt_product_id) and a.prod_id=b.id $search_string and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $search_cond order by a.prod_id, a.id ASC";		
//			
            $sql = "select a.id, a.mst_id, a.prod_id, a.transaction_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, a.cons_reject_qnty, b.product_name_details, b.unit_of_measure, b.lot, b.supplier_id,c.knit_dye_source, c.knit_dye_company, c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.prod_id in ($txt_product_id) and a.prod_id=b.id $search_string and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $search_cond order by a.prod_id, a.transaction_date, a.id ASC";

            //echo $sql;die;
            $result = sql_select($sql);
            $checkItemArr = array();
            $balQnty = $balValue = array();
            $rcvQnty = $rcvValue = $issQnty = $issValue = 0;
            $i = 1;
            ob_start();
            ?>

            <div> 
                <table style="width:1570px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                    <thead>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Yarn Item Ledger </td> 
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>                                
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                            <td colspan="12" align="center"><b>Weighted Average Method</b></td>
                        </tr> 
                        <tr>
                            <th width="50" rowspan="2">SL</th>
                            <th width="80" rowspan="2">Buyer</th>
                            <th width="80" rowspan="2">Order No</th>
                            <th width="80" rowspan="2">Styles</th>
                            <th width="80" rowspan="2">Trans Date</th>
                            <th width="120" rowspan="2">Trans Ref No</th>
                            <th width="100" rowspan="2">Trans Type</th>
                            <th width="100" rowspan="2">Purpose</th>
                            <th width="100" rowspan="2">Trans With</th>
                            <th width="" colspan="3">Receive</th>
                            <th width="" colspan="3">Issue</th>
                            <th width="" colspan="3">Balance</th>                    
                        </tr>
                        <tr>
                            <th width="80">Qnty</th>
                            <th width="60">Rate</th>
                            <th width="110">Value</th>
                            <th width="80">Qnty</th>
                            <th width="60">Rate</th>
                            <th width="110">Value</th>
                            <th width="80">Qnty</th>
                            <th width="60">Rate</th>
                            <th width="">Value</th>
                        </tr>
                    </thead>
                </table>  
                <div style="width:1570px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
                    <table style="width:1550px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >   
                        <?
                        $m = 1;
                        $product_id_arr = array();
                        $k = 1;

                        foreach ($result as $row) {
                            $pro_id = $row[csf("prod_id")];

                            //check items new or not and print product description-------------------
                            if (!in_array($row[csf("prod_id")], $checkItemArr)) {

                                if ($i != 1) { // product wise sum/total here------------
                                    ?>                                
                                    <tr class="tbl_bottom">
                                        <td colspan="9" align="right">Total</td>
                                        <td><? echo number_format($rcvQnty, 2); ?></td><td></td><td><? echo number_format($rcvValue, 2); ?></td>
                                        <td><? echo number_format($issQnty, 2); ?></td><td></td><td><? echo number_format($issValue, 2); ?></td>                                    
                                        <td>&nbsp;</td><td></td><td>&nbsp;</td>
                                    </tr>

                                    <!-- product wise herder -->
                                    <thead>
                                        <tr>
                                            <td colspan="12"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $row[csf("product_name_details")] . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                                            <td colspan="6" align="center">&nbsp;</td>
                                        </tr>
                                    </thead>
                                    <!-- product wise herder END -->
                                    <?
                                }


                                //opening balance query-----------
                                /* if( $from_date!="" && $to_date!="" ) 
                                  {
                                  if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date));
                                  if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd');
                                  //for opening balance
                                  $sqlTR = "select prod_id, SUM(CASE WHEN transaction_type in (1,4) THEN cons_quantity ELSE 0 END) as receive,
                                  SUM(CASE WHEN transaction_type in (2,3) THEN cons_quantity ELSE 0 END) as issue,
                                  SUM(CASE WHEN transaction_type in (1,4) THEN cons_amount ELSE 0 END) as rcv_balance,
                                  SUM(CASE WHEN transaction_type in (2,3) THEN cons_amount ELSE 0 END) as iss_balance
                                  from inv_transaction
                                  where prod_id in ($pro_id) and transaction_date < '".$from_date."' and status_active=1 and is_deleted=0 group by prod_id,id";
                                  $trResult = sql_select($sqlTR);
                                  } */
                                //echo $sqlTR ;die;



                                $flag = 0;
                                $opening_qnty = $opening_balance = $opening_rate = 0;
                                if ($opning_bal_arr[$pro_id]['prod_id'] != "") {
                                    ?>

                                    <tr style="background-color:#FFFFCC">
                                        <td colspan="12" align="right"><b>Opening Balance</b></td>  
                                        <?
                                        $opening_qnty = $opning_bal_arr[$pro_id]['receive'] - $opning_bal_arr[$pro_id]['issue'];
                                        $opening_balance = $opning_bal_arr[$pro_id]['rcv_balance'] - $opning_bal_arr[$pro_id]['iss_balance'];
                                        $opening_rate = $opening_balance / $opening_qnty;
                                        ?>
                                        <td width="80" align="right"><? echo number_format($opening_qnty, 2); ?></td>
                                        <td width="60" align="right"><? echo number_format($opening_rate, 2); ?></td>
                                        <td width="" align="right"><? echo number_format($opening_balance, 2); ?></td>              
                                    </tr>

                                    <?
                                    $balQnty[$opning_bal_arr[$pro_id]['prod_id']] = $opening_qnty;
                                    $balValue [$opning_bal_arr[$pro_id]['prod_id']] = $opening_balance;

                                    $flag = 1;
                                    $opening_qnty = 0;
                                    $opening_balance = 0;
                                } // end opening balance foreach 	

                                $checkItemArr[$row[csf("prod_id")]] = $row[csf("prod_id")];
                                $rcvQnty = $rcvValue = $issQnty = $issValue = 0; // initialize variable
                                //$balQnty=$balValue=0;	
                                $total_balQnty = 0;
                                $total_balValue = 0;
                            }
                            //var_dump($balQnty);							
                            //print product name details header---------------------------
                            if ($i == 1) {
                                ?> 
                                <thead>
                                    <tr>
                                        <td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $row[csf("product_name_details")] . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                                        <td colspan="6" align="center"></td>
                                    </tr>
                                </thead> 
                                <?
                            }
                            //print product name details header END -------------------------	


                            /* if($flag==1) // adjust opening balance
                              {
                              $balQnty = $balQnty+$opening_qnty;
                              $balValue = $balValue+$opening_balance;
                              }
                              else
                              {
                              $flag=0;
                              } */


                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3)
                                $stylecolor = 'style="color:#A61000"';
                            else
                                $stylecolor = 'style="color:#000000"';
                            //var_dump($balQnty); 
                            /* if(!in_array($row[csf("prod_id")],$each_pro_id))
                              { */

                            $cons_amount = $cons_amount = 0;
                            $cons_qnty = $row[csf("cons_quantity")] + $row[csf("cons_reject_qnty")];
                            $cons_amount = $cons_qnty * $row[csf("cons_rate")];

                            if (!in_array($row[csf("prod_id")], $product_id_arr)) {
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="50"><? echo $i; ?></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
                                    <td width="120">
                                        <p>
                                            <?
                                            if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
                                                echo $transMrrArr[$row[csf("mst_id")]];
                                            } else {
                                                echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]];
                                            }
                                            ?>
                                        </p>
                                    </td>
                                    <td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>

                                    <?
                                    if ($row[csf("transaction_type")] == 1 ) {
                                        $issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                                    } elseif ($row[csf("transaction_type")] == 2) {
                                        $issuePuposeS = $issuePupose[$row[csf("id")]];
                                    }
                                    ?>
                                    <td width="100"><p><? echo $issuePuposeS ?></p></td>

                                    <?
                                    if ($row[csf("transaction_type")] == 2) {
                                        if ($row[csf("knit_dye_source")] == 1)
                                            $transactionWith = $companyArr[$row[csf("knit_dye_company")]];
                                        else
                                            $transactionWith = $supplierArr[$row[csf("knit_dye_company")]];
                                    }
                                     else if ($row[csf("transaction_type")] == 3) {
                                       $transactionWith = $supplierArr[$row[csf("supplier_id")]];                                            
                                    }
                                    else if ($row[csf("transaction_type")] == 1) {
                                        $transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                                    } else if ($row[csf("transaction_type")] == 4) {
                                        if ($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_source"] == 1)
                                            $transactionWith = $companyArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                                        else
                                            $transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                                    }
                                    ?>
                                    <td width="100"><p><? echo $transactionWith; ?></p></td> 
                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>              

                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                                    <?
                                    $each_pro_id = array();
                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balQnty = $balQnty[$row[csf("prod_id")]] + $cons_qnty;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balQnty = $balQnty[$row[csf("prod_id")]] - $cons_qnty;

                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balValue = $balValue[$row[csf("prod_id")]] + $cons_amount;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balValue = $balValue[$row[csf("prod_id")]] - $cons_amount;

                                    //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                                    //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;
                                    //$total_balQnty=number_format($total_balQnty,2,'.','');
                                    //$total_balValue=number_format($total_balValue,2,'.','');

                                    $total_balQnty = number_format($total_balQnty, 4, '.', '');
                                    $total_balValue = number_format($total_balValue, 2, '.', '');
                                    if ($total_balQnty < 0.00009) {
                                        $bal_rate = 0;
                                        $total_balValue = 0.00;
                                    } else {
                                        $bal_rate = $total_balValue / $total_balQnty;
                                    }
                                    ?> 
                                    <td width="80" align="right"><? echo $total_balQnty; ?></td>
                                    <td width="60" align="right"><? echo number_format($bal_rate, 2); ?></td>
                                    <td width="" align="right"><? echo $total_balValue; ?></td>                 
                                </tr>
                                <?
                                $k++;
                                $product_id_arr[] = $row[csf("prod_id")];
                            } else {
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="50"><? echo $i; ?></td>	
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"></td>
                                    <td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
                                    <td width="120">
                                        <p>
                                            <?
                                            if ($row[csf("transaction_type")] == 5 || $row[csf("transaction_type")] == 6) {
                                                echo $transMrrArr[$row[csf("mst_id")]];
                                            } else {
                                                echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]];
                                            }
                                            ?>
                                        </p>
                                    </td>
                                    <td width="100" title="<?echo $row[csf("transaction_type")];?>"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>

                                    <?
                                    if ($row[csf("transaction_type")] == 1 ) {
                                        $issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                                    } else if ($row[csf("transaction_type")] == 2) {
                                        $issuePuposeS = $issuePupose[$row[csf("id")]];
                                    }
                                    ?>
                                    <td width="100"><p><? echo $issuePuposeS ?></p></td>
                                    <?
                                    if ($row[csf("transaction_type")] == 2 ) {
                                        if ($row[csf("knit_dye_source")] == 1)
                                            $transactionWith = $companyArr[$row[csf("knit_dye_company")]];
                                        else
                                            $transactionWith = $supplierArr[$row[csf("knit_dye_company")]];
                                    }
                                    else if ($row[csf("transaction_type")] == 3) {
                                       $transactionWith = $supplierArr[$row[csf("supplier_id")]];                                            
                                    }
                                    
                                    else if ($row[csf("transaction_type")] == 1) {
                                        $transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                                    } else if ($row[csf("transaction_type")] == 4) {
                                        if ($receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_source"] == 1)
                                            $transactionWith = $companyArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_company"]];
                                        else
                                        //$transactionWith =  $supplierArr[$receive_source[$row[csf("id")].$row[csf("transaction_type")]]["knitting_company"]];
                                            $transactionWith = $supplierArr[$receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["knitting_supplier"]];
                                    }
                                    ?>

                                    <td width="100"><p><? echo $transactionWith; ?></p></td> 
                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>              

                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                                    <?
                                    $each_pro_id = array();
                                    $total_balQnty = str_replace(",", "", $total_balQnty);
                                    $total_balValue = str_replace(",", "", $total_balValue);
                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balQnty += $cons_qnty;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balQnty -= $cons_qnty;

                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balValue += $cons_amount;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balValue -= $cons_amount;

                                    //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                                    //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;
                                    //if(
                                    //$total_balQnty=number_format($total_balQnty,2,'.','');
                                    //$total_balValue=number_format($total_balValue,2,'.','');
                                    //$x=$total_balValue/$total_balQnty;

                                    $total_balQnty = number_format($total_balQnty, 4, '.', '');
                                    $total_balValue = number_format($total_balValue, 2, '.', '');
                                    if ($total_balQnty < 0.00009) {
                                        $bal_rate = 0;
                                        $total_balValue = 0.00;
                                    } else {
                                        $bal_rate = $total_balValue / $total_balQnty;
                                    }
                                    ?> 
                                    <td width="80" align="right"><? echo $total_balQnty; ?></td>
                                    <td width="60" align="right"><? echo number_format($bal_rate, 2); ?></td>
                                    <td width="" align="right"><? echo $total_balValue; ?></td>              
                                </tr>
                                <?
                            }


                            //$total_balQnty=0;
                            //$total_balValue=0;

                            $i++;

                            //total sum START-----------------------
                            if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                $rcvQnty += $cons_qnty;
                            if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                $rcvValue += $cons_amount;

                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                $issQnty += $cons_qnty;
                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                $issValue += $cons_amount;

                            /* 		//total sum END-----------------------
                              $each_pro_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
                              $m++;
                              }
                              $total_balQnty=0;
                              $total_balValue=0; */
                        }
                        ?> <!---- END FOREACH LOOP-----> 


                        <tr class="tbl_bottom">
                            <td colspan="6" align="right">Total</td>
                            <td align="right" ><? echo number_format($rcvQnty, 2); ?></td><td></td><td align="right" ><? echo number_format($rcvValue, 2); ?></td>
                            <td align="right" ><? echo number_format($issQnty, 2); ?></td><td></td><td align="right" ><? echo number_format($issValue, 2); ?></td>                                    
                            <td>&nbsp;</td><td></td><td>&nbsp;</td>
                        </tr>  
                    </table> 
                </div>  
            </div>    
            <?
        }

        if ($cbo_method == 1 || $cbo_method == 2) { //FIFO=1 //LIFO=2 ################################################################################ 
            if ($from_date != "" && $to_date != "") {
                if ($db_type == 2)
                    $from_date = date("j-M-Y", strtotime($from_date));
                if ($db_type == 0)
                    $from_date = change_date_format($from_date, 'yyyy-mm-dd');
                //for opening balance
                $sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
			SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
			SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_amount ELSE 0 END) as rcv_balance,
			SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_amount ELSE 0 END) as iss_balance
			from inv_transaction
			where transaction_date < '" . $from_date . "' and status_active=1 and is_deleted=0 group by prod_id";
                $trResult = sql_select($sqlTR);
            }
            $opning_bal_arr = array();
            foreach ($trResult as $row) {
                $opning_bal_arr[$row[csf("prod_id")]]["prod_id"] = $row[csf("prod_id")];
                $opning_bal_arr[$row[csf("prod_id")]]["receive"] = $row[csf("receive")];
                $opning_bal_arr[$row[csf("prod_id")]]["issue"] = $row[csf("issue")];
                $opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"] = $row[csf("rcv_balance")];
                $opning_bal_arr[$row[csf("prod_id")]]["iss_balance"] = $row[csf("iss_balance")];
            }

//		$sql = "select a.id,a.prod_id,a.transaction_date,a.transaction_type,a.cons_quantity,a.cons_rate,a.cons_amount, b.product_name_details,b.unit_of_measure,b.lot,c.knit_dye_source,c.knit_dye_company,c.issue_purpose
//				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
//				where a.prod_id in ($txt_product_id) and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $search_cond order by  a.prod_id,a.transaction_date ASC";
//		
            $sql = "select a.id,a.prod_id,a.transaction_date,a.transaction_type,a.cons_quantity,a.cons_rate,a.cons_amount,b.supplier_id, b.product_name_details,b.unit_of_measure,b.lot,c.knit_dye_source,c.knit_dye_company,c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.prod_id in ($txt_product_id) and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $search_cond order by  a.prod_id,a.transaction_date,a.id ASC";

            //echo $sql;die;
            $result = sql_select($sql);

            $checkItemArr = array();
            $balQnty = $balValue = 0;
            $rcvQnty = $rcvValue = $issQnty = $issValue = 0;
            $balMRRArray = $qntyMRRArray = $amtMRRArray = array();
            $deductQntyArr = $deductAmtArr = array();
            $i = 1;
            ob_start();
            ?>
            <div> 
                <table style="width:1330px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                    <thead>
                        <tr class="form_caption" style="border:none;">
                            <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" >Yarn Item Ledger </td> 
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>                                
                            </td>
                        </tr>
                        <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
        <? if ($from_date != "" || $to_date != "") echo "From " . change_date_format($from_date) . " To " . change_date_format($to_date) . ""; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                            <td colspan="9" align="center"><b>Weighted Average Method</b></td>
                        </tr> 
                        <tr>
                            <th width="50" rowspan="2">SL</th>
                            <th width="80" rowspan="2">Trans Date</th>
                            <th width="120" rowspan="2">Trans Ref No</th>
                            <th width="100" rowspan="2">Trans Type</th>
                            <th width="100" rowspan="2">Purpose</th>
                            <th width="100" rowspan="2">Trans With</th>
                            <th width="" colspan="3">Receive</th>
                            <th width="" colspan="3">Issue</th>
                            <th width="" colspan="3">Balance</th>                    
                        </tr>
                        <tr>
                            <th width="80">Qnty</th>
                            <th width="60">Rate</th>
                            <th width="110">Value</th>
                            <th width="80">Qnty</th>
                            <th width="60">Rate</th>
                            <th width="110">Value</th>
                            <th width="80">Qnty</th>
                            <th width="60">Rate</th>
                            <th width="">Value</th>
                        </tr>
                    </thead>
                </table>  
                <div style="width:1330px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
                    <table style="width:1310px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >   
                        <?
                        $m = 1;
                        $product_id_arr = array();
                        $k = 1;
                        foreach ($result as $row) {
                            $pro_id = $row[csf("prod_id")];

                            //check items new or not and print product description-------------------
                            if (!in_array($row[csf("prod_id")], $checkItemArr)) {

                                if ($i != 1) { // product wise sum/total here------------
                                    ?>                                
                                    <tr class="tbl_bottom">
                                        <td colspan="6" align="right">Total</td>
                                        <td><? echo number_format($rcvQnty, 2); ?></td><td></td><td><? echo number_format($rcvValue, 2); ?></td>
                                        <td><? echo number_format($issQnty, 2); ?></td><td></td><td><? echo number_format($issValue, 2); ?></td>                                    
                                        <td>&nbsp;</td><td></td><td>&nbsp;</td>
                                    </tr>

                                    <!-- product wise herder -->
                                    <thead>
                                        <tr>
                                            <td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $row[csf("product_name_details")] . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                                            <td colspan="6" align="center">&nbsp;</td>
                                        </tr>
                                    </thead>
                                    <!-- product wise herder END -->
                                    <?
                                }


                                //opening balance query-----------
                                /* if( $from_date!="" && $to_date!="" ) 
                                  {
                                  if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date));
                                  if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd');
                                  //for opening balance
                                  $sqlTR = "select prod_id, SUM(CASE WHEN transaction_type in (1,4) THEN cons_quantity ELSE 0 END) as receive,
                                  SUM(CASE WHEN transaction_type in (2,3) THEN cons_quantity ELSE 0 END) as issue,
                                  SUM(CASE WHEN transaction_type in (1,4) THEN cons_amount ELSE 0 END) as rcv_balance,
                                  SUM(CASE WHEN transaction_type in (2,3) THEN cons_amount ELSE 0 END) as iss_balance
                                  from inv_transaction
                                  where prod_id in ($pro_id) and transaction_date < '".$from_date."' and status_active=1 and is_deleted=0 group by prod_id,id";
                                  $trResult = sql_select($sqlTR);
                                  } */
                                //echo $sqlTR ;die;		

                                $flag = 0;
                                $opening_qnty = $opening_balance = $opening_rate = 0;
                                if ($opning_bal_arr[$pro_id]['prod_id'] != "") {
                                    ?>

                                    <tr style="background-color:#FFFFCC">
                                        <td colspan="12" align="right"><b>Opening Balance</b></td>  
                                        <?
                                        $opening_qnty = $opning_bal_arr[$pro_id]['receive'] - $opning_bal_arr[$pro_id]['issue'];
                                        $opening_balance = $opning_bal_arr[$pro_id]['rcv_balance'] - $opning_bal_arr[$pro_id]['iss_balance'];
                                        $opening_rate = $opening_balance / $opening_qnty;
                                        ?>
                                        <td width="80" align="right"><? echo number_format($opening_qnty, 2); ?></td>
                                        <td width="60" align="right"><? echo number_format($opening_rate, 2); ?></td>
                                        <td width="" align="right"><? echo number_format($opening_balance, 2); ?></td>              
                                    </tr>

                                    <?
                                    $balQnty[$opning_bal_arr[$pro_id]['prod_id']] = $opening_qnty;
                                    $balValue [$opning_bal_arr[$pro_id]['prod_id']] = $opening_balance;

                                    $flag = 1;
                                    $opening_qnty = 0;
                                    $opening_balance = 0;
                                } // end opening balance foreach 	

                                $checkItemArr[$row[csf("prod_id")]] = $row[csf("prod_id")];
                                $rcvQnty = $rcvValue = $issQnty = $issValue = 0; // initialize variable
                                //$balQnty=$balValue=0;	
                                $total_balQnty = 0;
                                $total_balValue = 0;
                            }
                            //var_dump($balQnty);							
                            //print product name details header---------------------------
                            if ($i == 1) {
                                ?> 
                                <thead>
                                    <tr>
                                        <td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")] . " , " . $row[csf("product_name_details")] . ", Lot#" . $row[csf("lot")] . ", UOM#" . $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                                        <td colspan="6" align="center"></td>
                                    </tr>
                                </thead> 
                                <?
                            }

                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3)
                                $stylecolor = 'style="color:#A61000"';
                            else
                                $stylecolor = 'style="color:#000000"';
                            $cons_amount = $cons_amount = 0;
                            $cons_qnty = $row[csf("cons_quantity")] + $row[csf("cons_reject_qnty")];
                            $cons_amount = $cons_qnty * $row[csf("cons_rate")];
                            if (!in_array($row[csf("prod_id")], $product_id_arr)) {
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="50"><? echo $i; ?></td>								
                                    <td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
                                    <td width="120"><p><? echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]]; ?></p></td>
                                    <td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
                                    <? if ($row[csf("transaction_type")] == 1 ) {
                                        $issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                                    } elseif ($row[csf("transaction_type")] == 2) {
                                        $issuePuposeS = $issuePupose[$row[csf("id")]];
                                    }
                                    ?>
                                    <td width="100"><p><? echo $issuePuposeS; ?></p></td>

                                    <?
                                    if ($row[csf("knit_dye_source")] == 1)
                                        $transactionWith = $companyArr[$row[csf("knit_dye_company")]];
                                    else
                                        $transactionWith = $supplierArr[$row[csf("supplier_id")]];
                                    ?>

                                    <td width="100"><p><? echo $transactionWith; ?></p></td> 
                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>              

                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                                    <?
                                    $each_pro_id = array();


                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balQnty = $balQnty[$row[csf("prod_id")]] + $cons_qnty;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balQnty = $balQnty[$row[csf("prod_id")]] - $cons_qnty;

                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balValue = $balValue[$row[csf("prod_id")]] + $cons_amount;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balValue = $balValue[$row[csf("prod_id")]] - $cons_amount;

                                    //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                                    //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;

                                    $total_balQnty = number_format($total_balQnty, 4, '.', '');
                                    $total_balValue = number_format($total_balValue, 2, '.', '');
                                    if ($total_balQnty < 0.00009) {
                                        $bal_rate = 0;
                                        $total_balValue = 0.00;
                                    } else {
                                        $bal_rate = $total_balValue / $total_balQnty;
                                    }
                                    ?> 
                                    <td width="80" align="right"><? echo number_format($total_balQnty, 2); ?></td>
                                    <td width="60" align="right"><? echo number_format($bal_rate, 2); ?></td>
                                    <td width="" align="right"><? echo number_format($total_balValue, 2); ?></td>              
                                </tr>
                                <?
                                $k++;
                                $product_id_arr[] = $row[csf("prod_id")];
                            } else {
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="50"><? echo $i; ?></td>								
                                    <td width="80"><? echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
                                    <td width="120"><p><? echo $mrrArray[$row[csf("id")] . $row[csf("transaction_type")]]; ?></p></td>
                                    <td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
                                    
                                    <?
                                     if ($row[csf("transaction_type")] == 1 ) {
                                        $issuePuposeS = $receive_source[$row[csf("id")] . $row[csf("transaction_type")]]["receive_purpose"];
                                    } elseif ($row[csf("transaction_type")] == 2) {
                                        $issuePuposeS = $issuePupose[$row[csf("id")]];
                                    }
                                    ?>
                                    <td width="100"><p><? echo $issuePuposeS; ?></p></td>

                                    <?
                                    if ($row[csf("knit_dye_source")] == 1)
                                        $transactionWith = $companyArr[$row[csf("knit_dye_company")]];
                                    else
                                        $transactionWith = $supplierArr[$row[csf("supplier_id")]];
                                    ?>

                                    <td width="100"><p><? echo $transactionWith; ?></p></td> 
                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5) echo number_format($cons_amount, 2); ?></td>              

                                    <td width="80" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_qnty, 2); ?></td>
                                    <td width="60" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($row[csf("cons_rate")], 2); ?></td>
                                    <td width="110" align="right"><? if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6) echo number_format($cons_amount, 2); ?></td>
                                    <?
                                    $each_pro_id = array();


                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balQnty += $cons_qnty;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balQnty -= $cons_qnty;

                                    if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                        $total_balValue += $cons_amount;
                                    if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                        $total_balValue -= $cons_amount;

                                    //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                                    //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;

                                    $total_balQnty = number_format($total_balQnty, 4, '.', '');
                                    $total_balValue = number_format($total_balValue, 2, '.', '');
                                    if ($total_balQnty < 0.00009) {
                                        $bal_rate = 0;
                                        $total_balValue = 0.00;
                                    } else {
                                        $bal_rate = $total_balValue / $total_balQnty;
                                    }
                                    ?> 
                                    <td width="80" align="right"><? echo number_format($total_balQnty, 2); ?></td>
                                    <td width="60" align="right"><? echo number_format($bal_rate, 2); ?></td>
                                    <td width="" align="right"><? echo number_format($total_balValue, 2); ?></td>              
                                </tr>
                                <?
                            }
                            $i++;
                            //total sum START-----------------------
                            if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                $rcvQnty += $cons_qnty;
                            if ($row[csf("transaction_type")] == 1 || $row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5)
                                $rcvValue += $cons_amount;

                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                $issQnty += $cons_qnty;
                            if ($row[csf("transaction_type")] == 2 || $row[csf("transaction_type")] == 3 || $row[csf("transaction_type")] == 6)
                                $issValue += $cons_amount;
                        }
                        ?> <!---- END FOREACH LOOP-----> 
                        <tr class="tbl_bottom">
                            <td colspan="6" align="right">Total</td>
                            <td align="right" ><? echo number_format($rcvQnty, 2); ?></td><td></td><td align="right" ><? echo number_format($rcvValue, 2); ?></td>
                            <td align="right" ><? echo number_format($issQnty, 2); ?></td><td></td><td align="right" ><? echo number_format($issValue, 2); ?></td>       
                            <td>&nbsp;</td><td></td><td>&nbsp;</td>
                        </tr>  
                    </table> 
                </div>  
            </div> 
            <?
        }
        ?>
    </fieldset>  
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
        //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    exit();
}
?>

