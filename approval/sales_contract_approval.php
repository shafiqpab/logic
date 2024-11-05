<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sales Contract Approval		
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	26-07-2022
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
//echo load_html_head_contents("PI Approval", "../", 1, 1,'','','');
echo load_html_head_contents("Sales Contract Approval", "../", 1, 1,'','','');

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
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sc_no*txt_system_id*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		freeze_window(3);
		http.open("POST","requires/sales_contract_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
    
	// show button response function 
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//var response=http.responseText.split("####");
			$('#report_container').html(http.responseText);
			//setFilterGrid("tbl_list_search",-1);
			show_msg('18');
			release_freezing();
		}
	}


	function downloiadFile(id,company_name)
	{
		var title = 'PI Approval New File Download';	
		var page_link = 'requires/sales_contract_approval_controller.php?action=get_user_pi_file&id='+id+'&company_name='+company_name;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			
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
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&target_ids='+target_ids+'&approved_user_id='+approved_user_id+get_submitted_data_string('cbo_company_name',"../");
	
		freeze_window(operation);
		
		http.open("POST","requires/sales_contract_approval_controller.php",true);
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
		var page_link = 'requires/sales_contract_approval_controller.php?action=user_popup&company_id='+$("#cbo_company_name").val();
		  
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

	function sales_contract_approval_report(id, system_id, company_name, report_id)
	{ 
	 
		if (report_id==753)
		{
			print_report(3+'**'+id, 'sales_contact_lien_letter', '../commercial/export_details/requires/sales_contract_controller');
		}
		if(report_id==754)
		{
			print_report(3+'**'+id, 'sales_contact_lien_letter2', '../commercial/export_details/requires/sales_contract_controller');
		}
		if(report_id==755){
			print_report(3+'**'+id, 'sales_contact_lien_letter3', '../commercial/export_details/requires/sales_contract_controller');
		}
		if(report_id==756){
			print_report(3+'**'+id+'**'+company_name, 'sales_contact_lien_letter4', '../commercial/export_details/requires/sales_contract_controller');
		}
		if(report_id==757){
			print_report(3+'**'+id, 'sales_contact_check_list', '../commercial/export_details/requires/sales_contract_controller');
		}
		if(report_id==78){
			freeze_window();
			var data="action=sales_contact_print&report_type=3"+'&txt_system_id='+id+'&cbo_beneficiary_name='+company_name;
			http.open("POST","../commercial/export_details/requires/sales_contract_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_lien_letter_response;
		}
		if(report_id==466){
			print_report(3+'**'+id, 'sales_contact_lien_letter5', '../commercial/export_details/requires/sales_contract_controller');
		}
		if(report_id==476){
			let print_in_pad=0;
			if(confirm("Do you want to print in Pad?\n\nPress Ok to 'Allow'\nPress Cancel to 'Not Allow'")){print_in_pad=1;}
			print_report(3+'**'+id+'**'+company_name+'**'+print_in_pad,'sales_contact_lien_letter7','../commercial/export_details/requires/sales_contract_controller');
		}
		if(report_id==829){
		    var page_link='../commercial/export_details/requires/sales_contract_controller.php?action=designation_search&txt_system_id='+id; 
			var title="Designation Select Bar";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var txt_system_id=this.contentDoc.getElementById("txt_system_id").value; 
				$("#txt_system_id").val(txt_system_id);
			}
		}
		if(report_id==427){
			var show_textcbo_consignee = $("#show_textcbo_consignee").val(); 
		    print_report(id+'**'+company_name+'**'+$('#show_textcbo_consignee').val(), 'sales_contact_lien_letter12', '../commercial/export_details/requires/sales_contract_controller');
		}
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",'');?>
		 <form name="salescontractApproval_1" id="salescontractApproval_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
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
                                <th colspan="2">SC Date</th>
                                <th>SC No</th>
                                <th>System ID</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('salescontractApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/export_lc_approval_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); $('#txt_alter_user').val('');$('#txt_alter_user_id').val('');" );
                                    ?>
                                </td>
                                <td id="buyer_td">
                                    <? 
                                        echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- Select Buyer --", 0, "");
                                    ?>
                                </td>                               
                                <td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
								</td>
                                <td>
									<input name="txt_sc_no" id="txt_sc_no" style="width:100px" class="text_boxes" placeholder="Write">
								</td>
                                <td>
									<input name="txt_system_id" id="txt_system_id" style="width:100px" class="text_boxes" placeholder="Write">
								</td>
                                <td> 
                                    <?
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