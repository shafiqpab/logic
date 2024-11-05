<?
/* -------------------------------------------- Comments -----------------------
  Purpose			: 	This Form Will Create Order wise Production Report.
  Functionality	:
  JS Functions	:
  Created by		:	Bilas
  Creation date 	: 	1-04-2013
  Updated by 		:
  Update date		:
  QC Performed BY	:
  QC Date			:
  Comments		:
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Changed Country Shipment Report", "../../", 1, 1, $unicode, 1, 1);
?>	

<script>

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';
    var cbo_search_by = $("#cbo_search_by").val();

    var tableFilters =
            {
                //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
                col_operation: {
                    id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty"],
                    col: [14, 15, 41, 43, 44, 45],
                    operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
                    write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
                }
            }
    var tableFilters_GarBtn =
            {
                //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
                col_operation: {
                    id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty"],
                    col: [14, 15, 37, 39, 40, 41],
                    operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
                    write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
                }
            }

    var tableFilters2 =
            {
                //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
                col_operation: {
                    id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty", "total_balance_ship_qnty_as_ex"],
                    col: [15, 16, 45, 47, 48, 49, 50],
                    operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
                    write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
                }
            }
           
		    
			var tableFilters2_GarBtn =
            {
                //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
                col_operation: {
                    id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty", "total_balance_ship_qnty_as_ex"],
                    col: [15, 16, 41, 43, 44, 45, 46],
                    operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum"],
                    write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
                }
            }

    function fn_report_generated(garmentBtn)
    {
		var job_no=$('#txt_job_no').val();
		if(job_no ==''){
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name,From Date*To date')==false)
			{
				return;
			}
			else
			{	
				var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_internal_ref*txt_order_no*cbo_search_by', "../../") + "&garmentBtn=" + garmentBtn;

			http.open("POST", "requires/changed_ship_date_report_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
			}
		}
		if(job_no !=''){
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
			else
			{	
				var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_internal_ref*txt_order_no*cbo_search_by', "../../") + "&garmentBtn=" + garmentBtn;

			http.open("POST", "requires/changed_ship_date_report_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
			}
		}	
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {   
            release_freezing();
            var reponse = trim(http.responseText).split("**");
            $('#report_container2').html(reponse[0]);
            document.getElementById('report_container').innerHTML = report_convert_button('../../');
            //append_report_checkbox('table_header_1', 1);
            
            document.getElementById("check_uncheck_tr").style.display="table";
            if($("#check_uncheck").is(":checked")==false)
                $("#check_uncheck").attr("checked","checked");

            var cbo_search_by = $("#cbo_search_by").val();          
			
			var GarBtnId = reponse[1];
			
			if (cbo_search_by == 1 || cbo_search_by == 2)
            {
                if (GarBtnId == 2) {
                    setFilterGrid("table_body", -1, tableFilters_GarBtn);
                } else {
                    setFilterGrid("table_body", -1, tableFilters);
                }
            } else if(cbo_search_by == 3)
            {
                if (GarBtnId == 2) {
                    setFilterGrid("table_body", -1, tableFilters2_GarBtn);
                } else {
                    setFilterGrid("table_body", -1, tableFilters2);
                }
            }

            show_msg('3');
            release_freezing();
        }

    }

    function fn_check_uncheck(){
        var lengths = $("[type=checkbox]").length;
        if($("#check_uncheck").is(":checked") != true){     
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").prop('checked', false);
                $("[type=checkbox]").removeClass('rpt_check');
                $("[type=checkbox]").removeAttr('checked');
            }
        }else{
            $("[type=checkbox]").prop('checked', true);
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").not("#check_uncheck").addClass('rpt_check');
                $("[type=checkbox]").attr('checked',"checked");
            }
        }    
    }



    function generate_progress_comment_reponse()
    {
        if (http.readyState == 4)
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><title></title></head><body>' + http.responseText + '</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
            d.close();
        }
    }

    function disable_order(val)
    {
        if (val == 1)
        {
            document.getElementById('search_by_th_up').innerHTML = "Country Ship Date";
        } else
        {
            document.getElementById('search_by_th_up').innerHTML = "Insert Date";
        }
    }

    function openmypage_image(page_link, title)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '../../')
        emailwindow.onclose = function ()
        {
        }
    }




    function openmypage_order(po_break_down_id, company_name, item_id, country_id, action)
    {
        //var garments_nature = $("#cbo_garments_nature").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/changed_ship_date_report_controller.php?po_break_down_id=' + po_break_down_id + '&company_name=' + company_name + '&item_id=' + item_id + '&country_id=' + country_id + '&action=' + action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0', '../../');
    }

    function print_report_button_setting(report_ids) 
    {
       
        $('#show_button').hide();
        $('#show_button1').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==248){$('#show_button1').show();}
            });
    }
    




</script>
</head>
<body onLoad="set_hotkey();">
    <form id="dateWiseProductionReport_1">
        <div style="width:100%;" align="center">    
            <? echo load_freeze_divs("../../", ''); ?>
            <h3 style="width:840px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3> 
            <div id="content_search_panel" >      
                <fieldset style="width:840px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>                    
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>                     
                            <th>Job No</th>
                            <th>Internal Ref No</th>
                            <th>Order No</th>
                            <th>Date Type</th>
                            <th id="search_by_th_up">Country Shipdate Wise</th>
                            <th colspan="2" width="130"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td align="center"> 
                                    <?
                                    echo create_drop_down("cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/changed_ship_date_report_controller',this.value,'load_drop_down_buyer', 'buyer_td' );");
                                    ?>
                                </td>
                                <td id="buyer_td"  align="center">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 110, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 1, "");
                                    ?>
                                </td>
                                <td align="center">
                                    <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder="Job No" >
                                </td>
                                <td align="center">
                                    <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px" placeholder="Internal Ref" >
                                </td>
                                <td align="center">
                                    <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Order No" >
                                </td>
                                <td align="center">
                                    <?
                                    $search_by_arr = array(1 => "Country Shipdate Wise", 2 => "Insert Date Wise");
                                    echo create_drop_down("cbo_search_by", 90, $search_by_arr, "", 0, "", "", 'disable_order(this.value);', 0); //search_by(this.value)
                                    ?>
                                </td>
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date">&nbsp;
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date">
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:60px;" value="Show" onClick="fn_report_generated(1)" />
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
                    <table align="left">
                        <tr id="check_uncheck_tr" style="display:none;">
                            <td><input type="checkbox" id="check_uncheck" name="check_uncheck" onClick="fn_check_uncheck()"/> <strong style="color:#176aaa; font-size:14px; font-weight:bold;">Check/Uncheck All</strong>
                            </td>
                        </tr>
                    </table>                    
                    <br />
                </fieldset>
            </div>
        </div>

        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
