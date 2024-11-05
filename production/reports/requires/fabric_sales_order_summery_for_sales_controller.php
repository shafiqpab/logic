<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

$search_by_arr = array(1 => "Date Wise Report", 2 => "Wait For Heat Setting", 5 => "Wait For Singeing", 3 => "Wait For Dyeing", 4 => "Wait For Re-Dyeing"); //--------------------------------------------------------------------------------------------------------------------

if($action=="load_drop_down_buyer")
{
    extract($_REQUEST);

	echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$choosenCompany.") ".$buyer_cond." AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (1,3,21,90)) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", 0, "" );

	exit();
}

if($action=="load_drop_down_cust_buyer")
{
    extract($_REQUEST);

	echo create_drop_down( "cbo_cust_buyer_name", 130, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$choosenCompany.") ".$buyer_cond." AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (1,3,21,90)) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", 1, "--Select Cust Buyer--", 0, "" );
	exit();
}

if ($action == "load_drop_down_cust_buyer") {
    echo load_html_head_contents("Buyer Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $data = explode('_', $data);
    $company = $data[0];
    if ($company > 0) {
        echo create_drop_down("cbo_cust_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", 0, "");
    } else {
        echo create_drop_down("cbo_cust_buyer_name", 130, $blank_array, "", 1, "-- Select Cust Buyer --", $selected, "", 0, "", "", "", "");
    }
    exit();
}

if ($action == "booking_No_popup") {
    echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    ?>
    <script>
        var selected_id = new Array;
		var selected_name = new Array;
        var selected_booking_no = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;
            // alert(tbl_row_count);
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());
                selected_booking_no.push($('#txt_booking_no' + str).val());

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
                selected_booking_no.splice(i, 1);
			}
			var id = '';
			var name = '';
            var booking_no = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
                booking_no += selected_booking_no[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
            booking_no = booking_no.substr(0, booking_no.length - 1);

			$('#hidden_job_id').val(id);
			$('#hidden_job_no').val(name);
            $('#hidden_booking_no').val(booking_no);
		}
    </script>
    </head>

    <body>
        <div align="center">
            <fieldset style="width:830px;margin-left:4px;">
                <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
                    <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                        <thead>
                            <th>Year</th>
                            <th>Within Group</th>
                            <th>Company</th>
                            <th>Search By</th>
                            <th>Search</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                <input type="hidden" name="hidden_job_id" id="hidden_job_id" value="">
                                <input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
                                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
                            </th>
                        </thead>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
                                ?>
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_within_group", 80, $yes_no, "", 0, "-- All --", $cbo_within_group, "", 1, "");
                                ?>
                            </td>
                            <td align="center">
                                <?
                                echo create_drop_down("cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_id, "", 1);
                                ?>
                            </td>
                            <td align="center">
                                <?
                                $search_by_arr = array(1 => "Sales Order No", 2 => "Sales / Booking No", 3 => "Style Ref.");
                                echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", 1, "", 0);
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'fabric_sales_order_summery_for_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                    <div id="search_div" style="margin-top:10px"></div>
                </form>
            </fieldset>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

    </html>
    <?
    exit();
}

if ($action == "create_booking_search_list_view") {
    $data = explode('_', $data);
    // echo "<pre>"; print_r($data);

    $company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    $location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

    $search_string = trim($data[0]);
    $search_by = $data[1];
    $company_id = $data[2];
    $within_group = $data[3];
    $cbo_year = $data[4];

    $year_field = "to_char(a.insert_date,'YYYY') as year";

    if ($cbo_year == 0) $year_cond = "";
    else $year_cond = "and to_char(a.insert_date,'YYYY')='$cbo_year'";

    if ($within_group == 0) $within_group_cond = "";
    else $within_group_cond = " and a.within_group=$within_group";
    if ($within_group == 1) // Yes
    {
        $company_cond = " and a.buyer_id=$company_id";
        $search_field_cond = '';
        if ($search_string != "") {
            if ($search_by == 1) {
                $search_field_cond = " and a.job_no_prefix_num='$search_string'";
            } else if ($search_by == 2) {
                $search_field_cond = " and b.booking_no_prefix_num='$search_string'";
            } else {
                $search_field_cond = " and a.style_ref_no ='$search_string'";
            }
        }
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id
        from fabric_sales_order_mst a, wo_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $company_cond $within_group_cond $search_field_cond $year_cond
        union all
        SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id
        from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $company_cond $within_group_cond $search_field_cond $year_cond order by id";
    } else // No
    {
        $search_field_cond = '';
        if ($search_string != "") {
            if ($search_by == 1) {
                $search_field_cond = " and a.job_no_prefix_num='$search_string'";
            } else if ($search_by == 2) {
                $search_field_cond = " and a.sales_booking_no='$search_string'";
            } else {
                $search_field_cond = " and a.style_ref_no ='$search_string'";
            }
        }
        $company_cond = " and a.company_id=$company_id";
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.customer_buyer, a.po_buyer, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $company_cond $within_group_cond $search_field_cond $year_cond order by a.id";
    }

    // echo $sql; die;
    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="110">Sales Order No</th>
            <th width="40">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>
            <th width="120">Sales/ Booking No</th>
            <th width="80">Booking date</th>
            <th width="110">Style Ref.</th>
            <th>Location</th>
        </thead>
    </table>
    <div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="799" class="rpt_table" id="tbl_list_search">
            <?
            $i = 1;
            foreach ($result as $row) {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                else $bgcolor = "#FFFFFF";

                if ($row['WITHIN_GROUP'] == 1)
                    $buyer = $buyer_arr[$row['PO_BUYER']];
                else
                    $buyer = $buyer_arr[$row['CUSTOMER_BUYER']];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
                    <td width="40" align="center"><? echo $i; ?>
                        <input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row['ID']; ?>"/>
                        <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row['JOB_NO_PREFIX_NUM']; ?>"/>
                        <input type="hidden" name="txt_booking_no" id="txt_booking_no<?php echo $i ?>" value="<? echo $row['SALES_BOOKING_NO']; ?>"/>
                    </td>
                    <td width="110" align="center">
                        <p>&nbsp;<? echo $row['JOB_NO']; ?></p>
                    </td>
                    <td width="40" align="center">
                        <p><? echo $row['YEAR']; ?></p>
                    </td>
                    <td width="80" align="center">
                        <p><? echo $yes_no[$row['WITHIN_GROUP']]; ?>&nbsp;</p>
                    </td>
                    <td width="70" align="center">
                        <p><? echo $buyer; ?>&nbsp;</p>
                    </td>
                    <td width="120" align="center">
                        <p><? echo $row['SALES_BOOKING_NO']; ?></p>
                    </td>
                    <td width="80" align="center"><? echo change_date_format($row['BOOKING_DATE']); ?></td>
                    <td width="110" align="center">
                        <p><? echo $row['STYLE_REF_NO']; ?></p>
                    </td>
                    <td align="center">
                        <p><? echo $location_arr[$row['LOCATION_ID']]; ?></p>
                    </td>
                </tr>
            <?
                $i++;
            }
            ?>
        </table>
    </div>
    <table width="800" cellspacing="0" cellpadding="0" style="border:none" align="left">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
    <?
    exit();
}

if ($action == "jobNo_popup") {
    echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    ?>
    <script>
        var selected_id = new Array;
		var selected_name = new Array;
        var selected_booking_no = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;
            // alert(tbl_row_count);
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());
                selected_booking_no.push($('#txt_booking_no' + str).val());

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
                selected_booking_no.splice(i, 1);
			}
			var id = '';
			var name = '';
            var booking_no = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
                booking_no += selected_booking_no[i] + ",";
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
            booking_no = booking_no.substr(0, booking_no.length - 1);

			$('#hidden_job_id').val(id);
			$('#hidden_job_no').val(name);
            $('#hidden_booking_no').val(booking_no);
		}
    </script>
    </head>

    <body>
        <div align="center">
            <fieldset style="width:830px;margin-left:4px;">
                <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
                    <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                        <thead>
                            <th>Year</th>
                            <th>Within Group</th>
                            <th>Company</th>
                            <th>Search By</th>
                            <th>Search</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                <input type="hidden" name="hidden_job_id" id="hidden_job_id" value="">
                                <input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
                                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
                            </th>
                        </thead>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
                                ?>
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_within_group", 80, $yes_no, "", 0, "-- All --", $cbo_within_group, "", 1, "");
                                ?>
                            </td>
                            <td align="center">
                                <?
                                echo create_drop_down("cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_id, "", 1);
                                ?>
                            </td>
                            <td align="center">
                                <?
                                $search_by_arr = array(1 => "Sales Order No", 2 => "Sales / Booking No", 3 => "Style Ref.");
                                echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", 2, "", 0);
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value, 'create_job_search_list_view', 'search_div', 'fabric_sales_order_summery_for_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                    <div id="search_div" style="margin-top:10px"></div>
                </form>
            </fieldset>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

    </html>
    <?
    exit();
}

