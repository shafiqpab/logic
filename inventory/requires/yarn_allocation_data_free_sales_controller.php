<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

function get_users_buyer()
{
	$byr_str = '';
	if ($_SESSION['logic_erp']['data_level_secured'] == 1)
	{
		if ($_SESSION['logic_erp']['buyer_id'] != '')
		{
			$byr_str = $_SESSION['logic_erp']['buyer_id'];
		}
	}
	return $byr_str;
}

/*
|------------------------------------------------------------------------
| for load_drop_down_buyer
|------------------------------------------------------------------------
*/
if ($action == "load_drop_down_buyer") 
{
    echo create_drop_down("cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' order by buy.buyer_name", "id,buyer_name", 1, "--Select Buyer--", $selected, "", "");
    exit();
}

/*
|------------------------------------------------------------------------
| for load_drop_down_cust_buyer
|------------------------------------------------------------------------
*/
if ($action == "load_drop_down_cust_buyer") 
{
    if ($data == 0) 
    {
        echo create_drop_down("cbo_cust_buyer_id", 100, $blank_array, "", 1, "--Select Cust Buyer--", 0, "");
    }
    else  
    {
        echo create_drop_down("cbo_cust_buyer_id", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", $selected, "", 0);
    }
    exit();
}

$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

//item search------------------------------//
if ($action == "item_description_search") 
{
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>

        var selected_id = new Array;
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
            toggle(document.getElementById('tr_' + str), '#FFFFCC');

            if (jQuery.inArray(selectID, selected_id) == -1) {
                selected_id.push(selectID);
            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == selectID)
                        break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
			
            id = id.substr(0, id.length - 1);
            $('#txt_selected_id').val(id);
        }

        function fn_check_lot()
        {
            show_list_view(document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_search_common').value + '_' +<? echo $company; ?>+ '_' + document.getElementById('txt_prod_id').value, 'create_lot_search_list_view', 'search_div', 'yarn_allocation_data_free_sales_controller', 'setFilterGrid("list_view",-1)');
        }
    </script>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th>Search By</th>
                            <th align="center" width="180" id="search_by_td_up">Enter Lot Number</th>
                            <th>Product Id</th>
                            <th>
                                <input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                                <input type='hidden' id='txt_selected_id' />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr align="center">
                            <td align="center" width="160">
                                <?
                                $search_by = array(1 => 'Lot No', 2 => 'Item Description');
                                $dd = "change_search_event(this.value, '0*0', '0*0', '../../')";
                                echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
                                ?>
                            </td>
                            <td width="180" align="center" id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            </td>
                            <td width="110" align="center">
                                <input type="text" style="width:90px" class="text_boxes_numeric"  name="txt_prod_id" id="txt_prod_id" />
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
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <script language="javascript" type="text/javascript">      
    </script>
    <?
    exit();
}

if ($action=="create_lot_search_list_view")
{
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
    ?>
      <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>
    <div>
        <div style="width:580px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" >
                <thead>
                    <th width="50">SL No</th>
                    <th width="100">Product ID</th>
                    <th width="150">Supplier</th>
                    <th width="80">Lot</th>
                    <th width="">Item Description</th>
                </thead>
            </table>
        </div>

        <div style="width:580px; overflow-y:scroll; min-height:50px; max-height:250px;" id="buyer_list_view" align="left">
            <table  cellspacing="0" cellpadding="0" width="100%" class="rpt_table" id="list_view" >
                <?php

                $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
                $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

                $ex_data = explode("_", $data);
                $txt_search_by = $ex_data[0];
                $txt_search_common = trim($ex_data[1]);
                $company = $ex_data[2];
                $prod_id = trim($ex_data[3]);
                
                $sql_cond = "";
                if (trim($txt_search_common) != "") 
                {
                    if (trim($txt_search_by) == 1)
                    { // for LOT NO
                        $sql_cond = " AND D.LOT LIKE '%".$txt_search_common."%'";
                    }
                    else if (trim($txt_search_by) == 2)
                    { // for Yarn Count
                        $sql_cond = " AND D.PRODUCT_NAME_DETAILS LIKE '%".$txt_search_common."%'";
                    }
                }

                if($prod_id) $sql_cond .= " and d.id = $prod_id";

                $sql = "SELECT A.ID as TRANS_ID,A.MST_ID, A.TRANSACTION_TYPE, D.ID, D.COMPANY_ID, D.SUPPLIER_ID, D.LOT, D.PRODUCT_NAME_DETAILS FROM INV_TRANSACTION A, PRODUCT_DETAILS_MASTER D WHERE A.PROD_ID = D.ID AND A.TRANSACTION_TYPE IN (1,4,5) AND A.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND D.COMPANY_ID = ".$company.$sql_cond." GROUP BY A.ID,A.MST_ID, A.TRANSACTION_TYPE, D.ID, D.COMPANY_ID, D.SUPPLIER_ID, D.LOT, D.PRODUCT_NAME_DETAILS order by A.ID";
                //echo $sql; die;
                $sql_result = sql_select($sql);
                $rcv_id_arr = array();
                foreach ($sql_result as $row) 
                {
                    $rcv_id_arr[$row['MST_ID']] = $row['MST_ID'];
                    $trans_supplier_id[$row['TRANS_ID']][$row['ID']] = $row['SUPPLIER_ID'];
                }
                
                //for TMP_RECV_MST_ID table data delete
                $con = connect();
                execute_query("DELETE FROM TMP_RECV_MST_ID WHERE USERID = ".$user_id);
                oci_commit($con);
                
                //for TMP_RECV_MST_ID table data insert
                $con = connect();
                foreach($rcv_id_arr as $key=>$val)
                {
                    execute_query("INSERT INTO TMP_RECV_MST_ID(MST_ID, USERID) VALUES('".$val."', '".$user_id."')");
                }
                oci_commit($con);

                //for receive information
                $sql_rcv = "SELECT A.ID, A.SUPPLIER_ID, A.RECEIVE_PURPOSE FROM INV_RECEIVE_MASTER A, TMP_RECV_MST_ID B WHERE A.ID = B.MST_ID AND B.USERID = ".$user_id;
                $sql_rcv_rslt = sql_select($sql_rcv);
                $rcv_data_arr = array();
                foreach($sql_rcv_rslt as $row)
                {
                    $rcv_data_arr[$row['ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
                    $rcv_data_arr[$row['ID']]['RECEIVE_PURPOSE'] = $row['RECEIVE_PURPOSE'];
                }
                //end for receive information
                
                //for dyeing information
                $sql_dyeing = "SELECT A.ID, B.PAY_MODE FROM INV_RECEIVE_MASTER A, TMP_RECV_MST_ID B, WO_YARN_DYEING_MST C WHERE A.BOOKING_ID = C.ID AND B.MST_ID = C.ID AND B.USERID = ".$user_id;
                $sql_dyeing_rslt = sql_select($sql_dyeing);
                $dyeing_data_arr = array();
                foreach($sql_dyeing_rslt as $row)
                {
                    $dyeing_data_arr[$row['ID']]['PAY_MODE'] = $row['PAY_MODE'];
                }
                //end for dyeing information
                
                $i = 1;
                $prodIdChk = array();
                $transIdChk = array();
                foreach ($sql_result as $row) 
                {
                    $id_arr[] = $row['ID'];

                    //if($prodIdChk[$row['ID']]=="" && min($rcv_id_arr) == $row['MST_ID'])

                    if($prodIdChk[$row['ID']]=="" && $transIdChk[$row['TRANS_ID']]=="")
                    {
                        if ($i % 2 == 0)
                        {
                            $bgcolor = "#E9F3FF";
                        }
                        else
                        {
                            $bgcolor = "#FFFFFF";
                        }

                        $prodIdChk[$row['ID']] = $row['ID'];
                        $transIdChk[$row['TRANS_ID']] = $row['TRANS_ID'];
                        $rcv_supplier_id = $trans_supplier_id[$row['TRANS_ID']][$row['ID']];
                        $receive_purpose = $rcv_data_arr[$row['MST_ID']]['RECEIVE_PURPOSE'];
                        $pay_mode = $dyeing_data_arr[$row['MST_ID']]['PAY_MODE'];

                        if( $row['TRANSACTION_TYPE'] ==1 || $row['TRANSACTION_TYPE']==4)
                        {
                            if( $receive_purpose ==2 || $receive_purpose ==7 || $receive_purpose ==12 || $receive_purpose ==15 || $receive_purpose == 38 || $receive_purpose ==46 || $receive_purpose ==50 || $receive_purpose ==51 )
                            {
                                if($pay_mode==3 || $pay_mode==5)
                                {
                                    $factory_name = $company_arr[$rcv_supplier_id];
                                }
                                else
                                {
                                    $factory_name = $supplier_arr[$rcv_supplier_id];
                                } 
                            }
                            else
                            {
                                $factory_name = $supplier_arr[$rcv_supplier_id];
                            }
                        }
                        else 
                        {                           
                            $factory_name=$supplier_arr[$row['SUPPLIER_ID']];
                        }

                        $selectedString = "'".$i.'_'.$row['ID'].'_'.$row['ID']."'";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value(<? echo $selectedString;?>)">
                            <td width="50" align="center" class="wrd_brk"><?php echo $i; ?></td>
                            <td width="100" align="center" class="wrd_brk"><?php echo $row['ID']; ?></td>
                            <td width="150" class="wrd_brk">&nbsp;<?php echo $factory_name; ?></td>
                            <td width="80" class="wrd_brk">&nbsp; <?php echo $row['LOT']; ?></td>
                            <td class="wrd_brk">&nbsp; <?php echo $row['PRODUCT_NAME_DETAILS']; ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
            </table>
        </div>
        <div style="width:580px;" align="left">
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        setFilterGrid('list_view',-1);
        check_all_data();
    </script>
    <?
    exit();
}

if ($action == "lot_no_search") 
{
    echo load_html_head_contents("Lot No Info", "../../", 1, 1, $unicode, "");
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(str) {
            var splitData = str.split("_");
            $("#hidden_product").val(splitData[0]); 
            $("#hidden_lot").val(splitData[1]);
            parent.emailwindow.hide();
        }
    </script> 
    <input type="hidden" value="" id="hidden_product">
    <input type="hidden" value="" id="hidden_lot">
    <?
    $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$cbo_company_name and item_category_id=1";
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $arr = array(1 => $supplier_arr);
    echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description", "70,160,70", "600", "260", 0, $sql, "js_set_value", "id,lot", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "", "setFilterGrid('list_view',-1)", "0", "", "");
    ?> 

    <script src="../../includes/functions_bottom.js" type="text/javascript"></script> 

    <?
}

/*
|------------------------------------------------------------------------
| for job_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "job_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        var selected_id = new Array;
        var selected_name = new Array;

        function js_set_value_job(str) {
			var splitData = str.split("_");
            $('#hide_job_id').val(splitData[0]);
			$('#hide_job_no').val(splitData[1]);
            parent.emailwindow.hide();
        }
    </script>

    </head>

    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:780px;">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                    class="rpt_table" id="tbl_list">
                    <thead>
                        <th>PO Company</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Sales No</th>
                        <th>Booking Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                            <?

                            echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.company_name from  lib_company buy where buy.status_active =1 and buy.is_deleted=0   order by buy.company_name", "id,company_name", 1, "-- All--", 0, "", 0);
                            ?>
                        </td>
                        <td align="center">
                            <?
                            $search_by_arr = array(1 => "Sales No", 2 => "Style Ref", 3 => "Booking No");
                            $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                            echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                            id="txt_search_common"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                            style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                            readonly>
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show"
                            onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' +'<? echo $within_group; ?>', 'create_job_no_search_list_view', 'search_div', 'yarn_allocation_data_free_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
                            style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-top:15px" id="search_div"></div>
        </fieldset>
    </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

/*
|------------------------------------------------------------------------
| for create_job_no_search_list_view
|------------------------------------------------------------------------
*/
if ($action == "create_job_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];
    $within_group = $data[6];
    $within_group_cond = (str_replace("'","",$within_group)>0)?" and a.within_group=$within_group":"";

	if ($data[1] == 0) 
    {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) 
        {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_id=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "a.job_no";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else
		$search_field = "a.sales_booking_no";

	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $company_library);
	if ($db_type == 0)
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
		$year_field = ""; //defined Later

	$sql = "select a.id, a.job_no, $year_field, a.company_id, a.buyer_id, a.style_ref_no,a.booking_date,a.sales_booking_no from  fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $within_group_cond $date_cond order by a.id DESC";
	//echo $sql;	die;
	echo create_list_view("tbl_list_search", "Company,Buyer/Unit,Year,Sales No,Style Ref., Booking No, Booking Date", "120,120,50,110,120,120,80", "800", "220", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr, "company_id,buyer_id,year,job_no,style_ref_no,sales_booking_no,booking_date", "", '', '0,0,0,0,0,0,3', '');
	exit();
}

/*
|------------------------------------------------------------------------
| for booking_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(str)
		{
            $('#hide_booking_no').val(str);
            parent.emailwindow.hide();
        }

        function show_data_list()
        {
            if($("#txt_search_common").val() =="" && $("#cbo_buyer_name").val() ==0 )
            {
                if($("#txt_date_from").val() =="" && $("#txt_date_to").val() =="" )
                {
                    alert("Please select any reference");
                    return;
                }

            }
            show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' + document.getElementById('cbo_year_selection').value + '**' + document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'yarn_allocation_data_free_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
        }
 </script>

    </head>

    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:780px;">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                    class="rpt_table" id="tbl_list">
                    <thead>
                        <th>Within Group</th>
                        <th> PO Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Search</th>
                        <th>Booking Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
                        <input type="hidden" name="hide_booking_no" id="hide_booking_no" value=""/>

                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                            <?
                                echo create_drop_down("cbo_within_group", 60, $yes_no, "", 0, "", 1, '', 0);
                            ?>
                        </td>
                            <td align="center">
                            <?
                            echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
                            ?>
                        </td>
                        <td align="center">
                            <?
                            $search_by_arr = array(4 => "Booking No", 3 => "Job No", 1 => "Order No", 2 => "Style Ref");
                            $dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
                            echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", 4, "", $dd, 0);
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                            id="txt_search_common"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                            style="width:55px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"
                            readonly>
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show"
                            onClick="show_data_list();"
                            style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-top:15px" id="search_div"></div>
        </fieldset>
    </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

/*
|------------------------------------------------------------------------
| for create_order_no_search_list_view
|------------------------------------------------------------------------
*/
if ($action == "create_order_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];
    $year_id = $data[6];
	$within_group = $data[7];

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_id=$data[1]";
	}


	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($db_type == 2) {
        $year_field_con = " and to_char(d.insert_date,'YYYY')";
		$year_field_con2 = " and to_char(a.insert_date,'YYYY')";

        if ($year_id != 0) $year_cond = "$year_field_con=$year_id"; else $year_cond = "";
		if ($year_id != 0) $year_cond2 = "$year_field_con2=$year_id"; else $year_cond2 = "";
	} else {
        if ($year_id != 0) $year_cond = "and year(d.insert_date) =$year_id"; else $year_cond = "";
		if ($year_id != 0) $year_cond2 = "and year(a.insert_date) =$year_id"; else $year_cond2 = "";

	}
	if ($search_by == 1)
		$search_field = "c.po_number";
	else if ($search_by == 2)
		$search_field = "d.style_ref_no";
	else if ($search_by == 3)
		$search_field = "a.job_no";
	else
		$search_field = "a.booking_no";

	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $buyer_arr);

	if ($db_type == 0)
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
		$year_field = ""; //defined Later


    if($within_group==1){
        $sql = "SELECT a.id,a.booking_no, d.job_no, $year_field,a.company_id,a.buyer_id,a.booking_date,c.po_number,d.style_ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d, fabric_sales_order_mst e where  c.job_no_mst=d.job_no and b.po_break_down_id=c.id and b.job_no=d.job_no and a.booking_no=b.booking_no and a.booking_no=e.sales_booking_no and e.within_group=1 and a.status_active=1 and a.is_deleted=0 and e.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond group by a.id,a.booking_no, d.job_no,a.company_id,a.buyer_id,a.insert_date,a.booking_date,c.po_number,d.style_ref_no  
        order by a.booking_no, a.booking_date";
    }
    else
    {
        $sql ="SELECT null as id,a.sales_booking_no as booking_no, null as job_no, to_char(a.insert_date,'YYYY') as year,a.company_id,a.buyer_id, a.booking_date,null as po_number,a.style_ref_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=2 and a.sales_booking_no like '$search_string'  $buyer_id_cond $date_cond $year_cond2 group by a.sales_booking_no, a.insert_date, a.company_id,a.buyer_id, a.booking_date, a.style_ref_no order by a.sales_booking_no, a.booking_date";
    }

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Job No,Style,PO No,Booking Date", "120,100,100,100,100,100,100", "760", "220", 0, $sql, "js_set_value", "booking_no", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr, "company_id,buyer_id,booking_no,job_no,style_ref_no,po_number,booking_date", "", '', '0,0,0,0,0,0,3', '', 1);
	exit();
}

/*
|------------------------------------------------------------------------
| for report_generate
|------------------------------------------------------------------------
*/
if ($action == "report_generate")
{
    ?>
    <style>
        .cls_break td{
            word-break:break-all; 
        }
        
        .cls_tot{
            text-align:right;
            font-weight:bold;					
        }

        #alc_freebtn {
        cursor:pointer;
        border:outset 1px #66CC00;
        background-color: #AECBF1;
        color:#171717;
        font-size: 13px;
        font-weight:bold;
        padding: 1px 2px;
        border-radius:.7em;
        }
        .word-wrap{
            word-wrap: break-word;word-break: break-all;
        }
    </style>
    <?
    $started = microtime(true);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyId = str_replace("'", "", $cbo_company_name);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$cbo_cust_buyer_id = str_replace("'", "", $cbo_cust_buyer_id);
	$txt_product = str_replace("'", "", $txt_product);
	$txt_lot_no = trim(str_replace("'", "", $txt_lot_no));
	$hidden_prod_no = str_replace("'", "", $hidden_prod_no);
	$cbo_within_group = str_replace("'", "", $cbo_within_group);
	$txt_sales_no = trim(str_replace("'", "", $txt_sales_no));
	$hidden_fso_id = str_replace("'", "", $hidden_fso_id);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	$cbo_year_selection = str_replace("'", "", $cbo_year_selection);
    
    $companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
    $color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
    $brand_arr=return_library_array( "select id, brand_name from lib_brand","id", "brand_name");

    $main_sql_cond = "";
    $plan_booking_cond = "";
    $issue_po_cond = "";
    if($companyId>0)
    {
        $main_sql_cond .=" AND a.company_id=$companyId";
    }
    if($cbo_buyer_id>0)
    {
        $main_sql_cond .=" AND a.buyer_id=$cbo_buyer_id";
    }
    if($cbo_cust_buyer_id>0)
    {
        $main_sql_cond .=" AND a.customer_buyer=$cbo_cust_buyer_id";
    }
    if($cbo_within_group>0)
    {
        $main_sql_cond .=" AND a.within_group=$cbo_within_group";
    }
    if($hidden_fso_id!="")
    {
        $main_sql_cond .=" AND a.id=$hidden_fso_id";
        $plan_booking_cond .= " AND a.po_id=$hidden_fso_id";
        $issue_po_cond .= " AND b.po_breakdown_id=$hidden_fso_id";
        $wo_po_cond .= " AND b.job_no_id=$hidden_fso_id";
    }
    if($txt_sales_no!="")
    {
        $main_sql_cond .=" AND a.job_no like('%".$txt_sales_no."%')";
    }
    if($txt_booking_no!="")
    {
        $main_sql_cond .=" AND a.sales_booking_no='$txt_booking_no'";
        $plan_booking_cond .= " AND a.booking_no='$txt_booking_no'";
    }
    if($txt_booking_no!="")
    {
        $main_sql_cond .=" AND a.sales_booking_no='$txt_booking_no'";
    }
    if($txt_product!="")
    {
        $main_sql_cond .=" AND b.item_id=$txt_product";
    }
    if($hidden_prod_no!="")
    {
        $main_sql_cond .=" AND b.item_id=$hidden_prod_no";
    }
    if($txt_lot_no!="")
    {
        $main_sql_cond .=" AND c.lot='$txt_lot_no'";
    }   
    
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$main_sql_cond .= "and b.allocation_date between '" . change_date_format($date_from, "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$main_sql_cond .= "and b.allocation_date between '" . change_date_format($date_from, '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

    if ($cbo_year_selection != 0)
	{
		//$main_sql_cond .= " AND TO_CHAR(b.INSERT_DATE,'YYYY') = ".$cbo_year_selection;
	}

    $con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and ref_from in (1,2,3) and ENTRY_FORM=132"); // 1=>prod id, 2=>prod id,3=>requisition no
	oci_commit($con);

    /** 1st Step Allocation */
	$main_sql = "SELECT a.id AS po_id,a.job_no,a.sales_booking_no,a.buyer_id,a.customer_buyer,c.lot,c.id AS prod_id,c.yarn_comp_type1st,c.yarn_type,c.color,c.brand,b.id as alc_dtls_id,b.mst_id as alc_pk_id,b.qnty,c.current_stock,c.allocated_qnty,c.available_qnty FROM fabric_sales_order_mst a,inv_material_allocation_dtls b,product_details_master c WHERE a.id=b.po_break_down_id AND b.item_id=c.id AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $main_sql_cond";
    //echo $main_sql;

	$sql_result = sql_select($main_sql);
    
    $allocation_data = $po_id_array = $prod_id_array = array();
    foreach ($sql_result as $row) 
    {
        $po_id = $row[csf("po_id")];
        $booking = $row[csf("sales_booking_no")];
        $prod_id = $row[csf("prod_id")];

        $allocation_data[$po_id][$booking][$prod_id]['alc_dtls_id'] = $row[csf("alc_dtls_id")];
        $allocation_data[$po_id][$booking][$prod_id]['alc_pk_id'] = $row[csf("alc_pk_id")];
        $allocation_data[$po_id][$booking][$prod_id]['job_no'] = $row[csf("job_no")];
        $allocation_data[$po_id][$booking][$prod_id]['buyer_id'] = $row[csf("buyer_id")];
        $allocation_data[$po_id][$booking][$prod_id]['customer_buyer'] = $row[csf("customer_buyer")];
        $allocation_data[$po_id][$booking][$prod_id]['lot'] = $row[csf("lot")];
        $allocation_data[$po_id][$booking][$prod_id]['yarn_comp_type1st'] = $row[csf("yarn_comp_type1st")];
        $allocation_data[$po_id][$booking][$prod_id]['yarn_type'] = $row[csf("yarn_type")];
        $allocation_data[$po_id][$booking][$prod_id]['color'] = $row[csf("color")];
        $allocation_data[$po_id][$booking][$prod_id]['brand'] = $row[csf("brand")];
        $allocation_data[$po_id][$booking][$prod_id]['allocation_qnty'] += $row[csf("qnty")];
        $allocation_data[$po_id][$booking][$prod_id]['global_stock'] = number_format($row[csf("current_stock")],2,".","");
        $allocation_data[$po_id][$booking][$prod_id]['global_allocation_qnty'] = number_format($row[csf("allocated_qnty")],2,".","");
        $allocation_data[$po_id][$booking][$prod_id]['global_available_qnty'] = number_format($row[csf("available_qnty")],2,".","");

        $prod_id_array[$prod_id] = $prod_id;
        $po_id_array[$po_id] = $po_id;   
    }

    fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 132, 1, $prod_id_array, $empty_arr); // prod id		
    fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 132, 2, $po_id_array, $empty_arr); // po id	

    unset($sql_result);

    $wo_sql = "SELECT b.id AS WO_DTLS_ID, job_no_id AS PO_ID,fab_booking_no AS BOOKING_NO,product_id AS PROD_ID,yarn_color AS YARN_COLOR,yarn_wo_qty AS YARN_WO_QTY FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b,gbl_temp_engine tmp_prod,gbl_temp_engine tmp_po WHERE  tmp_prod.user_id = tmp_po.user_id AND tmp_prod.entry_form=tmp_po.entry_form AND tmp_prod.ref_val = b.product_id  AND tmp_prod.ref_from = 1 AND tmp_prod.user_id = ".$user_name." AND tmp_prod.entry_form = 132 and tmp_po.ref_val = b.job_no_id and tmp_po.ref_from = 2 AND tmp_po.entry_form = 132 AND tmp_po.user_id = ".$user_name." and a.id=b.mst_id and a.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_po_cond";
    $wo_result = sql_select($wo_sql); 
    $wo_data_arr = $color_wo_data_arr = array();
    foreach ($wo_result as $row) 
    {
        $po_id = $row["PO_ID"];
        $booking = $row["BOOKING_NO"];
        $prod_id = $row["PROD_ID"];
        $wo_dtls_id = $row["WO_DTLS_ID"];

        $wo_data_arr[$po_id][$prod_id]['wo_qty'] += $row["YARN_WO_QTY"];
        $wo_data_arr[$po_id][$prod_id]['wo_dtls_id'] = $row["WO_DTLS_ID"];
        
        $color_wo_data_arr[$po_id][$prod_id][$wo_dtls_id][$row["YARN_COLOR"]] += $row["YARN_WO_QTY"];
    }
    unset($wo_result);

    //echo "<pre>";
    //print_r($wo_data_arr); 

    /** 2nd Step program and requsition */
    $prqsn_sql = "SELECT b.id as requisition_id,a.booking_no,a.po_id,b.knit_id,b.requisition_no,b.prod_id,b.yarn_qnty FROM ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b,gbl_temp_engine tmp_prod,gbl_temp_engine tmp_po WHERE tmp_prod.user_id = tmp_po.user_id AND tmp_prod.entry_form=tmp_po.entry_form AND tmp_prod.ref_val = b.prod_id  AND tmp_prod.ref_from = 1 AND tmp_prod.user_id = ".$user_name." AND tmp_prod.entry_form = 132 and tmp_po.ref_val = a.po_id and tmp_po.ref_from = 2 AND tmp_po.entry_form = 132 AND tmp_po.user_id = ".$user_name." AND a.dtls_id=b.knit_id AND a.is_sales=1 AND b.status_active=1 AND b.is_deleted=0 $plan_booking_cond";
    //echo $prqsn_sql; die;

    $prqsn_result = sql_select($prqsn_sql);
    $requisition_data =  $requisition_no_array = array();
    foreach ($prqsn_result as $row) 
    {
        $po_id = $row[csf("po_id")];
        $booking = $row[csf("booking_no")];
        $prod_id = $row[csf("prod_id")];
        $requisition_id = $row[csf("requisition_id")];
        $requisition_no = $row[csf("requisition_no")];

        $requisition_po_booking_arr[$requisition_id][$requisition_no][$prod_id]['po_id'] = $po_id; 
        $requisition_po_booking_arr[$requisition_id][$requisition_no][$prod_id]['booking'] = $booking;
        $requisition_booking_arr[$prod_id][$po_id][$requisition_no]['booking'] = $booking;
        $requisition_no_array[$requisition_no]= $requisition_no;

        $requisition_data[$po_id][$booking][$prod_id]['requisition_qty'] += $row[csf("yarn_qnty")];
        $requisition_data[$po_id][$booking][$prod_id]['requisition_no'] = $requisition_no;
        $requisition_data[$po_id][$booking][$prod_id]['requisition_id'] = $requisition_id;
    }  
    fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 132, 3, $requisition_no_array, $empty_arr); // requisition no	
    unset($prqsn_result);

   /*  echo "<pre>";
    print_r($requisition_po_booking_arr);
    die; */
   
    /** 3rd Step demand optional */
    //$sql_demand = "SELECT a.requisition_no,c.id demand_dtls_id,c.mst_id AS demand_pkid,b.id AS demand_reqsn_pk_id, c.demand_qnty,c.save_string FROM ppl_yarn_requisition_entry a, ppl_yarn_demand_reqsn_dtls b,ppl_yarn_demand_entry_dtls c,gbl_temp_engine tmp WHERE tmp.ref_val=b.prod_id AND tmp.ref_from = 1 AND tmp.entry_form=132 AND tmp.user_id = $user_name AND a.id=b.requisition_id and a.requisition_no=b.requisition_no and a.prod_id=b.prod_id and b.DTLS_ID=c.id and b.mst_id=c.mst_id and b.requisition_no=c.requisition_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

    $sql_demand = "SELECT distinct d.id demand_dtls_id,d.mst_id AS demand_pkid,a.po_id,c.prod_id,a.booking_no,c.requisition_id,c.requisition_no,c.id AS demand_reqsn_pk_id,c.YARN_DEMAND_QNTY as demand_qnty,c.cone_qty,c.ctn_qty,c.remarks,d.save_string FROM ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b,ppl_yarn_demand_reqsn_dtls c,ppl_yarn_demand_entry_dtls d,gbl_temp_engine tmp_prod,gbl_temp_engine tmp_po,gbl_temp_engine tmp_rqsn WHERE tmp_prod.user_id = tmp_po.user_id AND tmp_po.user_id=tmp_rqsn.user_id AND tmp_prod.entry_form=tmp_po.entry_form AND tmp_po.entry_form=tmp_rqsn.entry_form AND tmp_prod.ref_val = b.prod_id  AND tmp_prod.ref_from = 1 AND tmp_prod.user_id = ".$user_name." AND tmp_prod.entry_form = 132 and tmp_po.ref_val = a.po_id and tmp_po.ref_from = 2 AND tmp_po.entry_form = 132 AND tmp_po.user_id = ".$user_name." AND tmp_rqsn.ref_val = b.requisition_no and tmp_rqsn.ref_from = 3 AND tmp_rqsn.entry_form = 132 AND tmp_rqsn.user_id = ".$user_name."  AND a.dtls_id=b.knit_id and b.id=c.requisition_id and b.requisition_no=c.requisition_no and b.prod_id=c.prod_id and c.dtls_id=d.id and c.mst_id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $plan_booking_cond order by d.id";
   //echo $sql_demand; die;
   
    $demand_result = sql_select($sql_demand);
    $demand_data = array();
    foreach ($demand_result as $row) 
    {
        $booking = $row[csf("booking_no")]; 
        $po_id = $row[csf("po_id")]; 
        $productId = $row[csf("prod_id")];

        $requisition_no =  $row[csf("requisition_no")];
        $demand_reqsn_pk_id =  $row[csf("demand_reqsn_pk_id")];
        $demand_pkid =  $row[csf("demand_pkid")];
        $demand_dtls_id =  $row[csf("demand_dtls_id")];
        $save_string_arr = explode("_",$row[csf("save_string")]);         
   
        $demand_data[$po_id][$booking][$productId]['demand_reqsn_pk_id'] .= $row[csf("demand_reqsn_pk_id")].",";
        $demand_data[$po_id][$booking][$productId]['demand_pkid'] = $row[csf("demand_pkid")];
        $demand_data[$po_id][$booking][$productId]['demand_dtls_id'][] = $demand_dtls_id;
        $demand_data[$po_id][$booking][$productId]['total_demand_qnty'] += $row[csf("demand_qnty")];  

        $demand_details_id_arr[$po_id][$booking][$productId][$requisition_id][$requisition_no][$demand_pkid][$demand_dtls_id]['cone_qty'] = $row[csf("cone_qty")];  
        $demand_details_id_arr[$po_id][$booking][$productId][$requisition_id][$requisition_no][$demand_pkid][$demand_dtls_id]['ctn_qty'] = $row[csf("ctn_qty")];  
        $demand_details_id_arr[$po_id][$booking][$productId][$requisition_id][$requisition_no][$demand_pkid][$demand_dtls_id]['remarks'] = $row[csf("remarks")];
        $demand_details_id_arr[$po_id][$booking][$productId][$requisition_id][$requisition_no][$demand_pkid][$demand_dtls_id]['demand_qnty'] = $row[csf("demand_qnty")];    
        
    }
    unset($demand_result);

    /* echo "<pre>";
    print_r($demand_data); die; */
   
    /** 4th Step issue/issue Return */
    
    $issue_issue_rtn_sql = "SELECT  distinct  b.id, c.issue_basis AS basis, a.demand_id, a.transaction_type, a.requisition_no, b.po_breakdown_id, b.prod_id,a.dyeing_color_id, b.quantity FROM inv_transaction a, order_wise_pro_details  b, inv_issue_master c,gbl_temp_engine tmp_prod,gbl_temp_engine tmp_po WHERE tmp_prod.user_id = tmp_po.user_id AND tmp_prod.entry_form=tmp_po.entry_form AND tmp_prod.ref_val = a.prod_id  AND tmp_prod.ref_from = 1 AND tmp_prod.user_id = ".$user_name." AND tmp_prod.entry_form = 132 and tmp_po.ref_val = b.po_breakdown_id and tmp_po.ref_from = 2 AND tmp_po.entry_form = 132 AND tmp_po.user_id = ".$user_name." AND c.id = a.mst_id AND a.id = b.trans_id AND a.prod_id = b.prod_id AND a.item_category= 1 AND a.transaction_type = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND a.status_active = 1 AND a.is_deleted = 0 $issue_po_cond
    
    UNION ALL 
    
    SELECT  distinct  b.id, c.receive_basis AS basis, a.demand_id, a.transaction_type, a.requisition_no, b.po_breakdown_id, b.prod_id,a.dyeing_color_id, b.quantity FROM inv_transaction a, order_wise_pro_details b, inv_receive_master c,gbl_temp_engine tmp_prod,gbl_temp_engine tmp_po WHERE tmp_prod.user_id = tmp_po.user_id AND tmp_prod.entry_form=tmp_po.entry_form AND tmp_prod.ref_val = a.prod_id  AND tmp_prod.ref_from = 1 AND tmp_prod.user_id = ".$user_name." AND tmp_prod.entry_form = 132 and tmp_po.ref_val = b.po_breakdown_id and tmp_po.ref_from = 2 AND tmp_po.entry_form = 132 AND tmp_po.user_id = ".$user_name." AND c.id=a.mst_id and a.id=b.trans_id AND a.prod_id=b.prod_id AND a.item_category = 1 AND transaction_type=4 AND a.status_active=1 AND a.is_deleted=0 AND a.status_active=1 AND a.is_deleted=0 $issue_po_cond";

    //echo  $issue_issue_rtn_sql; die;

    $issue_issuertn_result = sql_select($issue_issue_rtn_sql);
    $issue_data = $issue_return_data = array();
    foreach ($issue_issuertn_result as $row) 
    {
        $basis = $row[csf("basis")];
        $transaction_type = $row[csf("transaction_type")];
        $po_id = $row[csf("po_breakdown_id")];
        $prod_id = $row[csf("prod_id")];
        $requisition_no = $row[csf("requisition_no")];
        $booking_no = $requisition_booking_arr[$prod_id][$po_id][$requisition_no]['booking'];

        if( $basis==3 || $basis==8 ) // Requsition / Demand 
        {
            if( $transaction_type == 2 )
            {
                $issue_data[$po_id][$booking_no][$prod_id]['rqsn_issue_qnty'] += $row[csf("quantity")] ;
            }
            else
            {
                $issue_return_data[$po_id][$booking_no][$prod_id]['rqsn_issue_return_qnty'] += $row[csf("quantity")] ;
            }
        }
        else // Work order basis
        {
            if( $transaction_type == 2 )
            {
                $issue_data[$po_id][$prod_id]['wo_issue_qnty'] += $row[csf("quantity")] ;
                $issue_data[$po_id][$prod_id][$row[csf("dyeing_color_id")]] += $row[csf("quantity")] ;
            }
            else
            {
                $issue_return_data[$po_id][$prod_id]['wo_issue_return_qnty'] += $row[csf("quantity")] ;
                $issue_return_data[$po_id][$prod_id][$row[csf("dyeing_color_id")]] += $row[csf("quantity")] ;

            }
        }     
        
    }
    unset($issue_issuertn_result);

    // echo "<pre>";
    // print_r($issue_return_data);
    // die; 

    $con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_name." and REF_FROM in(1,2,3) and ENTRY_FORM=132"); // 1=>prod id, 2=>prod id,3=>requisition no
	oci_commit($con);
	disconnect($con);

	ob_start();
	?>
	<fieldset style="width:2670px;">
		<table cellpadding="0" cellspacing="0" width="2270">
            <tr style="border:none;">
                <td colspan="27" align="center">
                <strong>Company Name : <? echo $companyArr[$companyId]; ?></strong>
                </td>
            </tr>
            <tr>
				<td align="center" width="100%" colspan="26">
				<strong>Allocation Free [Sales]</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2670" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width='130'>FSO No</th>
				<th width="130">Booking NO</th>
				<th width="100">Buyer</th>
				<th width="100">Cust Buyer</th>
                <th width="100">Prod. ID</th>
				<th width="100">Yarn Lot</th>
				<th width="160">Composition</th>
				<th width="70">Yarn Type</th>
				<th width="70">Yarn Color</th>
				<th width="70">Brand</th>
                <th width="100">Current Stock</th>
                <th width="100">Global Allocation</th>
                <th width="100">Global Available</th>
				<th width="100">Ref. Allocation</th>
				<th width="100">Requisition</th>
				<th width="100">Demand</th>
                <th width="100">Work Order</th>
                <th width="100">WO. Net Issue</th>
                <th width="100">Requisition Net Issue</th>
				<th width="100">Total Issue</th>
				<th width="100">Total Issue Return</th>
                <th width="100">Total Net Issue</th>
				<th width="100" title="To un-allocate available quantity">Allocaiton Free<input type="checkbox" value="" title="Full un-allocaiton Yes?" id="full_allocation_free_yes"> </th>
                <th width="100">YDSW. Free</th>
                <th width="100">Requisition Free</th>
				<th width="100">Action</th>
			</thead>
		</table>
		<div style="width:2690px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2670" class="rpt_table" id="tbl_list_search">
				<tbody>
					<?
					$sl = 1;
                    $update_demand_data_arr = $color_wo_issue_qty = $color_wo_issue_return_qty =  $color_wq_arr = array();
                    $cl_wo_issue_qty = $cl_wo_issue_rtn_qty = $cl_wo_net_wo_qty = $rqsn_issue_qnty= $rqsn_issue_return_qnty= $rqsn_net_issue_qty= 0;
					$grand_total_ref_allocation = 0;
                    foreach ($allocation_data as $poId=>$bookingArr) 
					{
                        foreach ($bookingArr as $bookingNo=>$prodArr) 
					    {
                            foreach ($prodArr as $prodId=>$row) 
					        {
                                $alc_dtls_id = $row["alc_dtls_id"];
                                $alc_pk_id = $row["alc_pk_id"];

                                $requisition_id =  $requisition_data[$poId][$bookingNo][$prodId]['requisition_id'];
                                $requisition_no = $requisition_data[$poId][$bookingNo][$prodId]['requisition_no'];
                                $demand_pkid = $demand_data[$poId][$bookingNo][$prodId]['demand_pkid'];
                                $demand_dtls_id = $demand_data[$poId][$bookingNo][$prodId]['demand_dtls_id'];
                                $demand_reqsn_pk_id = chop($demand_data[$poId][$bookingNo][$prodId]['demand_reqsn_pk_id'],",");

                                //echo $demand_dtls_id."<br>";

                                $allocation_qnty = $row["allocation_qnty"];
                                $requisition_qnty = $requisition_data[$poId][$bookingNo][$prodId]['requisition_qty'];
                                $demand_qnty = $demand_data[$poId][$bookingNo][$prodId]['total_demand_qnty'];

                                //echo $poId."==".$bookingNo."==".$prodId."==".$demand_data[$poId][$bookingNo][$prodId]['total_demand_qnty']."<br>";

                                $rqsn_issue_qnty = $issue_data[$poId][$bookingNo][$prodId]['rqsn_issue_qnty'];
                                $rqsn_issue_return_qnty = $issue_return_data[$poId][$bookingNo][$prodId]['rqsn_issue_return_qnty'];
                                $rqsn_net_issue_qty = ($rqsn_issue_qnty-$rqsn_issue_return_qnty);

                                $reducible_requisition_qty = ($requisition_qnty-$rqsn_net_issue_qty); 
                                $requisition_free_title =  "Requisition Free=(Requisition-Requisition Net Issue)\nValue Details:(".$requisition_qnty."-".$rqsn_net_issue_qty.")";

                                $total_wo_qnty = $wo_data_arr[$poId][$prodId]['wo_qty'];

                                $wo_issue_qnty = $issue_data[$poId][$prodId]['wo_issue_qnty'];
                                $wo_issue_return_qnty = $issue_return_data[$poId][$prodId]['wo_issue_return_qnty'];
                                $wo_net_issue_qty = ($wo_issue_qnty-$wo_issue_return_qnty);

                                $reducible_wo_qty = ($total_wo_qnty-$wo_net_issue_qty); 
                                $ydsw_free_title =  "YDSW. Free=(Work Order-WO. Net Issue)\nValue Details:(".$total_wo_qnty."-".$wo_net_issue_qty.")";

                                foreach( $color_wo_data_arr[$poId][$prodId] as $wo_dtls_id=>$colorIdArr )
                                {
                                    foreach( $colorIdArr as $color_id=>$wo_qnty )
                                    {
                                        $cl_wo_issue_qty = $issue_data[$poId][$prodId][$color_id];
                                        $cl_wo_issue_rtn_qty = $issue_return_data[$poId][$prodId][$color_id];
                                        $cl_wo_net_wo_qty = $cl_wo_issue_qty-$cl_wo_issue_rtn_qty;                                      

                                        //echo $color_id."=>".$wo_data."<br>";
                                        if($cl_wo_net_wo_qty<$wo_qnty)
                                        {
                                            $color_wise_new_wo_qty = $cl_wo_net_wo_qty;
                                        }
                                        else{
                                            $color_wise_new_wo_qty = 0;
                                        }

                                        //echo $color_id."=>".$wo_qnty."=>".$color_wise_new_wo_qty."<br>";

                                        $update_wo_data_arr[$wo_dtls_id] = $wo_dtls_id."___".$color_id."___".$color_wise_new_wo_qty."___".$wo_qnty;
                                    }
                                }

                                /* echo "<pre>";
                                print_r($color_wq_arr); */

                                $total_issue = $rqsn_issue_qnty+$wo_issue_qnty;
                                $total_issue_rtn = $rqsn_issue_return_qnty+$wo_issue_return_qnty;
                                $total_net_issue = $total_issue - $total_issue_rtn;

                                $allocation_free_qnty =  number_format($allocation_qnty-$total_net_issue,2,".",""); 
                                $allocation_free_title = "Allocaiton Free = (Ref. Allocation-Total Net Issue)\nValue details: (".$allocation_qnty."-".$total_net_issue.")";

                                $new_allocation = $total_net_issue;

                                foreach($demand_details_id_arr[$poId][$bookingNo][$prodId] as $reqsn_id=>$requisition_no_arr)
                                { 
                                    foreach($requisition_no_arr as $reqsn_no=>$demand_pkid_arr)
                                    {
                                        foreach($demand_pkid_arr as $demand_id=>$demand_dtls_id_arr)
                                        {
                                            foreach($demand_dtls_id_arr as $demand_dtsl_id=>$demandDetailsData)
                                            {
                                                //echo $poId."==".$demand_id."==".$demand_dtsl_id."<br>";
                                                /* 
                                                echo "<pre>";
                                                print_r($demandDetailsData);
                                                */
                                                if( $rqsn_issue_qnty<=0 || $rqsn_net_issue_qty<=0) // not yet issue and net issue zero 
                                                {
                                                    $new_demand_qty = 0;
                                                }
                                                else
                                                {
                                                    if($demandDetailsData['demand_qnty']>=($rqsn_net_issue_qty)) // Demand qnty greater than net issue 
                                                    {
                                                        $new_demand_qty = ($rqsn_issue_qnty-$rqsn_issue_return_qnty);
                                                    }
                                                    else if ( ($rqsn_net_issue_qty)<=($new_demand_qty+$demandDetailsData['demand_qnty']) )
                                                    {
                                                        $new_demand_qty = 0;
                                                    }
                                                    else if( ($demandDetailsData['demand_qnty']<=$demand_qnty) && ($demand_qnty >=$rqsn_net_issue_qty) )
                                                    {
                                                        $new_demand_qty = $rqsn_net_issue_qty;
                                                    }
                                                }

                                                //echo  $poId."==".$demand_dtsl_id."==".$demand_qnty."==".$rqsn_net_issue_qty."==".$demandDetailsData['demand_qnty']."==".$new_demand_qty."<br>";
                                                
                                                $update_demand_data_arr[$demand_dtsl_id] = $demand_dtsl_id."___".$prodId."_".$reqsn_id."_".$new_demand_qty."_".$coneQnty."_".$ctnQnty."_".$remark;
                                            }
                                        }
                                    }
                                }

                                $update_workorder_data_str =  implode(",",$update_wo_data_arr);
                                $update_demand_data_str =  implode(",",$update_demand_data_arr);
                                $current_stock = $row["global_stock"];
                                $global_allocation = $row["global_allocation_qnty"];
                                $global_available = $row["global_available_qnty"];

                                $free_ref = "'".$poId."**".$bookingNo."**".$prodId."**".$alc_pk_id."**".$alc_dtls_id."**".$allocation_free_qnty."**".$new_allocation."**".$requisition_id."**".$requisition_no."**". $rqsn_net_issue_qty."**".$demand_pkid."**".$demand_reqsn_pk_id."**".$update_demand_data_str."**".$update_workorder_data_str."**".$current_stock."**".$global_allocation."**".$global_available."**".$row["job_no"]."'";
                           
                                $action_btn_disabled_cond = ($allocation_free_qnty<=0)?"disabled=disabled":"";
                                $action_btn_bg_color_cond = ($allocation_free_qnty<=0)?"background-color: grey !important;'":" background-color: #AECBF1";
                               
                                ?>
                                <tr bgcolor="#FFFFFF" onClick="change_color('tr_<? echo $sl; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>" class="cls_break">
                                    <td width="40"  valign="middle" style=""><? echo $sl; ?></td>
                                    <td width="130" valign="middle"  class="word-wrap" title="<? echo $poId; ?>"><? echo $row["job_no"];?></td>
                                    <td width="130" valign="middle"  class="word-wrap"><? echo $bookingNo;?></td>
                                    <td width="100" valign="middle"  class="word-wrap"><? echo $buyer_arr[$row["buyer_id"]];?></td>
                                    <td width="100" valign="middle"  class="word-wrap"><? echo $buyer_arr[$row["customer_buyer"]];?></td>
                                    <td width="100" valign="middle"  class="word-wrap"><? echo $prodId;?></td>
                                    <td width="100" valign="middle"  class="word-wrap"><? echo $row["lot"];?></td>
                                    <td width="160" valign="middle"  class="word-wrap"><? echo $composition[$row["yarn_comp_type1st"]];?></td>
                                    <td width="70" valign="middle"  class="word-wrap"><? echo $yarn_type[$row["yarn_type"]];?></td>
                                    <td width="70" valign="middle"  class="word-wrap"><? echo $color_arr[$row["color"]];?></td>
                                    <td width="70" valign="middle"  class="word-wrap"><? echo $brand_arr[$row["brand"]];?></td>
                                    <td width="100" valign="middle" align="right"  class="word-wrap"><? echo $row["global_stock"];?></td>
                                    <td width="100" valign="middle" align="right"  class="word-wrap"><? echo $row["global_allocation_qnty"];?></td>
                                    <td width="100" valign="middle" align="right"  class="word-wrap"><? echo $row["global_available_qnty"];?></td>
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($row["allocation_qnty"],2,".","");?></td>
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($requisition_qnty,2,".","")?></td>
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($demand_qnty,2,".","")?></td>
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($total_wo_qnty,2,".","")?></td>

                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($wo_net_issue_qty,2,".","")?></td>
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($rqsn_net_issue_qty,2,".","")?></td>
                                  
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($total_issue,2,".","")?></td>
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($total_issue_rtn,2,".","")?></td>
                                    <td width="100" align="right" valign="middle"  class="word-wrap"><? echo number_format($total_net_issue,2,".","")?></td>

                                    <td width="100" align="right" valign="middle"  class="word-wrap" title="<? echo $allocation_free_title; ?>" ><? echo number_format($allocation_free_qnty,2,".","")?></td>

                                    <td width="100" align="center" title="<? echo $ydsw_free_title;?>">
                                        <input style="width:60px;"  name="txt_wo_free_qty" id="txt_wo_free_qty__<? echo $sl;?>"  class="text_boxes_numeric" placeholder="<? echo $reducible_wo_qty; ?>" type="text" />
                                    </td>

                                    <td width="100" align="center" title="<? echo $requisition_free_title;?>">
                                        <input style="width:60px;"  name="txt_requisition_free_qty" id="txt_requisition_free_qty__<? echo $sl;?>"  class="text_boxes_numeric" placeholder="<? echo $reducible_requisition_qty; ?>" type="text"/>
                                    </td>

                                    <td width="100" align="right" valign="middle"  class="word-wrap"><input type="button" <? echo $action_btn_disabled_cond;?>  value="Action" id="alc_freebtn" style="width:80px; <? echo  $action_btn_bg_color_cond;?>" onClick="func_allocation_free(<? echo $sl; ?>,<? echo $free_ref; ?>,<? echo $reducible_wo_qty; ?>,<? echo $reducible_requisition_qty; ?>)"/></td>

                                </tr>
                                <?
                                $sl++;
                                $grand_total_ref_allocation_qty += $row["allocation_qnty"];
                                $grand_total_requisition_qty += $requisition_qnty;
                                $grand_total_demand_qty += $demand_qnty;
                                $grand_total_wo_qty += $total_wo_qnty;
                                $grand_total_wo_net_issue_qty += $wo_net_issue_qty;
                                $grand_total_rqsn_net_issue_qty += $rqsn_net_issue_qty;
                                $grand_total_total_issue_qty += $total_issue;
                                $grand_total_total_issue_rtn_qty += $total_issue_rtn;
                                $grand_total_total_net_issue += $total_net_issue;
                                $grand_total_allocation_free_qnty += $allocation_free_qnty;
                            }
                        }
                    }	
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="14" class="cls_tot">&nbsp;</th>
                        <th class="cls_tot"><? echo  number_format($grand_total_ref_allocation_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_requisition_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_demand_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_wo_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_wo_net_issue_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_rqsn_net_issue_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_total_issue_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_total_issue_rtn_qty,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_total_net_issue,2,".","") ; ?></th>
                        <th class="cls_tot"><? echo  number_format($grand_total_allocation_free_qnty,2,".","") ; ?></th>
                        <th class="cls_tot">&nbsp;</th>
                        <th class="cls_tot">&nbsp;</th>
                        <th class="cls_tot">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>

        
        <script> 
        $(document).ready(function()
        {
            $('#full_allocation_free_yes').val(0); // initialized
    
            $('#full_allocation_free_yes').change(function() 
            {
                
                if($(this).is(":checked")) 
                {
                    var returnVal = confirm("Would you like to full quantity un-allocate?");
                    
                    if(returnVal===true)
                    {
                        $('#full_allocation_free_yes').val(1);
                    }

                    $("input[name='txt_wo_free_qty']").each(function() {
                        $(this).attr("disabled", true);
                        $(this).val('');
                    });
                    
                    $("input[name='txt_requisition_free_qty']").each(function() {
                        $(this).attr("disabled", true);
                        $(this).val('');
                    });
                }
                else
                {
                    $('#full_allocation_free_yes').val(0);

                    $("input[name='txt_wo_free_qty']").each(function() {
                        $(this).attr("disabled", false);
                        $(this).val('');
                    }); 

                    $("input[name='txt_requisition_free_qty']").each(function() {
                        $(this).attr("disabled", false);
                        $(this).val('');
                    });
                }
                     
            });

        });
        </script>
	</fieldset>
	<?

    //echo "<br />Execution Time: " . (microtime(true) - $started).'S';
	foreach (glob("$user_name*.xls") as $filename)
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
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	$filename = "requires/" . $user_name . "_" . $name . ".xls";
	echo "$total_data####$filename";
	exit();
}

