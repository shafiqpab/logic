<?
/* -------------------------------------------- Comments -----------------------
  Purpose			: 	This Form Will Create Order Allocation Details Report.
  Functionality	:
  JS Functions	:
  Created by		:	Mezbah
  Creation date 	: 	15-02-2017
  Updated by 		:
  Update date		:
  QC Performed BY	:
  QC Date			:
  C

  omments		:
 */

session_start();


if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Allocation Details", "../../", 1, 1, $unicode, 1, 1);
?>	
<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";

    var tableFilters =
            {
                col_33: "none",
                col_operation: {
                    id: ["total_order_qnty", "total_order_qnty_in_pcs", "value_tot_cm_cost", "value_tot_cost", "value_order", "value_margin", "value_tot_trims_cost", "value_tot_embell_cost"],
                    col: [9, 11, 25, 26, 29, 30, 31, 32],
                    operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
                    write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
                }
            }
    function fn_report_generated()
    {
        if ($('#cbo_company_id').val() == 0)
        {
            if ($('#cbo_allocation_company_id').val() == 0)
            {
                alert('Please select Owner or Allocated company');
                return;
            }
        }

        if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false)//*txt_date_from*txt_date_to*From Date*To Date
        {
            return;
        } else
        {
            var data = "action=report_generate" + get_submitted_data_string('cbo_company_id*cbo_allocation_company_id*cbo_buyer_name*txt_date_from*txt_date_to', "../../");
            freeze_window(3);
            http.open("POST", "requires/order_allocation_details_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {
            //var reponse=trim(http.responseText).split("****");
            var reponse = trim(http.responseText).split("####");
            $("#report_container2").html(reponse[0]);
            //alert(reponse[0]);  
            document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            //append_report_checkbox('table_header_1',1);

            setFilterGrid("table_body", -1, tableFilters);
            setFilterGrid("tbl_header", -1);

            show_msg('3');
            release_freezing();
        }
    }

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";
        //$('#scroll_body tr:first').hide();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "380px";
        //$('#scroll_body tr:first').show();
        //document.getElementById('scroll_body').style.maxWidth="120px";
    }

</script>
</head>

<body onLoad="set_hotkey();">

    <form id="cost_breakdown_rpt">
        <div style="width:100%;" align="center">
            <? echo load_freeze_divs("../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:1060px" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
                <fieldset style="width:1060px;">
                    <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>                   
                                <th class="must_entry_caption">Company Name</th>
                                <th class="must_entry_caption" >Allocation Company</th>
                                <th>Buyer Name</th>

<!--                              <th>Job No</th>
 <th>Order</th>
 <th>Date Category</th> -->
                                <th class="must_entry_caption" id="td_date_caption">Cut-off Date</th>
                                <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                    echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( 'requires/sewing_plan_vs_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                    ?>
                                </td>

                                <td>
                                    <?
                                    echo create_drop_down("cbo_allocation_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
                                    ?>

                                </td>
                                <td id="buyer_td">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
                                    ?>
                                </td>

                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" disabled placeholder="From Date" >&nbsp; To&nbsp;
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" disabled  placeholder="To Date" ></td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <? echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </table> 
                </fieldset>
            </div>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>

    </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
