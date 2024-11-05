<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Roll and Barcode Wise Knitting Production Report
Functionality	:
JS Functions	:

Created by		:	Md. Abu Sayed
Creation date 	: 	05-10-2022
Updated by 		: 		
Update date		: 		   

QC Performed BY	:
QC Date			:
Comments		:

*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Roll and Barcode Wise Knitting Production Report", "../../", 1, 1,'',1,1);

?>
<script src="../../Chart.js-master/Chart.js"></script>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	/* var tableFilters = 
	 {
		col_60: "none",
		col_operation: {
		id: ["value_total_grey_qnty"],
	   	col: [15],
	   	operation: ["sum"],
	   	write_method: ["innerHTML"]
		}
	 } */
	 
	function fn_report_generated(type)
	{
		var txt_sales_order_no=$('#txt_sales_order_no').val();
		var txt_booking_no=$('#txt_booking_no').val();
		var txt_program_no=$('#txt_program_no').val();
		var txt_style_ref_no=$('#txt_style_ref_no').val();
		
        if( txt_sales_order_no !="" || txt_booking_no != "" || txt_program_no != "" || txt_style_ref_no != "" )
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date*To date')==false)
			{
				return;
			}
		}

		
        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_style_ref_no*txt_booking_no*txt_sales_order_no*txt_program_no*txt_date_from*txt_date_to*cbo_knitting_source*hdn_sales_order_no',"../../")+'&report_title='+report_title;
    
		//alert(data);
		freeze_window(5);
		http.open("POST","requires/roll_and_barcode_wise_knitting_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
		//	setFilterGrid("table_body",-1,tableFilters);

            setFilterGrid('table_body',-1);
			setFilterGrid('table_body2',-1);
			
			
		}
	}
		
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
        document.getElementById('scroll_body2').style.overflow="auto";
        document.getElementById('scroll_body2').style.maxHeight="none";
        $('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
        $('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
        document.getElementById('scroll_body2').style.overflowY="scroll";
        document.getElementById('scroll_body2').style.maxHeight="330px";
	}

	function openmypage_sales_order() 
	{
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year").val();
        var ordType = $("#cbo_type").val();
        var cbo_within_group = $("#cbo_within_group").val();
        var page_link = 'requires/roll_and_barcode_wise_knitting_production_report_controller.php?action=sales_order_no_search_popup&companyID=' + companyID + '&yearID=' + yearID + '&ordType=' + ordType + '&cbo_within_group=' + cbo_within_group;
        var title = 'Sales Order No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_job_no = this.contentDoc.getElementById("hidden_job_no").value;

            $('#txt_sales_order_no').val(sales_job_no);
            $('#hdn_sales_order_no').val(sales_job_no);
        }
    }

	function openmypage_booking_no() 
	{
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year").val();
        var ordType = $("#cbo_type").val();
        var page_link = 'requires/roll_and_barcode_wise_knitting_production_report_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&yearID=' + yearID + '&ordType=' + ordType;
        var title = 'Booking No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hidden_booking_data").value;

            $('#txt_booking_no').val(booking_no);
        }
    }
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="knitDyeingLoadReport_1" id="knitDyeingLoadReport_1"> 
         <h3 style="width:1070px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1060px;">
                <table class="rpt_table" width="1060" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="150">Company</th>
                            <th width="100">Style Ref.</th>
                            <th width="100">Fabric Booking</th>
                            <th width="100">Fabric Sales Order</th>
                             <th width="100">Program No</th>
                            <th class="must_entry_caption" width="170" >Production Date</th>
                            <th width="100">Knitting Source</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knitDyeingLoadReport_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
									<?
                                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" placeholder="Write" style="width:110px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" placeholder="Write/Browse" style="width:110px" onDblClick="openmypage_booking_no();"/>
									
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" placeholder="Write/Browse" style="width:110px" onDblClick="openmypage_sales_order();" />
									 <input type="hidden" name="hdn_sales_order_no" id="hdn_sales_order_no" class="text_boxes"  style="width:110px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" placeholder="Write" style="width:110px" />
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                                    &nbsp;To&nbsp;
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                                </td>
                                <td>
				                <? 
                                    echo create_drop_down( "cbo_knitting_source", 100, $knitting_source,"", 1, "-All-", $selected, "",0,"" );
                                ?>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" /></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="8">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
</body>
<script>
      set_multiselect('cbo_company_name','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>