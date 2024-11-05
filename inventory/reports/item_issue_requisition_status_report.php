<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Item Issue Requisition Status Report
Functionality	:	
JS Functions	:
Created by		:	Zayed 
Creation date 	: 	27-03-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:	
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Issue Requisition Status Report", "../../", 1, 1, '', '', '');
?>

<script>
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function generate_report(rep_type) {
        var txt_indent_no = $("#txt_indent_no").val();
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }
        if (txt_indent_no == '') {
            if (form_validation('txt_date_from*txt_date_to', 'Date From*Date To') == false) {
                return;
            }
        }

        var report_title = $("div.form_caption").html();
        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*txt_indent_no*txt_date_from*txt_date_to*cbo_year_selection', "../../") + '&report_title=' + report_title;
        freeze_window(3);
        http.open("POST", "requires/item_issue_requisition_status_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse() {
        if (http.readyState == 4) {
            var response = trim(http.responseText).split("****");
            $('#report_container2').html(response[0]);

            document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window() {
        document.getElementById('scroll_body').style.overflowY = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";
        $('#table_body tr:nth-child(1)').css('display', 'none');

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>'); // media="print"
        d.close();

        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "250px";
        $('#table_body tr:nth-child(1)').css('display', '');
    }
</script>
</head>

<body onLoad="set_hotkey();">
    <form id="item_issue_req_status_rpt">
        <div style="width:100%;" align="center">
            <? echo load_freeze_divs("../../", ''); ?>
            <h3 align="left" id="accordion_h1" style="width:745px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
                <fieldset style="width:745px;">
                    <table class="rpt_table" width="750" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                        <thead>
                            <tr>
                                <th width="200" class="must_entry_caption">Company name</th>
                                <th width="180">Indent No</th>
                                <th colspan="2" width="240" class="must_entry_caption">Indent Date</th>
                                <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('item_issue_req_status_rpt','report_container*report_container2','','','')" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td align="center">
                                    <?
                                    echo create_drop_down("cbo_company_name", 200, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --");
                                    ?>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_indent_no" class="text_boxes" id="txt_indent_no" placeholder="Write" style="width:180px" title="  Allowed Characters: abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/<>?+[]{};: ">
                                </td>
                                <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date">
                                </td>
                                <td align="center">
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date">
                                </td>
                                <td align="center">
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center" colspan="9"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
        <div style="margin-top:10px" id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
    </form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>