<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

if ($action == "report_generate")
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    $cbo_company = str_replace("'", "", $cbo_company_name);
    $cbo_category = str_replace("'", "", $cbo_category_name);
    $date_from = str_replace("'", "", $txt_date_from);
    $date_to = str_replace("'", "", $txt_date_to);

    $company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
    $category_arr=return_library_array("SELECT category_id, short_name from lib_item_category_list",'category_id','short_name');

    $search_cond = ''; $from_date = ''; $to_date = '';
    if ($cbo_company)
    {
        $search_cond .= " and a.COMPANY_ID=$cbo_company ";
    }
    if ($cbo_category)
    {
        $search_cond .= " and b.CATEGORY_ID=$cbo_category ";
    }
    if ($date_from != '' && $date_to != '')
    {
        if ($db_type == 0) {
            $from_date = change_date_format($date_from, 'yyyy-mm-dd');
            $to_date = change_date_format($date_to, 'yyyy-mm-dd');
        } else if ($db_type == 2) {
            $from_date = change_date_format($date_from, '', '', -1);
            $to_date = change_date_format($date_to, '', '', -1);
        }
        $search_cond .= " and a.APPLYING_DATE_FROM='$from_date' and a.APPLYING_DATE_TO='$to_date' ";
    }

    $main_sql = "SELECT a.ID, a.COMPANY_ID, b.CATEGORY_ID, b.BUDGET_AMOUNT
    from LIB_CATEGORY_BUDGET_MST a, LIB_CATEGORY_BUDGET_DTLS b
    where a.ID=b.MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $search_cond";
    $main_result = sql_select($main_sql);
    $main_array = array();
    foreach ($main_result as $row)
    {
        $main_array[$row['COMPANY_ID']][$row['CATEGORY_ID']]['ID'] = $row['ID'];
        $main_array[$row['COMPANY_ID']][$row['CATEGORY_ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
        $main_array[$row['COMPANY_ID']][$row['CATEGORY_ID']]['CATEGORY_ID'] = $row['CATEGORY_ID'];
        $main_array[$row['COMPANY_ID']][$row['CATEGORY_ID']]['BUDGET_AMOUNT'] = $row['BUDGET_AMOUNT'];
    }

    $sql = "SELECT c.ID, d.AMOUNT, d.ITEM_CATEGORY, c.COMPANY_ID
    from INV_PURCHASE_REQUISITION_MST c,  INV_PURCHASE_REQUISITION_dtls d
    where c.ID=d.MST_ID and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and
    c.COMPANY_ID=$cbo_company and c.REQUISITION_DATE between '$from_date' and '$to_date' and c.ENTRY_FORM = 69";

    $result = sql_select($sql);
    $main_array2 = array();
    foreach ($result as $row)
    {
        $main_array2[$row['COMPANY_ID']][$row['ITEM_CATEGORY']]['REQUISITION_AMOUNT'] += $row['AMOUNT'];
    }

    $table_width = 600;
    ob_start();
    ?>
    <style>
        .wrd_brk {
            word-break: break-all;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }
    </style>

    <body>
        <div style="width:100%">
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
                <thead>
                    <tr>
                        <th colspan="5"><?= $company_arr[$cbo_company]; ?></th>
                    </tr>
                    <tr>
                        <th width="40">SL No</th>
                        <th width="160">Category</th>
                        <th width="120">Budget Amt.</th>
                        <th width="120">Requisition Amt.</th>
                        <th>Balance Amt.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i = 1;
                    foreach ($main_array as $company_id => $category_data)
                    {
                        foreach ($category_data as $category_id => $row)
                        {
                            if ($i % 2 == 0) {
                                $bgcolor = "#E9F3FF";
                            } else {
                                $bgcolor = "#FFFFFF";
                            }
                        
                            $requisition_amount=$main_array2[$company_id][$category_id]['REQUISITION_AMOUNT'];
                            if($requisition_amount=="") $requisition_amount=0;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>" style="text-decoration:none; cursor:pointer">
                                <td class="wrd_brk center"><?= $i; ?></td>
                                <td class="wrd_brk left"><?= $category_arr[$row['CATEGORY_ID']]; ?></td>
                                <td class="wrd_brk right"><?= $row['BUDGET_AMOUNT']; ?></td>
                                <td class="wrd_brk right"><a href='#report_detals' onclick="openmypage('<?= $company_id; ?>','<?= $row['ID'] ?>','<?= $row['CATEGORY_ID'] ?>');"><? echo number_format($requisition_amount,2); ?></a></td>
                                <td class="wrd_brk right"><?= number_format($row['BUDGET_AMOUNT']-$requisition_amount,2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>
    </body>
    <?

    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old)) @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data****$filename";
    exit();
}

if ($action == "req_details")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $company_id = $company_id;
    $budget_id = $budget_id;
    $category_id = $category_id;

    $approved_status = array("0" => "Un-Approved", "1" => "Fully Approved", "3" => "Partillay Approved");
    $category_arr=return_library_array("SELECT category_id, short_name from lib_item_category_list",'category_id','short_name');

    $main_sql = "SELECT c.REQU_NO, c.IS_APPROVED, c.REQUISITION_DATE, d.ITEM_CATEGORY, d.AMOUNT as REQUISITION_AMOUNT
    from LIB_CATEGORY_BUDGET_DTLS b, LIB_CATEGORY_BUDGET_MST a
    left join INV_PURCHASE_REQUISITION_MST c on c.COMPANY_ID=a.COMPANY_ID and c.REQUISITION_DATE between a.APPLYING_DATE_FROM and a.APPLYING_DATE_TO
    left join INV_PURCHASE_REQUISITION_DTLS d on d.MST_ID=c.ID
    where a.ID=b.MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and a.ID=$budget_id and a.COMPANY_ID=$company_id and b.CATEGORY_ID=d.ITEM_CATEGORY and b.CATEGORY_ID=$category_id and c.ENTRY_FORM = 69
    group by c.REQU_NO, c.IS_APPROVED, c.REQUISITION_DATE, d.ITEM_CATEGORY, d.AMOUNT";

    $sql_result = sql_select($main_sql);
    ?>
    <script>
        function new_window() {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>' + document.getElementById('popup_body').innerHTML + '</body</html>');
            d.close();
        }
    </script>

    <table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <tr>
            <td align="center"><input type="button" class="formbutton" onClick="new_window()" style="width:100px;" value="Print"></td>
        </tr>
    </table><br>
    <div id="popup_body" style="width:520px;">
        <table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th colspan="5">Requisition Details</th>
                </tr>
                <tr>
                    <th colspan="5">Category Name: <? echo $category_arr[$category_id]; ?></th>
                </tr>
                <tr>
                    <th width="50">SL</th>
                    <th width="140">Requisition No</th>
                    <th width="100">Requisition Date</th>
                    <th width="100">Amount</th>
                    <th>Approval Status</th>
                </tr>
            </thead>
            <tbody>
                <?
                $i = 1;
                $total_amount = 0;
                foreach ($sql_result as $row)
                {
                    if ($i % 2 == 0) {
                        $bgcolor = "#E9F3FF";
                    } else {
                        $bgcolor = "#FFFFFF";
                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $row["REQU_NO"]; ?></td>
                        <td><? echo $row["REQUISITION_DATE"]; ?></td>
                        <td align="right"><? echo number_format($row["REQUISITION_AMOUNT"], 2); ?></td>
                        <td><? echo $approved_status[$row["IS_APPROVED"]]; ?></td>
                    </tr>
                    <?
                    $total_amount += $row["REQUISITION_AMOUNT"];
                    $i++;
                }
                ?>
                <tr bgcolor="#A2A2A2">
                    <td colspan="3" align="right"><strong>Total Amount</strong></td>
                    <td align="right"><strong><? echo number_format($total_amount, 2); ?></strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?
    exit();
}
?>