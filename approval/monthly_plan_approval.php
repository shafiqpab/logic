<?
/*
Purpose			: This form will create for Monthly Approval				
Functionality	:	
JS Functions	:
Created by		: Al-Hassan
Creation date 	: 21-08-2023
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
$_SESSION['page_permission'] = $permission;
$menu_id = $_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Monthly Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=82 and is_deleted=0" );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<?= $permission; ?>';
    

	function fn_report_generated(type)
	{
		var approval_setup =<?= $approval_setup; ?>;
		freeze_window(3);
		if(approval_setup != 1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();
			return;
		}
		else if (form_validation('cbo_company_name' , 'Comapny Name') == false)
		{
			release_freezing();
			return;
		}

		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_form_year*cbo_form_month*cbo_to_year*cbo_to_month*cbo_approval_type*txt_alter_user_id*cbo_location_id',"../");
		http.open("POST","requires/monthly_plan_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = ()=>{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split("####");
				$('#report_container').html(response[0]);
				setFilterGrid("tbl_list_search",-1,{});
				show_msg('3');
				release_freezing();
			}
		}
	}
    
 
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Monthly Plan Approval';	
		var page_link = 'requires/monthly_plan_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("selected_id").value;
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#report_container").html('');
		}
	}


	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}


	function submit_approved(total_tr,type)
	{ 
		//alert(total_tr);
		//return;

		var target_id_arr = Array();
		var comment_id_arr = Array();
		freeze_window(0);
		// confirm message   ***************************************************************
		if(type==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
			}
		}
		// confirm message finish ********************************************************************

		for(i=1; i < total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				target_id_arr.push($('#target_id_'+i).val());
				var id =$('#target_id_'+i).val();
 
				comment_id_arr.push($('#txt_comments_'+i).val()+'_'+id);

			}
		}
		  
		if(target_id_arr.length == 0)
		{
			alert("Please Select At Least One Job");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&cbo_approval_type='+type+'&comments='+comment_id_arr+'&target_id_str='+target_id_arr.join(',')+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
	   //alert(data);
		
		http.open("POST","requires/monthly_plan_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =()=>{
			if(http.readyState == 4) 
			{ 
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
				{
					fnc_remove_tr();
				}
				if(reponse[0]==25)
				{
					fnc_remove_tr();
					alert("You Have No Authority To Approved this.");
				}		

				release_freezing();	
			}
		}
	}	

	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tbody tr').length;
		for(var i=1;i<=tot_row;i++)
		{
			if($('#tbl_'+i).is(':checked'))
			{
				$('#tr_'+i).remove();
			}
		}
		$('#all_check').attr('checked',false);
	}
	
	function fnc_comments(id, value)
	{ 
		var page_link='requires/monthly_plan_approval_controller.php?action=comments_popup&comments_data='+value;
		var title='Comments Info';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var comments_data=this.contentDoc.getElementById("txt_comments").value;
			$('#'+id).val(comments_data);
		}
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",'');?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:820px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:820px;">
                 <table width="100%" class="rpt_table"  cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>                        	
					    <tr> 
                            <th colspan="4"></th>
                            <th colspan="3">
								<?php 
								$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
								if($user_lavel == 2){
								?>
									Alter User:
									<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()" placeholder="Browse " readonly>
								<?php
								}
								?>
								<input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                            </th>
                        </tr>
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Location</th>
                            <th colspan="2">Month Range</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_plan_approval_controller',this.value, 'load_drop_down_location', 'location_td_id' );" );
                                ?>
                            </td>
							<td id="location_td_id">
							    <?
                                	echo create_drop_down( "cbo_location_id", 120, [],"", 1, "--Location --",0, "" );
                                ?>
                            </td> 
                            <td>
							    <?
                                	echo create_drop_down( "cbo_form_year", 60, $year,"", 1, "-- Year --", date("Y"), "" );
                                	echo create_drop_down( "cbo_form_month", 80, $months,"", 1, "-- Month --", 0, "" );
                                ?>
                            </td> 
							<td>
							    <?
                                	echo create_drop_down( "cbo_to_year", 60, $year,"", 1, "-- Year --", date("Y"), "" );
                               	 	echo create_drop_down( "cbo_to_month", 80, $months,"", 1, "-- Month --", 0, "" );
                                ?>
                            </td>                                 
                            <td> 
                                <?
                                	echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                ?>
                            </td>
                            <td>
								<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/>
								<input type="hidden" id="txt_selected_id" name="txt_selected_id" />
						    </td>
                        </tr>
                    </tbody>
                 </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="data_panel2" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_approval_type').val(0); </script>
</html>