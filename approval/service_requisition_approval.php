<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Service Requisition Approval
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	08-06-2022
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
echo load_html_head_contents("Service Requisition Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=61 and is_deleted=0" );
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';


	function fn_report_generated()
	{ 
		var approval_setup =<? echo $approval_setup; ?>;
		//freeze_window(3);
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();
			return;
		}
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}

		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_req_year*txt_req_no*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		
 		//alert(data);return false;
		
		http.open("POST","requires/service_requisition_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4) 
			{
				var response=http.responseText.split("####");
				$('#report_container').html(response[0]);
				var tableFilters = { col_0: "none" }
				setFilterGrid("tbl_list_search",-1,tableFilters);
				show_msg('3');
				release_freezing();
			}
		}
	}
	
 
	
	function check_all(tot_check_box_id=0)
	{
		$('#tbl_list_search tbody tr').each(function() {
			$('#tbl_list_search tbody tr input:checkbox').attr('checked', $('#'+tot_check_box_id).is(":checked"));
		});
	}
		
	function submit_approved(total_tr=0,type=0)
	{ 
		freeze_window(3);
		var requisition_arr = Array(); var appv_cause_arr = Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				requisition_arr.push($('#requisition_id_'+i).val());
				appv_cause_arr.push($('#txt_appv_cause_'+i).val());
			}
		}
		var req_nos = requisition_arr.join(',');
		var appv_causes = appv_cause_arr.join('**');

		var alterUserID = $('#txt_alter_user_id').val();
		var data="action=approve&operation="+operation+'&txt_alter_user_id='+alterUserID+'&approval_type='+type+'&req_nos='+req_nos+'&appv_causes='+appv_causes+get_submitted_data_string('cbo_company_name',"../");
	 	// alert(data);
		
		http.open("POST","requires/service_requisition_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4) 
			{
				var reponse=http.responseText.split('**');	
				if(reponse[0]==40)
				{
					alert(reponse[1]);
					release_freezing();	
					return;
				}
				show_msg(reponse[0]);
				if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
				{
					fnc_remove_tr();
				}
				release_freezing();	
			}
		}
	}	
	
 
	
	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tbody tr').length;
		var req_id_str='';
		for(var i=1;i<=tot_row;i++)
		{
			if($('#tbl_'+i).is(':checked'))
			{
				if(req_id_str==''){req_id_str=$('#req_id_'+i).val();}
				else{req_id_str=+','+$('#req_id_'+i).val();}
				$('#tr_'+i).remove();
			}
		}
		sendMail(req_id_str);
	}
	
	function sendMail(req_id_str=0)
	{
		
		var cbo_company_name=$('#cbo_company_name').val();
		var data="action=service_requisition_approval&company_id="+cbo_company_name+"&req_id="+req_id_str;
		//alert(data);

		http.open("POST","../auto_mail/service_requisition_approval_auto_mail.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fnc_btb_mst_reponse()
		{
			if(http.readyState == 4)
			{
				var reponse=http.responseText;
				console.log(reponse);
			}
		}
		

		
	}	
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Purchase Requisition Approval';	
		var page_link = 'requires/service_requisition_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=390px,center=1,resize=1,scrolling=0','');
		
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
		
	function openmypage(req_id=0)
	{
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		var data=cbo_company_name+"_"+req_id;
		var title = 'Un Approval Request';	
		var page_link = 'requires/service_requisition_approval_controller.php?data='+data+'&action=unapprove_request_action';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function change_approval_type(value='')
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

	function print_report(company_name=0,id=0,report_title=0,cbo_temp_id=0,action_type='')
	{
		var report_title='Service Requisition';
		var cbo_temp_id='';

		var data=company_name+'*'+id+'*'+report_title+'*'+cbo_temp_id+'*'+action_type+'***'+'../../';
		var action='';
		if(action_type==1) 
			action="service_requisition_print";
		else if(action_type==2) {
			action="service_requisition_print2";
        }else{
			action="service_requisition_po_print";
		}
		
		freeze_window(5);

		http.open("POST","../commercial/work_order/requires/service_requisition_controller.php",true);

			
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{

			if(http.readyState == 4) 
		    {
		    	//alert(action+"**"+action_type);
				window.open("../commercial/work_order/requires/service_requisition_controller.php?action="+action+'&data='+data, "_blank");
				release_freezing();
		   }	
		}
	}



</script>


</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:950px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:950px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr>
                        		<tr>                            	
                                 <th colspan="5" align="center"></th>
                                <th colspan="5">
                                <?php 
								$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
								if( $user_lavel==2)
								{
									?>
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user();" placeholder="Browse " readonly>
                                	<?php 
								}									
								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                        	</tr>
                        	<tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Requisition Year</th>
                            <th>Requisition No</th>
                            <th colspan="2">Date Range</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_requisition_approval_controller', this.value, 'load_drop_down_store', 'store_td' ); " );
                                    ?>
                                </td>
                                <td>
									<? echo create_drop_down( "cbo_req_year", 110, $year, "", 1, "-- Select --", date("Y", time()), "" ); ?>
								</td>
                                <td>
									<input name="txt_req_no" id="txt_req_no" style="width:80px" class="text_boxes" placeholder="Write">
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