<?php

/*--------------------------------------------Comments----------------
Created by               :  Rezoanul 
Creation Date            :  31-07-2023
Purpose			         : 	This form will take entry of the yearly sample fabric budget buyer wise
Functionality	         :
JS Functions	         :
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
----------------------------------------------------------------------*/


session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;


//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yearly Sample Fabric Budget Entry", "../../", 1, 1, $unicode, 1, '');


?>

<script>
    var permission = '<? echo $permission; ?>';

    // function save_show_btn(){
    //     fnc_yearly_fabric_budget_entry(0);
    // }

    function buyer_total_pre_fnc() {
        var sum = 0;
        var inps = document.getElementsByName('current_year_budget_qty_id[]');
        for (var i = 0; i < inps.length; i++) {
            [text, buyer_id] = inps[i].id.split('_');
            sum += parseFloat($("#PreYearTotal_" + buyer_id).val());
        }
        document.getElementById("buyer_total_pre").value = sum;
    }

    function buyer_total_fnc() {

        var sum = 0;
        var inps = document.getElementsByName('current_year_budget_qty_id[]');
        for (var i = 0; i < inps.length; i++) {
            [text, buyer_id] = inps[i].id.split('_');
            sum += parseFloat($("#yearTotal_" + buyer_id).val());
        }
        document.getElementById("buyer_total").value = sum;
    }



    // machine entry function start
    function fnc_yearly_fabric_budget_entry(operation) {
        //txt_group
        // alert(operation); exit;
        // var catagory_id = $("#cbo_catagory").val();
        if (operation == 2) {
            alert("Delete Not Allowed");
            return;
        }
        var company_id = $("#cbo_company_name").val();
        var budget_year = $("#cbo_budgeted_year").val();
        var location = $("#cbo_location_name").val();
        var team_leader = $("#cbo_team_leader").val();
        var update_id = $("#update_id").val();



        if (form_validation('cbo_company_name*cbo_location_name*cbo_team_leader*cbo_budgeted_year', 'Company Name*Location Name*Team Leader*Budgeted Year') == false) {
            return;
        }

        // $("#buyer_data_tbl").find('tr').each(function() {
        //     var break_down_data=$(this).find('input[name="current_year_budget_qty_id[0]"]').val()*1;  // get value
        //     // var txtbalanceqnty=$(this).find('input[name="txtbalanceqnty[]"]').val()*1;   // get value
        //     // $(this).find('input[name="txtfinishQnty[]"]').val(finish_qnty.toFixed(2));  // set value
        //     // $(this).find('input[name="txtbalanceqnty[]"]').val(balance_qty.toFixed(2));  // set value
        //     alert(break_down_data);
        // });

        var yearTotalArr = Array();
        var monthBrkArr = Array();
        var inps = document.getElementsByName('current_year_budget_qty_id[]');
        //alert(inps.length);
        for (var i = 0; i < inps.length; i++) {
            [text, buyer_id] = inps[i].id.split('_');
            // alert(inps[i].value);
            yearTotalArr.push(buyer_id + '*' + inps[i].value);
            monthBrkArr.push(buyer_id + '#' + $("#break_down_data_" + buyer_id).val());
            //alert(buyer_id);
            //return;
        }

        var year_total = yearTotalArr.join(',');
        var month_br = monthBrkArr.join('___');
        // alert(month_br);return;

        var data = "action=save_update_delete&operation=" + operation + '&update_id=' + update_id + '&year_total=' + year_total + '&month_br=' + month_br + get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_team_leader*cbo_budgeted_year*cbo_starting_month', "../../");
        //alert(data);return;
        freeze_window(operation);
        http.open("POST", "requires/yearly_sample_fabric_budget_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_yearly_fabric_budget_entry_reponse;

    }

    function fnc_yearly_fabric_budget_entry_reponse() {
        if (http.readyState == 4) 
        {
            // alert(http.responseText);exit;
            var reponse = trim(http.responseText).split('**');
            //alert(reponse)
            show_msg(reponse[0]);
            $("#update_id").val(reponse[1]);
            budget_show();
            set_button_status(1, permission, 'fnc_yearly_fabric_budget_entry', 1);
            release_freezing();
            // alert(update_id);
        }
    }
    // machine entry function end 


    function current_year_budget_popup(buyer, dtls_id,mst_id) {
        // alert("hi"); exit;

        var companyID = $("#cbo_company_name").val();
        var budgeted_year = $("#cbo_budgeted_year").val();
        var location = $("#cbo_location_name").val();
        var team_leader = $("#cbo_team_leader").val();
        //alert(companyID+team_leader);
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/yearly_sample_fabric_budget_entry_controller.php?action=budget_month_popup&companyID=' + companyID + '&budgeted_year=' + budgeted_year + '&dtls_id=' + dtls_id + '&mst_id=' + mst_id + '&location=' + location + '&team_leader=' + team_leader + '&buyer=' + buyer, 'Budget Deatails', 'width=600px,height=350px,center=1,resize=0,scrolling=1', '../../');

        emailwindow.onclose = function() {

            //var year_total_result = this.contentDoc.getElementById("hidden_total_budget_qty").innerHTML;
            //var hidden_all_data = this.contentDoc.getElementById("hidden_all_data").innerHTML;
            var hidden_all_data = this.contentDoc.getElementById("hidden_all_data").value;
            var year_total_result = this.contentDoc.getElementById("hidden_total_budget_qty").value;
            // alert(hidden_all_data);return;
            // alert(buyer);
            var row_id = "yearTotal_" + buyer;
            // alert(row_id);
            document.getElementById(row_id).value = year_total_result;
            $("#break_down_data_" + buyer).val(hidden_all_data);
            buyer_total_fnc();
        }
    }

    function budget_show() {
        // $("#show_budget").attr("style","display:none;");
        var company_id = $("#cbo_company_name").val();
        var budget_year = $("#cbo_budgeted_year").val();
        var location = $("#cbo_location_name").val();
        var team_leader = $("#cbo_team_leader").val();
        if(budget_year==0){
            document.getElementById("report_container3").innerHTML = "";
            return;
        }

        if(location==0){
            alert("Select Location");
            return;
        }
        if(team_leader==0){
            alert("Select Team Leader");
            return;
        }
        $("#cbo_team_leader").attr("disabled", "");
        $("#cbo_company_name").attr("disabled", "");
        $("#cbo_location_name").attr("disabled", "");

        var data = "action=show_budget_list_buyer_wise&company_id=" + company_id + "&budget_year=" + budget_year + "&location=" + location + "&team_leader=" + team_leader;

        //alert(data);
        freeze_window(budget_year);
        http.open("POST", "requires/yearly_sample_fabric_budget_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fabric_budget_entry_reponse;
    }

    function fabric_budget_entry_reponse() {
        if (http.readyState == 4) {
            var response = trim(http.responseText).split('**');
            //alert(response);
            document.getElementById('report_container3').innerHTML = response[0];
            buyer_total_pre_fnc();
            var btn_status=$("#btn_status").val();
            var hid_update_id=$("#hid_update_id").val();
            $("#update_id").val(hid_update_id);
            //alert(btn_status);
            if(btn_status>0)
                set_button_status(1, permission, 'fnc_yearly_fabric_budget_entry', 1);
            else
            set_button_status(0, permission, 'fnc_yearly_fabric_budget_entry', 1);
            release_freezing();
        }
    }


    // onclick function for budgeted year dropdown 
    function show_budget_form(budget_year) {
        if (budget_year == 0) {
            return;
        }
        var company_id = $("#cbo_company_name").val();
        if (company_id == 0) {
            alert("Company is not selected");
            document.getElementById("cbo_budgeted_year").value = 0;
            return;
        }

        var data = "action=show_budget_list_buyer_wise&company_id=" + company_id + "&budget_year=" + budget_year;

        //alert(data);
        freeze_window(budget_year);
        http.open("POST", "requires/yearly_sample_fabric_budget_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fabric_budget_entry_reponse;

    }
    
    function disable_remove(){
        $("#cbo_team_leader"). removeAttr("disabled");   
        $("#cbo_location_name"). removeAttr("disabled");   
        $("#cbo_company_name"). removeAttr("disabled");   
        document.getElementById("report_container3").innerHTML ="";
    }

    
</script>

</head>

<body onLoad="set_hotkey();">
    <div align="center" style="width:100%">
        <? echo load_freeze_divs("../../", $permission);  ?>
        <fieldset style="width:1000px;">
            <legend>Yearly Sample Fabric Budget Entry</legend>
            <form name="yearly_sample_fabric_budget_entry_1" id="yearly_sample_fabric_budget_entry_1" autocomplete="off">
                <table align="center" width="1000">

                    <!-- labels  -->
                    <tr>
                        <td width="150px" align="left" class="must_entry_caption">Company</td>
                        <td width="80px" align="left" class="must_entry_caption">Location</td>
                        <td width="80px" align="left" class="must_entry_caption">Team Leader</td>
                        <td width="80px" align="left" class="must_entry_caption">Budgeted Year</td>
                        <td width="80px" align="left" class="must_entry_caption">Starting Month</td>
                    </tr>

                    <!-- drop downs  -->
                    <tr>
                        <td width="80px"><? echo create_drop_down(
                                                "cbo_company_name",
                                                150,
                                                "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name",
                                                'id,company_name',
                                                1,
                                                "--- Select Company ---",
                                                '',
                                                " load_drop_down( 'requires/yearly_sample_fabric_budget_entry_controller', this.value, 'load_drop_down_location', 'location' );load_drop_down('requires/yearly_sample_fabric_budget_entry_controller', this.value, 'select_sales_starting_month', 'cbo_starting_month');"
                                            ); ?>
                        </td>
                        <td id="location"><? echo create_drop_down("cbo_location_name", 150, $blank_array, '', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/machine_name_entry_controller', this.value, 'load_drop_down_floor', 'floor' )"); ?></td>
                        <td id="div_teamleader"><?= create_drop_down("cbo_team_leader", 120, "select id,team_leader_name from lib_marketing_team where project_type=1 and team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-- Select Team Leader --", $selected);
                                                ?></td>
                        <?php
                        $budget_year_arr = array(1 => "2020-2021", 2 => "2021-2022", 3 => "2022-2023", 4 => '2023-2024', 5 => '2024-2025');
                        // print_r($budget_year_arr);
                        ?>
                        <td id="budgeted_year"><? echo create_drop_down("cbo_budgeted_year", 150, $budget_year_arr, '', '1', 'Select Budgeted Year', '', 'budget_show()'); ?></td>
                        <td><? echo create_drop_down("cbo_starting_month", 150, $blank_array, '', 0, '--- Select Month ---', 0, '', 1); ?></td>
                    </tr>

                    <tr>
                        <td colspan="10" align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="15" align="center" class="button_container"><? echo load_submit_buttons($permission, "fnc_yearly_fabric_budget_entry", 0, 0, "reset_form('yearly_sample_fabric_budget_entry_1','','',0);disable_remove();"); ?>
                            <input type="hidden" id="update_id">

                        </td>
                    </tr>

                </table>
                <!-- <table>
					</tr class="button_container">
                        <td></td>
                        <td align="right" ><input style="width:4pc;" type="button" style="display: block;" class="formbutton" id="btn_save" value="Save" name="Save" onclick="fnc_yearly_fabric_budget_entry(0)"></td>
                        <td align="right" ><input style="width:4pc;" type="button" style="display: block;" class="formbutton" id="btn_update" value="Update" name="Update" onclick="fnc_yearly_fabric_budget_entry(1)"></td>
                        <td align="right" ><input style="width:4pc;" type="button" style="display: block;" class="formbutton" id="btn_refresh" value="Refresh" name="Refresh" onclick="fnc_yearly_fabric_budget_entry(4)"></td>
                       
                        
             
                        <tr>
				</table> -->
            </form>
        </fieldset>
    </div>

    <div id="report_container3">

    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>