if ($action == "create_job_search_list_view") {
    $data = explode('_', $data);

    $company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    $location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

    $search_string = trim($data[0]);
    $search_by = $data[1];
    $company_id = $data[2];
    $within_group = $data[3];
    $cbo_year = $data[4];

    $year_field = "to_char(a.insert_date,'YYYY') as year";

    if ($cbo_year == 0) $year_cond = "";
    else $year_cond = "and to_char(a.insert_date,'YYYY')='$cbo_year'";

    if ($within_group == 0) $within_group_cond = "";
    else $within_group_cond = " and a.within_group=$within_group";
    if ($within_group == 1) // Yes
    {
        $search_field_cond = '';
        if ($search_string != "") {
            if ($search_by == 1) {
                $search_field_cond = " and a.job_no_prefix_num='$search_string'";
            } else if ($search_by == 2) {
                $search_field_cond = " and b.booking_no_prefix_num='$search_string'";
            } else {
                $search_field_cond = " and a.style_ref_no ='$search_string'";
            }
        }
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id
        from fabric_sales_order_mst a, wo_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $year_cond
        union all
        SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id
        from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $year_cond order by id";
    } else // No
    {
        $search_field_cond = '';
        if ($search_string != "") {
            if ($search_by == 1) {
                $search_field_cond = " and a.job_no_prefix_num='$search_string'";
            } else if ($search_by == 2) {
                $search_field_cond = " and a.sales_booking_no='$search_string'";
            } else {
                $search_field_cond = " and a.style_ref_no ='$search_string'";
            }
        }
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.customer_buyer, a.po_buyer, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $year_cond order by id";
    }

    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="110">Sales Order No</th>
            <th width="40">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>
            <th width="120">Sales/ Booking No</th>
            <th width="80">Booking date</th>
            <th width="110">Style Ref.</th>
            <th>Location</th>
        </thead>
    </table>
    <div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="799" class="rpt_table" id="tbl_list_search">
            <?
            $i = 1;
            foreach ($result as $row) {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                else $bgcolor = "#FFFFFF";

                if ($row['WITHIN_GROUP'] == 1)
                    $buyer = $buyer_arr[$row['PO_BUYER']];
                else
                    $buyer = $buyer_arr[$row['CUSTOMER_BUYER']];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
                    <td width="40" align="center"><? echo $i; ?>
                        <input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row['ID']; ?>"/>
                        <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row['JOB_NO']; ?>"/>
                        <input type="hidden" name="txt_booking_no" id="txt_booking_no<?php echo $i ?>" value="<? echo $row['SALES_BOOKING_NO']; ?>"/>
                    </td>
                    <td width="110" align="center">
                        <p>&nbsp;<? echo $row['JOB_NO']; ?></p>
                    </td>
                    <td width="40" align="center">
                        <p><? echo $row['YEAR']; ?></p>
                    </td>
                    <td width="80" align="center">
                        <p><? echo $yes_no[$row['WITHIN_GROUP']]; ?>&nbsp;</p>
                    </td>
                    <td width="70" align="center">
                        <p><? echo $buyer; ?>&nbsp;</p>
                    </td>
                    <td width="120" align="center">
                        <p><? echo $row['SALES_BOOKING_NO']; ?></p>
                    </td>
                    <td width="80" align="center"><? echo change_date_format($row['BOOKING_DATE']); ?></td>
                    <td width="110" align="center">
                        <p><? echo $row['STYLE_REF_NO']; ?></p>
                    </td>
                    <td align="center">
                        <p><? echo $location_arr[$row['LOCATION_ID']]; ?></p>
                    </td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    <table width="800" cellspacing="0" cellpadding="0" style="border:none" align="left">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
    <?
    exit();
}


if ($action == "report_generate") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    $company = str_replace("'", "", $cbo_company_name);
    $buyer = str_replace("'", "", $cbo_buyer_name);
    $cust_buyer = str_replace("'", "", $cbo_cust_buyer_name);
    $within_group = str_replace("'", "", $cbo_within_group);
    $fso_no = str_replace("'", "", $txt_fso_no);
    $job_id = str_replace("'", "", $txt_job_hidden_id);
    $booking_no = str_replace("'", "", $txt_sales_booking_no);
    $marchant = str_replace("'", "", $cbo_marchant);
    $cbo_date_search_type = str_replace("'", "", $cbo_date_search_type);
    $txt_date_from = str_replace("'", "", $txt_date_from);
    $txt_date_to = str_replace("'", "", $txt_date_to);
    $cbo_year = str_replace("'", "", $cbo_year_selection);
    $cbo_order_nature = str_replace("'", "", $cbo_order_nature);

    //echo "cbo_order_nature".$cbo_order_nature;die;

    if($booking_no != ""){
        $elements = explode(',', $booking_no);
        $quotedElements = array_map(function($element) {
            return "'" . $element . "'";
        }, $elements);
        $booking_no = implode(',', $quotedElements);
    }
    // echo $marchant; die;

    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    $user_name_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
    $color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");

    if ($within_group == 0) $within_group_cond = "";
    else $within_group_cond = "  and a.within_group=$within_group";
    if ($buyer == 0) $buyer_cond = "";
    else $buyer_cond = "  and a.buyer_id in ($buyer)";
    if ($cust_buyer == 0) $cust_buyer_cond = "";
    else $cust_buyer_cond = "  and a.customer_buyer in ($cust_buyer)";

    if ($booking_no == "") $booking_no_cond = "";
    else $booking_no_cond = "  and a.sales_booking_no in ($booking_no)";
    if ($fso_no == "") $fso_no_cond = "";
    else $fso_no_cond = " and a.job_no_prefix_num in ($fso_no)";
    if ($job_id == "") $fsoid_cond = "";
    else $fsoid_cond = " and a.id in ($job_id)";
    if ($marchant == 0) $marchant_cond = "";
    else $marchant_cond = " and a.inserted_by in ($marchant)";
    if ($cbo_order_nature == 0) $order_nature_cond = "";
    else $order_nature_cond = " and a.order_nature in ($cbo_order_nature)";


    $delivery_date_cond = "";
    if ($txt_date_from && $txt_date_to) {
        $date_from = change_date_format($txt_date_from, '', '', 1);
        $date_to = change_date_format($txt_date_to, '', '', 1);
        if ($cbo_date_search_type == 1) // delivery start date
        {
            $delivery_date_cond = "and a.delivery_start_date between '$date_from' and '$date_to'";
        } else if ($cbo_date_search_type == 2) // delivery end date
        {
            $delivery_date_cond = "and a.delivery_date between '$date_from' and '$date_to'";
        }
        else if ($cbo_date_search_type == 3) // booking date
        {
            $delivery_date_cond = "and a.booking_date between '$date_from' and '$date_to'";
        }
    }
    $year_cond = "";
    if ($cbo_year != "") {
        if ($cbo_date_search_type == 1) // delivery_date
        {
            $year_cond = " and TO_CHAR(a.delivery_start_date, 'YYYY') = '$cbo_year'";
        } else if ($cbo_date_search_type == 2) // booking date
        {
            $year_cond = " and TO_CHAR(a.delivery_date, 'YYYY') = '$cbo_year'";
        }
    }

    // =================== MAIN SQL ===================
    $sql = "SELECT A.ID, A.CUSTOMER_BUYER, A.BOOKING_DATE, A.BOOKING_APPROVAL_DATE, A.DELIVERY_START_DATE, A.DELIVERY_DATE, A.JOB_NO_PREFIX_NUM, A.JOB_NO AS FSO_NO, A.SALES_BOOKING_NO, A.GARMENTS_MARCHANT, B.FABRIC_DESC, B.DETERMINATION_ID, B.COLOR_ID, B.GSM_WEIGHT, B.DIA, B.FINISH_QTY, B.PP_QNTY, B.MTL_QNTY, B.FPT_QNTY, B.GPT_QNTY, B.PROCESS_LOSS, B.ADJUST_GREY_QNTY, B.GREY_QTY,A.INSERTED_BY,A.ORDER_NATURE
    FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B
    where a.id=b.mst_id and a.company_id=$company $within_group_cond $delivery_date_cond $year_cond $fso_no_cond $fsoid_cond $booking_no_cond $buyer_cond $cust_buyer_cond $marchant_cond $order_nature_cond and a.entry_form=472 and a.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc"; // order by a.id asc crm 20833
    //echo $sql; die;

    $sql_result = sql_select($sql);
    if (empty($sql_result)) {
        echo "<b>Data Not Found</b>";
        die;
    }
    $data_arr = $job_id_arr = $del_start_date_arr = array();
    $tot_grey_qnty = $tot_fab_fin_qnty = 0;
    foreach ($sql_result as $row) {
        if ($row["WITHIN_GROUP"] == 1) {
            $buyer_id = $row["PO_BUYER"];
        } else {
            $buyer_id = $row["BUYER_ID"];
        }
        $str_ref = $row["DETERMINATION_ID"] . '*' . $row["COLOR_ID"] . '*' . $row["GSM_WEIGHT"] . '*' . $row["DIA"];
        $data_arr[$row["ID"]][$str_ref]["CUSTOMER_BUYER"] = $row["CUSTOMER_BUYER"];
        $data_arr[$row["ID"]][$str_ref]["BOOKING_DATE"] = $row["BOOKING_DATE"];
        $data_arr[$row["ID"]][$str_ref]["BOOKING_APPROVAL_DATE"] = $row["BOOKING_APPROVAL_DATE"];
        $data_arr[$row["ID"]][$str_ref]["DELIVERY_START_DATE"] = $row["DELIVERY_START_DATE"];
        $data_arr[$row["ID"]][$str_ref]["DELIVERY_DATE"] = $row["DELIVERY_DATE"];
        $data_arr[$row["ID"]][$str_ref]["JOB_NO_PREFIX_NUM"] = $row["JOB_NO_PREFIX_NUM"];
        $data_arr[$row["ID"]][$str_ref]["SALES_BOOKING_NO"] = $row["SALES_BOOKING_NO"];
        $data_arr[$row["ID"]][$str_ref]["GARMENTS_MARCHANT"] = $row["GARMENTS_MARCHANT"];
        $data_arr[$row["ID"]][$str_ref]["INSERTED_BY"] = $row["INSERTED_BY"];
        $data_arr[$row["ID"]][$str_ref]["FABRIC_DESC"] = $row["FABRIC_DESC"];
        $data_arr[$row["ID"]][$str_ref]["COLOR_ID"] = $row["COLOR_ID"];
        $data_arr[$row["ID"]][$str_ref]["GSM_WEIGHT"] = $row["GSM_WEIGHT"];
        $data_arr[$row["ID"]][$str_ref]["DIA"] = $row["DIA"];
        $data_arr[$row["ID"]][$str_ref]["FINISH_QTY"] = $row["FINISH_QTY"];
        $data_arr[$row["ID"]][$str_ref]["SAMPLE_FABRIC_FINISH_QTY"] = $row["PP_QNTY"] + $row["MTL_QNTY"] + $row["FPT_QNTY"] + $row["GPT_QNTY"];
        $data_arr[$row["ID"]][$str_ref]["PROCESS_LOSS"] = $row["PROCESS_LOSS"];
        $data_arr[$row["ID"]][$str_ref]["ADJUST_GREY_QNTY"] = $row["ADJUST_GREY_QNTY"];
        $data_arr[$row["ID"]][$str_ref]["GREY_QTY"] = $row["GREY_QTY"];
        $data_arr[$row["ID"]][$str_ref]["ORDER_NATURE"] = $row["ORDER_NATURE"];

        $tot_fab_fin_qnty += $row['FINISH_QTY'] + $row["PP_QNTY"] + $row["MTL_QNTY"] + $row["FPT_QNTY"] + $row["GPT_QNTY"];
        $tot_grey_qnty += $row["GREY_QTY"];
        $del_start_date_arr[$row["ID"]] .= $row["DELIVERY_START_DATE"] . ',';

        $job_id_arr[$row['ID']] = $row['ID'];
    }
    // echo "<pre>"; print_r($data_arr); die;
    $total_number_of_job = count($job_id_arr);

    $fso_id_arr = [];
    $total_gray_qnty_arr = [];
    foreach ($data_arr as $fso_id => $fso_data) {
        foreach ($fso_data as $fso_data_key => $val) {
            $fso_id_arr[$fso_id]++;
            $total_gray_qnty_arr[$fso_id] += $val['GREY_QTY'];
        }
    }
    // echo "<pre>"; print_r($fso_id_arr); die;


    // ======================= GBL TEMP ENGINE =======================
    $con = connect();
    execute_query("DELETE from GBL_TEMP_ENGINE where USER_ID=$user_name and ENTRY_FORM=84 and REF_FROM in(1)");
    oci_commit($con);
    fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 84, 1, $job_id_arr, $empty_arr);


    // ======================= YARN ISSUE (ALLOCATED) =======================
    $yarn_sql = "SELECT a.PO_BREAK_DOWN_ID, min(a.ALLOCATION_DATE) as ALLOCATION_DATE, sum(a.QNTY) as QNTY
    from INV_MATERIAL_ALLOCATION_MST a, GBL_TEMP_ENGINE b
    where a.PO_BREAK_DOWN_ID=b.REF_VAL and b.USER_ID=$user_name and b.ENTRY_FORM=84 and b.REF_FROM=1 and a.ENTRY_FORM= 475 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0
    group by a.PO_BREAK_DOWN_ID";
    // echo $yarn_sql; die;
    $yarn_sql_result = sql_select($yarn_sql);

    $yarn_arr = array(); $tot_yarn_issue_qnty = 0;
    foreach ($yarn_sql_result as $row) {
        $yarn_arr[$row["PO_BREAK_DOWN_ID"]]['QNTY'] = $row["QNTY"];
        $yarn_arr[$row["PO_BREAK_DOWN_ID"]]['ALLOCATION_DATE'] = $row["ALLOCATION_DATE"];

        $tot_yarn_issue_qnty += $row["QNTY"];
    }
    // echo "<pre>"; print_r($yarn_arr); die;


    // ================ KNITING PRODUCTION  =======================
    $knitting_sql = "SELECT a.PO_BREAKDOWN_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.GREY_RECEIVE_QNTY
    from ORDER_WISE_PRO_DETAILS a, PRO_GREY_PROD_ENTRY_DTLS b, GBL_TEMP_ENGINE g
    where a.DTLS_ID=b.ID and a.PO_BREAKDOWN_ID = g.REF_VAL and g.USER_ID=$user_name and g.ENTRY_FORM=84 and g.REF_FROM=1 AND a.ENTRY_FORM = 2 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.IS_SALES = 1 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0";
    // echo $knitting_sql; die;
    $knitting_sql_result = sql_select($knitting_sql);

    $knitting_arr = array(); $tot_knitting_qnty = 0;
    foreach ($knitting_sql_result as $row) {
        $str_ref = $row["FEBRIC_DESCRIPTION_ID"] . '*' . $row["COLOR_ID"] . '*' . $row["GSM"] . '*' . $row["WIDTH"];
        $knitting_arr[$row["PO_BREAKDOWN_ID"]][$str_ref]['GREY_RECEIVE_QNTY'] += $row["GREY_RECEIVE_QNTY"];
        $knitting_arr[$row["PO_BREAKDOWN_ID"]][$str_ref]['DETERMINATION_ID'] = $row["FEBRIC_DESCRIPTION_ID"];
        $knitting_arr[$row["PO_BREAKDOWN_ID"]][$str_ref]['COLOR'] = $row["COLOR_ID"];
        $knitting_arr[$row["PO_BREAKDOWN_ID"]][$str_ref]['GSM'] = $row["GSM"];
        $knitting_arr[$row["PO_BREAKDOWN_ID"]][$str_ref]['WIDTH'] = $row["WIDTH"];

        $tot_knitting_qnty += $row["GREY_RECEIVE_QNTY"];
    }
    // echo "<pre>"; print_r($knitting_arr); die;


    // ======================= DYEING PRODUCTION =======================
    $dyeing_sql = "SELECT a.SALES_ORDER_ID, a.COLOR_ID, b.PRODUCTION_DATE as START_DATE, b.PROCESS_END_DATE as END_DATE, c.PRODUCTION_QTY, c.GSM, c.DIA_WIDTH, d.DETARMINATION_ID
    FROM PRO_BATCH_CREATE_MST a, PRO_FAB_SUBPROCESS b, PRO_FAB_SUBPROCESS_DTLS c, PRODUCT_DETAILS_MASTER d, GBL_TEMP_ENGINE g
    WHERE a.ID=b.BATCH_ID AND b.ID=c.MST_ID AND c.PROD_ID=d.ID AND b.ENTRY_FORM = 35 AND c.ENTRY_PAGE = 35 AND c.LOAD_UNLOAD_ID = 2 AND a.SALES_ORDER_ID = g.REF_VAL AND g.USER_ID=$user_name AND g.ENTRY_FORM=84 AND g.REF_FROM=1 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0";
    // echo $dyeing_sql; die;
    $dyeing_sql_result = sql_select($dyeing_sql);

    $dyeing_arr = array(); $dyeing_date_arr = array(); $total_dyeing_qnty_arr = array(); $tot_dyeing_qnty = 0;
    foreach ($dyeing_sql_result as $row) {
        $str_ref = $row["DETARMINATION_ID"] . '*' . $row["COLOR_ID"] . '*' . $row["GSM"] . '*' . $row["DIA_WIDTH"];
        $dyeing_arr[$row["SALES_ORDER_ID"]][$str_ref]['PRODUCTION_QTY'] += $row["PRODUCTION_QTY"];
        $dyeing_date_arr[$row["SALES_ORDER_ID"]]['START_DATE'] .= $row["END_DATE"] . ',';
        $dyeing_date_arr[$row["SALES_ORDER_ID"]]['END_DATE'] .= $row["START_DATE"] . ',';
        $total_dyeing_qnty_arr[$row["SALES_ORDER_ID"]] += $row["PRODUCTION_QTY"];

        $tot_dyeing_qnty += $row["PRODUCTION_QTY"];
    }
    // echo "<pre>"; print_r($total_dyeing_qnty_arr); die;


    // ======================= FINISHING PRODUCTION =======================
    $finishing_sql = "SELECT c.PO_BREAKDOWN_ID, b.RECEIVE_QNTY as PRODUCTION_QTY, b.FABRIC_DESCRIPTION_ID, b.COLOR_ID, b.GSM, b.WIDTH, a.RECEIVE_DATE
    FROM INV_RECEIVE_MASTER a, PRO_FINISH_FABRIC_RCV_DTLS b, ORDER_WISE_PRO_DETAILS c, GBL_TEMP_ENGINE g
    WHERE a.ID=B.MST_ID AND b.ID=c.DTLS_ID AND c.PO_BREAKDOWN_ID = g.REF_VAL AND g.USER_ID=$user_name AND g.ENTRY_FORM=84 AND g.REF_FROM=1 AND c.ENTRY_FORM = 66 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 AND c.IS_SALES = 1 ";
    // echo $finishing_sql; die;
    $finishing_sql_result = sql_select($finishing_sql);

    $finishing_arr = array(); $finishing_date_arr = array(); $total_finishing_qnty_arr = array(); $tot_finishing_qnty = 0;
    foreach ($finishing_sql_result as $row) {
        $str_ref = $row["FABRIC_DESCRIPTION_ID"] . '*' . $row["COLOR_ID"] . '*' . $row["GSM"] . '*' . $row["WIDTH"];
        $finishing_arr[$row["PO_BREAKDOWN_ID"]][$str_ref]['QUANTITY'] += $row["PRODUCTION_QTY"];
        $finishing_date_arr[$row["PO_BREAKDOWN_ID"]] .= $row["RECEIVE_DATE"] . ',';
        $total_finishing_qnty_arr[$row["PO_BREAKDOWN_ID"]] += $row["PRODUCTION_QTY"];

        $tot_finishing_qnty += $row["PRODUCTION_QTY"];
    }
    // echo "<pre>"; print_r($finishing_date_arr); die;


    // ======================= FINISHING DELIVERY =======================
    $finishing_delivery_sql = "SELECT c.PO_BREAKDOWN_ID, b.ISSUE_QNTY, d.DETARMINATION_ID, d.COLOR, d.GSM, d.DIA_WIDTH, a.ISSUE_DATE
    FROM INV_ISSUE_MASTER a, INV_FINISH_FABRIC_ISSUE_DTLS b, ORDER_WISE_PRO_DETAILS c, PRODUCT_DETAILS_MASTER d, GBL_TEMP_ENGINE g
    WHERE a.ID=B.MST_ID AND b.ID=c.DTLS_ID AND c.PROD_ID=d.ID AND c.PO_BREAKDOWN_ID = g.REF_VAL AND g.USER_ID=$user_name AND g.ENTRY_FORM=84 AND g.REF_FROM=1 AND c.ENTRY_FORM = 318 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.IS_SALES = 1 AND d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0";
    // echo $finishing_delivery_sql; die;
    $finishing_delivery_sql_result = sql_select($finishing_delivery_sql);

    $finishing_delivery_arr = array(); $finishing_delivery_date_arr = array(); $total_finishing_delivery_qnty_arr = array(); $tot_finishing_delivery_qnty = 0;
    foreach ($finishing_delivery_sql_result as $row) {
        $str_ref = $row["DETARMINATION_ID"] . '*' . $row["COLOR"] . '*' . $row["GSM"] . '*' . $row["DIA_WIDTH"];
        $finishing_delivery_arr[$row["PO_BREAKDOWN_ID"]][$str_ref]['QUANTITY'] += $row["ISSUE_QNTY"];
        $finishing_delivery_date_arr[$row["PO_BREAKDOWN_ID"]] .= $row["ISSUE_DATE"] . ',';
        $total_finishing_delivery_qnty_arr[$row["PO_BREAKDOWN_ID"]] += $row["ISSUE_QNTY"];

        $tot_finishing_delivery_qnty += $row["ISSUE_QNTY"];
    }
    // echo "<pre>"; print_r($finishing_delivery_arr); die;

	$saved_approval = sql_select("SELECT a.mst_id, a.deter_id, a.gsm, a.dia, a.color_id, a.ld_approval, a.fbl_approval, a.strike_approval from fabric_sales_order_approval a, gbl_temp_engine b where a.mst_id=b.ref_val and b.user_id=$user_name and b.entry_form=84 and b.ref_from=1 and a.status_active=1 and a.is_deleted=0");

    $approvalInfoArr = array();
	foreach ($saved_approval as $row)
    {
        $str_ref = $row[csf('deter_id')] . '*' . $row[csf('color_id')] . '*' . $row[csf('gsm')] . '*' . $row[csf('dia')];

		$approvalInfoArr[$row[csf('mst_id')]][$str_ref]['ld_approval'] = $row[csf('ld_approval')];
		$approvalInfoArr[$row[csf('mst_id')]][$str_ref]['fbl_approval'] = $row[csf('fbl_approval')];
		$approvalInfoArr[$row[csf('mst_id')]][$str_ref]['strike_approval'] = $row[csf('strike_approval')];
	}
    unset($saved_approval);

    execute_query("DELETE from GBL_TEMP_ENGINE where USER_ID=$user_name and ENTRY_FORM=84 and REF_FROM in(1)");
    oci_commit($con);
    disconnect($con);
    $tna_pass = 0;
    foreach ($data_arr as $fso_id => $fso_data) {
        $fin_delivery_date_arr = explode(",", rtrim($finishing_delivery_date_arr[$fso_id], ','));
        $fin_delivery_start_date = min($fin_delivery_date_arr);

        $delivery_start_date_arr = explode(",", rtrim($del_start_date_arr[$fso_id], ','));
        $deliv_start_date = min($delivery_start_date_arr);

        if($fin_delivery_start_date < $deliv_start_date){
            $tna_pass++;
        }
    }

    ob_start();
    ?>
    <div align="left" style="padding-bottom: 10px;">
        <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
                <tr>
                    <th colspan="6">Marchant (User)</th>
                </tr>
                <tr>
                    <th align="center" width="120"></th>
                    <th width="80" align="right"></th>
                    <th width="80" align="right">Balance Qnty</th>
                    <th align="center" width="120"></th>
                    <th width="80" align="right"></th>
                    <th width="80" align="right">Balance Qnty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" width="120">Total Order Qty Finish</td>
                    <td width="80" align="right"><? echo number_format($tot_fab_fin_qnty,2,'.','');?></td>
                    <td width="80" align="right"></td>
                    <td align="center" width="120">Finish Production</td>
                    <td width="80" align="right"><? echo number_format($tot_finishing_qnty,2,'.',''); ?></td>
                    <td width="80" align="right"><? echo number_format($tot_dyeing_qnty-$tot_finishing_qnty,2,'.',''); ?></td>
                </tr>
                <tr>
                    <td align="center">Grey Fabric Total</td>
                    <td align="right"><? echo number_format($tot_grey_qnty,2,'.','');?></td>
                    <td align="right"></td>
                    <td align="center">Finish Delivery</td>
                    <td align="right"><? echo number_format($tot_finishing_delivery_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_grey_qnty-$tot_finishing_delivery_qnty,2,'.',''); ?></td>
                </tr>
                <tr>
                    <td align="center">Yarn Issue (Allocated)</td>
                    <td align="right"><? echo number_format($tot_yarn_issue_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_grey_qnty-$tot_yarn_issue_qnty,2,'.',''); ?></td>
                    <td align="center">Total Job</td>
                    <td align="right"><? echo number_format($total_number_of_job,2,'.','');?></td>
                    <td align="right"></td>
                </tr>
                <tr>
                    <td align="center">Total Knitting</td>
                    <td align="right"><? echo number_format($tot_knitting_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_grey_qnty-$tot_knitting_qnty,2,'.',''); ?></td>
                    <td align="center">TNA Pass</td>
                    <td align="right"><? echo $tna_pass;?></td>
                    <td align="right"></td>
                </tr>
                <tr>
                    <td align="center">Dyeing Total Qty</td>
                    <td align="right"><? echo number_format($tot_dyeing_qnty,2,'.','');?></td>
                    <td align="right"><? echo number_format($tot_grey_qnty-$tot_dyeing_qnty,2,'.','');?></td>
                    <td align="center">Achivement</td>
                    <td align="right">
                        <?
                            echo round(($tna_pass/$total_number_of_job)*100, 2). '%';
                        ?>
                    </td>
                    <td align="right"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div align="left">
        <table class="rpt_table" width="3000" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" style="margin-top:20px">
            <thead>
                <tr>
                    <th width="30" class="word_wrap_break" rowspan="2">SL</th>
                    <th width="100" class="word_wrap_break" rowspan="2">Cust Buyer</th>
                    <th width="60" class="word_wrap_break" rowspan="2">Booking Date</th>
                    <th width="60" class="word_wrap_break" rowspan="2">Booking Receive Date</th>
                    <th width="120" class="word_wrap_break" colspan="2">Delivery Date</th>
                    <th width="60" class="word_wrap_break" rowspan="2">Sales Order</th>
                    <th width="100" class="word_wrap_break" rowspan="2">Booking no</th>
                    <th width="100" class="word_wrap_break" rowspan="2">Order Nature</th>
                    <th width="80" class="word_wrap_break" rowspan="2">GMT Merchant Name</th>
                    <th width="170" class="word_wrap_break" rowspan="2">Fabrication</th>
                    <th width="60" class="word_wrap_break" rowspan="2">Color</th>
                    <th width="60" class="word_wrap_break" rowspan="2">Lab Dip Approval</th>
                    <th width="80" class="word_wrap_break" rowspan="2">FBL/Dyelot/Bulk Hanger Approval</th>
                    <th width="80" class="word_wrap_break" rowspan="2">Strike Off Approval</th>
                    <th width="60" class="word_wrap_break" rowspan="2">GSM</th>
                    <th width="60" class="word_wrap_break" rowspan="2">DIA</th>
                    <th width="70" class="word_wrap_break" rowspan="2">Bulk Fabric (Finish)</th>

                    <th width="350" class="word_wrap_break" colspan="5">PP MTL FPT GPT</th>

                    <th width="200" class="word_wrap_break" colspan="3">Yarn Issue (Allocated)</th>

                    <th width="140" class="word_wrap_break" colspan="2">Knitting Status</th>

                    <th width="260" class="word_wrap_break" colspan="4">Dyeing Status</th>

                    <th width="340" class="word_wrap_break" colspan="5">Finishing Status</th>

                    <th width="260" class="word_wrap_break" colspan="4">Finish Delivery to Party Status</th>

                    <th class="word_wrap_break">TNA Result</th>
                </tr>
                <tr>
                    <th width="60" class="word_wrap_break">Delivery Start Date</th>
                    <th width="60" class="word_wrap_break">Delivery End Date</th>

                    <th width="70" class="word_wrap_break">Sample Fabric (Finish)</th>
                    <th width="70" class="word_wrap_break">Total Fabric (Finish)</th>
                    <th width="70" class="word_wrap_break">Process loss</th>
                    <th width="70" class="word_wrap_break">Grey Adjustment</th>
                    <th width="70" class="word_wrap_break">Grey Fabric</th>

                    <th width="70" class="word_wrap_break">Total Qty</th>
                    <th width="70" class="word_wrap_break">Balance Qty</th>
                    <th width="60" class="word_wrap_break">Issue Date</th>

                    <th width="70" class="word_wrap_break">Total Qty</th>
                    <th width="70" class="word_wrap_break">Balance Qty</th>

                    <th width="70" class="word_wrap_break">Total Qty</th>
                    <th width="70" class="word_wrap_break">Balance Qty</th>
                    <th width="60" class="word_wrap_break">Start Date</th>
                    <th width="60" class="word_wrap_break">End Date</th>

                    <th width="70" class="word_wrap_break">Total Qty</th>
                    <th width="70" class="word_wrap_break">Balance Qty</th>
                    <th width="60" class="word_wrap_break">Start Date</th>
                    <th width="60" class="word_wrap_break">End Date</th>
                    <th width="80" class="word_wrap_break">Lead Time After Dyeing To Finishing Days</th>

                    <th width="70" class="word_wrap_break">Total Qty</th>
                    <th width="70" class="word_wrap_break">Balance Qty</th>
                    <th width="60" class="word_wrap_break">Start Date</th>
                    <th width="60" class="word_wrap_break">End Date</th>

                    <th class="word_wrap_break">Pass/Fail</th>
                </tr>
            </thead>
        </table>
        <div style=" max-height:350px; width:3020px; overflow-y:scroll;" id="scroll_body">
            <table class="rpt_table" id="table_body" width="3000" cellpadding="0" cellspacing="0" border="1" rules="all" >
                <tbody>
                    <?
                    $i = 1;
                    $total_grey_qnty = $total_finishing_qnty = $total_finishing_delivery_qnty = 0;
                    $ld_approval=$fbl_approval=$strike_approval="";
                    foreach ($data_arr as $fso_id => $fso_data)
                    {
                        $dyeing_start_date_arr = explode(",", rtrim($dyeing_date_arr[$fso_id]['START_DATE'], ','));
                        $dyeing_start_date = min($dyeing_start_date_arr);
                        $dyeing_end_date_arr = explode(",", rtrim($dyeing_date_arr[$fso_id]['END_DATE'], ','));
                        $dyeing_end_date = max($dyeing_end_date_arr);

                        $finishing_date_arr2 = explode(",", rtrim($finishing_date_arr[$fso_id], ','));
                        $finishing_start_date = min($finishing_date_arr2);
                        $finishing_end_date = max($finishing_date_arr2);

                        $finishing_delivery_date_arr2 = explode(",", rtrim($finishing_delivery_date_arr[$fso_id], ','));
                        $finishing_delivery_start_date = min($finishing_delivery_date_arr2);
                        $finishing_delivery_end_date = max($finishing_delivery_date_arr2);

                        foreach ($fso_data as $fso_data_key => $val) {
                            $rowspan = $fso_id_arr[$fso_id];

                            if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                            else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                                <?
                                $total_grey_qnty += $val['GREY_QTY'];
                                $total_finishing_qnty += $finishing_arr[$fso_id][$fso_data_key]['QUANTITY'];
                                $total_finishing_delivery_qnty += $finishing_delivery_arr[$fso_id][$fso_data_key]['QUANTITY'];

                                $ld_approval     =  $approvalInfoArr[$fso_id][$fso_data_key]['ld_approval'];
                                $fbl_approval    =  $approvalInfoArr[$fso_id][$fso_data_key]['fbl_approval'];
                                $strike_approval =  $approvalInfoArr[$fso_id][$fso_data_key]['strike_approval'];

                                if (!in_array($fso_id, $fso_id_arr)) {

                                    $deli_start_date =  $val['DELIVERY_START_DATE'];
                                    $del_start_date = strtotime($deli_start_date);
                                    $today_date = strtotime(date('Y-m-d'));
                                    $datediff = $del_start_date - $today_date;
                                    $datediff = round($datediff / (60 * 60 * 24));

                                    $tot_balance_fin_del_qnty =  $total_gray_qnty_arr[$fso_id] - $total_finishing_delivery_qnty_arr[$fso_id];

                                    $fin_del_start_date = strtotime($finishing_delivery_start_date);

                                    $bgcolor = "";
                                    $result = "";
                                    // if($datediff >= 0 && $datediff <= 7){
                                    //     $bgcolor = "green";
                                    // }
                                    // if($tot_balance_fin_del_qnty > 0 && $finishing_delivery_start_date != ""){
                                    //     $bgcolor = "yellow";
                                    // }
                                    // if($fin_del_start_date > $del_start_date){
                                    //     $bgcolor = "red";
                                    // }else{
                                    //     $bgcolor = "";
                                    // }
                                    // if($today_date >=$del_start_date  && $finishing_delivery_start_date == "")
                                    // {
                                    //     $bgcolor = "#808000";
                                    // }



                                    $finishing_delivery_percentage = ($total_finishing_delivery_qnty_arr[$fso_id] / $total_gray_qnty_arr[$fso_id]) * 100;

                                    if($finishing_delivery_percentage >= 98)
                                    {

                                        if(strtotime($val['DELIVERY_DATE'])>=strtotime($finishing_delivery_end_date))
                                        {
                                            $bgcolor = "green";
                                            $result = "Pass";
                                        }
                                    }
                                    else if( $today_date<=strtotime($val['DELIVERY_DATE']) && $finishing_delivery_percentage <= 98 && $finishing_delivery_percentage != 0)
                                    {
                                        $bgcolor = "Yellow";
                                        $result = "Pending";
                                    }
                                    else if( $today_date>=strtotime($val['DELIVERY_DATE']) && $today_date>=$del_start_date && $finishing_delivery_percentage <= 98 && $finishing_delivery_percentage != 0)
                                    {
                                        $bgcolor = "red";
                                        $result = "Fail";
                                    }
                                    else if($today_date >=$del_start_date  && $today_date >=strtotime($val['DELIVERY_DATE'])  && $finishing_delivery_start_date == "" && $finishing_delivery_percentage == 0)
                                    {
                                        $bgcolor = "red";
                                        $result = "Fail";
                                    }
                                    else if($today_date >=$del_start_date  && $finishing_delivery_start_date == "" && $finishing_delivery_percentage == 0)
                                    {
                                        $bgcolor = "#808000";
                                        $result = "Need To Delivery";
                                    }


                                    ?>
                                    <td style="background-color: <? echo $bgcolor; ?>" class="word_wrap_break" width="30" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
                                    <td class="word_wrap_break" width="100" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $buyer_arr[$val['CUSTOMER_BUYER']]; ?></td>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $val['BOOKING_DATE']; ?></td>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $val['BOOKING_APPROVAL_DATE']; ?></td>

                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>">
                                        <?
                                            echo $deli_start_date;
                                        ?>
                                    </td>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $val['DELIVERY_DATE']; ?></td>

                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $val['JOB_NO_PREFIX_NUM']; ?></td>
                                    <td class="word_wrap_break" width="100" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $val['SALES_BOOKING_NO']; ?></td>
                                    <td class="word_wrap_break" width="100" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $fbooking_order_nature[$val['ORDER_NATURE']]; ?></td>
                                    <td class="word_wrap_break" width="80" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $user_name_arr[$val['INSERTED_BY']]; ?></td>
                                <?
                                }
                                ?>
                                <td class="word_wrap_break" width="170" align="center"><? echo $val['FABRIC_DESC']; ?></td>
                                <td class="word_wrap_break" width="60" align="center"><? echo $color_library[$val['COLOR_ID']]; ?></td>
                                <td class="word_wrap_break" width="60" align="center">
                                    <input type="checkbox" <? echo ($ld_approval==1) ? "checked" : "";?> >
                                </td>
                                <td class="word_wrap_break" width="80" align="center">
                                    <input type="checkbox" <? echo ($fbl_approval==1) ? "checked" : "";?> >
                                </td>
                                <td class="word_wrap_break" width="80" align="center">
                                    <input type="checkbox" <? echo ($strike_approval==1) ? "checked" : "";?> >
                                </td>
                                <td class="word_wrap_break" width="60" align="center"><? echo $val['GSM_WEIGHT']; ?></td>
                                <td class="word_wrap_break" width="60" align="center"><? echo $val['DIA']; ?></td>
                                <td class="word_wrap_break" width="70" align="right"><? echo $val['FINISH_QTY']; ?></td>

                                <td class="word_wrap_break" width="70" align="right"><? echo $val['SAMPLE_FABRIC_FINISH_QTY']; ?></td>
                                <td class="word_wrap_break" width="70" align="right"><? echo ($val['FINISH_QTY'] + $val['SAMPLE_FABRIC_FINISH_QTY']); ?></td>
                                <td class="word_wrap_break" width="70" align="right"><? echo $val['PROCESS_LOSS']; ?></td>
                                <td class="word_wrap_break" width="70" align="right"><? echo $val['ADJUST_GREY_QNTY']; ?></td>
                                <td class="word_wrap_break" width="70" align="right"><? echo $val['GREY_QTY']; ?></td>

                                <?
                                if (!in_array($fso_id, $fso_id_arr)) {
                                    // $fso_id_arr[$fso_id] = $fso_id;
                                ?>
                                    <td class="word_wrap_break" width="70" valign="middle" align="right" rowspan="<? echo $rowspan; ?>">
                                        <p>
                                            <a href="#report_popup" onClick="allocation_report_popup(<? echo $fso_id; ?>, 'Allocation Details', '1000px')">
                                                <?
                                                echo $yarn_arr[$fso_id]['QNTY'];
                                                $total_yarn_qnty += $yarn_arr[$fso_id]['QNTY'];
                                                ?>
                                            </a>
                                        </p>
                                    </td>
                                    <td class="word_wrap_break" width="70" valign="middle" align="right" rowspan="<? echo $rowspan; ?>">
                                        <?
                                        $balance_qnty = $total_gray_qnty_arr[$fso_id] - $yarn_arr[$fso_id]['QNTY'];
                                        echo $balance_qnty;
                                        $total_balance_qnty += $balance_qnty;
                                        ?>
                                    </td>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $yarn_arr[$fso_id]['ALLOCATION_DATE']; ?></td>
                                <?
                                }
                                ?>

                                <td class="word_wrap_break" width="70" align="right">
                                    <p>
                                        <a href="#report_popup" onClick="total_report_popup(<? echo $fso_id; ?>, 'Knitting Production Details', '1000px', <? echo $knitting_arr[$fso_id][$fso_data_key]['DETERMINATION_ID']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['COLOR']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['GSM']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['WIDTH']; ?>,1)">
                                            <?
                                            echo $knitting_arr[$fso_id][$fso_data_key]['GREY_RECEIVE_QNTY'] ? $knitting_arr[$fso_id][$fso_data_key]['GREY_RECEIVE_QNTY'] : 0;
                                            ?>
                                        </a>
                                    </p>
                                </td>
                                <td class="word_wrap_break" width="70" align="right">
                                    <?
                                    $kniting_balance_qnty = $val['GREY_QTY'] - $knitting_arr[$fso_id][$fso_data_key]['GREY_RECEIVE_QNTY'];
                                    echo $kniting_balance_qnty;
                                    ?>
                                </td>

                                <td class="word_wrap_break" width="70" align="right">
                                    <p>
                                        <a href="#report_popup" onClick="total_report_popup(<? echo $fso_id; ?>, 'Dyeing Information', '1000px', <? echo $knitting_arr[$fso_id][$fso_data_key]['DETERMINATION_ID']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['COLOR']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['GSM']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['WIDTH']; ?>,2)">
                                            <?
                                            echo $dyeing_arr[$fso_id][$fso_data_key]['PRODUCTION_QTY'] ? $dyeing_arr[$fso_id][$fso_data_key]['PRODUCTION_QTY'] : 0;
                                            ?>
                                        </a>
                                    </p>
                                </td>
                                <td class="word_wrap_break" width="70" align="right">
                                    <?
                                    $dyeing_balance_qnty = $val['GREY_QTY'] - $dyeing_arr[$fso_id][$fso_data_key]['PRODUCTION_QTY'];
                                    echo $dyeing_balance_qnty;
                                    ?>
                                </td>
                                <?
                                if (!in_array($fso_id, $fso_id_arr)) {
                                ?>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>">
                                        <?
                                            echo $dyeing_start_date;
                                        ?>
                                    </td>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>">
                                        <?
                                           $dyeing_percentage = ($total_dyeing_qnty_arr[$fso_id] / $total_gray_qnty_arr[$fso_id]) * 100;

                                            // echo $total_dyeing_qnty_arr[$fso_id] . " = ". $total_gray_qnty_arr[$fso_id] . " = ".$dyeing_percentage;
                                            if($dyeing_percentage >= 98){
                                                echo $dyeing_end_date;
                                            }else{
                                                echo "N/A";
                                            }
                                        ?>
                                    </td>
                                <?
                                }
                                ?>

                                <td class="word_wrap_break" width="70" align="right">
                                    <p>
                                        <a href="#report_popup" onClick="total_report_popup(<? echo $fso_id; ?>, 'Finish Fabric Production Details', '1000px', <? echo $knitting_arr[$fso_id][$fso_data_key]['DETERMINATION_ID']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['COLOR']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['GSM']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['WIDTH']; ?>,3)">
                                            <?
                                                echo $finishing_arr[$fso_id][$fso_data_key]['QUANTITY'] ? $finishing_arr[$fso_id][$fso_data_key]['QUANTITY'] : 0;
                                            ?>
                                        </a>
                                    </p>
                                </td>
                                <td class="word_wrap_break" width="70" align="right">
                                    <?
                                    $finishing_balance_qnty = ($dyeing_arr[$fso_id][$fso_data_key]['PRODUCTION_QTY']) - ($finishing_arr[$fso_id][$fso_data_key]['QUANTITY']);
                                    echo $finishing_balance_qnty;
                                    ?>
                                </td>
                                <?
                                if (!in_array($fso_id, $fso_id_arr)) {
                                ?>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $finishing_start_date; ?></td>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>">
                                        <?
                                            $finishing_percentage = ($total_finishing_qnty_arr[$fso_id] / $total_gray_qnty_arr[$fso_id]) * 100;

                                            if($finishing_percentage >= 98){
                                                echo $finishing_end_date;
                                            }else{
                                                echo "N/A";
                                            }
                                        ?>
                                    </td>
                                    <td class="word_wrap_break" width="80" valign="middle" align="center" rowspan="<? echo $rowspan; ?>">
                                        <?
                                        $date_start = strtotime($dyeing_end_date);
                                        $date_end = strtotime($finishing_start_date);
                                        $diff = $date_end - $date_start;
                                        echo round($diff / 86400) . ' days';
                                        ?>
                                    </td>
                                <?
                                }
                                ?>

                                <td class="word_wrap_break" width="70" align="right">
                                    <p>
                                        <a href="#report_popup" onClick="total_report_popup(<? echo $fso_id; ?>, 'Finish Fabric Store Details', '1000px', <? echo $knitting_arr[$fso_id][$fso_data_key]['DETERMINATION_ID']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['COLOR']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['GSM']; ?>, <? echo $knitting_arr[$fso_id][$fso_data_key]['WIDTH']; ?>,4)">
                                            <?
                                                echo $finishing_delivery_arr[$fso_id][$fso_data_key]['QUANTITY'] ? $finishing_delivery_arr[$fso_id][$fso_data_key]['QUANTITY'] : 0;
                                            ?>
                                        </a>
                                    </p>
                                </td>
                                <td class="word_wrap_break" width="70" align="right">
                                    <?
                                    $finishing_delivery_balance_qnty = $val['GREY_QTY'] - $finishing_delivery_arr[$fso_id][$fso_data_key]['QUANTITY'];
                                    echo $finishing_delivery_balance_qnty;
                                    ?>
                                </td>
                                <?
                                if (!in_array($fso_id, $fso_id_arr)) {
                                    $fso_id_arr[$fso_id] = $fso_id;
                                    ?>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>"><? echo $finishing_delivery_start_date; ?></td>
                                    <td class="word_wrap_break" width="60" valign="middle" align="center" rowspan="<? echo $rowspan; ?>">
                                        <?
                                            //$finishing_delivery_percentage = ($total_finishing_delivery_qnty_arr[$fso_id] / $total_gray_qnty_arr[$fso_id]) * 100;

                                            if($finishing_delivery_percentage >= 98){
                                                echo $finishing_delivery_end_date;
                                            }else{
                                                echo "N/A";
                                            }
                                        ?>
                                    </td>
                                    <td class="word_wrap_break" valign="middle" align="center" rowspan="<? echo $rowspan; ?>">
                                        <?
                                            // echo $deli_start_date . " = ". $finishing_delivery_start_date;
                                            // if($fin_del_start_date > $del_start_date){
                                            //     echo "Fail";
                                            // }
                                            // else if( $fin_del_start_date =="")
                                            // {
                                            //     echo "Need To Delevery";
                                            // }
                                            // else{
                                            //     echo "Pass";
                                            // }
                                            echo $result;
                                        ?>
                                    </td>
                                    <?
                                }
                                ?>
                            </tr>
                            <?
                            $total_finish_qnty += $val['FINISH_QTY'];
                            $total_sample_finish_qnty += $val['SAMPLE_FABRIC_FINISH_QTY'];
                            $total_finish_sample_qnty += ($val['FINISH_QTY'] + $val['SAMPLE_FABRIC_FINISH_QTY']);
                            $total_process_loss += $val['PROCESS_LOSS'];
                            $total_adjust_grey_qnty += $val['ADJUST_GREY_QNTY'];

                            $total_knitting_grey_qnty += $knitting_arr[$fso_id][$fso_data_key]['GREY_RECEIVE_QNTY'];
                            $total_knitting_balance_qnty += $kniting_balance_qnty;
                            $total_dyeing_qnty += $dyeing_arr[$fso_id][$fso_data_key]['PRODUCTION_QTY'];
                            $total_dyeing_balance_qnty += $dyeing_balance_qnty;
                            $total_finishing_balance_qnty += $finishing_balance_qnty;
                            $total_finishing_delivery_balance_qnty += $finishing_delivery_balance_qnty;
                        }
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <table class="rpt_table" width="3000" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tfoot>
                <tr style="background-color: #e9e9e9; font-weight: bold;">
                    <td width="30"></td>
                    <td width="100"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="170"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="60"></td>
                    <td width="60">Total</td>
                    <td width="70" align="right"><? echo $total_finish_qnty; ?></td>

                    <td width="70" align="right"><? echo $total_sample_finish_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_finish_sample_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_process_loss; ?></td>
                    <td width="70" align="right"><? echo $total_adjust_grey_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_grey_qnty; ?></td>

                    <td width="70" align="right"><? echo $total_yarn_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_balance_qnty; ?></td>
                    <td width="60"></td>

                    <td width="70" align="right"><? echo $total_knitting_grey_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_knitting_balance_qnty; ?></td>

                    <td width="70" align="right"><? echo $total_dyeing_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_dyeing_balance_qnty; ?></td>
                    <td width="60"></td>
                    <td width="60"></td>

                    <td width="70" align="right"><? echo $total_finishing_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_finishing_balance_qnty; ?></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="80"></td>

                    <td width="70" align="right"><? echo $total_finishing_delivery_qnty; ?></td>
                    <td width="70" align="right"><? echo $total_finishing_delivery_balance_qnty; ?></td>
                    <td width="60"></td>
                    <td width="60"></td>

                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?

    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_name . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}
