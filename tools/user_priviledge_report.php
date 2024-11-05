<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create CI Statement Report.
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	23-12-2013
Updated by 		: 	REZA
Update date		: 	11-11-2015
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
echo load_html_head_contents("User Priviledge Report", "../", 1, 1,'','','');
?>
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';





	function generate_report()
	{
		if(form_validation('txt_user','User ID')==false)
		{
			return;
		}
		var data="action=report_generate"+get_submitted_data_string("txt_user_id*cbo_page_name*cbo_main_module","../");
		freeze_window(3);
		http.open("POST","requires/user_priviledge_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText);
			//alert(http.responseText);return;
			//$('#report_container2').html(response);
			//document.getElementById('report_container').innerHTML=report_convert_button('../');

			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		$("#table_body tr:first").show();
	}

	function openmypage_item()
	{
		//var company = $("#cbo_company_name").val();
		var page_link='requires/user_priviledge_report_controller.php?action=user_name_search';
		var title="Search User Name";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=355px,height=430px,overflow-y=hidden,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var user_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var user_description=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_user").val(user_description);
			$("#txt_user_id").val(user_id);
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs("../",''); ?>
    <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
	    <div style="width:670px;">
	      <h3 align="left" id="accordion_h1" style="width:670px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
	      <div id="content_search_panel">
	        <fieldset style="width:660px;">
                <table class="rpt_table" cellspacing="0" cellpadding="0" width="650" rules="all">
                   <thead>
                        <th>User ID</th>
                        <th>Module Name</th>
                        <th>Page Name</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
                   </thead>
                    <tr>
                        <td>
                            <input type="text" style="width:150px;" name="txt_user" id="txt_user" onDblClick="openmypage_item()"  class="text_boxes" placeholder="Dubble Click For User"  readonly />
                            <input type="hidden" name="txt_user_id" id="txt_user_id"/>
                        </td>
                        <td>
                            <? echo create_drop_down( "cbo_main_module", 250, "select main_module,m_mod_id from main_module where status=1 order by main_module",'m_mod_id,main_module', 1, '--- Select Module ---', 0, "load_drop_down( 'requires/user_priviledge_report_controller', this.value, 'load_page_dropdown', 'load_page_name' )" ); ?>
                        </td>
                        <td id="load_page_name">
                            <? echo create_drop_down( "cbo_page_name", 150, array(),"", 1, "-- Select Page --", $selected ); ?>
                        </td>
                        <td align="center">
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                </table>
	        </fieldset>
	      </div>
	    </div>
	    <div id="report_container" align="center"></div>
	    <div id="report_container2" align="left"></div>
	  </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
/*
|	QC Comments :

|	Form Page:
|	Need to indent code using hard tabs

|	Controller Page:
|	In user id popup load htlm header content by using load_html_head_contents() function but don't write html end tag
|	Modal window is not use in user id popup but unnecessarily loaded all modal sources

*/
?>