<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Ex-Factory Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	20-02-2014
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
echo load_html_head_contents("Cost Breakdown Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_order_no','Company Name*Order No')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_order_no',"../../../")+'&report_title='+report_title;;
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
			//alert(reponse);
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
	 		show_msg('3');
		}
	}
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
	function openmypage_job_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		page_link='requires/order_status_report_controller.php?action=file_job_popup'+'&company_id='+company_id+'&buyer_id='+buyer_id;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=580px,height=400px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var job_no=this.contentDoc.getElementById("hide_job_no").value;//alert(item_description_all); 
				document.getElementById('txt_job_no').value=job_no;
			}
		}
	}
	
	
	
	function openmypage_order_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		var job_no=document.getElementById('txt_job_no').value;
		page_link='requires/order_status_report_controller.php?action=file_popup'+'&company_id='+company_id+'&buyer_id='+buyer_id+'&job_no='+job_no;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=1005px,height=400px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var order_no=this.contentDoc.getElementById("hide_order_no").value;//alert(item_description_all); 
				document.getElementById('txt_order_no').value=order_no;
			}
		}
	}
	
</script>

</head>
 
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:720px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:720px;">
                <table class="rpt_table" width="720" cellpadding="1" cellspacing="0" align="center">
                    <thead>
                        <tr>
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="150">Buyer Name</th>
                            <th width="150">Job No</th>
                            <th width="150">Order No</th>
                            <th ><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                    </thead>
					<tbody>
                        <tr>                   
                            <td  align="center"> 
                            <?
                        	echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                            </td>
                            <td  align="center"id="buyer_td">
							<? 
                        		echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        	?>
                            </td>
                            <td  align="center">
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" onDblClick="openmypage_job_info()" placeholder="Browse or Write"  style="width:140px;" />
                            </td>
                            <td  align="center">
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" onDblClick="openmypage_order_info()" placeholder="Browse or Write"  style="width:140px;" />
                            </td>
                            <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </fieldset>
        </div>
     </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    
 </div>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
