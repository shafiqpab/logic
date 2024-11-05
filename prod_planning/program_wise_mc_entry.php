<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create planning Info Entry
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	27-07-2013
Updated by 		: 		
Update date		: 	Jahid Hasan	   
QC Performed BY	:	12/12/2016	
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

	function show_details(type) {
		if (form_validation('cbo_company_name', 'Company') == false) {
			return;
		}

		if (type == 2) {
			if (form_validation('txt_booking_no', 'Booking No.') == false) {
				return;
			}
		}

		var data = "action=booking_item_details" + get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*hide_job_id*txt_booking_no*txt_internal_ref*cbo_planning_status*txt_date_from*txt_date_to*txt_prog_no*cbo_type', "../") + '&type=' + type;
		freeze_window(5);
		http.open("POST", "requires/program_wise_mc_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_show_details_reponse;
	//show_list_view(1, 'booking_item_details', 'list_container_fabric_desc', 'requires/program_wise_mc_entry_controller', '');
	}

    function fn_show_details_reponse() {
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

        var page_link = 'requires/program_wise_mc_entry_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
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

    function openmypage_internal_ref() {
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

        var page_link = 'requires/program_wise_mc_entry_controller.php?action=internal_ref_no_search_popup&companyID=' + companyID + '&cbo_within_group=' + cbo_within_group;
        var title = 'IR/IB Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=370px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var hidden_internal_ref = this.contentDoc.getElementById("hidden_internal_ref").value;
            $('#txt_internal_ref').val(hidden_internal_ref);
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

        var page_link = 'requires/program_wise_mc_entry_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&cbo_within_group=' + cbo_within_group;
        var title = 'Booking Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=370px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
            $('#txt_booking_no').val(booking_no);
        }
    }


    /*    
    function generate_report2(company_id, program_id) {
        var path = '../';
        print_report(company_id + '*' + program_id + '*' + path, "print", "requires/yarn_requisition_entry_sales_controller")
    }*/
    
    function generate_report2(company_id, program_id,format_id="") {
       //action name= print
        var path = '../';
        
        if(format_id==273)
        {
        	print_report(company_id + '*' + program_id + '*' + path, "prog_info_print", "requires/program_wise_mc_entry_controller");
        }else {
        	print_report(company_id + '*' + program_id + '*' + path, "print", "requires/yarn_requisition_entry_sales_controller");
        }
    }

    function selected_row(rowNo) {
        var isChecked = $('#tbl_' + rowNo).is(":checked");
        var job_no = $('#job_no_' + rowNo).val();
        var source_no=$('#source_id_'+rowNo).val();
        var party_no=$('#party_id_'+rowNo).val();

        if (isChecked == true) {
            var tot_row = $('#tbl_list_search tbody tr').length;
            for (var i = 1; i <= tot_row; i++) {
                if (i != rowNo) {
                    try {
                        if ($('#tbl_' + i).is(":checked")) {
                            // for checking same source
                            var source_noCurrent = $('#source_id_' + i).val();
                            if ((source_no != source_noCurrent)) {
                                alert("Please Select Same Source.");
                                $('#tbl_' + rowNo).attr('checked', false);
                                return;
                            }
                            // for party same
                            var party_noCurrent = $('#party_id_' + i).val();
                            if ((party_no != party_noCurrent)) {
                                alert("Please Select Same Party.");
                                $('#tbl_' + rowNo).attr('checked', false);
                                return;
                            }
                        }
                    }
                    catch (e) {
                        //got error no operation
                    }
                }
            }
        }
    }

    function generate_knitting_card(type)
    { 
        //alert('ok')
        var program_ids = "";
        var programIds = "";
        var total_tr = $('#tbl_list_search tbody tr').length;
        for (i = 1; i < total_tr; i++)
        {
            try
            {
                if ($('#tbl_' + i).is(":checked")) {
                    programIds++;
                    program_id = $('#promram_id_' + i).val();
                    if (program_ids == "")
                        program_ids = program_id;
                    else
                        program_ids += ',' + program_id;
                }
            }
            catch (e)
            {
                //got error no operation
            }
        }
       
    
        if (program_ids == "")
        {
            alert("Please Select At Least One Program");
            return;
        }
       
        if(type == 9)
        {
            print_report(program_ids, "knitting_card_print_9", "requires/program_wise_mc_entry_controller" ) ;
        }
    }
    function company_wise_load(company_id)
    {
        get_php_form_data( company_id,'company_wise_load' ,'requires/program_wise_mc_entry_controller');
    }

    $(".drag-controls").live("click",function(){
        // when click red button on sales/booking popup then show_details(1) play that is why this function have been commented. issue id: 26609
	   //show_details(1);
	});
</script>
</head>

<body>
    <div style="width:100%;" align="center">

        <form name="palnningEntry_1" id="palnningEntry_1">
          <? echo load_freeze_divs("../", $permission); ?>
          <h3 style="width:1460px;" align="left" id="accordion_h1" class="accordion_h"
          onClick="accordion_menu(this.id,'content_search_panel','')">Planning Info Entry For Sales Order</h3>
          <div id="content_search_panel">
            <fieldset style="width:1460px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                align="center">
                <thead>
                    <th class="must_entry_caption">LC Company</th>
                    <th>Within Group</th>
                    <th>PO Company</th>
                    <th>Sales Order No</th>
                    <th>IR/IB</th>
                    <th>Booking No</th>
                    <th>Program No</th>
                    <th>Sales Order Date</th>
                    <th>Type</th>
                    <th>Planning Status</th>
                    <th><input type="reset" name="res" id="res" value="Reset"
                     onClick="reset_form('palnningEntry_1','list_container_fabric_desc','','','')"
                     class="formbutton" style="width:100px"/></th>
                 </thead>
                 <tbody>
                    <tr class="general">
                        <td>
                         <?
                         echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "company_wise_load(this.value)");
                         ?>
                        <input type="hidden" name="process_costing_maintain" id="process_costing_maintain" class="text_boxes">
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
                        <input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:130px"
                        placeholder="Browse" onDblClick="openmypage_internal_ref();" autocomplete="off" readonly>
                    </td>
                    <td>
                        <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"
                        style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_booking();">
                    </td>
                     <td>
                        <input type="text" name="txt_prog_no" id="txt_prog_no" class="text_boxes"
                        style="width:100px" placeholder="Write">
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                        style="width:70px" readonly>To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                        readonly>
                    </td>
                    <td>
                        <?
                        $search_by_arr = array(1 => "Inside", 3 => "Outside");
                        echo create_drop_down("cbo_type", 120, $search_by_arr, "", 0, "", "1", '', 0);
                        ?>
                    </td>
                    <td>
                     <? echo create_drop_down("cbo_planning_status", 100, $planning_status, "", 0, "", $selected, "", "", "1,2"); ?>
                    </td>


                    <td>
                    <input type="button" value="Show" name="show" id="show" class="formbutton"
                    style="width:100px" onClick="show_details(1)"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="10" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>

</form>
</div>
<div id="list_container_fabric_desc"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>   