<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Floor Wise Finish Fabric Issue Report
Functionality	:
JS Functions	:
Created by		: 	Md. Saidul Islam Reza. Cell: +880 1511 100004
Creation date 	: 	15 Jul 2019
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
echo load_html_head_contents("Floor Wise Finish Fabric Issue Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";


	var tableFilters =
		{
			col_operation: {
				id: ["value_td_total_issue_qty"],
				col: [15],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}	
	
	
	
	function generate_report()
	{
		
		if($("#txt_batch_no").val()=='' &&  $("#txt_job_no").val()=='' && $("#txt_order_no_show").val()==''  && $("#txt_booking_no_show").val()=='' && $("#txt_booking_no_show").val()=='' &&  $("#txt_date_from").val()==''  && $("#txt_date_to").val()=='' )
		
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Working Company*From Date*To Date')==false){
				return;
			}
		}
			
		
		
		/*if( 
			(form_validation('cbo_company_id*txt_order_no_show','Company Name*Order No')==false) &&
			(form_validation('cbo_company_id*txt_batch_no','Company Name*Batch No')==false) &&
			(form_validation('cbo_company_id*txt_booking_no_show','Company Name*Booking No')==false) &&
			(form_validation('cbo_company_id*txt_job_no','Company Name*Job No')==false) && 
			(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false) &&
			
			(form_validation('cbo_working_company_id*txt_order_no_show','Working Company*Order No')==false) &&
			(form_validation('cbo_working_company_id*txt_batch_no','Working Company*Batch No')==false) &&
			(form_validation('cbo_working_company_id*txt_booking_no_show','Working Company*Booking No')==false) &&
			(form_validation('cbo_working_company_id*txt_job_no','Working Company*Job No')==false) && 
			(form_validation('cbo_working_company_id*txt_date_from*txt_date_to','Working Company*From Date*To Date')==false)
		)
		{
			return;
		}*/
		
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+ get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_store_name*cbo_source*cbo_working_company_id*cbo_cutting_floor*txt_batch_id*txt_batch_no*txt_booking_no_show*txt_booking_no*txt_job_no*txt_job_id*txt_order_no_show*txt_order_no*cbo_order_type*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/floor_wise_finish_fabric_issue_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="button" onclick="new_window_summary()" value="Print Preview Summary" name="Print" class="formbutton" style="width:180px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		//$('#table_body tr:first').hide();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}
	
	
	function new_window_summary()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('summary').innerHTML+'</body</html>');
		d.close();
	}
	
	
	
	

	function openPopupJob()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		
		var page_link='requires/floor_wise_finish_fabric_issue_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Job No Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}
	
	function openPopupOrder()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		
		var page_link='requires/floor_wise_finish_fabric_issue_report_controller.php?action=order_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Order No Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$('#txt_order_no_show').val(order_no);
			$('#txt_order_no').val(order_id);
		}
	}
	
	function openPopupBooking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		
		var page_link='requires/floor_wise_finish_fabric_issue_report_controller.php?action=booking_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Order No Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
			$('#txt_booking_no_show').val(booking_no);
			$('#txt_booking_no').val(booking_id);
		}
	}
	
	function openPopupBatch()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		
		var page_link='requires/floor_wise_finish_fabric_issue_report_controller.php?action=batch_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Batch Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var batch_no=this.contentDoc.getElementById("hide_batch_no").value;
			var batch_id=this.contentDoc.getElementById("hide_batch_id").value;
			$('#txt_batch_no').val(batch_no);
			$('#txt_batch_id').val(batch_id);
		}
	}
	

	function load_working_company()
	{
		var company=$("#cbo_working_company_id").val();
		var cbo_source=$("#cbo_source").val();
		load_drop_down( 'requires/floor_wise_finish_fabric_issue_report_controller',cbo_source+'_'+company, 'load_drop_down_sewing_com','sewing_com_td');
		set_multiselect('cbo_working_company_id','0','0','0','0'); 
		$("#multiselect_dropdown_table_headercbo_working_company_id a").click(function(){
			load_cutting_floor();
		});
	}
	
	
	function load_cutting_floor()
	{
		var company=$("#cbo_working_company_id").val();
		var cbo_source=$("#cbo_source").val();
		load_drop_down( 'requires/floor_wise_finish_fabric_issue_report_controller',cbo_source+'_'+company, 'load_drop_down_cutting_floor','cutting_floor_td');
		set_multiselect('cbo_working_company_id','0','0','0','0');     		 
	}
	
	

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1655px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,
    'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" align="center">
            <fieldset>
                <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th class="must_entry_caption">Company</th>
                            <th>Buyer</th>
                            <th>Store Name</th>
                            <th>Source</th>
                            <th class="must_entry_caption">Working Company</th>
                            <th>Cutting Floor</th>
                            <th>Batch No</th>
                            <th>Booking No</th>
                            <th>Job No.</th>
                            <th>Order No</th>
                            <th >Order Type</th>
                            <th class="must_entry_caption" colspan="2">Issue Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center" class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/floor_wise_finish_fabric_issue_report_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/floor_wise_finish_fabric_issue_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                       <td id="store_td">
                            <? 
                                echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                       </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_source", 115, $knitting_source, "", 0, "--  --", 0, "load_working_company()", "", "1,3","");
                            ?>
                        </td>
                        <td id="sewing_com_td">
                            <?
                               echo create_drop_down( "cbo_working_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td id="cutting_floor_td">
                            <?
								echo create_drop_down( "cbo_cutting_floor", 140, "select id,floor_name from lib_prod_floor where company_id=1 and production_process=1 and status_active=1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", 0);
                            ?>
                        </td>
                         <td>
                            <input type="text" id="txt_batch_no" name="txt_batch_no" class="text_boxes" style="width:100px" onDblClick="openPopupBatch()" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_batch_id" name="txt_batch_id"/>
                        </td>

                         <td>
                            <input type="text" id="txt_booking_no_show" name="txt_booking_no_show" class="text_boxes" style="width:100px" onDblClick="openPopupBooking()" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_booking_no" name="txt_bookint_no"/>
                        </td>

                         <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openPopupJob()" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" />
                        </td>

                         <td>
                            <input type="text" id="txt_order_no_show" name="txt_order_no_show" class="text_boxes" style="width:100px" onDblClick="openPopupOrder()" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:100px" />
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_order_type", 115, array(1=>'With Order',2=>'Without Order'), "", 0, "--  --", 0, "", "", "","");
                            ?>
                        </td>

                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px;" readonly/>
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                    		<td colspan="14" align="center">
                    			<?  echo load_month_buttons(1); ?>
                    		</td>
                    	</tr>
                    </tfoot>

                </table>
            </fieldset>
        </div>

    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script> 
	set_multiselect('cbo_working_company_id','0','0','','0');
	$("#multiselect_dropdown_table_headercbo_working_company_id a").click(function(){
		load_cutting_floor();
	});
</script>
</html>
