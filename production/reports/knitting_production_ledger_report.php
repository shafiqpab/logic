

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Bundle Status Report.
Functionality	:	
JS Functions	:
Created by		:	Abdul Barik Tipu
Creation date 	: 	14-09-2021
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

//-----------------------------------------------------------------------------------------------------
echo load_html_head_contents("Knitting Production Ledger Report", "../../", 1, 1,'',1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
 	var permission = '<? echo $permission; ?>';

 	var tableFilters = 
	{
		//col_30: "none",
		col_operation: {
		id: ["value_total_booking_qty","value_total_grey_qty","value_total_finish_qty","value_total_deliv_qty","value_total_stock_qty"],
		col: [14,16,17,19,20],
		operation: ["sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_machine() 
    {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var cbo_floor_id = $("#cbo_floor_id").val();
        var page_link = 'requires/knitting_production_ledger_report_controller.php?action=machine_no_search_popup&company_id=' + companyID + '&floor_id=' + cbo_floor_id;
        var title = 'Machine No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var order_no = this.contentDoc.getElementById("hide_order_no").value;
            var order_id = this.contentDoc.getElementById("hide_order_id").value;

            $('#txt_machine_name').val(order_no);
            $('#txt_machine_id').val(order_id);
        }
    }
	
	function openmypage_sales_order() 
	{
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var companyID = $("#cbo_company_name").val();
		
		var page_link='requires/knitting_production_ledger_report_controller.php?action=sales_order_no_search_popup&companyID='+companyID;

		var title = 'Sales Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_sales_job_no').val(exdata[1]);
				$('#hide_job_id').val(exdata[0]);	 
			}
		}
	}

	function onchange_function_shift(shift)
	{
		/*if (shift>0) 
		{
			$("#txt_date_from").prop("disabled", true);
			$("#txt_date_to").prop("disabled", true);
		}
		else
		{
			$("#txt_date_from").prop("disabled", false);
			$("#txt_date_to").prop("disabled", false);
		}*/
	}
	
	/*
	|--------------------------------------------------------------------------
	| fnc_report_generated
	|--------------------------------------------------------------------------
	|
	*/
	function fnc_report_generated()
	{
		var get_upto = $("#cbo_get_upto").val();
		var txt_qty = $("#txt_qty").val();
		if (get_upto>0 && txt_qty=="") 
		{
			if(form_validation('txt_qty','Upto Quantity')==false)
			{
				return;
			}
		}

		var sales_no = $('#txt_sales_job_no').val();
	    if(sales_no=="")
	    {
	        if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From Date*To Date')==false )
	        {
	            return;
	        }
	    }
	    else
	    {
	        if( form_validation('cbo_company_name','Company')==false )
	        {
	            return;
	        }
	    }
	    /*
		if(form_validation('cbo_company_name*txt_date_from','Company*From Date')==false)
		{
			return;
		}
		else
		{*/
			var report_title=$( "div.form_caption" ).html();
			var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_knitting_source*cbo_floor_id*txt_machine_name*txt_machine_id*txt_sales_job_no*hide_job_id*cbo_get_upto*txt_qty*cbo_knitting_status*cbo_Shift_id*txt_date_from*txt_date_to', "../../")+'&report_title='+report_title;
			// alert(data);return;
			freeze_window(3);
			http.open("POST","requires/knitting_production_ledger_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		//}
	}

	/*
	|--------------------------------------------------------------------------
	| fnc_report_generated_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			/*var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// setFilterGrid("tbl_list_dtls",-1,tableFilters);
	 		show_msg('3');
			release_freezing();*/

			var reponse=trim(http.responseText).split("####"); 
			show_msg('3');
			release_freezing();
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../');
 			append_report_checkbox('table_header_1',1);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| fnc_print_and_excel_file_generated
	|--------------------------------------------------------------------------
	|
	*/
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		// $('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		// $('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}


	  $(function() {
	    $( "#txt_date_from" ).datepicker({ 
	    	duration:"slow", 
	    	showAnim:"show", 
	    	dateFormat:"dd-mm-yy", 
	    	maxDate: "0D" 
	    });
	  }); 
	
</script>
<style type="text/css">
	.datepickerCss {
	    height: 18px;
	    font-size: 11px;
	    line-height: 16px;
	    padding: 0 5px;
	    text-align: left;
	    border: 1px solid #676767;
	    border-radius: 3px;
	    border-radius: .5em;
	}
</style>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="bulk_yarn_allocation_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 align="left" id="accordion_h1" style="width:1260px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1260px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
							<th>Knitting Source</th>
							<th>Floor</th>
							<th>Machine Name</th>
							<th>Sales Order No</th>
							<th>Get Upto Qty</th>
							<th>Qty</th>
							<th>Knitting Status</th>
							<th>Shift</th>
							<th class="must_entry_caption" colspan="2">Production Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('bulk_yarn_allocation_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                      	<td>
							<?php
							echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/knitting_production_ledger_report_controller',this.value, 'load_drop_down_floor', 'prod_floor_td' );");
							?>
						</td>
						<td>
							<?php
							echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 1,"",0,'1,3');
							?>
						</td>
						<td id="prod_floor_td">
							<? echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
						</td>
						<td align="center">
                            <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:100px" placeholder="Browse Machine" onDblClick="openmypage_machine()" readonly />
                            <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:80px"  />
                        </td>
						<td>
							<input type="text" name="txt_sales_job_no" id="txt_sales_job_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_sales_order();" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						</td>
						<td>
							<?
							$get_upto_arr=array(0=>'All',1=>'Greater Than',2=>'Greater/Equal',3=>'Equal');
							echo create_drop_down("cbo_get_upto",100,$get_upto_arr,"", 0, "-- Select --", 0,"",0);
							?>
						</td>
						<td>
							<input type="text" name="txt_qty" id="txt_qty" class="text_boxes_numeric" style="width:50px" placeholder="Write" />
						</td>
						<td>
							<?
							$knitting_status_arr=array(0=>'All',1=>'Partial Complete',2=>'Full Complete');
							echo create_drop_down("cbo_knitting_status",100,$knitting_status_arr,"", 0, "-- Select --", 0,"",0);
							?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_Shift_id",100,$shift_name,"", 1, "-- Select --", 0,"onchange_function_shift(this.value);",0);
							?>
						</td>
						<td>
							<!-- <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" > -->
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepickerCss" style="width:70px" placeholder="From Date" >
						</td>
						<td>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							placeholder="To Date" value="<? echo date("d-m-Y"); ?>" readonly="" disabled="">
						</td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div align="center" id="report_container2"></div>
   
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
