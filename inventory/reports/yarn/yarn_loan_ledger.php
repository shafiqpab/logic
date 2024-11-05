<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Loan Party Ledger
				
Functionality	:	
JS Functions	:
Created by		:	Tipu 
Creation date 	: 	04-02-2019
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
echo load_html_head_contents("Loan Party Ledger","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*txt_loan_party_id','Company Name*Party Name')==false )
		{
			return;
		} 
		var dataString = "cbo_company_name*txt_loan_party_id*txt_date_from*txt_date_to";
		var data="action=generate_report"+get_submitted_data_string(dataString,"../../../");
		freeze_window(operation);
		http.open("POST","requires/yarn_loan_ledger_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		 
			//document.getElementById('scroll_body').style.overflow="auto";
			//document.getElementById('scroll_body').style.maxHeight="none"; 
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			//document.getElementById('scroll_body').style.overflow="auto"; 
			//document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	function openmypage_party()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();		
		var txt_loan_party_name = $("#txt_loan_party_name").val();
		var txt_loan_party_id = $("#txt_loan_party_id").val();
		var txt_loan_party_no = $("#txt_loan_party_no").val();
		var page_link='requires/yarn_loan_ledger_controller.php?action=party_search_popup&company='+company+'&txt_loan_party_name='+txt_loan_party_name+'&txt_loan_party_id='+txt_loan_party_id+'&txt_loan_party_no='+txt_loan_party_no;
		// alert(page_link);
		var title="Search Party Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=370px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var loan_party_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var loan_party_name=this.contentDoc.getElementById("txt_selected").value; // product Description
			var loan_party_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_loan_party_name").val(loan_party_name);
			$("#txt_loan_party_id").val(loan_party_id); 
			$("#txt_loan_party_no").val(loan_party_no);
		}
	}
	
	function fn_empty_loan_party()
	{
		$('#txt_loan_party_name').val("");
		$('#txt_loan_party_id').val("");
		$('#txt_loan_party_no').val("");
	}

	function order_type()
	{ 		
		$('#txt_loan_party_name').val("");
		$('#txt_loan_party_id').val("");
		$('#txt_loan_party_no').val(""); 
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>   		 
    <form name="loan_ledger_1" id="loan_ledger_1" autocomplete="off" > 
    <h3 style="width:750px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:750px;">
        <legend>Search Panel</legend> 
			<table class="rpt_table" width="750" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="200" class="must_entry_caption">Company</th>    
                        <th width="160" class="must_entry_caption">Party</th>                               
                        <th width="230">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                        <? 
                           echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fn_empty_loan_party();" );
                        ?>
                    </td> 
                    <td align="center">
	                    <input type="text" id="txt_loan_party_name" name="" class="text_boxes" style="width:130px;" placeholder="Browse" onDblClick="openmypage_party();" readonly >
	                    <input type="hidden" id="txt_loan_party_id" name="txt_loan_party_id" >  <input type="hidden" id="txt_loan_party_no" name="txt_loan_party_no" >                    
                    </td>
                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px"/>                    							
                         To
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px"/>                                                        
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:80px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table> 
        </fieldset> 
    </div>
    <br /> 
        <!-- Result Contain Start-->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-->
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
