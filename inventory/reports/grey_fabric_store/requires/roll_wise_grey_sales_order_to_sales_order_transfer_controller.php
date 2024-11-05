<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
    //$data=explode('_',$data);
    echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
    exit();
}

if ($action == "style_ref_search_popup")
{
    echo load_html_head_contents("FSO Grey Transfer Detalis", "../../../../", 1, 1,'','','');
    extract($_REQUEST);

    ?>

    <script>

        var selected_id = new Array;
        var selected_name = new Array;

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            tbl_row_count = tbl_row_count - 1;

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

            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_job_id' + str).val()) break;
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

            $('#hide_job_id').val(id);
            $('#hide_job_no').val(name);
        }

    </script>

    </head>

    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:780px;">
                    <table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                    class="rpt_table" id="tbl_list">
                    <thead>
                        <th>Buyer Name</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="buyer_td">
                                <?
                                echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $buyerID, "");
                                ?>
                            </td>
                            <td align="center">
                                <?
                                $search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref",3 => "Booking No");
                                $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                                echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                                ?>
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                                id="txt_search_common"/>
                            </td>
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show"
                                onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'roll_wise_grey_sales_order_to_sales_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
                                style="width:90px;"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-top:05px" id="search_div"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_job_search_list_view")
{
    $data = explode('**', $data);
    $company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

    $company_id = $data[0];
    $buyer_id = $data[1];
    $po_buyer_id = $data[2];
    $within_group = $data[3];
    $search_by = $data[4];
    $search_string = trim($data[5]);

    $search_field_cond = '';
    if ($search_string != "") {
        if ($search_by == 1) {
            $search_field_cond = " and a.job_no like '%" . $search_string . "'";
        } else if($search_by == 2) {
            $search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
        }
        else
        {
            $search_field_cond = " and a.sales_booking_no like '%$search_string%'";
        }
    }

    if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
    //echo "==".$_SESSION['logic_erp']["buyer_id"];die;
    if ($po_buyer_id == 0) {
        if ($_SESSION['logic_erp']["buyer_id"] != "")
        {
            if($within_group == 1)
            {
                $po_buyer_id_cond = " and a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            }
            else if($within_group == 2)
            {
                $po_buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            }
            else
            {
                $po_buyer_id_cond = " and (a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] .") or a.buyer_id in ( " .$_SESSION['logic_erp']["buyer_id"]. ") )";
            }
        }
        else
        {
            $po_buyer_id_cond = "";
        }
    }
    else
    {
        if($within_group == 1)
        {
            $po_buyer_id_cond = " and a.po_buyer=$po_buyer_id";
        }
        else if($within_group == 2)
        {
            $po_buyer_id_cond = " and a.buyer_id=$po_buyer_id";
        }
        else
        {
            $po_buyer_id_cond = " and (a.po_buyer=$po_buyer_id or a.buyer_id=$po_buyer_id )";
        }
    }

    if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
    else $year_field = "";

    $sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, a.po_buyer, a.po_company_id from fabric_sales_order_mst a
    where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $po_buyer_id_cond order by a.id desc";

    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="115">Sales Order No</th>
            <th width="60">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Sales Order Buyer</th>
            <th width="70">PO Buyer</th>
            <th width="70">PO Company</th>
            <th width="120">Sales/ Booking No</th>
            <th>Style Ref.</th>
        </thead>
    </table>
    <div style="width:820px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" align="left" id="tbl_list_search">
            <?
            $i = 1;
            foreach ($result as $row)
            {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                if ($row[csf('within_group')] == 1)
                    $sales_order_buyer = $company_arr[$row[csf('buyer_id')]];
                else
                    $sales_order_buyer = $buyer_arr[$row[csf('buyer_id')]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
                    <td width="40" align="center"><? echo $i; ?>
                    <input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                    <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row[csf('job_no')]; ?>"/>
                </td>
                <td width="115" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
                <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                <td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? echo $sales_order_buyer; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? echo $company_arr[$row[csf('po_company_id')]]; ?>&nbsp;</p></td>
                <td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
                <td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
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

if ($action == "booking_no_search_popup")
{
    echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>

        function js_set_value(booking_no,booking_num) {
            $('#hidden_booking_no').val(booking_no);
            $('#hidden_booking_num').val(booking_num);
            parent.emailwindow.hide();
        }

    </script>
    </head>

    <body>
        <div align="center" style="width:730px;">
            <form name="searchwofrm" id="searchwofrm" autocomplete=off>
                <fieldset style="width:100%;">
                    <legend>Enter search words</legend>
                    <table cellpadding="0" cellspacing="0" width="725" class="rpt_table" border="1" rules="all">
                        <thead>
                            <th>Po Buyer</th>
                            <th>Booking Date</th>
                            <th>Search By</th>
                            <th id="search_by_td_up" width="150">Please Enter Booking No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
                                class="formbutton"/>
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
                                value="<? echo $companyID; ?>">
                                <input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
                                value="<? echo $cbo_within_group; ?>">
                                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                                <input type="hidden" name="hidden_booking_num" id="hidden_booking_num" class="text_boxes" value="">
                            </th>
                        </thead>
                        <tr>
                            <td align="center">
                                <?
                                echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                                style="width:70px" readonly>To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                                readonly>
                            </td>
                            <td align="center">
                                <?
                                $search_by_arr = array(1 => "Booking No", 2 => "Job No");
                                $dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
                                echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                                ?>
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                                id="txt_search_common"/>
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show"
                                onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'roll_wise_grey_sales_order_to_sales_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                style="width:90px;"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </table>
                    <div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_booking_search_list_view") 
{
    $data = explode("_", $data);

    $search_string = trim($data[0]);
    $search_by = $data[1];
    $company_id = $data[2];
    $buyer_id = $data[3];
    $date_from = trim($data[4]);
    $date_to = trim($data[5]);
    $cbo_within_group = trim($data[6]);


    if ($date_from != "" && $date_to != "") {
        if ($db_type == 0) {
            $date_cond = "and booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
        } else {
            $date_cond = "and booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
        }
    }

    $company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

    $search_field_cond = "";
    if ($search_by == 1) {
        $search_field_cond .= " and sales_booking_no like '%$search_string%'";
    }else{
        $search_field_cond .= " and po_job_no like '%$search_string%'";
    }
    if ($buyer_id != 0) {
        $search_field_cond .= " and po_buyer=$buyer_id";
    }
    if ($cbo_within_group > 0) {
        $search_field_cond .= " and within_group=$cbo_within_group";
    }
    $sql = "select id, sales_booking_no booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no from fabric_sales_order_mst where company_id= $company_id and status_active =1 and is_deleted=0 $search_field_cond $date_cond group by id, sales_booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no";

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="80">PO Buyer</th>
            <th width="120">Booking No</th>
            <th width="90">Sales Order No</th>
            <th width="120">Style Ref.</th>
            <th width="80">Booking Date</th>
            <th>Job No.</th>
        </thead>
    </table>
    <div style="width:720px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table"
        id="tbl_list_search">
        <?
        $i = 1;
        $j = 1;
        $result = sql_select($sql);
        foreach ($result as $row) {
            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

            if ($row[csf('po_break_down_id')] != "") {
                $po_no = '';
                $po_ids = explode(",", $row[csf('po_break_down_id')]);
                foreach ($po_ids as $po_id) {
                    if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
                }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>')">
                <td width="40"><? echo $i; ?></td>
                <td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
                <td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                <td><? echo $row[csf('po_job_no')]; ?></td>
            </tr>
            <?
            $i++;
        }

        $sql_partial = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, b.job_no,b.po_job_no,b.style_ref_no from wo_booking_mst a, wo_booking_dtls c,fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_no_prefix_num,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,b.job_no,b.po_job_no,b.style_ref_no";
        $result_partial = sql_select($sql_partial);
        foreach ($result_partial as $row) {
            if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

            if ($row[csf('po_break_down_id')] != "") {
                $po_no = '';
                $po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
                foreach ($po_ids as $po_id) {
                    if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
                }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>')">
                <td width="40"><? echo $j; ?>p</td>
                <td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
                <td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                <td><p><? echo $row[csf('po_job_no')]; ?>&nbsp;</p></td>
            </tr>
            <?
            $j++;
        }
        ?>
    </table>
    </div>
    <?
    exit();
}


if($action=="generate_report") // Show
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    $cbo_company=str_replace("'","",$cbo_company_id);
    $txt_sales_order_no=str_replace("'","",$txt_sales_order_no);
    $hide_job_id=str_replace("'","",$hide_job_id);
    $txt_booking_no=str_replace("'","",$txt_booking_no);
    $date_from=str_replace("'","",$from_date);
    $date_to=str_replace("'","",$to_date);
    
    if($db_type==0)
    {
        if($date_from!="") $date_from=change_date_format($date_from,'yyyy-mm-dd');
        if($date_to!="") $date_to=change_date_format($date_to,'yyyy-mm-dd');
    }
    else
    {
        if($date_from!="") $date_from=change_date_format($date_from,'','',1);
        if($date_to!="") $date_to=change_date_format($date_to,'','',1);
    }
    //echo $date_from."==".$date_to;die;

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $buyer_name_arr = return_library_array( "select style_ref_no, buyer_name from wo_po_details_master",'style_ref_no','buyer_name');
    $booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
    $supplier_arr=return_library_array( "select id,short_name from lib_supplier where status_active =1",'id','short_name');
    $company_sql = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_sql as  $val) 
	{
		$company_array[$val[csf("id")]] = $val[csf("company_name")];
		$company_short_array[$val[csf("id")]] = $val[csf("company_short_name")];
	}
    
    
    if($hide_job_id == ""){
        $sales_order_cond = ($txt_sales_order_no != "") ? " and  c.job_no like '%".trim($txt_sales_order_no)."%'" : "";
    }else{
        $sales_order_cond = " and c.id in($hide_job_id)";
    }
    // echo $sales_order_cond;die;
    $sql_cond="";    
    // if($txt_sales_order_no!="") $sql_cond.=" and  c.job_no like '%".trim($txt_sales_order_no)."%'";
    if($txt_booking_no!="") $sql_cond.=" and  c.sales_booking_no like '%".trim($txt_booking_no)."%'";
    if($date_from!="" && $date_to!="") $sql_cond.=" and  a.transfer_date between '$date_from' and '$date_to'";


    ob_start(); 
    //echo "tofa";die;
    ?>
    <div>
    <table width="2280" cellpadding="0" cellspacing="0" id="caption" align="center">
        <tr>
           <td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:22px"><? echo $company_library[$cbo_company]; ?></strong></td>
        </tr> 
        <tr>  
           <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:16px">Roll wise Grey Sales Order To Sales Order Transfer Status-FSO</strong></td>
        </tr>         
    </table>
    <br />
    <table width="2280" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
        <thead>
            <tr>
                <th width="35" rowspan="2">SL</th>
                <th colspan="10"><strong>From Roll wise Grey Sales Order Transfer</strong></th>
                <th width="20"></th>
                <th colspan="10"><strong>To Roll wise Grey Sales Order Transfer</strong></th>
            </tr>
            <tr>
                <th width="100">Party</th>
                <th width="80">W/G</th>
                <th width="100">FSO No.</th>
                <th width="100"> Booking No</th>
                <th width="80">Booking Type</th>
                <th width="90">Buyer</th>
                <th width="120">Style Ref</th>
                <th width="80">Stock Qty.(kg)</th>

                <th width="80">Transfer Grey</th>
                <th width="80">Balance Qty.(kg)</th>

                <th width="20"></th>

                <th width="100">Party</th>
                <th width="80">W/G</th>
                <th width="100">FSO No.</th>
                <th width="100"> Booking No</th>
                <th width="80">Booking Type</th>

                <th width="90">Buyer</th>
                <th width="120">Style Ref</th>
                <th width="80">Stock Qty.(kg)</th>
                <th width="80">Transfer Grey</th>
                <th width="80">Balance Qty.(kg)</th>
            </tr>
        </thead>
    </table>
    <div style="width:2300px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2280" class="rpt_table" id="tbl_sales_order_transfer" >
            <?
               
            // $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, 
            // sum( case when b.transaction_type=6 then b.cons_quantity else 0 end) as from_order_qnty, 
            // sum( case when b.transaction_type=5 then b.cons_quantity else 0 end) as to_order_qnty, c.buyer_id, c.job_no as sales_order_no, c.style_ref_no, c.sales_booking_no, c.within_group, c.booking_type, c.booking_without_order, c.booking_entry_form,c.company_id,c.po_company_id,b.prod_id
            // from inv_item_transfer_mst a, inv_transaction b, fabric_sales_order_mst c
            // where a.id=b.mst_id and a.from_order_id=c.id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$cbo_company and a.transfer_date between '$date_from' and '$date_to' $job_order_cond and a.entry_form=133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
            // group by a.id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, c.buyer_id, c.job_no , c.style_ref_no, c.sales_booking_no, c.within_group, c.booking_type, c.booking_without_order, c.booking_entry_form,c.company_id,c.po_company_id,b.prod_id";

            $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id,b.transaction_type,b.cons_quantity, c.buyer_id, c.job_no as sales_order_no, c.style_ref_no, c.sales_booking_no, c.within_group, c.booking_type, c.booking_without_order, c.booking_entry_form,c.company_id,c.po_company_id,b.prod_id
            from inv_item_transfer_mst a, inv_transaction b, fabric_sales_order_mst c
            where a.id=b.mst_id and a.from_order_id=c.id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$cbo_company $sql_cond $sales_order_cond and a.entry_form=133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
           
            // echo $sql_transfer;//die;
            $sql_transfer_result=sql_select($sql_transfer);
            $to_order_arr=array();
            $trnsfer_arr=array();
            // $from_to_order_arr=array();
            $trnsfer_sql_arr=array();
            $from_to_order_id=array();
          
            foreach($sql_transfer_result as $rows)
            {
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['sales_order_no']=$rows[csf('sales_order_no')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['within_group']=$rows[csf('within_group')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['po_company_id']=$rows[csf('po_company_id')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['company_id']=$rows[csf('company_id')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['from_order_id']=$rows[csf('from_order_id')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['to_order_id']=$rows[csf('to_order_id')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['style_ref_no']=$rows[csf('style_ref_no')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['sales_booking_no']=$rows[csf('sales_booking_no')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['booking_type']=$rows[csf('booking_type')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['booking_without_order']=$rows[csf('booking_without_order')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['booking_entry_form']=$rows[csf('booking_entry_form')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['prod_id']=$rows[csf('prod_id')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['transaction_type']=$rows[csf('transaction_type')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['transfer_date']=$rows[csf('transfer_date')];
                $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['mst_id'].=$rows[csf('mst_id')].",";
               
                if($rows[csf('transaction_type')]==6)
                {
                    $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['from_order_qnty']+=$rows[csf('cons_quantity')];
                }
                if($rows[csf('transaction_type')]==5)
                {
                    $trnsfer_sql_arr[$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['to_order_qnty']+=$rows[csf('cons_quantity')];
                }

                $to_order_arr[$rows[csf('to_order_id')]]=$rows[csf('to_order_id')];
               
                $trnsfer_arr[$rows[csf('mst_id')]]=$rows[csf('mst_id')];
              
              
                $from_to_order_id[$rows[csf('from_order_id')]]=$rows[csf('from_order_id')];
                $from_to_order_id[$rows[csf('to_order_id')]]=$rows[csf('to_order_id')];
            }

            
            $poBreakdownIdCond = where_con_using_array($from_to_order_id,0,"e.po_breakdown_id");
          

            // var_dump($from_to_order_arr);
            if(!empty($to_order_arr))
            {
                $all_to_order_cond = where_con_using_array($to_order_arr,0,"id");
                
           
                $to_order_info="SELECT id, buyer_id, job_no as sales_order_no, style_ref_no, sales_booking_no, within_group,booking_type, booking_without_order, booking_entry_form, company_id, po_company_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 $all_to_order_cond";
                //echo $to_order_info;
                $to_order_data_arr=array();
                $order_info_result=sql_select($to_order_info);
                foreach ($order_info_result as $key => $value) 
                {
                    if ($value[csf("within_group")]==1) 
                    {
                        $to_order_data_arr[$value[csf("id")]]['buyer_id']=$company_library[$value[csf("buyer_id")]];
                        $to_order_data_arr[$value[csf("id")]]['company_id']=$company_short_array[$value[csf("po_company_id")]];
                    }
                    else
                    {
                        $to_order_data_arr[$value[csf("id")]]['buyer_id']=$buyer_arr[$value[csf("buyer_id")]];
                        $to_order_data_arr[$value[csf("id")]]['company_id']=$company_short_array[$value[csf("company_id")]];
                    }

                    $bookingType="";
                    if($value[csf('booking_type')] == 4)
                    {
                        if($value[csf('booking_without_order')] == 1)
                        {
                            $bookingType = "Sample Without Order";
                        }
                        else
                        {
                            $bookingType =  "Sample With Order";
                        }
                    }
                    else
                    {
                        $bookingType =  $booking_type_arr[$value[csf('booking_entry_form')]];
                    }
                    $to_order_data_arr[$value[csf("id")]]['booking_type'] = $bookingType;

                    $to_order_data_arr[$value[csf("id")]]['sales_order_no']=$value[csf("sales_order_no")];
                    $to_order_data_arr[$value[csf("id")]]['style_ref_no']=$value[csf("style_ref_no")];
                    $to_order_data_arr[$value[csf("id")]]['sales_booking_no']=$value[csf("sales_booking_no")];
                    $to_order_data_arr[$value[csf("id")]]['within_group']=$value[csf("within_group")];

                }
            }

            $sqlRcvRollQty = "
                SELECT
                    d.company_id,e.prod_id, e.po_breakdown_id, e.entry_form, SUM(h.qnty) AS rcv_qty, d.sales_booking_no
                FROM
                    fabric_sales_order_mst d
                    INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
                    INNER JOIN inv_transaction f ON e.trans_id = f.id
                    INNER JOIN pro_grey_prod_entry_dtls g ON f.id = g.trans_id
                    LEFT JOIN pro_roll_details h ON g.id = h.dtls_id
                WHERE
                    e.status_active = 1
                    AND e.is_deleted = 0
                    AND e.entry_form IN(2,22,58,84)
                    AND e.trans_type IN(1,4)
                    AND e.trans_id > 0
                    AND f.status_active = 1
                    AND f.is_deleted = 0
                    AND d.company_id IN(".$cbo_company.")
                    ".$poBreakdownIdCond."
                    AND h.entry_form IN(2,22,58,84) and h.is_sales=1
                GROUP BY 
                    d.company_id, e.prod_id, e.po_breakdown_id, e.entry_form, d.sales_booking_no
            ";	
            //echo $sqlRcvRollQty; //die;
            $sqlRcvRollRslt = sql_select($sqlRcvRollQty);
            $dataArr = array();
            $poArr = array();
            foreach($sqlRcvRollRslt as $row)
            {
                $compId = $row[csf('company_id')];
                $productId = $row[csf('prod_id')];
                $orderId = $row[csf('po_breakdown_id')];
                $sales_booking_no = $row[csf('sales_booking_no')];
                $poArr[$orderId] = $orderId;
                if($row[csf('entry_form')]  == 84)
                {
                    $issueReturnQty[$productId][$sales_booking_no]['issueReturnQty'] += $row[csf('rcv_qty')];
                }
                else
                {
                    $dataArr[$productId][$sales_booking_no]['rcvQty'] += $row[csf('rcv_qty')];
                }
            }


            $sqlNoOfRoll="
            
                SELECT
                    d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, SUM(e.quantity) AS rcv_qty, d.sales_booking_no
                FROM 
                    fabric_sales_order_mst d
                    INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
                    INNER JOIN inv_transaction f ON e.trans_id = f.id 
                    INNER JOIN inv_item_transfer_dtls g ON e.dtls_id = g.id 
                WHERE
                    e.status_active = 1 
                    AND e.is_deleted = 0 
                    AND e.entry_form IN(133) 
                    AND e.trans_type IN(5,6) 
                    AND f.status_active = 1 
                    AND f.is_deleted = 0 
                    AND g.status_active = 1 
                    AND g.is_deleted = 0	
                    AND d.company_id IN(".$cbo_company.")
                    ".$poBreakdownIdCond."
                GROUP BY 
                    d.company_id, e.prod_id, e.po_breakdown_id, e.trans_type, d.sales_booking_no
               
            ";

            // echo "<br>".$sqlNoOfRoll; //die;
            $sqlNoOfRollResult = sql_select($sqlNoOfRoll);
            foreach($sqlNoOfRollResult as $row)
            {
                $compId = $row[csf('company_id')];
                $productId = $row[csf('prod_id')];
                $orderId = $row[csf('po_breakdown_id')];
                $sales_booking_no = $row[csf('sales_booking_no')];
                $from_prod_id = $row[csf('from_prod_id')];

                if($row[csf('trans_type')] == 5)
                {
                    $transinArr[$productId][$sales_booking_no]['transferInQty'] += $row[csf('rcv_qty')];
                }
                if($row[csf('trans_type')] == 6)
                {
                    $transOutArr[$productId][$sales_booking_no]['transferOutQty'] += $row[csf('rcv_qty')];
                }
            }
            //print_r($dataArr);

            $sqlNoOfRollIssue="
                
                SELECT
                    d.company_id,
                    e.prod_id, e.po_breakdown_id, SUM(e.quantity) AS issue_qty, d.sales_booking_no
                FROM
                    fabric_sales_order_mst d
                    INNER JOIN order_wise_pro_details e ON d.id = e.po_breakdown_id
                    INNER JOIN inv_transaction f ON e.trans_id = f.id
                    INNER JOIN pro_roll_details g ON e.dtls_id = g.dtls_id
                WHERE
                    e.status_active = 1
                    AND e.is_deleted = 0
                    AND e.entry_form IN(61)
                    AND e.trans_type = 2
                    AND f.status_active = 1
                    AND f.is_deleted = 0
                    AND g.status_active = 1
                    AND g.is_deleted = 0
                    AND g.entry_form IN(61) and g.is_sales=1
                    AND d.company_id IN(".$cbo_company.")
                    ".$poBreakdownIdCond."
                    
                GROUP BY 
                    d.company_id,
                    e.prod_id, e.po_breakdown_id, d.sales_booking_no
            ";
          
            //echo $sqlNoOfRollIssue; //die;
            $sqlNoOfRollIssueResult = sql_select($sqlNoOfRollIssue);
            foreach($sqlNoOfRollIssueResult as $row)
            {
                $compId = $row[csf('company_id')];
                $productId = $row[csf('prod_id')];
                $orderId = $row[csf('po_breakdown_id')];
                $sales_booking_no = $row[csf('sales_booking_no')];
                // echo $productId.'='.$sales_booking_no.'<br>';
                $issueQtyArr[$productId][$sales_booking_no]['issueQty'] += $row[csf('issue_qty')];
            }
          

            $i=1;            

            foreach($trnsfer_sql_arr as $from_order=>$from_order_id)
            {
                foreach($from_order_id as $to_order=>$row)
                {
                    //var_dump($to_order);
                    $mst_ids =implode(",",array_unique(explode(",",chop($row['mst_id'],","))));

                  
                    $from_rcvQty         = $dataArr[$row['prod_id']][$row['sales_booking_no']]['rcvQty'];
                    $from_issueReturnQty = $issueReturnQty[$row['prod_id']][$row['sales_booking_no']]['issueReturnQty'];
                   
                    $from_transferInQty  =  $transinArr[$row['prod_id']][$to_order_data_arr[$row["to_order_id"]]['sales_booking_no']]['transferInQty'];
                    
                    $from_totalRcvQty = number_format($from_rcvQty,2,'.','')+number_format($from_issueReturnQty,2,'.','')+number_format($from_transferInQty,2,'.','');

                    $from_issueQty = $issueQtyArr[$row['prod_id']][$row['sales_booking_no']]['issueQty'];
                    $from_rcvReturnQty = 0;
                    $from_transferOutQty = $transOutArr[$row['prod_id']][$row['sales_booking_no']]['transferOutQty'];
                    $from_totalIssueQty = number_format($from_issueQty,2,'.','')+number_format($from_rcvReturnQty,2,'.','')+number_format($from_transferOutQty,2,'.','');

                    //echo $from_rcvQty."=".$from_issueReturnQty."=".$from_transferInQty."<br>";
                    //stock qty calculation
                    $from_stockQty =  $from_totalRcvQty-$from_totalIssueQty;

                    // To order
                    $to_rcvQty         = $dataArr[$row['prod_id']][$to_order_data_arr[$row["to_order_id"]]['sales_booking_no']]['rcvQty'];
                    $to_issueReturnQty = $issueReturnQty[$row['prod_id']][$to_order_data_arr[$row["to_order_id"]]['sales_booking_no']]['issueReturnQty'];
                   
                    // $to_transferInQty  = $transinArr[$row['prod_id']][$row['sales_booking_no']]['transferInQty'];
                    $to_transferInQty  = $transinArr[$row['prod_id']][$to_order_data_arr[$row["to_order_id"]]['sales_booking_no']]['transferInQty'];
                    
                    // echo number_format($to_rcvQty,2,'.','').'+'.number_format($to_issueReturnQty,2,'.','').'+'.number_format($to_transferInQty,2,'.','').'<br>';
                    $to_totalRcvQty = number_format($to_rcvQty,2,'.','')+number_format($to_issueReturnQty,2,'.','')+number_format($to_transferInQty,2,'.','');

                    // echo $row['prod_id'].'*'.$to_order_data_arr[$row["to_order_id"]]['sales_booking_no'].'<br>';
                    $to_issueQty = $issueQtyArr[$row['prod_id']][$to_order_data_arr[$row["to_order_id"]]['sales_booking_no']]['issueQty'];
                    $to_rcvReturnQty = 0;
                    $to_transferOutQty = $transOutArr[$row['prod_id']][$to_order_data_arr[$row["to_order_id"]]['sales_booking_no']]['transferOutQty'];


                    // echo number_format($to_issueQty,2,'.','').'+'.number_format($to_rcvReturnQty,2,'.','').'+'.number_format($to_transferOutQty,2,'.','').'<br>';
                    $to_totalIssueQty = number_format($to_issueQty,2,'.','')+number_format($to_rcvReturnQty,2,'.','')+number_format($to_transferOutQty,2,'.','');
                    //echo $to_issueQty."=".$to_rcvReturnQty."=".$to_transferOutQty."<br>";
                    //stock qty calculation
                    // echo $to_totalRcvQty.'-'.$to_totalIssueQty.'<br>';
                    $to_stockQty = $to_totalRcvQty-$to_totalIssueQty;
                    //$to_stockQty = $to_totalRcvQty;

                    $bookingType="";
                    if($row['booking_type'] == 4)
                    {
                        if($row['booking_without_order'] == 1)
                        {
                            $bookingType = "Sample Without Order";
                        }
                        else
                        {
                            $bookingType =  "Sample With Order";
                        }
                    }
                    else
                    {
                        $bookingType =  $booking_type_arr[$row['booking_entry_form']];
                    }

                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><p><? echo $i; ?></p></td>
                        <td width="100"><?
                            if($row["within_group"]==2)
                            {
                            echo $party= $company_short_array[$row["company_id"]];
                            }else{
                                echo $party= $company_short_array[$row["po_company_id"]];
                            }
                        ?> </td>
                    
                        <td width="80"><? echo $yes_no[$row["within_group"]]; ?></td>
                        <td width="100" title="<? echo $row["from_order_id"]; ?>"><? echo $row["sales_order_no"]; ?></td>
                        <td width="100" > <? echo $row["sales_booking_no"]; ?> </td>
                        <td width="80"> <?  echo $bookingType;  ?></td>
                        <td width="90"><? echo $buyer_arr[$buyer_name_arr[$row["style_ref_no"]]]; ?></td>
                        <td width="120"><? echo $row["style_ref_no"]; ?></td>
                        <td width="80" align="right"><? echo number_format($from_stockQty,2);?></td>
                        <td width="80" align="right"><? $fromOrderQnty = $row["from_order_qnty"]; echo number_format($fromOrderQnty,2); ?></td>
                        <td width="80" align="right"><? $f_balance_qty = $from_stockQty-$fromOrderQnty;echo number_format($f_balance_qty,2);?></td>

                        <td width="20"></td>
                        <td width="100"><? echo $party = $to_order_data_arr[$row["to_order_id"]]['company_id']; ?></td>
                        <td width="80"><? echo $yes_no[$to_order_data_arr[$row["to_order_id"]]['within_group']]; ?></td>

                        <td width="100" title="<? echo $row["to_order_id"]; ?>"><? echo $to_order_data_arr[$row["to_order_id"]]['sales_order_no']; ?></td>
                        <td width="100" title="<?echo $row['prod_id'];?>"><? echo $to_order_data_arr[$row["to_order_id"]]['sales_booking_no']; ?></td>
                        <td width="80"><? echo $to_order_data_arr[$row["to_order_id"]]['booking_type']; ?></td>

                        <td width="90"> <? echo $buyer_arr[$buyer_name_arr[$to_order_data_arr[$row["to_order_id"]]['style_ref_no']]]; ?></td>
                        <td width="120"><? echo $to_order_data_arr[$row["to_order_id"]]['style_ref_no']; ?></td>
                        <td width="80" align="right"><? echo number_format($to_stockQty,2);?></td>
                        <td width="80" align="right">
                        <a href="##" onclick="open_mypage_fso('<? echo $cbo_company;?>','<? echo $mst_ids;?>')"> <? $toOrderQnty = $row["to_order_qnty"]; echo number_format($toOrderQnty,2); ?></a>
                           </td>
                        <td width="80" align="right"><? $to_balance_qty = $to_stockQty;echo number_format($to_balance_qty,2);?></td>
                    </tr>
                    <?
                    $tot_from_stockQty += $from_stockQty;
                    $tot_f_balance_qty += $f_balance_qty;
                    $from_order_qnty += $fromOrderQnty;
                    
                    $tot_to_stockQty += $to_stockQty;
                    $to_order_qnty  += $toOrderQnty;
                    $tot_to_balance_qty += $to_balance_qty;
                    $i++;
                }
            }
            ?>
            </table>
            <table width="2280 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                <tfoot>
                    <th width="35">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100"> &nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="120">Total : </th>
                    <th width="80" id="tot_from_stockQty"><? echo number_format($tot_from_stockQty,2); ?></th>
                    <th width="80" id="from_order_qnty"><? echo number_format($from_order_qnty,2); ?></th>
                    <th width="80" id="tot_f_balance_qty"><? echo number_format($tot_f_balance_qty,2); ?></th>
                    <th width="20">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100"> &nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="80" id="tot_to_stockQty"><? echo number_format($tot_to_stockQty,2); ?></th>
                    <th width="80" id="to_order_qnty"><? echo number_format($to_order_qnty,2); ?></th>
                    <th width="80" id="tot_to_balance_qty"><? echo number_format($tot_to_balance_qty,2); ?></th>
                </tfoot>
            </table>
        </div>
        </div>
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
    echo "$total_data****$filename";
  
	disconnect($con);
    exit();  
}

if($action=="generate_report2") // Show 2
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    $cbo_company=str_replace("'","",$cbo_company_id);
    $txt_sales_order_no=str_replace("'","",$txt_sales_order_no);
    $hide_job_id=str_replace("'","",$hide_job_id);
    $txt_booking_no=str_replace("'","",$txt_booking_no);
    $date_from=str_replace("'","",$from_date);
    $date_to=str_replace("'","",$to_date);
    
    if($db_type==0)
    {
        if($date_from!="") $date_from=change_date_format($date_from,'yyyy-mm-dd');
        if($date_to!="") $date_to=change_date_format($date_to,'yyyy-mm-dd');
    }
    else
    {
        if($date_from!="") $date_from=change_date_format($date_from,'','',1);
        if($date_to!="") $date_to=change_date_format($date_to,'','',1);
    }

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    //$buyer_name_arr = return_library_array( "select style_ref_no, buyer_name from wo_po_details_master",'style_ref_no','buyer_name');
    $company_sql = sql_select("select id, company_name, company_short_name from lib_company");
    foreach ($company_sql as  $val) 
    {
        $company_array[$val[csf("id")]] = $val[csf("company_name")];
        $company_short_array[$val[csf("id")]] = $val[csf("company_short_name")];
    }    
    
    /*if($hide_job_id == ""){
        $sales_order_cond = ($txt_sales_order_no != "") ? " and  d.job_no like '%".trim($txt_sales_order_no)."%'" : "";
    }else{
        $sales_order_cond = " and d.id in($hide_job_id)";
    }*/
    if($txt_sales_order_no!="") $sales_order_cond.=" and d.job_no = '$txt_sales_order_no'";

    $sql_cond="";    
    if($txt_booking_no!="") $sql_cond.=" and  d.sales_booking_no like '%".trim($txt_booking_no)."%'";
    if($date_from!="" && $date_to!="") $sql_cond.=" and  b.transaction_date between '$date_from' and '$date_to'";

    /*$sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id,b.transaction_type,b.cons_quantity, c.buyer_id, c.job_no as sales_order_no, c.style_ref_no, c.sales_booking_no, c.within_group, c.booking_type, c.booking_without_order, c.booking_entry_form,c.company_id,c.po_company_id,b.prod_id, c.po_buyer
    from inv_item_transfer_mst a, inv_transaction b, fabric_sales_order_mst c
    where a.id=b.mst_id and a.from_order_id=c.id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$cbo_company $sql_cond $sales_order_cond and a.entry_form=133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/

    $con = connect();
    $r_id=execute_query("delete from tmp_barcode_no where userid=$user_id");
    // $r_id2=execute_query("delete from tmp_po_id where user_id=$user_id");
    $r_id2=execute_query("delete from TMP_POID where USERID=$user_id");
    oci_commit($con);

    $sql_transfer="SELECT a.from_order_id, a.to_order_id, b.transaction_type, b.cons_quantity, b.prod_id, c.po_breakdown_id, d.buyer_id, d.job_no as sales_order_no, d.style_ref_no, d.sales_booking_no, d.within_group, d.booking_type, d.booking_without_order, d.booking_entry_form, d.company_id, d.po_company_id, d.po_buyer, d.booking_id, e.qnty, e.barcode_no
    from inv_item_transfer_mst a, inv_transaction b, order_wise_pro_details c, fabric_sales_order_mst d, pro_roll_details e
    where a.id=b.mst_id and a.id=e.mst_id and a.from_order_id=c.po_breakdown_id and b.id=c.trans_id and c.po_breakdown_id=d.id and c.entry_form=133 and c.trans_type in(5,6) and b.item_category=13 and b.transaction_type in(5,6) and b.company_id=$cbo_company $sql_cond $sales_order_cond
    and b.mst_id = e.mst_id and c.dtls_id=e.dtls_id and a.entry_form=133 and e.entry_form=133 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
               
    //echo $sql_transfer;//die;// and b.mst_id=7991
    $sql_transfer_result=sql_select($sql_transfer);

    $barcode_no_check =array();
    if(!empty($sql_transfer_result))
    {
        foreach ($sql_transfer_result as $row)
        {
            // if( !in_array($row[csf('barcode_no')], $barcode_no_check))
            if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
            {
                $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
                $barcodeno = $row[csf('barcode_no')];
                // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
                $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form) values ($user_id,$barcodeno,133)");
                $barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
            }            

            if ($po_id_check2[$row[csf('from_order_id')]] == "")
            {
                $po_id_check2[$row[csf('from_order_id')]]=$row[csf('from_order_id')];
                $po_id = $row[csf('from_order_id')];
                // echo "insert into tmp_poid (userid, po_id) values ($user_id,$po_id)";
                $r_id2=execute_query("insert into TMP_POID (userid, poid, type) values ($user_id, $po_id, 6)");
                $po_id_arr[$row[csf("from_order_id")]]=$row[csf("from_order_id")];
            }

            if ($po_id_check[$row[csf('to_order_id')]] == "")
            {
                $po_id_check[$row[csf('to_order_id')]]=$row[csf('to_order_id')];
                $po_id = $row[csf('to_order_id')];
                // echo "insert into TMP_POID (USERID, po_id) values ($user_id,$po_id)";
                $r_id2=execute_query("insert into TMP_POID (userid, poid, type) values ($user_id, $po_id, 5)");
                $po_id_arr[$row[csf("to_order_id")]]=$row[csf("to_order_id")];
            }
        }
        oci_commit($con);
    }
    else
    {
        echo "Data Not Found";
        die;
    }

    $barcodeArr = array_filter($barcodeArr);
    if(count($barcodeArr ) >0 ) // production
    {
        $production_sql = sql_select("SELECT b.barcode_no,a.color_range_id,a.yarn_lot, a.yarn_count,b.po_breakdown_id, a.prod_id, b.booking_no,c.booking_id, b.receive_basis, a.color_id, a.febric_description_id, a.gsm, a.width, a.stitch_length, a.machine_dia, a.machine_gg,a.machine_no_id, c.knitting_source, c.challan_no as production_challan, c.knitting_company, a.yarn_prod_id, a.body_part_id, b.coller_cuff_size
        from pro_grey_prod_entry_dtls a, pro_roll_details b, inv_receive_master c, tmp_barcode_no d 
        where a.id=b.dtls_id and a.mst_id = c.id and c.entry_form in(2,58) and b.entry_form in(2,58)  and a.status_active=1 and b.status_active=1 and b.barcode_no = d.barcode_no and d.userid= $user_id and d.entry_form=133");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["receive_basis"] =$row[csf("receive_basis")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prog_book"] =$row[csf("booking_no")];
            $prodBarcodeData[$row[csf('barcode_no')]]["booking_id"]=$row[csf('booking_id')];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_range_id"] =$row[csf("color_range_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_lot"] =$row[csf("yarn_lot")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_count"] =$row[csf("yarn_count")];
            $prodBarcodeData[$row[csf("barcode_no")]]["yarn_prod_id"] =$row[csf("yarn_prod_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_id"] =$row[csf("prod_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["color_id"] =$row[csf("color_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["febric_description_id"] =$row[csf("febric_description_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] =$row[csf("gsm")];
            $prodBarcodeData[$row[csf("barcode_no")]]["width"] =$row[csf("width")];
            $prodBarcodeData[$row[csf("barcode_no")]]["stitch_length"] =$row[csf("stitch_length")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_dia"] =$row[csf("machine_dia")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_gg"] =$row[csf("machine_gg")];
            $prodBarcodeData[$row[csf("barcode_no")]]["item_size"] =$row[csf("coller_cuff_size")];
            $prodBarcodeData[$row[csf("barcode_no")]]["machine_no_id"] =$row[csf("machine_no_id")];
            $prodBarcodeData[$row[csf("barcode_no")]]["prod_challan"] =$row[csf("production_challan")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_source"] =$row[csf("knitting_source")];
            $prodBarcodeData[$row[csf("barcode_no")]]["knitting_company"] =$row[csf("knitting_company")];
            $prodBarcodeData[$row[csf("barcode_no")]]["body_part_id"] =$row[csf("body_part_id")];
            $allDeterArr[$row[csf("febric_description_id")]] =$row[csf("febric_description_id")];
            $allColorArr[$row[csf("color_id")]] =$row[csf("color_id")];
            $allYarnProdArr[$row[csf("yarn_prod_id")]]= $row[csf("yarn_prod_id")];
        }
    }

    $sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id 
    from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b 
    where a.id=b.mst_id $ref_febric_description_cond and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
    $deter_array=sql_select($sql_deter);
    if(count($deter_array)>0)
    {
        foreach($deter_array as $row )
        {
            if(array_key_exists($row[csf('id')],$composition_arr))
            {
                $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
            }
            else
            {
                $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
            }

            $constuction_arr[$row[csf('id')]]=$row[csf('construction')];

            if($row[csf('type_id')]>0)
            {
                $type_array[$row[csf('id')]].=$yarn_type[$row[csf('type_id')]].",";
            }
        }
    }
    unset($deter_array);

    $booking_status_sql_arr=array(); $transfer_status_sql_arr=array(); $to_order_count=array();
     $from_total_order_trans_qnty_arr=array();
    foreach($sql_transfer_result as $rows) // Transfer Status array prepare
    {
        $body_part_id=$prodBarcodeData[$rows[csf("barcode_no")]]["body_part_id"];
        $febric_detar_id=$prodBarcodeData[$rows[csf("barcode_no")]]["febric_description_id"];
        $gsm=$prodBarcodeData[$rows[csf("barcode_no")]]["gsm"];
        $dia=$prodBarcodeData[$rows[csf("barcode_no")]]["width"];
        $str_ref=$body_part_id.'*'.$febric_detar_id.'*'.$gsm.'*'.$dia;
        if($rows[csf('transaction_type')]==6) // Out, Booking Status
        {
            $from_total_order_trans_qnty_arr[$rows[csf('po_breakdown_id')]]+=$rows[csf('qnty')];
        }
        
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['sales_order_no']=$rows[csf('sales_order_no')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['within_group']=$rows[csf('within_group')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['po_company_id']=$rows[csf('po_company_id')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['from_order_id']=$rows[csf('from_order_id')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['sales_booking_no']=$rows[csf('sales_booking_no')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['booking_without_order']=$rows[csf('booking_without_order')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['booking_entry_form']=$rows[csf('booking_entry_form')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['prod_id']=$rows[csf('prod_id')];
        $transfer_status_sql_arr[$rows[csf('to_order_id')]][$str_ref]['to_order_qnty']+=$rows[csf('qnty')];
        $to_order_id[$rows[csf('to_order_id')]]=$rows[csf('to_order_id')];
        // echo $rows[csf('to_order_id')].'<br>';
        // $to_order_count[$rows[csf('to_order_id')]]++;
        $to_total_order_qnty_arr[$rows[csf('to_order_id')]]+=$rows[csf('qnty')];
    }
    // echo '<pre>';print_r($to_order_count);
    // echo '<pre>';print_r($transfer_status_sql_arr);

    if(!empty($po_id_arr)) // Booking Status data prepare
    {
        $fso_sql="SELECT t.type, a.id, a.sales_booking_no, a.job_no as from_fso_no, a.po_buyer, a.customer_buyer, b.body_part_id, b.determination_id, b.gsm_weight, b.dia,  b.color_type_id, b.order_uom, b.pre_cost_remarks, b.grey_qty
        from TMP_POID t, fabric_sales_order_mst a, fabric_sales_order_dtls b 
        where t.poid=a.id and a.id=b.mst_id and t.poid=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.userid=$user_id and t.type in(5,6)";
        // echo $fso_sql;
        $fso_order_data_arr=array();
        $fso_result=sql_select($fso_sql);$from_total_order_qnty_arr=array();
        foreach ($fso_result as $key => $rows) 
        {
            $fso_order_data_arr[$rows[csf("id")]]['color_type_id'].=$rows[csf("color_type_id")].',';
            $fso_order_data_arr[$rows[csf("id")]]['uom'].=$unit_of_measurement[$rows[csf("order_uom")]].',';
            $fso_order_data_arr[$rows[csf("id")]]['remarks'].=$rows[csf("pre_cost_remarks")].',';
            $fso_order_data_arr[$rows[csf("id")]]['sales_booking_no']=$rows[csf("sales_booking_no")];
            $fso_order_data_arr[$rows[csf("id")]]['fso_no']=$rows[csf("from_fso_no")];
            $fso_order_data_arr[$rows[csf("id")]]['customer_buyer']=$rows[csf("customer_buyer")];

            if ($rows[csf('type')]==6) 
            {
                $str_ref2=$rows[csf('body_part_id')].'*'.$rows[csf('determination_id')].'*'.$rows[csf('gsm_weight')].'*'.$rows[csf('dia')].'*'.$rows[csf('color_type_id')];

                $booking_status_arr[$rows[csf('id')]][$str_ref2]['sales_order_no']=$rows[csf('from_fso_no')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['sales_booking_no']=$rows[csf('sales_booking_no')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['booking_without_order']=$rows[csf('booking_without_order')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['booking_entry_form']=$rows[csf('booking_entry_form')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['prod_id']=$rows[csf('prod_id')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['type']=$rows[csf('type')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['po_buyer']=$rows[csf('po_buyer')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['remarks']=$rows[csf('pre_cost_remarks')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['uom']=$rows[csf('order_uom')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['customer_buyer']=$rows[csf('customer_buyer')];
                $booking_status_arr[$rows[csf('id')]][$str_ref2]['grey_qty']+=$rows[csf('grey_qty')];
                
                $from_order_id[$rows[csf('id')]]=$rows[csf('id')];
                $from_order_count[$rows[csf('id')]]++;
                $from_total_order_qnty_arr[$rows[csf('id')]]+=$rows[csf('grey_qty')];
            }
        }
    }

    // echo '<pre>';print_r($from_total_order_qnty_arr);


    $from_order_count1=array();
    foreach($booking_status_arr as $from_order=>$from_order_data)
    {
        foreach($from_order_data as $str_ref1=>$row)
        {
            $from_order_count1[$from_order]++;
        }
    }
    // echo '<pre>';print_r($from_order_count1);

    $to_order_count2=array();
    foreach($transfer_status_sql_arr as $to_order=>$to_order_data)
    {
        foreach($to_order_data as $str_ref2=>$row)
        {
            $to_order_count2[$to_order]++;
        }
    }
    // echo '<pre>';print_r($to_order_count2);

    $con = connect();
    $r_id=execute_query("delete from tmp_barcode_no where userid=$user_id");
    $r_id2=execute_query("delete from tmp_poid where userid=$user_id");
    oci_commit($con);

    ob_start();
    ?>
    <div>
        <table width="1415" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="16" class="form_caption"><strong style="font-size:22px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="16" class="form_caption" ><strong style="font-size:16px">Roll wise Grey Sales Order To Sales Order Transfer Status-FSO</strong></td>
            </tr>         
        </table>
        <br />
        <!--  Booking Status Start -->
        <p><strong>Booking Status</strong></p>
        <table width="1415" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
            <thead>
                <tr>
                    <th width="35">SL</th>
                    <th width="100">Buyer Name</th>
                    <th width="80">Booking No (from)</th>
                    <th width="100">Sales Order (from )</th>
                    <th width="100">Part</th>
                    <th width="200">Item Description</th>
                    <th width="60">GSM</th>
                    <th width="60">Dia</th>
                    <th width="80">Color Type</th>
                    <th width="80">UOM</th>
                    <th width="80">Qty</th>
                    <th width="100">Total</th>
                    <th width="100">Balance</th>
                    <th width="">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1435px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1415" class="rpt_table" id="tbl_booking_status" align="left" >
                <?
                $i=1;$from_rowspan=0;
                foreach($booking_status_arr as $from_order=>$from_order_data)
                {
                    foreach($from_order_data as $str_ref1=>$row)
                    {
                        $str_ref_arr1 = explode("*", $str_ref1);
                        $body_part_id=$str_ref_arr1[0];
                        $fabric_detar=$str_ref_arr1[1];
                        $gsm=$str_ref_arr1[2];
                        $dia=$str_ref_arr1[3];
                        $color_type_id=$str_ref_arr1[4];

                        $from_rowspan = $from_order_count1[$from_order];

                        $colory_type_arr = array_unique(array_filter(explode(",", $color_type_id)));
                        $colorType = "";
                        foreach ($colory_type_arr as $id)
                        {
                            $colorType .= $color_type[$id].",";
                        }
                        $colorType =implode(",",array_filter(array_unique(explode(",", $colorType))));

                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><p><? echo $i; ?></p></td>
                            <td width="100"><? echo $buyer_arr[$row["customer_buyer"]]; ?></td>

                            <?
                            if(!in_array($from_order,$from_order_chk1))
                            {
                                $from_order_chk1[]=$from_order;
                                ?>
                                <td width="80" rowspan="<? echo $from_rowspan ;?>" valign="middle"><? echo $row["sales_booking_no"]; ?></td>
                                <td width="100" rowspan="<? echo $from_rowspan ;?>" valign="middle" title="<? echo $from_order; ?>"><? echo $row["sales_order_no"]; ?></td>
                                <?
                            }
                            ?>
                            <td width="100"><? echo $body_part[$body_part_id]; ?></td>
                            <td width="200"><? echo $constuction_arr[$fabric_detar].', '.$composition_arr[$fabric_detar]; ?></td>
                            <td width="60"><? echo $gsm; ?></td>
                            <td width="60"><? echo $dia; ?></td>
                            <td width="80"><? echo $colorType; ?></td>
                            <td width="80"><? echo $unit_of_measurement[$row["uom"]]; ?></td>
                            <td width="80" align="right"><? echo number_format($row["grey_qty"],2); ?></td>

                            <?
                            $tot_from_order_qty=0;$tot_from_order_balance_qty=0;
                            if(!in_array($from_order,$from_order_chk))
                            {
                                $from_order_chk[]=$from_order;
                                ?>
                                <td width="100" align="right" rowspan="<? echo $from_rowspan ;?>" valign="middle"><? echo number_format($from_total_order_qnty_arr[$from_order],2); $tot_from_order_qty+=$from_total_order_qnty_arr[$from_order]; ?></td>

                                <td width="100" align="right" rowspan="<? echo $from_rowspan ;?>" valign="middle"><? echo number_format($from_total_order_qnty_arr[$from_order]-$from_total_order_trans_qnty_arr[$from_order],2); $tot_from_order_balance_qty+=$from_total_order_qnty_arr[$from_order]-$from_total_order_trans_qnty_arr[$from_order]; ?></td>
                            <?
                            }
                            ?>
                            
                            <td width=""><? echo $row["remarks"]; ?></td>
                        </tr>
                        <?
                        $from_order_qnty += $row["grey_qty"];
                        $tot_from_order_qty2 += $tot_from_order_qty;
                        $tot_from_order_balance_qty2 += $tot_from_order_balance_qty;
                        $i++;
                    }
                }
                ?>
            </table>
            <table width="1415 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left"> 
                <tfoot>
                    <th width="35">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100"> &nbsp;</th>
                    <th width="200">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60"></th>
                    <th width="80"></th>
                    <th width="80">Total : </th>
                    <th width="80" align="right"><? echo number_format($from_order_qnty,2); ?></th>
                    <th width="100" align="right"><? echo number_format($tot_from_order_qty2,2); ?></th>
                    <th width="100" align="right"><? echo number_format($tot_from_order_balance_qty2,2); ?></th>
                    <th width="">&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <!--  Booking Status End   -->

        <br />

        <!-- Transfer Status Start -->
        <p><strong>Transfer Status</strong></p>
        <table width="1235" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
            <thead>
                <tr>
                    <th width="35">SL</th>
                    <th width="100">Buyer Name</th>
                    <th width="80">Booking No (to)</th>
                    <th width="100">Sales Order (to )</th>
                    <th width="100">Part</th>
                    <th width="200">Item Description</th>
                    <th width="60">GSM</th>
                    <th width="60">Dia</th>
                    <th width="80">UOM</th>
                    <th width="80">Qty</th>
                    <th width="100">Transfer Qty</th>
                    <th width="">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1255px; max-height:300px; overflow-y:scroll; float: left;" id="scroll_body2" > 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1235" class="rpt_table" id="tbl_transfer_status"  align="left">
                <?
                $j=1;$tot_to_order_qty=0;$to_rowspan=0;
                foreach($transfer_status_sql_arr as $to_order=>$to_order_data)
                {
                    foreach($to_order_data as $str_ref2=>$row)
                    {
                        $str_ref_arr2 = explode("*", $str_ref2);
                        $body_part_id=$str_ref_arr2[0];
                        $fabric_detar=$str_ref_arr2[1];
                        $gsm=$str_ref_arr2[2];
                        $dia=$str_ref_arr2[3];

                        $to_rowspan = $to_order_count2[$to_order];
                        // echo $to_rowspan.'<br>';

                        $colory_type_arr = array_unique(array_filter(explode(",", $fso_order_data_arr[$to_order]['color_type_id'])));
                        $colorType = "";
                        foreach ($colory_type_arr as $id)
                        {
                            $colorType .= $color_type[$id].",";
                        }
                        $colorType =implode(",",array_filter(array_unique(explode(",", $colorType))));
                        $uom =implode(",",array_filter(array_unique(explode(",", $fso_order_data_arr[$to_order]['uom']))));                        
                        $remarks =implode(",",array_filter(array_unique(explode(",", $fso_order_data_arr[$to_order]['remarks']))));

                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr2_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $j; ?>">
                            <td width="35"><p><? echo $j; ?></p></td>
                            <td width="100"><? echo $buyer_arr[$fso_order_data_arr[$to_order]['customer_buyer']]; ?></td>

                            <?
                            if(!in_array($to_order,$to_order_chk1))
                            {
                                $to_order_chk1[]=$to_order;
                                ?>
                                <td width="80" rowspan="<? echo $to_rowspan ;?>" valign="middle"><? echo $fso_order_data_arr[$to_order]['sales_booking_no']; ?></td>
                                <td width="100" rowspan="<? echo $to_rowspan ;?>" valign="middle" title="<? echo $to_order; ?>"><? echo $fso_order_data_arr[$to_order]['fso_no']; ?></td>
                                <?
                            }
                            ?>

                            
                            <td width="100"><? echo $body_part[$body_part_id]; ?></td>
                            <td width="200"><? echo $constuction_arr[$fabric_detar].', '.$composition_arr[$fabric_detar]; ?></td>
                            <td width="60"><? echo $gsm; ?></td>
                            <td width="60"> <? echo $dia; ?></td>
                            <td width="80"><? echo $uom; ?></td>
                            <td width="80" align="right"><? echo number_format($row["to_order_qnty"],2); ?></td>

                            <?
                            $tot_to_order_qty=0;
                            if(!in_array($to_order,$to_order_chk))
                            {
                                $to_order_chk[]=$to_order;
                                ?>
                                <td width="100" align="right" rowspan="<? echo $to_rowspan ;?>" valign="middle"><? echo number_format($to_total_order_qnty_arr[$to_order],2); $tot_to_order_qty+=$to_total_order_qnty_arr[$to_order] ?></td>
                                <?
                            }
                            ?>
                            <td width=""><? echo $remarks; ?></td>
                        </tr>
                        <?
                        $to_order_qnty  += $row["to_order_qnty"];
                        $tot_to_order_qty2 += $tot_to_order_qty;
                        $j++;
                    }
                }
                ?>
            </table>
            <table width="1235 " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
                <tfoot>
                    <th width="35">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100"> &nbsp;</th>
                    <th width="200">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60"></th>
                    <th width="80">Total : </th>
                    <th width="80" align="right"><? echo number_format($to_order_qnty,2); ?></th>
                    <th width="100" align="right"><? echo number_format($tot_to_order_qty2,2); ?></th>
                    <th width="">&nbsp;</th>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- Transfer Status End -->
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
    echo "$total_data****$filename";
  
    disconnect($con);
    exit();  
}

if($action=="fso_dtls_popup")
{
	echo load_html_head_contents("FSO Grey Transfer Detalis", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$company_id = $company_id;
    $mst_ids    = $mst_ids;

	?>
	<fieldset style="width:1040; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="10"><b>FSO Grey Transfer Detalis</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL no</th>
                        <th width="135">From Sales Order</th>
                        <th width="80">Transfer Grey Out<br> Qty.(kg)</th>
                        <th width="120">Transfer Criteria</th>
                        <th width="135">To Sales Order</th>
                        <th width="130">Transfer ID</th>
                        <th width="80">Transfer Date</th>
                        <th width="80">Total No<br>of Roll</th>
                        <th width="80">Transfer Grey In<br> Qty.(kg)</th>
                    </tr>
				</thead>
            </table>
            <table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0" id="table_body">
                <?	
				if(!empty($mst_ids))
				{
                    $sql_transfer="SELECT a.id as mst_id, a.transfer_prefix_number, a.transfer_system_id, a.transfer_date, a.from_order_id, a.to_order_id, a.transfer_criteria, b.transaction_type,b.cons_quantity, c.buyer_id, c.job_no as sales_order_no, c.style_ref_no, c.sales_booking_no, c.within_group, c.booking_type, c.booking_without_order, c.booking_entry_form,c.company_id,c.po_company_id,b.prod_id 
                    from inv_item_transfer_mst a, inv_transaction b, fabric_sales_order_mst c
                    where a.id=b.mst_id and a.from_order_id=c.id and a.transfer_criteria=4 and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) and a.company_id=$company_id and a.id in($mst_ids) and a.entry_form=133 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id DESC";

                    //echo $sql_transfer;//die;
                    $sql_transfer_result=sql_select($sql_transfer);
                    $to_order_arr=array();
                    $trnsfer_arr=array();
                // $from_to_order_arr=array();
                    $trnsfer_sql_arr=array();
                    $from_to_order_id=array();
                
                    foreach($sql_transfer_result as $rows)
                    {
                        $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['mst_id']=$rows[csf('mst_id')];
                        $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['transfer_system_id']=$rows[csf('transfer_system_id')];
                        $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['sales_order_no']=$rows[csf('sales_order_no')];
                        $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['to_order_id']=$rows[csf('to_order_id')];
                        $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['sales_booking_no']=$rows[csf('sales_booking_no')];
                        $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['transfer_date']=$rows[csf('transfer_date')];
                        $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['transfer_criteria']=$rows[csf('transfer_criteria')];
                    
                        if($rows[csf('transaction_type')]==6)
                        {
                            $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['from_order_qnty']+=$rows[csf('cons_quantity')];
                        }
                        if($rows[csf('transaction_type')]==5)
                        {
                            $trnsfer_sql_arr[$rows[csf('transfer_system_id')]][$rows[csf('from_order_id')]][$rows[csf('to_order_id')]]['to_order_qnty']+=$rows[csf('cons_quantity')];
                        }
        
                        $to_order_arr[$rows[csf('to_order_id')]]=$rows[csf('to_order_id')];
                    
                        $trnsfer_arr[$rows[csf('mst_id')]]=$rows[csf('mst_id')];
                    
                    }
                    //var_dump($trnsfer_sql_arr);

                    $allToOrderCond = where_con_using_array($to_order_arr,0,"id");
                    $mstIdCond      = where_con_using_array($trnsfer_arr,0,"mst_id");
                   
                    $to_order_info="SELECT id, buyer_id, job_no as sales_order_no, style_ref_no, sales_booking_no, within_group,booking_type, booking_without_order, booking_entry_form, company_id, po_company_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 $allToOrderCond";
                    //echo $to_order_info;
                    $to_order_data_arr=array();
                    $order_info_result=sql_select($to_order_info);
                    foreach ($order_info_result as $key => $value) 
                    {
                        $to_order_data_arr[$value[csf("id")]]['sales_order_no']=$value[csf("sales_order_no")];
        
                    }

                    $sql_qry="SELECT mst_id from pro_roll_details WHERE  entry_form in(133) and status_active=1 and is_deleted=0 $mstIdCond order by mst_id";
                    //echo $sql_qry;
                     $roll_no_arr=array();
                     $sql_qry_result=sql_select($sql_qry);
                     foreach ($sql_qry_result as $key => $value) 
                     {
                         $roll_no_arr[$value[csf("mst_id")]]['no_of_roll']++;
                     }

				}else{
					 echo "Data Not Found";
				}
               
				$i=1;
				foreach ($trnsfer_sql_arr as $trans_sys_id=>$trans_sys_data) 
				{
                  
                    foreach ($trans_sys_data as $from_order_id => $from_order_data) 
                    {
                        foreach ($from_order_data as $to_order_data => $row) 
                        {
                       
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="135" align="center"><p><? echo $row['sales_order_no']; ?></p></td>
                                <td width="80" align="right"><? echo $row['from_order_qnty']; ?></td>
                                <td width="120" align="center"><? echo $item_transfer_criteria[$row['transfer_criteria']]; ?></td>
                                <td width="135" align="center"><? echo $to_order_data_arr[$row["to_order_id"]]['sales_order_no']; ?></td>
                                <td width="130" align="center">
                                    <a href="##" onclick="generate_print_report('<? echo $company_id;?>','<? echo $row['mst_id'];?>');"> <? echo $row['transfer_system_id']; ?></a>
                                    
                                </td>
                                <td width="80" align="center"><? echo change_date_format($row['transfer_date']); ?></td>
                                <td width="80" align="right"><? echo $roll_no_arr[$row["mst_id"]]['no_of_roll']; ?></td>
                                <td width="80" align="right"><? echo $row['to_order_qnty']; ?></td>
                            </tr>
                            <?
                            $fromOrderQnty+=$row['from_order_qnty'];
                            $noOfRoll+=$roll_no_arr[$row["mst_id"]]['no_of_roll'];
                            $toOrderQnty+=$row['to_order_qnty'];
                            $i++;
                        }
                    }
                }
                ?>
                <tfoot>
                	<tr>
                        <th colspan="2" align="right">Total</th>
                        <th align="right"><? echo number_format($fromOrderQnty,2); ?></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"><? echo number_format($noOfRoll,2); ?></th>
                        <th align="right"><? echo number_format($toOrderQnty,2); ?></th>
                    </tr>
                    
                </tfoot>
            </table>
		</div>
	</fieldset>	
  <script>

    function generate_print_report(company_id,mst_id)
    {
        var action="grey_fabric_order_to_order_transfer_print";
        var report_title="Roll wise Grey Sales Order To Sales Order Transfer";
        
        var data=company_id+'*'+mst_id+'*'+report_title;	

        window.open("../../../grey_fabric/requires/grey_sales_order_to_order_roll_trans_controller.php?data=" + data+'&action='+action, true );

    }

  setFilterGrid("table_body",-1);
  
  </script>
    <?
	exit();
}
?>