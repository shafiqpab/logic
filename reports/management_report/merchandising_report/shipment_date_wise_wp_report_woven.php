<?
/* -------------------------------------------- Comments -----------------------
  Purpose           :   This Form Will Create Order wise Production Report.
  Functionality :
  JS Functions  :
  Created by        :   Bilas
  Creation date     :   1-04-2013
  Updated by        :
  Update date       :
  QC Performed BY   :
  QC Date           :
  Comments      :
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Work Progress Report", "../../../", 1, 1, $unicode, 1, 1);
?>

<script>

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../../logout.php";
    var permission = '<? echo $permission; ?>';
    var cbo_search_by = $("#cbo_search_by").val();

    var tableFilters =
    {
        //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
        col_operation: {
            id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty"],
            col: [14, 15, 40, 42, 43, 44],
            operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
            write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
        }
    }
    var tableFilters_GarBtn =
    {
        //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
        col_operation: {
            id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty"],
            //col: [14, 15, 37, 39, 40, 41],
            col: [17, 18, 38, 40, 41, 42],
            operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
            write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
        }
    }
	

    var tableFilters2 =
    {
        //col_0: "none",col_5: "none",col_5: "none",col_21: "none",col_29: "none",
        col_operation: {
            id: ["value_total_order_value", "total_order_qnty_pcs", "td_ship_per_ex_qty", "total_ship_qnty", "value_total_ship_value", "total_balance_ship_qnty", "total_balance_ship_qnty_as_ex"],
            col: [15, 16, 44, 46, 47, 48, 49],
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

        var budget_version=document.getElementById('cbo_budget_version').value;

        if (form_validation('cbo_company_name', 'Comapny Name') == false)
        {
            return;
        }
		else
        {
            var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*txt_file_no*txt_ref_no*cbo_search_by*cbo_year*cbo_team_name*cbo_team_member*cbo_ls_sc*cbo_season_id*cbo_order_status*cbo_budget_version*cbo_brand_id*cbo_season_year', "../../../") + "&garmentBtn=" + garmentBtn;
            //alert(data);
            freeze_window(3);
            if(budget_version==1)
            {
                http.open("POST", "requires/shipment_date_wise_wp_report_woven_controller.php", true);
            }
            else
            {
                http.open("POST", "requires/shipment_date_wise_wp_report_woven_controller2.php", true);
            }
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {
            release_freezing();
            var reponse = trim(http.responseText).split("####");
            $('#report_container2').html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            // document.getElementById('report_container').innerHTML = report_convert_button('../../../');
            //append_report_checkbox('table_header_1', 1);

            document.getElementById("check_uncheck_tr").style.display="table";
            //if($("#check_uncheck").is(":checked")==false)
            //$("#check_uncheck").attr("checked","checked");

            var cbo_search_by = $("#cbo_search_by").val();

            var GarBtnId = reponse[1];
	 		//alert(cbo_search_by+'='+GarBtnId);
            
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
    
    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none"; 
        $(".flt").css("display","none");
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title><style>table tr th,table tr td{font-size:10px !important;}</style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        document.getElementById('scroll_body').style.overflowY="scroll"; 
        document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css("display","block");
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

    function show_progress_report_details(action, job_number, width, type, country)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_woven_controller.php?action=' + action + '&job_number=' + job_number + '&type=' + type + '&country=' + country, 'Work Progress Report Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
    }

    function show_progress_report_daysInHand(action, job_number, width, country)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_woven_controller.php?action=' + action + '&job_number=' + job_number + '&country=' + country, 'Work Progress Report Details', 'width=' + width + ',height=370px,center=1,resize=0,scrolling=0', '../../');
    }

    function show_trims_rec(action, po_number, po_id, width)
    {
        var budget_version=document.getElementById('cbo_budget_version').value;
        if(budget_version==1)
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_woven_controller.php?action=' + action + '&po_number=' + po_number + '&po_id=' + po_id, 'Trims Receive Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
        }
        else
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_woven_controller2.php?action=' + action + '&po_number=' + po_number + '&po_id=' + po_id, 'Trims Receive Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
        }
    }

    function progress_comment_popup(job_no, po_id, template_id, tna_process_type)
    {
        var budget_version=document.getElementById('cbo_budget_version').value;
        var data = "action=update_tna_progress_comment" +
            '&job_no=' + "'" + job_no + "'" +
            '&po_id=' + "'" + po_id + "'" +
            '&template_id=' + "'" + template_id + "'" +
            '&tna_process_type=' + "'" + tna_process_type + "'" +
            '&permission=' + "'" + permission + "'";

        if(budget_version==1)
        {
            http.open("POST", "requires/shipment_date_wise_wp_report_woven_controller.php", true);
        }
        else
        {
            http.open("POST", "requires/shipment_date_wise_wp_report_woven_controller2.php", true);
        }

        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_progress_comment_reponse;
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
        $('#cbo_ls_sc').val(0);

        if (val == 1)
        {
            $('#cbo_ls_sc').removeAttr('disabled', 'disabled');
        } else
        {
            $('#cbo_ls_sc').attr('disabled', 'disabled');
        }
        if (val == 3)
        {
            document.getElementById('search_by_th_up').innerHTML = "Country Ship Date";
        } else
        {
            document.getElementById('search_by_th_up').innerHTML = "Pub. Shipment Date";
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
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_woven_controller.php?po_break_down_id=' + po_break_down_id + '&company_name=' + company_name + '&item_id=' + item_id + '&country_id=' + country_id + '&action=' + action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0', '../../');
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


    function show_report_details(action, dataStr, width, title)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_date_wise_wp_report_woven_controller2.php?action=' + action + '&data_str=' + dataStr , title, 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
    }

	function fnc_brandload()
	{
		var buyer=$('#cbo_buyer_name').val();
		if(buyer!=0)
		{
			load_drop_down( 'requires/shipment_date_wise_wp_report_woven_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}

</script>
</head>
<body onLoad="set_hotkey(); fnc_brandload();">
<form id="dateWiseProductionReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../../../", ''); ?>
        <h3 style="width:1811px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1880px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Brand</th>
                    <th>Season</th>
                    <th>Season Year</th>                  
                    <th>Team</th>
                    <th>Team Member</th>
                    <th>Type</th>
                    <th>LC/SC Type</th>
                    <th>Year</th>
                    <th>Job No</th>
                    <th>Style Ref.</th>
                    <th>Order No</th>
                    <th>File No</th>
                    <th>Master Style/Internal Ref</th>                   
                    <th>Order Status</th>
                    <th>Budget Version</th>
                    <th id="search_by_th_up">Pub. Shipment Date</th>
                    <th colspan="2" width="120"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td align="center">
                            <?
                            echo create_drop_down("cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/shipment_date_wise_wp_report_woven_controller',this.value,'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/shipment_date_wise_wp_report_woven_controller' );");
                            ?>
                        </td>
                        <td id="buyer_td"  align="center">
                            <?
                            echo create_drop_down("cbo_buyer_name", 110, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 1, "");
                            ?>
                        </td>                      
                        <td id="brand_td">
                            <?= create_drop_down( "cbo_brand_id", 100, $blank_array,'', 1, "--Brand--",$selected );?>
                        </td>
                        <td id="season_td"><? echo create_drop_down("cbo_season_id", 80, $blank_array, "", 1, "-Season- ", $selected, ""); ?></td>
                        <td>
                        <?
									//$selected_year=date("Y");                               
                       				// echo create_drop_down( "cbo_season_year", 70, $year,"", 1, "--Select Year--",$selected_year,'',0)
                                        echo create_drop_down( "cbo_season_year", 70, $year,"", 1, "--Select Year--",$selected,'',0);;
								?>
                        </td>
                      
                        <td>
                            <?
                            echo create_drop_down("cbo_team_name", 110, "select id,team_name from lib_marketing_team where status_active=1 and is_deleted=0 order by team_name", "id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/shipment_date_wise_wp_report_woven_controller', this.value, 'load_drop_down_team_member', 'team_td' );");
                            ?>
                        </td>
                        <td id="team_td">
                            <?
                            echo create_drop_down("cbo_team_member", 120, $blank_array, "", 1, "- Team Member- ", $selected, "");
                            ?>
                        </td>
                        <td align="center">
                            <?
                            $search_by_arr = array(1 => "Order Wise", 2 => "Style Wise", 3 => "Country Ship date");
                            echo create_drop_down("cbo_search_by", 90, $search_by_arr, "", 0, "", "", 'disable_order(this.value);', 0); //search_by(this.value)
                            ?>
                        </td>
                        <td align="center">
                            <?
                            $lcSc_type = array(1 => "All", 2 => "With LC/SC", 3 => "Without LC/SC");
                            echo create_drop_down("cbo_ls_sc", 90, $lcSc_type, "", 0, "", "", '', 0); //search_by(this.value)
                            ?>
                        </td>
                        <td align="center">
                            <?
                            echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --", $selected, "", 0, "");
                            ?>
                        </td>
                        <td align="center">
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder="Job No" >
                        </td>
                        <td align="center">
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Styel Ref" >
                        </td>
                        <td align="center">
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Order No" >
                        </td>
                        <td align="center">
                            <input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px" placeholder="File No" >
                        </td>
                        <td align="center">
                            <input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:120px" placeholder="Ref. No" >
                        </td>
                      
                        <td>
                            <? echo create_drop_down( "cbo_order_status", 80, $order_status, "", 1, "----All----",0, "",0,"" ); ?>
                        </td>
                        <td width="" align="center">
                            <?
                            $budget_version_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
                            //$dd="search_populate(this.value)";

                            //echo create_drop_down("cbo_buyer_name", 110, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 1, "");
                            echo create_drop_down( "cbo_budget_version", 100, $budget_version_arr,"",1, "--Select--", 2,"",1,"" );
                            ?>
                        </td>

                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date">&nbsp;
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date">
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:60px; display:none;" value="Show" onClick="fn_report_generated(2)" />
                        </td>
                        <td>
                            <input type="button" id="show_button1" class="formbutton" style="width:60px; display:none;" value="Garments" onClick="fn_report_generated(2)" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
