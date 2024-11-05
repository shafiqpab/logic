<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Sales Order Entry
Functionality	:
JS Functions	:
Created by		:	
Creation date 	: 	05.01.2023
Updated by 		:   
Update date		:
Report by		:
Creation date 	:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Fabric Sales Order Entry Yarn Part", "../", 1, 1, '', '', '');

$checkMandatory = $_SESSION['logic_erp']['mandatory_field'][109];

	if($checkMandatory[2] == 'Main Process')
	{
		$isMainProcessMandatory=1;
	}
	else
	{
		$isMainProcessMandatory=0;
	}

	if($checkMandatory[3] == 'Sub Process')
	{
		$isSubProcessMandatory=1;
	}
	else
	{
		$isSubProcessMandatory=0;
	}

?>
<script>

	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
	var isMainProcessMandatory = '<? echo $isMainProcessMandatory; ?>';
	var isSubProcessMandatory = '<? echo $isSubProcessMandatory; ?>';

	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][109] );
    //echo "<pre>";print_r($data_arr);echo "</pre>";
	echo "var field_level_data= ". $data_arr . ";\n";

	?>

    function fnc_fabric_yarn_dtls_entry(operation) {
    	if (operation == 2) {
    		show_msg('13');
    		return;
    	}
    	if ($("#is_approved").val()==1 || $("#is_approved").val()==3) {
    		alert("This Sales Order Is Approved. Save, Update , Delete Restricted.");
    		return;
    	}
    	if (form_validation('txt_job_no', 'Sales Order No') == false) {
    		return;
    	}

    	var j = 0;
    	var dataString = '';
    	$("#table_yarn_details").find('tbody tr').each(function () {
    		var fabricDescIdY = $(this).find('input[name="fabricDescIdY[]"]').val();
            var txtFabricGsmY = $(this).find('input[name="txtFabricGsmY[]"]').val();
    		var cboColorRangeY = $(this).find('select[name="cboColorRangeY[]"]').val();
    		var txtGreyQtyY = $(this).find('input[name="txtGreyQtyY[]"]').val();
    		var yarnData = $(this).find('input[name="yarnData[]"]').val();
    		j++;
    		dataString += '&fabricDescIdY' + j + '=' + fabricDescIdY + '&txtFabricGsmY' + j + '=' + txtFabricGsmY + '&cboColorRangeY' + j + '=' + cboColorRangeY + '&txtGreyQtyY' + j + '=' + txtGreyQtyY + '&yarnData' + j + '=' + yarnData;

    	});

    	if (j < 1) {
    		alert('No data');
    		return;
    	}

        
        if($('#cbo_within_group').val() == 1)
        {
            var response=trim(return_global_ajax_value( $('#txt_job_no').val(), 'is_booking_revised', '', 'requires/fabric_sales_order_entry_yarn_part_controller'));
            if(response == "invalid")
            {
                alert("Booking No is Revised.\nSyncronize Fabric Details with \"Apply last update\" button.");
                return;
            }
        }

    	var data = "action=save_update_delete_yarn&operation=" + operation + get_submitted_data_string('txt_job_no*update_id', "../") + dataString + '&total_row=' + j;

    	freeze_window(operation);

    	http.open("POST", "requires/fabric_sales_order_entry_yarn_part_controller.php", true);
    	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    	http.send(data);
    	http.onreadystatechange = fnc_fabric_yarn_dtls_entry_Reply_info;
    }

    function fnc_fabric_yarn_dtls_entry_Reply_info() {
    	if (http.readyState == 4) {
    		var response = trim(http.responseText).split('**');

    		show_msg(response[0]);
    		if ((response[0] == 0 || response[0] == 1)) {
    			set_button_status(1, permission, 'fnc_fabric_yarn_dtls_entry', 2);
    		}
            var datas = return_global_ajax_value(response[1], 'yarn_details', '', 'requires/fabric_sales_order_entry_yarn_part_controller');
            var yarn_datas = trim(datas).split("##")
            $('#yarn_details_list_view').html(yarn_datas[0]);
            
    		//show_change_bookings();
    		release_freezing();
    	}
    }



    function openmypage_fabricDescription(i) {
    	var title = 'Fabric Description Info';
    	var page_link = 'requires/fabric_sales_order_entry_yarn_part_controller.php?action=fabricDescription_popup';

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0', '');

    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var theemail = this.contentDoc.getElementById("hidden_desc_id").value;
    		var theename = this.contentDoc.getElementById("hidden_desc_no").value;
    		var theegsm = this.contentDoc.getElementById("hidden_gsm").value;
    		var theecolorrange = this.contentDoc.getElementById("hidden_color_range").value;

    		$('#txtFabricDesc_' + i).val(theename);
    		$('#fabricDescId_' + i).val(theemail);
    		$('#txtFabricGsm_' + i).val(theegsm);
    		$('#cboColorRange_' + i).val(theecolorrange);
    	}
    }

    function openmypage_color(i) {
    	var title = 'Color Info';
    	var page_link = 'requires/fabric_sales_order_entry_yarn_part_controller.php?action=color_popup';

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=1,scrolling=0', '');

    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var theemail = this.contentDoc.getElementById("hidden_color_id").value;
    		var theename = this.contentDoc.getElementById("hidden_color_no").value;

    		$('#txtColor_' + i).val(theename);
    		$('#colorId_' + i).val(theemail);
    	}
    }

    function openmypage_yarnDetails(i) {
    	var cbo_company_id = $('#cbo_company_id').val();
    	var txtGreyQty = $('#txtGreyQtyY_' + i).val();
    	var yarnData = $('#yarnData_' + i).val();
    	var txtFabricDesc = $('#txtFabricDescY_' + i).val();

        var fabric_Desc_IdY = $('#fabricDescIdY_' + i).val();
        var txt_Fabric_GsmY = $('#txtFabricGsmY_' + i).val();
        var cbo_Color_Range = $('#cboColorRangeY_' + i).val();
        var update_id = $('#update_id').val();


    	if (form_validation('cbo_company_id', 'Company') == false) {
    		return;
    	}

    	var title = 'Yarn Details Info';
    	var page_link = 'requires/fabric_sales_order_entry_yarn_part_controller.php?action=yarnDetails_popup&cbo_company_id=' + cbo_company_id + '&txtGreyQty=' + txtGreyQty + '&yarnData=' + yarnData + '&txtFabricDesc=' + txtFabricDesc + '&fabric_Desc_IdY=' + fabric_Desc_IdY + '&txt_Fabric_GsmY=' + txt_Fabric_GsmY + '&cbo_Color_Range=' + cbo_Color_Range + '&update_id=' + update_id;

    	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1140px,height=370px,center=1,resize=1,scrolling=0', '');

    	emailwindow.onclose = function () {
    		var theform = this.contentDoc.forms[0];
    		var yarn_data = this.contentDoc.getElementById("hidden_yarn_data").value;

    		$('#yarnData_' + i).val(yarn_data);
    	}
    }



 function total_fin_grey_cal(){
 	
 	var total_FinishQty = 0;
 	var total_GreyQty = 0;
 	$("#tbl_item_details").find('tbody tr').each(function () {
 		var txtFinishQty = $(this).find('input[name="txtFinishQty[]"]').val();
 		var txtGreyQty   = $(this).find('input[name="txtGreyQty[]"]').val();
 	
		total_FinishQty += txtFinishQty*1;
		total_GreyQty += txtGreyQty*1;
 	});
 
 	total_FinishQty = total_FinishQty.toFixed(4);
 	total_GreyQty   = total_GreyQty.toFixed(4);

 	
 	$('#total_FinishQty').html(total_FinishQty);
 	$('#total_GreyQty').html(total_GreyQty);
 }


 function openmypage_jobNo() {
 	var cbo_company_id = $('#cbo_company_id').val();
 	var color_from_library = $('#color_from_library').val();

 	if (form_validation('cbo_company_id', 'Company') == false) {
 		return;
 	}
 	else {
 		var title = 'Job Selection Form';
 		var page_link = 'requires/fabric_sales_order_entry_yarn_part_controller.php?cbo_company_id=' + cbo_company_id + '&action=jobNo_popup';

 		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0', '');

 		emailwindow.onclose = function () {
 			var theform = this.contentDoc.forms[0];
 			var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
 			var booking_data = hidden_booking_data.split("**");
 			var job_id = booking_data[9];
 			$('#txt_booking_no').val(booking_data[1]);

			$("#print1").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print1").addClass( "formbutton"); //To make enable print to button
            $("#print_2").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_2").addClass( "formbutton"); //To make enable print to button
            $("#print_3").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_3").addClass( "formbutton"); //To make enable print to button
            $("#print_4").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_4").addClass( "formbutton"); //To make enable print to button
            $("#print_5").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_5").addClass( "formbutton"); //To make enable print to button
			$("#print_6").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_6").addClass( "formbutton"); //To make enable print to button
            $("#print_7").removeClass( "formbutton_disabled"); //To make disable print to button
            $("#print_7").addClass( "formbutton"); //To make enable print to butto

            get_php_form_data(job_id+'_'+booking_data[1], "populate_data_from_sales_order", "requires/fabric_sales_order_entry_yarn_part_controller");

            var cbo_within_group = $('#cbo_within_group').val();
			var company_id = $('#cbo_company_id').val();
            show_fabric_yarn_details(job_id);
        }
    }
}

