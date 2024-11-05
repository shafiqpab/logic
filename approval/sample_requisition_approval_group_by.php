<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Yern Requisition Approval
					
Functionality	:	
				

JS Functions	:

Created by		:	Shajib Jaman
Creation date 	: 	01-09-2023
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
$_SESSION['page_permission']=$permission;

$approval_setup=is_duplicate_field( "entry_form", "electronic_approval_setup", "entry_form=25 and is_deleted=0" );

echo load_html_head_contents("Sample Requisition Approval", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';


	function fn_report_generated()
	{
		freeze_window(3);
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}
		
		
		if($('#previous_approved').is(":checked")) previous_approved=1;
 		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_approval_type*cbo_buyer_name*cbo_season_name*txt_style_ref*txt_req_no*txt_st_date*txt_end_date*txt_alter_user_id*cbo_year',"../");
 		
		http.open("POST","requires/sample_requisition_approval_group_by_controller.php",true);
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
			var tableFilters = { col_0: "none"}//,col_3: "select", display_all_text: " --- All Category ---" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
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
		//var operation=4;
		
		var approval_ids='';
		freeze_window(0);
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Requisition No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Requisition No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
			
		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Requisition No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Requisition No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}			
		}
		// Confirm Message End ***************************************************************************************************
		var appv_cause_arr = Array(); var req_id_arr = Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				req_id_arr.push($('#req_id_'+i).val());
				var approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}

				appv_cause_arr.push($('#txt_appv_cause'+i).val());
			}

		}

		
		var req_ids = req_id_arr.join(",");
		var appv_causes = appv_cause_arr.join("**");
		var alterUserID = $('#txt_alter_user_id').val();
		var company_name = $('#cbo_company_name').val();

		if(type==5){
			$('#txt_selected_id').val(req_ids);
			fnSendMail('../','',1,0,0,1);
		}
		var data="action=approve&operation="+operation+'&approval_type='+type+'&req_ids='+req_ids+'&txt_alter_user_id='+alterUserID+'&approval_ids='+approval_ids+'&appv_causes='+appv_causes+'&cbo_company_name='+company_name;
		
		http.open("POST","requires/sample_requisition_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//show_msg(reponse[0]);
			
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
			{
				fnc_remove_tr();
				//return;
			}
			
		  	if(reponse[0]=='308')
			{
				 alert('Requisition  '+ reponse[2] +'  has already acknowledged');
				 alert('You can not UnApproved this Requisition Before unacknowledged');
				// return;
 			}
			release_freezing();	
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
	}
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Sample Requisition Approval';	
		var page_link = 'requires/sample_requisition_approval_group_by_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			//load_drop_down( 'requires/pre_costing_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			// load_drop_down( 'requires/sample_requisition_approval_group_by_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			$("#report_container").html('');
		}
	}
	
	function change_approval_type(value)
	{
		if(value==0)
		{
			$("#previous_approved").val(1);
			$("#cbo_approval_type").val(1);
			$("#cbo_approval_type").attr("disabled",true);	
		}
		else
		{
			$("#previous_approved").val(0);
			$("#cbo_approval_type").val(0);
			$("#cbo_approval_type").attr("disabled",false);
		}		
	}	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:900px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:900px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                        		<th colspan="4"></th>                            	
                                 <th colspan="2" align="center">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)" />

                                <?php
									}
									else
									{
								?>
                                		<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none"  />
                                <?php	
									}
								?> 
                                 
                                </th>
                                <th colspan="4">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
									?>
                                		<!--<input type="button" class="image_uploader" style="width:100px" value="CHANGE USER" onClick="change_user()">-->
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
										<input type="hidden" id="txt_selected_id" name="txt_selected_id" />
                                	<?php 
									}
								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                        	<tr>
                        		<th class="must_entry_caption">Company Name</th>
								<th>Buyer</th>
								<th width="70">Season</th>
								<th width="70">Year</th>
								<th width="70">Style Ref</th>
								<th width="70">Req.No</th>
								<th>Delv St Date</th>
								<th>Delv End Date</th>
								<th>Approval Type</th>
	                            <th>
	                            	<input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" />
	                            </th>
                        	</tr>
                        </thead>
                        <tbody>
                           <tr class="general">
								<td> 
								<?
								echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_requisition_approval_group_by_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id');" );
								?>
								</td>
								<td id="buyer_td_id"> 
								<?
								echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
								?>
								</td>  
								<td id="season_td">  <? 
                                       echo create_drop_down( "cbo_season_name", 70,$blank_array ,"", 1, "-- Select Season --", $selected, "" ); ?></td>
								<td>  <? 
                                      $selected_year=date("Y");                               
									  echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--",$selected_year,'',0); ?></td>

								 
								<td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:65px"></td>
								<td><input name="txt_req_no" id="txt_req_no" class="text_boxes_numeric" style="width:65px"></td>

								<td><input type="text" name="txt_st_date" id="txt_st_date" class="datepicker" readonly style="width:80px"/></td>
								<td><input type="text" name="txt_end_date" id="txt_end_date" class="datepicker" readonly style="width:80px"/></td>
								<td>
								<?
								echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 1, "", $selected,"","", "" );
								?>
								</td>
								<td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>                	
                           </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <script src="../includes/functions_bottom.js" type="text/javascript"></script>
</body>
</html>