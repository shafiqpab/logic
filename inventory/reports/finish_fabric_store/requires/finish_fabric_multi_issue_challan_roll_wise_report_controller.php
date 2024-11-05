<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,80,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer--", $selected, "" );
	exit();
}

if ($action == "load_drop_down_cust_buyer")
{
	echo create_drop_down("cbo_cust_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", $selected, "");
	exit();
}

$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

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
                           $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
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
                        onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'finish_fabric_multi_issue_challan_roll_wise_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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

if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(str) {

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
            show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' + document.getElementById('cbo_year_selection').value + '**' + document.getElementById('cbo_within_group').value, 'create_order_no_search_list_view', 'search_div', 'finish_fabric_multi_issue_challan_roll_wise_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
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



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id = str_replace("'", "", $cbo_company_name);
	$cbo_within_group = str_replace("'", "", $cbo_within_group);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$cbo_cust_buyer_name = str_replace("'", "", $cbo_cust_buyer_name);
	$txt_fso_no = str_replace("'", "", $txt_fso_no);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$txt_batch_no = str_replace("'", "", $txt_batch_no);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);

    //var_dump($cbo_within_group);

	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$serving_sup_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$serving_com_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');

    //for cbo_buyer_name(customer)
    if (str_replace("'", "", $cbo_buyer_name) == 0)
    {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1)
        {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id_cond = " and d.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id_cond = "";
        }
        else
        {
            $buyer_id_cond = "";
        }
    }
    else
    {
        if($cbo_within_group == 2)
        {
            $buyer_id_cond = " and d.buyer_id = ".$cbo_buyer_name;
        }
        else
        {
            $buyer_id_cond = " and d.po_buyer = ".$cbo_buyer_name;
        }

    }

    //for cbo_buyer_id(customer buyer)
    if ($cbo_cust_buyer_name != 0)
    {
        $customer_buyer_cond = " and d.customer_buyer=".$cbo_cust_buyer_name;
    }
    else
    {
        $customer_buyer_cond = "";
    }

    if ($cbo_within_group != 0)
    {
        $within_group_cond = " and d.within_group=".$cbo_within_group;
    }
    else
    {
        $within_group_cond = "";
    }

    //for txt_sales_no
    $sales_no_cond = "";
    if (str_replace("'", "", $txt_fso_no) != "")
    {
        $chk_prefix_sales_no=explode("-",str_replace("'", "", $txt_fso_no));
        if($chk_prefix_sales_no[3]!="")
        {
            $sales_number = "%" . trim(str_replace("'", "", $txt_fso_no));
            $sales_no_cond = "and d.job_no like '$sales_number'";
        }
        else
        {
            $sales_number = trim(str_replace("'", "", $txt_fso_no));
            $sales_no_cond = "and d.job_no_prefix_num = '$sales_number'";
        }
    }

    //for txt_booking_no
    $booking_search_cond = "";
    if (str_replace("'", "", trim($txt_booking_no)) != "")
    {
        $booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
        $booking_search_cond = "and d.sales_booking_no like '$booking_number'";
    }

    //for txt_batch_no
    $batch_search_cond = "";
    if (str_replace("'", "", trim($txt_batch_no)) != "")
    {
        $batch_number = "%" . trim(str_replace("'", "", $txt_batch_no)) . "%";
        $batch_search_cond = "and c.batch_no like '$batch_number'";
    }

    if($db_type==0)
	{
		$date_cond=" and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' ";
	}
	else
	{
		$date_cond=" and a.issue_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' ";
	}

    $con = connect();
    $r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
    if($r_id1)
    {
        oci_commit($con);
    }

	$dataArray=sql_select("SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.challan_no, a.company_id, a.supplier_id as party_name, a.buyer_id, sum(b.issue_qnty) as issue_qnty, listagg(cast(c.batch_no as varchar2(4000)), ',') within group (order by c.id) as batch_no, d.sales_booking_no, d.booking_id, d.buyer_id,d.within_group,d.po_buyer, count(e.id) as roll_no, d.customer_buyer, d.job_no, d.style_ref_no,f.unit_of_measure as uom, sum(e.qnty) as fin_qnty, listagg(cast(e.barcode_no as varchar2(4000)), ',') within group (order by e.id) as barcode_no
    from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,fabric_sales_order_mst d, pro_roll_details e,product_details_master f
    where a.entry_form=318 and a.id=b.mst_id and b.batch_id=c.id and b.id=e.dtls_id and d.id=e.po_breakdown_id and b.prod_id=f.id and b.order_id=to_char(d.id) and a.item_category=2 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id $sales_no_cond $booking_search_cond $date_cond $buyer_id_cond $customer_buyer_cond $batch_search_cond $within_group_cond
    group by a.id, a.issue_number_prefix_num, a.issue_number, a.challan_no, a.company_id, a.supplier_id, a.buyer_id, a.insert_date,d.job_no,d.sales_booking_no,d.booking_id, d.buyer_id,d.within_group,d.po_buyer, d.customer_buyer, d.job_no, d.style_ref_no,f.unit_of_measure
    order by a.id");

    $all_barcode_no_arr = array();
    foreach ($dataArray as $rows)
    {
        $all_barcode_arr = array_unique(explode(",", $rows[csf('barcode_no')]));

        foreach ($all_barcode_arr as $val)
        {
            $all_barcode_no_arr[$val] = $val;
        }
    }

    $all_barcode_no_arr = array_filter($all_barcode_no_arr);
    //var_dump($all_barcode_no_arr);die;
    if(!empty($all_barcode_no_arr))
    {
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1990, 1,$all_barcode_no_arr, $empty_arr);
        //die;
        $production_sql_data=sql_select("SELECT a.barcode_no, a.qnty as prod_qty
        FROM pro_roll_details a, GBL_TEMP_ENGINE b
        WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 and a.barcode_no=b.ref_val and b.user_id=$user_id and b.entry_form=1990");

        $production_data_arr=array();
        foreach($production_sql_data as $value)
        {
            $production_data_arr[$value[csf("barcode_no")]]['prod_qty'] +=$value[csf("prod_qty")];
        }
        //echo "<pre>";print_r($production_data_arr);die;
    }
    //var_dump($all_roll_id_arr);die;
    $con = connect();
    $r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1990)");
    if($r_id111)
    {
        oci_commit($con);
    }

    ?>
 	<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
 			<script type="text/javascript">
				setFilterGrid('tbl_list_search',-1);
			</script>
        <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <thead>
               <tr>
	                <th width="30"></th>
	                <th width="50">SL</th>
	                <th width="120">System ID</th>
	                <th width="100">Challan</th>
	             	<th width="100">Buyer</th>
	                <th width="100">Cust. Buyer</th>
	                <th width="120">FSO</th>
	                <th width="100">F. Booking</th>
	                <th width="100">Styel Reff</th>
	                <th width="80">UOM</th>
	                <th width="100">Batch</th>
	                <th width="100">Delivery Grey Qty</th>
	                <th width="100">Delivery Finish Qty</th>
					<th width="100" align="right">Delivery Roll Qty</th>
				</tr>
            </thead>

    		<tbody id="tbl_list_search" align="center">
        	<?
        	$j=1;
        	foreach ($dataArray as $row)
        	{
       			if ($j%2==0)
                $bgcolor="#E9F3FF";
            	else
                $bgcolor="#FFFFFF";

                $buyer_id = ($row[csf('within_group')]==1)?$row[csf('po_buyer')]:$row[csf('buyer_id')];
                $all_batch_no = implode(",", array_unique(explode(",", $row[csf('batch_no')])));
                $all_barcode_no = array_unique(explode(",", $row[csf('barcode_no')]));

                $grey_used = 0;
                foreach ($all_barcode_no as $bar_rows)
                {
                    $grey_used +=$production_data_arr[$bar_rows]['prod_qty'];
                }


        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
        		<td>
        			<input type="checkbox" id="tbl_<? echo $j; ?>"  onClick="fnc_checkbox_check(<? echo $j; ?>);"  />

        			<input type="hidden" id="mstidall_<? echo $j; ?>" value="<? echo $row[csf('id')]; ?>" />
        			<input type="hidden" id="cust_buyer_id_<? echo $j; ?>" value="<? echo $row[csf('customer_buyer')]; ?>" />
        		</td>
	            <td><? echo $j; ?></td>
	            <td><? echo $row[csf('issue_number')]; ?></td>
	            <td><? echo $row[csf('challan_no')]; ?></td>
	            <td><? echo $buyer_arr[$buyer_id]; ?></td>
	            <td><? echo $buyer_arr[$row[csf('customer_buyer')]]; ?></td>
	            <td><? echo $row[csf('job_no')]; ?></td>
	            <td><? echo $row[csf('sales_booking_no')]; ?></td>
	            <td><? echo $row[csf('style_ref_no')]; ?></td>
	            <td><? echo $unit_of_measurement[$row[csf('uom')]];?></td>
	            <td><? echo $all_batch_no; ?></td>
	            <td align="right"><? echo number_format($grey_used); ?></td>
	            <td align="right"><? echo $row[csf('fin_qnty')]; ?> </td>
	            <td align="right"><? echo $row[csf('roll_no')]; ?> </td>
        	</tr>
        	<?
        		$j++;
        	}
        	?>
        	</tbody>
        </table>
    </div>
    <?
	exit();
}

