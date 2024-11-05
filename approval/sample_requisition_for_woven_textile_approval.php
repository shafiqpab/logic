<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sample Requisition for Woven Textile Approval
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin 
Creation date 	: 	18-03-2023
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
$menu_id=$_SESSION['menu_id'];
//-------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Requisition for Woven Textile Approval", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*txt_system_id*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/sample_requisition_for_woven_textile_approval_controller.php",true);
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
			
			setFilterGrid("tbl_list_search",-1);
				
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
		// alert(type);return;
		var workOrder_nos = "";  var workOrder_ids = ""; var approval_ids = ""; 
		freeze_window(0);
		// Confirm Message  ******************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All System No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All System No");
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
				first_confirmation=confirm("Are You Want to Approved All System No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All System No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End *******************************************************************
		
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				work_order_id = $('#work_order_id_'+i).val();
				if(workOrder_ids=="") workOrder_ids= work_order_id; else workOrder_ids +=','+work_order_id;
				
     			approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		if(workOrder_ids=="")
		{
			alert("Please Select At Least One Requisition No");
			release_freezing();
			return;
		}
		var alterUserID = $('#txt_alter_user_id').val();
		var data="action=approve&operation="+operation+'&txt_alter_user_id='+alterUserID+'&approval_type='+type+'&workOrder_ids='+workOrder_ids+'&approval_ids='+approval_ids+get_submitted_data_string('cbo_company_name',"../");
		//alert(data);return;
		
		http.open("POST","requires/sample_requisition_for_woven_textile_approval_controller.php",true);
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
			//alert(http.responseText);release_freezing();return;	
			show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
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
		var title = 'Service Work Order Approval';	
		var page_link = 'requires/sample_requisition_for_woven_textile_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#report_container").html('');
		}
	}

	function openImgFile(id,action)
	{
		var page_link='requires/sample_requisition_for_woven_textile_approval_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
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
	 
	function generate_report(company_name,wo_no,wo_id,title)
	{
		print_report( company_name+'**'+wo_no+'**'+wo_id+'**'+title, "inquery_entry_print", "../order/woven_gmts/requires/sample_requisition_woven_textile_controller" );
			return;
			
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
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <tr>
                            <th colspan="2" align="center">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id'].""); 
									if( $user_lavel==2)
									{
										?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)"/>
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
									if($user_lavel==2)
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
							<th>Requisition No</th>
                            <th colspan="2" >Date Range</th>                         
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                	<tbody>
                    	<tr class="general">
							<td> 
								<?
								echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected);
								?>
							</td>
							<td><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes_numeric" style="width:120px"/></td>
							<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date"/></td>					
							<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" /></td>
							
							<td> 
								<?
								$approval_type_arr = array(0=>'Un-Approved',1=>'Approved');
								echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
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
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>