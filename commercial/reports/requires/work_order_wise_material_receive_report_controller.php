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
    $cbo_wo_type = str_replace("'", "", $cbo_wo_type);
    $txt_wo_no = str_replace("'", "", $txt_wo_no);
    $cbo_job_year = str_replace("'", "", $cbo_job_year);

    $company_arr=return_library_array( "SELECT ID, COMPANY_NAME FROM LIB_COMPANY",'ID','COMPANY_NAME');
    $item_group_arr = return_library_array("SELECT ID, ITEM_NAME FROM LIB_ITEM_GROUP",'ID','ITEM_NAME');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
    
    $con = connect();
	$r_id1 = execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID=$user_id AND ENTRY_FORM IN (19900)");
	if($r_id1)
	{
		oci_commit($con);
	}
    // ======================== WO DETAILS START ========================
    $search_cond = '';
    if ($cbo_company)
    {
        $search_cond .= " AND a.COMPANY_NAME=$cbo_company ";
    }
    if ($cbo_wo_type=='1')
    {
        $search_cond .= " AND a.ENTRY_FORM=147";
    }
    else if ($cbo_wo_type=='2')
    {
        $search_cond .= " AND a.ENTRY_FORM=146";
    }
    if ($txt_wo_no != '')
    {
        $search_cond .= " AND a.WO_NUMBER_PREFIX_NUM=$txt_wo_no";
    }
    if ($cbo_job_year != '')
    {
        $search_cond .= " AND TO_CHAR(a.WO_DATE,'YYYY')=$cbo_job_year";
    }
    
    $main_sql = "SELECT a.ID, a.COMPANY_NAME,a.SUPPLIER_ID, a.WO_NUMBER, a.WO_DATE, b.SUPPLIER_ORDER_QUANTITY, b.RATE, b.AMOUNT, b.REMARKS, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION
    FROM WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b, PRODUCT_DETAILS_MASTER c
    WHERE a.ID=b.MST_ID AND b.ITEM_ID=c.ID AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0 AND c.STATUS_ACTIVE=1 AND c.IS_DELETED=0 $search_cond 
    ORDER BY a.WO_NUMBER";
     //echo $main_sql; die;
    $main_result = sql_select($main_sql);

    $main_array = array(); $wo_id_check = array(); $all_wo_id_arr = array();
    foreach ($main_result as $row)
    {    $supplier=$row['SUPPLIER_ID'];
        $main_array[$row['WO_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ID'] = $row['ID'];
        $main_array[$row['WO_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['WO_DATE'] = $row['WO_DATE'];
        $main_array[$row['WO_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['SUPPLIER_ORDER_QUANTITY'] += $row['SUPPLIER_ORDER_QUANTITY'];
        $main_array[$row['WO_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['RATE'] = $row['RATE'];
        $main_array[$row['WO_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['AMOUNT'] += $row['AMOUNT'];
        $main_array[$row['WO_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['REMARKS'] = $row['REMARKS'];

        if($wo_id_check[$row['ID']] == "")
        {
            $wo_id_check[$row['ID']] = $row['ID'];
            $all_wo_id_arr[$row['ID']] = $row['ID'];
        }
    }
    // echo "<pre>"; print_r($all_wo_id_arr); die;

    $wo_count=array();
    foreach ($main_array as $k_wo_number => $wo_val)
    {
        foreach ($wo_val as $k_item_group => $item_group_val)
        {
            foreach ($item_group_val as $k_item_description => $item_description_val)
            {
                $wo_count[$k_wo_number]++;
            }
        }
    }
    // ======================== WO DETAILS END ========================

    $all_wo_id_arr = array_filter($all_wo_id_arr);
    //var_dump()
    if(!empty($all_wo_id_arr))
    {
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 19900, 1,$all_wo_id_arr, $empty_arr);
        // die;

        // ======================== MATERIAL RECEIVE ========================
        $mrr_sql = "SELECT a.ID, a.BOOKING_ID, a.RECV_NUMBER, a.RECEIVE_DATE, a.CHALLAN_NO, b.PROD_ID, b.ORDER_QNTY, b.ORDER_RATE, b.ORDER_AMOUNT, b.REMARKS, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION
        FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c, GBL_TEMP_ENGINE d
        WHERE a.ID=b.MST_ID AND b.PROD_ID=c.ID AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND b.STATUS_ACTIVE=1 and b.IS_DELETED=0 AND a.ENTRY_FORM=20 and a.BOOKING_ID=d.REF_VAL and d.USER_ID=$user_id and d.ENTRY_FORM=19900
        ORDER BY a.RECV_NUMBER";
        // echo $mrr_sql; die;
        $mrr_result = sql_select($mrr_sql);
        // echo "<pre>"; print_r($mrr_result); die;

        // ==================== MATERIAL RECEIVE RETURN ====================
        $mrrr_sql = "SELECT a.ID, a.BOOKING_ID, a.ISSUE_NUMBER, a.ISSUE_DATE, b.PROD_ID, b.CONS_QUANTITY, b.RCV_RATE AS CONS_RATE, b.RCV_AMOUNT AS CONS_AMOUNT, a.REMARKS, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION
        FROM INV_ISSUE_MASTER a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c, GBL_TEMP_ENGINE d
        WHERE a.ID=b.MST_ID AND b.PROD_ID=c.ID AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0 AND c.STATUS_ACTIVE=1 AND c.IS_DELETED=0 AND a.ENTRY_FORM=26 AND b.TRANSACTION_TYPE=3 AND a.BOOKING_ID=d.REF_VAL AND d.USER_ID=$user_id AND d.ENTRY_FORM=19900
        ORDER BY a.ISSUE_NUMBER";
        // echo $mrrr_sql; die;
        $mrrr_result = sql_select($mrrr_sql);
        // echo "<pre>"; print_r($mrrr_result); die;
    }

    $mrr_array = array(); $mrr_id_check = array(); $all_mrr_id_arr = array(); $summary_array = array();
    foreach ($mrr_result as $row)
    {
        $mrr_array[$row['RECV_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['RECEIVE_DATE'] = $row['RECEIVE_DATE'];
        $mrr_array[$row['RECV_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['CHALLAN_NO'] = $row['CHALLAN_NO'];
        $mrr_array[$row['RECV_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ORDER_QNTY'] += $row['ORDER_QNTY'];
        $mrr_array[$row['RECV_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ORDER_RATE'] = $row['ORDER_RATE'];
        $mrr_array[$row['RECV_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ORDER_AMOUNT'] += $row['ORDER_AMOUNT'];
        $mrr_array[$row['RECV_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['REMARKS'] = $row['REMARKS'];

        $summary_array[$row['BOOKING_ID']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ORDER_QNTY'] += $row['ORDER_QNTY'];
        $summary_array[$row['BOOKING_ID']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ORDER_RATE'] = $row['ORDER_RATE'];
        $summary_array[$row['BOOKING_ID']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ORDER_AMOUNT'] += $row['ORDER_AMOUNT'];
        
        if($mrr_id_check[$row['ID']] == "")
        {
            $mrr_id_check[$row['ID']] = $row['ID'];
            $all_mrr_id_arr[$row['ID']] = $row['ID'];
        }
    }
    // echo "<pre>"; print_r($summary_array); die;

    $last_received_date_arr = end($mrr_array);
    foreach ($last_received_date_arr as $k_item_group => $item_group_val)
    {
        foreach ($item_group_val as $k_item_description => $item_description_val)
        {
            $last_received_date = $item_description_val['RECEIVE_DATE'];
        }
    }
    // echo $last_received_date; die;

    $mrr_count=array();
    foreach ($mrr_array as $k_mrr_number => $mrr_val)
    {
        foreach ($mrr_val as $k_item_group => $item_group_val)
        {
            foreach ($item_group_val as $k_item_description => $item_description_val)
            {
                $mrr_count[$k_mrr_number]++;
            }
        }
    }
    // ======================== MATERIAL RECEIVE END ========================

    // =================== MATERIAL RECEIVE RETURN START ====================

    $mrrr_array = array(); $mrrr_id_check = array(); $all_mrrr_id_arr = array();
    foreach ($mrrr_result as $row)
    {
        $mrrr_array[$row['ISSUE_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['ISSUE_DATE'] = $row['ISSUE_DATE'];
        $mrrr_array[$row['ISSUE_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['CONS_QUANTITY'] += $row['CONS_QUANTITY'];
        $mrrr_array[$row['ISSUE_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['CONS_RATE'] = $row['CONS_RATE'];
        $mrrr_array[$row['ISSUE_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['CONS_AMOUNT'] += $row['CONS_AMOUNT'];
        $mrrr_array[$row['ISSUE_NUMBER']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['REMARKS'] = $row['REMARKS'];

        $summary_array[$row['BOOKING_ID']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]['CONS_QUANTITY'] += $row['CONS_QUANTITY'];

        if($mrrr_id_check[$row['ID']] == "")
        {
            $mrrr_id_check[$row['ID']] = $row['ID'];
            $all_mrrr_id_arr[$row['ID']] = $row['ID'];
        }
    }
    // echo "<pre>"; print_r($summary_array); die;

    $mrrr_count=array();
    foreach ($mrrr_array as $k_mrrr_number => $mrrr_val)
    {
        foreach ($mrrr_val as $k_item_group => $item_group_val)
        {
            foreach ($item_group_val as $k_item_description => $item_description_val)
            {
                $mrrr_count[$k_mrrr_number]++;
            }
        }
    }
    // =================== MATERIAL RECEIVE RETURN END ====================
    $con = connect();
    $r_id111=execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID=$user_id AND ENTRY_FORM IN (19900)");
   
    if($r_id111)
    {
        oci_commit($con);
    }

    $table_width = 1000;
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
        <div style="<?= $table_width+20; ?>">
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header" >
                <thead>
                    <tr>
                        <th colspan="9">
                            <?= $company_arr[$cbo_company]; ?> <br>
                            <?= "Material Receive Report (MRR)"; ?> <br>
                            <?= "Last Received Date: ". change_date_format($last_received_date, 'd-m-Y'); ?><br>
                            <?= $supplier_arr[$supplier]; ?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="9">WO Details</th>
                    </tr>
                    <tr>
                        <th width="40">SL No</th>
                        <th width="160">WO NO</th>
                        <th width="120">WO Date</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Description</th>
                        <th width="100">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?= $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
                <table width="<?= $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" >
                    <tbody>
                        <?
                        $total_quantity = 0; $total_amount = 0; $wo_chk = array();
                        $i = 1;
                        foreach ($main_array as $k_wo_number => $wo_val)
                        {
                            foreach ($wo_val as $k_item_group => $item_group_val)
                            {
                                foreach ($item_group_val as $k_item_description => $item_description_val)
                                {
                                    if ($i % 2 == 0)
                                    {
                                        $bgcolor = "#E9F3FF";
                                    }
                                    else
                                    {
                                        $bgcolor = "#FFFFFF";
                                    }
                                    $wo_span = $wo_count[$k_wo_number];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>" style="text-decoration:none; cursor:pointer">
                                    <?
                                        if(!in_array($k_wo_number,$wo_chk))
                                        {
                                            $wo_chk[]=$k_wo_number;
                                        ?>
                                        <td width="40" valign="middle" class="wrd_brk center" rowspan="<? echo $wo_span; ?>"><?= $i; ?></td>
                                        <td width="160" valign="middle" class="wrd_brk center" rowspan="<? echo $wo_span; ?>"><?= $k_wo_number; ?></td>
                                        <td width="120" valign="middle" class="wrd_brk center" rowspan="<? echo $wo_span; ?>"><?= change_date_format($item_description_val['WO_DATE'], 'd-m-Y'); ?></td>
                                        <?
                                        }
                                        ?>
                                        <td width="100" class="wrd_brk center"><?= $item_group_arr[$k_item_group]; ?></td>
                                        <td width="100" class="wrd_brk center"><?= $k_item_description; ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['SUPPLIER_ORDER_QUANTITY'], 2); ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['RATE'], 2); ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['AMOUNT'], 2); ?></td>
                                        <td class="wrd_brk center"><?= $item_description_val['REMARKS']; ?></td>
                                    </tr>
                                    <?

                                    $total_quantity += $item_description_val['SUPPLIER_ORDER_QUANTITY'];
                                    $total_amount += $item_description_val['AMOUNT'];
                                }
                            }
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_footer" >
                <tfoot>
                    <tr>
                        <th width="40" >&nbsp;</th>
                        <th width="160" >&nbsp;</th>
                        <th width="120" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100" class="wrd_brk center">Total</th>
                        <th width="100" class="wrd_brk right"><?= number_format($total_quantity, 2); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" class="wrd_brk right"><?= number_format($total_amount, 2); ?></th>
                        <th class="wrd_brk center"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="<?= $table_width+20; ?>">
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header2">
                <thead>
                    <tr>
                        <th colspan="10">Material Receive</th>
                    </tr>
                    <tr>
                        <th width="40">SL No</th>
                        <th width="160">MRR No</th>
                        <th width="120">Recv. Date</th>
                        <th width="100">Challan No</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Description</th>
                        <th width="100">Receive Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Value</th>
                        <th>Comments</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?= $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body2">
                <table width="<?= $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" >
                    <tbody>
                        <?
                        $total_quantity = 0; $total_amount = 0; $mrr_chk = array();
                        $i = 1;
                        foreach ($mrr_array as $k_mrr_number => $mrr_val)
                        {
                            foreach ($mrr_val as $k_item_group => $item_group_val)
                            {
                                foreach ($item_group_val as $k_item_description => $item_description_val)
                                {
                                    if ($i % 2 == 0)
                                    {
                                        $bgcolor = "#E9F3FF";
                                    }
                                    else
                                    {
                                        $bgcolor = "#FFFFFF";
                                    }
                                    $mrr_span = $mrr_count[$k_mrr_number];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('mrr_tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="mrr_tr_<?= $i; ?>" style="text-decoration:none; cursor:pointer">
                                    <?
                                        if(!in_array($k_mrr_number,$mrr_chk))
                                        {
                                            $mrr_chk[]=$k_mrr_number;
                                        ?>
                                        <td width="40" valign="middle" class="wrd_brk center" rowspan="<? echo $mrr_span; ?>"><?= $i; ?></td>
                                        <td width="160" valign="middle" class="wrd_brk center" rowspan="<? echo $mrr_span; ?>"><?= $k_mrr_number; ?></td>
                                        <td width="120" valign="middle" class="wrd_brk center" rowspan="<? echo $mrr_span; ?>"><?= change_date_format($item_description_val['RECEIVE_DATE'], 'd-m-Y'); ?></td>
                                        <td width="100" valign="middle" class="wrd_brk center" rowspan="<? echo $mrr_span; ?>"><?= $item_description_val['CHALLAN_NO']; ?></td>
                                        <?
                                        }
                                        ?>
                                        <td width="100" class="wrd_brk center"><?= $item_group_arr[$k_item_group]; ?></td>
                                        <td width="100" class="wrd_brk center"><?= $k_item_description; ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['ORDER_QNTY'], 2); ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['ORDER_RATE'], 2); ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['ORDER_AMOUNT'], 2); ?></td>
                                        <td class="wrd_brk center"><?= $item_description_val['REMARKS']; ?></td>
                                    </tr>
                                    <?
                                    $total_quantity += $item_description_val['ORDER_QNTY'];
                                    $total_amount += $item_description_val['ORDER_AMOUNT'];
                                }
                            }
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_footer2" style="margin-right: 18px;">
                <tfoot>
                    <tr>
                        <th width="40" >&nbsp;</th>
                        <th width="160" >&nbsp;</th>
                        <th width="120" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100" class="wrd_brk center">Total</th>
                        <th width="100" class="wrd_brk right"><?= number_format($total_quantity, 2); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" class="wrd_brk right"><?= number_format($total_amount, 2); ?></th>
                        <th class="wrd_brk center"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="<?= $table_width+20; ?>">
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header3" style="margin-right: 17px;">
                <thead>
                    <tr>
                        <th colspan="10">Material Receive Return</th>
                    </tr>
                    <tr>
                        <th width="40">SL No</th>
                        <th width="160">Return No</th>
                        <th width="120">Return Date</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Description</th>
                        <th width="100">Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?= $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body3">
                <table width="<?= $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" >
                    <tbody>
                        <?
                        $total_quantity = 0; $total_amount = 0; $mrrr_chk = array();
                        $i = 1;
                        foreach ($mrrr_array as $k_mrrr_number => $mrrr_val)
                        {
                            foreach ($mrrr_val as $k_item_group => $item_group_val)
                            {
                                foreach ($item_group_val as $k_item_description => $item_description_val)
                                {
                                    if ($i % 2 == 0)
                                    {
                                        $bgcolor = "#E9F3FF";
                                    }
                                    else
                                    {
                                        $bgcolor = "#FFFFFF";
                                    }
                                    $mrrr_span = $mrrr_count[$k_mrrr_number];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('mrrr_tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="mrrr_tr_<?= $i; ?>" style="text-decoration:none; cursor:pointer">
                                    <?
                                        if(!in_array($k_mrrr_number,$mrrr_chk))
                                        {
                                            $mrrr_chk[]=$k_mrrr_number;
                                        ?>
                                        <td width="40" valign="middle" class="wrd_brk center" rowspan="<? echo $mrrr_span; ?>"><?= $i; ?></td>
                                        <td width="160" valign="middle" class="wrd_brk center" rowspan="<? echo $mrrr_span; ?>"><?= $k_mrrr_number; ?></td>
                                        <td width="120" valign="middle" class="wrd_brk center" rowspan="<? echo $mrrr_span; ?>"><?= change_date_format($item_description_val['ISSUE_DATE'], 'd-m-Y'); ?></td>
                                        <?
                                        }
                                        ?>
                                        <td width="100" class="wrd_brk center"><?= $item_group_arr[$k_item_group]; ?></td>
                                        <td width="100" class="wrd_brk center"><?= $k_item_description; ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['CONS_QUANTITY'], 2); ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['CONS_RATE'], 2); ?></td>
                                        <td width="100" class="wrd_brk right"><?= number_format($item_description_val['CONS_AMOUNT'], 2); ?></td>
                                        <td class="wrd_brk center"><?= $item_description_val['REMARKS']; ?></td>
                                    </tr>
                                    <?
                                    $total_quantity += $item_description_val['CONS_QUANTITY'];
                                    $total_amount += $item_description_val['CONS_AMOUNT'];
                                }
                            }
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" id="table_footer3" style="margin-right: 18px;">
                <tfoot>
                    <tr>
                        <th width="40" >&nbsp;</th>
                        <th width="160" >&nbsp;</th>
                        <th width="120" >&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100" class="wrd_brk center">Total</th>
                        <th width="100" class="wrd_brk right"><?= number_format($total_quantity, 2); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" class="wrd_brk right"><?= number_format($total_amount, 2); ?></th>
                        <th class="wrd_brk center"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="<?= $table_width+200; ?>">
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width+180; ?>" cellpadding="0" cellspacing="0" id="table_header4" >
                <thead>
                    <tr>
                        <th colspan="13">Summary of WO and Receive</th>
                    </tr>
                    <tr>
                        <th colspan="7">WO Summary</th>
                        <th colspan="6">Receive Summary</th>
                    </tr>
                    <tr>
                        <th width="40">SL No</th>
                        <th width="140">WO Number</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Description</th>
                        <th width="90">WO Qnty</th>
                        <!-- <th width="90">WO Rate</th> -->
                        <th width="90">WO Value</th>
                        <th width="90">Rcv. Qnty</th>
                        <th width="90">Return Qnty</th>
                        <th width="90">Payable Qnty</th>
                        <!-- <th width="90">Rcv. Rate</th> -->
                        <th width="90">Payable Value</th>
                        <th width="90">Rcv. Balance</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?= $table_width+200; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body4">
                <table width="<?= $table_width+180; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body4" >
                    <tbody>
                        <?
                        $total_quantity = 0; $total_amount = 0; $wo_chk = array(); $total_rcv_quantity = 0; $total_rcv_amount = 0;
                        $i = 1;
                        foreach ($main_array as $k_wo_number => $wo_val)
                        {
                            foreach ($wo_val as $k_item_group => $item_group_val)
                            {
                                foreach ($item_group_val as $k_item_description => $item_description_val)
                                {
                                    $rcv_qnty = $summary_array[$item_description_val['ID']][$k_item_group][$k_item_description]['ORDER_QNTY'];
                                    $rcv_rate = $summary_array[$item_description_val['ID']][$k_item_group][$k_item_description]['ORDER_RATE'];
                                    // echo $rcv_rate."___" ;
                                    $rcv_amount = $summary_array[$item_description_val['ID']][$k_item_group][$k_item_description]['ORDER_AMOUNT'];
                                    $rcv_return_qnty = $summary_array[$item_description_val['ID']][$k_item_group][$k_item_description]['CONS_QUANTITY'];
                                    if ($i % 2 == 0)
                                    {
                                        $bgcolor = "#E9F3FF";
                                    }
                                    else
                                    {
                                        $bgcolor = "#FFFFFF";
                                    }
                                    $wo_span = $wo_count[$k_wo_number];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('summary_tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="summary_tr_<?= $i; ?>" style="text-decoration:none; cursor:pointer">
                                    <?
                                        if(!in_array($k_wo_number,$wo_chk))
                                        {
                                            $wo_chk[]=$k_wo_number;
                                        ?>
                                        <td width="40" valign="middle" class="wrd_brk center" rowspan="<? echo $wo_span; ?>"><?= $i; ?></td>
                                        <td width="140" valign="middle" class="wrd_brk center" rowspan="<? echo $wo_span; ?>"><?= $k_wo_number; ?></td>
                                        <?
                                        }
                                        ?>
                                        <td width="100" class="wrd_brk center"><?= $item_group_arr[$k_item_group]; ?></td>
                                        <td width="100" class="wrd_brk center"><?= $k_item_description; ?></td>
                                        <td width="90" class="wrd_brk right"><?= number_format($item_description_val['SUPPLIER_ORDER_QUANTITY'], 2); ?></td>
                                        
                                        <td width="90" class="wrd_brk right"><?=number_format($item_description_val['AMOUNT'], 2); ?></td>
                                        <td width="90" class="wrd_brk right"><?= number_format($rcv_qnty, 2); ?></td>
                                        <td width="90" class="wrd_brk right"><?= number_format($rcv_return_qnty, 2); ?></td>
                                        <td width="90" class="wrd_brk right"><?= number_format($rcv_qnty - $rcv_return_qnty, 2); ?></td>
                                        
                                        <td width="90" class="wrd_brk right"><?= number_format($rcv_amount, 2);//$rcv_qnty - $rcv_return_qnty)*$rcv_rate ?></td>
                                        <td width="90" class="wrd_brk right"><?= number_format($item_description_val['SUPPLIER_ORDER_QUANTITY']-$rcv_qnty + $rcv_return_qnty, 2); ?></td>
                                    </tr>
                                     <!-- <td width="90" class="wrd_brk right"><?= number_format($rcv_rate,2); ?></td> -->
                                     <!-- <td width="90" class="wrd_brk right"><?= number_format($item_description_val['RATE'], 2); ?></td> -->
                                    <?
                                    $total_quantity += $item_description_val['SUPPLIER_ORDER_QUANTITY'];
                                    $total_amount += $item_description_val['AMOUNT'];
                                    $total_rcv_quantity += $rcv_qnty;
                                    $total_rcv_return_qnty += $rcv_return_qnty;
                                    // $total_rcv_amount += $rcv_amount;
                                    $total_payable_quantity += $rcv_qnty - $rcv_return_qnty;
                                    $total_payable_amount += $rcv_amount;//($rcv_qnty - $rcv_return_qnty)*$rcv_rate;
                                }
                            }
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<?= $table_width+180; ?>" cellpadding="0" cellspacing="0" id="table_footer4" >
                <tfoot>
                    <tr>
                        <th width="40">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" class="wrd_brk right">Total</th>
                        <th width="90" class="wrd_brk right"><?= number_format($total_quantity, 2); ?></th>
                      
                        <th width="90" class="wrd_brk right"><?= number_format($total_amount, 2); ?></th>
                        <th width="90" class="wrd_brk right"><?= number_format($total_rcv_quantity, 2); ?></th>
                        <th width="90" class="wrd_brk right"><?= number_format($total_rcv_return_qnty, 2); ?></th>
                        <th width="90" class="wrd_brk right"><?= number_format($total_payable_quantity, 2); ?></th>
                        <!-- <th width="90">&nbsp;</th> -->
                        <th width="90" class="wrd_brk right"><?= number_format($total_payable_amount, 2); ?></th>
                        <th width="90" class="wrd_brk right"><?= number_format(($total_quantity-$total_rcv_quantity+$total_rcv_return_qnty), 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        <? echo signature_table(303,$cbo_company,$table_width,"",0,$user_lib_name[$inserted_by]); ?>
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
?>