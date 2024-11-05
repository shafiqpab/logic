<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create daily activities verification
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam REZA
Creation date 	: 	27-08-2020
Updated by 		:   	
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:logout.php");
require_once('../../includes/common.php');

extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Activities Verification", "../../", 1, 1,'','','');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		freeze_window(3);
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_team_leader_id*txt_team_member_id*txt_date_from*txt_date_to',"../../");
		http.open("POST","requires/daily_task_report_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			var tot_rows=reponse[2];
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			release_freezing();
			show_msg('3');
		}
	}
	

	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
	}

    function open_team_leader()
	{	
        var team_leader_id = $('#txt_team_leader_id').val();
		var company_id = $('#cbo_company_id').val();
		var title = 'Team Leader';	
		var page_link='requires/daily_task_report_v2_controller.php?company_id='+company_id+'&team_leader_id='+team_leader_id+'&action=team_leader_list';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_user_id").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_user_name").value; //Access form field with id="emailfield"
			
			$('#txt_team_leader').val(theename);
			$('#txt_team_leader_id').val(theemail);
		}
	}
	
	
	
function open_team_member()
	{	
        var team_member_id = $('#txt_team_member_id').val();
        var team_leader_id = $('#txt_team_leader_id').val();
		var title = 'Team Member';	
		var page_link='requires/daily_task_report_v2_controller.php?team_leader_id='+team_leader_id+'&team_member_id='+team_member_id+'&action=team_member_list';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_user_id").value;	 //Access form field with id="emailfield"
			var theename=this.contentDoc.getElementById("hidden_user_name").value; //Access form field with id="emailfield"
			
			$('#txt_team_member').val(theename);
			$('#txt_team_member_id').val(theemail);
		}
	}	   

	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Team Leader</th>
                                <th>Team Member</th>
                                <th title="Task Date" colspan="3">Date Range</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
			   						<?= create_drop_down( "cbo_company_id", 150, "SELECT id, company_name from lib_company where  status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );?>
                                </td>
                                <td> 
									<? //echo create_drop_down( "cbo_team_leader_id", 150, "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/daily_task_report_v2_controller', this.value, 'load_team_member', 'td_team_member' ); " ); ?>
                                    <input type="text" name="txt_team_leader" id="txt_team_leader" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="open_team_leader();" readonly/>
                            		<input type="hidden" name="txt_team_leader_id" id="txt_team_leader_id" />

                                </td>
                                
                                <td id="td_team_member"> 
                                    <? //echo create_drop_down( "cbo_team_member_id", 150, $type,"",1, "-- Select --", 2, "",0,"" );?>
                                    <input type="text" name="txt_team_member" id="txt_team_member" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="open_team_member();" readonly/>
                            		<input type="hidden" name="txt_team_member_id" id="txt_team_member_id" />
                                </td>
                                <td>
                                	<input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:100px"  value="<?= date('d-m-Y', strtotime('-1 day', time()));?>"/>
                                </td>
                                <td>To</td>
                                <td>
                                	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:100px"  value="<?= date('d-m-Y');?>"/>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                            </tr>
                           <tr>
                                <td colspan="7" align="center">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div> 
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>