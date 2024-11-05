<?
/*-------------------------------------------- Comments

Purpose			: 	This Report will Create Dying to Delivery lead time monitory report
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	24-03-2020
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
echo load_html_head_contents("Fabric Finishing Report", "../../", 1, 1,'','','');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var tableFilters =
	{
		col_30: "none",
		col_operation: {
			id: ["btg"],
			col: [12],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
	}

	function fn_dyeing_report_generated(type)
	{
		var booking_number_show=$("#booking_number_show").val();
		var cbo_buyer_name=$("#cbo_buyer_name").val();
		var batch_number_show=$("#batch_number_show").val();
	
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();



		if(booking_number_show || batch_number_show )
		{

		}
		else
		{
			if(form_validation('txt_date_from*txt_date_to','From date Fill*To date Fill')==false)
			{
				return;
			}

		}

		var report_title=$( "div.form_caption" ).html();
		freeze_window(5);
		
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*batch_number*batch_number_show*booking_number*booking_number_show*cbo_year*txt_date_from*txt_date_to*batch_extension*cbo_base_on_date',"../../")+'&report_title='+report_title;
		

		http.open("POST","requires/dying_to_delivery_leadtime_monitory_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_batch_report;
	}

	function fnc_show_batch_report()
	{
		if(http.readyState ==4)
		{
			var response=trim(http.responseText).split("****");
			$("#report_container2").html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			release_freezing();
		}
	}
	function new_window(type)
	{

		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
			document.getElementById('scroll_body1').style.overflow="auto";
			document.getElementById('scroll_body1').style.maxHeight="none";
		}

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
			document.getElementById('scroll_body1').style.overflowY="scroll";
			document.getElementById('scroll_body1').style.maxHeight="none";

		}
		$("tr th:first-child").show();
		$("tr td:first-child").show();
	}
	<!--BookingNumber -->
	function bookingnumber()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var batch_number=document.getElementById('batch_number_show').value;
		var page_link="requires/dying_to_delivery_leadtime_monitory_report_controller.php?action=bookingnumbershow&company_name="+company_name+"&cbo_buyer_name="+cbo_buyer_name;
		var title="Booking Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('booking_number').value=batch[0];
			document.getElementById('booking_number_show').value=batch[1];
			release_freezing();
		}
	}
	<!--BatchNumber -->
	function batchnumber()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var batch_number=document.getElementById('batch_number_show').value;
		var page_link="requires/dying_to_delivery_leadtime_monitory_report_controller.php?action=batchnumbershow&company_name="+company_name;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('batch_number').value=batch[0];
			document.getElementById('batch_number_show').value=batch[1];
			document.getElementById('batch_extension').value=batch[2];
			release_freezing();
		}
	}

	function check_date(type)
	{
	 //var company_name=document.getElementById('cbo_company_name').value;
	 if(type==32)
	 {
	 	document.getElementById('th_date').innerHTML='Batch Date';
	 	$('#th_date').css('color','blue');
	 }
	 else if(type==30)
	 {
	 	document.getElementById('th_date').innerHTML='Unload End Date';
	 	$('#th_date').css('color','blue');
	 }
	 else  document.getElementById('th_date').innerHTML='Process End Date';
	 $('#th_date').css('color','blue');
	}
	function fn_recipe_calcq(job_no,action)
	{
		
		
		var job_data=return_global_ajax_value( trim(job_no), 'pre_cost_data', '', 'requires/fabric_booking_approval_controller');
		var job_data_arr=job_data.split('***');
	//a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.costing_date,b.costing_per
	
			var zero_val='';
			var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
			if (r==true) zero_val="1"; else zero_val="0";
			
			var data="action="+action+"&zero_value="+zero_val+"&txt_job_no='"+trim(job_data_arr[0])+"'&cbo_company_name="+job_data_arr[1]+"&cbo_buyer_name="+job_data_arr[2]+"&txt_style_ref='"+trim(job_data_arr[3])+"'&txt_costing_date='"+job_data_arr[4]+"'&txt_po_breack_down_id=''&cbo_costing_per="+job_data_arr[5]+"&path=../";
			
			http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_worder_report4_reponse;
		
	}
	
	function generate_worder_report4_reponse()
	{
		if(http.readyState == 4) 
		{
			//$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			
			
			
		}
	}
	
	function fn_recipe_calc(update_id,batch_id,txt_yarn_lot,brand_id,type_id)
    {
      //  if(operation=="btn_recipe_calc")
		//{
            //alert(operation);return;
			if(type_id==1)
			{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+update_id+'*'+$('#txt_labdip_no').val()+'*'+txt_yarn_lot+'*'+brand_id+'*'+$('#txt_count').val()+'*'+$('#txt_pick_up').val()+'*'+$('#surpls_solution').val()+'*'+batch_id+'*'+$('#cbo_sub_process').val()+'*'+report_title, "recipe_entry_print_2", "../requires/recipe_entry_controller")
			}
			else
			{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+update_id+'*'+report_title, "chemical_dyes_issue_requisition_without_rate_print_urmi", "../../inventory/chemical_dyes/requires/chemical_dyes_issue_requisition_controller" );
			}
			//return;
			show_msg("3");
		//}
    }


</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
			<h3 style="width:1000px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>  <div id="content_search_panel" >
				<fieldset style="width:1000px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							
							<th class="must_entry_caption">Company</th>
							<th>Buyer</th>
							<th>Year</th>
							<th>Sales Order No</th>
							<th>Batch No</th>
                            <th>Based On Date</th>

							<th class="must_entry_caption" id="th_date">Date Range</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
						</thead>
						<tbody>
							<tr>
								
                            <td>
                            	<?
                            	echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/dying_to_delivery_leadtime_monitory_report_controller', this.value, 'load_drop_down_location','location_td2');load_drop_down('requires/dying_to_delivery_leadtime_monitory_report_controller', this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td2');" );
                            	?>

                            </td>

                           <td id="cbo_buyer_name_td2">
                            	<?
                            	echo create_drop_down( "cbo_buyer_name", 110,$blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            	?>
                            </td>
                            <td>
                            	<?
                            	echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            	?>
                            </td>

                            <td>
                            	<input type="text"  name="booking_number_show" id="booking_number_show" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="bookingnumber();">
                            	<input type="hidden" name="booking_number" id="booking_number">
                            </td>
                            <td>
                            	<input type="text"  name="batch_number_show" id="batch_number_show" readonly class="text_boxes" style="width:80px;" tabindex="1" placeholder="Browse" onDblClick="batchnumber();"  >
                            	<input type="hidden" name="batch_number" id="batch_number">
                            	<input type="hidden" name="batch_extension" id="batch_extension">
                            </td>
                             <td>
                            	<?
								$base_on_date_arr=array(1=>"Prod. Date",2=>"Batch Date");
                            	echo create_drop_down( "cbo_base_on_date", 100, $base_on_date_arr,"", 0,"-- All --", 1, "",0,"" );
                            	?>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                            	&nbsp;To&nbsp;
                            	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                            </td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:55px" value="Show" onClick="fn_dyeing_report_generated(1)" />&nbsp;<input type="button" id="show_button2" class="formbutton" style="width:40px" value="Report2" onClick="fn_dyeing_report_generated(2)" />

                            </td>
                        </tr>
                    </tbody>
                </table>
                <table>
                	<tr>
                		<td colspan="8">
                			<? echo load_month_buttons(1); ?>
                		</td>
                	</tr>
                </table>
                <br />
            </fieldset>
            <div id="report_container" align="center"></div>
            <div id="report_container2" align="center"></div>
        </div>
    </form>

</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>