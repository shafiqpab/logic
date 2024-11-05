<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status  Without Order Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	01-09-2020
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
echo load_html_head_contents("Fabric Production Status Report Sample without Order", "../../", 1, 1,'',1,1);
?>	

<script>
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	function func_style_description()
	{ 
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		
		var company=$('#cbo_company_name').val()*1;
		var page_link="requires/fabric_prod_status_report_sample_without_order_controller.php?action=action_style_description&company="+company; 
		var title="Style Description";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030px,height=450px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_booking=this.contentDoc.getElementById("selected_booking").value;
			$('#txt_style_description').val(selected_booking);
			release_freezing();
		}
	}	
	
	function func_show()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		else
		{
			if($('#txt_booking_no').val() == '' && $('#txt_style_description').val() == '' && $('#txt_program_no').val() == '' && $('#txt_requisition_no').val() == '')
			{
				if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
				{
					return;
				}
			}
			
			var data="action=action_show"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_sample_type*cbo_booking_year*txt_booking_no*txt_style_description*txt_program_no*txt_requisition_no*txt_date_from*txt_date_to',"../../");
			freeze_window(3);
			http.open("POST","requires/fabric_prod_status_report_sample_without_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = func_show_reponse;
		}
	}
	
	function func_show_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			//append_report_checkbox('table_header_1',1);
			// $("input:checkbox").hide();
			show_msg('3');
			release_freezing();
		}
		
	}
	
	//func_issue_qty_popup
	function func_issue_qty_popup(id)
	{
		var popup_width = '690px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_prod_status_report_sample_without_order_controller.php?id='+id+ '&action=actn_issue_qty_popup', 'Detail Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}
	
	//func_production_qty_popup
	function func_production_qty_popup(id)
	{
		var popup_width = '1270px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_prod_status_report_sample_without_order_controller.php?id=' + id + '&action=actn_production_qty_popup', 'Detail Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}
</script>
<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
<body onLoad="set_hotkey();">
	<form id="FabricProductionStatusReportSamplewithoutOrder_1">
   	 <div style="width:100%;" align="center">    
         <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1200px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1200px;">
             <table class="rpt_table" width="1180" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption" width="140">Company Name</th>
                    <th width="140">Buyer Name</th>
                    <th width="100">Sample Type</th>
                    <th width="90">Booking Year</th>
                    <th width="100">Booking No.</th>
                    <th width="100">Syle Description</th>
                    <th width="100">Program No.</th>
                    <th width="100">Requisition No</th>
                    <th colspan="2" width="200">Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('FabricProductionStatusReportSamplewithoutOrder_1','report_container*report_container2','','','')" class="formbutton" style="width:90px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                            echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/fabric_prod_status_report_sample_without_order_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_sample_type", 100, "select id,sample_name from lib_sample order by sample_name","id,sample_name", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td >
                            <? 
								echo create_drop_down( "cbo_booking_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" />
                        </td>
                        <td>
                            <input name="txt_style_description" id="txt_style_description" class="text_boxes" style="width:100px" placeholder="browse" onDblClick="func_style_description()" readonly />
                        </td>
                        <td>
                            <input name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:100px" />
                        </td>
                        <td>
                            <input name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:100px" />
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly />
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" readonly />
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="func_show()" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    	</div>
    </div>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
