<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Work Order for AOP Approval
					
Functionality	:	
				

JS Functions	:

Created by		:	Shajib Jaman
Creation date 	: 	01-0-2023
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
echo load_html_head_contents("Service Booking For AOP Approval", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "entry_form", "electronic_approval_setup", "entry_form=88 and is_deleted=0" );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
    
    // show button function 
	function fn_report_generated()
	{
		var approval_setup =<? echo $approval_setup; ?>;
		freeze_window(3);
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
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_transport_company*txt_bill_no*cbo_trns_type*txt_system_no*txt_date_from*cbo_approval_type*txt_date_to*txt_alter_user_id',"../");
		//alert(data);
		http.open("POST","requires/transport_bill_entry_approval_controller.php",true);
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
			$('#txt_bar_code').focus();
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
		//var operation=4; 
		var approval_ids = ""; var target_ids = ""; 
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
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{								
                target_id = parseInt($('#target_id_'+i).val());                            
                
                if(target_id>0)
				{
					if(target_ids=="") target_ids= target_id; else target_ids +=','+target_id;
				
                }
                approval_id = parseInt($('#approval_id_'+i).val());
               // alert(approval_id);
                if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}

		
		if(type==5){
			//alert(target_ids);release_freezing();	return;
			
			$('#txt_selected_id').val(target_ids);
			//fnSendMail('../','',1,0,0,1)
			
		}

		
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&approval_ids='+approval_ids + '&target_ids='+target_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
	     
		http.open("POST","requires/transport_bill_entry_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
    // Approve Button responds function 
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');
			
			//how_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20|| reponse[0]==50))
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
	
	
	
	
function generate_order_report(booking_no,company_id,is_approved, entry_form_id,report_id,fab_booking,action)
	{
		// alert(report_id);
		if(report_id==163 ||  report_id==164  || report_id==177 || report_id==16 || report_id==176 || report_id==288){	
		    show_comment="1";	
			var data="action="+action+'&txt_currier_no='+"'"+booking_no+"'"+'&cbo_company_name='+"'"+company_id+"'"+'&txt_fab_booking='+"'"+fab_booking+"'";+'&show_comment='+show_comment+'&path=../../';		
			http.open("POST","../order/woven_order/requires/service_booking_aop_urmi_controller.php",true);

		}else{
				var data="action=show_work_order_aop_report"+
						'&txt_currier_no='+"'"+booking_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&id_approved_id='+"'"+is_approved+"'";
						
			    http.open("POST","requires/transport_bill_entry_approval_controller.php",true);		
		}

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}
	
function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'PI Approval New';	
		var page_link = 'requires/transport_bill_entry_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
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
	
	
		
	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",'');?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1450px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1450px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>                        	
                        <tr> 
                            <th colspan="7"></th>
                            <th colspan="4">
                            <?php 
                                $user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
                                if( $user_lavel==2)
                                {
                            ?>
                                    Alter User:
                                    <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                            <?php 
                                }
                                
                            ?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
								<input type="hidden" id="txt_selected_id" name="txt_selected_id" />
                            </th>
                        </tr>
                    
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Trans. Comp.</th>
                            <th> Trans. Type</th>
							<th>System No</th>
							<th>Bill NO</th>
                            <th colspan="2">Bill Date</th>
                           
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/transport_bill_entry_approval_controller',this.value, 'load_drop_down_transport_com', 'buyer_td_id' );" );
                                ?>
                            </td>
                            <td id="buyer_td_id"> 
                                <?
                                   echo create_drop_down( "cbo_transport_company", 152, $blank_array,"", 1, "-- All Transport Company --", 0, "" );
                                ?>
                            </td>                                
                            <td>
                            <?
							 echo create_drop_down( "cbo_trns_type",100,array(1=>"Export",2=>"Import"),'',1,'--Select--',0,"fn_container(this.value)",0);
							?>
                            </td>  
							
                            
                            
                            
                            <td><input name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:100px"></td>

                            <td><input name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:100px"></td>
							<td> 
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" /> 
                            </td> 
							<td> 
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" /> 
				               
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
    <div id="data_panel2" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_approval_type').val(0); </script>
</html>