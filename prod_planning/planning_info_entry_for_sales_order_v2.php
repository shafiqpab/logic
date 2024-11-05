<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create planning Info Entry Sales V2
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	27-07-2013
Updated by 		: 	Md. Nuruzzaman
Update date		: 	15-05-2021	   
QC Performed BY	:	
QC Date			:	
Comments		:
*/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
    require_once('../includes/common.php');
    extract($_REQUEST);
    $_SESSION['page_permission'] = $permission;
    echo load_html_head_contents("Planning Info Entry For Sales Order", "../", 1, 1, '', '', '');
    ?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][282]) ;
	echo "var field_level_data= ". $data_arr . ";\n";
	?>
	var isClickShowBtn = 0;
	function show_details(type)
	{
		if (form_validation('cbo_company_name', 'Company') == false) {
			return;
		}

		if (type == 2) {
			if (form_validation('txt_booking_no', 'Booking No.') == false) {
				return;
			}
		}
		
		isClickShowBtn = 1;
		var data = "action=booking_item_details" + get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*hide_job_id*txt_booking_no*cbo_planning_status*txt_barcode*txt_date_from*txt_date_to', "../") + '&type=' + type;
		freeze_window(5);
		http.open("POST", "requires/planning_info_entry_for_sales_order_v2_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_show_details_reponse;
	//show_list_view(1, 'booking_item_details', 'list_container_fabric_desc', 'requires/planning_info_entry_for_sales_order_v2_controller', '');
	}

    function fn_show_details_reponse()
	{
        if (http.readyState == 4) {
            var response = http.responseText;
            $('#list_container_fabric_desc').html(response);
            set_all_onclick();
            show_msg('18');
            release_freezing();
        }
    }

    function openmypage_job()
	{
        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var within_group = $("#cbo_within_group").val();
		
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        if(within_group == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false) {
				return;
			}
		}

        var page_link = 'requires/planning_info_entry_for_sales_order_v2_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
        ;
        var title = 'Style Ref./ Job No. Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var job_no = this.contentDoc.getElementById("hide_job_no").value;
            var job_id = this.contentDoc.getElementById("hide_job_id").value;

            $('#txt_job_no').val(job_no);
            $('#hide_job_id').val(job_id);
        }
    }

    function openmypage_booking() {
        var companyID = $("#cbo_company_name").val();
        var cbo_within_group = $("#cbo_within_group").val();

        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }
		
        if(cbo_within_group == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false) {
				return;
			}
		}

        var page_link = 'requires/planning_info_entry_for_sales_order_v2_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&cbo_within_group=' + cbo_within_group;
        var title = 'Booking Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=370px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
            $('#txt_booking_no').val(booking_no);
        }
    }

    function openmypage_prog()
	{
        var type = $('#txt_type').val();
        if (type == 2)
		{
            alert("Not Allow");
            return;
        }
        var tot_row = $('#tbl_list_search tbody tr').length;
        var data = '';
        var selected_row = 0;
        var currentRowColor = '';
        var booking_no = '';
        var body_part_id = '';
        var fabric_typee = '';
        var buyer_id = '';
        var job_id = '';
        var dia = '';
        var gsm = '';
        var desc = '';
        var booking_qnty = 0;
        var plan_id = '';
        var determination_id = '';
        var job_dtls_id = '';
        var within_group = '';
        var color_type_id = '';
        var sales_order_dtls = '';
        var pre_cost = '';
        var companyID = $('#company_id').val();
        var action_type = $('#action_type').val();
        var customer_buyer_id = '';
		var balance_qnty = 0;

        for (var j = 1; j <= tot_row; j++)
		{
            currentRowColor = document.getElementById('tr_' + j).style.backgroundColor;
            if (currentRowColor == 'yellow')
			{
                selected_row++;

                if (data == '')
				{
                    data = $('#bookingNo_' + j).val() + "**"
                    + $('#job_id_' + j).val() + "**"
                    + $('#withinGroup_' + j).val() + "**"
                    + $('#job_dtls_id_' + j).val() + "**"
                    + $('#buyer_id_' + j).val() + "**"
                    + $('#body_part_id_' + j).val() + "**"
                    + $('#fabric_typee_' + j).val() + "**"
                    + $('#desc_' + j).text() + "**"
                    + $('#gsm_weight_' + j).text() + "**"
                    + $('#dia_width_' + j).text() + "**"
                    + $('#determination_id_' + j).val() + "**"
                    + $('#booking_qnty_' + j).text() + "**"
                    + $('#color_type_id_' + j).val() + "**"
                    + $('#sales_order_dtls_id' + j).val() + "**"
                    + $('#pre_cost_fabric_cost_dtls_id' + j).val() + "**"
                    + $('#customer_buyer_id_' + j).val() + "**"
                    + $('#after_wash_gsm_' + j).text();
                }
                else
				{
                    data += "_" + $('#bookingNo_' + j).val() + "**"
                    + $('#job_id_' + j).val() + "**"
                    + $('#withinGroup_' + j).val() + "**"
                    + $('#job_dtls_id_' + j).val() + "**"
                    + $('#buyer_id_' + j).val() + "**"
                    + $('#body_part_id_' + j).val() + "**"
                    + $('#fabric_typee_' + j).val() + "**"
                    + $('#desc_' + j).text() + "**"
                    + $('#gsm_weight_' + j).text() + "**"
                    + $('#dia_width_' + j).text() + "**"
                    + $('#determination_id_' + j).val() + "**"
                    + $('#booking_qnty_' + j).text() + "**"
                    + $('#color_type_id_' + j).val() + "**"
                    + $('#sales_order_dtls_id' + j).val() + "**"
                    + $('#pre_cost_fabric_cost_dtls_id' + j).val() + "**"
                    + $('#customer_buyer_id_' + j).val() + "**"
                    + $('#after_wash_gsm_' + j).text();
                }
                booking_no = $('#bookingNo_' + j).val();
                gsm = $('#gsm_weight_' + j).text();
                dia = $('#dia_width_' + j).text();
                desc = $('#desc_' + j).text();
                within_group = $('#withinGroup_' + j).val();
                buyer_id = $('#buyer_id_' + j).val();
                job_id = $('#job_id_' + j).val();
                determination_id = $('#determination_id_' + j).val();
                color_type_id = $('#color_type_id_' + j).val();
                fabric_typee = $('#fabric_typee_' + j).val();
                pre_cost_id = $('#pre_cost_id_' + j).val();
                pre_cost_fabric_cost_dtls_id = $('#pre_cost_fabric_cost_dtls_id' + j).val();
                customer_buyer_id = $('#customer_buyer_id_' + j).val();

                //for body_part_id
				if (body_part_id == '')
					body_part_id = $('#body_part_id_' + j).val();
				else
					body_part_id += "," + $('#body_part_id_' + j).val();
				
				if (plan_id == '')
					plan_id = $('#plan_id_' + j).text();
					
                if (job_dtls_id == '')
					job_dtls_id = $('#job_dtls_id_' + j).val();
				else
					job_dtls_id += "," + $('#job_dtls_id_' + j).val();
				
                if (sales_order_dtls == '')
					sales_order_dtls = $('#sales_order_dtls_id' + j).val();
				else
					sales_order_dtls += "_" + $('#sales_order_dtls_id' + j).val();
					
                if (pre_cost == '')
					pre_cost = $('#pre_cost_fabric_cost_dtls_id' + j).val();
				else
					pre_cost += "_" + $('#pre_cost_fabric_cost_dtls_id' + j).val();
					
                booking_qnty = booking_qnty * 1 + $('#booking_qnty_' + j).text() * 1;
				balance_qnty = balance_qnty * 1 + $('#ballance_qnty_' + j).text() * 1;
            }
        }
		
        if (selected_row < 1)
		{
            alert("Please Select At Least One Item");
            return;
        }
        
		var page_link = 'requires/planning_info_entry_for_sales_order_v2_controller.php?action=prog_qnty_popup&gsm=' + gsm + '&dia=' + dia + '&desc=' + desc + '&within_group=' + within_group + '&job_id=' + job_id + '&booking_qnty=' + booking_qnty + '&companyID=' + companyID + '&data="' + data + '"' + '&plan_id=' + plan_id + '&determination_id=' + determination_id + '&booking_no=' + booking_no + '&body_part_id=' + body_part_id + '&fabric_type=' + fabric_typee + '&pre_cost_id=' + pre_cost_id + '&buyer_id=' + buyer_id + '&job_dtls_id=' + job_dtls_id + '&color_type_id=' + color_type_id + '&sales_order_dtls_id=' + sales_order_dtls + '&pre_cost=' + pre_cost + '&customer_buyer_id=' + customer_buyer_id+ '&balance_qnty=' + balance_qnty;
        var title = 'Program Qnty Info';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=450px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose=function()
		{	
            show_details(1);
		}
    }

    function selected_row(rowNo, status)
	{
        if(status=='no')
        { 
            alert("This Booking is not Approved");
            return;
        }
        var color = document.getElementById('tr_' + rowNo).style.backgroundColor;
        var bookingNo = $('#bookingNo_' + rowNo).val();
        var body_part_id = $('#body_part_id_' + rowNo).val();
        var determinationId = $('#determination_id_' + rowNo).val();
        var widthDiaType = $('#fabric_typee_' + rowNo).val();
        var gsm = $('#gsm_weight_' + rowNo).text();
        var fabricDia = $('#dia_width_' + rowNo).text();
        var plan_id = $('#plan_id_' + rowNo).text();
        var color_type_id = $('#color_type_id_' + rowNo).val();
        var job_id = $('#job_id_' + rowNo).val();

        var stripe_or_not = '';

        if (color_type_id == 2 || color_type_id == 3 || color_type_id == 4)
		{
            stripe_or_not = 1;//1 means stripe yes
        }
        else
		{
            stripe_or_not = 0;//0 means stripe no
        }

        var currentRowColor = '';
        var check = '';
        if (color != 'yellow')
		{
            var tot_row = $('#tbl_list_search tbody tr').length;
            for (var i = 1; i <= tot_row; i++)
			{
                if (i != rowNo)
				{
                    currentRowColor = document.getElementById('tr_' + i).style.backgroundColor;
                    if (currentRowColor == 'yellow') {
                        var bookingNoCur = $('#bookingNo_' + i).val();
                        var body_part_idCur = $('#body_part_id_' + i).val();
                        var determinationIdCur = $('#determination_id_' + i).val();
                        var widthDiaTypeCur = $('#fabric_typee_' + i).val();
                        var gsmCur = $('#gsm_weight_' + i).text();
                        var fabricDiaCur = $('#dia_width_' + i).text();
                        var plan_idCur = $('#plan_id_' + i).text();
                        var color_type_idCur = $('#color_type_id_' + i).val();
                        var job_idCur = $('#job_id_' + i).val();

                        var stripe_or_notCur = '';
                        if (color_type_idCur == 2 || color_type_idCur == 3 || color_type_idCur == 4)
						{
                            stripe_or_notCur = 1;//1 means stripe yes
                        }
                        else
						{
                            stripe_or_notCur = 0;//0 means stripe no
                        }
                        if (plan_id == "" || plan_idCur == "")
						{
                            //if (!(job_id == job_idCur && bookingNo == bookingNoCur && determinationId == determinationIdCur && widthDiaType == widthDiaTypeCur && gsm == gsmCur && fabricDia == fabricDiaCur && stripe_or_not == stripe_or_notCur && job_id == job_idCur && body_part_id == body_part_idCur && color_type_id == color_type_idCur))
                            if (!(job_id == job_idCur && bookingNo == bookingNoCur && determinationId == determinationIdCur && widthDiaType == widthDiaTypeCur && gsm == gsmCur && fabricDia == fabricDiaCur && stripe_or_not == stripe_or_notCur && job_id == job_idCur && color_type_id == color_type_idCur))
							{
                                alert("Please Select Same Description");
                                return;
                            }
                        }
                        else
						{
                            //if (!(plan_id == plan_idCur && job_id == job_idCur && bookingNo == bookingNoCur && determinationId == determinationIdCur && widthDiaType == widthDiaTypeCur && gsm == gsmCur && fabricDia == fabricDiaCur && stripe_or_not == stripe_or_notCur && job_id == job_idCur && body_part_id == body_part_idCur && color_type_id == color_type_idCur))
							if (!(plan_id == plan_idCur && job_id == job_idCur && bookingNo == bookingNoCur && determinationId == determinationIdCur && widthDiaType == widthDiaTypeCur && gsm == gsmCur && fabricDia == fabricDiaCur && stripe_or_not == stripe_or_notCur && job_id == job_idCur && color_type_id == color_type_idCur))
							{
                                alert("Please Select Same Description and Same Plan ID");
                                return;
                            }
                        }
                    }
                }
            }

            $('#tr_' + rowNo).css('background-color', 'yellow');
        }
        else
		{
            var reqsn_found_or_not = $('#reqsn_found_or_not_' + rowNo).val();
            if (reqsn_found_or_not == 0)
			{
                $('#tr_' + rowNo).css('background-color', '#FFFFCC');
            }
            else
			{
                alert("Requisition Found Against This Planning. So Change Not Allowed");
                return;
            }
        }
    }

    function delete_prog()
	{
        if (confirm("Are you sure?")) {
            var program_ids = "";
            var total_tr = $('#tbl_list_search tr').length;
            for (i = 1; i < total_tr; i++) {
                try {
                    if ($('#tbl_' + i).is(":checked")) {
                        program_id = $('#promram_id_' + i).val();
                        if (program_ids == "") program_ids = program_id; else program_ids += ',' + program_id;
                    }
                }
                catch (e) {
                    //got error no operation
                }
            }

            if (program_ids == "") {
                alert("Please Select At Least One Program");
                return;
            }

            var data = "action=delete_program&operation=" + operation + '&program_ids=' + program_ids + get_submitted_data_string('cbo_company_name', "../");

            freeze_window(operation);

            http.open("POST", "requires/planning_info_entry_for_sales_order_v2_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_delete_prog_Reply_info;
        }
    }

    function fnc_delete_prog_Reply_info() {
        if (http.readyState == 4) {
            var reponse = trim(http.responseText).split('**');

            show_msg(trim(reponse[0]));

            if (reponse[0] == 2) {
                fnc_remove_tr();
            }

            release_freezing();
        }
    }

    function fnc_remove_tr() {
        var tot_row = $('#tbl_list_search tr').length;
        for (var i = 1; i <= tot_row; i++) {
            try {
                if ($('#tbl_' + i).is(':checked')) {
                    $('#tr_' + i).remove();
                }
            }
            catch (e) {

            }
        }
    }

    function fnc_update(i)
	{
        if (confirm("Are you sure?"))
		{
            var prog_qty = $('#prog_qty_' + i).val();
            var program_id = $('#promram_id_' + i).val();
            var data = "action=update_program&operation=" + operation + '&program_id=' + program_id + '&prog_qty=' + prog_qty;
            freeze_window(operation);
            http.open("POST", "requires/planning_info_entry_for_sales_order_v2_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_update_prog_Reply_info;
        }
    }

    function fnc_update_prog_Reply_info() {
        if (http.readyState == 4) {
            var response = trim(http.responseText);
            if (response == 20) {
                alert("Program Qty Cannot Be Less Than Knitting Qty.");
                release_freezing();
                return;
            }
            else if(response == 21) {
                alert("Program Qty Cannot Be More Than Knitting Qty.");
                release_freezing();
                return;
            }
            show_msg(response);
            release_freezing();
        }
    }

    function active_inactive() {
        reset_form('', '', 'txt_job_no*hide_job_id*txt_booking_no', '', '', '');

        var within_group = $('#cbo_within_group').val();
        var company_id = document.getElementById('cbo_company_name').value;

        if (within_group == 1) {
            $('#txt_booking_no').attr('onDblClick', 'openmypage_booking();');
            $('#txt_booking_no').attr('placeholder', 'Browse Or Write');
            $('#txt_booking_no').removeAttr('disabled', 'disabled');
        }
        else {
            $('#txt_booking_no').removeAttr('onDblClick', 'onDblClick');
            $('#txt_booking_no').attr('placeholder', '');
            $('#txt_booking_no').attr('disabled', 'disabled');
        }

        if (company_id == 0) {
            $("#cbo_buyer_name option[value!='0']").remove();
        }
        else {
            load_drop_down('requires/planning_info_entry_for_sales_order_v2_controller', company_id + '_' + within_group, 'load_drop_down_buyer', 'buyer_td');
        }
    }

    function fnc_close() {
        var data = '';
        $('#selected_data').val(data);
        parent.emailwindow.hide();
    }

    /*    
    function generate_report2(company_id, program_id) {
        var path = '../';
        print_report(company_id + '*' + program_id + '*' + path, "print", "requires/yarn_requisition_entry_sales_controller")
    }*/
    
    function generate_report2(company_id, program_id, format_id="")
	{
		//action name= print
		var path = '../';
		print_report(program_id + '**0**' + path + '**2', "requisition_print_two", "requires/yarn_requisition_entry_sales_v2_controller");
		/*if(format_id==273)
		{
			print_report(company_id + '*' + program_id + '*' + path, "prog_info_print", "requires/planning_info_entry_for_sales_order_v2_controller");
		}
		else
		{
			//print_report(company_id + '*' + program_id + '*' + path, "print", "requires/yarn_requisition_entry_sales_controller");
			//print_report(company_id + '*' + program_id + '*' + path, "print", "requires/yarn_requisition_entry_sales_controller");
			//print_report(company_id + '*' + program_id + '*' + path, "requisition_print_two", "requires/planning_info_entry_for_sales_order_v2_controller");
			
			//var path = '../';
			//print_report(program_id + '**0**' + path + '**2', "requisition_print_two", "requires/yarn_requisition_entry_sales_v2_controller");
		}*/
    }

    function activePlan(e,plan_id,program_ids,balance_qnty,dtls_ids,prog_qty)
	{
        if(confirm("Are you sure?")){
            if(balance_qnty >= 0){
                var data = plan_id+"**"+program_ids+"**"+balance_qnty+"**"+dtls_ids+"**"+prog_qty;
                var response = trim(return_global_ajax_value(data, 'activePlan', '', 'requires/planning_info_entry_for_sales_order_v2_controller'));
                if(response == 1){
                    alert("Plan activated successfully");
                    $(e).parents("tr").fadeOut();
                }else{
                    alert("Activation failed.");
                }
            }else{
                alert("Program quantity can not be greater than Booking quantity");
            }
        }
    }

    function openmypage_barcode() {

        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        
        var within_group = $("#cbo_within_group").val();
        var page_link = 'requires/planning_info_entry_for_sales_order_v2_controller.php?action=barcode_popup&companyID=' + companyID + '&within_group=' + within_group;
        ;
        var title = 'Style Ref./ Job No. Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
            
            if(barcode_nos!="")
            {
                $("#txt_barcode").val(barcode_nos);
                show_details(1);
               // openmypage_prog(1);
            }
        }
    }

    $('#txt_barcode').live('keydown', function(e) {
        if (e.keyCode === 13) 
        {
            e.preventDefault();
            var txt_barcode= $("#txt_barcode").val();
            if(txt_barcode){
                show_details(1);
               // openmypage_prog(1);

            }
        }
    });

    $(".drag-controls").live("click",function(){
		if(isClickShowBtn == 1)
		{
			show_details(1);
		}
	});
</script>
</head>

<body>
    <div style="width:100%;" align="center">
        <form name="palnningEntry_1" id="palnningEntry_1">
          <? echo load_freeze_divs("../", $permission); ?>
          <h3 style="width:1410px;" align="left" id="accordion_h1" class="accordion_h"
          onClick="accordion_menu(this.id,'content_search_panel','')">Planning Info Entry For Sales Order</h3>
          <div id="content_search_panel">
            <fieldset style="width:1410px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                align="center">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Within Group</th>
                    <th>PO Company</th>
                    <th>Sales Order No</th>
                    <th>Sales Job/Booking No</th>
                    <th>Sales Order Date</th>
                    <th>Planning Status</th>
                    <th>Barcode</th>
                    <th><input type="reset" name="res" id="res" value="Reset"
                     onClick="reset_form('palnningEntry_1','list_container_fabric_desc','','','')"
                     class="formbutton" style="width:100px"/></th>
                 </thead>
                 <tbody>
                    <tr class="general">
                        <td>
                         <?
                         echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
                         ?>
                     </td>
                     <td>
                         <?php echo create_drop_down("cbo_within_group", 110, $yes_no, "", 0, "-- Select --", 0, ""); ?>
                     </td>
                     <td>
                         <?
                         echo create_drop_down("cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
                         ?>
                     </td>
                     <td>
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:130px"
                        placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                        <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                    </td>
                    <td>
                        <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"
                        style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_booking();">
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                        style="width:70px" readonly>To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                        readonly>
                    </td>
                    <td>
                     <? echo create_drop_down("cbo_planning_status", 100, $planning_status, "", 0, "", $selected, "", "", "1,2"); ?>
                    </td>

                    <td>
                        <input type="text" name="txt_barcode" id="txt_barcode" class="text_boxes" style="width:100px" onDblClick="openmypage_barcode()" placeholder="Write/Scan/Browse">
                    </td>

                    <td>
                    <input type="button" value="Show" name="show" id="show" class="formbutton"
                    style="width:100px" onClick="show_details(1)"/>
                    &nbsp;
                    <input type="button" value="Revised Booking" name="show" id="show" class="formbutton"
                    style="width:105px" onClick="show_details(2)"/>
                    <?php
                    if($_SESSION['logic_erp']['user_level'] == 2 || $_SESSION['logic_erp']['user_level'] == 1){
                        ?>
                        <input type="button" value="Deleted Booking" name="show" id="show" class="formbutton"
                        style="width:105px" onClick="show_details(3)"/>
                        <? } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>
<div style="width:100%;margin-top:2px;">
    <input type="button" value="Click For Program" name="generate" id="generate" class="formbuttonplasminus"
    style="width:150px" onClick="openmypage_prog()"/>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="hidden" value="" id="selected_data"/>
    <input type="button" value="Close" name="close" id="close" class="formbuttonplasminus" style="width:150px"
    onClick="fnc_close()"/>
</div>
</form>
</div>
<div id="list_container_fabric_desc"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>