if($action == "yarn_allocation_free")
{
    $con = connect();

	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

    $process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

    $ydsw_free_qty=str_replace("'", "", $ydsw_free_qty);
	$requisition_free_qty=str_replace("'", "", $requisition_free_qty);
	$full_allocation_free_yes=str_replace("'", "", $full_allocation_free_yes);

    //$free_ref='4843**FAL-Fb-23-01115**68372**20931**34288**3940**287**17132**12909**100**1948**5278**3589___68372_17132_100_0_0_**5542___2___0___150,5538___7___100___187';
    list($poId,$bookingNo,$prodId,$alc_pk_id,$alc_dtls_id,$allocation_free_qnty,$new_allocation,$requisition_id,$requisition_no,$rqsn_net_issue_qty,$demand_pkid,$demand_reqsn_pk_id,$update_demand_data_str,$update_workorder_data_str,$current_stock,$global_allocation,$global_available,$job_no) = explode("**",$free_ref);
    //echo $poId."test";

    /* echo "<pre>";
    print_r( $save_string_arr );
    die; */
    //echo "###";
    //var_dump($prodId); 
    
    if( ($full_allocation_free_yes==1) && ((float)$allocation_free_qnty>0) && ($full_allocation_free_yes==1) ) // full allocation free 
    {
        $rID1 = $rID2 = $rID3 = $rID4 = $rID5 = $rID6 = $rID7 = $rID8 = true; 

        if((int)$prodId>0)
        {
            $rID1 =  execute_query("UPDATE inv_material_allocation_mst SET QNTY=$new_allocation,updated_by=999,update_date='".$pc_date_time."'  WHERE id=$alc_pk_id and item_id=$prodId and po_break_down_id=$poId",1);

            if(!$rID1)
            {
                echo "UPDATE inv_material_allocation_mst SET QNTY=$new_allocation,updated_by=999,update_date='".$pc_date_time."'  WHERE id=$alc_pk_id and item_id=$prodId and po_break_down_id=$poId";
                oci_rollback($con);
                disconnect($con);
                die;
            }
            
            $rID2 = execute_query("UPDATE inv_material_allocation_dtls SET QNTY=$new_allocation,updated_by=999,update_date='".$pc_date_time."' WHERE id=$alc_dtls_id and item_id=$prodId and po_break_down_id=$poId",1);

            if(!$rID2)
            {
                echo "UPDATE inv_material_allocation_dtls SET QNTY=$new_allocation,updated_by=999,update_date='".$pc_date_time."' WHERE id=$alc_dtls_id and item_id=$prodId and po_break_down_id=$poId";
                oci_rollback($con);
                disconnect($con);
                die;
            }

            if($global_allocation>=$allocation_free_qnty)
            {
                $rID3 = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty-$allocation_free_qnty) WHERE id=$prodId ",1);
            
                if(!$rID3)
                {
                    echo "UPDATE product_details_master SET allocated_qnty = (allocated_qnty-$allocation_free_qnty) WHERE id=$prodId ";
                    oci_rollback($con);
                    disconnect($con);
                    die;
                }
    
                $rID4 = execute_query("UPDATE product_details_master SET available_qnty = (current_stock-allocated_qnty) WHERE id=$prodId ",1);
               
                if(!$rID4)
                {
                    echo "UPDATE product_details_master SET available_qnty = (current_stock-allocated_qnty) WHERE id=$prodId ";
                    oci_rollback($con);
                    disconnect($con);
                    die;
                }
            }
           
        }
        
        if((int)$requisition_no>0)
        {
            $rID5 = execute_query("UPDATE ppl_yarn_requisition_entry SET yarn_qnty=$rqsn_net_issue_qty,updated_by=999,update_date='".$pc_date_time."' WHERE id=$requisition_id and requisition_no=$requisition_no and prod_id=$prodId",1);
            
            if(!$rID5)
            {
                echo "UPDATE ppl_yarn_requisition_entry SET yarn_qnty=$rqsn_net_issue_qty,updated_by=999,update_date='".$pc_date_time."' WHERE id=$requisition_id and requisition_no=$requisition_no and prod_id=$prodId";
                oci_rollback($con);
                disconnect($con);
                die;
            }
        }
        
        //$update_demand_data_str = 3265___57861_15041_250_0_0_,3312___57861_15041_0_0_0_**';
        //echo $update_demand_data_str; die;
        $update_demand_data_strarr = explode(",",$update_demand_data_str);
        foreach($update_demand_data_strarr as $key=>$value)
        {
            $update_demand_data_arr= explode("___",$value);
            $reqsn_demand_arr = explode("_",$update_demand_data_arr[1]);
            
           // echo "<pre>";
            //print_r($update_demand_data_arr);
            $reqsn_dtls_id = $update_demand_data_arr[0];
            $demand_data = $update_demand_data_arr[1];
            $reqsn_demand_qty = $reqsn_demand_arr[2];

            if((int)$reqsn_dtls_id>0)
            {
                $rID6 = execute_query("UPDATE ppl_yarn_demand_reqsn_dtls SET yarn_demand_qnty=$reqsn_demand_qty,updated_by=999,UPDATE_DATE='".$pc_date_time."' WHERE id=$reqsn_dtls_id",1);

                if(!$rID6)
                {
                    echo "UPDATE ppl_yarn_demand_reqsn_dtls SET yarn_demand_qnty=$reqsn_demand_qty,updated_by=999,UPDATE_DATE='".$pc_date_time."' WHERE id=$reqsn_dtls_id"."<br>";
                    oci_rollback($con);
                    disconnect($con);
                    die;
                }

                $rID7 = execute_query("UPDATE ppl_yarn_demand_entry_dtls SET demand_qnty=$reqsn_demand_qty,save_string='".$demand_data."',updated_by=999,UPDATE_DATE='".$pc_date_time."' WHERE id=$reqsn_dtls_id",1);

                if(!$rID7)
                {
                    echo "UPDATE ppl_yarn_demand_entry_dtls SET demand_qnty=$reqsn_demand_qty,save_string='".$demand_data."',updated_by=999,UPDATE_DATE='".$pc_date_time."' WHERE id=$reqsn_dtls_id";
                    oci_rollback($con);
                    disconnect($con);
                    die;
                }

            }          
        }

       /*  echo "<pre>";
        print_r($update_demand_data_arr); die; */


        //$update_workorder_data_str = '3589___68372_17132_100_0_0_**5542___2___0___150,5538___7___100___187';
        $update_wo_data_strarr = explode(",",$update_workorder_data_str);
        foreach($update_wo_data_strarr as $key=>$value)
        {
           
            $update_wo_data_arr= explode("___",$value);
            $wo_dtls_id = $update_wo_data_arr[0];
            $wo_color_id = $update_wo_data_arr[1];
            $new_wo_qty = $update_wo_data_arr[2];
            $prev_wo_qty = $update_wo_data_arr[3];

            if((int)$wo_dtls_id>0)
            {
                $rID8 = execute_query("UPDATE wo_yarn_dyeing_dtls SET yarn_wo_qty= $new_wo_qty WHERE id=$wo_dtls_id",1);
                if(!$rID8)
                {
                    echo "UPDATE wo_yarn_dyeing_dtls SET yarn_wo_qty= $new_wo_qty WHERE id=$wo_dtls_id"."<br>";
                    oci_rollback($con);
                    disconnect($con);
                    die;
                }
            } 
        }

        /* echo "5**".$rID1 ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID5 ."&&".  $rID6 ."&&". $rID7 ."&&".$rID8;
        oci_rollback($con);
        disconnect($con);
        die; */

        if ($db_type == 0)
        {
            if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 &&  $rID6 && $rID7 && $rID8)
            {
                mysql_query("COMMIT");
                echo "0####" . $poId;
            } else {
                mysql_query("ROLLBACK");
                echo "5####" . $poId;
            }

        }
        else if ($db_type == 2 || $db_type == 1)
        {
            if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 &&  $rID6 && $rID7 && $rID8)
            {
                oci_commit($con);
                echo "0####" . $poId;
            } else {
                oci_rollback($con);
                echo "5####" . $poId;
            }
        }

        disconnect($con);
        die;

    }
    
    if( ( $ydsw_free_qty>0 || $requisition_free_qty>0) ) // pertial allocation free
    {
        $issue_sql = "SELECT a.BOOKING_ID AS WO_ID,b.DEMAND_ID,a.ISSUE_BASIS, c.PO_BREAKDOWN_ID AS PO_ID,b.DYEING_COLOR_ID AS YARN_COLOR,b.PROD_ID AS PRODUCT_ID,b.REQUISITION_NO, c.QUANTITY AS ISSUE_QNTY FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c WHERE a.id=b.mst_id AND b.id=c.trans_id AND b.prod_id=c.prod_id and b.prod_id=$prodId AND c.po_breakdown_id=$poId AND a.issue_basis IN(1,3,8) AND a.entry_form=3 AND b.item_category=1 AND b.transaction_type=2 AND c.is_sales=1";
        //echo $issue_sql; die;

        $issue_sql_result = sql_select($issue_sql);
        $po_wise_issue_data_array = $po_wise_demand_issue_data_array = array();
        foreach($issue_sql_result as $row)
        {
            if($row['ISSUE_BASIS']==1)
            {
                $po_wise_issue_data_array[$row['WO_ID']][$row['PO_ID']][$row['YARN_COLOR']][$row['PRODUCT_ID']]+= $row['ISSUE_QNTY'];
            }
            else
            {
                $po_wise_issue_data_array[$row['REQUISITION_NO']][$row['PO_ID']][$row['PRODUCT_ID']]+= $row['ISSUE_QNTY'];
                $po_wise_demand_issue_data_array[$row['REQUISITION_NO']][$row['DEMAND_ID']][$row['PO_ID']][$row['PRODUCT_ID']]+= $row['ISSUE_QNTY'];
            } 
        }

        $wo_issue_return_sql = "SELECT a.BOOKING_ID AS WO_ID,a.RECEIVE_BASIS, c.PO_BREAKDOWN_ID AS PO_ID,b.DYEING_COLOR_ID AS YARN_COLOR,b.PROD_ID AS PRODUCT_ID,b.REQUISITION_NO,c.QUANTITY AS ISSUE_RTN_QNTY FROM INV_RECEIVE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c WHERE a.id=b.mst_id AND b.id=c.trans_id AND b.prod_id=c.prod_id AND b.prod_id=$prodId AND c.po_breakdown_id=$poId AND a.receive_basis IN (1,3,8) AND a.entry_form=9 AND b.item_category=1 AND b.transaction_type=4 AND c.is_sales=1";
        //echo $wo_issue_return_sql; die;
        
        $wo_issue_return_sql_result = sql_select($wo_issue_return_sql);
        $po_wise_issue_return_data_array = $po_wise_demand_issue_return_data_array = array();
        foreach($wo_issue_return_sql_result as $row)
        {
            if($row['RECEIVE_BASIS']==1)
            {  
                $po_wise_issue_return_data_array[$row['WO_ID']][$row['PO_ID']][$row['YARN_COLOR']][$row['PRODUCT_ID']]+= $row['ISSUE_RTN_QNTY']; 
            }
            else
            {
                $po_wise_issue_return_data_array[$row['REQUISITION_NO']][$row['PO_ID']][$row['PRODUCT_ID']]+= $row['ISSUE_RTN_QNTY']; 
                $po_wise_demand_issue_return_data_array[$row['REQUISITION_NO']][$row['WO_ID']][$row['PO_ID']][$row['PRODUCT_ID']]+= $row['ISSUE_RTN_QNTY']; 
            }           
        }

        if($ydsw_free_qty>0) // Work order 
        {
            $wo_sql = "Select a.ID,a.mst_id as WO_ID,b.ID as PO_ID,a.PRODUCT_ID, a.YARN_COLOR ,SUM(a.YARN_WO_QTY) AS YARN_WO_QTY from wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls a,fabric_sales_order_mst b where c.id=a.mst_id and a.job_no=b.job_no and a.product_id=$prodId and a.job_no='$job_no' and b.id=$poId and c.is_sales=1 and c.entry_form=135 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.mst_id,b.id,a.product_id,a.yarn_color ORDER BY a.id";
            //echo $wo_sql; die;
            $wo_sql_result = sql_select($wo_sql);

            $update_wo_field = "yarn_wo_qty*updated_by*update_date"; 
            $update_wo_data = array();
            $reducible_balance_qty = 0;
            foreach($wo_sql_result as $row)
            {
                $wo_qty = $row['YARN_WO_QTY']; 
                $issue_qty = $po_wise_issue_data_array[$row['WO_ID']][$row['PO_ID']][$row['YARN_COLOR']][$row['PRODUCT_ID']];
                $issue_rtn_qty = $po_wise_issue_return_data_array[$row['WO_ID']][$row['PO_ID']][$row['YARN_COLOR']][$row['PRODUCT_ID']];
                $net_issue_qty = ($issue_qty-$issue_rtn_qty);
                $wo_balance_qnty =  ($wo_qty-$net_issue_qty);
                //echo $row['ID']."==".$wo_qty."==".$wo_balance_qnty."<br>";

                $reducible_balance_qty = ($wo_balance_qnty-$ydsw_free_qty);
                if($reducible_balance_qty>=0)
                {
                    $new_wo_qty = ($wo_qty-$ydsw_free_qty);

                    $update_wo_id_array[] = $row['ID'];
					$update_wo_data[$row['ID']] = explode("*", ("" . $new_wo_qty . "*" . $user_name . "*'" . $pc_date_time . "'"));
                    break;
                }
                else
                {   
                    $new_wo_qty = ($wo_qty-$wo_balance_qnty);
                    $ydsw_free_qty = abs($ydsw_free_qty-$wo_balance_qnty);

                    $update_wo_id_array[] = $row['ID'];
                    $update_wo_data[$row['ID']] = explode("*", ("" . $new_wo_qty . "*" . $user_name . "*'" . $pc_date_time . "'"));               
                }
            }
           
        }

        /* echo "<pre>";
        print_r($update_wo_data);
        die; */

        if($requisition_free_qty>0) // Requisition and demand start
        {
            $requisition_sql = "SELECT b.ID, a.PO_ID,b.REQUISITION_NO,b.prod_id as PRODUCT_ID,sum(b.YARN_QNTY) as YARN_QNTY  FROM  ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b WHERE a.dtls_id=b.knit_id AND a.booking_no='$bookingNo' AND a.po_id=$poId AND b.prod_id=$prodId AND a.is_sales=1 AND a.status_active=1 AND a.is_deleted=0 GROUP BY b.ID, a.PO_ID,b.REQUISITION_NO,b.prod_id ORDER BY b.id";
            //echo  $requisition_sql;
            $requisition_sql_result = sql_select($requisition_sql);
            
            $update_requisition_field = "yarn_qnty*updated_by*update_date"; 
            $update_requisition_data = array();
            $reducible_balance_qty = 0;
            $user_given_requisition_free_qty = str_replace("'","",$requisition_free_qty);
            foreach($requisition_sql_result as $row) // Requisition 
            {
                $requisition_no_array[$row['REQUISITION_NO']] = $row['REQUISITION_NO'];

                $requisition_qty = $row['YARN_QNTY'];
                $issue_qty = $po_wise_issue_data_array[$row['REQUISITION_NO']][$row['PO_ID']][$row['PRODUCT_ID']];
                $issue_rtn_qty = $po_wise_issue_return_data_array[$row['REQUISITION_NO']][$row['PO_ID']][$row['PRODUCT_ID']];
                $net_issue_qty = ($issue_qty-$issue_rtn_qty);
                $requisition_balance_qnty =  ($requisition_qty-$net_issue_qty);                
                //echo $row['ID']."==".$requisition_qty."==".$requisition_balance_qnty."<br>";
                $reducible_balance_qty = ($requisition_balance_qnty-$user_given_requisition_free_qty);

                if($reducible_balance_qty>=0) // prepare for update data 
                {
                    $new_requisition_qty = ($requisition_qty-$user_given_requisition_free_qty);

                    $update_requisition_id_array[] = $row['ID'];
					$update_requisition_data[$row['ID']] = explode("*", ("" . $new_requisition_qty . "*" . $user_name . "*'" . $pc_date_time . "'"));
                    break;
                }
                else
                {    
                    $new_requisition_qty = ($requisition_qty-$requisition_balance_qnty);  
                    $user_given_requisition_free_qty = abs($user_given_requisition_free_qty-$requisition_balance_qnty);

                    $update_requisition_id_array[] = $row['ID'];            
                    $update_requisition_data[$row['ID']] = explode("*", ("" . $new_requisition_qty . "*" . $user_name . "*'" . $pc_date_time . "'"));
                                 
                }
            }

            if(!empty($requisition_no_array)) // requisition demand dtls
            {
                $demand_sql = "SELECT a.mst_id AS DEMAND_PK, a.id AS DEMAND_DTLS_PK,b.id AS DEMAND_REQSN_PK,b.REQUISITION_ID,b.REQUISITION_NO,b.PROD_ID AS PRODUCT_ID,b.CONE_QTY,b.CTN_QTY,b.REMARKS, a.SAVE_STRING,SUM(b.YARN_DEMAND_QNTY) AS YARN_DEMAND_QNTY FROM ppl_yarn_demand_entry_dtls a,ppl_yarn_demand_reqsn_dtls b WHERE a.id=b.dtls_id AND a.mst_id=b.mst_id AND a.requisition_no=b.requisition_no AND b.prod_id=$prodId AND b.requisition_no in (".implode(",",$requisition_no_array).") AND yarn_demand_qnty>0 GROUP BY a.id, a.mst_id,b.id, b.requisition_no,b.requisition_id,b.prod_id, b.cone_qty,b.ctn_qty,b.remarks,a.save_string ORDER BY b.requisition_id,b.requisition_no,b.id";
                //echo $demand_sql; die;
                $demand_sql_result = sql_select($demand_sql);

                $user_given_demand_free_qty = str_replace("'","",$requisition_free_qty);
                $update_demand_reqsn_field = "yarn_demand_qnty*updated_by*update_date"; 
                $update_demand_rqsnn_data = $update_demand_data_string = array();
                $reducible_balance_qty = 0;
                foreach($demand_sql_result as $drow)
                {
                    $demand_qty = $drow['YARN_DEMAND_QNTY'];
                    $issue_qty = $po_wise_demand_issue_data_array[$drow['REQUISITION_NO']][$drow['DEMAND_PK']][$poId][$drow['PRODUCT_ID']];
                    $issue_rtn_qty = $po_wise_demand_issue_return_data_array[$drow['REQUISITION_NO']][$drow['DEMAND_PK']][$poId][$drow['PRODUCT_ID']];
                    $net_issue_qty = ($issue_qty-$issue_rtn_qty);
                    $demand_balance_qnty =  ($demand_qty-$net_issue_qty);

                    //echo $drow['DEMAND_REQSN_PK']."==".$demand_qty."==".$demand_balance_qnty."==".$issue_rtn_qty."<br>";
                    //echo $drow['DEMAND_PK']."=>".$demand_balance_qnty."==".$user_given_demand_free_qty."<br>";//."-".$user_given_demand_free_qty."==".$reducible_balance_qty."<br>";

                    $reducible_balance_qty = ($demand_balance_qnty-$user_given_demand_free_qty);

                    if($reducible_balance_qty>=0) 
                    {
                        $new_requisiiton_demand_qty = ($demand_qty-$demand_balance_qnty);

                        $update_demand_reqsn_id_array[] = $drow['DEMAND_REQSN_PK']; 
                        $update_demand_rqsnn_data[$drow['DEMAND_REQSN_PK']] = explode("*", ("" . $new_requisiiton_demand_qty . "*" . $user_name . "*'" . $pc_date_time . "'")); // prepare for update data 
                       
                        $new_save_string = $drow['PRODUCT_ID']."_".$drow['REQUISITION_ID']."_".$new_requisiiton_demand_qty."_".$drow['CONE_QTY']."_".$drow['CTN_QTY']."_".$drow['REMARKS'];
                        $update_demand_data_string[$drow['DEMAND_DTLS_PK']][$drow['DEMAND_PK']][$drow['REQUISITION_NO']]['new_save_string'] = $new_save_string;
                        $update_demand_data_string[$drow['DEMAND_DTLS_PK']][$drow['DEMAND_PK']][$drow['REQUISITION_NO']]['previous_save_string'] = $drow['SAVE_STRING'];

                        break;
                    }
                    else
                    {     
                        $new_requisiiton_demand_qty = ($demand_qty-$demand_balance_qnty); 
                        $user_given_demand_free_qty = abs($user_given_demand_free_qty-$demand_balance_qnty);   

                        $update_demand_reqsn_id_array[] = $drow['DEMAND_REQSN_PK'];          
                        $update_demand_rqsnn_data[$drow['DEMAND_REQSN_PK']] = explode("*", ("" . $new_requisiiton_demand_qty . "*" . $user_name . "*'" . $pc_date_time . "'")); // prepare for update data                       
 
                        $new_save_string = $drow['PRODUCT_ID']."_".$drow['REQUISITION_ID']."_".$new_requisiiton_demand_qty."_".$drow['CONE_QTY']."_".$drow['CTN_QTY']."_".$drow['REMARKS'];
                        $update_demand_data_string[$drow['DEMAND_DTLS_PK']][$drow['DEMAND_PK']][$drow['REQUISITION_NO']]['new_save_string'] = $new_save_string;
                        $update_demand_data_string[$drow['DEMAND_DTLS_PK']][$drow['DEMAND_PK']][$drow['REQUISITION_NO']]['previous_save_string'] = $drow['SAVE_STRING'];
                                          
                    }
                }

                if(!empty($update_demand_data_string)) // 
                {
                    $update_demand_field = "demand_qnty*save_string*updated_by*update_date"; 
                    $update_demand_data = array();
                    foreach($update_demand_data_string as $dtls_pk_id=>$master_pk_id_arr)
                    {
                        foreach($master_pk_id_arr as $master_pk_id=>$requisition_no_arr)
                        {
                            foreach($requisition_no_arr as $demand_data) // prepare for update data 
                            {
                                $previous_save_string = $demand_data['previous_save_string'];
                                $prod_new_save_string =  $demand_data['new_save_string'];

                                $previous_string_arr = explode(",",$previous_save_string);
                                $update_demand_string = "";
                                $update_total_demand_qty = 0;
                                foreach($previous_string_arr as $prev_string_data)
                                {
                                    $prev_dtls_string_arr = explode("_",$prev_string_data);
                                    $new_save_string_arr = explode("_", $prod_new_save_string);
                                    
                                    $demand_prod_id = $prev_dtls_string_arr[0];                                   
                                    $demand_requisition_id = $prev_dtls_string_arr[1];
                                    $demand_qnty = ( ($demand_prod_id==$new_save_string_arr[0]) &&  ($demand_requisition_id==$new_save_string_arr[1]) ) ? $new_save_string_arr[2] : $prev_dtls_string_arr[2];
                                    $demand_cone_qty  = $prev_dtls_string_arr[3];
                                    $demand_ctn_qty  = $prev_dtls_string_arr[4];
                                    $demand_remarks  = $prev_dtls_string_arr[5];

                                    $update_total_demand_qty += $demand_qnty;

                                    if($update_demand_string=="")
                                    {
                                        $update_demand_string = $demand_prod_id."_".$demand_requisition_id."_".$demand_qnty."_".$demand_cone_qty."_".$demand_ctn_qty."_".$demand_remarks;
                                    }
                                    else{
                                        $update_demand_string .= ",".$demand_prod_id."_".$demand_requisition_id."_".$demand_qnty."_".$demand_cone_qty."_".$demand_ctn_qty."_".$demand_remarks;
                                    }
                                    
                                    $update_demand_data_id_array[] = $dtls_pk_id; 
                                    $update_demand_data[$dtls_pk_id] = explode("*", ("" . $update_total_demand_qty . "*'" .$update_demand_string. "'*" .$user_name . "*'" . $pc_date_time . "'")); // prepare for update data 

                                }
                                
                            }
                        }
                    }
                } 
            }

            /* echo "<pre>";
            print_r($update_requisition_data); */
            
        } // End requisition and demand 
              
        $rID1 = $rID2 = $rID3 = $rID4 = $rID5 = $rID6 = $rID7 = $rID8 = true; 

        if(!empty($update_wo_id_array))
        {
            $rID1 = execute_query(bulk_update_sql_statement("wo_yarn_dyeing_dtls", "id", $update_wo_field, $update_wo_data, $update_wo_id_array),0);
        }

        if(!empty($update_requisition_id_array))
        {
           
            $rID2 = execute_query(bulk_update_sql_statement("ppl_yarn_requisition_entry", "id", $update_requisition_field, $update_requisition_data, $update_requisition_id_array),0);
        }
        
        if(!empty($update_demand_reqsn_id_array))
        {
            $rID3 = execute_query(bulk_update_sql_statement("ppl_yarn_demand_reqsn_dtls", "id", $update_demand_reqsn_field, $update_demand_rqsnn_data, $update_demand_reqsn_id_array),0);
        }
        
        if(!empty($update_demand_data_id_array))
        {
            $rID4 = execute_query(bulk_update_sql_statement("ppl_yarn_demand_entry_dtls", "id", $update_demand_field, $update_demand_data, $update_demand_data_id_array),0);
        }

        $allocation_reduce_qnty = ($ydsw_free_qty+$requisition_free_qty); 

        if((int)$prodId>0)
        {
            $rID5 =  execute_query("UPDATE inv_material_allocation_mst SET QNTY=(QNTY- $allocation_reduce_qnty),updated_by=999,update_date='".$pc_date_time."'  WHERE id=$alc_pk_id and item_id=$prodId and po_break_down_id=$poId",0);
            $rID6 = execute_query("UPDATE inv_material_allocation_dtls SET QNTY=(QNTY- $allocation_reduce_qnty),updated_by=999,update_date='".$pc_date_time."' WHERE id=$alc_dtls_id and item_id=$prodId and po_break_down_id=$poId",0);

            if($global_allocation>=$allocation_reduce_qnty)
            {
                $rID7 = execute_query("UPDATE product_details_master SET allocated_qnty = (allocated_qnty-$allocation_reduce_qnty) WHERE id=$prodId ",0);
                $rID8 = execute_query("UPDATE product_details_master SET available_qnty = (current_stock-allocated_qnty) WHERE id=$prodId ",0);
            }          
        }


        /* echo "5**".$rID1 ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID5 ."&&". $rID6 ."&&". $rID7 ."&&". $rID8;
        oci_rollback($con);
        disconnect($con);
        die;  */

        if ($db_type == 0)
        {
            if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8)
            {
                mysql_query("COMMIT");
                echo "0####" . $poId;
            } else {
                mysql_query("ROLLBACK");
                echo "5####" . $poId;
            }

        }
        else if ($db_type == 2 || $db_type == 1)
        {
            if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 )
            {
                oci_commit($con);
                echo "0####" . $poId;
            } else {
                oci_rollback($con);
                echo "5####" . $poId;
            }
        }

        disconnect($con);
        die;

    }

    
    
}
?>