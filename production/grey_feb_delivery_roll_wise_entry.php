<?
/*-------------------------------------------- Comments
Purpose         :   This form will create Grey Fabric Delivery Roll Wise
Functionality   :
JS Functions    :
Created by      :   Fuad
Creation date   :   27-01-2015
Updated by      :   Zaman || Zaman
Update date     :   22.10.2015 || 10.12.2019
QC Performed BY :
QC Date         :
Comments        :
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

    require_once('../includes/common.php');
    extract($_REQUEST);

    $_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
    echo load_html_head_contents("Grey Fabric Delivery Roll Wise", "../", 1, 1, $unicode, '', '');
    ?>
    <script>

        if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
        var permission = '<? echo $permission; ?>';
        var scanned_barcode = new Array();
        <?
        $scanned_barcode_array = array();
    /*$scanned_barcode_data = sql_select("select id, barcode_no as BARCODE_NO from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0");
    foreach ($scanned_barcode_data as $row) {
        $scanned_barcode_array[] = $row['BARCODE_NO'];
    }
    unset($scanned_barcode_data);

    $jsscanned_barcode_array = json_encode($scanned_barcode_array);
    echo "scanned_barcode = " . $jsscanned_barcode_array . ";\n";*/

    $composition_arr = array(); $constructtion_arr = array();
    $sql_deter = "select a.id as ID, a.construction as CONSTRUCTION, b.copmposition_id as COPMPOSITION_ID, b.percent as PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
    $data_array = sql_select($sql_deter);
    foreach ($data_array as $row) {
        $constructtion_arr[$row['ID']] = $row['CONSTRUCTION'];
        $composition_arr[$row['ID']] .= $composition[$row['COPMPOSITION_ID']] . " " . $row['PERCENT'] . "% ";
    }
    unset($data_array);
    $jsconstructtion_arr = json_encode($constructtion_arr);
    echo "var constructtion_arr = " . $jsconstructtion_arr . ";\n";

    $jscomposition_arr = json_encode($composition_arr);
    echo "var composition_arr = " . $jscomposition_arr . ";\n";

    ?>
    function load_scanned_barcode()
    {
        scanned_barcode = new Array();
        var scanned_barcode_nos = trim(return_global_ajax_value('', 'load_scanned_barcode_nos', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
        scanned_barcode = eval(scanned_barcode_nos);

        set_button_status(0, permission, 'fnc_grey_delivery_roll_wise', 1);
    }

    function openmypage_challan()
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/grey_feb_delivery_roll_wise_entry_controller.php?action=challan_popup', 'Challan Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0', '')
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var hidden_data = this.contentDoc.getElementById("hidden_data").value;   //challan Id and Number

            if (hidden_data != "") {
                fnc_reset_form();
                var challan_data = hidden_data.split("**");
                $('#update_id').val(challan_data[0]);
                $('#txt_challan_no').val(challan_data[1]);
                $('#txt_delivery_date').val(challan_data[2]);
                $('#cbo_company_id').val(challan_data[3]);
                $('#cbo_location_id').val(challan_data[4]);
                $('#cbo_knitting_source').val(challan_data[5]);
                $('#knit_company_id').val(challan_data[6]);
                $('#txt_knit_company').val(challan_data[7]);
                $('#txt_remarks').val(challan_data[8]);
                $('#txt_floor_no').val(challan_data[9]);
                $('#cbo_barcode_type').val(challan_data[10]).attr('disabled', 'disabled');
                $('#txt_attention').val(challan_data[11]);

                get_php_form_data( challan_data[3], 'company_wise_report_button_setting','requires/grey_feb_delivery_roll_wise_entry_controller' );

                var floor_name = trim(return_global_ajax_value(challan_data[9], 'populate_floor_ids_to_name', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
                $('#txt_floor_name').val(floor_name);
                var html = trim(return_global_ajax_value(challan_data[0], 'populate_barcode_data_update', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
                if (html != "") {
                    $("#scanning_tbl tbody").html(html);
                    var num_row = $('#scanning_tbl tbody tr').length;
                    $('#txt_tot_row').val(num_row);
                }
                var proQtyTotal = 0;
                var rejectQtyTotal = 0;
                var qcQntyTotal = 0;
                var qntyInPcsTotal = 0;
                $("#scanning_tbl").find('tbody tr').each(function () {
                    proQtyTotal += $(this).find('td:nth-child(23)').html() * 1;
                    rejectQtyTotal += $(this).find('td:nth-child(24)').html() * 1;
                    qntyInPcsTotal += $(this).find('td:nth-child(25)').html() * 1;
                    qcQntyTotal += $(this).find('input[name="currentDelivery[]"]').val() * 1;
                });
                //alert(qntyInPcsTotal);

                $("#total_prodQnty").html(number_format(proQtyTotal, 2));
                $("#total_rejectQnty").html(number_format(rejectQtyTotal, 2));
                $("#total_QcPass").html(number_format(qcQntyTotal, 2));
                $("#total_qntyInPcs").html(qntyInPcsTotal);

                /*var barcode_upd=barcode_nos.split(",");
                 for(var k=0; k<barcode_upd.length; k++)
                 {
                 create_row(1,barcode_upd[k]);
             }*/
             set_button_status(1, permission, 'fnc_grey_delivery_roll_wise', 1);
         }
     }
 }

    function openmypage_barcode()
    {
        if (form_validation('cbo_barcode_type', 'Barcode Type') == false)
        {
            return;
        }

        var company_id = $('#cbo_company_id').val();
        var location_id = $('#cbo_location_id').val();
        var floor_id = $('#txt_floor_no').val();
        var barcode_type = $('#cbo_barcode_type').val();

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/grey_feb_delivery_roll_wise_entry_controller.php?company_id=' + company_id + '&location_id=' + location_id+ '&floor_id=' + floor_id+ '&barcode_type=' + barcode_type + '&action=barcode_popup', 'Barcode Popup', 'width=1330px,height=350px,center=1,resize=1,scrolling=0', '')
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var barcode_nos = this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

            if (barcode_nos != "")
            {
                create_row(barcode_nos);
                load_floor();
            }
        }
    }

    function generate_report_file(data, action)
    {
        window.open("requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=' + action, true);
    }

    function fnc_grey_delivery_roll_wise(operation)
    {
        if (operation == 2) {
            show_msg('13');
            return;
        }

        if (operation == 4) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + $('#cbo_location_id').val(), 'grey_delivery_print');
            return;
        }
        if (operation == 14) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + $('#cbo_location_id').val(), 'grey_delivery_print9');
            return;
        }
        if (operation == 10) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val(), 'grey_delivery_print10');
            return;
        }
        if (operation == 23) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val(), 'grey_delivery_print23');
            return;
        }
        if (operation == 5) {
            var update_id = $('#update_id').val();
            if (update_id == "") {
                alert("Save Data First");
                return;
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val(), 'grey_delivery_print_machine');
            return;
        }

        if (operation == 6) {
            var update_id = $('#update_id').val();
            if (update_id == "") {
                alert("Save Data First");
                return;
            }

            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+ '*' + $('#txt_remarks').val() + '*' + $('#txt_attention').val(), 'grey_delivery_print_fabric_label');
            return;
        }
        if (operation == 11) {
            var update_id = $('#update_id').val();
            if (update_id == "") {
                alert("Save Data First");
                return;
            }

            var show_val_column = "0";

        	var r = confirm("Press \"OK\" to open with Yarn Brand.\nPress \"Cancel\" to open without Yarn Brand.");
        	if (r == true) {
        		show_val_column = "1";
        	}
        	else {
        		show_val_column = "0";
        	}

            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+ '*' + $('#txt_knit_company').val()+ '*' + $('#cbo_location_id').val() + '*' + show_val_column, 'grey_delivery_print_11');
            return;
        }

        // new print button 
        if (operation == 21) {
            var update_id = $('#update_id').val();
            if (update_id == "") {
                alert("Save Data First");
                return;
            }

            var show_val_column = "0";

        	var r = confirm("Press \"OK\" to open with Yarn Brand.\nPress \"Cancel\" to open without Yarn Brand.");
        	if (r == true) {
        		show_val_column = "1";
        	}
        	else {
        		show_val_column = "0";
        	}

            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+ '*' + $('#txt_knit_company').val()+ '*' + $('#cbo_location_id').val() + '*' + show_val_column, 'grey_delivery_print_21');
            return;
        }



        if (operation == 15) {
            var update_id = $('#update_id').val();
            if (update_id == "") {
                alert("Save Data First");
                return;
            }

            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+ '*' + $('#txt_knit_company').val()+ '*' + $('#cbo_location_id').val(), 'grey_delivery_print_15');
            return;
        }

        if (operation == 12)
        {

            if($('#checkbox_organic').prop("checked") == true)
            {
                var organ_print = 1;
            }else {
                var organ_print = 0;
            }
            var update_id = $('#update_id').val();
            if (update_id == "")
            {
                alert("Save Data First");
                return;
            }

            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+'*'+organ_print, 'grey_delivery_print_7');
            return;
        }
        if (operation == 22)
        {

            if($('#checkbox_organic').prop("checked") == true)
            {
                var organ_print = 1;
            }else {
                var organ_print = 0;
            }
            var update_id = $('#update_id').val();
            if (update_id == "")
            {
                alert("Save Data First");
                return;
            }

            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+'*'+organ_print, 'grey_delivery_print_22');
            return;
        }
        if (operation == 20)
        {

            if($('#checkbox_organic').prop("checked") == true)
            {
                var organ_print = 1;
            }else {
                var organ_print = 0;
            }
            var update_id = $('#update_id').val();
            if (update_id == "")
            {
                alert("Save Data First");
                return;
            }

            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+'*'+organ_print, 'grey_delivery_print_13');
            return;
        }

        if (operation == 13)
        {
            var update_id = $('#update_id').val();
            if (update_id == "")
            {
                alert("Save Data First");
                return;
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val()+ '*' + $("#no_copy").val()+ '*' + $("#txt_floor_no").val(), 'grey_delivery_print_13');
            return;
        }

        if (operation == 7) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + $('#cbo_location_id').val(), 'grey_delivery_print');
            return;
        }

        if (operation == 16) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + $('#cbo_location_id').val(), 'grey_delivery_print11');
            return;
        }

        if (operation == 17) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + $('#cbo_location_id').val(), 'grey_delivery_print12');
            return;
        }

        if (operation == 9) {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + 1, 'grey_delivery_print4');
            return;
        }
        if (operation == 8) //Group by
        {
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + 1, 'grey_delivery_print3');
            return;
        }
        if (operation == 18) //
        {
            var update_id = $('#update_id').val();
            if (update_id == "")
            {
                alert("Save Data First.");
                return;
            }
            var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val() + '*' + 1, 'grey_delivery_printmg');
            return;
        }
        var cbo_knitting_source = $('#cbo_knitting_source').val();
        if(cbo_knitting_source == 3){
            if (form_validation('txt_delivery_date*cbo_company_id*cbo_knitting_source*txt_knit_company', 'Delivery Date*Company*Knitting Source*Knitting Company') == false) {
                return;
            }
        }
        else{
            if (form_validation('txt_delivery_date*cbo_company_id*cbo_location_id*cbo_knitting_source*txt_knit_company', 'Delivery Date*Company*Location*Knitting Source*Knitting Company') == false) {
                return;
            }
        }

        remove_duplicate_row();
        var j = 0;
        var dataString = '';
        $("#scanning_tbl").find('tbody tr').each(function () {
            var currentDelivery = $(this).find('input[name="currentDelivery[]"]').val() * 1;
            var prodQty = $(this).find('input[name="prodQty[]"]').val() * 1;
            var rejectQty = $(this).find('input[name="rejectQty[]"]').val() * 1;
            var productionId = $(this).find('input[name="productionId[]"]').val();
            var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
            var productionDtlsId = $(this).find('input[name="productionDtlsId[]"]').val();
            var deterId = $(this).find('input[name="deterId[]"]').val();
            var productId = $(this).find('input[name="productId[]"]').val();
            var orderId = $(this).find('input[name="orderId[]"]').val();
            var rollId = $(this).find('input[name="rollId[]"]').val();
            var dtlsId = $(this).find('input[name="dtlsId[]"]').val();
            var isSales = $(this).find('input[name="isSales[]"]').val();
            var rollNo = $(this).find('input[name="rollNo[]"]').val();
            var bookingWithoutOrder = $(this).find('input[name="bookingWithoutOrder[]"]').val();
            var smnBookingNo = $(this).find("td:eq(3)").text();
            //alert(prodQty);
            try {
                /*if (currentDelivery < 0.1) {
                    alert("Please Insert Roll Qty.");
                    return;
                }*/

                j++;

                dataString += '&currentDelivery_' + j + '=' + currentDelivery + '&productionId_' + j + '=' + productionId + '&barcodeNo_' + j + '=' + barcodeNo + '&productionDtlsId_' + j + '=' + productionDtlsId + '&deterId_' + j + '=' + deterId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollNo_' + j + '=' + rollNo + '&dtlsId_' + j + '=' + dtlsId + '&bookingWithoutOrder_' + j + '=' + bookingWithoutOrder + '&smnBookingNo_' + j + '=' + smnBookingNo + '&isSales_' + j + '=' + isSales;
            }
            catch (e) {
                //got error no operation
            }
        });

        if (j < 1) {
            alert('No data');
            return;
        }

        var data = "action=save_update_delete&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('txt_delivery_date*txt_challan_no*cbo_company_id*cbo_location_id*cbo_knitting_source*txt_knit_company*knit_company_id*update_id*txt_deleted_id*txt_deleted_roll_id*txt_remarks*txt_floor_no*txt_deleted_barcode*cbo_barcode_type*txt_attention', "../") + dataString;
        // alert(data);return;
        freeze_window(operation);

        http.open("POST", "requires/grey_feb_delivery_roll_wise_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_grey_delivery_roll_wise_Reply_info;
    }

    function fnc_grey_delivery_roll_wise_Reply_info()
    {
        if (http.readyState == 4) {
            //release_freezing();return;
            var response = trim(http.responseText).split('**');
            show_msg(response[0]);
            if (response[0] == 11) {
                alert(response[1]);
                /*var update_id = document.getElementById('update_id').value;
                var html = trim(return_global_ajax_value(update_id, 'populate_barcode_data_update', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
                if (html != "") {
                    $("#scanning_tbl tbody").html(html);
                    var num_row = $('#scanning_tbl tbody tr').length;
                    $('#txt_tot_row').val(num_row);
                }*/
                release_freezing();
                return;
            }
            if ((response[0] == 0 || response[0] == 1)) {
                document.getElementById('update_id').value = response[1];
                document.getElementById('txt_challan_no').value = response[2];
                $('#txt_deleted_id').val('');
                $('#txt_deleted_roll_id').val('');
                add_dtls_data(response[3]);
                set_button_status(1, permission, 'fnc_grey_delivery_roll_wise', 1);
            }
            release_freezing();
        }
    }

    function create_row(barcode_no)
    {
        $('#cbo_barcode_type').attr('disabled', 'disabled');
        var row_num = $('#txt_tot_row').val();
        var barcode_nos = trim(barcode_no);
        var proQntTotal = 0;
        var rejectQntTotal = 0;
        var QcQntyTotal = 0;
        var qntInPcsTotal = 0;
        scanned_barcode=[];
        var i=1;
        var msg=0;
        var barcode_da = barcode_nos.split(",");
        $("#scanning_tbl").find('tbody tr').each(function() {
            for (var k = 0; k < barcode_da.length; k++) {
                var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
                if(trim(barcodeNo) == barcode_da[k]){
                    msg++;
                    return;
                }
            }
        });
        if(msg>0){
            alert("Barcode already scanned");
            return;
        }

        //var barcode_data = trim(return_global_ajax_value(barcode_nos, 'populate_barcode_data', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
        var barcode_data = trim(return_global_ajax_value_post(barcode_nos+'_'+$('#cbo_barcode_type').val(), 'populate_barcode_data', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));

        if (barcode_data == 0)
        {
            alert('Barcode is Not Valid');
            $('#messagebox_main', window.parent.document).fadeTo(100, 1, function () //start fading the messagebox
            {
                $('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
            });
            $('#txt_bar_code_num').val('');
            return;
        }
        var all_unqc_barcode = "";
        var barcode_datas = barcode_data.split("___");
        for (var k = 0; k < barcode_datas.length; k++)
        {
            var data = barcode_datas[k].split("**");
            var bar_code = data[0];
            var mst_id = data[1];
            var company_id = data[2];
            var recv_number = data[3];
            var receive_basis = data[4];
            var receive_date = data[5];
            var booking_no = data[6];
            var knitting_source_id = data[7];
            var knitting_source = data[8];
            var knitting_company_id = data[9];
            var knitting_company = data[10];
            var location_id = data[11];
            var dtls_id = data[12];
            var prod_id = data[13];
            var deter_id = data[14];
            var gsm = data[15];
            var width = data[16];
            var roll_id = data[17];
            var roll_no = data[18];
            var po_breakdown_id = data[19];
            var qnty = data[20];
            var prodQnty = data[21];
            var bwo = data[22];
            var booking_without_order = data[23];
            var rcvChallanNo = data[24];

            var serviceBookingNo = data[25];
            //alert(serviceBookingNo)
            var po_id = data[26];
            var buyer_id = data[27];
            var buyer_name = data[28];
            var po_no = data[29];
            var job_no = data[30];
            var year = data[31];
            var system_challan = data[32];
            var is_sales = data[33];
            var color = data[34];
            var body_part = data[35];
            var qnty_in_pcs = data[36];
            var reject_qnty = data[37];
            var internal_ref_no = data[38];
            var coller_cuff_size = data[39];
            var settingAutoQC_barcode = data[40];

            // alert(reject_qnty);
            //if(system_challan=='') system_challan='Not Found';else system_challan=system_challan;

            //var is_barcode_scanned=return_global_ajax_value( bar_code,'check_if_barcode_scanned', '' , 'requires/grey_feb_delivery_roll_wise_entry_controller');
            if(system_challan !='')
			{
                alert('Barcode Already Scanned.\nChallan No : ' + system_challan);
                $('#txt_bar_code_num').val('');
                return;
            }

            //var barcode_qc = trim(return_global_ajax_value(bar_code+"**"+company_id, 'CheckVariableSettingAutoQC', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));

            if(settingAutoQC_barcode==1)
            {
                all_unqc_barcode +=  bar_code + ","
                continue;
            }

            var bar_code_no = $('#barcodeNo_' + row_num).val();
            if (bar_code_no == "")
            {
                $('#cbo_company_id').val(company_id);
                $('#cbo_knitting_source').val(knitting_source_id);
                $('#txt_knit_company').val(knitting_company);
                $('#knit_company_id').val(knitting_company_id);
                $('#cbo_location_id').val(location_id);

                get_php_form_data( company_id, 'company_wise_report_button_setting','requires/grey_feb_delivery_roll_wise_entry_controller' );
            }
            else
            {
                var company_id_prev = $('#cbo_company_id').val();
                var knitting_source_prev = $('#cbo_knitting_source').val();
                var knitting_company_prev = $('#knit_company_id').val();
                var location_id_prev = $('#cbo_location_id').val();

                if (company_id_prev != company_id)
                {
                    alert("Multiple Company Not Allowed");
                    return;
                }

                if (location_id_prev != location_id)
                {
                    alert("Multiple Location Not Allowed");
                    return;
                }

                if (knitting_source_prev != knitting_source_id)
                {
                    alert("Multiple Knitting Source Not Allowed");
                    return;
                }

                if (knitting_company_prev != knitting_company_id)
                {
                    alert("Multiple Knitting Company Not Allowed");
                    return;
                }

                row_num++;
                $("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function () {
                    $(this).attr({
                        'id': function (_, id) {
                            var id = id.split("_");
                            return id[0] + "_" + row_num
                        },
                        'value': function (_, value) {
                            return value
                        }
                    });
                }).end().prependTo("#scanning_tbl");

                $("#scanning_tbl tbody tr:first").removeAttr('id').attr('id', 'tr_' + row_num);
            }

            $("#sl_" + row_num).text(row_num);
            $("#barcode_" + row_num).text(bar_code);
            $("#systemId_" + row_num).text(recv_number);
            $("#progBookId_" + row_num).text(booking_no);
            $("#basis_" + row_num).text(receive_basis);
            $("#knitSource_" + row_num).text(knitting_source);
            $("#prodDate_" + row_num).text(receive_date);
            $("#prodId_" + row_num).text(prod_id);
            if (booking_without_order == 1)
            {
                $("#year_" + row_num).text(year);
                $("#buyer_" + row_num).text(buyer_name);
                $("#order_" + row_num).text(bwo);
                $("#job_" + row_num).text('');
                $("#internalRefNo_" + row_num).text('');
            }
            else
            {
                $("#year_" + row_num).text(year);
                $("#job_" + row_num).text(job_no);
                $("#buyer_" + row_num).text(buyer_name);
                $("#order_" + row_num).text(po_no);
                $("#internalRefNo_" + row_num).text(internal_ref_no);
            }

            $("#cons_" + row_num).text(constructtion_arr[deter_id]);
            $("#comps_" + row_num).text(composition_arr[deter_id]);
            $("#gsm_" + row_num).text(gsm);
            $("#dia_" + row_num).text(width);
            $("#roll_" + row_num).text(roll_no);
            $("#rollNo_1" + row_num).text(roll_no);
            $("#prodQty_" + row_num).text(prodQnty);
            $("#rejectQty_" + row_num).text(reject_qnty);
            $("#currentDelivery_" + row_num).val(qnty);

            $("#barcodeNo_" + row_num).val(bar_code);
            $("#productionId_" + row_num).val(mst_id);
            $("#productionDtlsId_" + row_num).val(dtls_id);
            $("#deterId_" + row_num).val(deter_id);
            $("#productId_" + row_num).val(prod_id);
            $("#orderId_" + row_num).val(po_breakdown_id);
            $("#rollId_" + row_num).val(roll_id);
            $("#dtlsId_" + row_num).val('');
            $("#bookingWithoutOrder_" + row_num).val(booking_without_order);
            $("#rcvChallanNo_" + row_num).text(rcvChallanNo);
            $("#serviceBookingNo_" + row_num).text(serviceBookingNo);
            $('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");
            $('#currentDelivery_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "check_qty(" + row_num + ");");
            $('#isSales_' + row_num).val(is_sales);
            $('#color_' + row_num).text(color);
            $('#bodyPart_' + row_num).text(body_part);
            $('#qntyInPcs_' + row_num).text(qnty_in_pcs);
            $('#size_' + row_num).text(coller_cuff_size);

            $('#txt_tot_row').val(row_num);
            scanned_barcode.push(bar_code);
            $('#txt_bar_code_num').val('');
            $('#txt_bar_code_num').focus();

            proQntTotal += prodQnty * 1;
            rejectQntTotal += reject_qnty * 1;
            QcQntyTotal += qnty * 1;
            qntInPcsTotal += qnty_in_pcs * 1;
        }

        if( all_unqc_barcode!="")
        {
            alert('Barcode/s not QC passed yet.\nBarcode No/s : ' + all_unqc_barcode);
        }
        // alert(rejectQntTotal);
        //$("#total_prodQnty").text()*1 + proQntTotal;
        $("#total_prodQnty").html($("#total_prodQnty").text() * 1 + proQntTotal);
        $("#total_rejectQnty").html($("#total_rejectQnty").text() * 1 + rejectQntTotal);
        $("#total_QcPass").html($("#total_QcPass").text() * 1 + QcQntyTotal);
        $("#total_qntyInPcs").html($("#total_qntyInPcs").text() * 1 + qntInPcsTotal);
        //setFilterGrid('scanning_tbl',-1);
    }

    $('#txt_bar_code_num').live('keydown', function (e) {
        if (e.keyCode === 13)
        {
            //for barcode type
            if (form_validation('cbo_barcode_type', 'Barcode Type') == false)
            {
                return;
            }

            e.preventDefault();
            var bar_code = $('#txt_bar_code_num').val();
            create_row(bar_code);
            load_floor();
        }
    });

    function add_dtls_data(data)
    {
        var barcode_dtlsId_array = new Array();
        var barcode_datas = data.split(",");
        for (var k = 0; k < barcode_datas.length; k++) {
            var datas = barcode_datas[k].split("__");
            var barcode_no = datas[0];
            var dtls_id = datas[1];
            var qty = datas[2];

            barcode_dtlsId_array[barcode_no] = dtls_id;
        }

        $("#scanning_tbl").find('tbody tr').each(function () {
            var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
            var dtlsId = $(this).find('input[name="dtlsId[]"]').val();

            if (dtlsId == "") {
                $(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
            }
        });
    }

    function remove_duplicate_row()
    {
        var check_barcode_arr = new Array();
        var txt_deleted_id = $('#txt_deleted_id').val();
        var txt_deleted_roll_id = $('#txt_deleted_roll_id').val();
        var selected_id = '';
        var selected_id_roll = '';
        $("#scanning_tbl").find('tbody tr').each(function () {
            var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
            var dtlsId = $(this).find('input[name="dtlsId[]"]').val();
            var rollId = $(this).find('input[name="rollId[]"]').val();

            if (jQuery.inArray(barcodeNo, check_barcode_arr) > -1) {
                if (dtlsId != '') {
                    if (selected_id == '') selected_id = dtlsId; else selected_id = selected_id + ',' + dtlsId;
                    if (selected_id_roll == '') selected_id_roll = rollId; else selected_id_roll = selected_id_roll + ',' + rollId;
                }

                $(this).remove();
            }
            else {
                check_barcode_arr.push(barcodeNo);
            }
        });

        if (selected_id != '')
        {
            if (txt_deleted_id == '') txt_deleted_id = selected_id; else txt_deleted_id = txt_deleted_id + ',' + selected_id;
            $('#txt_deleted_id').val(txt_deleted_id);

            if (txt_deleted_roll_id == '') txt_deleted_roll_id = selected_id_roll; else txt_deleted_roll_id = txt_deleted_roll_id + ',' + selected_id_roll;
            $('#txt_deleted_roll_id').val(txt_deleted_roll_id);
        }
    }

    function fn_deleteRow(rid)
    {
        var num_row = $('#scanning_tbl tbody tr').length;
        var dtlsId = $("#dtlsId_" + rid).val();
        var rollId = $("#rollId_" + rid).val();
        var txt_deleted_id = $('#txt_deleted_id').val();
        var txt_deleted_roll_id = $('#txt_deleted_roll_id').val();
        var txt_deleted_barcode = $('#txt_deleted_barcode').val();
        var update_id = $('#update_id').val();

        var bar_code = $("#barcodeNo_" + rid).val();

        if (num_row == 1)
        {
            $('#tr_' + rid + ' td:not(:nth-last-child(2)):not(:last-child)').each(function (index, element) {
                $(this).html('');
            });

            $('#tr_' + rid).find(":input:not(:button)").val('');
            if(update_id=="")
            {
                $('#cbo_company_id').val(0);
                $('#cbo_location_id').val(0);
                $('#cbo_knitting_source').val(0);
                $('#txt_knit_company').val('');
                $('#knit_company_id').val('');
            }
        }
        else
        {
            $("#tr_" + rid).remove();
        }

        var selected_id = '';
        var selected_id_roll = '';
        if (dtlsId != '')
        {
            if (txt_deleted_id == '') selected_id = dtlsId; else selected_id = txt_deleted_id + ',' + dtlsId;
            $('#txt_deleted_id').val(selected_id);

            if (txt_deleted_roll_id == '') selected_id_roll = rollId; else selected_id_roll = txt_deleted_roll_id + ',' + rollId;
            $('#txt_deleted_roll_id').val(selected_id_roll);

            if (txt_deleted_barcode == '') selected_id_borcode = bar_code; else selected_id_borcode = txt_deleted_barcode + ',' + bar_code;
            $('#txt_deleted_barcode').val(selected_id_borcode);
        }

        var index = scanned_barcode.indexOf(bar_code);
        scanned_barcode.splice(index, 1);

        var proQtyTotal = 0;
        var rejectQtyTotal = 0;
        var qcQntyTotal = 0;
        var qntyInPcsTotal = 0;
        $("#scanning_tbl").find('tbody tr').each(function () {
            proQtyTotal += $(this).find('td:nth-child(23)').html() * 1;
            rejectQtyTotal += $(this).find('td:nth-child(24)').html() * 1;
            qntyInPcsTotal += $(this).find('td:nth-child(25)').html() * 1;
            qcQntyTotal += $(this).find('input[name="currentDelivery[]"]').val() * 1;
        });
        $("#total_prodQnty").html(proQtyTotal);
        $("#total_rejectQnty").html(rejectQtyTotal);
        $("#total_QcPass").html(qcQntyTotal);
        $("#total_qntyInPcs").html(qntyInPcsTotal);
        load_floor();
    }

    function check_qty(rid)
    {
        var production_qty = $("#prodQty_" + rid).text() * 1;
        var roll_delv_qty = $("#currentDelivery_" + rid).val() * 1;
        if (roll_delv_qty > production_qty) {
            alert("Delivery Quantity Exceeds Production Quantity.");
            $("#currentDelivery_" + rid).val(production_qty.toFixed(2));
            return;
        }
    }

    function fnc_reset_form()
    {
        $('#scanning_tbl tbody tr').remove();

        var html = '<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="80" id="barcode_1"></td><td width="100" id="systemId_1"></td><td width="85" id="progBookId_1"></td><td width="75" id="basis_1"></td><td width="75" id="knitSource_1"></td><td width="100" id="prodDate_1"></td><td width="80" id="rcvChallanNo_1"></td><td width="120" id="serviceBookingNo_1"></td><td width="50" id="prodId_1"></td><td width="40" id="year_1" align="center"></td><td width="110" id="job_1"></td><td width="100" id="internalRefNo_1"></td><td width="55" id="buyer_1"></td><td width="80" id="order_1" style="word-break:break-all;" align="left"></td><td width="100" id="bodyPart_1" style="word-break:break-all;" align="left"></td><td width="80" id="color_1" style="word-break:break-all;"></td><td width="80" id="cons_1" style="word-break:break-all;" align="left"></td><td width="100" id="comps_1" style="word-break:break-all;" align="left"></td><td width="40" id="gsm_1"></td><td width="40" id="dia_1"></td><td width="40" id="roll_1"></td><td width="70" id="prodQty_1" align="right"></td><td width="50" id="rejectQty_1" align="right"></td><td width="50" id="qntyInPcs_1" align="right"></td><td id="delevQt_1" width="80" align="center"><input type="tex" name="currentDelivery[]" id="currentDelivery_1" style="width:65px" class="text_boxes_numeric" onKeyUp="check_qty(1)" disabled readonly/></td><td id="button_1" align="center" width="30"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="productionId[]" id="productionId_1"/><input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/><input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/></td></tr>';

        $('#cbo_company_id').val(0);
        $('#cbo_knitting_source').val(0);
        $('#txt_knit_company').val('');
        $('#knit_company_id').val('');
        $('#cbo_location_id').val(0);
        $('#txt_tot_row').val(1);
        $('#update_id').val('');
        $('#txt_challan_no').val('');
        $('#txt_delivery_date').val('');
        $('#txt_deleted_id').val('');
        $('#txt_deleted_roll_id').val('');
        $("#scanning_tbl tbody").html(html);
        $("#total_prodQnty").html("");
        $("#total_rejectQnty").html("");
        $("#total_QcPass").html("");
        $("#total_qntyInPcs").html("");
        $("#txt_remarks").val("");
        $("#txt_attention").val("");
        $("#txt_floor_no").val("");
        $("#txt_floor_name").val("");
        $('#cbo_barcode_type').val(0);
        //load_scanned_barcode();
        set_button_status(0, permission, 'fnc_grey_delivery_roll_wise', 1);
    }

    function load_floor()
    {
        var barcode_dtlsId_array = new Array();
        var num_row = $('#scanning_tbl tbody tr').length;
        if (num_row > 0)
        {
            $("#scanning_tbl").find('tbody tr').each(function () {
                var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
                barcode_dtlsId_array.push(barcodeNo);
            });

            var floor_data = trim(return_global_ajax_value(barcode_dtlsId_array, 'populate_floor_data', '', 'requires/grey_feb_delivery_roll_wise_entry_controller'));
            var barcode_datas = floor_data.split("**");
            $("#txt_floor_no").val(barcode_datas[0]);
            $("#txt_floor_name").val(barcode_datas[1]);
        }
    }
</script>
</head>
<body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../", $permission); ?>
        <form name="rollscanning_1" id="rollscanning_1" autocomplete="off">
            <fieldset style="width:810px;">
                <legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td align="right" class="must_entry_caption" width="100">Delivery Date</td>
                        <td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date"
                            class="datepicker" style="width:140px;" value="<? echo date("d-m-Y"); ?>" readonly/></td>
                            <td align="right" width="100">Challan No</td>
                            <td>
                                <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes"
                                style="width:140px;" onDblClick="openmypage_challan()"
                                placeholder="Browse For Challan No" readonly/>
                                <input type="hidden" name="update_id" id="update_id"/>
                            </td>
                            <td align="right">Company</td>
                            <td>
                                <?
                        echo create_drop_down("cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0", "id,company_name", 1, "--Display--", 0, "", 1);//$company_cond
                        ?>
                    </td>


                </tr>
                <tr>
                    <td align="right">Knitting Company</td>
                    <td id="knitting_com">
                        <input type="text" name="txt_knit_company" id="txt_knit_company" class="text_boxes"
                        style="width:140px;" placeholder="Display" disabled/>
                        <input type="hidden" name="knit_company_id" id="knit_company_id"/>
                    </td>
                    <td align="right" class="must_entry_caption">Location</td>
                    <td>
                        <?
                        echo create_drop_down("cbo_location_id", 152, "select id, location_name from lib_location", "id,location_name", 1, "--Display--", 0, "", 1);
                        ?>
                    </td>
                    <td align="right">Floor No.</td>
                    <td>
                        <input type="text" name="txt_floor_name" id="txt_floor_name" class="text_boxes"
                        style="width:140px;" placeholder="Display" disabled>
                        <input type="hidden" name="txt_floor_no" id="txt_floor_no" class="text_boxes">
                    </td>
                </tr>
                <!--<tr>
                    <td height="5" colspan="6"></td>
                </tr>-->
                <tr>
                    <td align="right">Knitting Source</td>
                    <td>
                        <?
                        echo create_drop_down("cbo_knitting_source", 152, $knitting_source, "", 1, "-- Display --", 0, "", 1);
                        ?>
                    </td>
                    <td align="right">Remarks</td>
                    <td colspan="3">
                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:385px">
                    </td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Barcode Type</td>
                    <td><?php echo create_drop_down("cbo_barcode_type", 152, array(1=>'Order barcode',2=>'Without Order barcode'), "", 1, "-- select --", 0, ""); ?></td>
                    <td colspan="2"></td>
                    <td align="right">Attention</td>
                    <td>
                        <input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:140px">
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td align="right"><strong>Barcode Number</strong></td>
                    <td><input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/Scan"/></td>
                    <td colspan="2"></td>
                </tr>
            </table>
        </fieldset>
        <br>
        <fieldset style="width:1850px;text-align:left">
            <style>
            #scanning_tbl tr td {
                background-color: #FFF;
                color: #000;
                border: 1px solid #666666;
                line-height: 12px;
                height: 20px;
                overflow: auto;
            }
        </style>
        <table cellpadding="0" width="1990" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table"  rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="80">Barcode No</th>
                <th width="100">System Id</th>
                <th width="85">Booking/ Programm No</th>
                <th width="75">Production Basis</th>
                <th width="75">Knitting Source</th>
                <th width="100">Production date</th>
                <th width="80">Rcv. Challan No.</th>
                <th width="120">Service Booking No.</th>
                <th width="50">Product Id</th>
                <th width="40">Year</th>
                <th width="110">Job No</th>
                <th width="100">Internal Ref. No</th>
                <th width="55">Buyer</th>
                <th width="80">Order/FSO No</th>
                <th width="100">Body Part</th>
                <th width="80">Fabric Color</th>
                <th width="80">Construction</th>
                <th width="100">Composition</th>
                <th width="40">GSM</th>
                <th width="40">Dia</th>
                <th width="40">Roll No</th>
                <th width="70">Production Qty.</th>
                <th width="50">Reject Qty.</th>
                <th width="50">Qnty in Pcs</th>
                <th width="80">QC Pass Qty.</th>
                <th width="50">Size</th>
                <th width="30"></th>
            </thead>
        </table>
        <div style="width:2008px; max-height:250px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="1990" cellspacing="0" border="1" id="scanning_tbl" class="rpt_table" rules="all">
                <tbody>
                    <tr id="tr_1" align="center" valign="middle">
                        <td width="30" id="sl_1"></td>
                        <td width="80" id="barcode_1"></td>
                        <td width="100" id="systemId_1"></td>
                        <td width="85" id="progBookId_1"></td>
                        <td width="75" id="basis_1"></td>
                        <td width="75" id="knitSource_1"></td>
                        <td width="100" id="prodDate_1"></td>
                        <td width="80" id="rcvChallanNo_1"></td>
                        <td width="120" id="serviceBookingNo_1"></td>
                        <td width="50" id="prodId_1"></td>
                        <td width="40" id="year_1" align="center"></td>
                        <td width="110" id="job_1"></td>
                        <td width="100" id="internalRefNo_1"></td>
                        <td width="55" id="buyer_1"></td>
                        <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                        <td width="100" id="bodyPart_1" style="word-break:break-all;" align="left"></td>
                        <td width="80" id="color_1" style="word-break:break-all;"></td>
                        <td width="80" id="cons_1" style="word-break:break-all;" align="left"></td>
                        <td width="100" id="comps_1" style="word-break:break-all;" align="left"></td>
                        <td width="40" id="gsm_1"></td>
                        <td width="40" id="dia_1"></td>
                        <td width="40" id="roll_1"></td>
                        <td width="70" id="prodQty_1" align="right"></td>
                        <td width="50" id="rejectQty_1" align="right"></td>
                        <td width="50" id="qntyInPcs_1" align="right"></td>
                        <td id="delevQt_1" width="80" align="center"><input type="text" name="currentDelivery[]"
                            id="currentDelivery_1" style="width:65px"
                            class="text_boxes_numeric"
                            onKeyUp="check_qty(1)" disabled readonly/>
                        </td>
                        <td width="50" id="size_1" align="center"></td>
                        <td id="button_1" align="center" width="30">
                            <input type="button" id="decrease_1" name="decrease[]" style="width:30px"
                            class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                            <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                            <input type="hidden" name="productionId[]" id="productionId_1"/>
                            <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1"/>
                            <input type="hidden" name="deterId[]" id="deterId_1"/>
                            <input type="hidden" name="productId[]" id="productId_1"/>
                            <input type="hidden" name="orderId[]" id="orderId_1"/>
                            <input type="hidden" name="rollId[]" id="rollId_1"/>
                            <input type="hidden" name="rollNo[]" id="rollNo_1"/>
                            <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                            <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/>
                            <input type="hidden" name="isSales[]" id="isSales_1" value="0" />
                        </td>
                    </tr>
                </tbody>
           </table>
        </div>
        <table cellpadding="0" width="1990" cellspacing="0" border="" class="rpt_table" rules="all">
        <tfoot>
            <tr>
                <th colspan="21" width="1460">Total</th>
                <th width="70" id="total_prodQnty"></th>
                <th width="50" id="total_rejectQnty"></th>
                <th width="50" id="total_qntyInPcs"></th>
                <th width="80" id="total_QcPass"></th>
                <th width="50"></th>
                <th width="30"></th>
            </tr>
        </tfoot>
        </table>
    </div>
    <br>
    <table width="1520" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
        <tr>
            <td align="center" class="button_container">
                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                <input type="hidden" name="txt_deleted_roll_id" id="txt_deleted_roll_id" class="text_boxes"
                value="">
                <input type="hidden" name="txt_deleted_barcode" id="txt_deleted_barcode" class="text_boxes" value="">
                <?
                echo load_submit_buttons($permission, "fnc_grey_delivery_roll_wise", 0, "", "fnc_reset_form()", 1);
                ?>

                <input id="Printt1" class="formbutton" type="button" style="width:80px" onClick="fnc_grey_delivery_roll_wise(4)" name="print" value="Print">
                <input type="button" class="formbutton" id="btn_mc_wise1" style="width:80px;" value="Print 2"
                onClick="fnc_grey_delivery_roll_wise(7)"/>
                <input type="button" class="formbutton" id="btn_mc_wise2" style="width:80px;" value="Print 3"
                onClick="fnc_grey_delivery_roll_wise(8)"/>
                <input type="button" class="formbutton" id="btn_mc_wise3" style="width:80px;" value="Print 4"
                onClick="fnc_grey_delivery_roll_wise(9)"/>
                <input id="Printt1_booking" class="formbutton" type="button" style="width:100px"
                onClick="fnc_grey_delivery_roll_wise(10)" name="print" value="Booking Wise">
                <input type="button" class="formbutton" id="btn_mc_wise4" style="width:120px;"
                value="Machine Wise" onClick="fnc_grey_delivery_roll_wise(5)"/>
                <input type="button" class="formbutton" id="btn_fabric_label5" style="width:120px;"
                value="Fabric Label" onClick="fnc_grey_delivery_roll_wise(6)"/>

                <input type="button" class="formbutton" id="btn_print11" style="width:120px;"
                value="Print 6" onClick="fnc_grey_delivery_roll_wise(11)"/>

                <input type="button" class="formbutton" id="btn_print12" style="width:120px;"
                value="Print 7" onClick="fnc_grey_delivery_roll_wise(12)"/>
                 <span style="font-weight:bold;display:block; display: inline-block;" id="organic_check"> ORGANIC <input type="checkbox" id="checkbox_organic" ></span>

                <input type="text" value="1"  title="No. of copy" placeholder="No. of copy" id="no_copy" class="text_boxes_numeric" style="width:55px;"/>
                <input type="button" class="formbutton" id="btn_print13" style="width:120px;"
                value="Print 8" onClick="fnc_grey_delivery_roll_wise(13)"/>
                <input type="button" class="formbutton" id="print9" style="width:120px;"
                value="Print 9" onClick="fnc_grey_delivery_roll_wise(14)"/>
                <input type="button" class="formbutton" id="print10" style="width:120px;"
                value="Print 10" onClick="fnc_grey_delivery_roll_wise(15)"/>
                <input type="button" class="formbutton" id="print11" style="width:120px;"
                value="Print 11" onClick="fnc_grey_delivery_roll_wise(16)"/>
                <input type="button" class="formbutton" id="print12" style="width:120px;"
                value="Print 12" onClick="fnc_grey_delivery_roll_wise(17)"/>
                <input type="button" class="formbutton" id="printmg" style="width:80px;" value="Print MG"
                onClick="fnc_grey_delivery_roll_wise(18)"/>
                <input type="button" class="formbutton" id="print 13" style="width:80px;" value="Print 13"
                onClick="fnc_grey_delivery_roll_wise(20)"/>

                <input type="button" class="formbutton" id="btn_print21" style="width:120px;"
                value="Print 21" onClick="fnc_grey_delivery_roll_wise(21)"/>

                <input type="button" class="formbutton" id="btn_print20" style="width:120px;"
                value="Print 20" onClick="fnc_grey_delivery_roll_wise(22)"/>

                <input id="Printt1_booking_2" class="formbutton" type="button" style="width:110px"
                onClick="fnc_grey_delivery_roll_wise(23)" name="print" value="Booking Wise 2">

            </td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>