<?
/*-------------------------------------------- Comments
Purpose			: 	This page created for Garments Service Work Order Approval				
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	18-09-2022
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Erosion Approval", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
    
    // show button function 
	function fn_report_generated()
	{   
		var approval_setup =<? echo $approval_setup; ?>;
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			return;
		}
		else if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_party_name*txt_job_id*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/sub_contract_order_entry_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
    
	// show button response function 
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
	
    // check_all check box function 
	function check_all(tot_check_box_id)
	{
        if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				try 
				{                    
					var hide_approval_type=parseInt($('#hide_approval_type').val());
					
					if(!(hide_approval_type==1))
					{												
						$(this).find('input[name="tbl[]"]').attr('checked', true);						
					}
					else
					{
						$(this).find('input[name="tbl[]"]').attr('checked', true);
					}
				}
				catch(e) 
				{
					//got error no operation
				}				
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}
	
	function check_last_update(rowNo)
	{				       
        var isChecked = $('#tbl_'+rowNo).is(":checked");		
	}
	
    // Approve Button function 
	function submit_approved(total_tr,type)
	{ 
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
		else if(type==0)
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
		var target_id_arr=Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{								
                target_id = parseInt($('#target_id_'+i).val()); 
				
				if(target_id>0)
				{
					target_id_arr.push(target_id);
                }
			}
		}

		if(target_id_arr.length==0){
			alert('Please select one for approve or unapprove');
			return;
		}

		var target_ids = target_id_arr.join(',');

		$('#txt_selected_id').val(target_ids);
		//fnSendMail('../','',1,0,0,1,type);


		
		var data="action=approve&operation="+operation+"&target_ids="+target_ids+'&cbo_approval_type='+type+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_party_name*txt_job_id*txt_date_from*txt_date_to*txt_alter_user_id',"../");
		//alert(data);return;
		freeze_window(0);
		http.open("POST","requires/sub_contract_order_entry_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	let openImgFile=(id,action)=>{
		var page_link='requires/sub_contract_order_entry_approval_controller.php?action='+action+'&id='+id;
		if(action=='garments_service_work_order') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}
    // Approve Button responds function 
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
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
		$('#all_check').attr('checked',false);
	}
	
	
	function fn_generate_print(update_id,cbo_company_name,job_no){
	   //alert(update_id);
	   var all_data=cbo_company_name+'*'+update_id+'*'+job_no
		var data = "action=sub_order_print&data=" + all_data ;
		
		freeze_window(operation);
		http.open("POST", "../subcontract_bill/requires/sub_contract_order_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_generate_print_res;
	
}

function fn_generate_print_res(){
	
		if(http.readyState == 4) 
		{
			release_freezing();
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			
		}
}
	
	
	function change_user()
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		var title = 'PI Approval New';	
		var page_link = 'requires/sub_contract_order_entry_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_id',"../");
		  
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
			document.getElementById("comments_"+quo_id).innerHTML=cause;
		}
	 }
	 
	 function fnc_load_party(within_group)
	{
		var company = $('#cbo_company_id').val();
		load_drop_down( 'requires/sub_contract_order_entry_approval_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
	}

	 
</script>
</head>

<body>
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",''); ?>
		<form name="csApproval_1" id="csApproval_1"> 
         	<h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         	<div id="content_search_panel"> 
            	<fieldset style="width:1000px;">
                	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead> 
                        	<tr> 
                                <th colspan="4" align="center">
									<?
										$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
										if( $user_lavel==2)
										{
											?><span style="vertical-align: 5px;">Previous Approved:&nbsp;</span><span><input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value);" /></span>
											<?
										}
										else
										{
											?>
											<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none" />
											<?
										}
									?> 
                                </th>
                                <th colspan="4">
									<?
										$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
										if( $user_lavel==2)
										{
											?>Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user();" placeholder="Browse " style="width:200px" readonly>
											<?
										}
									?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>                    	
                            <tr>
                                <th>Company Name</th>
                                <th>Within Group.</th>
                                <th>Party Name</th>
                                <th>Job No.</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','');" class="formbutton" style="width:70px" /> <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/></th>
                        	</tr>
                        </thead>
                        <tbody>
                        	<tr class="general">                                
                                <td>
                                    <? 
                                        // echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- All Category --", $selected,"",1,4,"","","1,2,3,12,13,14");
                                    ?>
									<? echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
                                </td>
                                <td>
									<?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", 0, "fnc_load_party(this.value);" ); ?>
								</td>
                                <td id="buyer_td">
									<? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
									<!-- <? echo create_drop_down( "cbo_cs_year", 110, $year, "", 1, "-- Select --", date("Y", time()), "" ); ?> -->
								</td>
                                <td>
									<input name="txt_job_id" id="txt_job_id" style="width:80px" class="text_boxes" placeholder="Write">
								</td>
                                <td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
								</td>
                                <td> 
                                    <?
                                       echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated()"/></td> 
                                
                                <input type="hidden" id="txt_selected_id" name="txt_selected_id" />               	
                            </tr>                            
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_approval_type').val(0); </script>
</html>