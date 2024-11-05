<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Proceed Realisation Certificate Against Direct Export Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	06/04/2019
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
echo load_html_head_contents("Proceed Realisation Certificate Against Direct Export Report","../../", 1, 1, $unicode,'',''); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*txt_file_no','Company Name*File No')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*txt_file_no*txt_hide_year',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/proceed_realization_certificate_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert(http.responseText);	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
			show_msg('3');
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		}
	} 

	function new_window()
	{
		 
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none";
			//$('#table_body tr:first').hide(); 
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			//document.getElementById('scroll_body').style.overflow="auto"; 
			//document.getElementById('scroll_body').style.maxHeight="250px";
			//$('#table_body tr:first').show();
	}
	
	
	function openmypage_file_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		page_link='requires/proceed_realization_certificate_controller.php?action=file_popup&company_id='+company_id;
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			alert("Please Select Company Name");return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=600px,height=390px,center=1,resize=0,scrolling=0','../')
	
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var file_no=this.contentDoc.getElementById("hide_file_no").value.split(",");//alert(item_description_all);
				//alert(file_no[4]);
				document.getElementById('txt_file_no').value=file_no[0];
				document.getElementById('txt_hide_year').value=file_no[1];
			}
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:750px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:750px;" align="center" id="content_search_panel">
        <fieldset style="width:750px;">
        <legend>Search Panel</legend> 
            <table class="rpt_table" width="750" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="250" class="must_entry_caption">Company</th>                                
                        <th width="250" class="must_entry_caption">File No</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_form( 'stock_ledger_1', 'report_container2', '', '', '', '' );" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                            <?
                        	echo create_drop_down( "cbo_company_name", 190, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>                          
                    </td>
                    <td>
                         <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" onDblClick="openmypage_file_info();" placeholder="Browse" style="width:180px;" readonly /> 
                         <input type="hidden" name="txt_hide_year" id="txt_hide_year" value="<? //echo ?>"/>
                           
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:90px" class="formbutton" />
                    </td>
                </tr>
            </table> 
        </fieldset> 
           
    </div>
    <br /> 
    <!-- Result Contain Start-------------------------------------------------------------------->
    <fieldset style="width:1000px;">
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
    </fieldset>
    <!-- Result Contain END-------------------------------------------------------------------->
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
