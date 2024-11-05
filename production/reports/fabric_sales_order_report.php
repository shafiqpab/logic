<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Daily Yarn Delivery Status
					
Functionality	:	
				

JS Functions	:

Created by		:	Monir Hossain
Creation date 	: 	06-12-2016
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
echo load_html_head_contents("Fabric Sales Order Report", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var booking_no=$('#txt_booking_no').val();
		var order_no=$('#txt_job_no').val();
		
		if(booking_no=='' && order_no=='')
		{
			alert('Pls, Browse Booking or Sales Order No.');
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_booking_no*txt_job_no*txt_job_hidden_id',"../../");
		//alert(data);
		freeze_window(5);
		http.open("POST","requires/fabric_sales_order_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
				show_msg('3');
		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';

		release_freezing();
		}
	}
		
function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none'; 
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
	d.close();
	//document.getElementById('scroll_body').style.overflowY='scroll';
	//document.getElementById('scroll_body').style.maxHeight='600px';
	//../../css/style_common.css
}
	
	
	function openmypage_jobNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		//var color_from_library = $('#color_from_library').val();
		
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Job Selection Form';	
			var page_link = 'requires/fabric_sales_order_report_controller.php?cbo_company_id='+cbo_company_id+'&action=jobNo_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var job_id=this.contentDoc.getElementById("hidden_job_id").value;
				var job_no=this.contentDoc.getElementById("hidden_job_no").value;	 
				$('#txt_job_no').val(job_no);
				$('#txt_job_hidden_id').val(job_id);
				$('#txt_booking_no').attr('disabled',true);
			}
		}
	}
	
	function openmypage_fabricBooking()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		

		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Booking Selection Form';	
			var page_link = 'requires/fabric_sales_order_report_controller.php?cbo_company_id='+cbo_company_id+'&action=booking_No_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=420px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var booking_data=this.contentDoc.getElementById("hidden_booking_no").value;	 //Access form field with id="emailfield"
				var job_data=this.contentDoc.getElementById("hidden_job_no").value;
				var job_id=this.contentDoc.getElementById("hidden_job_id").value;
				
				$('#txt_booking_no').val(booking_data);
				$('#txt_job_no').val(job_data);
				$('#txt_job_hidden_id').val(job_id);
				$('#txt_job_no').attr('disabled',true);
			}
		}
	
		
		
	}
	
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="fabricSalesOrderReport_1" id="fabricSalesOrderReport_1"> 
         <h3 style="width:600px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:600px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th class="must_entry_caption">Fabric Booking No.</th>
                            <th class="must_entry_caption">Sales Order No.</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricSalesOrderReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td>
                               <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px;" placeholder="Double Click " onDblClick="openmypage_fabricBooking()" maxlength="30" title="Maximum Characters 30" readonly/>
                                </td>
                                <td>
                                  <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:150px;" placeholder="Double Click" onDblClick="openmypage_jobNo()" readonly/>
                                    <input type="hidden" name="txt_job_hidden_id" id="txt_job_hidden_id" readonly>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
     
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>