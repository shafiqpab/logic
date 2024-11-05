
<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Bundle Status Report.
Functionality	:	
JS Functions	:
Created by		:	Abdul Barik Tipu
Creation date 	: 	22-08-2021
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

//-----------------------------------------------------------------------------------------------------
echo load_html_head_contents("Bulk Yarn Allocation Report", "../../", 1, 1,'',1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
 	var permission = '<? echo $permission; ?>';
	
	/*
	|--------------------------------------------------------------------------
	| ile_no popup
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_file_no()
	{
		if(form_validation('cbo_company_name*cbo_file_year','Company Name*File Year')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var file_year = $("#cbo_file_year").val();
		var page_link='requires/bulk_yarn_allocation_report_controller.php?action=file_popup&companyID='+companyID+'&file_year='+file_year;
		var title='PI Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=190px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var file_no = this.contentDoc.getElementById("hide_file_no").value;
			
			$('#txt_file_no').val(file_no);	 
		}
	}

	/*
	|--------------------------------------------------------------------------
	| pi popup
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_pi()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var page_link='requires/bulk_yarn_allocation_report_controller.php?action=pi_search_popup&companyID='+companyID;
		var title='PI Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var pi_no = this.contentDoc.getElementById("hidden_pi_no").value;
			var pi_id = this.contentDoc.getElementById("hidden_pi_id").value;
			var file_no = this.contentDoc.getElementById("txt_file_no").value;
			
			$('#txt_pi_no').val(pi_no);
			$('#hidden_pi_id').val(pi_id);	 
			$('#txt_file_no').val(file_no);	 
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| fnc_report_generated
	|--------------------------------------------------------------------------
	|
	*/
	function fnc_report_generated()
	{
		if(form_validation('cbo_company_name*txt_file_no','Company*File No')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_file_year*txt_file_no*txt_pi_no*hidden_pi_id',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/bulk_yarn_allocation_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| fnc_report_generated_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list",-1,'');
	 		show_msg('3');
			release_freezing();
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| fnc_print_and_excel_file_generated
	|--------------------------------------------------------------------------
	|
	*/
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$('#scroll_body tr:first').show();
		
		
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="bulk_yarn_allocation_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 align="left" id="accordion_h1" style="width:510px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:510px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company</th>
                            <th>File Year</th>
                            <th class="must_entry_caption">File No</th>
                            <th>PI No</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('bulk_yarn_allocation_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                      	<td> 
                            <?php
                            echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td>
                        	<? 
								$selected_year=date("Y");
								echo create_drop_down( "cbo_file_year", 60, $year,"", 1, "--All--", $selected_year, "",0,"","" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage_file_no();" readonly="">
                        </td>
                        <td>
                            <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Browse/write" onDblClick="openmypage_pi();" >
                            <input type="hidden" name="hidden_pi_id" id="hidden_pi_id">
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table> 
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div align="center" id="report_container2"></div>
   
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
