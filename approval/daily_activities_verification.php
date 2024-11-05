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
require_once('../includes/common.php');

extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Activities Verification", "../", 1, 1,'','','');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		freeze_window(3);
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			release_freezing();
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_team_leader_id*cbo_team_member_id*txt_date_from*txt_date_to',"../");
		http.open("POST","requires/daily_activities_verification_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container').html(response[0]);
			release_freezing();
		}
	}

	function calculate_minute(){
		var total_minutes=0;
		var txt_minutes = document.getElementsByName('txt_minutes[]');
		var inps = document.getElementsByName('update_id[]');
		for (var i = 0; i <inps.length; i++) {
			total_minutes +=txt_minutes[i].value*1;
		}
		document.getElementById('td_varified_minutes').innerHTML=total_minutes;
	}

	function fn_daily_activities( operation )
	{
		freeze_window(operation);
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			release_freezing();
			return;   
		}	
		else
		{
			
			var txt_re_comments = document.getElementsByName('txt_re_comments[]');
			var txt_minutes = document.getElementsByName('txt_minutes[]');
			var update_id = document.getElementsByName('update_id[]');
			var update_id_arr = [];var minutes_arr = [];var re_comments_arr = [];
			
			for (var i = 0; i < update_id.length; i++) {
				update_id_arr.push(update_id[i].value);
				minutes_arr.push(txt_minutes[i].value);
				re_comments_arr.push(txt_re_comments[i].value);
			}
			var update_id_str= update_id_arr.join('__');
			var minutes_str= minutes_arr.join('__');
			var re_comments_str= re_comments_arr.join('__');
			
			var data="action=save_update_delete&operation="+operation+"&update_id_str="+update_id_str+"&minutes_str="+minutes_str+"&re_comments_str="+re_comments_str;
			
			http.open("POST","requires/daily_activities_verification_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_daily_activities_reponse;
		}
	}
		 
	function fn_daily_activities_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			
			
				show_msg(trim(reponse[0]));
				//set_button_status(0, permission, 'fnc_buyer_inspection_entry',1); 
				//show_list_view(document.getElementById('txt_job_no').value,'show_active_listview','inspection_production_list_view','requires/buyer_inspection_controller','setFilterGrid(\'tbl_list_search\',-1)');
				//reset_form('','','cbo_order_id','','');
			
				release_freezing();
		}
	}


	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
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
									<? echo create_drop_down( "cbo_team_leader_id", 150, "select id,team_leader_name from lib_team_mst where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/daily_activities_verification_controller', this.value, 'load_team_member', 'td_team_member' ); " ); ?>
                                </td>
                                
                                <td id="td_team_member"> 
                                    <? echo create_drop_down( "cbo_team_member_id", 150, $type,"",1, "-- Select --", 2, "",0,"" );?>
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
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>