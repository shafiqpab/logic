<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gate Pass Approval v2
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam REZA 
Creation date 	: 	03-05-2023
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
echo load_html_head_contents("Gate Pass Approval", "../", 1, 1,'','','');

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
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_basis*cbo_department_id*txt_system_id*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/gate_pass_entry_approval_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split("####");
				$('#report_container').html(response[0]);
				setFilterGrid("tbl_list_search",-1);
				show_msg('3');
				release_freezing();
			}
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
		var gatePass_nos = "";  var gatePass_ids = ""; var approval_ids = ""; 
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
				gate_id = $('#gate_id_'+i).val();
				if(gatePass_ids=="") gatePass_ids= gate_id; else gatePass_ids +=','+gate_id;
				
     			approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		if(gatePass_ids=="")
		{
			alert("Please Select At Least One Gate Pass No");
			release_freezing();
			return;
		}
		var alterUserID = $('#txt_alter_user_id').val();
		var data="action=approve&operation="+operation+'&txt_alter_user_id='+alterUserID+'&approval_type='+type+'&gatePass_ids='+gatePass_ids+'&approval_ids='+approval_ids+get_submitted_data_string('cbo_company_name',"../");
		//alert(data);return;
		
		http.open("POST","requires/gate_pass_entry_approval_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = ()=>{		
			if(http.readyState == 4) 
			{ 
				// alert(http.responseText);
				var reponse=trim(http.responseText).split('**');
				//alert(http.responseText);release_freezing();return;	
				show_msg(reponse[0]);
				if((reponse[0]==19 || reponse[0]==20 || reponse[0]==37))
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
		var title = 'Gate Pass Approval';	
		var page_link = 'requires/gate_pass_entry_approval_v2_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
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
		var page_link='requires/gate_pass_entry_approval_v2_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}

	function change_approval_type(value)
	{
		if(value==0)
		{
			$("#cbo_approval_type").val(1);
			$("#cbo_approval_type").attr("disabled",true);	
		}
		else
		{
			$("#cbo_approval_type").val(0);
			$("#cbo_approval_type").attr("disabled",false);
		}		
	}

	function generate_trims_print_report(company_id,sys_number,print_btn,location_id,emb_issue_ids,basis,returnable)
	{ 
		
		  var report_title="Gate Pass Entry";
				if(print_btn==116)
				{

					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else  
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report&template_id=1', true );
				}
				else if(print_btn==136)
				{
					if(basis==13){

						window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+emb_issue_ids+'&action=get_out_entry_emb_issue_print&template_id=1', true );
					}
				}
				else if(print_btn==137)
				{
				   var show_item=0;	
                   window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report5&template_id=1', true );		        	
				}
				else if(print_btn==129)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}

					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print12&template_id=1', true );	
					
					// return;
				}
				else if(print_btn==191)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report_13&template_id=1', true );	

				}
				else if(print_btn==196)
				{
				
					if($("#cbo_basis").val()!=14)
					{
						alert('Report Generate only for Challan[Cutting Delivery] Basis');
					}
					else
					{
						var show_item=0;	
						window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+show_item+'*'+emb_issue_ids+'*'+location_id+'&action=print_to_html_report6&template_id=1', true );	

					}
				}
				else if(print_btn==199)
				{
					

					if(basis!=4 && basis!=3)
					{
						alert('Report Generate only for Challan[Grey Fabric] and Challan[Finish Fabric] Basis');
					}
					else
					{
						var show_item=0;	

						window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+show_item+'*'+emb_issue_ids+'&action=print_to_html_report7&template_id=1', true );	

					}
				}
				else if(print_btn==207)
				{
					if(basis==12)
					{
						var show_item='';			
 						window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=print_to_html_report9&template_id=1', true );	
					}
					else
					{
						alert("This is for Garments Delivery Basis");
					}
				}
				else if(print_btn==208)
				{
					
					if(basis==28)
					{
						var show_item='';	

						window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=print_to_html_report10&template_id=1', true );	
					}
					else
					{
						alert("This is for Sample Delivery Basis");
					}
				}
				else if(print_btn==212)
				{
					
					if(basis==2)
					{				
						window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'&action=print_to_html_report11&template_id=1', true );
					}
					else
					{
						alert("This is for Yarn Basis Only");
					}
				}
				else if(print_btn==271)
				{
					if(basis==11)
					{			
						window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'&action=print_to_html_report14&template_id=1', true );
					}
					else
					{
						alert("This is for Finish Fabric Delivery to Store Basis");
					}
				}
				else if(print_btn==707)
				{
					
					if (basis != 8){
					alert("This Button Only For Subcon Knitting Delevery Basis");
					return;
					}			
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+basis+'*'+emb_issue_ids+'&action=print_to_html_report17&template_id=1', true );
				}
				else if(print_btn==115)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print&template_id=1', true );
				}
				else if(print_btn==161)
				{
									
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print6&template_id=1', true );
				}
				else if(print_btn==206)
				{
					var show_item="0";
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print8_fashion&template_id=1', true );	
					return;
				}
				else if(print_btn==235)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print9&template_id=1', true );	
					return;
				}
				else if(print_btn==274)
				{
				
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'*'+1+'&action=get_out_entry_print10&template_id=1', true );	
				}
				else if(print_btn==738)
				{
					if(basis==13){

					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_printamt&template_id=1', true );
					}
					else{
						alert("This is for Embellishment Issue Entry");
					}
				}
				else if(print_btn==747)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print14&template_id=1', true );
				
				}
				else if(print_btn==241)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_pass_entry_print11&template_id=1', true );
					return;
				}
				else if(print_btn==427)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print20&template_id=1', true );
					return;
				}
				else if(print_btn==28)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print21&template_id=1', true );
					return;
				}
				else if(print_btn==437)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print22&template_id=1', true );
					return;
				}
				else if(print_btn==719)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}		
					window.open("../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print16&template_id=1', true );
					return;
				}	
	}
	
</script>


</head>
<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		<form name="requisitionApproval_1" id="requisitionApproval_1"> 
        <h3 style="width:1050px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
        <div id="content_search_panel">      
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="98%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <tr>
                            <th colspan="3" align="center">                            
                            </th>
                            <th colspan="5">
	                            <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if($user_lavel==2)
									{
										?>
	                                    Alter User:
	                                    <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()" placeholder="Browse " readonly  style="width:170px" />
	                                	<?php 
									}
								?>
	                            <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                            </th>
                        </tr>                        
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Basis</th>
							<th>Department</th>
							<th>Gate Pass ID</th>
                            <th colspan="2" >Date Range</th>                         
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                	<tbody>
                    	<tr class="general">
							<td> 
								<?
								echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected);
								?>
							</td>
							<td> 
								<?= create_drop_down("cbo_basis", 150,$get_pass_basis,"", 1,"-- All --","0","","",""); ?>
							</td>
							<td> 
								<?= create_drop_down("cbo_department_id", 150,"select ID,DEPARTMENT_NAME from LIB_DEPARTMENT where STATUS_ACTIVE=1 and IS_DELETED=0","ID,DEPARTMENT_NAME", 1,"-- All --","0","","","");?>
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