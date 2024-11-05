<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Status Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	06-04-2014
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Status Report","../", 1, 1, $unicode,1,'');
?>	
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	
	function fn_report_generated()
	{
		var job_no=$('#txt_job_no').val();
		var file_no=$('#txt_file_no').val();
		var ref_no=$('#txt_ref_no').val();
		var order_no=$('#txt_order_no').val();
		//alert(job_no);return;
		if(file_no!="" || ref_no!="" || order_no!="")
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_job_no','Company Name*Job No')==false)
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_file_no*txt_ref_no*txt_order_no',"../")+'&report_title='+report_title;;
		freeze_window(3);
		http.open("POST","requires/order_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse[0]);
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}
	
	function openmypage_job_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		var cbo_year=document.getElementById('cbo_year').value;
		
		page_link='requires/order_status_report_controller.php?action=job_popup'+'&company_id='+company_id+'&buyer_name='+buyer_id+'&cbo_year='+cbo_year;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Job Info", 'width=590px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var job_no=this.contentDoc.getElementById("hide_job_no").value;//alert(item_description_all); 
				job_no=job_no.split("_");
				document.getElementById('cbo_year').value=job_no[0];
				document.getElementById('txt_job_no').value=job_no[1];
			}
		}
	}

	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();

		var page_link='requires/order_status_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&cbo_buyer_name='+cbo_buyer_name+'&txt_job_no='+txt_job_no+'&cbo_year='+cbo_year;
		var title='Order No Search';		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
</script>

</head>
 
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<form id="orderStatusReport" name="orderStatusReport">
        <? echo load_freeze_divs ("../"); ?>
         <h3 align="left" id="accordion_h1" style="width:940px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:720px;">
                <table class="rpt_table" width="720" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Year</th>
                        <th class="must_entry_caption">Job No</th>
                        <th>Order No</th>
                        <th>File No</th>
                        <th>Ref. No</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">                   
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year", 100, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" onDblClick="openmypage_job_info()" placeholder="Write/Browse" style="width:120px;" />
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" placeholder="Write" style="width:120px">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes"   placeholder="Write" style="width:80px;" />
                        </td>
                        <td>
                            <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes"  placeholder="Write" style="width:80px;" />
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
     </form>
 </div> 
 <div id="report_container" align="center"></div>
 <div id="report_container2"></div>   
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
