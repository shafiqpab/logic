<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sample Req Delivery Report.
Functionality	:	
JS Functions	:
Created by		:	Md Aziz 
Creation date 	: 	26-09-2020
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

//---------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Delivert Status Report","../../", 1, 1, $unicode,1,''); 

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	
	

	function fn_report_generated(excel_type)
	{
		
		var txt_req_no=document.getElementById('txt_req_no').value;
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var txt_style_ref=document.getElementById('txt_style_ref').value;
			if(txt_req_no!="" || txt_booking_no!="" || txt_style_ref!="")
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
		
			var report_title=$("div.form_caption" ).html();	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_sample_stage*cbo_buyer_name*txt_style_ref*txt_req_no*txt_req_id*txt_style_ref*txt_booking_no*txt_style_ref*cbo_sample_name*txt_date_from*txt_date_to',"../")+'&report_title='+report_title;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/sample_requisition_wise_delivery_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####"); 
			$('#report_container2').html(reponse[0]);
			 document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		 
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide(); 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}
	function openmypage_requisition(type)
	{
		var cbo_company_name=$('#cbo_company_name').val();
	
		var title = 'Requisition ID Search';
		var page_link = 'requires/sample_requisition_wise_delivery_status_report_controller.php?&action=requisition_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_name='+cbo_company_name+'&type='+type, title, 'width=1000px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_req_id=this.contentDoc.getElementById("hidden_req_id").value;//mst id
			var hidden_req_no=this.contentDoc.getElementById("hidden_req_no").value;//mst id
			//alert(hidden_req_id);
			if (hidden_req_id!="")
			{
				freeze_window(5);
				release_freezing();
				if(type==1)
				{
				$('#txt_req_id').val(hidden_req_id);
				$('#txt_req_no').val(hidden_req_no);
				}
				else if(type==2)
				{
				 $('#txt_req_id').val(hidden_req_id);
				 $('#txt_booking_no').val(hidden_req_no);
				}
				else if(type==3)
				{
				 $('#txt_req_id').val(hidden_req_id);
				 $('#txt_style_ref').val(hidden_req_no);
				}
				
			}
		}
 	}
	function open_delivery_popup(req_data,title,action,type)
	{
		//alert(req_data);
		var width=700+'px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_requisition_wise_delivery_status_report_controller.php?action='+action+'&req_data='+req_data+'&type='+type, title, 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
	}
</script>
</head>
<body onLoad="set_hotkey();">

<form id="SampleProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <fieldset style="width:770px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="770px" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                    <tr>
						<th width="100">Sample Stage </th>
                        <th width="130" class="must_entry_caption">Company Name</th>
						<th width="110">Buyer Name</th>
                         <th width="110">Requisition No</th>
                        <th width="60">Booking No</th>
                        <th width="110">Style</th>
                        <th width="110">Sample Name</th>
                        <th width="130" colspan="2" id="search_text_td" class="must_entry_caption">Delivery Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                    <tr class="general">
                        <td>
						<? echo create_drop_down( "cbo_sample_stage", 100, $sample_stage,"", 1, "-- Select --", $selected, "" ); ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_requisition_wise_delivery_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" ); ?></td>
                        <td><input type="text"  name="txt_req_no" id="txt_req_no" class="text_boxes_numeric" style="width:100px;" onDblClick="openmypage_requisition(1);" placeholder="Wr./Br."><input type="hidden"  name="txt_req_id" id="txt_req_id" class="text_boxes_numeric" style="width:50px;"></td>
                        <td><input type="text"  name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px;" placeholder="Wr/Br."  onDblClick="openmypage_requisition(2);"></td>
                         <td><input type="text"  name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px;" placeholder="Wr/Br."  onDblClick="openmypage_requisition(3);"></td>
                        <td title="sample_td"><? 
						echo create_drop_down( "cbo_sample_name",110,"select a.id,a.sample_name from lib_sample a ,sample_development_dtls b where a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id=b.sample_name and b.entry_form_id=203 group by a.id,a.sample_name","id,sample_name", 1, "-- Select Stage --", $selected, "",0,"" ); 
						?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="14"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_sample_stage','0','0','0','0');
</script> 

</html>