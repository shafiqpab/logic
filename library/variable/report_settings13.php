<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Report Variable Settings
					 
Functionality	:	Must fill Company, Variable List

JS Functions	:

Created by		:	Sumon, CTO 
Creation date 	: 	27-01-2013
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
//echo load_html_head_contents("Report Variable Settings", "../../", 1, '1','',1);
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
		//alert(new_page); 
	}
	
	function fnc_report_settings( operation )
	{  
		
		if(operation==4)
		{
			show_priview();
			return;
		}
		
		if(form_validation('txt_template_name*cbo_module_name*cbo_report_name*cbo_template_name','Template Name*Module Name*Report Name*Report Format')==false)
		{
			return;
		}
		else if(($('#cbo_buyer_name').val()!="" && $('#cbo_bank_name').val()!=""))
		{
			alert("Please Select One Specific Between Buyer and Bank");
			return;
		}
		else
		{ 
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_template_name*cbo_module_name*cbo_report_name*cbo_template_name*cbo_buyer_name*cbo_bank_name*cbo_status*update_id*txt_template_id',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/report_settings_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_report_settings_reponse;
		}
	}

	function fnc_report_settings_reponse()
	{
		if(http.readyState == 4) 
		{   
		   
			var reponse=trim(http.responseText).split('**');
			
			show_msg(reponse[0]);
			
			show_list_view(reponse[2],'report_settings','list_view_report_settings','requires/report_settings_controller','setFilterGrid("list_view",-1)');
			reset_form('reportsettings_1','','');
		
			set_button_status(0, permission, 'fnc_report_settings',1); 
			release_freezing();
			
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
			
			show_list_view(template_id,'report_settings','list_view_report_settings','requires/report_settings_controller','setFilterGrid("list_view",-1)');
			release_freezing();
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
                        <? echo create_drop_down( "cbo_company_id", 182, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" ); ?>
                        </td>
                    </tr>
            		<tr>
                		<td align="right" class="must_entry_caption">Module Name</td>
                        <td>
							<? 
                                echo create_drop_down( "cbo_module_name", 182, "select m_mod_id,main_module from main_module where status=1 order by main_module",'m_mod_id,main_module', 1, '--- Select Module ---', 0, "load_drop_down( 'requires/report_settings_controller', this.value, 'load_drop_down_report_name', 'report_td' );"  );
                            ?>
                        </td>
                    </tr>
            		<tr> 
                        <td align="right" class="must_entry_caption">Report Name</td>
                        <td id="report_td">
							<? 
							$report_name=array(1=>"Main Fabric Booking",2=>"Short Fabric Booking",3=>"Sample Fabric Booking -With order",4=>"Sample Fabric Booking -Without order",5=>"Multiple Order Wise Trims Booking",6=>"Service Booking For Knitting",7=>"Yarn Dyeing Work Order",8=>"Yarn Dyeing Work Order Without Order",9=>"Embellishment Work Order",10=>"Service Booking For AOP");
                                echo create_drop_down( "cbo_report_name", 182, $report_name,'', 1, '--- Select Report ---', 0, ""  );
                            ?>
                        </td>
                    </tr>
            		<tr>    
                        <td align="right" class="must_entry_caption">Report Format</td>
                        <td>
							<? 
                                $tmpl=array();
                                for($i=1; $i<11; $i++)
                                {
                                    $tmpl[$i]="Template- ".$i;
                                }
                                echo create_drop_down( "cbo_template_name", 182, $tmpl,'', 1, '--- Select Template ---', 0, ""  );
                            ?>
                        </td>
            		</tr>
                    <tr>    
                        <td align="right">Buyer Specific</td>
                        <td>
                        	<?
							   echo create_drop_down( "cbo_buyer_name", 182, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 0, "", 0, "" );
 							?>
                        </td>
            		</tr>
                    <tr>    
                        <td align="right">Bank Specific</td>
                        <td>
							<? 
                                echo create_drop_down( "cbo_bank_name", 182, "select id,bank_name from lib_bank where is_deleted=0 and status_active=1 order by bank_name","id,bank_name", 0, "", 0, "" );
                            ?>
                        </td>
            		</tr>
                    <tr>    
                        <td align="right">Status</td>
                        <td>
                        	<?php echo create_drop_down( "cbo_status", 182, $row_status,'', 0, '',1,0); ?> 
                        </td>
            		</tr>
                    <tr>
                        <td colspan="2" height="50" valign="middle" align="center" class="button_container">						
                            <input type="text" name="update_id" id="update_id" value=""/>
                            <input type="hidden" name="txt_template_id" id="txt_template_id" value=""/>
                          <? echo load_submit_buttons($permission, "fnc_report_settings", 0,1 ,"reset_form('reportsettings_1','','','')",1); ?>
                         </td>                          			
                    </tr>  
        		</table>
                <div style="width:750px; float:left; min-height:40px; margin:auto" align="center" id="list_view_report_settings"></div>
            	<div style="width:750px; float:left; min-height:40px; margin:auto" align="center" id="variable_settings_container"></div>
		</form>	
	</fieldset>
    </div>
</body>
<script>
	set_multiselect('cbo_buyer_name*cbo_bank_name','0*0','0','','0*0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>