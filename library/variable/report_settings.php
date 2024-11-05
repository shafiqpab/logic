<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Report Variable Settings
Functionality	:	Must fill Company, Variable List
JS Functions	:
Created by		:	Aziz
Creation date 	: 	12-05-2015
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
//-------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Report Settings", "../../", 1, 1,'','1','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var permission='<? echo $permission; ?>';

	function show_priview()
	{
		var page_location=return_ajax_request_value(document.getElementById('cbo_report_name').value, "get_page_url", "requires/report_settings_controller").split("=");
		var tmp_loc=page_location[1].split("/");
		var count=tmp_loc.length-1;
		var page=tmp_loc[count].split(".");
		var new_page="";
		for(var i=0; i<count; i++)
		{
			new_page=new_page+"/"+tmp_loc[i];
		}
		new_page=new_page+"/requires/"+page[0]+"_controller"

		var template_name=document.getElementById('cbo_template_name').value;
		//var action="report_generate";
		//var template_name=document.getElementById('cbo_template_name').value

		var page_location=return_ajax_request_value("viewtemplate"+"**"+template_name, "report_generate", "../../"+new_page);
		//alert(page_location)
		//document.getElementById('variable_settings_container').innerHTML=page_location;
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+page_location+'</body</html>');
		d.close();
		//page_container.php?m=order/woven_order/reports/pre_cost_entry_report.php
	}

	function fnc_report_settings( operation )
	{
		// alert(operation);
		if(operation==4)
		{
			show_priview();
			return;
		} 

		if(form_validation('cbo_company_id*cbo_module_name*cbo_report_name*cbo_format_name','Company Name*Module Name*Report Name*Report Format')==false)
		{
			return;
		}
		//
		 
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_module_name*cbo_report_name*cbo_format_name*txt_report_button_wise_user_id*cbo_status*update_id',"../../");
		// alert(data);
		freeze_window(operation);
		http.open("POST","requires/report_settings_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		 
		http.onreadystatechange = fnc_report_settings_reponse;
		
	}

	function fnc_report_settings_reponse()
	{ 
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			show_msg(reponse[0]);
			if(reponse[0]==1)
			{
				reset_form('reportsettings_1','','','','','');
				set_button_status(0, permission, 'fnc_report_settings',1);
			}
			show_list_view(reponse[2],'report_settings','list_view_report_settings','requires/report_settings_controller','setFilterGrid("list_view",-1)');
			//reset_form('reportsettings_1','','');
			document.getElementById('update_id').value  = reponse[1];
			release_freezing();
			if(reponse[0]==11)
			{
				show_msg(reponse[1]);
			    return;
			}
			if(reponse[0]==0)
			{
				set_button_status(1, permission, 'fnc_report_settings',1);
			}
			else if(reponse[0]==10)
			{
				set_button_status(0, permission, 'fnc_report_settings',1);
			}
		}
	}

	function openmypage()
	{
		var title = 'Report Settings Form';
		var page_link = 'requires/report_settings_controller.php?&action=report_settings_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var template_id=this.contentDoc.getElementById("id_field").value;
			var template_name=this.contentDoc.getElementById("name_field").value;

			$('#txt_template_id').val(template_id);
			$('#txt_template_name').val(template_name);

			freeze_window(5);

		//	show_list_view(template_id,'report_settings','list_view_report_settings','requires/report_settings_controller','setFilterGrid("list_view",-1)');
			release_freezing();
		}
	}

	function company_wise_filter()
	{
		setFilterGrid("list_view",-1);
	}

	function ReportFormate()
	{ 
		//alert('[');
		if(form_validation('cbo_company_id*cbo_module_name*cbo_report_name','Company*Module*Report') == false){
			return; 
		}
		else{  
			// report_button_wise_user_id  
			var page_link='requires/report_settings_controller.php?action=openpopup_report_formate&data='+$("#cbo_report_name").val()+'&txt_report_button_wise_user_id='+$("#txt_report_button_wise_user_id").val()+'&txt_report_button_name='+$("#cbo_format_name_view").val();
			 
			var title_header = 'Report Button';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title_header,'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var txt_report_format_id = this.contentDoc.getElementById("txt_report_format_id").value;
				var txt_report_format_name = this.contentDoc.getElementById("txt_report_format_name").value;
				var report_format_id_arr = txt_report_format_id.split(',');
				var userDataArr = Array();
				var btnArr = Array();
				var btnNameArr = Array();

				var i=0;
				report_format_id_arr.forEach((btn_id) => {
					var selected_button_user_id = this.contentDoc.getElementById("selected_button_user_id_"+btn_id).value;
					userDataArr.push(selected_button_user_id);
					if(selected_button_user_id){
						btnArr.push(btn_id);
					}
					i++;
				});

				$('#cbo_format_name_view').val(txt_report_format_name); 
				$('#cbo_format_name').val(btnArr.join());
				$('#txt_report_button_wise_user_id').val(userDataArr.join('*'));
		    }
	    }
	}