function show_fabric_yarn_details(update_id) 
{
        var datas = return_global_ajax_value(update_id, 'yarn_details', '', 'requires/fabric_sales_order_entry_yarn_part_controller');
        var yarn_datas = trim(datas).split("##")
        $('#yarn_details_list_view').html(yarn_datas[0]);

         var button_status = 0;
         if (parseInt(yarn_datas[1]) > 1) {
         	button_status = 1;
         }
         set_button_status(button_status, permission, 'fnc_fabric_yarn_dtls_entry', 2);

     }

     function btn_load_change_bookings()
	 {
        // Pending Bookings Button
        $("#list_change_pending_booking_nos").html("<span id='btn_span2' style='cursor: pointer; width: 60px !important; float: left;'><input id='show' onClick='show_change_pending_bookings()' type='button' class='formbutton' value='&nbsp;&nbsp;Pending Yarn info &nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Pending Booking List'></span>");
     	(function blink() {
     		$('#btn_span').fadeOut(900).fadeIn(900, blink);
            $('#btn_span2').fadeOut(900).fadeIn(900, blink);
     	})();
     }

     function set_form_data(data) 
	 {
     	var data = data.split("**");
     	var job_id = data[0];
     	var booking_no = data[2];
     	var cbo_company_id = data[1];
     	$('#cbo_company_id').val(cbo_company_id);
     	$("#last_update").css("visibility", "visible");
        var approved_data = trim(return_global_ajax_value(booking_no, 'check_booking_approval', '', 'requires/fabric_sales_order_entry_yarn_part_controller'));

        var approved_data_arr=approved_data.split('**');
        var approved=approved_data_arr[0];
        if (approved != 1) {

        	var data_for_setup=approved_data_arr[4]+"_"+approved_data_arr[1]+"_"+approved_data_arr[2]+"_"+approved_data_arr[3];
        	var response=return_global_ajax_value( data_for_setup, 'check_approvl_necessity_setup_revised', '', 'requires/fabric_sales_order_entry_yarn_part_controller');
        	if( approved==3){
        		if(response!=1){
        			alert("Approved Booking First.");
        			return;
        		}
        	}
        	else{
        		alert("Approved Booking First.");
        		return;
        	}

        }

            load_drop_down('requires/fabric_sales_order_entry_yarn_part_controller', cbo_company_id, 'load_drop_down_location', 'location_td');
            get_php_form_data(cbo_company_id, 'process_loss_method', 'requires/fabric_sales_order_entry_yarn_part_controller');

            var color_from_library = $('#color_from_library').val();

            get_php_form_data(job_id, "populate_data_from_sales_order", "requires/fabric_sales_order_entry_yarn_part_controller");

            var within_group = $('#cbo_within_group').val();
            var company_id = $('#cbo_company_id').val();
            show_list_view(job_id + "**" + color_from_library + "**" + within_group + "**" + company_id, 'show_fabric_details_update', 'order_details_container', 'requires/fabric_sales_order_entry_yarn_part_controller', '');
            total_amount_cal();

            show_fabric_yarn_details(job_id);
        }


        function open_terms_condition_popup(page_link, title) {
        	var txt_job_no = document.getElementById('txt_job_no').value;
        	if (txt_job_no == "") {
        		alert("Save The Sales Order First")
        		return;
        	}
        	else {
        		page_link = page_link + get_submitted_data_string('txt_booking_no*txt_job_no', '../');
        		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0', '../')
        		emailwindow.onclose = function () {
        		}
        	}
        }

