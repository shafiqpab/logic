<?

/*-------------------------------------------- Comments
Purpose         :   This form will create Yarn Issue Entry
                
Functionality   :   
JS Functions    :
Created by      :   Bilas 
Creation date   :   07-05-2013
Updated by      :   Kausar  
Update date     :   29-10-2013     
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=3 and status_active=1 and is_deleted=0",'company_name','independent_controll');

$YarnIssueValidationBasedOnServiceApproval = return_library_array( "select yarn_iss_with_serv_app, company_name from  variable_order_tracking where variable_list=60 and status_active = 1 and is_deleted = 0 order by id",'company_name','yarn_iss_with_serv_app');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Issue Info", "../", 1, 1, $unicode, 1, 1);

?>

<script>
    <?
    $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][3]) ;
    echo "var field_level_data= ". $data_arr . ";\n";
    ?>

    var permission = '<? echo $permission; ?>';
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
    set_field_level_access(3);


    // popup for booking no ----------------------
    function popuppage_fabbook() {
        if (form_validation('cbo_company_id*cbo_issue_purpose', 'Company Name*Issue Purpose') == false) {
            return;
        }

        var company = $("#cbo_company_id").val();
        var issue_purpose = $("#cbo_issue_purpose").val();
        var basis = $("#cbo_basis").val();

        var page_link = 'requires/yarn_issue_store_update_controller.php?action=fabbook_popup&company=' + company + '&issue_purpose=' + issue_purpose + '&basis=' + basis;
        var title = "K&D Information";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px, height=400px, center=1, resize=0, scrolling=0', '')
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var bookingNumber = this.contentDoc.getElementById("hidden_booking_number").value;

            if (bookingNumber != "") {
                bookingNumber = bookingNumber.split("_");
                freeze_window(5);

                $("#txt_booking_id").val(bookingNumber[0]);
                $("#txt_booking_no").val(bookingNumber[1]);
                

                if (basis == 4) {
                    $("#txt_buyer_job_no").val(bookingNumber[1]);
                    $("#txt_issue_qnty").attr('placeholder', 'Entry');
                    $("#txt_issue_qnty").removeAttr('ondblclick');
                    $("#txt_issue_qnty").removeAttr('readOnly');
                    $("#txt_returnable_qty").removeAttr('readOnly');
                    $("#txt_returnable_qty").attr('placeholder', 'Entry');
                    $("#txt_style_ref").val(bookingNumber[4]);
                    $("#cbo_buyer_name option[value!='0']").remove();
                    $("#cbo_buyer_name").append("<option selected value='" + bookingNumber[2] + "'>" + bookingNumber[3] + "</option>");
                    load_drop_down('requires/yarn_issue_store_update_controller', bookingNumber[0], 'load_drop_down_dyeing_color', 'dyeingColor_td');
                }
                else {
                    $("#cbo_buyer_name").val(bookingNumber[2]);

                    if (issue_purpose == 2 || issue_purpose == 15 || issue_purpose == 38 || issue_purpose == 46 || issue_purpose == 7) {
                        $("#txt_buyer_job_no").val('');
                        $("#txt_style_ref").val('');
                        $("#save_data").val('');
                        $("#all_po_id").val('');
                        $("#txt_issue_qnty").val('');
                        $("#txt_returnable_qty").val('');

                        if(bookingNumber[9]==7 && (bookingNumber[8]==3 || bookingNumber[8]==5)){
                            $("#cbo_knitting_source").val(1);
                            load_drop_down( 'requires/yarn_issue_store_update_controller', 1 +'**'+ company, 'load_drop_down_knit_com', 'knitting_company_td' );
                            $("#cbo_knitting_company").val(bookingNumber[6]);
                        }else{
                            load_drop_down( 'requires/yarn_issue_store_update_controller', bookingNumber[7] +'**'+ company, 'load_drop_down_knit_com', 'knitting_company_td' );
                            $("#cbo_knitting_source").val(bookingNumber[7]);
                            $("#cbo_knitting_company").val(bookingNumber[6]);
                        }

                        load_drop_down( 'requires/yarn_issue_store_update_controller', bookingNumber[6] +'_'+bookingNumber[7], 'load_drop_down_location', 'location_td' );

                        $("#txt_buyer_job_no").val(bookingNumber[3]);
                        if (bookingNumber[5] == 42 || bookingNumber[5] == 114) {
                            $("#txt_issue_qnty").attr('placeholder', 'Entry');
                            $("#txt_issue_qnty").removeAttr('ondblclick');
                            $("#txt_issue_qnty").removeAttr('readOnly');
                            $("#txt_returnable_qty").removeAttr('readOnly');
                            $("#txt_returnable_qty").attr('placeholder', 'Entry');
                        }
                        else {
                            $("#txt_issue_qnty").attr('placeholder', 'Double Click');
                            $("#txt_issue_qnty").attr('ondblclick', 'openmypage_po()');
                            $("#txt_issue_qnty").attr('readOnly', true);
                            $("#txt_returnable_qty").attr('readOnly', true);
                            $("#txt_returnable_qty").attr('placeholder', 'Display');
                        }
                        show_list_view(bookingNumber[0], 'show_yarn_dyeing_list_view', 'requisition_item', 'requires/yarn_issue_store_update_controller', '');
                        load_drop_down('requires/yarn_issue_store_update_controller', bookingNumber[0], 'load_drop_down_dyeing_color', 'dyeingColor_td');
                    }
                    else {
                        $("#txt_buyer_job_no").val(bookingNumber[3]);
                        $("#txt_style_ref").val(bookingNumber[4]);
                        $("#dyeingColor_td").html('<? echo create_drop_down("cbo_dyeing_color", 142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?>');
                    }
                }
                $("#txt_entry_form").val(bookingNumber[5]);
                release_freezing();
            }
        }
    }

    function openmypage_lot() {     
        var yarn_rate_match = $("#yarn_rate_match").val();
        var txt_booking_no = $("#txt_booking_no").val();
        var cbo_basis = $("#cbo_basis").val();
        var issue_purpose = $("#cbo_issue_purpose").val();
        var job_no = $("#txt_buyer_job_no").val();
        if(cbo_basis==1 && issue_purpose==2)
        {
            if (form_validation('cbo_company_id*cbo_basis*cbo_store_name*txt_composition', 'Company Name*Basis*Store Name*Composition') == false)
            {
                return;
            }
        }
        else
        {
            if (form_validation('cbo_company_id*cbo_basis*cbo_store_name', 'Company Name*Basis*Store Name') == false)
            {
                return;
            }
        }       
        
        if(yarn_rate_match==1 && cbo_basis==1 && txt_booking_no=="")
        {
            alert("Select Booking First.");
            return;
        }

        var company = $("#cbo_company_id").val();
        var supplier = $("#cbo_supplier").val();
        var issue_purpose = $("#cbo_issue_purpose").val();
        var cbo_store_name = $("#cbo_store_name").val();
        var txt_composition_id = $("#txt_composition_id").val();
        var txt_composition_percent = $("#txt_composition_percent").val();
        var cbo_yarn_type = $("#cbo_yarn_type").val();
        var cbo_color = $("#cbo_color").val();
        var cbo_yarn_count = $("#cbo_yarn_count").val();
        var page_link = 'requires/yarn_issue_store_update_controller.php?action=yarnLot_popup&company=' + company + '&supplier=' + supplier + '&issue_purpose=' + issue_purpose + '&cbo_store_name=' + cbo_store_name + '&txt_composition_id=' + txt_composition_id + '&txt_composition_percent=' + txt_composition_percent + '&cbo_yarn_type=' + cbo_yarn_type + '&cbo_color=' + cbo_color + '&cbo_yarn_count=' + cbo_yarn_count+ '&yarn_rate_match=' + yarn_rate_match+ '&txt_booking_no=' + txt_booking_no+ '&cbo_basis=' + cbo_basis+ '&issue_purpose=' + issue_purpose+'&job_no=' + job_no;
        var title = "Yarn Lot Search";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px, height=350px, center=1, resize=0, scrolling=0', '')
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var hidden_data = this.contentDoc.getElementById("hidden_prod_id").value;
            var hidden_prod_id = hidden_data.split("**")
            if (hidden_prod_id[0] != "") {
                freeze_window(5);
                $("#txt_prod_id").val(hidden_prod_id[0]);
                get_php_form_data(hidden_prod_id[0] + "**" + issue_purpose + "**" + cbo_store_name + "**" + hidden_prod_id[1], "populate_data_child_from", "requires/yarn_issue_store_update_controller");
                release_freezing();
            }
        }
    }

    function openmypage_requis() {
        if (form_validation('cbo_company_id*cbo_basis', 'Company Name*Basis') == false) {
            return;
        }

        var company = $("#cbo_company_id").val();
        var YarnIssueValidationBasedOnServiceApproval_arr = JSON.parse('<? echo json_encode($YarnIssueValidationBasedOnServiceApproval); ?>');
        if(YarnIssueValidationBasedOnServiceApproval_arr){
            if(YarnIssueValidationBasedOnServiceApproval_arr[company]==1)
            {
                if (form_validation('cbo_knitting_source', 'knitting_source') == false) {
                    return;
                }
            }
        }
        var knitting_source = $("#cbo_knitting_source").val();
        var page_link = 'requires/yarn_issue_store_update_controller.php?action=requis_popup&company=' + company+"&knitting_source="+knitting_source;
        var title = "Yarn Requisition Search";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px, height=350px, center=1, resize=0, scrolling=0', '')
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var hidden_req_no = this.contentDoc.getElementById("hidden_req_no").value;
            if (hidden_req_no != "") {
                hidden_req_no = hidden_req_no.split(",");
                freeze_window(5);

                $('#txt_req_no').val(hidden_req_no[0]);
                $('#hdn_requis_qnty').val(hidden_req_no[3]);
                $('#hidden_p_issue_qnty').val(hidden_req_no[4]);
                $('#cbo_knitting_source').val(hidden_req_no[5]);
                $('#cbo_buyer_name').val(hidden_req_no[2]);
                load_drop_down( 'requires/yarn_issue_store_update_controller', hidden_req_no[5] +'**'+ company, 'load_drop_down_knit_com', 'knitting_company_td' );
                $('#cbo_knitting_company').val(hidden_req_no[6]);
                show_list_view(hidden_req_no[0] + ',' + hidden_req_no[1] + ',' + hidden_req_no[2], 'show_req_list_view', 'requisition_item', 'requires/yarn_issue_store_update_controller', '');
                load_drop_down( 'requires/yarn_issue_store_update_controller', hidden_req_no[6] +'_'+hidden_req_no[5], 'load_drop_down_location', 'location_td' );
                release_freezing();
            }
        }
    }

    function openmypage_po()
    {
        var purpose = $("#cbo_issue_purpose").val();
        var receive_basis = $('#cbo_basis').val();
        var booking_no = $('#txt_booking_no').val();
        var cbo_company_id = $('#cbo_company_id').val();
        var save_data = $('#save_data').val();
        var all_po_id = $('#all_po_id').val();
        var issueQnty = $('#txt_issue_qnty').val();
        var retnQnty = $('#txt_returnable_qty').val();
        var distribution_method = $('#distribution_method_id').val();
        var job_no = $('#job_no').val();
        var txt_lot_no = $('#txt_lot_no').val();
        var txt_prod_id = $('#txt_prod_id').val();
        var req_no = $('#txt_req_no').val();
        var extra_quantity = $('#extra_quantity').val();
        var entry_form = $('#txt_entry_form').val();
        var update_id = $('#update_id').val();

        if (form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose', 'Company*Basis*Issue Purpose') == false) {
            return;
        }
        else if (receive_basis == 1 && (purpose == 1 || purpose == 2 || purpose == 4 || purpose == 12)) {
            if (form_validation('txt_booking_no', 'Booking') == false) {
                return;
            }
        }
        else if (receive_basis == 3) {
            if (form_validation('txt_req_no', 'Requisition. No') == false) {
                return;
            }
        }

        if (receive_basis == 1 && purpose == 2 && job_no == "") {
            alert("Please Select Job From Right Side List View");
            return;
        }

        if (receive_basis == 3 && txt_lot_no == "") {
            alert("Please Select Yarn From Right Side List View");
            return;
        }

        var title = 'PO Info';
        var page_link = 'requires/yarn_issue_store_update_controller.php?receive_basis=' + receive_basis + '&cbo_company_id=' + cbo_company_id + '&booking_no=' + booking_no + '&all_po_id=' + all_po_id + '&save_data=' + save_data + '&issueQnty=' + issueQnty + '&retnQnty=' + retnQnty + '&distribution_method=' + distribution_method + '&job_no=' + job_no + '&issue_purpose=' + purpose + '&req_no=' + req_no + '&extra_quantity=' + extra_quantity + '&txt_prod_id='+ txt_prod_id + '&entry_form=' + entry_form + '&update_id=' + update_id + '&action=po_popup';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=370px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var save_string = this.contentDoc.getElementById("save_string").value;
            var tot_issue_qnty = this.contentDoc.getElementById("tot_grey_qnty").value;
            var tot_retn_qnty = this.contentDoc.getElementById("tot_retn_qnty").value;
            var all_po_id = this.contentDoc.getElementById("all_po_id").value;
            var distribution_method = this.contentDoc.getElementById("distribution_method").value;
            var extra_quantity = this.contentDoc.getElementById("extra_quantity").value;
            $('#save_data').val(save_string);
            $('#txt_issue_qnty').val(tot_issue_qnty);
            $('#txt_returnable_qty').val(tot_retn_qnty);
            $('#all_po_id').val(all_po_id);
            $('#distribution_method_id').val(distribution_method);
            $('#extra_quantity').val(extra_quantity);
        }
    }

    function fn_room_rack_self_box() {
        if ($("#cbo_room").val() != 0)
            disable_enable_fields('txt_rack', 0, '', '');
        else {
            reset_form('', '', 'txt_rack*txt_shelf', '', '', '');
            disable_enable_fields('txt_rack*txt_shelf', 1, '', '');
        }
        if ($("#txt_rack").val() != 0)
            disable_enable_fields('txt_shelf', 0, '', '');
        else {
            reset_form('', '', 'txt_shelf', '', '', '');
            disable_enable_fields('txt_shelf', 1, '', '');
        }
    }

    function generate_report_file(data, action, page) {
        window.open("requires/yarn_issue_store_update_controller.php?data=" + data + '&action=' + action, true);
    }

    function fnc_yarn_issue_entry(operation) {

        if (operation == 4) {

            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            
            var show_val_column = "0";
            var print_with_vat = 0;
            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
            if (r == true) {
                show_val_column = "1";
            }
            else {
                show_val_column = "0";
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print', 'requires/yarn_issue_store_update_controller');

            return;
        }
        else if (operation == 12) {

            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            
            var show_val_column = "0";
            var print_with_vat = 0;
            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
            if (r == true) {
                show_val_column = "1";
            }
            else {
                show_val_column = "0";
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print12', 'requires/yarn_issue_store_update_controller');

            return;
        } 
           
        else if (operation == 10) 
        {
            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }

            var show_val_column = "0";
            var print_with_vat = 0;
            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
            if (r == true) {
                show_val_column = "1";
            }
            else {
                show_val_column = "0";
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print10', 'requires/yarn_issue_store_update_controller');
            return;

        }
        else if (operation == 5) {
            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            var show_val_column = "0";
            var print_with_vat = 1;
            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
            if (r == true) {
                show_val_column = "1";
            }
            else {
                show_val_column = "0";
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print', 'requires/yarn_issue_store_update_controller');

            return;
        }

        /*############## created by foysal ##################*/
        else if (operation == 6) {
            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            var show_val_column = "0";
            var print_with_vat = 1;
            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
            if (r == true) {
                show_val_column = "1";
            }
            else {
                show_val_column = "0";
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print2', 'requires/yarn_issue_store_update_controller');
            return;
        }

        else if (operation == 7) {
            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            var show_val_column = "0";
            var print_with_vat = 1;
            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
            if (r == true) {
                show_val_column = "1";
            }
            else {
                show_val_column = "0";
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print3', 'requires/yarn_issue_store_update_controller');
            return;
        }
        else if (operation == 8) {
            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            var show_val_column = "0";
            var print_with_vat = 1;
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print8', 'requires/yarn_issue_store_update_controller');
            return;
        }

        /*############## created by foysal ##################*/
        else if (operation == 9) {
            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            var show_val_column = "0";
            var print_with_vat = 1;
            var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
            if (r == true) {
                show_val_column = "1";
            }
            else {
                show_val_column = "0";
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat, 'yarn_issue_print5', 'requires/yarn_issue_store_update_controller');
            return;
        }
        else if (operation == 11) {
            if ($("#txt_system_no").val() == "") {
                alert("Please Save First.");
                return;
            }
            var show_val_column = "0";
            var print_with_vat = 0;
            
            if($('#checkbox_organic').prop("checked") == true)
            {
                var organ_print = 1;
            }else {
                var organ_print = 0;
            }
            
            
            
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + $('#update_id_mst').val() + '*' + show_val_column + '*' + print_with_vat+'*'+organ_print, 'yarn_issue_print6', 'requires/yarn_issue_store_update_controller');
            return;
        }
        else {

            if ($("#is_posted_account").val() == 1) {
                alert("Already Posted In Accounting. Save Update Delete Restricted.");
                return;
            }
            var is_approved = $('#is_approved').val();

            if (is_approved == 1) {
                alert("Yarn issue is Approved. So Change Not Allowed");
                return;
            }

            if (form_validation('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_issue_date', 'Company Name*Basis*Issue Purpose*Issue Date') == false) {
                return;
            }
            var current_date = '<? echo date("d-m-Y"); ?>';
            if (date_compare($('#txt_issue_date').val(), current_date) == false) {
                alert("Issue Date Can not Be Greater Than Current Date");
                return;
            }

            var purpose = parseInt($("#cbo_issue_purpose").val());
            if (purpose == 1) {
                if (form_validation('cbo_knitting_source*cbo_knitting_company', 'Knitting Source*Knitting Company') == false) {
                    return;
                }
            }
            else if (purpose == 4 || purpose == 8) {
                if (form_validation('txt_booking_no', 'Fabric Booking No.') == false) {
                    return;
                }
            }
            else if (purpose == 5) {
                if (form_validation('cbo_loan_party', 'Loan Party') == false) {
                    return;
                }
            }

            if (form_validation('cbo_supplier*txt_lot_no*txt_issue_qnty*cbo_store_name', 'Supplier*Lot No*Issue Quantity*Store Name') == false) {
                return;
            }

            if(($('#cbo_basis').val() * 1 == 1) && ($('#cbo_issue_purpose').val() * 1 == 2 ))
            {
                if (form_validation('cbo_dyeing_color', 'Dyeing Color') == false) 
                {
                    return;
                }
            }

            if(($('#cbo_basis').val() * 1 == 1) && ($('#cbo_issue_purpose').val() * 1 == 8 ))
            {
                if (form_validation('cbo_knitting_source*cbo_knitting_company', 'Knitting Source*Knitting Company') == false) 
                {
                    return;
                }
            }

            if (operation == 0) {

            	if($('#txt_current_stock').val()<=0)
              {
                 alert("Current Stock Quantity can not less than Zero");
                 return;
             }

            
        } else if (operation == 1) {
           
        }

        var dataString = 'txt_system_no*cbo_company_id*cbo_basis*cbo_issue_purpose*txt_issue_date*txt_booking_no*txt_booking_id*cbo_location_id*cbo_knitting_source*cbo_knitting_company*cbo_supplier*cbo_store_name*txt_challan_no*cbo_loan_party*cbo_buyer_name*txt_style_ref*txt_buyer_job_no*cbo_sample_type*txt_remarks*txt_req_no*txt_lot_no*cbo_yarn_count*cbo_color*cbo_floor*cbo_room*txt_issue_qnty*txt_returnable_qty*txt_composition*cbo_brand*txt_rack*txt_no_bag*txt_no_cone*txt_weight_per_bag*txt_weight_per_cone*cbo_yarn_type*cbo_dyeing_color*txt_shelf*txt_current_stock*cbo_uom*cbo_item*update_id_mst*update_id*save_data*all_po_id*txt_prod_id*job_no*cbo_ready_to_approved*cbo_supplier_lot*txt_btb_lc_id*extra_quantity*txt_entry_form*hidden_p_issue_qnty*hdn_wo_qnty';

        var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string(dataString, "../");
        freeze_window(operation);
        http.open("POST", "requires/yarn_issue_store_update_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_yarn_issue_entry_reponse;
    }
}

function fnc_yarn_issue_entry_reponse() {
    if (http.readyState == 4) {
        var reponse = trim(http.responseText).split('**');
        release_freezing();
        if (reponse[0] * 1 == 20 * 1) {
            alert(reponse[1]);
            return;
        }
        else if (reponse[0] == 10) {
            show_msg(reponse[0]);
            return;
        }
        else if (reponse[0] == 11) {
            alert(reponse[1]);
            return;
        }else if (reponse[0] == 30) {

            var returnData = reponse[1].split(',');

            alert("You can't delete!!, because of issue return found accross this issue id"+ "\n"+ "Take a look at bellow return number and qty" +"\n\n"+ returnData[0] + "\n" + returnData[1]);
            return;
        }
        else if (reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2) {
            show_msg(reponse[0]);
            $("#txt_system_no").val(reponse[1]);
            $("#update_id_mst").val(reponse[2]);
            disable_enable_fields('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no*cbo_supplier*cbo_knitting_source*cbo_knitting_company*cbo_location_id', 1, "", "");
            $("#tbl_child").find('select,input:not([name="txt_req_no"])').val('');
            $("#save_data").val('');
            $("#all_po_id").val('');
            $("#distribution_method_id").val('');
            show_list_view(reponse[2], 'show_dtls_list_view', 'list_container_yarn', 'requires/yarn_issue_store_update_controller', '');
            set_button_status(0, permission, 'fnc_yarn_issue_entry', 1, 1);
        }
    }
}

function active_inactive() {
    var basis = parseInt($("#cbo_basis").val());
    var purpose = parseInt($("#cbo_issue_purpose").val());
    if (form_validation('cbo_basis', 'Basis') == false) {
        $("#cbo_issue_purpose").val(0);
        return;
    }

    $('#tbl_child').find('input,select').val("");

    if (basis == 1 || basis == 4)
    {
        $("#txt_booking_no").val('');
        $("#txt_req_no").val('');
        $("#txt_lot_no").val('');
        $("#requisition_item").html('');

        disable_enable_fields('txt_req_no', 1, "", "");
        disable_enable_fields('txt_booking_no*txt_lot_no*cbo_sample_type*cbo_buyer_name', 0, "", "");
    }
        else if (basis == 3) //requisition
        {
            $("#txt_booking_no").val('');

            disable_enable_fields('txt_req_no', 0, "", ""); // disable false
            disable_enable_fields('txt_booking_no*txt_lot_no*cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
        }
        else //idependent
        {
            $("#txt_booking_no").val('');
            $("#txt_req_no").val('');
            $("#txt_lot_no").val('');
            $("#requisition_item").html('');

            disable_enable_fields('txt_lot_no*cbo_sample_type*cbo_buyer_name', 0, "", ""); // disable false
            disable_enable_fields('txt_booking_no*txt_req_no', 1, "", ""); // disable false
        }

        if (purpose == 2) {
            document.getElementById('knit_source').innerHTML = 'Dyeing Source';
            document.getElementById('knit_com').innerHTML = 'Dyeing Company';
        }
        else {
            document.getElementById('knit_source').innerHTML = 'Knitting Source';
            document.getElementById('knit_com').innerHTML = 'Knitting Company';
        }

        if (purpose == 5) {
            $('#cbo_loan_party').removeAttr('disabled', 'disabled');
            $('#loanParty_td').css('color', 'blue');
        }
        else {
            $('#cbo_loan_party').attr('disabled', 'disabled');
            $('#loanParty_td').css('color', 'black');
        }

        if (basis == 4) {
            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
            $("#txt_returnable_qty").removeAttr('readOnly');
            $("#txt_returnable_qty").attr('placeholder', 'Entry');
        }
        else {
            $("#txt_issue_qnty").attr('placeholder', 'Double Click');
            $("#txt_issue_qnty").attr('ondblclick', 'openmypage_po()');
            $("#txt_issue_qnty").attr('readOnly', true);
            $("#txt_returnable_qty").attr('readOnly', true);
            $("#txt_returnable_qty").attr('placeholder', 'Display');
        }
        //$issue_basis=array(1=>"Booking",2=>"Independent");
        //$yarn_issue_purpose=array(1=>"Knitting",2=>"Yarn Dyeing",3=>"Sales",4=>"Sample",5=>"Loan",6=>"Sample-material", 7=>"Yarn Test", 8=>"Sample-No Order");
        if (basis == 1 && (purpose == 1 || purpose == 12 || purpose == 26 || purpose == 29)) {
            disable_enable_fields('txt_booking_no*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
        }
        else if (basis == 1 && purpose == 2) {
            disable_enable_fields('cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
            $("#cbo_sample_type").val(0);
            $("#cbo_buyer_name").val(0);
        }
        else if (basis == 1 && (purpose == 3 || purpose == 5 || purpose == 15 || purpose == 30 || purpose == 38 || purpose == 39)) {
            disable_enable_fields('cbo_sample_type', 1, "", ""); // disable true
            disable_enable_fields('cbo_buyer_name', 0, "", ""); // disable false
            $("#cbo_sample_type").val(0);
        }
        else if (basis == 1 && purpose == 4) {
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('cbo_buyer_name', 1, "", ""); // disable true
        }
        else if (basis == 1 && purpose == 8) {
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_knitting_source*cbo_knitting_company*cbo_buyer_name', 0, "", ""); // disable false
            disable_enable_fields('cbo_buyer_name', 1, "", ""); // disable true
            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
            $("#txt_returnable_qty").removeAttr('readOnly');
            $("#txt_returnable_qty").attr('placeholder', 'Entry');
            
        }
        else if (basis == 2 && (purpose == 1 || purpose == 29)) {
            disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name', 1, "", ""); // disable true
        }
        else if (basis == 2 && purpose == 12) {
            disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name', 1, "", ""); // disable true

            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
        }
        else if (basis == 2 && purpose == 2) {
            disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name', 1, "", ""); // disable true
        }
        else if (basis == 2 && (purpose == 3 || purpose == 5 || purpose == 15 || purpose == 26 || purpose == 30 || purpose == 38 || purpose == 39)) {
            disable_enable_fields('cbo_buyer_name', 0, "", ""); // disable false
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_knitting_source*cbo_knitting_company', 1, "", ""); // disable true

            if (purpose == 15) {
                disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            }

            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
            $("#txt_returnable_qty").removeAttr('readOnly');
            $("#txt_returnable_qty").attr('placeholder', 'Entry');
        }
        else if (basis == 2 && purpose == 2) {
            disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_other_party*cbo_buyer_name', 1, "", ""); // disable true
        }
        else if (basis == 2 && purpose == 4) {
            disable_enable_fields('cbo_sample_type*cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('txt_booking_no', 1, "", ""); // disable true
        }
        else if (basis == 2 && purpose == 6) {
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 1, "", ""); // disable true
            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
            $("#txt_returnable_qty").removeAttr('readOnly');
            $("#txt_returnable_qty").attr('placeholder', 'Entry');
        }
        else if (basis == 2 && purpose == 7) {
            disable_enable_fields('cbo_sample_type*txt_booking_no*cbo_buyer_name*cbo_buyer_name', 1, "", ""); // disable true
            disable_enable_fields('cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
            $("#txt_returnable_qty").removeAttr('readOnly');
            $("#txt_returnable_qty").attr('placeholder', 'Entry');
        }
        else if (basis == 2 && purpose == 8) {
            disable_enable_fields('cbo_sample_type*cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            //disable_enable_fields( 'cbo_loan_party', 1, "", "" ); // disable true
            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
            $("#txt_returnable_qty").removeAttr('readOnly');
            $("#txt_returnable_qty").attr('placeholder', 'Entry');
        }
        else if (basis == 2 && purpose == 10) {
            disable_enable_fields('cbo_buyer_name*cbo_knitting_source*cbo_knitting_company', 0, "", ""); // disable false
            disable_enable_fields('cbo_sample_type', 1, "", ""); // disable true
            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
            $("#txt_returnable_qty").removeAttr('readOnly');
            $("#txt_returnable_qty").attr('placeholder', 'Entry');
        }
        else if (basis == 4 && (purpose == 1 && purpose == 2 || purpose == 12 || purpose == 26 || purpose == 29 || purpose == 3 || purpose == 5 || purpose == 15 || purpose == 30 || purpose == 38 || purpose == 39)) {
            disable_enable_fields('cbo_sample_type*cbo_buyer_name', 1, "", ""); // disable true
        }
        else if (basis == 4 && purpose == 4 && purpose == 8) {
            disable_enable_fields('cbo_sample_type', 0, "", ""); // disable false
        }

        if (purpose == 3) {
            load_drop_down('requires/yarn_issue_store_update_controller', document.getElementById('cbo_company_id').value + '_' + 0, 'load_drop_down_buyer', 'buyer_td_id');
        }
        else {
            load_drop_down('requires/yarn_issue_store_update_controller', document.getElementById('cbo_company_id').value + '_' + 1, 'load_drop_down_buyer', 'buyer_td_id');
        }
    }

    function open_mrrpopup() {
        if (form_validation('cbo_company_id', 'Company Name') == false) {
            return;
        }
        var company = $("#cbo_company_id").val();
        var page_link = 'requires/yarn_issue_store_update_controller.php?action=mrr_popup&company=' + company;
        var title = "Search Issue Popup";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0', '')
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sysNumber = this.contentDoc.getElementById("hidden_sys_number").value.split(","); // system number
            $("#txt_system_no").val(sysNumber[0]);
            $("#is_approved").val(sysNumber[1]);
            $("#is_posted_account").val(sysNumber[6]);
            if (sysNumber[6] == 1) document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting.";
            else                document.getElementById("accounting_posted_status").innerHTML = "";

            // master part call here
            get_php_form_data(sysNumber[2], "populate_data_from_data", "requires/yarn_issue_store_update_controller");

            if (sysNumber[3] == 1) {
                $("#cbo_buyer_name option[value!='0']").remove();
                $("#cbo_buyer_name").append("<option selected value='" + sysNumber[4] + "'>" + sysNumber[5] + "</option>");
            }
            
            

            //list view call here
            show_list_view(sysNumber[2], 'show_dtls_list_view', 'list_container_yarn', 'requires/yarn_issue_store_update_controller', '');
            disable_enable_fields('cbo_company_id*cbo_basis*cbo_issue_purpose*txt_booking_no*cbo_supplier*cbo_knitting_source*cbo_knitting_company*cbo_location_id', 1, "", "");
            set_button_status(0, permission, 'fnc_yarn_issue_entry', 1, 1);
        }
    }

    //form reset/refresh function here
    function fnResetForm() {
        $("#tbl_master").find('input').attr("disabled", false);
        $("#dyeingColor_td").html('<? echo create_drop_down("cbo_dyeing_color", 142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?>');
        $("#tbl_master").find('input,select').attr("disabled", false);
        set_button_status(0, permission, 'fnc_yarn_issue_entry', 1);
        reset_form('yarn_issue_1', 'list_container_yarn*requisition_item', '', '', '', 'cbo_uom');
        document.getElementById("accounting_posted_status").innerHTML = "";
    }

    function generate_report_req(req_id) {
        if ($("#txt_system_no").val() == "") {
            alert("Please Save First.");
            return;
        }
        else if ($("#txt_req_no").val() == "") {
            alert("Please Select Requisition Number.");
            return;
        }
        else {
            if ($("#cbo_basis").val() == 3) {
                generate_report_file($("#cbo_company_id").val() + '_' + req_id + '_' + $('#txt_system_no').val(), 'requisition_print', 'requires/yarn_issue_store_update_controller');
            }
            else {
                alert("Basis is not Requisition.");
                return;
            }
        }
    }

    function generate_report_widthout_prog(i) {
        if ($("#txt_system_no").val() == "") {
            alert("Please Save First.");
            return;
        }

        if ($("#cbo_basis").val() == 3) {
            var report_title = $("div.form_caption").html();
            print_report($('#cbo_company_id').val() + '*' + $('#txt_system_no').val() + '*' + report_title + '*' + $('#txt_booking_id').val() + '*' + $('#is_approved').val() + '*' + i, "yarn_issue_print", "requires/yarn_issue_store_update_controller")
        }
        else {
            alert("Basis is not Requisition.");
            return;
        }
    }

    function load_list_view(str) {
        if (str == "") {
            $('#requisition_item').html('');
            return;
        }
        show_list_view(str + ',' + $("#cbo_company_id").val() + ',' + $("#cbo_buyer_name").val(), 'show_req_list_view', 'requisition_item', 'requires/yarn_issue_store_update_controller', '');
    }

    function load_supplier() {
        var issue_purpose = $("#cbo_issue_purpose").val();
        var company = $("#cbo_company_id").val();

        if (form_validation('cbo_company_id', 'Company') == false) {
            $("#cbo_issue_purpose").val(0);
            return;
        }

        if (issue_purpose == 5) {
            load_drop_down('requires/yarn_issue_store_update_controller', company, 'load_drop_down_supplier_loan', 'loanParty');
        }

    }

    function load_purpose() {
        var cbo_basis = $("#cbo_basis").val();

        if (form_validation('cbo_company_id', 'Company') == false) {
            return;
        }
        load_drop_down('requires/yarn_issue_store_update_controller', cbo_basis+'_0', 'load_drop_down_purpose', 'issue_purpose_td');
    }

    function change_basis(purpose_id) {
        var purpose_arr = [3, 5, 12, 26, 29, 30,39];
        var selectedValue = purpose_id*1;
        if(jQuery.inArray(selectedValue, purpose_arr) !== -1)
        {
            $("#cbo_basis").val('2');
            $("#txt_issue_qnty").attr('placeholder', 'Entry');
            $("#txt_issue_qnty").removeAttr('ondblclick');
            $("#txt_issue_qnty").removeAttr('readOnly');
        }else{
            if(purpose_id == 8){
                $("#txt_issue_qnty").attr('placeholder', 'Entry');
                $("#txt_issue_qnty").removeAttr('ondblclick');
                $("#txt_issue_qnty").removeAttr('readOnly');
            }else{
                $("#txt_issue_qnty").attr('placeholder', 'Double Click');
                $("#txt_issue_qnty").attr('ondblclick', 'openmypage_po()');
            }
        }        
    }

    function fn_empty_lot(str_id) {
        var receive_basis = $('#cbo_basis').val();
        if (receive_basis == 1 || receive_basis == 3) {
            var prod_id = $('#txt_prod_id').val();
            if (str_id > 0 && prod_id != "") {
                get_php_form_data(prod_id + '**' + str_id, "populate_req_store_data", "requires/yarn_issue_store_update_controller");
            }
            else {
                $('#txt_current_stock').val('');
            }

        }
        else {
            $('#txt_lot_no').val("");
            $('#txt_prod_id').val("");
            $('#txt_issue_qnty').val("");
            $('#txt_current_stock').val("");
        }

    }

    function openmypage_btb_selection() {

        if (form_validation('cbo_company_id*txt_lot_no', 'Company*Lot No.') == false) {
            return;
        }
        var comany_name = $("#cbo_company_id").val();
        var lot_no = $("#txt_lot_no").val();
        var supplier = $("#cbo_supplier").val();
        var update_id_mst = $("#update_id_mst").val();
        var page_link = 'requires/yarn_issue_store_update_controller.php?action=btb_selection_popup&lot_no=' + lot_no + '&supplier=' + supplier + '&comany_name=' + comany_name + '&update_id_mst=' + update_id_mst;
        var title = "Search BTB Selection Popup";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=370px,center=1,resize=0,scrolling=0', '')
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var btb_id = this.contentDoc.getElementById("hidden_btb_id").value;
            var btb_lc_no = this.contentDoc.getElementById("hidden_btb_lc_no").value;

            $('#txt_btb_selection').val(btb_lc_no);
            $('#txt_btb_lc_id').val(btb_id);

        }

    }

    function independence_basis_controll_function(data)
    {
        var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
        $("#cbo_basis option[value='2']").show();
        $("#cbo_basis").val(0);
        if(independent_control_arr && independent_control_arr[data]==1)
        {
            $("#cbo_basis option[value='2']").hide();
        }
    }

</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="left">
        <? echo load_freeze_divs("../", $permission); ?><br/>
        <form name="yarn_issue_1" id="yarn_issue_1" autocomplete="off">
            <div style="width:980px; float:left; position:relative" align="center">
                <table width="80%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="100%" align="center" valign="top">
                            <fieldset style="width:980px;">
                                <legend>Yarn Issue</legend>
                                <br/>
                                <fieldset style="width:950px;">
                                    <table width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                                        <tr>
                                            <td colspan="6" align="center"><b>System ID</b>
                                                <input type="text" name="txt_system_no" id="txt_system_no"
                                                class="text_boxes" style="width:160px"
                                                placeholder="Double Click To Search" onDblClick="open_mrrpopup()"
                                                readonly/>&nbsp;&nbsp;
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="120" align="right" class="must_entry_caption">Company Name</td>
                                            <td width="170">
                                                <?
                                                echo create_drop_down("cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_issue_store_update_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/yarn_issue_store_update_controller',this.value+'_'+1, 'load_drop_down_buyer', 'buyer_td_id' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/yarn_issue_store_update_controller' ); independence_basis_controll_function(this.value);load_room_rack_self_bin('requires/yarn_issue_store_update_controller*1', 'store','store_td', this.value,'','','','','','','','fn_empty_lot(this.value);');");
                                                ?>
                                            </td>
                                            <td width="120" align="right" class="must_entry_caption">Basis</td>
                                            <td width="160" id="receive_baisis_td">
                                                <?
                                                echo create_drop_down("cbo_basis", 170, $issue_basis, "", 1, "-- Select Basis --", $selected, "active_inactive();load_purpose();", "", "");
                                                ?>
                                            </td>
                                            <td width="120" align="right" class="must_entry_caption">Issue Purpose</td>
                                            <td  id="issue_purpose_td">
                                                <?
                                                echo create_drop_down("cbo_issue_purpose", 170, $blank_array, "", 1, "-- Select Purpose --", $selected, "", "", "");
                                                //echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "load_supplier();active_inactive();change_basis(this.value)", "", "1,2,3,4,5,6,7,8,12,15,16,26,29,30,38,39,40,45,46","","","");//9,10,11,13,14,16,27,28,32,33
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" class="must_entry_caption">Issue Date</td>
                                            <td>
                                                <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" placeholder="Select Date" value="<? echo date('d-m-Y');?>" readonly/>
                                            </td>
                                            <td align="right">Fab Booking No</td>
                                            <td >
                                                <input name="txt_booking_no" id="txt_booking_no" class="text_boxes"
                                                style="width:160px" placeholder="Double Click to Search"
                                                onDblClick="popuppage_fabbook();" readonly/>
                                                <input type="hidden" name="txt_booking_id" id="txt_booking_id"/>
                                                <input type="hidden" name="txt_entry_form" id="txt_entry_form"/>
                                            </td>
                                            <td align="right" id="knit_source">Knitting Source</td>
                                            <td width="170">
                                                <?
                                                echo create_drop_down("cbo_knitting_source", 170, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/yarn_issue_store_update_controller', this.value+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_knit_com', 'knitting_company_td' );", "", "1,3");
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" id="knit_com"> Issue To</td>
                                            <td id="knitting_company_td">
                                                <?
                                                echo create_drop_down("cbo_knitting_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
                                                ?>
                                            </td>
                                            <td align="right">Location</td>
                                            <td id="location_td">
                                                <?
                                                echo create_drop_down("cbo_location_id", 170, $blank_array, "", 1, "-- Select Location --", "", "");
                                                ?>
                                            </td>
                                            <td align="right" class="must_entry_caption" id="supplier_td">Supplier</td>
                                            <td id="supplier">
                                                <?
                                                echo create_drop_down("cbo_supplier", 170, $blank_array, "", 1, "-- Select --", 0, "", 1);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td  align="right">Challan/Program No</td>
                                            <td >
                                                <input type="text" name="txt_challan_no" id="txt_challan_no"
                                                class="text_boxes" style="width:160px" placeholder="Entry">
                                            </td>
                                            <td id="loanParty_td" align="right">Loan Party</td>
                                            <td id="loanParty">
                                                <?
                                                echo create_drop_down("cbo_loan_party", 170, $blank_array, "", 1, "--- Select Party ---", $selected, "", 1);
                                                ?>
                                            </td>
                                            <td  align="right">Sample Type</td>
                                            <td ><?
                                            echo create_drop_down("cbo_sample_type", 170, "select id,sample_name from lib_sample where status_active=1 and is_deleted=0 order by sample_name", "id,sample_name", 1, "-- Select --", $selected, "", "", "");
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Buyer Name</td>
                                        <td id="buyer_td_id">
                                            <?
                                            echo create_drop_down("cbo_buyer_name", 170, $blank_array, "", 1, "-- Select Buyer --", 0, "", 1);
                                            ?>
                                        </td>
                                        <td align="right">Style Reference</td>
                                        <td>
                                            <input type="text" name="txt_style_ref" id="txt_style_ref"
                                            class="text_boxes" style="width:160px" readonly
                                            placeholder="Display"/>
                                        </td>
                                        <td align="right">Buyer Job No</td>
                                        <td>
                                            <input type="text" name="txt_buyer_job_no" id="txt_buyer_job_no"
                                            class="text_boxes" style="width:160px" readonly
                                            placeholder="Display"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Remarks</td>
                                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks"
                                         class="text_boxes" style="width:460px"
                                         placeholder="Entry"/></td>
                                         <td align="right">Ready to Approve</td>
                                         <td>
                                            <?
                                            echo create_drop_down("cbo_ready_to_approved", 172, $yes_no, "", 1, "-- Select--", 2, "", "", "");
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">&nbsp;</td>
                                        <td colspan="3">&nbsp;</td>
                                        <td align="right">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </fieldset>
                            <br/>
                            <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                                <tr>
                                    <td width="49%" valign="top">
                                        <fieldset style="width:950px;">
                                            <legend>New Issue Item</legend>
                                            <table width="100%" cellspacing="2" cellpadding="0" border="0">
                                                <tr>
                                                    <td width="110" align="right">Requisition. No</td>
                                                    <td>
                                                        <input type="text" name="txt_req_no" id="txt_req_no"
                                                        class="text_boxes" onDblClick="openmypage_requis()"
                                                        placeholder="Browse or Write" style="width:150px;"
                                                        onBlur="load_list_view(this.value);"/>
                                                    </td>
                                                    <td align="right">Composition</td>
                                                    <td>
                                                        <input type="text" name="txt_composition" id="txt_composition" class="text_boxes" style="width:130px;" placeholder="Display" readonly>
                                                        <input type="hidden" name="txt_composition_id" id="txt_composition_id">
                                                        <input type="hidden" name="txt_composition_percent" id="txt_composition_percent">
                                                    </td>


                                                    <td align="right">UOM</td>
                                                    <td><? echo create_drop_down("cbo_uom", 162, $unit_of_measurement, "", 1, "--Select--", $selected, "", 1); ?></td>
                                                    <td align="right" class="must_entry_caption">Store Name</td>
                                                    <td id="store_td">
                                                        <?
                                                        echo create_drop_down("cbo_store_name", 162, $blank_array, "", 1, "-- Select Store --", 0, "fn_empty_lot(this.value);", 0);
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="110" align="right" class="must_entry_caption">Lot No</td>
                                                    <td>
                                                        <input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" onDblClick="openmypage_lot()"
                                                        placeholder="Double Click" style="width:150px;" readonly/>
                                                        <input type="hidden" name="txt_prod_id" id="txt_prod_id" readonly/>
                                                    </td>
                                                    <td align="right">Weight per Bag</td>
                                                    <td>
                                                        <input name="txt_weight_per_bag" id="txt_weight_per_bag" class="text_boxes_numeric" type="text" style="width:130px;" placeholder="Entry"/>
                                                    </td>
                                                    <td align="right">Yarn Type</td>
                                                    <td><? echo create_drop_down("cbo_yarn_type", 162, $yarn_type, "", 1, "--Select--", 0, "", 1); ?></td>
                                                    <td align="right">Floor</td>
                                                    <td id="floor_td">
                                                        <? echo create_drop_down( "cbo_floor", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="right" class="must_entry_caption">Issue Qty.</td>
                                                    <td>
                                                        <input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:150px;"
                                                        placeholder="Double Click" readonly onDblClick="openmypage_po()"/>
                                                        <input type="hidden" name="hidden_p_issue_qnty" id="hidden_p_issue_qnty" readonly/>
                                                        <input type="hidden" name="extra_quantity" id="extra_quantity" readonly/>
                                                        <input type="hidden" name="hdn_requis_qnty" id="hdn_requis_qnty" readonly/>
                                                        <input type="hidden" name="hdn_wo_qnty" id="hdn_wo_qnty" readonly/>
                                                    </td>
                                                    <td align="right">Wght @ Cone</td>
                                                    <td><input class="text_boxes_numeric" name="txt_weight_per_cone"
                                                     id="txt_weight_per_cone" type="text" style="width:130px;"
                                                     placeholder="Entry"/></td>
                                                     <td align="right">Color</td>
                                                     <td>
                                                        <select id="cbo_color" name="cbo_color" class="combo_boxes" style="width:162px;" disabled="disabled">
                                                            <option value="0">--Select--</option>
                                                        </select>
                                                    </td>
                                                    <td align="right">Room</td>
                                                    <td id="room_td">
                                                        <?
                                                        echo create_drop_down( "cbo_room", 162,$blank_array,"", 1, "--Select--", 0, "",0 );
                                                        ?>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Current Stock</td>
                                                    <td>
                                                        <input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric"
                                                        style="width:150px;" placeholder="Display" readonly/>
                                                    </td>
                                                    <td align="right">No. Of Cone</td>
                                                    <td>
                                                        <input type="text" name="txt_no_cone" id="txt_no_cone" class="text_boxes_numeric" style="width:130px;" placeholder="Entry"/>
                                                    </td>
                                                    <td align="right">Brand</td>
                                                    <td>
                                                        <?
                                                        echo create_drop_down("cbo_brand", 162, "select id,brand_name from lib_brand", "id,brand_name", 1, "--Select--", "", "", 1);
                                                        ?>
                                                    </td>
                                                    <td align="right">Rack</td>
                                                    <td id="rack_td">
                                                     <?  echo create_drop_down( "txt_rack", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>

                                                        <!-- <select id="txt_rack" name="txt_rack" class="combo_boxes " style="width:162px">
                                                            <option value="">--Select--</option>
                                                        </select> -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="right">No. Of Bag</td>
                                                    <td><input type="text" name="txt_no_bag" id="txt_no_bag"
                                                     class="text_boxes_numeric" style="width:150px;"
                                                     placeholder="Entry"/></td>
                                                     <td align="right">Dyeing Color</td>
                                                     <td id="dyeingColor_td"><? echo create_drop_down("cbo_dyeing_color", 142, $blank_array, "", 1, "-- Select --", 0, "", 0); ?></td>
                                                     <td align="right">Yarn Count</td>
                                                     <td>
                                                        <?
                                                        echo create_drop_down("cbo_yarn_count", 162, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "--Select--", 0, "", 1);
                                                        ?>
                                                    </td>
                                                    <td align="right">Shelf</td>
                                                    <td id="shelf_td">
                                                        <?  echo create_drop_down( "txt_shelf", 162,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                                        <!-- <select id="txt_shelf" name="txt_shelf" class="combo_boxes " style="width:162px">
                                                            <option value="">--Select--</option>
                                                        </select> -->
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td align="right">Returnable Qty.</td>
                                                    <td><input type="text" name="txt_returnable_qty"
                                                     id="txt_returnable_qty" class="text_boxes_numeric"
                                                     placeholder="Display" style="width:150px;" readonly/>
                                                 </td>
                                                 <td align="right">Supplier</td>
                                                 <td>
                                                    <?
                                                    echo create_drop_down("cbo_supplier_lot", 142, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0", "id,supplier_name", 1, "-- Display --", 0, "", 1);
                                                    ?>
                                                </td>
                                                <td align="right">BTB Selection</td>
                                                <td>
                                                    <input type="text" class="text_boxes" id="txt_btb_selection"
                                                    name="txt_btb_selection" value=""
                                                    onDblClick="openmypage_btb_selection()"
                                                    placeholder="Double Click" style="width:150px;" readonly>
                                                    <input type="hidden" class="text_boxes" id="txt_btb_lc_id"
                                                    name="txt_btb_lc_id" value="">
                                                </td>
                                                <td align="right">Using Item</td>
                                                <td>
                                                    <?
                                                    echo create_drop_down("cbo_item", 162, $using_item_arr, "", 1, "--Select--", "", "", 0);
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                        <table cellpadding="0" cellspacing="1" width="100%">
                            <tr>
                                <td colspan="6" align="center"></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="6" valign="middle" class="button_container">
                                    <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                                    <!-- details table id for update -->
                                    <input type="hidden" id="is_approved" name="is_approved" value="" readonly/>
                                    <input type="hidden" id="update_id_mst" name="update_id_mst" readonly/>
                                    <input type="hidden" id="update_id" name="update_id" readonly/>
                                    <input type="hidden" name="save_data" id="save_data" readonly/>
                                    <input type="hidden" name="all_po_id" id="all_po_id" readonly/>
                                    <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly/>
                                    <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                                    <!--Check Posted in account-->
                                    <input type="hidden" name="job_no" id="job_no" readonly/>
                                    <input type="hidden" name="yarn_rate_match" id="yarn_rate_match" readonly/>
                                    <!--For Basis Bokking and Yarn Dyeing Purpose-->&nbsp;
                                    <? echo load_submit_buttons($permission, "fnc_yarn_issue_entry", 0, 0, "fnResetForm()", 1); ?>

                                    <input type="button" name="print" id="Printt1" value="Print" onClick="fnc_yarn_issue_entry(4)" style="width: 80px; display:none;" class="formbutton">

                                    <input type="button" name="print_vat" id="print_vat1" value="Print2"
                                    onClick="fnc_yarn_issue_entry(6)" style="width:80px;display:none;"
                                    class="formbutton"/>
                                    <input type="button" name="print_vat" id="print_vat2" value="Print3"
                                    onClick="fnc_yarn_issue_entry(7)" style="width:80px;display:none;"
                                    class="formbutton"/>
                                    
                                    <input type="button" name="print_vat" id="print_vat4" value="Print4"
                                    onClick="fnc_yarn_issue_entry(10)" style="width:80px;display:none;"
                                    class="formbutton"/>


                                    <input type="button" name="print_vat" id="print_vat3" value="Print With VAT"
                                    onClick="fnc_yarn_issue_entry(5)" style="width:100px;display:none;"
                                    class="formbutton"/>

                                    <input type="button" name="print_vat" id="print_vat9" value="Print7"
                                    onClick="fnc_yarn_issue_entry(12)" style="width:100px;display:none;"
                                    class="formbutton"/>
                                    
                                    <input type="button" name="print_vat" id="print_vat8" value="Print Outbound"
                                    onClick="fnc_yarn_issue_entry(8)" style="width:100px;display:none;"
                                    class="formbutton"/>
                                    <input type="button" name="print_vat" id="print_vat9" value="Print5"
                                    onClick="fnc_yarn_issue_entry(9)" style="width:100px;display:none;"
                                    class="formbutton"/>                                   
                                    
                                    <input type="button" name="search" id="search1" value="Requisition Details"
                                    onClick="generate_report_req(document.getElementById('txt_req_no').value)"
                                    style="width:130px;display:none;" class="formbutton"/>
                                    
                                    <input type="button" name="without_prog" id="without_prog1"
                                    value="Without Program" onClick="generate_report_widthout_prog(1)"
                                    style="width:130px;display:none;" class="formbutton"/>
                                    
                                    <input type="button" name="print_6" id="print_6" value="Print6"
                                    onClick="fnc_yarn_issue_entry(11)" style="width:100px;display:none;"
                                    class="formbutton"/><span style="font-weight:bold;display:none;" id="organic_check"> ORGANIC <input type="checkbox" id="checkbox_organic"></span> 
                                   
                                    <div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <div style="width:970px;" id="list_container_yarn"></div>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
    <div style="float:left; position:relative; margin-left:15px" align="left" id="requisition_item"></div>
</form>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