</script>

</head>
<body onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:750px;">
        <legend>Report Settings</legend>
            <form name="reportsettings_1" id="reportsettings_1" autocomplete="off">
      			<table width="500" cellspacing="2" cellpadding="0" border="0">
                	<tr>
                    	<td width="180" align="right" class="must_entry_caption">Company Name</td>
                        <td>
                        <?= create_drop_down( "cbo_company_id", 182, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "show_list_view(this.value,'report_settings','list_view_report_settings','requires/report_settings_controller','company_wise_filter()');" );?>
                        </td>
						<input type="hidden" id="cbo_id" name="cbo_id"/>
						<input type="hidden" id="cboformate_id" name="cboformate_id"/>
                    </tr>
            		<tr>
                		<td align="right" class="must_entry_caption">Module Name</td>
                        <td>
							<?= create_drop_down("cbo_module_name", 182, "select m_mod_id,main_module from main_module where status=1 order by main_module",'m_mod_id,main_module', 1, '--- Select Module ---', 0, "load_drop_down( 'requires/report_settings_controller', this.value, 'load_drop_down_report_module', 'report_name_td' );");?>
                        </td>
                    </tr>
            		<tr>
                        <td align="right" class="must_entry_caption">Report Name </td>
                        <td id="report_name_td">
						    <?= create_drop_down("cbo_report_name", 182, $blank_array,'', 1, '--- Select Name ---', 0, "","","");?>
                        </td>
                    </tr>
            		<tr>
                        <td align="right" class="must_entry_caption">Report Format</td>
                        <td>
						    <input type="text" placeholder="Browse" id="cbo_format_name_view" name="cbo_format_name_view" readonly class="text_boxes" style="width:170px" onDblClick="ReportFormate()"/>
						    <input type="hidden" id="cbo_format_name" name="cbo_format_name" value=""/>
						    <input type="hidden" id="txt_report_button_wise_user_id" name="txt_report_button_wise_user_id" value=""/>
                        </td>
            		</tr>
                    <tr>
                        <td align="right">Status</td>
                        <td>
                        	<?= create_drop_down( "cbo_status", 182, $row_status,'', 0, '',1,0); ?>
                        </td>
            		</tr>
                    <tr>
                        <td colspan="2" height="50" valign="middle" align="center" class="button_container">
                            <input type="hidden" name="update_id" id="update_id" value=""/>
                            <?= load_submit_buttons($permission, "fnc_report_settings", 0,0 ,"reset_form('reportsettings_1','','','')",1); ?>
                         </td>
                    </tr>
        		</table>
                <div style="width:750px; float:left; min-height:40px; margin:auto" align="center" id="list_view_report_settings"></div>
            	<!-- <div style="width:750px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container"></div> -->
		</form>
	</fieldset>
    </div>
</body>
<script>
    set_multiselect('cbo_format_name','0','0','','0');
	set_multiselect('cbo_user_id','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
