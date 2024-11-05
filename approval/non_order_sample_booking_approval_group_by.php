<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Booking Approval
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam REZA 
Creation date 	: 	02-12-2023
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
echo load_html_head_contents("Non Order Sample Booking Approval Group By", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=9" );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';


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
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_approval_type*txt_booking_no*txt_alter_user_id*txt_ref_no*cbo_year',"../");
		
		http.open("POST","requires/non_order_sample_booking_approval_group_by_controller.php",true);
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
	
	function check_all(tot_check_box_id)
	{
		var hide_approval_type=parseInt($('#cbo_approval_type').val());
		var i=1;
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_list_search tbody tr').each(function() {
								
				try 
				{										
					if( hide_approval_type==1 && i>1)
					{
						var booking_no=$(this).find('input[name="booking_no[]"]').val();
						
						var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/non_order_sample_booking_approval_group_by_controller');
						if(salse_order_approved==1 || salse_order_approved==3)
						{
							alert("Corresponding Sales Order is approved.So Booking Unapproved Not Allow.");
							$(this).find('input[name="tbl[]"]').attr('checked', false);
						}
						else 
						{
							$(this).find('input[name="tbl[]"]').attr('checked', true);
						}								
					}
					else $(this).find('input[name="tbl[]"]').attr('checked', true);
					i++;
						
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
	
	function check_on_scan(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var tbl_len=$("#tbl_list_search tbody tr").length;
		$('#tbl_'+row_no).attr('checked', true);
		var hide_approval_type=parseInt($('#cbo_approval_type').val());
		
		if( hide_approval_type==1)
		{
			var salse_order_approved=return_global_ajax_value( trim(scan_no), 'check_sales_order_approved', '', 'requires/non_order_sample_booking_approval_group_by_controller');
			if(salse_order_approved==1 || salse_order_approved==3)
			{
				alert("Corresponding Sales Order is approved.So Booking Unapproved Not Allow.");
				$('#tbl_'+row_no).attr('checked', false);
			}
			else 
			{
				$('#tbl_'+row_no).attr('checked', true);
			}

		}
		//new
		if($('#tbl_'+row_no).is(":checked")==false)
		{
			alert("No data found");
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			return;
		} 
		else
		{
			submit_approved(tbl_len, $('#cbo_approval_type').val());
		}
	}
	
	function check_booking_approved(rowNo)
	{
		//alert("su..re"); return;
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var booking_no=$('#booking_no_'+rowNo).val();
		var approval_type=$('#cbo_approval_type').val();
		
	
		if(isChecked==true && approval_type==1)
		{
			var salse_order_approved=return_global_ajax_value( trim(booking_no), 'check_sales_order_approved', '', 'requires/non_order_sample_booking_approval_group_by_controller');
			if(salse_order_approved==1 || salse_order_approved==3)
			{
				alert("Corresponding Sales Order is Approved.So Booking Unapproved Not Allow.");
				$('#tbl_'+rowNo).attr('checked',false);
			}
		}
	}
	
	
		
	function submit_approved(total_tr,type,approved_user_id)
	{ 
		var target_id_arr= Array();
		freeze_window(0);
		// Confirm Message  ********************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
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
				first_confirmation=confirm("Are You Want to Approved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Booking No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End *********************************************
		
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
		if(type==5){
			$('#txt_selected_id').val(target_ids);
			fnSendMail('../','',1,0,0,1)
			
		}
		
		var alterUserID = $('#txt_alter_user_id').val();
		var data="action=approve&operation="+operation+'&approval_type='+type+'&target_ids='+target_ids+'&txt_alter_user_id='+alterUserID+get_submitted_data_string('cbo_company_name',"../");	
		//freeze_window(operation);		
		http.open("POST","requires/non_order_sample_booking_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);			
			var reponse=trim(http.responseText).split('**');	
		
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
			{
				fnc_remove_tr();
				show_msg(reponse[0]);
			}
			release_freezing();
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
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
	
	function generate_worder_report(booking_no,company_id,is_approved,entry_form,Print_id,type,i)
	{  //alert(update_id);
		var report_title="";

		if (entry_form==90)
		{
			report_title='Sample Fabric Booking -Without order';
			// ES6 String Literals
			var data=`action=${type}&txt_booking_no='${booking_no}'&cbo_company_name='${company_id}'&id_approved_id='${is_approved}'&report_title=${report_title}`;
			http.open("POST","../order/woven_order/requires/sample_booking_non_order_controller.php",true);
		}
		else if (entry_form==140)
		{
			report_title='Sample Fabric Booking -Without order';
			// ES6 String Literals
			var data=`action=${type}&txt_booking_no='${booking_no}'&cbo_company_name='${company_id}'&id_approved_id='${is_approved}'&report_title=${report_title}`;
			http.open("POST","../order/woven_order/requires/sample_requisition_booking_non_order_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_reponse;
	}

		
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4)
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}

	function generate_worder_report_samp(company_id,update_id,booking_no,entry_form,Print_id,type,i)
	
	{  //alert(update_id);
		var report_title="";

		 if (entry_form==203)
		{  report_title='Sample Fabric Booking -Without order';
			print_report( company_id+'*'+update_id+'*'+booking_no+'*'+report_title+'*1', "sample_requisition_print", "../order/woven_order/requires/sample_requisition_with_booking_controller" );
			return;
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_fabric_report_samp_reponse;
	}

		
	function generate_fabric_report_samp_reponse()
	{
		if(http.readyState == 4)
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}


	function generate_sample_requisition_with_booking(booking_no,company_id,booking_id,action)
	{
		var requisition_id = trim(return_global_ajax_value(booking_id, 'get_requisition_no_from_booking', '', 'requires/non_order_sample_booking_approval_group_by_controller'));
		print_report( company_id+'*'+requisition_id+'*'+booking_no+'*1*../', action, "../order/woven_order/requires/sample_requisition_with_booking_controller" );
		return;
	}

	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Sample Fabric Booking Aproval-With order';	
		var page_link = 'requires/non_order_sample_booking_approval_group_by_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=705px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			//load_drop_down( 'requires/pre_costing_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			load_drop_down( 'requires/non_order_sample_booking_approval_group_by_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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
         <h3 style="width:950px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:950px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="4">Barcode Scan:<input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                 <th align="center">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" value="0"  onChange="change_approval_type(this.value)" />
                                		<?
									}
									else
									{
										?>
                                		<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none"  />
                                		<?
									}
									?>                                 
                                </th>
                                <th colspan="2">
                                	<?
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                                		<?
									}									
									?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
								<th>Year</th>
                                <th>Buyer</th>
								<th>Req.No</th>
                                <th>Booking No</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                    echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/non_order_sample_booking_approval_group_by_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                    ?>
                                </td>
								<td>
									<?
									$selected_year=date("Y");                               
                       				echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--",$selected_year,'',0);
									?>
								</td>
                                <td id="buyer_td_id"> 
									<?
                                    echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
								<td> 
                                    <input type="text" value="" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:150px"/>
                                </td>
                                <td> 
                                    <input type="text" value="" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px"/>
                                </td>
                                <td> 
                                    <?
                                    echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/>

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
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>