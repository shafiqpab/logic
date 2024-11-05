<?
/* -------------------------------------------- Comments
  Purpose		: 	This form will create Lot Wise Yarn Transection Report

  Functionality	:
  JS Functions	:
  Created by                :	Tofael
  Creation date             : 	02-04-2017
  Updated by                :
  Update date               :
  QC Performed BY           :
  QC Date                   :
  Comments                  :
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Lot Wise Yarn Transection Report", "../../../", 1, 1, $unicode, 1, 1);
?>	
<script>
    var permission = '<? echo $permission; ?>';
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../logout.php";



    function generate_report(operation)
    {
        if (form_validation('cbo_company_name*cbo_supplier_name*txt_lot_no', 'Company Name*Supplier Name*Lot No') == false)
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_supplier_name = $("#cbo_supplier_name").val();
        var cbo_method = $("#cbo_method").val();
        var txt_lot_no = $("#txt_lot_no").val();
        var hidden_prod_no = $("#hidden_prod_no").val();
        var from_date = $("#txt_date_from").val();
        var to_date = $("#txt_date_to").val();

        var dataString = "&cbo_company_name=" + cbo_company_name + "&cbo_supplier_name=" + cbo_supplier_name + "&cbo_method=" + cbo_method + "&from_date=" + from_date + "&to_date=" + to_date + "&txt_lot_no=" + txt_lot_no + "&hidden_prod_no=" + hidden_prod_no;
        if(operation == 4){
            var data = "action=generate_report_lot_wise" + dataString;
        }else if(operation == 5){
            var data = "action=generate_report_lot_wise2" + dataString;
        }else{
            var data = "action=generate_report" + dataString;
        }
        freeze_window(3);
        http.open("POST", "requires/lot_wise_yarn_transaction_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;
    }

    function generate_report_reponse()
    {
        if (http.readyState == 4)
        {
            var reponse = trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window()
    {

        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";
        var currentdate = new Date();
        var datetime = "Print Date and Time: " + currentdate.getDate() + "-"
            + (currentdate.getMonth()+1)  + "-"
            + currentdate.getFullYear() + " "
            + currentdate.getHours() + ":"
            + currentdate.getMinutes() + ":"
            + currentdate.getSeconds();
        if($('#printDate').length > 0){
            $('#printDate').html(datetime);
        }

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "250px";
        if($('#printDate').length > 0){
            $('#printDate').html("");
        }
    }

    function change_color(v_id, e_color)
    {
        if (document.getElementById(v_id).bgColor == "#33CC00")
        {
            document.getElementById(v_id).bgColor = e_color;
        } else
        {
            document.getElementById(v_id).bgColor = "#33CC00";
        }
    }
    function fnc_lot_no() {
        if (form_validation('cbo_company_name*cbo_supplier_name', 'Company Name*Supplier Name') == false)
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_supplier_name = $("#cbo_supplier_name").val();

        var page_link = 'requires/lot_wise_yarn_transaction_report_controller.php?action=lot_no_search&cbo_company_name=' + cbo_company_name + '&cbo_supplier_name=' + cbo_supplier_name;
        var title = "Search Lot No Popup";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0', '../../')
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0];
            var prod_id = this.contentDoc.getElementById("hidden_product").value;
            var lot = this.contentDoc.getElementById("hidden_lot").value;
            $("#hidden_prod_no").val(prod_id);
            $("#txt_lot_no").val(lot);
        }
    }

    function reset_field() {
        $("#cbo_company_name").val("");
        $("#cbo_supplier_name").val("");
        $("#cbo_method").val("");
        $("#txt_lot_no").val("");
        $("#hidden_prod_no").val("");
        $("#txt_date_from").val("");
        $("#txt_date_to").val("");
    }
    function reset_lot(){
        $("#txt_lot_no").val("");
        $("#hidden_prod_no").val("");
    }

</script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../../../", $permission); ?>   		 
        <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
            <h3 style="width:910px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" style="width:100%;" align="center">
                <fieldset style="width:910px;">
                    <table class="rpt_table" width="910" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="160" class="must_entry_caption">Company</th>
                                <th width="150" class="must_entry_caption">Supplier Name</th>
                                <th width="130">Method</th>
                                <th width="90" class="must_entry_caption">Lot</th>
                                <th width="185">Date</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down("cbo_company_name", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/lot_wise_yarn_transaction_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );");
                                ?>                            
                            </td>
                            <td align="center" id="supplier_td">
                                <?
                                echo create_drop_down("cbo_supplier_name", 160, $blank_array, "", 1, "-- Select Supplier --", 0, "");
                                ?>          
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_method", 130, $store_method, "", 1, "Weighted Average", $selected, "", "", "");
                                ?>
                            </td>
                            <td>
                                <input name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:90px" placeholder="Browse" ondblclick="fnc_lot_no()" readonly>
                                <input type="hidden" id="hidden_prod_no" name="hidden_prod_no" value="">
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:68px"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:68px"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Lot Wise" onClick="generate_report(4)" style="width:70px; margin-left: 5px;" class="formbutton" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center"><? echo load_month_buttons(1); ?></td>
                            <td align="center">
                                <input type="button" name="search" id="search1" value="Lot Wise 2" onClick="generate_report(5)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </table> 
                </fieldset> 
            </div>

            <!-- Result Contain Start-------------------------------------------------------------------->

            <div id="report_container" align="center" style="margin-top: 7px; margin-bottom: 7px;"></div>
            <div id="report_container2"></div> 

            <!-- Result Contain END-------------------------------------------------------------------->


        </form>    
    </div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
