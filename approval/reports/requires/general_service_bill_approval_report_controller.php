<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");

if ($action == "load_drop_supplier") {
    echo create_drop_down("cbo_supplier_id", 150, "select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type in(1,7) and c.status_active=1 and c.is_deleted=0", 'id,supplier_name', 1, '-- All --', 0, '', 0);
    //and b.party_type =9
    exit();
}

if ($action == "load_drop_down_location") {
    echo create_drop_down("cbo_location", 130, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
    exit();
}

if ($action == 'outside_bill_popup') 
{
    echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode, '', '');
    $ex_data = explode('_', $data);
   // print_r($ex_data);
    ?>
    <script>
        function js_set_value(id) {
            document.getElementById('txt_bill_no').value = id;
            parent.emailwindow.hide();
        }
    </script>
    </head>

    <body>
        <div align="center" style="width:100%;">
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
                                <input type="hidden" id="txt_bill_no">
                                <?php
                                echo create_drop_down('cbo_company_id', 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $ex_data[0], "load_drop_down( 'general_service_bill_entry_controller', this.value, 'load_drop_supplier', 'supplier_td' );", 1);
                                ?>
                            </td>
                            <td width="140" id="supplier_td">
                                <?php
                                echo create_drop_down("cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$ex_data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=7) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $ex_data[1], "", "", "", "", "", "", 5);
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+<?echo $ex_data[2];?>, 'outside_yarn_service_bill_list_view', 'search_div', 'general_service_bill_approval_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
                                <?php echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id="">
                                <div id="search_div"></div>
                            </td>
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

if ($action == 'outside_yarn_service_bill_list_view') {
    $data = explode('_', $data);
    //print_r($data);
    if ($data[0] != 0) $company_cond = " and company_id='$data[0]'";
    else {
        echo "Please Select Company First.";
        die;
    }
    if ($data[1] != 0) $supplier_cond = " and supplier_id='$data[1]'";

    if ($db_type == 0) {
        if ($data[2] != "" &&  $data[3] != "") $trans_date_cond = "and bill_date between '" . change_date_format($data[2], 'yyyy-mm-dd') . "' and '" . change_date_format($data[3], 'yyyy-mm-dd') . "'";
        else $return_date = "";
    } else {
        if ($data[2] != "" &&  $data[3] != "") $trans_date_cond = "and bill_date between '" . change_date_format($data[2], "", "", 1) . "' and '" . change_date_format($data[3], "", "", 1) . "'";
        else $return_date = "";
    }
    if ($data[4] != '') $bill_id_cond = " and prefix_no_num='$data[4]'";
    else $bill_id_cond = "";
    if ($data[5] != '') $year_con_type = " and TO_CHAR(insert_date,'YYYY')='$data[5]'";
    else $year_con_type = "";

    $location = return_library_array('select id,location_name from lib_location', 'id', 'location_name');
    $supplier_library_arr = return_library_array('select id,supplier_name from lib_supplier', 'id', 'supplier_name');
    $arr = array(2 => $location, 4 => $supplier_library_arr, 5 => $yarn_issue_purpose, 6 => $production_process);

    if ($db_type == 0) {
        $year_cond = "year(insert_date)as year";
    } else if ($db_type == 2) {
        $year_cond = "TO_CHAR(insert_date,'YYYY') as year";
    }

    $sql = "select id, bill_no, prefix_no_num, $year_cond, party_bill_no, bill_date, supplier_id, bill_for
    from subcon_outbound_bill_mst
    where  entry_form=483 and status_active=1 $company_cond $supplier_cond $trans_date_cond $bill_id_cond $year_con_type
    order by id desc";
    // echo $sql;

    echo create_list_view('list_view', 'Bill No,Year,Party Bill No,Bill Date,Supplier,Bill For', '70,70,100,100,120,100', '600', '250', 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0,0,supplier_id,bill_for', $arr, 'prefix_no_num,year,party_bill_no,bill_date,supplier_id,bill_for', 'general_service_bill_approval_report_controller', '', '0,0,0,3,0,0');
    exit();
}

if ($action == 'load_php_data_to_form_outside_bill') {
    $sql = "SELECT min(receive_date) as min_date, max(receive_date) as max_date from subcon_outbound_bill_dtls where mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";

    $sql_result_arr = sql_select($sql);
    $mindate = '';
    $maxdate = '';
    $mindate = $sql_result_arr[0][csf('min_date')];
    $maxdate = $sql_result_arr[0][csf('max_date')];

    $nameArray = sql_select("SELECT id, entry_form, bill_no, is_approved, ready_to_approve from subcon_outbound_bill_mst where id='$data'");

    foreach ($nameArray as $row) {
        echo "document.getElementById('txt_bill_no').value = '" . $row[csf("bill_no")] . "';\n";
    }
    exit();
}

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $cbo_company_name = str_replace("'", "", $cbo_company_name);
    $cbo_location = str_replace("'", "", $cbo_location);
    $cbo_year = str_replace("'", "", $cbo_year);
    $cbo_supplier_id = str_replace("'", "", $cbo_supplier_id);
    $party_bill_no = str_replace("'", "", $txt_party_bill_no);
    $txt_bill_no = str_replace("'", "", $txt_bill_no);
    $cbo_date_type = str_replace("'", "", $cbo_date_type);
    $cbo_type = str_replace("'", "", $cbo_type);
    $txt_date_from = str_replace("'", "", $txt_date_from);
    $txt_date_to = str_replace("'", "", $txt_date_to);

    //echo $cbo_company_name."-".$cbo_company_name."-".$cbo_location."-".$cbo_year."-".$cbo_supplier_id."-".$party_bill_no."-".$party_bill_no."-".$cbo_date_type."-".$cbo_type."-".$txt_date_from."-".$txt_date_to;

    if ($cbo_company_name != 0) {
        $where_con .= " AND A.COMPANY_ID = $cbo_company_name";
    }
    if ($cbo_location != 0) {
        $where_con .= " AND A.LOCATION_ID = $cbo_location";
    }
    if ($cbo_year != 0) {
        $where_con .= " AND TO_CHAR(B.INSERT_DATE,'YYYY') =$cbo_year";
    }
    if ($cbo_supplier_id != 0) {
        $where_con .= " AND A.SUPPLIER_ID = $cbo_supplier_id";
    }
    if ($party_bill_no != '') {
        $where_con .= " AND A.PARTY_BILL_NO = '$party_bill_no'";
    }
    if ($txt_bill_no != '') {
        $where_con .= " AND A.BILL_NO LIKE '%$txt_bill_no%'";
    }
    //{$where_con .= " AND A.BILL_NO LIKE('%".$txt_bill_no."')"; }
    if ($cbo_type == 3) $type_con = " AND A.IS_APPROVED=1";
    elseif ($cbo_type == 1) $type_con = " AND A.IS_APPROVED=0";
    elseif ($cbo_type == 2) $type_con = " AND A.IS_APPROVED=3";
    elseif ($cbo_type == 0) $type_con = "";
    else $type_con = " AND A.IS_APPROVED IN (0,2)";

    if ($cbo_date_type != '' && $txt_date_from != "" && $txt_date_to != "") {
        $where_bill_con .= " AND A.BILL_DATE BETWEEN '" . $txt_date_from . "' AND '" . $txt_date_to . "'";
        $where_apprv_con .= " AND C.APPROVED_DATE BETWEEN '" . $txt_date_from . "' AND '" . $txt_date_to ." 11:59:59 PM'";
       // $date_cond=" and a.insert_date between '".$date_form."' and '".$date_to." 11:59:59 PM'"; //Oracle
    }

    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    $location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
    $designation_array = return_library_array("SELECT id, custom_designation from lib_designation", "id", "custom_designation");


    $user_name_array = array();
    $userData = sql_select("SELECT ID, USER_NAME, USER_FULL_NAME, DESIGNATION FROM user_passwd");
    foreach ($userData as $user_row) {
        $user_name_array[$user_row['ID']]['NAME'] = $user_row['USER_NAME'];
        $user_name_array[$user_row['ID']]['FULL_NAME'] = $user_row['USER_FULL_NAME'];
        $user_name_array[$user_row['ID']]['DESIGNATION'] = $designation_array[$user_row['DESIGNATION']];
    }

    $signatory_sql_res = sql_select("SELECT USER_ID, sequence_no, BYPASS from electronic_approval_setup where company_id=$cbo_company_name and is_deleted=0 and entry_form=71 order by sequence_no");

    foreach ($signatory_sql_res as $sval) {
        $signatory_data_arr[$sval['USER_ID']] = $sval['BYPASS'];
    }
    $rowspan = count($signatory_data_arr) == 0 ? 1 : count($signatory_data_arr);
    //echo "RowSpan: ". $rowspan; exit();
    if($cbo_date_type==1)
    {
    $sql = "SELECT A.ID,A.COMPANY_ID, A.BILL_DATE, A.LOCATION_ID,TO_CHAR(B.INSERT_DATE,'YYYY') AS YEAR,A.SUPPLIER_ID,A.PARTY_BILL_NO,A.BILL_NO, A.PAY_MODE,  A.CURRENCY_ID,A.IS_APPROVED, B.RECEIVE_QTY, B.RATE, B.AMOUNT, A.WO_NON_ORDER_INFO_MST_ID AS SERVICE_WO_ID, B.WO_NON_ORDER_INFO_DTLS_ID AS DTLS_ID, B.BILL_STATUS 
	FROM SUBCON_OUTBOUND_BILL_MST A, SUBCON_OUTBOUND_BILL_DTLS B 
	WHERE A.ID=B.MST_ID $where_con $type_con $where_bill_con AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ORDER BY A.ID DESC";
    }
    else{
    $sql = "SELECT A.ID,A.COMPANY_ID, A.BILL_DATE, A.LOCATION_ID,TO_CHAR(B.INSERT_DATE,'YYYY') AS YEAR,A.SUPPLIER_ID,A.PARTY_BILL_NO,A.BILL_NO, A.PAY_MODE,  A.CURRENCY_ID,A.IS_APPROVED, B.RECEIVE_QTY, B.RATE, B.AMOUNT, A.WO_NON_ORDER_INFO_MST_ID AS SERVICE_WO_ID, B.WO_NON_ORDER_INFO_DTLS_ID AS DTLS_ID, B.BILL_STATUS 
	FROM SUBCON_OUTBOUND_BILL_MST A, SUBCON_OUTBOUND_BILL_DTLS B ,APPROVAL_HISTORY C
	WHERE A.ID=B.MST_ID AND A.ID = C.MST_ID AND C.CURRENT_APPROVAL_STATUS=1 $where_con $type_con $where_apprv_con AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY  A.ID,A.COMPANY_ID, A.LOCATION_ID,TO_CHAR(B.INSERT_DATE,'YYYY'),A.SUPPLIER_ID,A.PARTY_BILL_NO,A.BILL_NO, A.PAY_MODE,  A.CURRENCY_ID,A.IS_APPROVED, B.RECEIVE_QTY, B.RATE, B.AMOUNT, A.WO_NON_ORDER_INFO_MST_ID , B.WO_NON_ORDER_INFO_DTLS_ID , B.BILL_STATUS  ORDER BY A.ID DESC
    ";
    }
     //echo $sql;die;
    $sql_result = sql_select($sql);
    foreach ($sql_result as $row) {
        $app_mst_id_arr[$row['ID']] = $row['ID'];
        $wo_dtls_id .= $row['SERVICE_WO_ID'] . ',';
    }
    $wo_dtls_id = ltrim(implode(",", array_unique(explode(",", chop($wo_dtls_id, ",")))), ',');
    if ($wo_dtls_id == "") $wo_dtls_id = 0;

    $service_sql = "SELECT A.ID, B.SERVICE_FOR, B.SERVICE_DETAILS , B.ITEM_CATEGORY_ID from WO_NON_ORDER_INFO_MST A, WO_NON_ORDER_INFO_DTLS B WHERE  A.ID=B.MST_ID AND  A.ID IN($wo_dtls_id) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
    //echo $service_sql;
    $sql_service_result = sql_select($service_sql);
    foreach ($sql_service_result as $row) {
        $service_arr[$row['ID']]['SERVICE_FOR'] = $row['SERVICE_FOR'];
    }

    $approved_no_array = array();
    $queryApp = "SELECT MST_ID, APPROVED_NO,INSERTED_BY, APPROVED_BY, APPROVED_DATE, USER_IP, ENTRY_FORM FROM APPROVAL_HISTORY WHERE ENTRY_FORM=71 AND CURRENT_APPROVAL_STATUS =1 AND UN_APPROVED_BY=0 " . where_con_using_array($app_mst_id_arr, 0, 'mst_id') . " ORDER BY ID";
    //echo $queryApp;
    $resultApp = sql_select($queryApp);
    foreach ($resultApp as $row) {
         $approved_no_name[$row['MST_ID']][$row['APPROVED_BY']] = $row['INSERTED_BY'];
         $approved_no_array[$row['MST_ID']][$row['APPROVED_BY']] = $row['APPROVED_NO'];
         $approved_date_array[$row['MST_ID']][$row[('APPROVED_BY')]] = $row['APPROVED_DATE'];
         $approved_ip_array[$row['MST_ID']][$row[('APPROVED_BY')]] = $row['USER_IP'];
    }
   // echo "<pre>";print_r($approved_no_name);
    $width = 1765;

    ob_start();
    ?>
    <fieldset style="width:<?= $width + 20; ?>px;">
        <table cellpadding="0" cellspacing="0" width="<?= $width; ?>">
            <tr>
                <td align="center" width="100%" colspan="17" style="font-size:20px"><strong><?= $report_title; ?></strong></td>
            </tr>
            <tr>
                <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><?= $company_arr[$cbo_company_name]; ?></strong></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left">
            <thead>
                <th width="35"><P>SL</P></th>
                <th width="120"><P>Company</P></th>
                <th width="120"><P>Location</P></th>
                <th width="80"><P>Year</P></th>
                <th width="100"><P>Service For</P></th>
                <th width="100"><P>Party Bill No</P></th>
                <th width="120"><P>Bill No</P></th>
                <th width="100"><P>Bill Date</P></th>
                <th width="80"><P>Pay Mode</P></th>
                <th width="80"><P>Currency</P></th>
                <th width="120"><P>Bill Amount</P></th>
                <th width="100"><P>Supplier</P></th>
                <th width="100"><P>Signatory</P></th>
                <th width="100"><P>Designation</P></th>
                <th width="100"><P>Approval User</P></th>
                <th width="100"><P>Approval Name</P></th>
                <th width="100"><P>Approval Date</P></th>
                <th width="100"><P>Approval Time</P></th>
            </thead>
        </table>
        <style>
            .cellContents {
                
            }
        </style>
        <div style="width:<?= $width + 20; ?>px; overflow-y:scroll; max-height:310px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                <tbody>
                    <?
                    $i = 1;
                    foreach ($sql_result as $row) 
                    {
                        //echo "<pre>";print_r($row);
                        $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                        ?>
                        <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
                            <td width="35" valign="middle" align="center" style="vertical-align: middle" rowspan="<?= $rowspan; ?>"><P><?= $i; ?></P>
                            </td>
                            <td width="120" rowspan="<?= $rowspan; ?>" valign="middle" align="center" style="vertical-align: middle">
                                <P><? echo $company_short_arr[$row['COMPANY_ID']]; ?></P>
                            </td>
                            <td width="120" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><? echo $location_arr[$row['LOCATION_ID']]; ?></P>
                            </td>
                            <td width="80" rowspan="<?= $rowspan; ?>" valign="middle" align="center" style="vertical-align: middle">
                                <P><?= $row['YEAR']; ?></P>
                            </td>
                            <td width="100" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><? echo  $service_for_arr[$service_arr[$row['SERVICE_WO_ID']]['SERVICE_FOR']]; ?></P>
                            </td>
                            <td width="100" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><? echo $row['PARTY_BILL_NO']; ?></P>
                            </td>
                            <td width="120" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><a href="#" onClick="general_service_print_report('<? echo $row['COMPANY_ID'];?>','<? echo $row['ID'];?>','<? echo $row['BILL_NO'];?>','')"><? echo $row['BILL_NO']; ?></a></P>
                            </td>

                            <td width="100" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><? echo $row['BILL_DATE']; ?></P>
                            </td>
                            <td width="80" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><? echo $pay_mode[$row['PAY_MODE']]; ?></P>
                            </td>
                            <td width="80" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><?= $currency[$row['CURRENCY_ID']]; ?></P>
                            </td>
                            <td width="120" rowspan="<?= $rowspan; ?>" align="right" valign="middle" style="vertical-align: middle">
                                <P><? echo number_format($row['AMOUNT'], 2); ?></P>
                            </td>
                            <td width="100" rowspan="<?= $rowspan; ?>" align="center" valign="middle" style="vertical-align: middle">
                                <P><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></P>
                            </td>
                            <?
                                $flag = 0;
                                if(count($signatory_data_arr) > 0){
                                    foreach ($signatory_data_arr as $signator => $bypass)
                                    {
                                        if ($flag == 1)
                                        {
                                            echo "<tr bgcolor='" . $bgcolor . "'>";
                                        }
                                        ?>
                                        <td width="100">
                                            <p><? echo $user_name_array[$signator]['NAME'];?></p>
                                        </td>
                                        <td width="100">
                                            <p><?= $user_name_array[$signator]['DESIGNATION']; ?></p>
                                        </td>
                                        <td width="100" align="center">
                                            <P><?
                                            echo $user_name_array[$approved_no_name[$row['ID']][$signator]]['NAME'];?></P>
                                        </td>
                                        <td width="100" align="center">
                                            <P><? echo $user_name_array[$approved_no_name[$row['ID']][$signator]]['FULL_NAME']; ?></P>
                                        </td>
                                        <td width="100" align="center">
                                            <P><? if ($approved_date_array[$row['ID']][$signator]) {echo date('d-m-y', strtotime($approved_date_array[$row['ID']][$signator]));} ?></P>
                                        </td>
                                        <td  width="100" align="center">
                                            <P><? if ($approved_date_array[$row['ID']][$signator]) {echo date('h-i-s', strtotime($approved_date_array[$row['ID']][$signator]));}?></P>
                                        </td>

                            
                                        <?php
                                        $flag = 1;
                                    }
                                        $total_amount += $row['AMOUNT'];
                                        $i++;
                                }else{
                                    ?>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                    <?
                                }
                                
                                    ?>
                        </tr>
                        <?php
                    } ?>
                    <tr>
                        <td colspan="9"></td>
                        <td align="right"><b>Total:</b></td>
                        <td align="right"><? echo number_format($total_amount, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <?
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
 	echo "$total_datass####$filename";
	exit();
}

//---------------------------END----------------------------

?>