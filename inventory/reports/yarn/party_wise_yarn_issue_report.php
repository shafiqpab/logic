<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Party Wise Yarn Issue Report.

Functionality	:
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	06-04-2023
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Party Wise Yarn Issue Report", "../../../", 1, 1, $unicode, 1, 1);

?>

<script>
    var permission = '<? echo $permission; ?>';
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";


    function generate_report_party_wise() {
        if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company Name*From Date*To Date') == false) {
            return;
        }
        var lot_search_type = 0;
        if ($('#lot_search_type').is(":checked")) {
            lot_search_type = 1;
        }
        var report_title = $("div.form_caption").html();
        var data = "action=report_generate_party_wise" + get_submitted_data_string('cbo_company_name*cbo_store_name*cbo_buyer_id*txt_booking_no*txt_booking_id*txt_knitting_com_id*cbo_yarn_type*cbo_yarn_count*txt_lot_no*txt_date_from*txt_date_to*cbo_issue_purpose*cbo_using_item*cbo_knitting_source', "../../../") + '&report_title=' + report_title + "&lot_search_type=" + lot_search_type;
        freeze_window(3);
        http.open("POST", "requires/party_wise_yarn_issue_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;
    }

    function generate_report_reponse() {
        if (http.readyState == 4) {
            var reponse = trim(http.responseText).split("####");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            show_msg('3');
            release_freezing();
        }
    }


    function new_window() {
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();

        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "380px";
    }

    function openmypage_booking() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }
        var company_id = document.getElementById('cbo_company_name').value;
        var buyer_id = document.getElementById('cbo_buyer_id').value;
        var data = company_id + '_' + buyer_id;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_yarn_issue_report_controller.php?action=booking_no_popup&data=' + data, 'Booking No Popup', 'width=1055px,height=420px,center=1,resize=0', '../../')

        emailwindow.onclose = function() {
            var theform = this.contentDoc.forms[0];
            // alert(no+','+id);

            var no = this.contentDoc.getElementById("txt_booking_no").value;
            var id = this.contentDoc.getElementById("txt_booking_id").value;
            $('#txt_booking_no').val(no);
            $('#txt_booking_id').val(id);
        }
    }

    function openmypage_party() {
        if (form_validation('cbo_company_name*cbo_knitting_source', 'Company Name* Knitting source') == false) {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var cbo_knitting_source = $("#cbo_knitting_source").val();
        var page_link = 'requires/party_wise_yarn_issue_report_controller.php?action=party_popup&companyID=' + companyID + '&cbo_knitting_source=' + cbo_knitting_source;
        var title = 'Party Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0', '../../');
        emailwindow.onclose = function() {
            var theform = this.contentDoc.forms[0];
            var party_name = this.contentDoc.getElementById("hide_party_name").value;
            var party_id = this.contentDoc.getElementById("hide_party_id").value;

            $('#txt_knitting_company').val(party_name);
            $('#txt_knitting_com_id').val(party_id);
        }
    }

    function kniting_company_val() {
        $('#txt_knitting_company').val('');
        $('#txt_knitting_com_id').val('');
    }


    function getStore(company_id) {
        var company_id = company_id;

        if (company_id != '') {
            var data = "action=load_drop_down_company_store&choosenCompany=" + company_id;
            http.open("POST", "requires/party_wise_yarn_issue_report_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = function() {
                if (http.readyState == 4) {
                    var response = trim(http.responseText);
                    $('#store_td').html(response);
                    set_multiselect('cbo_store_name', '0', '0', '', '0');
                }
            };
        }
    }
