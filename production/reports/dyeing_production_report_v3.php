<?
/* -------------------------------------------- Comments

  Purpose           :   This form will Create Dyeing Production Reprot V3
  Functionality :
  JS Functions  :
  Created by        :   Aziz
  Creation date     :   23-03-2019
  Updated by        :
  Update date       :
  QC Performed BY   :
  QC Date           :
  Comments      :

 */
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Dyeing Production Report V3", "../../", 1, 1, '', '', '');
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

    var tableFilters =
            {
                col_17: "none",
                col_operation: {
                    id: ["grand_total_td_batch_qty","value_total_batch_trim_qty"],
                    col: [9,10],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                }
            }
			 var tableFilters2 =
            {
                col_17: "none",
                col_operation: {
                    id: ["value_total_batch_qty2","value_total_batch_trim_qty2"],
                    col: [9,10],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                }
            }
			 var tableFilters3 =
            {
                col_17: "none",
                col_operation: {
                    id: ["value_total_batch_qty3","value_total_batch_trim_qty3"],
                    col: [9,10],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                }
            }

    function fn_dyeing_report_generated(operation)
    {
        // alert(operation);
        var b_number = document.getElementById('batch_number').value;
        var batch_no = document.getElementById('batch_number_show').value;
        var order_no = document.getElementById('order_no').value;
        var order_no_hidden = document.getElementById('hidden_order_no').value;
        var j_number = document.getElementById('job_number_show').value;
        var j_number_hidden = document.getElementById('job_number').value;
      
        var company = document.getElementById('cbo_company_name').value;
        var booking_no = document.getElementById('txt_booking_no').value;
        //  var txt_booking_id = document.getElementById('txt_booking_id').value;
        var workingCompany = document.getElementById('cbo_working_company_name').value;

        var txt_date_from = document.getElementById('txt_date_from').value;
        var txt_date_to = document.getElementById('txt_date_to').value;
		var cbo_group_by = document.getElementById('cbo_group_by').value;
     
        if (j_number != "" || j_number_hidden != "" || b_number != "" || batch_no != "" || order_no != "" || order_no_hidden != "" || booking_no != "")
        {
            if (form_validation('cbo_company_name', 'Company') == false)
            {
                return;
            }
        }
        else if(company==0 && workingCompany==0)
        {
            if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company*From date Fill*To date Fill') == false)
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
		//
		if(operation==3)
		{
			if(cbo_group_by!=3)
			{
					alert('Please Select Group by Machine.');
					return;
			}
		}
		if(operation!=3)
		{
			if(operation==2)
			{
				$("#check_uncheck").attr("checked",false);
				
				document.getElementById("check_uncheck_tr").style.display="table";
					if($("#check_uncheck").is(":checked")==false){
						$("#check_uncheck").attr("checked","checked");
		
					}else{
						$("#check_uncheck").removeAttr("checked","checked");
					}
			}
			else
			{
				$("#check_uncheck").attr("checked",false);
				$("#check_uncheck").removeAttr("checked","checked");
				document.getElementById("check_uncheck_tr").style.display="none";
			
			}
		}
		if (operation==4) 
        {
            action="report_generate_construction_wise";
        }
        else
        {
            action="generate_report";
        }	
        freeze_window(5);
        var data = "action="+action+"&operation=" + operation + get_submitted_data_string('cbo_company_name*cbo_buyer_name*job_number_show*job_number*batch_number*batch_number_show*order_no*hidden_order_no*cbo_type*cbo_year*txt_date_from*txt_date_to*cbo_result_name*cbo_batch_type*cbo_group_by*search_type*cbo_prod_type*cbo_party_id*cbo_working_company_name*txt_hide_booking_id*txt_booking_no', "../../");
            //alert(data);
        http.open("POST", "requires/dyeing_production_report_controller_v3.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_show_batch_report;
    }

    function fnc_show_batch_report()
    {
        if (http.readyState == 4)
        {
            var response = trim(http.responseText).split("****");
            //alert(reponse);
            //$('#report_container2').html(response[0]);
            // document.getElementById('report_container2').innerHTML = http.responseText;
            // document.getElementById('report_container').innerHTML = report_convert_button('../../');
            //show_msg('3');
            //release_freezing();
              $("#report_container2").html(response[0]);
            //console.log(response[0]);
          // alert(response[2]);
          
			//document.getElementById('report_container').innerHTML='';
				//document.getElementById('report_container').innerHTML = report_convert_button('../../');
			if(response[2]!=3)
			{
				document.getElementById('report_container').innerHTML = report_convert_button('../../');
			}
			else
			{
            document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			
                append_report_checkbox('table_header_1', 1);
                append_report_checkbox('table_header_2', 1);
                append_report_checkbox('table_header_3', 1);	
		  
            var batch_type = document.getElementById('cbo_batch_type').value*1;
            var group_by = document.getElementById('cbo_group_by').value*1;
            if (batch_type == 1 || batch_type == 0)
            {
               // alert(batch_type+'='+group_by);
				if (group_by == 0)
                {
                    setFilterGrid("table_body", -1, tableFilters);
					setFilterGrid("table_body2", -1, tableFilters2);
					setFilterGrid("table_body3", -1, tableFilters3);
                }
            }
			else if (batch_type == 2)
            {
                if (group_by == 0)
                {
                    setFilterGrid("table_body2", -1, tableFilters2);
                }
            }
			else if (batch_type == 3)
            {
                if (group_by == 0)
                {
                    setFilterGrid("table_body3", -1, tableFilters3);
                }
            } else
            {

                if (group_by == 0)
                {
                    setFilterGrid("table_body", -1, tableFilters);
                    setFilterGrid("table_body2", -1, tableFilters2);
                }
            }        
            show_msg('3');
            release_freezing();
        }
    }
   function new_window()
  {
        if(document.getElementById('scroll_body'))
        { document.getElementById('scroll_body').style.overflow="auto";
       	 document.getElementById('scroll_body').style.maxHeight="none";
		}

        if(document.getElementById('scroll_body_sub'))
         {
             document.getElementById('scroll_body_sub').style.overflow="auto";
             document.getElementById('scroll_body_sub').style.maxHeight="none";
        }
	  if(document.getElementById('scroll_body_samp'))
       {
            document.getElementById('scroll_body_samp').style.overflow="auto";
            document.getElementById('scroll_body_samp').style.maxHeight="none";
        }

		$('#table_body tr:first').hide();$('#table_body2 tr:first').hide();$('#table_body3 tr:first').hide();
       var w = window.open("Surprise", "#");
         var d = w.document.open();
         d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
         '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
       d.close();
		 $('#table_body tr:first').show();$('#table_body2 tr:first').show();$('#table_body3 tr:first').show();
       if(document.getElementById('scroll_body'))
       {
         document.getElementById('scroll_body').style.overflowY="auto";
        document.getElementById('scroll_body').style.maxHeight="400px";
		}

         if(document.getElementById('scroll_body_sub'))
         {
             document.getElementById('scroll_body_sub').style.overflowY="auto";
            document.getElementById('scroll_body_sub').style.maxHeight="400px";
        }
		 if(document.getElementById('scroll_body_samp'))
         {
             document.getElementById('scroll_body_samp').style.overflowY="auto";
            document.getElementById('scroll_body_samp').style.maxHeight="400px";
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
    function batchnumber()
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var batch_number = document.getElementById('batch_number_show').value;
        var batch_type = document.getElementById('cbo_batch_type').value;
        var page_link = "requires/dyeing_production_report_controller_v3.php?action=batchnumbershow&company_name=" + company_name + "&batch_type=" + batch_type;
        var title = "Batch Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=0,scrolling=0', '../')

        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            var batch = theemail.split("_");
            document.getElementById('batch_number').value = batch[0];
            document.getElementById('batch_number_show').value = batch[1];
            release_freezing();
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
        var page_link = "requires/dyeing_production_report_controller_v3.php?action=jobnumbershow&company_id=" + company_name + "&cbo_buyer_name=" + cbo_buyer_name + "&year=" + year + "&batch_type=" + batch_type;
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

    function openmypage_order(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var buyer_name = document.getElementById('cbo_buyer_name').value;
        var year = document.getElementById('cbo_year').value;
        var job_number = document.getElementById('job_number_show').value;
        var batch_number = document.getElementById('batch_number_show').value;
        var batch_type = document.getElementById('cbo_batch_type').value;
        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/dyeing_production_report_controller_v3.php?action=order_number_popup&company_name=" + company_name + "&buyer_name=" + buyer_name + "&year=" + year + "&batch_type=" + batch_type;
        var title = "Order Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=515px,height=420px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("selected_id").value;
            //var job=theemail.split("_");
            document.getElementById('hidden_order_no').value = theemail;
            document.getElementById('order_no').value = theemail;
            release_freezing();
        }
    }

    function toggle(x, origColor)
    {
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
        var page_link = "requires/dyeing_production_report_controller_v3.php?action=check_color_id&txtcolor=" + txtcolor;
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
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_production_report_controller_v3.php?action=machine_no_popup&data=' + data, 'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0', '../')

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
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_production_report_controller_v3.php?company_name=' + company_name + '&batch_id=' + batch_id + '&dia_type=' + dia_type + '&order_id=' + order_id + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=350px,center=1,resize=0,scrolling=0', '../');
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
    function openmypage_booking()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var buyer_name = $("#cbo_buyer_name").val();
        var cbo_year_id = $("#cbo_year").val();
        //var cbo_month_id = $("#cbo_month").val();
        var page_link='requires/dyeing_production_report_controller_v3.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
        var title='Booking No Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
            var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
            $('#txt_booking_no').val(booking_no);
            $('#txt_hide_booking_id').val(booking_id);
        }
    }
	function print_report_part_by_part(id,button_id)
	{
		$("#dyeing_prod_print_button").hide();
		$(button_id).removeAttr("onClick").attr("onClick","javascript:window.print()");
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+document.getElementById(id).innerHTML+'</body</html>');
		d.close();
		$(button_id).removeAttr("onClick").attr("onClick","print_report_part_by_part('"+id+"','"+button_id+"')");
		
		
	}
	function fnc_report_setting(type)
	{
		//alert(type);
		if(type==1) 
		{ 
			var com_id=$('#cbo_company_name').val();
		}
		else  { var com_id=$('#cbo_working_company_name').val();
		}
		get_php_form_data(com_id,'print_button_variable_setting','requires/dyeing_production_report_controller_v3' ); 
		
	}
	function print_report_button_setting(report_ids) 
	{
		$("#show_button1").hide();
		$("#show_button2").hide();
        $("#show_button3").hide();
		$("#show_button4").hide();
		
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==23)//summary
			{
				$("#show_button2").show();
			}
			if(report_id[k]==108)//Show
			{
				$("#show_button1").show();
			}
			if(report_id[k]==138)//Machine Wise
			{
				$("#show_button3").show();
			}
            if(report_id[k]==290)//Construction Wise
            {
                $("#show_button4").show();
            }
		}
		
	}

	
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../../", ''); ?>
        <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
            <h3 style="width:1310px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')">-Search Panel</h3>         <div id="content_search_panel" >
                <fieldset style="width:1310px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        <th width="80" class="must_entry_caption">Report Type</th>
                        <th width="70">Batch Type</th>
                        <th width="80" class="must_entry_caption">Company</th>
                        <th width="100">Working Company</th>
                        <th width="70">Prod. Type</th>
                        <th width="70">Party</th>
                        <th width="100">Buyer</th>
                        <th width="50">Year</th>
                        <th width="50">Job No</th>
                        <th width="60">Booking No</th>
                        <th width="60">Batch No</th>
                        <th width="60">Order No</th>
                        <th width="80">Result</th>
                        <th width="70">Group By</th>
                        <th width="60">Search By</th>
                        <th width="200" id="search_by_th_up" class="must_entry_caption">Dyeing Date</th>
                        <th  width="70">&nbsp;</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <?
                                    $search_by_arr = array(1 => "Dyeing WIP", 2 => "Daily Dyeing Production", 3 => "Daily Right First Time", 4 => "Daily Dyeing Reprocess Summary");
                                    echo create_drop_down("cbo_type", 80, $search_by_arr, "", 0, "", 2, '', 0);
                                    ?>
                                </td>
                                <td>
                                    <?
                                    $batch_type_arr = array(1 => "Self Batch", 2 => "SubCon Batch", 3 => "Sample Dyeing Production");
                                    echo create_drop_down("cbo_batch_type", 70, $batch_type_arr, "", 1, "--All--", 0, "load_drop_down('requires/dyeing_production_report_controller_v3',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td' );", 0);
                                    ?>
                                </td>

                                <td>
                                    <?
                                    echo create_drop_down("cbo_company_name", 80, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/dyeing_production_report_controller_v3',this.value+'_'+document.getElementById('cbo_batch_type').value, 'load_drop_down_buyer','cbo_buyer_name_td' );fnc_report_setting(1);");
                                    ?>
                                </td>

                                 <td>
                                    <?
                                    echo create_drop_down("cbo_working_company_name", 80, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Working Company --", 0, "fnc_report_setting(2);");
                                    ?>
                                </td>

                                <td id="">
                                    <?
                                     echo create_drop_down("cbo_prod_type",70,$knitting_source,"", 1, "-- Select Source --", 1,"load_drop_down( 'requires/dyeing_production_report_controller_v3',this.value,'load_drop_down_knitting_com','cbo_party_td' );",0,'1,3');
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
                                    echo create_drop_down("cbo_year", 50, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
                                    ?>
                                </td>
                                <td>
                                    <input type="text"  name="job_number_show" id="job_number_show" class="text_boxes" style="width:50px;"placeholder="Wr/Br" onDblClick="jobnumber();">
                                    <input type="hidden" name="job_number" id="job_number">
                                </td>
                                <td>
                                <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:60px" placeholder="Br. Or Wr." onDblClick="openmypage_booking();" >
                                <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
                              </td>
                                <td>
                                    <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="batchnumber();">
                                    <input type="hidden" name="batch_number" id="batch_number">
                                </td>


                                <td>
                                    <input type="text"  name="order_no" id="order_no" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="openmypage_order()">
                                    <input type="hidden" name="hidden_order_no" id="hidden_order_no">
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
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:45px" placeholder="From Date"/>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:45px" placeholder="To Date"/>
                                </td>
                                <td><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1', 'report_container*report_container2', '', '', '')" class="formbutton" style="width:50px" /></td>
                            </tr>
                            <tr>
                                <td colspan="19" align="center">
                                    <? echo load_month_buttons(1); ?>

                                    <input type="button" id="show_button1" class="formbutton" style="width:50px;display: none;" value="Show" onClick="fn_dyeing_report_generated(2)" />&nbsp;&nbsp;
									<input type="button" id="show_button2" class="formbutton" style="width:70px;display: none;" value="Summary" onClick="fn_dyeing_report_generated(1)" />&nbsp;&nbsp;
                                    <input type="button" id="show_button3" class="formbutton" style="width:85px;display: none;" value="Machine Wise" onClick="fn_dyeing_report_generated(3)" />
                                    <input type="button" id="show_button4" class="formbutton" style="width:100px;display: none;" value="Construction Wise" onClick="fn_dyeing_report_generated(4)" />
                                    
                                </td>
                            </tr>
                             <tr >
                    			<td colspan="17" align="center" id="data_panel">
                       			</td>
                   			 </tr>
                    
                        </tbody>

                    </table>
                    <table align="left">
                        <tr>
                            <td><p id="check_uncheck_tr" style="display:none;"><input type="checkbox" id="check_uncheck" name="check_uncheck" onClick="fn_check_uncheck()"/> <strong style="color:#176aaa; font-size:14px; font-weight:bold;">Check/Uncheck All</strong> </p>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
            <div id="report_container" style="padding:10px;"></div>
            <div id="report_container2"></div>
            
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>