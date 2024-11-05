<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../../includes/common.php');
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
	echo create_drop_down("cbo_buyer_id", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "");
	exit();
}

$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

/*
|------------------------------------------------------------------------
| for job_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "job_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        var selected_id = new Array;
        var selected_name = new Array;

        /* function check_all_data()
         {
         var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
         tbl_row_count = tbl_row_count - 1;

         for (var i = 1; i <= tbl_row_count; i++)
         {
         $('#tr_' + i).trigger('click');
         }
         }

         function toggle(x, origColor) {
         var newColor = 'yellow';
         if (x.style) {
         x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
         }
         }

         function js_set_value_job(str) {

         if (str != "")
         str = str.split("_");

         toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

         if (jQuery.inArray(str[1], selected_id) == -1) {
         selected_id.push(str[1]);
         selected_name.push(str[2]);

         } else {
         for (var i = 0; i < selected_id.length; i++) {
         if (selected_id[i] == str[1])
         break;
         }
         selected_id.splice(i, 1);
         selected_name.splice(i, 1);
         }
         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         id += selected_id[i] + ',';
         name += selected_name[i] + '*';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hide_job_no').val(id);
         $('#hide_job_id').val(name);
     }*/


     function js_set_value_job(str) {
            //alert(str);
            $('#hide_job_no').val(str);
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
                            onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'buyer_wise_daily_yarn_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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

	$sql = "select a.id, a.job_no, $year_field, a.company_id, a.buyer_id, a.style_ref_no,a.booking_date,a.sales_booking_no from  fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by a.id, a.booking_date";
	// echo $sql;	die;
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
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(str)
		{
            var booking_no = str.split("_");
            //alert(str);
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
            show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' + document.getElementById('cbo_year_selection').value + '**' + document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'buyer_wise_daily_yarn_stock_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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
                            $dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../') ";
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
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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
    $started = microtime(true);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_machine_no = str_replace("'", "", $txt_machine_no);


	$cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
	$based_on = str_replace("'", "", $cbo_based_on);
	$presentationType = str_replace("'", "", $presentationType);
	$type = str_replace("'", "", $cbo_type);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$company_name = $cbo_company_name;
	$companyId = str_replace("'", "", $cbo_company_name);

	//var_dump($based_on);

	$buyer_dtls = return_library_array("select buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.short_name", 'id', 'short_name');
    $supplier_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
    $brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$yrn_count_dtls = get_yarn_count_array();
	$yrn_color_dtls = get_color_array();


	if ($cbo_buyer_id != 0)
	{
		$buyer_cond = " AND A.CUSTOMER_BUYER = ".$cbo_buyer_id;
	}
	else
	{
		$buyer_cond = "";
	}

	$sales_no_cond = "";
	if (str_replace("'", "", $txt_sales_no) != "")
	{
		$chk_prefix_sales_no=explode("-",str_replace("'", "", $txt_sales_no));
		if($chk_prefix_sales_no[3]!="")
		{
			$sales_number = "%" . trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND A.JOB_NO LIKE '".$sales_number."'";
		}
		else
		{
			$sales_number = trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND A.JOB_NO_PREFIX_NUM = '".$sales_number."'";
		}
	}

	$booking_search_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
		$booking_search_cond = "  AND A.SALES_BOOKING_NO LIKE '$booking_number'";
	}

	$year_field = "TO_CHAR(E.INSERT_DATE,'YYYY') AS YEAR";

	//for date
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
	{
		if ($based_on == 2)
		{
			$date_cond = " AND A.BOOKING_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		}
		else
		{
			$date_cond2 = " AND A.BOOKING_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		}
	}
	else
	{
		$date_cond = "";
		$date_cond2 = "";
	}

	if ($based_on == 1)
	{
		$booking_sql = "SELECT A.ID,A.BOOKING_NO
		FROM WO_BOOKING_MST A
		WHERE A.COMPANY_ID=$companyId  ".$date_cond2."  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
		ORDER BY A.ID ";
		//echo $booking_sql;die;
		$bookArray = sql_select($booking_sql);
		$bookDateArr = array();
		foreach ($bookArray as $row)
		{
			array_push($bookDateArr,$row['BOOKING_NO']);
		}

		if($bookDateArr)
		{
			$bookDateCond =" ".where_con_using_array($bookDateArr,1,'A.SALES_BOOKING_NO')." ";
		}
	}


	$sql = "SELECT A.ID AS SALES_ID, B.YARN_COUNT_ID, B.COMPOSITION_ID, B.COMPOSITION_PERC, B.COLOR_ID, B.YARN_TYPE, A.CUSTOMER_BUYER, A.WITHIN_GROUP, A.STYLE_REF_NO, A.JOB_NO, A.SALES_BOOKING_NO, SUM(B.CONS_QTY) as CONS_QTY
	FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_YARN_DTLS B
	WHERE  A.ID = B.MST_ID AND A.COMPANY_ID=$companyId  ".$buyer_cond.$sales_no_cond.$date_cond.$booking_search_cond.$bookDateCond."  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
	GROUP BY  A.ID, B.YARN_COUNT_ID, B.COMPOSITION_ID, B.COMPOSITION_PERC, B.COLOR_ID, B.YARN_TYPE, A.CUSTOMER_BUYER, A.WITHIN_GROUP, A.STYLE_REF_NO, A.JOB_NO, A.SALES_BOOKING_NO
	ORDER BY A.ID ";


	//echo $sql;
	//die;
	$nameArray = sql_select($sql);
	if(empty($nameArray))
	{
		echo get_empty_data_msg();
		die;
	}


	$print_data = array();
	$sales_ids_arr = array();
	$sales_prog_qty = array();
	$duplicate_check = array();
	$jo_no_chk = array();
	$job_no_arr = array();
	foreach ($nameArray as $row)
	{

		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['SALES_ID'] = $row['SALES_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['JOB_NO'] = $row['JOB_NO'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['CUSTOMER_BUYER'] = $row['CUSTOMER_BUYER'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['COMPOSITION_ID'] = $row['COMPOSITION_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['COMPOSITION_PERC'] = $row['COMPOSITION_PERC'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['COLOR_ID'] = $row['COLOR_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['CONS_QTY'] = $row['CONS_QTY'];


		//for sales booking qty

		if($duplicate_check[$row['SALES_ID']] == '')
		{
			$duplicate_check[$row['SALES_ID']] = $row['SALES_ID'];
			array_push($sales_ids_arr, $row['SALES_ID']);
		}

		if($jo_no_chk[$row['JOB_NO']] == "")
		{
			$jo_no_chk[$row['JOB_NO']] = $row['JOB_NO'];
			array_push($job_no_arr,$row['JOB_NO']);
		}
	}
	unset($nameArray);
	// echo "<pre>";
	// print_r($print_data);
	// echo "</pre>";

	$s_dtls_sql = "SELECT A.MST_ID, SUM(A.GREY_QTY) as GREY_QTY
	FROM FABRIC_SALES_ORDER_dtls A
	WHERE  A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 ".where_con_using_array($sales_ids_arr,0,'A.MST_ID')."
	GROUP BY  A.MST_ID
	ORDER BY A.MST_ID ";
	$s_dtls_result = sql_select($s_dtls_sql);
	$s_dtls_info_arr = array();
	foreach ($s_dtls_result as $row)
	{
		$s_dtls_info_arr[$row['MST_ID']]['GREY_QTY'] += $row['GREY_QTY'];
	}
	unset($s_dtls_result);

	$sql_product = "SELECT ID, LOT, SUPPLIER_ID, BRAND, PRODUCT_NAME_DETAILS FROM PRODUCT_DETAILS_MASTER WHERE COMPANY_ID=$companyId AND ID IN(SELECT ITEM_ID FROM INV_MATERIAL_ALLOCATION_MST WHERE ITEM_CATEGORY=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 )";
	//".where_con_using_array($job_no_arr,1,'JOB_NO')."
	//echo $sql_product;die;
	$sql_product_rslt = sql_select($sql_product);
	$product_data_arr = array();
	$prod_ids_arr = array();
	foreach($sql_product_rslt as $row)
	{
        $product_data_arr[$row['ID']]['lot'] = $row['LOT'];
        $product_data_arr[$row['ID']]['supplier_id'] = $supplier_arr[$row['SUPPLIER_ID']];
        $product_data_arr[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
		$product_data_arr[$row['ID']]['product_name'] = $row['PRODUCT_NAME_DETAILS'];

		if($prod_id_chk[$row['ID']] == "")
		{
			$prod_id_chk[$row['ID']] = $row['ID'];
			array_push($prod_ids_arr,$row[csf('ID')]);
		}
	}


	$aloca_sql = "SELECT A.ID, A.JOB_NO AS SALES_ORDER_NO,  A.ITEM_ID, A.QNTY AS QTY FROM INV_MATERIAL_ALLOCATION_MST A WHERE  A.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND IS_SALES=1 ".where_con_using_array($prod_ids_arr,1,'A.ITEM_ID')."";
	//".where_con_using_array($job_no_arr,1,'A.JOB_NO')."
	//echo $aloca_sql;
	$aloca_rslt = sql_select($aloca_sql);

	foreach($aloca_rslt as $row)
	{
        $data_arr[$row['SALES_ORDER_NO']]['lot'] .= $product_data_arr[$row['ITEM_ID']]['lot'].',';
        $data_arr[$row['SALES_ORDER_NO']]['supplier_id'] .= $product_data_arr[$row['ITEM_ID']]['supplier_id'].',';
        $data_arr[$row['SALES_ORDER_NO']]['brand'] .= $product_data_arr[$row['ITEM_ID']]['brand'].',';
		$data_arr[$row['SALES_ORDER_NO']]['product_id'] .= $row['ITEM_ID'].',';
		$data_arr[$row['SALES_ORDER_NO']]['product_name'] .= $product_data_arr[$row['ITEM_ID']]['product_name'].',';
		$data_arr[$row['SALES_ORDER_NO']]['qty'] .= $row['QTY'].',';
		$sales_arr[$row['ITEM_ID']]['SALES_ORDER_NO'] .= $row['SALES_ORDER_NO'].',';
	}
	unset($aloca_rslt);
	//var_dump($data_arr);
	$fso_data_arr = array();
	foreach($sql_product_rslt as $row)
	{
		$fso_data_arr[$row['LOT']]['sales_order_no'] =$sales_arr[$row['ID']]['SALES_ORDER_NO'];
	}
	unset($sql_product_rslt);

	$prog_sql = "SELECT A.ID, B.PO_ID
	FROM PPL_PLANNING_INFO_ENTRY_DTLS A, PPL_PLANNING_ENTRY_PLAN_DTLS B
	WHERE A.ID = B.DTLS_ID AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1
	".where_con_using_array($sales_ids_arr,0,'B.PO_ID')."
	GROUP BY A.ID, B.PO_ID
	ORDER BY A.ID";
	//echo $sql;

	$prog_rslt = sql_select($prog_sql);
	$prog_data_arr = array();
	$prog_duplicate_chk = array();
	$prog_no_arr = array();
	foreach($prog_rslt as $row)
	{
		$prog_data_arr[$row['PO_ID']]['ID'] .= $row['ID'].',';
		if($prog_duplicate_chk[$row['ID']] == "")
		{
			$prog_duplicate_chk[$row['ID']] = $row['ID'];
			array_push($prog_no_arr,$row[csf('ID')]);
		}
	}
	unset($prog_rslt);

	$sql_data = sql_select("SELECT A.ISSUE_NUMBER, B.ID AS TRANS_ID, C.KNIT_ID AS PROGRAM_NO, B.CONS_QUANTITY, A.ID AS ISSUE_ID, D.LOT, D.YARN_COUNT_ID FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, PRODUCT_DETAILS_MASTER D WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID = D.ID AND C.PROD_ID = D.ID AND B.RECEIVE_BASIS in(3,8) AND A.ISSUE_BASIS in(3,8) AND A.ENTRY_FORM = 3 AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 ".where_con_using_array($prog_no_arr,0,'C.KNIT_ID')." ");
	$transId_chk = array();
	foreach($sql_data as $row)
	{
		if($transId_chk[$row['TRANS_ID']] == "")
		{
			$transId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
			$knit_issue_arr[$row['PROGRAM_NO']]['qnty'] += $row['CONS_QUANTITY'];
			$knit_issue_arr[$row['PROGRAM_NO']]['lot'][$row['LOT']] = $row['LOT'];
			$knit_issue_arr[$row['PROGRAM_NO']]['issue_id'] .= $row['ISSUE_ID'].",";
			$knit_issue_arr[$row['PROGRAM_NO']]['issue_number'][$row['ISSUE_NUMBER']] = $row['ISSUE_NUMBER'];
			$knit_issue_arr[$row['PROGRAM_NO']]['trans_id'] .= $row['TRANS_ID'].",";
			$knit_issue_arr[$row['PROGRAM_NO']]['yarn_count'][$row['YARN_COUNT_ID']] .= $yarn_count_dtls[$row['YARN_COUNT_ID']].",";
		}
	}
	unset( $sql_data);

	$sql_allocation = "SELECT A.JOB_NO, SUM(A.QNTY) AS ALLOCATE_QTY, B.LOT FROM INV_MATERIAL_ALLOCATION_DTLS A,PRODUCT_DETAILS_MASTER B WHERE A.ITEM_ID=B.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.QNTY>0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND IS_SALES=1 GROUP BY A.JOB_NO, B.LOT";

	$allocation_rslt = sql_select($sql_allocation);

	$aqof_arr = array();
	foreach($allocation_rslt as $row)
	{
		$aqof_arr[$row['JOB_NO']][$row['LOT']]['allocate_qty'] = $row['ALLOCATE_QTY'];
	}
	unset($allocation_rslt);

	$sih_sql="SELECT  A.PROD_ID, A.TRANSACTION_TYPE, A.CONS_QUANTITY  FROM INV_TRANSACTION A, PRODUCT_DETAILS_MASTER B WHERE A.PROD_ID = B.ID AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND A.COMPANY_ID='$companyId'";
	//echo $sih_sql;

	$sih_rslt = sql_select($sih_sql);

	$stock_in_hand = array();
	foreach($sih_rslt as $row)
	{
		if($row["TRANSACTION_TYPE"] == 1 || $row["TRANSACTION_TYPE"] == 4 || $row["TRANSACTION_TYPE"] == 5)
		{
			$stock_in_hand[$row['PROD_ID']]['cons_quantity'] += $row['CONS_QUANTITY'];
		}
		else
		{
			$stock_in_hand[$row['PROD_ID']]['cons_quantity'] -= $row['CONS_QUANTITY'];
		}

	}
	unset($allocation_rslt);


	$sales_no_count=array();
	foreach($print_data as $k_sales_no=>$v_sales_no)
	{

		foreach($v_sales_no as $k_yarn_count_id=>$v_yarn_count_id)
		{

			foreach ($v_yarn_count_id as $k_composition_id => $v_composition_id)
			{

				foreach ($v_composition_id as $k_composition_perc => $v_composition_perc)
				{

					foreach ($v_composition_perc as $k_color_id => $v_color_id)
					{
						foreach ($v_color_id as $key=>$val)
						{
							$sales_no_count[$k_sales_no]++;
						}
					}
				}
			}
		}
	}


	$col = 16;
	$colspan = 22;
	$tbl_width = 2310;

	if ($presentationType == 1)
	{
		ob_start();
		?>
		<style>
			.cls_break td{
				word-break:break-all;
			}

			.cls_tot{
				text-align:right;
				font-weight:bold;
			}
		</style>
		<fieldset style="width:<? echo $tbl_width; ?>px;">
			<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
				<tr>
					<td align="center" width="100%" colspan="<? echo $col; ?>" style="font-size:16px">
					<strong>Buyer Wise Daily Yarn Stock Report[Sales]</strong></td>
					<td></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
				<thead>
				<th width="40">SL</th>
				<th width='120'>Booking No</th>
				<th width="120">Tex. Reff</th>
				<th width="100">Buyer</th>
				<th width="130">Style No</th>
				<th width="200">Yarn Description (FSO)</th>
				<th width="100">Qty</th>
                <th width="280">Yarn Description <br>(Allocation)</th>
                <th width="100">Lot</th>
                <th width="100">Brand</th>
				<th width="180">Supplier</th>
				<th width="100">Grey Qty(FSO)</th>
				<th width="100">Allocation Qty </th>
				<th width="80">Allocation Balance</th>
				<th width="100">Sample Yarn Issue Qty</th>
				<th width="100">Allocated Yarn Issue Qty</th>
				<th width="120">Allocated Qty(Other FSO)</th>
				<th width="120">Stock In Hand</th>
				<th width="120">Remarks</th>
			</thead>
			</table>
			<div style="width:<? echo $tbl_width - 20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table" id="tbl_list_search">
					<tbody>
					<?

					$i = 1;
					$g_tot_sales_qty = 0;
					$g_tot_alocat = 0;
					$g_allocat_balance = 0;
					$g_qnty = $g_tot_aqof = 0;
					$g_tot_stockInHand = 0;
					foreach($print_data as $k_sales_no=>$v_sales_no)
					{
						foreach($v_sales_no as $k_yarn_count_id=>$v_yarn_count_id)
						{
							foreach ($v_yarn_count_id as $k_composition_id => $v_composition_id)
							{
								foreach ($v_composition_id as $k_composition_perc => $v_composition_perc)
								{
									foreach ($v_composition_perc as $k_color_id => $v_color_id)
									{
										$s_tot_grey_qnty = 0;
										$s_tot_alocat = 0;
										$s_tot_allocat_ba = 0;
										$s_tot_qnty = 0;
										$s_tot_aqof = 0;
										$s_tot_stockInHand = 0;

										foreach ($v_color_id as $key=>$val)
										{
											$yrn_desc = $yrn_count_dtls[$k_yarn_count_id].' '.$composition[$k_composition_id].' '.$k_composition_perc.' '.$yarn_type[$key].' '.$yrn_color_dtls[$k_color_id];

											$r_span = $sales_no_count[$k_sales_no];
											?>
											<tr bgcolor="#FFFFFF" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" class="cls_break">

											<?
											if(!in_array($k_sales_no,$sales_chk))
											{
												$sales_chk[]=$k_sales_no;
												?>

												<td width="40" rowspan="<? echo $r_span; ?>" valign="middle" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>
												<td width="120" rowspan="<? echo $r_span; ?>" valign="middle" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $val['SALES_BOOKING_NO']; ?></td>
												<td width="120" rowspan="<? echo $r_span; ?>" valign="middle" title="<? echo 'Sales Id : '.$val['SALES_ID']?>" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $k_sales_no; ?></td>
												<td width="100" rowspan="<? echo $r_span; ?>" valign="middle" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $buyer_dtls[$val['CUSTOMER_BUYER']]; ?></td>
												<td width="130" rowspan="<? echo $r_span; ?>" valign="middle" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $val['STYLE_REF_NO']; ?></td>

											<? }?>
												<td width="200" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $yrn_desc; ?></td>
												<td width="100" align="right" style="word-wrap: break-word;word-break: break-all;" ><? $cons_qty = $val['CONS_QTY']; echo number_format($cons_qty,2); $tot_cons_qty +=$cons_qty; ?></td>

												<?	if(!in_array($k_sales_no,$sale_chk))
												{
												$sale_chk[]=$k_sales_no;
												?>

												<td width="280" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
												    <?
													$product_name = $data_arr[$k_sales_no]['product_name'];
													echo $alocat_product_name =implode('<br>',explode(",",chop($product_name ,",")));
												    ?>
												</td>

                                                <td width="100" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
                                                    <?
                                                    $lot = $data_arr[$k_sales_no]['lot'];
                                                    echo $alocat_lot =implode('<br>',explode(",",chop($lot ,",")));
                                                    ?>
                                                </td>

                                                <td width="100" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
                                                    <?
                                                    $brand = $data_arr[$k_sales_no]['brand'];
                                                    echo $alocat_brand =implode('<br>',explode(",",chop($brand ,",")));
                                                    ?>
                                                </td>

                                                <td width="180" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
                                                    <?
                                                    $supplier_id = $data_arr[$k_sales_no]['supplier_id'];
                                                    echo $alocat_supplier =implode('<br>',explode(",",chop($supplier_id ,",")));

                                                    ?>
                                                </td>

												<td width="100" rowspan="<? echo $r_span; ?>" align="right" valign="middle" style="word-wrap: break-word;word-break: break-all;"><? $grey_qnty = $s_dtls_info_arr[$val['SALES_ID']]['GREY_QTY']; echo number_format($grey_qnty,2); ?></td>
												<td width="100" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>" align="right" >
												<?
													$alocal_qty = $data_arr[$k_sales_no]['qty'];
													$alocal_qty =explode(",",chop($alocal_qty ,","));
													$tot_alocat = 0;
													foreach ($alocal_qty as $value )
													{
														echo number_format($value,2)."<br>";
														$tot_alocat +=$value;

													}
													?>
												</td>
												<td width="80" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>" align="right" valign="middle"><?
												$allocat_balance =($grey_qnty-$tot_alocat);
												echo number_format($allocat_balance,2)?></td>

												<?
												$g_tot_sales_qty += $grey_qnty;
												$g_tot_alocat += $tot_alocat;
												$g_allocat_balance += $allocat_balance;

												}?>

												<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"></td>

												<?
												if(!in_array($k_sales_no,$sale_chks))
												{
												$sale_chks[]=$k_sales_no;
												?>
												<td width="100" align="right" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>" >
												<?
													$prog_no = $prog_data_arr[$val['SALES_ID']]['ID'];
													$prog_nos =explode(",",chop($prog_no ,","));
													$tot_qnty = 0;
													foreach ($prog_nos as $prog_no )
													{
														$qnty = $knit_issue_arr[$prog_no]['qnty'];
														echo number_format($qnty,2).'<br>';
														$tot_qnty +=$qnty;


													}
													//echo number_format($tot_qnty,2);

												?>
												</td>
												<td width="120" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>"   align="right">
												<?
													$product_lot = $data_arr[$k_sales_no]['lot'];
													$alocat_lot =explode(",",chop($product_lot ,","));
													$tot_aqof = 0;
													foreach ($alocat_lot as $lot_no )
													{
														$salesNo = $fso_data_arr[ $lot_no]['sales_order_no'];
														$salesNo =explode(",",chop($salesNo ,","));
														//echo $salesNo;
														$totLotWiseaqof = 0;
														foreach ($salesNo as $s_data)
														{
															if($s_data !=$k_sales_no)
															{
																$totLotWiseaqof += $aqof_arr[$s_data][$lot_no]['allocate_qty'];
																$tot_aqof += $aqof_arr[$s_data][$lot_no]['allocate_qty'];

															}
														}
														echo number_format($totLotWiseaqof,2).'<br>';

													}

													?>
												</td>

												<td width="120" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>"  align="right"><?
												$product_id = $data_arr[$k_sales_no]['product_id'];
												$productids = array_unique(explode(",",chop($product_id ,",")));
												$stockInHand=0;
												foreach ($productids as $productid)
												{
													echo number_format($stock_in_hand[$productid]['cons_quantity'],2).'<br>';
													$stockInHand +=$stock_in_hand[ $productid]['cons_quantity'];
												}
												//echo number_format($stockInHand,2);
												?></td>

												<?
												$g_qnty += $tot_qnty;
												$g_tot_aqof += $tot_aqof;
												$g_tot_stockInHand += $stockInHand;

												} ?>

												<td width="120" align="right" valign="middle" style="word-wrap: break-word;word-break: break-all;"></td>


											</tr>
											<?
												$s_tot_grey_qnty += $grey_qnty;
												$s_tot_alocat += $tot_alocat;
												$s_tot_allocat_ba += $allocat_balance;
												$s_tot_qnty += $tot_qnty;
												$s_tot_aqof += $tot_aqof;
												$s_tot_stockInHand += $stockInHand;

										}

									}

								}
							}
						}
						?>
							<tr bgcolor="#CCCCCC">
								<td colspan="11" class="cls_tot">Sub Total</td>
								<td class="cls_tot"><? echo decimal_format($s_tot_grey_qnty, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_alocat, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_allocat_ba, '1', ','); ?></td>
								<td class="cls_tot"></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_qnty, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_aqof, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_stockInHand, '1', ','); ?></td>
								<td class="cls_tot"></td>
							</tr>
							<?
						$i++;
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="11" class="cls_tot">Grand Total</th>
							<th class="cls_tot"><? echo decimal_format($g_tot_sales_qty, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_tot_alocat, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_allocat_balance, '1', ','); ?></th>
							<th class="cls_tot"></th>
							<th class="cls_tot"><? echo decimal_format($g_qnty, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_tot_aqof, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_tot_stockInHand, '1', ','); ?></th>
							<th class="cls_tot"></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}


    echo "<br />Execution Time: " . (microtime(true) - $started).'S';
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

if ($action == "report_generate_old")
{
    $started = microtime(true);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_machine_no = str_replace("'", "", $txt_machine_no);


	$cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
	$based_on = str_replace("'", "", $cbo_based_on);
	$presentationType = str_replace("'", "", $presentationType);
	$type = str_replace("'", "", $cbo_type);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$company_name = $cbo_company_name;
	$companyId = str_replace("'", "", $cbo_company_name);

	//var_dump($based_on);

	$buyer_dtls = return_library_array("select buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.short_name", 'id', 'short_name');
    $supplier_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
    $brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$yrn_count_dtls = get_yarn_count_array();
	$yrn_color_dtls = get_color_array();


	if ($cbo_buyer_id != 0)
	{
		$buyer_cond = " AND A.CUSTOMER_BUYER = ".$cbo_buyer_id;
	}
	else
	{
		$buyer_cond = "";
	}

	$sales_no_cond = "";
	if (str_replace("'", "", $txt_sales_no) != "")
	{
		$chk_prefix_sales_no=explode("-",str_replace("'", "", $txt_sales_no));
		if($chk_prefix_sales_no[3]!="")
		{
			$sales_number = "%" . trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND A.JOB_NO LIKE '".$sales_number."'";
		}
		else
		{
			$sales_number = trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND A.JOB_NO_PREFIX_NUM = '".$sales_number."'";
		}
	}

	$booking_search_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
		$booking_search_cond = "  AND A.SALES_BOOKING_NO LIKE '$booking_number'";
	}

	$year_field = "TO_CHAR(E.INSERT_DATE,'YYYY') AS YEAR";

	//for date
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
	{
		if ($based_on == 2)
		{
			$date_cond = " AND A.BOOKING_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		}
		else
		{
			$date_cond2 = " AND A.BOOKING_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		}
	}
	else
	{
		$date_cond = "";
		$date_cond2 = "";
	}

	if ($based_on == 1)
	{
		$booking_sql = "SELECT A.ID,A.BOOKING_NO
		FROM WO_BOOKING_MST A
		WHERE A.COMPANY_ID=$companyId  ".$date_cond2."  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
		ORDER BY A.ID ";
		//echo $booking_sql;die;
		$bookArray = sql_select($booking_sql);
		$bookDateArr = array();
		foreach ($bookArray as $row)
		{
			array_push($bookDateArr,$row['BOOKING_NO']);
		}

		if($bookDateArr)
		{
			$bookDateCond =" ".where_con_using_array($bookDateArr,1,'A.SALES_BOOKING_NO')." ";
		}
	}


	$sql = "SELECT A.ID AS SALES_ID, B.YARN_COUNT_ID, B.COMPOSITION_ID, B.COMPOSITION_PERC, B.COLOR_ID, B.YARN_TYPE, A.CUSTOMER_BUYER, A.WITHIN_GROUP, A.STYLE_REF_NO, A.JOB_NO, A.SALES_BOOKING_NO, SUM(B.CONS_QTY) as CONS_QTY
	FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_YARN_DTLS B
	WHERE  A.ID = B.MST_ID AND A.COMPANY_ID=$companyId  ".$buyer_cond.$sales_no_cond.$date_cond.$booking_search_cond.$bookDateCond."  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
	GROUP BY  A.ID, B.YARN_COUNT_ID, B.COMPOSITION_ID, B.COMPOSITION_PERC, B.COLOR_ID, B.YARN_TYPE, A.CUSTOMER_BUYER, A.WITHIN_GROUP, A.STYLE_REF_NO, A.JOB_NO, A.SALES_BOOKING_NO
	ORDER BY A.ID ";


	//echo $sql;
	//die;
	$nameArray = sql_select($sql);
	if(empty($nameArray))
	{
		echo get_empty_data_msg();
		die;
	}


	$print_data = array();
	$sales_ids_arr = array();
	$sales_prog_qty = array();
	$duplicate_check = array();
	$jo_no_chk = array();
	$job_no_arr = array();
	foreach ($nameArray as $row)
	{

		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['SALES_ID'] = $row['SALES_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['JOB_NO'] = $row['JOB_NO'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['CUSTOMER_BUYER'] = $row['CUSTOMER_BUYER'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['COMPOSITION_ID'] = $row['COMPOSITION_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['COMPOSITION_PERC'] = $row['COMPOSITION_PERC'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['COLOR_ID'] = $row['COLOR_ID'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$print_data[$row['JOB_NO']][$row['YARN_COUNT_ID']][$row['COMPOSITION_ID']][$row['COMPOSITION_PERC']][$row['COLOR_ID']][$row['YARN_TYPE']]['CONS_QTY'] = $row['CONS_QTY'];


		//for sales booking qty

		if($duplicate_check[$row['SALES_ID']] == '')
		{
			$duplicate_check[$row['SALES_ID']] = $row['SALES_ID'];
			array_push($sales_ids_arr, $row['SALES_ID']);
		}

		if($jo_no_chk[$row['JOB_NO']] == "")
		{
			$jo_no_chk[$row['JOB_NO']] = $row['JOB_NO'];
			array_push($job_no_arr,$row[csf('JOB_NO')]);
		}
	}
	unset($nameArray);
	// echo "<pre>";
	// print_r($print_data);
	// echo "</pre>";

	$s_dtls_sql = "SELECT A.MST_ID, SUM(A.GREY_QTY) as GREY_QTY
	FROM FABRIC_SALES_ORDER_dtls A
	WHERE  A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 ".where_con_using_array($sales_ids_arr,0,'A.MST_ID')."
	GROUP BY  A.MST_ID
	ORDER BY A.MST_ID ";
	$s_dtls_result = sql_select($s_dtls_sql);
	$s_dtls_info_arr = array();
	foreach ($s_dtls_result as $row)
	{
		$s_dtls_info_arr[$row['MST_ID']]['GREY_QTY'] += $row['GREY_QTY'];
	}
	unset($s_dtls_result);

	$sql_product = "SELECT ID, LOT, SUPPLIER_ID, BRAND, PRODUCT_NAME_DETAILS FROM PRODUCT_DETAILS_MASTER WHERE COMPANY_ID=$companyId AND ID IN(SELECT ITEM_ID FROM INV_MATERIAL_ALLOCATION_MST WHERE ITEM_CATEGORY=1 AND STATUS_ACTIVE=1 AND IS_DELETED=0 )";
	//".where_con_using_array($job_no_arr,1,'JOB_NO')."
	//echo $sql_product;die;
	$sql_product_rslt = sql_select($sql_product);
	$product_data_arr = array();
	$prod_ids_arr = array();
	foreach($sql_product_rslt as $row)
	{
        $product_data_arr[$row['ID']]['lot'] = $row['LOT'];
        $product_data_arr[$row['ID']]['supplier_id'] = $supplier_arr[$row['SUPPLIER_ID']];
        $product_data_arr[$row['ID']]['brand'] = $brand_arr[$row['BRAND']];
		$product_data_arr[$row['ID']]['product_name'] = $row['PRODUCT_NAME_DETAILS'];

		if($prod_id_chk[$row['ID']] == "")
		{
			$prod_id_chk[$row['ID']] = $row['ID'];
			array_push($prod_ids_arr,$row[csf('ID')]);
		}
	}


	$aloca_sql = "SELECT A.ID, A.JOB_NO AS SALES_ORDER_NO,  A.ITEM_ID, A.QNTY AS QTY FROM INV_MATERIAL_ALLOCATION_MST A WHERE  A.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND IS_SALES=1 ".where_con_using_array($prod_ids_arr,1,'A.ITEM_ID')."";
	//".where_con_using_array($job_no_arr,1,'A.JOB_NO')."
	//echo $aloca_sql;
	$aloca_rslt = sql_select($aloca_sql);

	foreach($aloca_rslt as $row)
	{
        $data_arr[$row['SALES_ORDER_NO']]['lot'] .= $product_data_arr[$row['ITEM_ID']]['lot'].',';
        $data_arr[$row['SALES_ORDER_NO']]['supplier_id'] .= $product_data_arr[$row['ITEM_ID']]['supplier_id'].',';
        $data_arr[$row['SALES_ORDER_NO']]['brand'] .= $product_data_arr[$row['ITEM_ID']]['brand'].',';
		$data_arr[$row['SALES_ORDER_NO']]['product_id'] .= $row['ITEM_ID'].',';
		$data_arr[$row['SALES_ORDER_NO']]['product_name'] .= $product_data_arr[$row['ITEM_ID']]['product_name'].',';
		$data_arr[$row['SALES_ORDER_NO']]['qty'] .= $row['QTY'].',';
		$sales_arr[$row['ITEM_ID']]['SALES_ORDER_NO'] .= $row['SALES_ORDER_NO'].',';
	}
	unset($aloca_rslt);
	//var_dump($data_arr);
	$fso_data_arr = array();
	foreach($sql_product_rslt as $row)
	{
		$fso_data_arr[$row['LOT']]['sales_order_no'] =$sales_arr[$row['ID']]['SALES_ORDER_NO'];
	}
	unset($sql_product_rslt);

	$prog_sql = "SELECT A.ID, B.PO_ID
	FROM PPL_PLANNING_INFO_ENTRY_DTLS A, PPL_PLANNING_ENTRY_PLAN_DTLS B
	WHERE A.ID = B.DTLS_ID AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1
	".where_con_using_array($sales_ids_arr,0,'B.PO_ID')."
	GROUP BY A.ID, B.PO_ID
	ORDER BY A.ID";
	//echo $sql;

	$prog_rslt = sql_select($prog_sql);
	$prog_data_arr = array();
	$prog_duplicate_chk = array();
	$prog_no_arr = array();
	foreach($prog_rslt as $row)
	{
		$prog_data_arr[$row['PO_ID']]['ID'] .= $row['ID'].',';
		if($prog_duplicate_chk[$row['ID']] == "")
		{
			$prog_duplicate_chk[$row['ID']] = $row['ID'];
			array_push($prog_no_arr,$row[csf('ID')]);
		}
	}
	unset($prog_rslt);

	$sql_data = sql_select("SELECT A.ISSUE_NUMBER, B.ID AS TRANS_ID, C.KNIT_ID AS PROGRAM_NO, B.CONS_QUANTITY, A.ID AS ISSUE_ID, D.LOT, D.YARN_COUNT_ID FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, PPL_YARN_REQUISITION_ENTRY C, PRODUCT_DETAILS_MASTER D WHERE A.ID = B.MST_ID AND B.REQUISITION_NO = C.REQUISITION_NO AND B.PROD_ID = D.ID AND C.PROD_ID = D.ID AND B.RECEIVE_BASIS in(3,8) AND A.ISSUE_BASIS in(3,8) AND A.ENTRY_FORM = 3 AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 ".where_con_using_array($prog_no_arr,0,'C.KNIT_ID')." ");
	$transId_chk = array();
	foreach($sql_data as $row)
	{
		if($transId_chk[$row['TRANS_ID']] == "")
		{
			$transId_chk[$row['TRANS_ID']] = $row['TRANS_ID'];
			$knit_issue_arr[$row['PROGRAM_NO']]['qnty'] += $row['CONS_QUANTITY'];
			$knit_issue_arr[$row['PROGRAM_NO']]['lot'][$row['LOT']] = $row['LOT'];
			$knit_issue_arr[$row['PROGRAM_NO']]['issue_id'] .= $row['ISSUE_ID'].",";
			$knit_issue_arr[$row['PROGRAM_NO']]['issue_number'][$row['ISSUE_NUMBER']] = $row['ISSUE_NUMBER'];
			$knit_issue_arr[$row['PROGRAM_NO']]['trans_id'] .= $row['TRANS_ID'].",";
			$knit_issue_arr[$row['PROGRAM_NO']]['yarn_count'][$row['YARN_COUNT_ID']] .= $yarn_count_dtls[$row['YARN_COUNT_ID']].",";
		}
	}
	unset( $sql_data);

	$sql_allocation = "SELECT A.JOB_NO, SUM(A.QNTY) AS ALLOCATE_QTY, B.LOT FROM INV_MATERIAL_ALLOCATION_DTLS A,PRODUCT_DETAILS_MASTER B WHERE A.ITEM_ID=B.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.QNTY>0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND IS_SALES=1 GROUP BY A.JOB_NO, B.LOT";

	$allocation_rslt = sql_select($sql_allocation);

	$aqof_arr = array();
	foreach($allocation_rslt as $row)
	{
		$aqof_arr[$row['JOB_NO']][$row['LOT']]['allocate_qty'] = $row['ALLOCATE_QTY'];
	}
	unset($allocation_rslt);

	$sih_sql="SELECT  A.PROD_ID, A.TRANSACTION_TYPE, A.CONS_QUANTITY  FROM INV_TRANSACTION A, PRODUCT_DETAILS_MASTER B WHERE A.PROD_ID = B.ID AND A.STATUS_ACTIVE = 1 AND B.STATUS_ACTIVE = 1 AND A.COMPANY_ID='$companyId'";
	//echo $sih_sql;

	$sih_rslt = sql_select($sih_sql);

	$stock_in_hand = array();
	foreach($sih_rslt as $row)
	{
		if($row["TRANSACTION_TYPE"] == 1 || $row["TRANSACTION_TYPE"] == 4 || $row["TRANSACTION_TYPE"] == 5)
		{
			$stock_in_hand[$row['PROD_ID']]['cons_quantity'] += $row['CONS_QUANTITY'];
		}
		else
		{
			$stock_in_hand[$row['PROD_ID']]['cons_quantity'] -= $row['CONS_QUANTITY'];
		}

	}
	unset($allocation_rslt);


	$sales_no_count=array();
	foreach($print_data as $k_sales_no=>$v_sales_no)
	{

		foreach($v_sales_no as $k_yarn_count_id=>$v_yarn_count_id)
		{

			foreach ($v_yarn_count_id as $k_composition_id => $v_composition_id)
			{

				foreach ($v_composition_id as $k_composition_perc => $v_composition_perc)
				{

					foreach ($v_composition_perc as $k_color_id => $v_color_id)
					{
						foreach ($v_color_id as $key=>$val)
						{
							$sales_no_count[$k_sales_no]++;
						}
					}
				}
			}
		}
	}


	$col = 17;
	$colspan = 23;
	$tbl_width = 2230;

	if ($presentationType == 1)
	{
		ob_start();
		?>
		<style>
			.cls_break td{
				word-break:break-all;
			}

			.cls_tot{
				text-align:right;
				font-weight:bold;
			}
		</style>
		<fieldset style="width:<? echo $tbl_width; ?>px;">
			<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width - 40; ?>">
				<tr>
					<td align="center" width="100%" colspan="<? echo $col; ?>" style="font-size:16px">
					<strong>Buyer Wise Daily Yarn Stock Report[Sales]</strong></td>
					<td></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table">
				<thead>
				<th width="40">SL</th>
				<th width='120'>Booking No</th>
				<th width="120">Tex. Reff</th>
				<th width="100">Buyer</th>
				<th width="130">Style No</th>
				<th width="200">Yarn Description (FSO)</th>
				<th width="100">Qty</th>
                <th width="200">Yarn Description <br>(Allocation)</th>
                <th width="100">Lot</th>
                <th width="100">Brand</th>
				<th width="100">Supplier</th>
				<th width="80">Qty</th>
				<th width="100">Grey Qty(FSO)</th>
				<th width="100">Allocation Qty </th>
				<th width="80">Allocation Balance</th>
				<th width="100">Sample Yarn Issue Qty</th>
				<th width="100">Allocated Yarn Issue Qty</th>
				<th width="120">Allocated Qty(Other FSO)</th>
				<th width="120">Stock In Hand</th>
				<th width="120">Remarks</th>
			</thead>
			</table>
			<div style="width:<? echo $tbl_width - 20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width - 40; ?>" class="rpt_table" id="tbl_list_search">
					<tbody>
					<?

					$i = 1;
					$g_tot_sales_qty = 0;
					$g_tot_alocat = 0;
					$g_allocat_balance = 0;
					$g_qnty = $g_tot_aqof = 0;
					$g_tot_stockInHand = 0;
					foreach($print_data as $k_sales_no=>$v_sales_no)
					{
						foreach($v_sales_no as $k_yarn_count_id=>$v_yarn_count_id)
						{
							foreach ($v_yarn_count_id as $k_composition_id => $v_composition_id)
							{
								foreach ($v_composition_id as $k_composition_perc => $v_composition_perc)
								{
									foreach ($v_composition_perc as $k_color_id => $v_color_id)
									{
										$s_tot_grey_qnty = 0;
										$s_tot_alocat = 0;
										$s_tot_allocat_ba = 0;
										$s_tot_qnty = 0;
										$s_tot_aqof = 0;
										$s_tot_stockInHand = 0;

										foreach ($v_color_id as $key=>$val)
										{
											$yrn_desc = $yrn_count_dtls[$k_yarn_count_id].' '.$composition[$k_composition_id].' '.$k_composition_perc.' '.$yarn_type[$key].' '.$yrn_color_dtls[$k_color_id];

											$r_span = $sales_no_count[$k_sales_no];
											?>
											<tr bgcolor="#FFFFFF" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" class="cls_break">

											<?
											if(!in_array($k_sales_no,$sales_chk))
											{
												$sales_chk[]=$k_sales_no;
												?>

												<td width="40" rowspan="<? echo $r_span; ?>" valign="middle" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>
												<td width="120" rowspan="<? echo $r_span; ?>" valign="middle" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $val['SALES_BOOKING_NO']; ?></td>
												<td width="120" rowspan="<? echo $r_span; ?>" valign="middle" title="<? echo 'Sales Id : '.$val['SALES_ID']?>" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $k_sales_no; ?></td>
												<td width="100" rowspan="<? echo $r_span; ?>" valign="middle" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $buyer_dtls[$val['CUSTOMER_BUYER']]; ?></td>
												<td width="130" rowspan="<? echo $r_span; ?>" valign="middle" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $val['STYLE_REF_NO']; ?></td>

											<? }?>
												<td width="200" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $yrn_desc; ?></td>
												<td width="100" align="right" style="word-wrap: break-word;word-break: break-all;" ><? $cons_qty = $val['CONS_QTY']; echo number_format($cons_qty,2); $tot_cons_qty +=$cons_qty; ?></td>

												<?	if(!in_array($k_sales_no,$sale_chk))
												{
												$sale_chk[]=$k_sales_no;
												?>

												<td width="200" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
												    <?
													$product_name = $data_arr[$k_sales_no]['product_name'];
													echo $alocat_product_name =implode('<br>',explode(",",chop($product_name ,",")));
												    ?>
												</td>

                                                <td width="100" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
                                                    <?
                                                    $lot = $data_arr[$k_sales_no]['lot'];
                                                    echo $alocat_lot =implode('<br>',explode(",",chop($lot ,",")));
                                                    ?>
                                                </td>

                                                <td width="100" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
                                                    <?
                                                    $brand = $data_arr[$k_sales_no]['brand'];
                                                    echo $alocat_brand =implode('<br>',explode(",",chop($brand ,",")));
                                                    ?>
                                                </td>

                                                <td width="100" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="center">
                                                    <?
                                                    $supplier_id = $data_arr[$k_sales_no]['supplier_id'];
                                                    echo $alocat_supplier =implode('<br>',explode(",",chop($supplier_id ,",")));
                                                    ?>
                                                </td>

												<td width="80" rowspan="<? echo $r_span; ?>" style="word-wrap: break-word;word-break: break-all;" align="right">
												<?
												$product_qty = $data_arr[$k_sales_no]['qty'];
												 $alocat_product_qty =implode('<br>',explode(",",chop($product_qty ,",")));
												 echo number_format($alocat_product_qty,2);
												?></td>

												<td width="100" rowspan="<? echo $r_span; ?>" align="right" valign="middle" style="word-wrap: break-word;word-break: break-all;"><? $grey_qnty = $s_dtls_info_arr[$val['SALES_ID']]['GREY_QTY']; echo number_format($grey_qnty,2); ?></td>
												<td width="100" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>" align="right" >
												<?
													$alocal_qty = $data_arr[$k_sales_no]['qty'];
													$alocal_qty =explode(",",chop($alocal_qty ,","));
													$tot_alocat = 0;
													foreach ($alocal_qty as $value )
													{
														echo number_format($value,2)."<br>";
														$tot_alocat +=$value;

													}
													?>
												</td>
												<td width="80" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>" align="right" valign="middle"><?
												$allocat_balance =($grey_qnty-$tot_alocat);
												echo number_format($allocat_balance,2)?></td>

												<?
												$g_tot_sales_qty += $grey_qnty;
												$g_tot_alocat += $tot_alocat;
												$g_allocat_balance += $allocat_balance;

												}?>

												<td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"></td>

												<?
												if(!in_array($k_sales_no,$sale_chks))
												{
												$sale_chks[]=$k_sales_no;
												?>
												<td width="100" align="right" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>" >
												<?
													$prog_no = $prog_data_arr[$val['SALES_ID']]['ID'];
													$prog_nos =explode(",",chop($prog_no ,","));
													$tot_qnty = 0;
													foreach ($prog_nos as $prog_no )
													{
														$qnty = $knit_issue_arr[$prog_no]['qnty'];
														echo number_format($qnty,2).'<br>';
														$tot_qnty +=$qnty;


													}
													//echo number_format($tot_qnty,2);

												?>
												</td>
												<td width="120" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>"   align="right">
												<?
													$product_lot = $data_arr[$k_sales_no]['lot'];
													$alocat_lot =explode(",",chop($product_lot ,","));
													$tot_aqof = 0;
													foreach ($alocat_lot as $lot_no )
													{
														$salesNo = $fso_data_arr[ $lot_no]['sales_order_no'];
														$salesNo =explode(",",chop($salesNo ,","));
														//echo $salesNo;
														$totLotWiseaqof = 0;
														foreach ($salesNo as $s_data)
														{
															if($s_data !=$k_sales_no)
															{
																$totLotWiseaqof += $aqof_arr[$s_data][$lot_no]['allocate_qty'];
																$tot_aqof += $aqof_arr[$s_data][$lot_no]['allocate_qty'];
															}
														}
														echo number_format($totLotWiseaqof,2).'<br>';


													}

													?>
												</td>

												<td width="120" style="word-wrap: break-word;word-break: break-all;" rowspan="<? echo $r_span; ?>"  align="right"><?
												$product_id = $data_arr[$k_sales_no]['product_id'];
												$productids = array_unique(explode(",",chop($product_id ,",")));
												$stockInHand=0;
												foreach ($productids as $productid)
												{
													echo number_format($stock_in_hand[$productid]['cons_quantity'],2).'<br>';
													$stockInHand +=$stock_in_hand[ $productid]['cons_quantity'];
												}
												//echo number_format($stockInHand,2);
												?></td>

												<?
												$g_qnty += $tot_qnty;
												$g_tot_aqof += $tot_aqof;
												$g_tot_stockInHand += $stockInHand;

												} ?>

												<td width="120" align="right" valign="middle" style="word-wrap: break-word;word-break: break-all;"></td>


											</tr>
											<?
												$s_tot_grey_qnty += $grey_qnty;
												$s_tot_alocat += $tot_alocat;
												$s_tot_allocat_ba += $allocat_balance;
												$s_tot_qnty += $tot_qnty;
												$s_tot_aqof += $tot_aqof;
												$s_tot_stockInHand += $stockInHand;

										}

									}

								}
							}
						}
						?>
							<tr bgcolor="#CCCCCC">
								<td colspan="12" class="cls_tot">Sub Total</td>
								<td class="cls_tot"><? echo decimal_format($s_tot_grey_qnty, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_alocat, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_allocat_ba, '1', ','); ?></td>
								<td class="cls_tot"></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_qnty, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_aqof, '1', ','); ?></td>
								<td class="cls_tot"><? echo decimal_format($s_tot_stockInHand, '1', ','); ?></td>
								<td class="cls_tot"></td>
							</tr>
							<?
						$i++;
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="12" class="cls_tot">Grand Total</th>
							<th class="cls_tot"><? echo decimal_format($g_tot_sales_qty, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_tot_alocat, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_allocat_balance, '1', ','); ?></th>
							<th class="cls_tot"></th>
							<th class="cls_tot"><? echo decimal_format($g_qnty, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_tot_aqof, '1', ','); ?></th>
							<th class="cls_tot"><? echo decimal_format($g_tot_stockInHand, '1', ','); ?></th>
							<th class="cls_tot"></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}


    echo "<br />Execution Time: " . (microtime(true) - $started).'S';
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

?>