</script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="left">
        <? echo load_freeze_divs("../../../", $permission); ?><br />
        <form name="partyYarnIssueReport_1" id="partyYarnIssueReport_1" autocomplete="off">
            <div style="width:100%;" align="center">
                <fieldset style="width:1720px;">
                    <legend>Search Panel</legend>
                    <table class="rpt_table" width="1720" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <tr>
                                <th width="132" class="must_entry_caption">Company</th>
                                <th width="112">Store</th>
                                <th width="112">Buyer</th>
                                <th width="130">Source</th>
                                <th width="140">Party</th>
                                <th width="112">Booking</th>
                                <th width="112">Yarn Type</th>
                                <th width="102">Count</th>
                                <th width="82">Lot<br><input type="checkbox" name="lot_search_type" id="lot_search_type" title="Lot Search start with"></th>
                                <th width="112">Issue Purpose</th>
                                <th width="112">Issue Basis</th>
                                <th width="102">Using Item</th>
                                <th width="185" class="must_entry_caption">Issue Date</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('partyYarnIssueReport_1','report_container*report_container2','','','','');" /></th>
                            </tr>
                        </thead>
                        <tr align="center">
                            <td valign="middle">
                                <?
                                echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "getStore(this.value);load_drop_down('requires/party_wise_yarn_issue_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/party_wise_yarn_issue_report_controller' );");
                                //load_drop_down('requires/party_wise_yarn_issue_report_controller',this.value, 'load_drop_down_store', 'store_td' );
                                ?>
                            </td>

                            <td valign="middle" id="store_td">
                                <?
                                //echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and b.category_type in(1) order by a.store_name","id,store_name", 0, "", 0, "" );

                                echo create_drop_down("cbo_store_name", 110, $blank_array, "", 1, "--Select--", 0, "", 0);

                                ?>
                            </td>

                            <td valign="middle" id="buyer_td">
                                <?
                                echo create_drop_down("cbo_buyer_id", 110, $blank_array, "", 1, "--Select--", 0, "", 0);
                                ?>
                            </td>
                            <td valign="middle">
                                <?
                                echo create_drop_down("cbo_knitting_source", 130, $knitting_source, "", 1, "-- Select Source --", 0, "kniting_company_val();", 0, '1,3');
                                ?>
                            </td>
                            <td id="knitting_com">
                                <input type="text" id="txt_knitting_company" name="txt_knitting_company" class="text_boxes" style="width:100px" onDblClick="openmypage_party();" placeholder="Browse Party" />
                                <input type="hidden" id="txt_knitting_com_id" name="txt_knitting_com_id" class="text_boxes" style="width:60px" />
                            </td>
                            <td valign="middle">
                                <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:110px" onDblClick="openmypage_booking();" placeholder="Browse" />
                                <input type="hidden" id="txt_booking_id" name="txt_booking_id" class="text_boxes" />
                            </td>
                            <td valign="middle">
                                <?
                                echo create_drop_down("cbo_yarn_type", 110, $yarn_type, "", 0, "--Select--", 0, "", 0);
                                ?>
                            </td>
                            <td valign="middle">
                                <?
                                echo create_drop_down("cbo_yarn_count", 100, "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count", 0, "-- Select --", $selected, "");
                                ?>
                            </td>
                            <td valign="middle">
                                <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:80px" value="" placeholder="Write" />
                            </td>
                            <td valign="middle">
                                <?
                                //echo create_drop_down("cbo_issue_purpose",100,$yarn_issue_purpose,"",1, "-- Select --", $selected, "");
                                //echo create_drop_down( "cbo_issue_purpose", 130, $yarn_issue_purpose,"", 1, "--Select Purpose--",$selected,"","","","","","9,10,11,13,14,27,28" );
                                echo create_drop_down("cbo_issue_purpose", 110, $yarn_issue_purpose, "", 0, "--Select Purpose--", $selected, "", "", "1,2,4,7,8,15,16,38,46,3,5,6,12,26,29,30,39,40,45,50,51,54", "", "", "");
                                ?>
                            </td>
                            <td valign="middle">
                                <?
                                echo create_drop_down("cbo_basis", 110, $issue_basis, "", 0, "--Select Basis--", $selected, "", "", "", "", "", "");
                                ?>
                            </td>
                            <td valign="middle">
                                <?
                                echo create_drop_down("cbo_using_item", 100, $using_item_arr, "", 1, "--Select--", "", "", 0);
                                ?>
                            </td>

                            <td valign="middle" align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date" />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date" />
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report_party_wise()" style="width:70px;" class="formbutton" />
                            </td>

                        </tr>
                        <tr>
                            <td colspan="10" align="center"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </table>
                </fieldset>

                <div id="report_container" align="center" style="padding: 10px;"></div>
                <div id="report_container2"></div>

            </div>
        </form>
    </div>
</body>
<script>
    set_multiselect('cbo_yarn_type*cbo_yarn_count*cbo_issue_purpose*cbo_using_item*cbo_store_name*cbo_basis', '0*0*0*0*0*0', '0*0*0*0*0*0', '', '0*0*0*0*0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

</html>