<?
/*-------------------------------------------- Comments
Purpose			:	This form will create Program Wise Ref. Creation
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	22-09-2021
Updated by 		: 	
Update date		: 	
QC Performed BY	:	
QC Date			:	
Comments		:
*/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
    require_once('../includes/common.php');
    extract($_REQUEST);
    $_SESSION['page_permission'] = $permission;
    echo load_html_head_contents("Program Wise Ref. Creation", "../", 1, 1, '', '', '');
    ?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
	
	//for func_show_details
	function func_show_details(type)
	{
		if (form_validation('cbo_company_name', 'Company') == false)
		{
			return;
		}
		
		if ($('#cbo_within_group').val() == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false)
			{
				return;
			}
		}
		
		if($('#txt_job_no').val() == '' && $('#txt_booking_no').val() == '' && $('#txt_program_no').val() == '')
		{
			if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false)
			{
				return;
			}
		}
		
		isClickShowBtn = 1;
		var data = "action=actn_show_details" + get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*txt_job_no*hide_job_id*txt_booking_no*txt_program_no*txt_date_from*txt_date_to', "../") + '&type=' + type;
		freeze_window(5);
		http.open("POST", "requires/program_wise_ref_creation_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = reponse_show_details;
	}
	
	//for reponse_show_details
    function reponse_show_details()
	{
        if (http.readyState == 4)
		{
            var response = http.responseText;
            $('#container_program_details').html(response);
            set_all_onclick();
            show_msg('18');
			setFilterGrid("table_body",-1);
			$('#id_clk_for_ref').css('display','block');
            release_freezing();
        }
    }

    //for openmypage_job
	function openmypage_job()
	{
        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var within_group = $("#cbo_within_group").val();
		
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        if(within_group == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false)
			{
				return;
			}
		}

        var page_link = 'requires/program_wise_ref_creation_controller.php?action=actn_job_popup&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
        ;
        var title = 'Style Ref./ Job No. Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var job_no = this.contentDoc.getElementById("hide_job_no").value;
            var job_id = this.contentDoc.getElementById("hide_job_id").value;

            $('#txt_job_no').val(job_no);
            $('#hide_job_id').val(job_id);
        }
    }
	
	//for openmypage_booking
    function openmypage_booking()
	{
        var companyID = $("#cbo_company_name").val();
        var cbo_within_group = $("#cbo_within_group").val();

        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }
		
        if(cbo_within_group == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false)
			{
				return;
			}
		}

        var page_link = 'requires/program_wise_ref_creation_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&cbo_within_group=' + cbo_within_group;
        var title = 'Booking Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=370px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
            $('#txt_booking_no').val(booking_no);
        }
    }
	
	//for selected_row
    function selected_row(rowNo)
	{
        var color = document.getElementById('tr_' + rowNo).style.backgroundColor;
        if (color != 'yellow')
		{
            $('#tr_'+rowNo).css('background-color', 'yellow');
        }
        else
		{
			$('#tr_'+rowNo).css('background-color', '#FFFFCC'); 
        }
    }
	
    //for openmypage_prog
	function openmypage_prog()
	{
        var tot_row = $('#table_body tbody tr').length-1;
        var prog_no = '';
		var selected_row = 0;
        for (var j = 1; j <= tot_row; j++)
		{
            var currentRowColor = document.getElementById('tr_'+j).style.backgroundColor;
            if (currentRowColor == 'yellow')
			{
                selected_row++;
                if (prog_no == '')
				{
                    prog_no = $('#program_no_'+j).text();
                }
                else
				{
                    prog_no += ","+$('#program_no_' + j).text();
                }
            }
        }
		
        if (selected_row < 1)
		{
            alert("Please Select At Least One Item");
            return;
        }

		var company_id = $('#cbo_company_name').val();
		var within_group = $('#cbo_within_group').val();
		
		var page_link = 'requires/program_wise_ref_creation_controller.php?action=actn_tube_ref_popup&prog_no='+prog_no+'&company_id='+company_id+'&within_group='+within_group;
        var title = 'Tube/Ref. Entry Pop-Up';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=450px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose=function()
		{	
            func_show_details(1);
		}
    }

    function fnc_close()
	{
        var data = '';
        //$('#selected_data').val(data);
        parent.emailwindow.hide();
    }
</script>
</head>
<body>
    <div style="width:100%;" align="center">
        <form name="refCreation_1" id="refCreation_1">
          <? echo load_freeze_divs("../", $permission); ?>
          <h3 style="width:1030px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">Program Wise Ref. Creation</h3>
          <div id="content_search_panel">
            <fieldset style="width:1030px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Within Group</th>
                    <th>PO Company</th>
                    <th>Sales Order No</th>
                    <th>Booking No</th>
                    <th>Program No</th>
                    <th class="must_entry_caption">Program Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('refCreation_1','container_program_details','','','')" class="formbutton" style="width:100px"/></th>
                 </thead>
                 <tbody>
                    <tr class="general">
                        <td>
                         <?
                         echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
                         ?>
                     </td>
                     <td>
                         <?php echo create_drop_down("cbo_within_group", 120, $yes_no, "", 0, "-- Select --", 0, ""); ?>
                     </td>
                     <td>
                         <?
                         echo create_drop_down("cbo_buyer_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
                         ?>
                     </td>
                     <td>
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:110px" placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                        <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                    </td>
                    <td>
                        <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:110px" placeholder="Browse Or Write" onDblClick="openmypage_booking();">
                    </td>                    <td>
                        <input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:110px" placeholder="Write">
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                    </td>
                    <td>
                    	<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="func_show_details(1)"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>
<div style="width:100%;margin-top:10px; display:none;" id="id_clk_for_ref">
    <input type="button" value="Click For Ref." name="generate" id="generate" class="formbuttonplasminus" style="width:120px" onClick="openmypage_prog()"/>
</div>
</form>
<div id="container_program_details" align="center" style="width:1120px;margin-top:10px;"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>