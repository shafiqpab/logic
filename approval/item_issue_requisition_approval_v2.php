<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Item Issue Requisition Approval v2
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	20-06-2023
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

$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT id,unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$id = $userCredential[0][csf('id')];
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];
$user_cred_item_cat_cond = "";$user_cred_company_id_cond = "";
if($id)
{
	if($item_cate_id != "")
	{
		$user_cred_item_cat_cond = $item_cate_id;
	}
	if($company_id != "")
	{
		$user_cred_company_id_cond = " and id in ($company_id) ";
	}
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Issue Requisition Approval V2", "../", 1, 1,'','','');

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
		var previous_approved=0;

		if($('#previous_approved').is(":checked")) previous_approved=1;
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_item_category_id*cbo_req_year*txt_date_from*txt_date_to*txt_requsition_no*cbo_approval_type*txt_alter_user_id',"../");
		
		http.open("POST","requires/item_issue_requisition_approval_controller_v2.php",true);
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
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var cause=$("#comments_"+quo_id).text();
		var txt_alter_user_id=$("#txt_alter_user_id").val();
		var page_link = page_link + "&quo_id="+quo_id + "&cause="+cause + "&txt_alter_user_id="+txt_alter_user_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			// alert(cause+"="+quo_id);
			// document.getElementById("comments_"+quo_id).innerHTML=cause;
			$('#comments_'+quo_id).val(cause);
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
		var approval_ids = ""; var requisition_ids = ""; 
        freeze_window(0);
		// Confirm Message  ***************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
            if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All");
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
				first_confirmation=confirm("Are You Want to Approved All");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End *******************************************************************
		var requisition_ids="";var approval_ids="";
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{								
                target_id = parseInt($('#requisition_id_'+i).val());                            
                
				// var comments =$('#comments_'+target_id).text();
				// if(target_id && comments==''){
				// 	alert("Please Write Comments");
				// 	release_freezing();
				// 	return;
				// }
				
				if(target_id>0)
				{
					if(requisition_ids=="") requisition_ids= target_id; else requisition_ids +=','+target_id;
				
                }
                approval_id = parseInt($('#approval_id_'+i).val());
                if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				
				if(type==5){
				
					$('#txt_selected_id').val(requisition_ids);
					fnSendMail('../','',1,0,0,1,type);
					
				}

	

			}
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&requisition_ids='+requisition_ids+'&approval_ids='+approval_ids+get_submitted_data_string
        ('cbo_company_name*cbo_item_category_id*previous_approved*txt_alter_user_id',"../");
       
		http.open("POST","requires/item_issue_requisition_approval_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split('**');	
			
			show_msg(reponse[0]);
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				
			}
			fnc_remove_tr();
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
		var title = 'User Info';	
		var page_link = 'requires/item_issue_requisition_approval_controller_v2.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#cbo_approval_type").val(0);
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

	function print_report(company_name,id,system_no,store_id,is_approved,action_type)
	{
		var report_title='';
		var approved_id='';
		var data=company_name+'*'+id+'*'+system_no+'*'+store_id+'*'+approved_id+'*'+action_type+'../../';
		var action='';
		if(action_type==2) action="print_item_issue_requisition_print2";
		else if(action_type==3) action="print_item_issue_requisition_print3";
		else
		{
			 action="print_item_issue_requisition";
			// alert(action);
		}

		http.open("POST","../inventory/requires/item_issue_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				window.open("../inventory/requires/item_issue_requisition_controller.php?action="+action+'&data='+data, "_blank");
		   }	
		}
	}

	function call_print_button_for_mail(mail,mail_body,type){
		 
	
		 var req_id=$('#txt_selected_id').val();
				
		 var txt_alter_user_id=$('#txt_alter_user_id').val();		
		 var cbo_company_name=$('#cbo_company_name').val();	
		 var sysIdArr=req_id.split(',');

	
		 var mail=mail.split(',');
		 var ret_data=return_global_ajax_value(sysIdArr.join(',')+'__'+mail.join(',')+'__'+txt_alter_user_id+'__'+cbo_company_name+'__'+type, 'app_mail_notification', '', 'requires/item_issue_requisition_approval_controller_v2');
		
	 }
		
</script>
</head>
<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr> 
                                 <th colspan="5" align="center">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>
                                		<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)" style="display:none" />

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
                                <th colspan="3">
                                <?php 
									if( $user_lavel==2)
									{
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
                                <th>Item Category</th>
                                <th>Requisition Year</th>
                                <th colspan="2">Date Range</th>
                                <th>Requsition No.</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $user_cred_company_id_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "",0 );
                                    ?>
                                </td>
                                <td>
                                    <? 
                                        echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- All Category --", $selected,"",0,"$user_cred_item_cat_cond","","","");
                                    ?>
                                </td>
                                <td>
									<? echo create_drop_down( "cbo_req_year", 110, $year, "", 1, "-- Select --", date("Y", time()), "" ); ?>
								</td>
								 <td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
								</td>
                                <td>
                                    <input type= "text" name="txt_requsition_no" id="txt_requsition_no" class="text_boxes" style="width:120px;" placeholder="write">
                                </td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td> 
								<input type="hidden" id="txt_selected_id" name="txt_selected_id" />               	
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
<script>
	$('#cbo_approval_type').val(0);
</script>
</html>