if ($action == 'allocation_popup') {
    echo load_html_head_contents('Report Info', '../../../', 1, 1, $unicode, '', '');
    extract($_REQUEST);

    $colorArr = return_library_array("SELECT ID, COLOR_NAME FROM LIB_COLOR", "ID", "COLOR_NAME");
    $countArr = return_library_array("SELECT ID, YARN_COUNT FROM LIB_YARN_COUNT", 'ID', 'YARN_COUNT');
    $yarnTypeArr = return_library_array("SELECT YARN_TYPE_ID, YARN_TYPE_SHORT_NAME FROM LIB_YARN_TYPE", 'YARN_TYPE_ID', 'YARN_TYPE_SHORT_NAME');
    $supplierArr = return_library_array("SELECT ID, SUPPLIER_NAME FROM LIB_SUPPLIER", 'ID', 'SUPPLIER_NAME');
    $brandArr = return_library_array("SELECT ID, BRAND_NAME FROM LIB_BRAND", 'ID', 'BRAND_NAME');

    $sql = "SELECT a.ALLOCATION_DATE, a.ITEM_ID as PROD_ID, a.QNTY, a.IS_DYIED_YARN, c.YARN_COUNT_ID, c.YARN_COMP_PERCENT1ST, c.YARN_COMP_TYPE1ST, c.YARN_TYPE, c.COLOR, c.LOT, c.SUPPLIER_ID, c.BRAND
    from INV_MATERIAL_ALLOCATION_MST a, PRODUCT_DETAILS_MASTER c
    where a.ITEM_ID = c.ID AND a.PO_BREAK_DOWN_ID= '$fso_id' AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND c.STATUS_ACTIVE=1 AND c.IS_DELETED=0";
    // echo $sql; die;
    $dataArray = sql_select($sql);

    ?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Yarn Allocation Info (Non-Dyed Yarn)</strong></caption>
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Date</th>
                    <th width="80">Prod. ID</th>
                    <th width="90">Count</th>
                    <th width="120">Composition</th>
                    <th width="90">Yarn Type</th>
                    <th width="90">Color</th>
                    <th width="90">Lot</th>
                    <th width="120">Supplier</th>
                    <th width="100">Brand</th>
                    <th>Allocation Qnty</th>
                </tr>
            </thead>
            <tbody id="allocation_table_body_popup">
                <?
                $sl = 1;
                foreach ($dataArray as $data) {
                    if ($data['IS_DYIED_YARN'] == 0) {
                ?>
                        <tr>
                            <td align="center"><? echo $sl; ?></td>
                            <td align="center"><? echo $data['ALLOCATION_DATE']; ?></td>
                            <td align="center"><? echo $data['PROD_ID']; ?></td>
                            <td align="center"><? echo $countArr[$data['YARN_COUNT_ID']]; ?></td>
                            <td align="center"><? echo $data['YARN_COMP_PERCENT1ST'] . "% "; ?></td>
                            <td align="center"><? echo $yarnTypeArr[$data['YARN_TYPE']]; ?></td>
                            <td align="center"><? echo $colorArr[$data['COLOR']]; ?></td>
                            <td align="center"><? echo $data['LOT']; ?></td>
                            <td align="center"><? echo $supplierArr[$data['SUPPLIER_ID']]; ?></td>
                            <td align="center"><? echo $brandArr[$data['BRAND']]; ?></td>
                            <td align="right"><? echo $data['QNTY']; ?></td>
                        </tr>
                <?
                        $total_allocated_qnty += $data['QNTY'];
                        $sl++;
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10">Total&nbsp;</th>
                    <th align="right"><? echo $total_allocated_qnty; ?></th>
                </tr>
            </tfoot>
        </table>
        <script>
            setFilterGrid("allocation_table_body_popup", -1);
        </script>
    </div>
    <br>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Yarn Allocation Info (Dyed Yarn)</strong></caption>
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Date</th>
                    <th width="80">Prod. ID</th>
                    <th width="90">Count</th>
                    <th width="120">Composition</th>
                    <th width="90">Yarn Type</th>
                    <th width="90">Color</th>
                    <th width="90">Lot</th>
                    <th width="120">Supplier</th>
                    <th width="100">Brand</th>
                    <th>Allocation Qnty</th>
                </tr>
            </thead>
            <tbody id="allocation_table_body_popup2">
                <?
                $sl = 1;
                foreach ($dataArray as $data) {
                    if ($data['IS_DYIED_YARN'] == 1) {
                ?>
                        <tr>
                            <td align="center"><? echo $sl; ?></td>
                            <td align="center"><? echo $data['ALLOCATION_DATE']; ?></td>
                            <td align="center"><? echo $data['PROD_ID']; ?></td>
                            <td align="center"><? echo $countArr[$data['YARN_COUNT_ID']]; ?></td>
                            <td align="center"><? echo $data['YARN_COMP_PERCENT1ST'] . "% "; ?></td>
                            <td align="center"><? echo $yarnTypeArr[$data['YARN_TYPE']]; ?></td>
                            <td align="center"><? echo $colorArr[$data['COLOR']]; ?></td>
                            <td align="center"><? echo $data['LOT']; ?></td>
                            <td align="center"><? echo $supplierArr[$data['SUPPLIER_ID']]; ?></td>
                            <td align="center"><? echo $brandArr[$data['BRAND']]; ?></td>
                            <td align="right"><? echo $data['QNTY']; ?></td>
                        </tr>
                <?
                        $tot_allocated_qnty += $data['QNTY'];
                        $sl++;
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10">Total&nbsp;</th>
                    <th align="right"><? echo $tot_allocated_qnty; ?></th>
                </tr>
            </tfoot>
        </table>
        <script>
            setFilterGrid("allocation_table_body_popup2", -1);
        </script>
    </div>
    <?
    exit();
}

if ($action == 'knitting_popup') {
    echo load_html_head_contents('Report Info', '../../../', 1, 1, $unicode, '', '');
    extract($_REQUEST);

    ?>
    <script>
        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";
            $('#scroll_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
            $('#scroll_body tr:first').show();
        }
    </script>
    <?

    $receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
    $product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
    $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

    if ($determination_id != "") $deter_id_cond = " AND b.FEBRIC_DESCRIPTION_ID=$determination_id";
    if ($color != "") $color_cond = " AND b.COLOR_ID=$color";
    if ($gsm != "") $gsm_cond = " AND b.GSM=$gsm";
    if ($width != "") $width_cond = " AND b.WIDTH=$width";

    $sql = "SELECT a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, b.MACHINE_NO_ID, b.PROD_ID, SUM(c.QUANTITY) as QUANTITY
    FROM INV_RECEIVE_MASTER a, PRO_GREY_PROD_ENTRY_DTLS b, ORDER_WISE_PRO_DETAILS c
    WHERE a.ID=b.MST_ID AND b.ID=c.DTLS_ID AND a.ENTRY_FORM=2 AND c.ENTRY_FORM=2 AND c.PO_BREAKDOWN_ID IN($fso_id) AND c.IS_SALES=1 $deter_id_cond $color_cond $gsm_cond $width_cond AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0 AND c.STATUS_ACTIVE=1 AND c.IS_DELETED=0 GROUP BY b.ID, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, b.MACHINE_NO_ID, b.PROD_ID";
    // echo $sql; die;

    $dataArray = sql_select($sql);
    ?>
    <div style="width:980px" align="center">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" />
    </div>
    <div id="report_container" align="center">
        <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
            <caption><strong>Knitting Production Details</strong></caption>
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Receive ID</th>
                    <th width="80">Receive Basis</th>
                    <th width="170">Product Details</th>
                    <th width="70">Booking / Program No.</th>
                    <th width="60">Machine No.</th>
                    <th width="80">Production Date</th>
                    <th width="90">In-House Production</th>
                    <th width="90">Outside Production</th>
                    <th width="90">Production Qnty</th>
                    <th>Challan No.</th>
                </tr>
            </thead>
        </table>
        <div style="width:990px; max-height:320px; overflow-y:scroll" id="scroll_body">
            <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                <tbody id="knitting_table_body_popup">
                    <?
                    $sl = 1;
                    foreach ($dataArray as $data) {
                        if ($sl % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                            <td width="30" align="center"><? echo $sl; ?></td>
                            <td width="100" align="center"><? echo $data['RECV_NUMBER']; ?></td>
                            <td width="80" align="center"><? echo $receive_basis[$data['RECEIVE_BASIS']]; ?></td>
                            <td width="170" align="center"><? echo $product_arr[$data['PROD_ID']]; ?></td>
                            <td width="70" align="center"><? echo $data['BOOKING_NO']; ?></td>
                            <td width="60" align="center"><? echo $machine_arr[$data['MACHINE_NO_ID']]; ?></td>
                            <td width="80" align="center"><? echo $data['RECEIVE_DATE']; ?></td>
                            <td width="90" align="right">
                                <?
                                if ($data['KNITTING_SOURCE'] != 3) {
                                    echo number_format($data['QUANTITY'], 2, '.', '');
                                    $total_receive_qnty_in += $data['QUANTITY'];
                                }
                                ?>
                            </td>
                            <td width="90" align="right">
                                <?
                                if ($data['KNITTING_SOURCE'] == 3) {
                                    echo number_format($data['QUANTITY'], 2, '.', '');
                                    $total_receive_qnty_out += $data['QUANTITY'];
                                }
                                ?>
                            </td>
                            <td width="90" align="right"><? echo number_format($data['QUANTITY'], 2, '.', ''); ?></td>
                            <td align="right"><? echo $data['CHALLAN_NO']; ?></td>
                        </tr>
                        <?
                        $total_receive_qnty += $data['QUANTITY'];
                        $sl++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7">Total&nbsp;</th>
                        <th align="right"><? echo $total_receive_qnty_in; ?></th>
                        <th align="right"><? echo $total_receive_qnty_out; ?></th>
                        <th align="right"><? echo $total_receive_qnty; ?></th>
                        <th align="right">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <script>
        setFilterGrid("knitting_table_body_popup", -1);
    </script>
    <?
    exit();
}

if ($action == "dyeing_popup") {
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    // page_style();
    extract($_REQUEST);
    ?>
    <script>
        function print_window() {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";
            $('#scroll_body tr:first').hide();

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
            $('#scroll_body tr:first').show();
        }
    </script>
    <?
    $color_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');

    $result = sql_select("SELECT a.BATCH_NO,a.BATCH_AGAINST,a.COLOR_ID,a.EXTENTION_NO,a.SALES_ORDER_NO,a.BOOKING_NO, b.ITEM_DESCRIPTION as FEBRIC_DESCRIPTION, SUM(b.BATCH_QNTY) as BATCH_QNTY, c.PROCESS_END_DATE, c.PRODUCTION_DATE, c.PROCESS_ID
    FROM PRO_BATCH_CREATE_MST a, PRO_BATCH_CREATE_DTLS b, PRO_FAB_SUBPROCESS c, PRODUCT_DETAILS_MASTER d
    WHERE a.ID=b.MST_ID and a.ID=c.BATCH_ID and a.COLOR_ID=$color and c.LOAD_UNLOAD_ID=2 and c.ENTRY_FORM=35 and b.PROD_ID=d.ID and d.DETARMINATION_ID=$determination_id and b.PO_ID in($fso_id) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0
    GROUP BY a.BATCH_NO,a.BATCH_AGAINST,a.COLOR_ID, a.EXTENTION_NO, a.SALES_ORDER_NO, a.BOOKING_NO, b.ITEM_DESCRIPTION, c.PROCESS_END_DATE, c.PRODUCTION_DATE, c.PROCESS_ID");

    ?>
    <div style="width:980px" align="center">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" />
    </div>
    <fieldset>
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="50">SL No</th>
                        <th width="100">Batch No</th>
                        <th width="70">Ext. No</th>
                        <th width="150">Sales Order No</th>
                        <th width="105">Booking No</th>
                        <th width="80">Batch Quantity</th>
                        <th width="80">Batch Date</th>
                        <th width="80">Batch Unload Date</th>
                        <th width="80">Batch Against</th>
                        <th width="85">Batch For</th>
                        <th>Color</th>
                    </tr>
                </thead>
            </table>
            <div style="width:990px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                    <tbody id="dyeing_table_body_popup">
                        <?php
                        $i = 1;
                        foreach ($result as $row) {
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                            else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
                                <td align="center" width="50"><?php echo $i; ?></td>
                                <td align="center" width="100"><?php echo $row["BATCH_NO"]; ?></td>
                                <td align="center" width="70"><?php echo $row["EXTENTION_NO"]; ?></td>
                                <td align="center" width="150">
                                    <p><?php echo $row["SALES_ORDER_NO"]; ?></p>
                                </td>
                                <td align="center" width="105"><?php echo $row["BOOKING_NO"]; ?></td>
                                <td align="right" width="80"><?php echo number_format($row["BATCH_QNTY"], 2); ?></td>
                                <td align="center" width="80"><?php echo $row["PROCESS_END_DATE"]; ?></td>
                                <td align="center" width="80"><?php echo $row["PRODUCTION_DATE"]; ?></td>
                                <td align="center" width="80"><?php echo $batch_against[$row["BATCH_AGAINST"]]; ?></td>
                                <td align="center" width="85"><?php echo $conversion_cost_head_array[$row["PROCESS_ID"]]; ?></td>
                                <td align="center"><?php echo $color_arr[$row["COLOR_ID"]]; ?></td>
                            </tr>
                            <?
                            $total_batch_qnty += $row["BATCH_QNTY"];
                            $i++;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><? echo number_format($total_batch_qnty, 2); ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </fieldset>
    <script>
        setFilterGrid("dyeing_table_body_popup", -1);
    </script>
    <?
    exit();
}

if($action=="finishing_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	// page_style();
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";
            $('#scroll_body tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
            $('#scroll_body tr:first').show();
		}
	</script>
	<?
	$color_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');

	$sql = "SELECT a.ID, a.BATCH_NO,a.BATCH_AGAINST,a.COLOR_ID,a.EXTENTION_NO,a.SALES_ORDER_NO,a.BOOKING_NO, a.BATCH_FOR as PROCESS_FOR, sum(c.RECEIVE_QNTY) as PRODUCTION_QTY, d.RECEIVE_DATE, d.RECV_NUMBER
	FROM PRO_BATCH_CREATE_MST a, PRO_FINISH_FABRIC_RCV_DTLS c, INV_RECEIVE_MASTER d
	WHERE a.ID=c.BATCH_ID and c.MST_ID=d.ID and a.COLOR_ID=$color and d.ENTRY_FORM in(7,66) and c.ORDER_ID in('$fso_id') and c.FABRIC_DESCRIPTION_ID=$determination_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0
	and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0
	GROUP BY a.ID, a.BATCH_NO,a.BATCH_AGAINST,a.COLOR_ID,a.EXTENTION_NO,a.SALES_ORDER_NO,a.BOOKING_NO, a.BATCH_FOR, d.RECEIVE_DATE, d.RECV_NUMBER ORDER BY d.RECV_NUMBER";
	// echo $sql;
	$result = sql_select($sql);
	?>
	<div style="width:980px" align="center">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/>
    </div>
    <fieldset>
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="50">SL No</th>
                        <th width="100">FFPR. Challan</th>
                        <th width="100">Batch No</th>
                        <th width="50">Ext. No</th>
                        <th width="100">Sales Order No</th>
                        <th width="80">Booking No</th>
                        <th width="80">Fin. Fab. Production</th>
                        <th width="80">Production Date</th>
                        <th width="80">Batch Against</th>
                        <th width="80">Process For</th>
                        <th>Color</th>
                    </tr>
                </thead>
            </table>
            <div style="width:990px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                    <tbody id="finishing_table_body_popup">
                        <?php
                        $i = 1;
                        foreach ($result as $row)
                        {
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
                                <td style="text-align: center;" width="50"><?php echo $i; ?></td>
                                <td style="text-align: center;" width="100"><?php echo $row["RECV_NUMBER"]; ?></td>
                                <td style="text-align: center;" width="100"><?php echo $row["BATCH_NO"]; ?></td>
                                <td style="text-align: center;" width="50"><?php echo $row["EXTENTION_NO"]; ?></td>
                                <td style="text-align: center;" width="100"><p><?php echo $row["SALES_ORDER_NO"]; ?></p></td>
                                <td style="text-align: center;" width="80"><?php echo $row["BOOKING_NO"]; ?></td>
                                <td style="text-align: right;" width="80"><?php echo number_format($row["PRODUCTION_QTY"], 2); ?></td>
                                <td style="text-align: center;" width="80"><?php echo $row["RECEIVE_DATE"]; ?></td>
                                <td style="text-align: center;" width="80"><?php echo $batch_against[$row["BATCH_AGAINST"]]; ?></td>
                                <td style="text-align: center;" width="80"><?php echo $conversion_cost_head_array[$row["PROCESS_FOR"]]; ?></td>
                                <td style="text-align: center;"><?php echo $color_arr[$row["COLOR_ID"]]; ?></td>
                            </tr>
                            <?php
                            $total_production_qty += $row["PRODUCTION_QTY"];
                            $i++;
                        }
                        ?>
                        <tfoot>
                            <tr>
                                <th colspan="6" align="right">Total</th>
                                <th align="right"><? echo number_format($total_production_qty, 2); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
    <script>
        setFilterGrid("finishing_table_body_popup", -1);
    </script>
	<?
	exit();
}

if($action=="finishing_delivery_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	// page_style();
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";
            $('#scroll_body tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
            $('#scroll_body tr:first').show();
		}
	</script>
	<?
	$color_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');

    $sql = "SELECT a.ID, a.BATCH_NO,a.BATCH_AGAINST,a.COLOR_ID,a.EXTENTION_NO,a.SALES_ORDER_NO,a.BOOKING_NO, a.BATCH_FOR AS PROCESS_FOR, SUM(c.ISSUE_QNTY) AS ISSUE_QNTY, d.ISSUE_DATE, d.ISSUE_NUMBER
    FROM PRO_BATCH_CREATE_MST a, INV_FINISH_FABRIC_ISSUE_DTLS c, INV_ISSUE_MASTER d, PRODUCT_DETAILS_MASTER e
    WHERE a.ID=c.BATCH_ID AND c.MST_ID=d.ID AND a.COLOR_ID=$color AND d.ENTRY_FORM IN(224,318) AND c.ORDER_ID IN('$fso_id') AND c.PROD_ID=e.ID AND e.DETARMINATION_ID=$determination_id AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND c.STATUS_ACTIVE=1 AND c.IS_DELETED=0 AND d.STATUS_ACTIVE=1 AND d.IS_DELETED=0
    GROUP BY a.ID, a.BATCH_NO,a.BATCH_AGAINST,a.COLOR_ID,a.EXTENTION_NO,a.SALES_ORDER_NO,a.BOOKING_NO, a.BATCH_FOR, d.ISSUE_DATE, d.ISSUE_NUMBER ORDER BY d.ISSUE_NUMBER";

	// echo $sql; die;
	$result = sql_select($sql);
	?>
	<div style="width:980px" align="center">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton" />
    </div>
    <fieldset>
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="50">SL No</th>
                        <th width="100">Batch No</th>
                        <th width="100">FDSR. Challan</th>
                        <th width="50">Ext. No</th>
                        <th width="100">Sales Order No</th>
                        <th width="80">Booking No</th>
                        <th width="80">Fin. Fab. Deliv.</th>
                        <th width="80">Deliv. Date</th>
                        <th width="80">Batch Against</th>
                        <th width="80">Process For</th>
                        <th>Color</th>
                    </tr>
                </thead>
            </table>
            <div style="width:990px; max-height:320px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="980" cellpadding="0" cellspacing="0">
                    <tbody id="finishing_delivery_table_body_popup">
                        <?php
                        $i = 1;
                        foreach ($result as $row)
                        {
                            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
                                <td style="text-align: center;" width="50"><?php echo $i; ?></td>
                                <td style="text-align: center;" width="100"><?php echo $row["BATCH_NO"]; ?></td>
                                <td style="text-align: center;" width="100"><?php echo $row["ISSUE_NUMBER"]; ?></td>
                                <td style="text-align: center;" width="50"><?php echo $row["EXTENTION_NO"]; ?></td>
                                <td style="text-align: center;" width="100"><p><?php echo $row["SALES_ORDER_NO"]; ?></p></td>
                                <td style="text-align: center;" width="80"><?php echo $row["BOOKING_NO"]; ?></td>
                                <td style="text-align: right;" width="80"><?php echo number_format($row["ISSUE_QNTY"], 2); ?></td>
                                <td style="text-align: center;" width="80"><?php echo $row["ISSUE_DATE"]; ?></td>
                                <td style="text-align: center;" width="80"><?php echo $batch_against[$row["BATCH_AGAINST"]]; ?></td>
                                <td style="text-align: center;" width="80"><?php echo $conversion_cost_head_array[$row["PROCESS_FOR"]]; ?></td>
                                <td style="text-align: center;"><?php echo $color_arr[$row["COLOR_ID"]]; ?></td>
                            </tr>
                            <?php
                            $total_delv_to_store_qty += $row["ISSUE_QNTY"];
                            $i++;
                        }
                        ?>
                        <tfoot>
                            <tr>
                                <th colspan="6" align="right">Total</th>
                                <th align="right"><? echo number_format($total_delv_to_store_qty, 2); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
    <script>
        setFilterGrid("finishing_delivery_table_body_popup", -1);
    </script>
	<?
	exit();
}

?>