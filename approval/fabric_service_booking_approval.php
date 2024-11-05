<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Fabric Service Booking Approval
					
Functionality	:	
				

JS Functions	:

Created by		:	Rakib
Creation date 	: 	13-08-2022
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
echo load_html_head_contents("Fabric Service Booking Approval", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=65 and is_deleted=0" );

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
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_booking_no*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		freeze_window(3);
		http.open("POST","requires/fabric_service_booking_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4) 
			{
				$('#report_container').html(http.responseText);
				show_msg('18');
				release_freezing();
			}
		}
	 }
    

    // check_all check box function 
	function check_all(tot_check_box_id)
	{
		$("#tbl_list_search tbody").find('tr').each(function()
		{
			$(this).find('input[name="tbl[]"]').attr('checked', $('#'+tot_check_box_id).is(":checked"));		
		});
	}
	
 
    // Approve Button function 
	function submit_approved(total_tr,type,approved_user_id)
	{ 
		var target_id_arr= Array(); 
        
		// Confirm Message  ***************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
            if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All");
					if(second_confirmation==false)
					{
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
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All");
					if(second_confirmation==false)
					{
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
                target_id = parseInt($('#target_id_'+i).val());                            
                
                if(target_id>0)
				{
					target_id_arr.push(target_id);
                }
                
			}
		}
		var target_ids=target_id_arr.join(',');	
		
		$('#txt_selected_id').val(target_ids);
		fnSendMail('../','',1,0,0,1,type);
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&target_ids='+target_ids+'&approved_user_id='+approved_user_id+get_submitted_data_string('cbo_company_name',"../");
	
		freeze_window(operation);
		
		http.open("POST","requires/fabric_service_booking_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_response;
	}	
	
    // Approve Button responds function 
	function submit_approved_response()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=http.responseText.split('**');
			fnc_remove_tr();
			//show_msg(reponse[1]);
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
	
	
	
	function change_user()
	{
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		
		var title = 'CS Approval Accessories Info';	
		var page_link = 'requires/fabric_service_booking_approval_controller.php?action=user_popup&company_id='+$("#cbo_company_name").val();
		  
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
	
	function openmypage(req_id)
	{
		
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		
		
		var data=cbo_company_name+"_"+req_id;
		
		var title = 'Un Approval Request';	
		var page_link = 'requires/fabric_service_booking_approval_controller.php?data='+data+'&action=unapprove_request_action';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function fabric_booking_req_report(action,txt_booking_no,cbo_company_name,hidden_supplier_id,booking_mst_id){

	
		var show_comments="";
		var r=confirm("Press  \"Ok\"  to Show  Rate \nPress  \"Cancel\"  to Hide Rate ");
			
			if (r==true)
			{
				show_comments="1";
			}
			else
			{
				show_comments="0";
			}
		var show_buyer="0";

		

		window.open("../order/woven_order/requires/service_booking_multi_job_wise_dyeing_controller.php?txt_booking_no='" +txt_booking_no+"'&cbo_company_name="+cbo_company_name+"&hidden_supplier_id="+hidden_supplier_id+"&booking_mst_id="+booking_mst_id+"&show_comments="+show_comments+"&show_buyer="+show_buyer+"&action="+action, true );
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",'');?>
		 <form name="fabricservicebookingApproval_1" id="fabricservicebookingApproval_1">
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">Search Panel</h3> 
         <div id="content_search_panel">
             <fieldset style="width:1000px;">
                 <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>                        	
                            <tr>
                            	<td colspan="9" align="right"> 
                                <?php
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
									?>
                                    Alter User: 
                                    <input id="txt_alter_user" name="txt_alter_user" type="text" onDblClick="change_user();" class="text_boxes" style="width:150px" placeholder="Browse" readonly >
                                    <?php } ?>
                                    <input id="txt_alter_user_id" name="txt_alter_user_id" type="hidden">
                                </td>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Buyer</th>
                                <th>Year</th>
                                <th>Booking No</th>
                                <th colspan="2">Booking Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricservicebookingApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/fabric_service_booking_approval_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); $('#txt_alter_user').val('');$('#txt_alter_user_id').val('');" );
                                    ?>
                                </td>
                                <td id="buyer_td">
                                    <? 
                                        echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- Select Buyer --", 0, "");
                                    ?>
                                </td>

                                <td>
                                    <? echo create_drop_down( "cbo_year", 130, $year,"", 1, "-- Select --", 0, "" ); ?>
                                </td>
                                
                                <td>
									<input name="txt_booking_no" id="txt_booking_no" style="width:100px" class="text_boxes" placeholder="Write">
								</td>                                
                                <td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
								</td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
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