if($action=="delivery_challan_print")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Delivery Challan Print", "../../../", 1, 1,'','','');
	$data=explode('*',$data);
    $mst_id=implode(',',explode("_",$data[1]));

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

    $con = connect();
    $r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (19990,19999)");
    if($r_id1)
    {
        oci_commit($con);
    }

    $dataArray=sql_select("SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.challan_no, a.company_id, a.supplier_id as party_name, a.buyer_id, b.issue_qnty, b.width_type, c.batch_no, d.sales_booking_no, d.booking_id, d.buyer_id, d.within_group, d.po_buyer, d.customer_buyer, d.job_no, d.style_ref_no, f.unit_of_measure as uom, e.qnty as fin_qnty, f.gsm, f.dia_width, f.detarmination_id, f.color as color_id, d.id as fso_id, d.job_no_prefix_num,e.barcode_no,a.delivery_addr, a.remarks from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,fabric_sales_order_mst d,
    pro_roll_details e,product_details_master f where a.entry_form=318 and a.id=b.mst_id and b.batch_id=c.id and b.id=e.dtls_id and d.id=e.po_breakdown_id
    and b.prod_id=f.id and b.order_id=to_char(d.id) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
    and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id='$data[0]' and a.id in($mst_id) ");


    $issue_wise_dtls = array();
    $fsoIdChk = array();
    $challanNoChk = array();
    $deladdrChk = array();
    $remarksChk = array();
    $all_fso_id_arr = array();
    $all_barcode_no_arr = array();
    $challan_nos = '';
    $deladdrs = '';
    $allremarks = '';
	foreach ($dataArray as $row)
	{
        if($row[csf('within_group')]==1)
        {
            $issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['buyer_id']=$row[csf('po_buyer')];
        }
        else
        {
            $issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['buyer_id']=$row[csf('buyer_id')];
        }

		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['issue_number']=$row[csf('issue_number')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['customer_buyer']=$row[csf('customer_buyer')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['job_no']=$row[csf('job_no')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['uom']=$row[csf('uom')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['within_group']=$row[csf('within_group')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['batch_no']=$row[csf('batch_no')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['width_type']=$row[csf('width_type')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['fin_qnty'] +=$row[csf('fin_qnty')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['fso_id'] =$row[csf('fso_id')];
		$issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['barcode_no'] .=$row[csf('barcode_no')].",";
        $issue_wise_dtls[$row[csf('id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['roll_qnty']++;

        if($challanNoChk[$row[csf('challan_no')]] == "")
        {
            $challanNoChk[$row[csf('challan_no')]] = $row[csf('challan_no')];
            $challan_nos .=$row[csf('challan_no')].",";
        }
        if($deladdrChk[$row[csf('delivery_addr')]] == "")
        {
            $deladdrChk[$row[csf('delivery_addr')]] = $row[csf('delivery_addr')];
            $deladdrs .=$row[csf('delivery_addr')].",";
        }

        if($remarksChk[$row[csf('remarks')]] == "")
        {
            $remarksChk[$row[csf('remarks')]] = $row[csf('remarks')];
            $allremarks .=$row[csf('remarks')].",";
        }


        if($fsoIdChk[$row[csf('fso_id')]] == "")
        {
            $fsoIdChk[$row[csf('fso_id')]] = $row[csf('fso_id')];
            $all_fso_id_arr[$row[csf("fso_id")]] = $row[csf("fso_id")];
        }

        if($barcodeNoChk[$row[csf('barcode_no')]] == "")
        {
            $barcodeNoChk[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
            $all_barcode_no_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
        }
	}

    //echo "<pre>";print_r($issue_wise_dtls);die;
    $all_fso_id_arr = array_filter($all_fso_id_arr);
    //var_dump($all_fso_id_arr);die;
    if(!empty($all_fso_id_arr))
    {
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 19990, 1,$all_fso_id_arr, $empty_arr);
        //die;

        $fso_sql_data=sql_select("SELECT a.mst_id, a.color_id, a.determination_id as deter_id, a.color_range_id
        FROM fabric_sales_order_dtls a, GBL_TEMP_ENGINE b WHERE a.status_active=1 and a.is_deleted=0 and a.mst_id=b.ref_val and b.user_id=$user_id and b.entry_form=19990");
        $fso_data_arr=array();
        foreach($fso_sql_data as $val)
        {
            $fso_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]][$val[csf("deter_id")]]['shade_type'].=$val[csf("color_range_id")].',';
        }
        //echo "<pre>";print_r($fso_data_arr);

    }


    $all_barcode_no_arr = array_filter($all_barcode_no_arr);
    //var_dump($all_barcode_no_arr);die;
    if(!empty($all_barcode_no_arr))
    {
        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 19999, 1,$all_barcode_no_arr, $empty_arr);
        //die;

        $production_sql_data=sql_select("SELECT a.barcode_no, a.roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty
        FROM pro_roll_details a, GBL_TEMP_ENGINE b
        WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 and a.barcode_no=b.ref_val and b.user_id=$user_id and b.entry_form=19999");

        $production_data_arr=array();
        foreach($production_sql_data as $value)
        {
            $production_data_arr[$value[csf("barcode_no")]]['prod_qty'] +=$value[csf("prod_qty")];
        }
        //echo "<pre>";print_r($production_data_arr);
    }


    $composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

    $issue_count = array();
    foreach ($issue_wise_dtls as $k_issue=>$v_issue)
    {
        foreach ($v_issue as $k_detar=>$v_detar)
        {
            foreach ($v_detar as $k_gsm=>$v_gsm)
            {
                foreach ($v_gsm as $k_dia_width=>$v_dia_width)
                {
                    foreach ($v_dia_width as $k_color_id=>$row)
                    {
                        $issue_count[$k_issue]++;
                    }
                }
            }
        }
    }
    //var_dump($issue_count);

    $con = connect();
    $r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (19990,19999)");
    if($r_id111)
    {
        oci_commit($con);
    }


    ?>
    <div style="width:1230px;">
        <table width="950" cellspacing="0" align="center">
            <tr>
                <td colspan="4" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="4" align="center" style="font-size:14px">
                    <?
                        $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                        foreach ($nameArray as $result)
                        {
                        ?>
                            <? echo $result[csf('plot_no')]; ?> &nbsp;
                            <? echo $result[csf('level_no')]?>&nbsp;
                            <? echo $result[csf('road_no')]; ?> &nbsp;
                            <? echo $result[csf('block_no')];?> &nbsp;
                            <? echo $result[csf('city')];?> &nbsp;
                            <? echo $result[csf('zip_code')]; ?> &nbsp;
                            <? echo $result[csf('province')];?> &nbsp;
                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                            <? echo $result[csf('email')];?> &nbsp;
                            <? echo $result[csf('website')];
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
            </tr>

            <tr>
                <table width="550" cellspacing="0" align="center">
                    <tr>
                        <td width="100">&nbsp;&nbsp;</td>
                        <td  ><strong> Delivery Date  : </strong><? echo change_date_format($data[3]); ?> </td>
                        <td></td>
                        <td ><strong>Challan : </strong><? echo implode(",",array_filter(array_unique(explode(",",chop($challan_nos,","))))); ?></td>
                    </tr>
                    <tr>
                        <td width="100">&nbsp;&nbsp;</td>
                        <td ><strong>Vehicle No : </strong><? echo implode(",",array_filter(array_unique(explode(",",chop($deladdrs,","))))); ?> </td>
                        <td></td>
                        <td > <strong>Bolt Seal Sl : </strong><? echo implode(",",array_filter(array_unique(explode(",",chop($allremarks,","))))); ?></td>

                    </tr>
                </table>
            </tr>

        </table>
            <br>

        <div style="width:100%;">
        <table align="center" cellspacing="0" cellpadding="0" border="1" rules="all" width="1550" class="rpt_table" >
                <thead  align="center">
                    <th width="30">SL</th>
                    <th width="150">System ID</th>
                    <th width="80">Buyer</th>
                    <th width="80">Cust. Buyer</th>
                    <th width="60">FSO</th>
                    <th width="100">F. Booking</th>
                    <th width="100">Styel Reff</th>
                    <th width="150">Composition</th>
                    <th width="100">Construction</th>
                    <th width="50">DIA</th>
                    <th width="50">GSM</th>
                    <th width="50">UOM</th>
                    <th width="80">Color</th>
                    <th width="100">Shade Type</th>
                    <th width="80">Batch</th>
                    <th width="100">Dia/ Width Type</th>
                    <th width="60">Delivery <br>Grey Qty</th>
                    <th width="60">Delivery Finish Qty</th>
                    <th width="50">Delivery Roll Qty</th>
                </thead>
                <tbody align="center">
                    <?
                        $i=1; $total_grey_qnty=0;$total_fin_qnty=0;$total_roll_qnty=0;

                        foreach ($issue_wise_dtls as $k_issue=>$v_issue)
                        {
                            foreach ($v_issue as $k_detar=>$v_detar)
                            {
                                foreach ($v_detar as $k_gsm=>$v_gsm)
                                {
                                    foreach ($v_gsm as $k_dia_width=>$v_dia_width)
                                    {
                                        foreach ($v_dia_width as $k_color_id=>$row)
                                        {

                                           $issue_span = $issue_count[$k_issue];
                                            //echo "<pre>";print_r($issue_span);

                                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                                            $buyer_id = ($row['within_group']==1)?$row['po_buyer']:$row['buyer_id'];

                                            $shade_type=$fso_data_arr[$row['fso_id']][$k_color_id][$k_detar]['shade_type'];
                                            $shade_type_arr=array_unique(explode(',', chop($shade_type,',')));
                                            $shade_type_data ="";
                                            foreach($shade_type_arr as $key => $shade_type_val)
                                            {
                                                if ($shade_type_data=="")
                                                {
                                                    $shade_type_data.= $color_range[$shade_type_val];
                                                }
                                                else
                                                {
                                                    $shade_type_data.= ','.$color_range[$shade_type_val];
                                                }
                                            }

                                            $all_barcode_no = array_unique(explode(",", $row['barcode_no']));

                                            $grey_used = 0;
                                            foreach ($all_barcode_no as $bar_rows)
                                            {
                                                $grey_used +=$production_data_arr[$bar_rows]['prod_qty'];
                                            }

                                            ?>
                                            <tr>
                                            <?
                                            if(!in_array($k_issue,$issue_chk))
                                            {
                                                $issue_chk[]=$k_issue;
                                                ?>

                                                <td rowspan="<? echo $issue_span ;?>" valign="middle"><? echo $i;  ?></td>

                                                <td rowspan="<? echo $issue_span ;?>" align="center" valign="middle" title="<? echo $k_issue;?>"><? echo $row["issue_number"]; ?></td>
                                                <td rowspan="<? echo $issue_span ;?>" align="center" valign="middle"><? echo $buyer_arr[$buyer_id]; ?></td>
                                                <td rowspan="<? echo $issue_span ;?>" align="center" valign="middle"><? echo $buyer_arr[$row["customer_buyer"]]; ?></td>
                                                <td rowspan="<? echo $issue_span ;?>" align="center"  title="<? echo $row["job_no"]; ?>" valign="middle"><? echo $row["job_no_prefix_num"]; ?></td>
                                                <td rowspan="<? echo $issue_span ;?>" align="center" valign="middle"><? echo $row["sales_booking_no"]; ?></td>
                                                <td rowspan="<? echo $issue_span ;?>" align="center" valign="middle"><? echo $row["style_ref_no"]; ?></td>
                                                <?
                                            }?>
                                                <td align="center"><? echo $composition_arr[$k_detar]; ?></td>
                                                <td align="center"><? echo $constructtion_arr[$k_detar]; ?></td>
                                                <td align="center"><? echo $k_dia_width; ?></td>
                                                <td align="center"><? echo $k_gsm; ?></td>
                                                <td><? echo $unit_of_measurement[$row["uom"]]; ?></td>
                                                <td><? echo $color_arr[$k_color_id]; ?></td>
                                                <td><? echo $shade_type_data;  ?></td>
                                                <td><? echo $row["batch_no"]; ?> </td>
                                                <td><? echo $fabric_typee[$row['width_type']]; ?></td>
                                                <td align="right"><? echo number_format($grey_used,2); ?> </td>
                                                <td align="right"><? echo number_format($row["fin_qnty"],2); ?></td>
                                                <td align="right"><? echo $row["roll_qnty"]; ?> </td>
                                            </tr>
                                            <?

                                            $total_grey_qnty += $grey_used;
                                            $total_fin_qnty += $row["fin_qnty"];
                                            $total_roll_qnty += $row["roll_qnty"];
                                        }
                                    }
                                }
                            }
                            $i++;
                        }
                    ?>
                </tbody>
                <tr>
                    <td colspan="16" align="right"><strong>Grand Total : &nbsp;</strong></td>
                    <td align="right"><?php echo number_format($total_grey_qnty,2); ?></td>
                    <td align="right"><?php echo number_format($total_fin_qnty,2); ?></td>
                    <td align="right"><?php echo $total_roll_qnty; ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        </div>
    <?
    exit();
}
?>