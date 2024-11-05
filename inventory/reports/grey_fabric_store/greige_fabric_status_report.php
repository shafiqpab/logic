<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Receive WIP Report
Functionality	:
JS Functions	:
Created by		:	Tipu
Creation date 	: 	22-02-2021
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Batch Receive WIP Report", "../../../", 1, 1,'','','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	var permission='<? echo $permission; ?>';


	function openmypage_job()
	{
	  	if( form_validation('cbo_company_name*cbo_type','Company Name*Type')==false )
	  	{
	   		return;
	 	}

		var companyID = $("#cbo_company_name").val();
		var cbo_type = $("#cbo_type").val();
		var cbo_year = $("#cbo_year").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/greige_fabric_status_report_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_type='+cbo_type+'&cbo_year='+cbo_year+'&buyer_name='+buyer_name;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}

	function openmypage_style()
	{
	  	if( form_validation('cbo_company_name*cbo_type','Company Name*Type')==false )
	  	{
	   		return;
	 	}

		var companyID = $("#cbo_company_name").val();
		var cbo_type = $("#cbo_type").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/greige_fabric_status_report_controller.php?action=style_no_popup&companyID='+companyID+'&cbo_type='+cbo_type+'&buyer_name='+buyer_name;
		var title='Style No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_style_no').val(style);
			$('#hidden_style').val(job_id);
		}
	}

	function openmypage_booking()
	{
	  	if( form_validation('cbo_company_name*cbo_type','Company Name*Type')==false )
	  	{
	   		return;
	 	}

		var companyID = $("#cbo_company_name").val();
		var cbo_type = $("#cbo_type").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/greige_fabric_status_report_controller.php?action=booking_no_popup&companyID='+companyID+'&cbo_type='+cbo_type+'&buyer_name='+buyer_name;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_booking_no').val(job_no);
			$('#hidden_booking_id').val(job_id);
		}
	}

	function openmypage_order()
	{
	  	if( form_validation('cbo_company_name*cbo_type','Company Name*Type')==false )
	  	{
	   		return;
	 	}

		var companyID = $("#cbo_company_name").val();
		var cbo_type = $("#cbo_type").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/greige_fabric_status_report_controller.php?action=order_no_popup&companyID='+companyID+'&cbo_type='+cbo_type+'&buyer_name='+buyer_name;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_order_no').val(job_no);
			$('#hidden_order_id').val(job_id);
		}
	}

	function fn_report_generated(operation)
	{
		var txt_job_no =  trim($("#txt_job_no").val());
		var txt_style_no =  trim($("#txt_style_no").val());
		var txt_order_no = trim($("#txt_order_no").val());
		var txt_booking_no =  trim($("#txt_booking_no").val());
		var validate_id = "";
		var validate_msg = "";
		if(txt_job_no == "" && txt_style_no == "" && txt_order_no == "" && txt_booking_no =="")
		{
			validate_id += "*txt_date_from*txt_date_to";
			validate_msg += "**Date From*Date To";
		}
		if (form_validation('cbo_company_name*cbo_type*cbo_report_criteria','Comapny Name*Type*Report Criteria')==false)
		{
			return;
		}
		var get_upto = $("#cbo_get_upto").val();
		var txt_days = $("#txt_days").val();
		var get_upto_qty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();
		if ( get_upto>0 && txt_days=="")
		{
			if(form_validation('txt_days','Upto Days')==false)
			{
				return;
			}
		}
		if (get_upto_qty>0 && txt_qnty=="")
		{
			if(form_validation('txt_qnty','Upto Quantity')==false)
			{
				return;
			}
		}

		if(operation==1 )
		{
			var r=confirm("Press \"OK\" to open with Summery\nPress \"Cancel\" to open without Summery");
			if (r==true)
			{
				show_summery="1";
			}
			else
			{
				show_summery="0";
			}
		}

		freeze_window(5);

	 	var data="action=generated_report&operation="+operation+"&show_summery="+show_summery+get_submitted_data_string('cbo_company_name*cbo_year*cbo_type*txt_job_no*txt_job_id*txt_style_no*hidden_style*txt_booking_no*hidden_booking_id*txt_order_no*hidden_order_id*cbo_stock_for*cbo_get_upto*txt_days*cbo_get_upto_qnty*txt_qnty*cbo_report_criteria*txt_date_from*txt_date_to*cbo_buyer_id*hidden_color_id',    "../../../");

	 	// alert(data);release_freezing();return;

		http.open("POST","requires/greige_fabric_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_report_response;
	}

	function fnc_show_report_response()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("##");
			$("#report_container2").html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

				/*setFilterGrid("table_body",-1,tableFilters);
				if(document.getElementById('table_body2'))
				{
					setFilterGrid("table_body2",-1,tableFilters_2);
				}*/


			show_msg('3');
			release_freezing();

		}
	}

	function new_window()
	{
		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			// $('#scroll_body tr:first').hide();
		}

		if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="none";
			// $('#scroll_body_subcon tr:first').hide();
		}

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="397px";
	        $('#scroll_body tr:first').show();
	    }

	    if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="397px";
	        $('#scroll_body_subcon tr:first').show();
		}
	}

	function openmypage_color() // For color
	{
		if( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var cbo_year = $("#cbo_year").val();
		var txt_job_id = $("#txt_job_id").val();
		var hidden_style_job = $("#hidden_style").val();
		if (txt_job_id=="") { var job_id=hidden_style_job; } else { var job_id=txt_job_id; }
		var booking_id = $("#hidden_booking_id").val();
		var order_id = $("#hidden_order_id").val();
		var company_id = $("#cbo_company_name").val();
		var page_link='requires/greige_fabric_status_report_controller.php?action=color_popup&job_id='+job_id+'&booking_id='+booking_id+'&order_id='+order_id+'&cbo_year='+cbo_year+'&company_id='+company_id;
		var title="Color Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=250px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var color_name=this.contentDoc.getElementById("txt_selected").value; // product ID
			$("#hidden_color_id").val(color_id);
			$("#txt_color_name").val(color_name);
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",''); ?>
		<form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
        	<h3 style="width:1680px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
	        <div id="content_search_panel" >
	            <fieldset style="width:1680px;">
	                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
	                    <thead>
	                        <th class="must_entry_caption">Company Name</th>
	                        <th>Job Year</th>
	                        <th class="must_entry_caption">Type</th>
	                        <th>Buyer</th>
	                        <th>Job</th>
	                        <th>Style</th>
	                        <th>Booking</th>
	                        <th>Order</th>
	                        <th>Color</th>
	                        <th>Stock For</th>
	                        <th>Get Upto</th>
		                    <th>Days</th>
		                    <th>Get Upto</th>
		                    <th>Quantity</th>
		                    <th class="must_entry_caption">Report Criteria</th>
	                        <th>Transaction Date</th>
	                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
	                    </thead>
	                    <tbody>
	                        <tr class="general">
	                            <td>
	                                <?
	                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/greige_fabric_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	                                ?>
	                            </td>
	                            <td>
					            	<? echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
					            </td>
					            <td>
	                            	<?
	                            		$type_arr = array('1' => 'Bulk', '2' => 'Sample With Order', '3' => 'Sample Non Order', '4' => 'Short' );
	                                	echo create_drop_down( "cbo_type", 100, $type_arr,"", 1,"-- Select --", 0, "",0,"" );
									?>
	                            </td>
	                            <td id="buyer_td">
	                                <?
	                                echo create_drop_down( "cbo_buyer_id", 130,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
	                                ?>
	                            </td>
	                            <td>
					            	<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
					              	<input type="hidden" id="txt_job_id" name="txt_job_id"/>
					            </td>
					            <td>
					            	<input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:100px" onDblClick="openmypage_style();" placeholder="Browse/Write" />
					              	<input type="hidden" id="hidden_style" name="hidden_style"/>
					            </td>
					            <td>
					            	<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
					              	<input type="hidden" id="hidden_booking_id" name="hidden_booking_id"/>
					            </td>
					            <td>
					            	<input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:100px" onDblClick="openmypage_order();" placeholder="Browse/Write" />
					              	<input type="hidden" id="hidden_order_id" name="hidden_order_id"/>
					            </td>
					            <td>
									<input type="text" id="txt_color_name"  name="txt_color_name"  style="width:100px" class="text_boxes" placeholder="Browse"  onDblClick="openmypage_color()" readonly="true" />
									<input type="hidden" id="hidden_color_id"  name="hidden_color_id" />
			                    </td>
					            <td>
	                            	<?
	                            		$stock_for_arr = array('1' => 'Running Order', '2' => 'Cancelled Order', '3' => 'Left Over' );
	                                	echo create_drop_down( "cbo_stock_for", 120, $stock_for_arr,"", 1,"-- Select --", 0, "",0,"" );
									?>
	                            </td>
	                            <td>
		                            <?
										$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
		                                echo create_drop_down( "cbo_get_upto", 100, $get_upto,"", 1, "- All -", 0, "",0 );
		                            ?>
		                        </td>
		                        <td>
		                            <input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
		                        </td>
		                        <td>
		                            <?
		                                echo create_drop_down( "cbo_get_upto_qnty", 80, $get_upto,"", 1, "- All -", 3, "",0 );
		                            ?>
		                        </td>
		                        <td>
		                            <input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="1" />
		                        </td>
		                        <td>
		                            <?
		                            	$report_criteria_arr = array('1' => 'Receive challan List', '2' => 'Receive Issue & Stock', '3' => 'Date Wise Receive Issue', '4' => 'Date Wise Transfer', '5' => 'Compact Report', '6' => 'Knitting Delivery Challan', '7' => 'Greige Issue' );
		                                echo create_drop_down( "cbo_report_criteria", 80, $report_criteria_arr,"", 0, "- All -", 2, "",0 );
		                            ?>
		                        </td>
	                            <td align="center">
	                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
	                                &nbsp;To&nbsp;
	                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
	                            </td>
	                            <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>
	                        </tr>
	                        <tr>
	                            <td align="center" colspan="14">
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>