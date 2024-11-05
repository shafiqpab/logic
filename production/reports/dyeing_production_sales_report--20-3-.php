<?
/* -------------------------------------------- Comments

  Purpose			: 	This form will Create Dyeing Production Sales Reprot
  Functionality	:
  JS Functions	:
  Created by		:	
  Creation date 	: 	
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
echo load_html_head_contents("Daily Dyeing Production Report", "../../", 1, 1, '', '', '');
?>	
<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';
    var str_color = [<? echo substr(return_library_autocomplete("select color_name from lib_color group by color_name", "color_name"), 0, -1); ?>];
    $(document).ready(function (e)
    {
        $("#txt_color").autocomplete({
            source: str_color
        });
    });


    function fn_dyeing_report_generated(operation)
    {
        var b_number = document.getElementById('batch_number').value;
        var batch_no = document.getElementById('batch_number_show').value;
        var fso_no = document.getElementById('fso_no').value;
        var hidden_fso_no = document.getElementById('hidden_fso_no').value;
        var j_number = document.getElementById('job_number_show').value;
        var j_number_hidden = document.getElementById('job_number').value;
        var hidden_booking_no = document.getElementById('hidden_booking_no').value;
        var booking_no = document.getElementById('booking_no').value;

		var company = document.getElementById('cbo_company_name').value;
		var workingCompany = document.getElementById('cbo_working_company_name').value;
		
		var txt_date_from = document.getElementById('txt_date_from').value;
		var txt_date_to = document.getElementById('txt_date_to').value;
       
		if (j_number != "" || j_number_hidden != "" || b_number != "" || batch_no != "" || fso_no != "" ||  hidden_fso_no != "" || booking_no!="")
        {
            if (form_validation('cbo_working_company_name', 'Working Company') == false)
            {
                return;
            }
        } 
        else if(workingCompany==0)
        {
            if (form_validation('cbo_working_company_name*txt_date_from*txt_date_to', 'Working Company*From date Fill*To date Fill') == false)
            {
                return;
            }
        }
		else if(txt_date_from=="" || txt_date_to=="")
		{
			 if (form_validation('txt_date_from*txt_date_to', 'From date Fill*To date Fill') == false)
            {
                return;
            }
			
		}
        freeze_window(5);
        if (operation == 1)
        {
    var data = "action=generate_report&operation=" + operation + get_submitted_data_string('cbo_company_name*cbo_buyer_name*job_number_show*job_number*batch_number*batch_number_show*fso_no*hidden_fso_no*booking_no*hidden_booking_no*cbo_type*cbo_year*txt_date_from*txt_date_to*cbo_result_name*cbo_batch_type*txt_machine_name*txt_machine_id*cbo_floor_id*cbo_shift_name*cbo_group_by*search_type*cbo_prod_type*cbo_party_id*cbo_working_company_name', "../../");
        } 
		
		
        http.open("POST", "requires/dyeing_production_sales_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_show_batch_report;
    }

    function fnc_show_batch_report()
    {
        if (http.readyState == 4)
        {
            var reponse = trim(http.responseText).split("****");
            document.getElementById('report_container2').innerHTML = http.responseText;
            document.getElementById('report_container').innerHTML = report_convert_button('../../');
            var batch_type = document.getElementById('cbo_batch_type').value;
            var group_by = document.getElementById('cbo_group_by').value;
            var cbo_type = document.getElementById('cbo_type').value;
            append_report_checkbox('table_header_1', 1);
            //append_report_checkbox('table_header_2', 1);
            //append_report_checkbox('table_header_3', 1);
            

            $("#report_container2").html(reponse[0]); 
			
            if (cbo_type == 1)
            {
                if (group_by == 0)
                {
                    var tableFilters =
                    {
                        col_operation: {
                            id: ["value_grand_batch_qnty","value_grand_trims_qnty","value_grand_tot_batch_wgt"],
                            col: [13,14,15],
                            operation: ["sum","sum","sum"],
                            write_method: ["innerHTML","innerHTML","innerHTML"]
                        }
                    }
                    setFilterGrid("table_body", -1,tableFilters);
                }
            }

 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; //flt
		$(".flt").css("display","none");

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css("display","block");
	}
  

    function batchnumber()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var buyer_name = $("#cbo_buyer_name").val();
        var batch_type = document.getElementById('cbo_batch_type').value;
        var page_link='requires/dyeing_production_sales_report_controller.php?action=batch_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name + "&batch_type=" + batch_type;
        var title='Batch Number';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hide_batch_no=this.contentDoc.getElementById("hide_batch_no").value;
            var hide_batch_id=this.contentDoc.getElementById("hide_batch_id").value;
            $('#batch_number_show').val(hide_batch_no);
            $('#batch_number').val(hide_batch_id);   
        }
    }

    function jobnumber(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
        var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
        var batch_type = document.getElementById('cbo_batch_type').value;
        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/dyeing_production_sales_report_controller.php?action=jobnumbershow&company_id=" + company_name + "&cbo_buyer_name=" + cbo_buyer_name + "&year=" + year + "&batch_type=" + batch_type;
        var title = "Job Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=615px,height=420px,center=1,resize=0,scrolling=0', '../')

        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            //var job=theemail.split("_");
            document.getElementById('job_number').value = theemail;
            document.getElementById('job_number_show').value = theemail;
            release_freezing();
        }
    }

    function openmypage_fso(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var buyer_name = document.getElementById('cbo_buyer_name').value;
        var year = document.getElementById('cbo_year').value;

        var batch_type = document.getElementById('cbo_batch_type').value;
        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/dyeing_production_sales_report_controller.php?action=FSO_No_popup&company_name=" + company_name + "&buyer_name=" + buyer_name + "&year=" + year + "&batch_type=" + batch_type;
        var title = "Order Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=420px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theform=this.contentDoc.forms[0];
            var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
            var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
            $('#fso_no').val(fso_no);
            $('#hidden_fso_no').val(fso_id);

            release_freezing();
        }
    }

    function openmypage_booking()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var cbo_year = $("#cbo_year").val();

        var page_link='requires/dyeing_production_sales_report_controller.php?action=booking_no_popup&companyID='+companyID+ '&cbo_year='+cbo_year;
        var title='Booking No Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var booking_no=this.contentDoc.getElementById("hide_job_no").value;
            var booking_id=this.contentDoc.getElementById("hide_job_id").value;
            $('#hidden_booking_no').val(booking_no);
            $('#booking_no').val(booking_no);
        }
    }

    function toggle(x, origColor) {
        var newColor = 'green';
        if (x.style) {
            x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
        }
    }

    function js_set_value(str) {
        toggle(document.getElementById('tr_' + str), '#FFF');
    }

    function openmypage_color(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var buyer_name = document.getElementById('cbo_buyer_name').value;
        var txtcolor = document.getElementById('txt_color').value;
        var job_number = document.getElementById('job_number_show').value;
        var batch_number = document.getElementById('batch_number_show').value;
        //var ext_number=document.getElementById('txt_ext_no').value;
        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/dyeing_production_sales_report_controller.php?action=check_color_id&txtcolor=" + txtcolor;
        var title = "Color Name";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=350px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            var split_value = theemail.split('_');
            //alert(theemail);
            document.getElementById('hidden_color_id').value = split_value[0];
            document.getElementById('txt_color').value = split_value[1];
            release_freezing();
        }
    }

    function change_color(v_id, e_color)
    {
        if (document.getElementById(v_id).bgColor == "#33CC00")
        {
            document.getElementById(v_id).bgColor = e_color;
        } else
        {
            document.getElementById(v_id).bgColor = "#33CC00";
        }
    }

    function openmypage_machine()
    {
        if (form_validation('cbo_company_name', 'Company Name') == false)
        {
            return;
        }
        var data = document.getElementById('cbo_company_name').value;//+"_"+document.getElementById('cbo_location_id').value
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_production_sales_report_controller.php?action=machine_no_popup&data=' + data, 'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0', '../')

        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("hid_machine_id");
            var theemailv = this.contentDoc.getElementById("hid_machine_name");
            var response = theemail.value.split('_');
            if (theemail.value != "")
            {
                freeze_window(5);
                document.getElementById("txt_machine_id").value = theemail.value;
                document.getElementById("txt_machine_name").value = theemailv.value;
                release_freezing();
            }
        }
    }

    function fabric_dtls_poup(batch_id, dia_type, order_id, action)
    {
        var company_name = document.getElementById('cbo_company_name').value;
        var popup_width = '500px';
        //alert(batch_id);
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_production_sales_report_controller.php?company_name=' + company_name + '&batch_id=' + batch_id + '&dia_type=' + dia_type + '&order_id=' + order_id + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=350px,center=1,resize=0,scrolling=0', '../');
    }
    function search_populate(str)
    {
        if (str == 1)
        {
            document.getElementById('search_by_th_up').innerHTML = "Dyeing Date";
            $('#search_by_th_up').css('color', 'blue');
        } else if (str == 2)
        {
            document.getElementById('search_by_th_up').innerHTML = "Insert Date";
            $('#search_by_th_up').css('color', 'blue');
        }

    }
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../../", ''); ?>
        <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
            <h3 style="width:1670px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')">-Search Panel</h3>         <div id="content_search_panel" >      
                <fieldset style="width:1670px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        <th width="80" class="must_entry_caption">Report Type</th>
                        <th width="70">Batch Type</th>
                        <th width="100" class="must_entry_caption">Working Company</th>
                        <th width="80">LC Company</th>
                        <th width="70">Prod. Type</th>
                        <th width="70">Party</th>
                        <th width="100">Buyer</th>
                        <th width="50">Job Year</th>
                        <th width="50">Job No</th>
                        <th width="60">FSO No</th>
                        <th width="60">Booking No</th>
                        <th width="60">Batch No</th>
                        <th width="100">Floor</th>
                        <th width="50">Shift</th>
                        <th width="60">Machine</th>
                        <th width="80">Result</th>
                        <th width="70">Group By</th>
                        <th width="60">Search By</th>
                        <th width="150" id="search_by_th_up" class="must_entry_caption">Dyeing Date</th>
                        <th  width="">&nbsp;</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <?
                                    $search_by_arr = array(1 => "Dyeing WIP", 2 => "Dyeing Production Report");
                                    echo create_drop_down("cbo_type", 80, $search_by_arr, "", 0, "", 2, '', 0);
                                    ?>
                                </td>
                                <td>
                                    <?
                                    $batch_type_arr = array(1 => "Self Batch", 2 => "In bound SubCon Batch");
                                    echo create_drop_down("cbo_batch_type", 70, $batch_type_arr, "", 1, "--All--", 0, "load_drop_down('requires/dyeing_production_sales_report_controller',document.getElementById('cbo_working_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td' );", 0);
                                    ?>
                                </td>
                                 
                                 <td> 
                                    <?
                                echo create_drop_down("cbo_working_company_name", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Working Company --", 0, "load_drop_down('requires/dyeing_production_sales_report_controller', this.value+'_'+ document.getElementById('cbo_batch_type').value , 'load_drop_down_buyer' ,'cbo_buyer_name_td' );load_drop_down( 'requires/dyeing_production_sales_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );");
                                    ?>
                                </td>

                                <td> 
                                    <?
                                    echo create_drop_down("cbo_company_name", 80, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Company --", 0, "");
                                    ?>
                                </td>
                                
                                <td id="">
                                    <?
									 echo create_drop_down("cbo_prod_type",70,$knitting_source,"", 1, "-- Select Source --", 1,"load_drop_down( 'requires/dyeing_production_sales_report_controller',this.value,'load_drop_down_knitting_com','cbo_party_td' );",0,'1,3');
                                    ?>
                                </td>
                                <td id="cbo_party_td">
                                    <?
								  	echo create_drop_down("cbo_party_id", 70, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", "", "", "");
                                    ?>
                                </td>
                                <td id="cbo_buyer_name_td">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 100, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 0, "");
                                    ?>
                                </td>
                                <td>
                                    <?
									// date("Y", time())
                                    echo create_drop_down("cbo_year", 50, create_year_array(), "", 1, "-- All --","", "", 0, "");
                                    ?>
                                </td>
                                <td>
                                    <input type="text"  name="job_number_show" id="job_number_show" class="text_boxes" style="width:50px;" placeholder="Browse" onDblClick="jobnumber();" readonly>
                                    <input type="hidden" name="job_number" id="job_number">
                                </td>
                                
                                <td>
                                    <input type="text"  name="fso_no" id="fso_no" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="openmypage_fso()">
                                    <input type="hidden" name="hidden_fso_no" id="hidden_fso_no">
                                </td>
                                <td>
                                    <input type="text"  name="booking_no" id="booking_no" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="openmypage_booking()">
                                    <input type="hidden" name="hidden_booking_no" id="hidden_booking_no">
                                </td>
                                <td>
                                    <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="batchnumber();">
                                    <input type="hidden" name="batch_number" id="batch_number">
                                </td>
                                <td id="floor_td">
                                    <? echo create_drop_down("cbo_floor_id", 100, $blank_array, "", 1, "-Select Floor-", 0, "", 1); ?>
                                </td>
                                <td>
                                    <? echo create_drop_down("cbo_shift_name", 50, $shift_name, "", 1, "--Shift--", 0, "", 0, "", "", "", "", ""); ?>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_machine()" readonly />
                                    <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:50px" />
                                </td>
                                <td>
                                    <? echo create_drop_down("cbo_result_name", 80, $dyeing_result, "", 1, "-Select Result-", 0, "", 0, "", "", "", "", ""); ?>
                                </td>
                                <td>
                                    <?
                                    $group_type_arr = array(1 => "Floor", 2 => "Shift", 3 => "Machine");
                                    echo create_drop_down("cbo_group_by", 70, $group_type_arr, "", 1, "-Select-", 0, "", 0, "", "", "", "", "");
                                    ?>
                                </td>
                                <td>
                                    <?
                                    $search_type_arr = array(1 => "Dyeing Date", 2 => "Insert Date");
                                    $fnc_name = "search_populate(this.value)";
                                    echo create_drop_down("search_type", 60, $search_type_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
                                    ?>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_dyeing_report_generated(1)" />
                                    <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1', 'report_container*report_container2', '', '', '')" class="formbutton" style="width:60px" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="20" align="center">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </fieldset>
            </div>
            <div id="report_container"></div>
            <div id="report_container2"></div>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>