function refresh_fields(i){
	var within_group = $('#cbo_within_group').val();
	if(within_group == 2){
		$('#txtAvgRate_'+i).val('');
		$('#txtAmount_'+i).val('');
		$('#txtBookingQnty_'+i).val('');
		$('#txtFinishQty_'+i).val('');
		$('#txtProcessLoss_'+i).val('');
		$('#txtGreyQty_'+i).val('');
	}
}


	function consumtion_calculate(i)
	{
		var within_group    = $('#cbo_within_group').val();
		var cboConsUom      = $('#cboConsUom_'+i).val();
		var txtBookingQnty  = $('#txtBookingQnty_'+i).val();
		var cboUom          = $('#cboUom_'+i).val();
		var txtFabricGsm    = $('#txtFabricGsm_'+i).val();
		var txtFabricDia    = $('#txtFabricDia_'+i).val();
		var finishQnty="";

        if(cboUom==12) //kg
        {
            if(cboConsUom==12)//kg
            {
            	finishQnty=txtBookingQnty;
            }
            else if (cboConsUom==23)//mtr
            {
            	var meter_cal=(txtFabricDia*2.54/100);
            	finishQnty=(txtBookingQnty*meter_cal*txtFabricGsm/1000);
            }
            else if (cboConsUom==27)//yds
            {
                //yds to kg formula=
            }
        }
        else if(cboUom==27) //kg
        {
            if(cboConsUom==12)//kg
            {
            	finishQnty=txtBookingQnty;
            }
            else if (cboConsUom==23)//mtr
            {
            	var meter_cal=(txtFabricDia*2.54/100);
            	finishQnty=(txtBookingQnty*meter_cal*txtFabricGsm/1000);
            }
            else if (cboConsUom==27)//yds
            {
                //yds to kg formula=
            }
        }
        else if(cboUom==23) //mtr
        {
            if(cboConsUom==12)//kg
            {
            	finishQnty=(txtBookingQnty * 1000)/(txtFabricGsm * txtFabricDia * 0.0254);
            }
            else if (cboConsUom==23)//mtr
            {
            	finishQnty=txtBookingQnty;
            }
            else if (cboConsUom==27)//yds
            {
               // yds to mtr
           }
       }
        $('#txtFinishQty_'+i).val(finishQnty);
        $('#txtGreyQty_'+i).val(finishQnty);
	}
	function show_change_pending_bookings() 
	{
		if (form_validation('cbo_company_id', 'Company') == false) 
		{
			return;
		}
		else 
		{
			show_list_view($('#cbo_company_id').val(), 'show_change_pending_bookings', 'list_change_pending_booking_nos', 'requires/fabric_sales_order_entry_yarn_part_controller', 'setFilterGrid(\'tbl_list_search_pending_booking\',-1);');
		}
	}

	function pending_booking_data_dtls(data_str) 
    {
        var booking_data = data_str; 
        var data = booking_data.split("__");
		var job_id = data[0];
		var txt_booking_no = data[1];

		$('#txt_booking_no').val(txt_booking_no);
		get_php_form_data(job_id+'_'+txt_booking_no, "populate_data_from_sales_order","requires/fabric_sales_order_entry_yarn_part_controller");
        show_fabric_yarn_details(job_id);

		$("#print1").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#print1").addClass( "formbutton"); //To make enable print to button
		$("#print_2").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#print_2").addClass( "formbutton"); //To make enable print to button
		$("#print_3").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#print_3").addClass( "formbutton"); //To make enable print to button
		$("#print_4").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#print_4").addClass( "formbutton"); //To make enable print to button
		$("#print_5").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#print_5").addClass( "formbutton"); //To make enable print to button
		$("#print_6").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#print_6").addClass( "formbutton"); //To make enable print to button
		$("#print_7").removeClass( "formbutton_disabled"); //To make disable print to button
		$("#print_7").addClass( "formbutton"); //To make enable print to butto
    }

	function fabric_sales_order_print3() {
		if (form_validation('txt_job_no', 'Sales Order') == false) 
		{
			return;
		}
    	var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
    	window.open("requires/fabric_sales_order_entry_yarn_part_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
    	return;
    }

	function fabric_sales_order_print4() {
		if (form_validation('txt_job_no', 'Sales Order') == false) 
		{
			return;
		}
        freeze_window();
		var data="action=fabric_sales_order_print4"+'&companyId='+$('#cbo_company_id').val()+'&bookingId='+$('#txt_booking_no_id').val()+'&bookingNo='+$('#txt_booking_no').val()+'&salesOrderNo='+$('#txt_job_no').val()+'&formCaption='+$("div.form_caption").html()+'&update_id='+$('#update_id').val();
		http.open("POST","requires/fabric_sales_order_entry_yarn_part_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fabric_sales_order_print4_reponse;
    }

	function fabric_sales_order_print4_reponse(){
    	if(http.readyState == 4){
            release_freezing();
    		var file_data=http.responseText.split("****");
    		//alert(file_data);
    		$('#data_panel').html(file_data[1]);
    		$('#print_report_Excel').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
    		document.getElementById('print_report_Excel').click();

    		var w = window.open("Surprise", "_blank");
    		var d = w.document.open();
    		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    		'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
    		d.close();
    	}
    }

	function fabric_sales_order_print_kds2() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html() + '*' + $('#cbo_within_group').val();
        window.open("requires/fabric_sales_order_entry_yarn_part_controller.php?data=" + data + '&action=fabric_sales_order_print_kds2', true);
        return;
    }

	function fabric_sales_order_print5() {
        var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
        var within_group=$('#cbo_within_group').val()
            if (within_group == 2) {
                window.open("requires/fabric_sales_order_entry_yarn_part_controller.php?data=" + data + '&action=fabric_sales_order_print5', true);
            } else {
                alert("This report available for Within Group No");
            }

            return;
    }

	function fnc_fabric_sales_order_entry(operation)
	{
		var within_group=$('#cbo_within_group').val();
		if (operation == 4) {
    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_yarn_part_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
    		} else {
    			window.open("requires/fabric_sales_order_entry_yarn_part_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
    		}

    		return;
    	}

        if (operation == 7) {
            var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();
            window.open("requires/fabric_sales_order_entry_yarn_part_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
            return;
        }

		if (operation == 6) {
    		var data = $('#cbo_company_id').val() + '*' + $('#txt_booking_no_id').val() + '*' + $('#txt_booking_no').val() + '*' + $('#txt_job_no').val() + '*' + $("div.form_caption").html();

    		if (within_group == 1) {
    			window.open("requires/fabric_sales_order_entry_yarn_part_controller.php?data=" + data + '&action=fabric_sales_order_print_yes_6', true);
    		} else {
				alert("This report generated only within group yes");
				return;
    		}

    		return;
    	}
	}

</script>
</head>
	<body onLoad="set_hotkey(); btn_load_change_bookings();">
	<? echo load_freeze_divs("../", $permission); ?>
		<div style="width:740px; float:left;" align="left">
		
			<fieldset style="width:740px;">
				<legend>Fabric Sales Order Entry</legend>
				<form name="fabricOrderEntry_1" id="fabricOrderEntry_1">
					<div style="width:700px; float:left;" align="center">
						<fieldset style="width:700px;">
							<table width="700" align="center" border="0">
								<tr>
									<td align="right" colspan="3"><strong>Sales Order No</strong></td>
									<td width="130">
										<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes"
										style="width:150px;" placeholder="Double Click To Edit"
										onDblClick="openmypage_jobNo()" readonly/>
									</td>
								</tr>
								<tr>
									<td width="110" class="must_entry_caption">Company</td>
									<td width="190">
										<?
										echo create_drop_down("cbo_company_id", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', 0, "get_php_form_data( this.value, 'company_wise_report_button_setting','requires/fabric_sales_order_entry_yarn_part_controller' )", '', '', '', '', '');
										?>
										<input type="hidden" id="editableId" value=""/>
									</td>
									<td width="110" class="must_entry_caption">Within Group</td>
									<td>
										<?
										echo create_drop_down("cbo_within_group", 162, $yes_no, "", 0, "--  --", 0, "");
										?>
									</td>
									<td width="110" class="must_entry_caption">Sales/Booking No.</td>
									<td>
										<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px;" readonly/>
									</td>
								</tr>
								<tr>
									<td class="must_entry_caption">FSO Date</td>
									<td>
										<input type="text" name="txt_booking_date" id="txt_booking_date" class="datepicker"
										style="width:150px;" value="<? echo date("d-m-Y"); ?>" readonly disabled/>
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
				</form>
			</fieldset>

			<form name="fabricOrderEntry_2" id="fabricOrderEntry_2">
				<fieldset style="width:740px; margin-top:10px" align="center">
					<legend>Grey Qty. For Yarn Details Entry</legend>
					<table class="rpt_table" border="1" width="675" cellpadding="0" cellspacing="0" rules="all" id="table_yarn_details">
						<thead>
							<th width="400">Fabric Description</th>
							<th width="100">Fabric GSM</th>
							<th width="100">Color Range</th>
							<th class="must_entry_caption">Grey Quantity</th>
						</thead>
						<tbody id="yarn_details_list_view"></tbody>
					</table>
					<table>
						<tr>
							<td width="100%" align="center" colspan="3">
								<? 
								echo load_submit_buttons($_SESSION['page_permission'], "fnc_fabric_yarn_dtls_entry", 0, 0, "reset_form('','','','','')", 2); ?>
								<input type="hidden" name="is_approved" id="is_approved" readonly>
								<input type="hidden" name="update_id" id="update_id"/>

								<!-- <input type="button" name="print_2" class="formbutton" value="Print 2" id="print_2" onClick="fabric_sales_order_print3();" />
								<input type="button" name="print_3" class="formbutton" value="KDS" id="print_3" onClick="fabric_sales_order_print4();" /> -->
								
								<input type="button" name="print1" class="formbutton_disabled" value="Print" id="print1" style="width:80px; display:none;" onClick="fnc_fabric_sales_order_entry(4)"  >
								<input type="button" name="print_2" class="formbuttonplasminus formbutton_disabled" style="display:none;" value="Print 2" id="print_2" onClick="fabric_sales_order_print3();" />
								<input type="button" name="print_3" class="formbuttonplasminus formbutton_disabled" style="display:none;" value="KDS" id="print_3" onClick="fabric_sales_order_print4();" />
								<input type="button" name="print_4" class="formbuttonplasminus formbutton_disabled" style="display:none;" value="KDS 2" id="print_4" onClick="fabric_sales_order_print_kds2();" />
								<input type="button" name="print_5" class="formbuttonplasminus formbutton_disabled" style="display:none;" value="Print 4" id="print_5" onClick="fabric_sales_order_print5();" />
								<input type="button" name="print_6" class="formbuttonplasminus formbutton_disabled" style="display:none;" value="Print 5" id="print_6" onClick="fnc_fabric_sales_order_entry(6);" />
								<input type="button" name="print_7" class="formbuttonplasminus formbutton_disabled" style="display:none;" value="Print 6" id="print_7" onClick="fnc_fabric_sales_order_entry(7);" />

							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
		<div style="width:10px; float:left;padding-left:15px;">
			<p>&nbsp;&nbsp;&nbsp;</p>
		</div>
		<div style="width:400px; float:left;">
			<fieldset style="width:400px;">
				<div id="list_change_pending_booking_nos" style="max-height:500px; width:380px; overflow:auto; "></div>
			</fieldset>
		</div>
	<div style="display:none" id="data_panel"></div>
	<a id="print_report_Excel" href="" style="text-decoration:none" download hidden>#</a>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>