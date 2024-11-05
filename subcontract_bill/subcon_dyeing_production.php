<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing Production Entry

Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-05-2014
Updated by 		: 		
Update date		: 
Oracle Convert 	:	Kausar		
Convert date	: 	24-05-2014	  
QC Performed BY	:		
QC Date			:	
Comments		:
*/ 

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyeing Production Entry Info","../", 1, 1, "",'1','');
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });
     });

	function openmypage_batchnum()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_no = $('#txt_batch_no').val();

		/* if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		} */
		
		if (form_validation('cbo_load_unload','Load Unload')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/subcon_dyeing_production_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				//alert( batch_id)
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+batch_id, "populate_data_from_batch", "requires/subcon_dyeing_production_controller" );
					show_list_view(batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dyeing_production_controller','');
					
					release_freezing();
				}
				get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/subcon_dyeing_production_controller' );
			}
		}
	}
	
	function check_batch()
	{
		var batch_no=$('#txt_batch_no').val();
		var cbo_company_id = $('#cbo_company_id').val();
		if(batch_no!="")
		{
			/*if (form_validation('cbo_company_id','Company')==false)
			{
				return;
			}*/
			
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no, 'check_batch_no', '', 'requires/subcon_dyeing_production_controller');
		
			var response=response.split("_");
			//alert(response[0]);return;
			if(response[0]==0)
			{
				alert('Batch no not found.');
				
				$('#txt_batch_no').val('');
				$('#hidden_batch_id').val(''); 
				//$('#cbo_company_id').val(''); 
				$('#txt_update_id').val(''); 
				$('#cbo_sub_process').val('');
				$('#txt_process_end_date').val('');
				$('#txt_end_hours').val('');
				$('#txt_end_minutes').val('');
				$('#cbo_machine_name').val('');
				$('#txt_remarks').val('');
				reset_form('dyeingproduction_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
			}
			else
			{
				$('#hidden_batch_id').val(response[1]);
				get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+response[1]+'_'+batch_no+'_'+response[2], "populate_data_from_batch", "requires/subcon_dyeing_production_controller" );
				show_list_view(response[1],'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dyeing_production_controller','');
				get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/subcon_dyeing_production_controller' );
			}
		}
	}
	
	function fnc_pro_fab_subprocess( operation )
	{	
		if( form_validation('cbo_load_unload','Load Unload')==false )
		{
			return;
		} 
		
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		var service_source=document.getElementById('cbo_service_source').value;
		
		if (document.getElementById('cbo_load_unload').value==1)
		{
			if(service_source==3)
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_recevied_chalan*cbo_ltb_btb*txt_process_start_date*cbo_floor*cbo_machine_name*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*Received Challan*LTB/BTB*Process Date*Floor*Machine*Batch ID')==false )
				{
					return;
				}		
			}
			else
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*cbo_ltb_btb*txt_process_start_date*cbo_floor*cbo_machine_name*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*LTB/BTB*Process Date*Floor*Machine*Batch ID')==false )
				{
					return;
				}	
			}
			var start_hours=document.getElementById('txt_start_hours').value;	
			var start_minutes=document.getElementById('txt_start_minutes').value;
			if( start_hours=="" ||  start_minutes==""  )
			{
				alert('Hour & Minute Must fill Up');
				return;	
			}  
			
			var batch_no=$('#txt_batch_no').val();
			var machine_no=$('#cbo_machine_name').val();
			var cbo_company_id = $('#cbo_company_id').val();
			var batch_id = $('#hidden_batch_id').val();
			var yesno = $('#cbo_yesno').val();
			var response2=return_global_ajax_value( cbo_company_id+"**"+batch_no+"**"+batch_id+"**"+machine_no, 'check_for_shade_matched', '', 'requires/subcon_dyeing_production_controller');
			var response2=response2.split("_");
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no+"**"+batch_id+"**"+machine_no, 'check_batch_no_for_machine', '', 'requires/subcon_dyeing_production_controller');
			var response=response.split("_");
			if(operation==0)
			{
				if(response2[0]==1)
				{
					alert('This Batch Shade Matched');
					return;
				}
				else
				{
					if(yesno==2)
					{
						if(response[0]==1)
						{
							alert('This Machine Currently Loaded By='+response[1]);
							return;
						}
						else
						{
							var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_start_date*txt_start_hours*txt_start_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_yesno*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*txt_system_no',"../"); 
			//alert(data);return;
							  freeze_window(operation);
							  http.open("POST","requires/subcon_dyeing_production_controller.php",true);
							  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							  http.send(data);
							  http.onreadystatechange = fnc_pro_fab_subprocess_response;
						}
					}
					else if(yesno==1)
					{
						var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_start_date*txt_start_hours*txt_start_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_yesno*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*txt_system_no',"../");
						//alert(data);return;
						freeze_window(operation);
						http.open("POST","requires/subcon_dyeing_production_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = fnc_pro_fab_subprocess_response;
					}
				}
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_start_date*txt_start_hours*txt_start_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_yesno*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*txt_system_no',"../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","requires/subcon_dyeing_production_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_pro_fab_subprocess_response;
			}
		}
		
		if (document.getElementById('cbo_load_unload').value==2)//uploading here
		{
			//alert(operation);
			var service_source=document.getElementById('cbo_service_source').value;
			if(service_source==3)
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*txt_recevied_chalan*cbo_ltb_btb*txt_process_end_date*txt_process_date*cbo_floor*cbo_machine_name*cbo_result_name*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*Received Challan*LTB/BTB*Production Date*Process Date*Floor*Machine*Result*Batch ID')==false )			{
					return;
				} 	
			}
			else
			{
				if( form_validation('cbo_load_unload*txt_batch_no*cbo_company_id*cbo_service_source*cbo_service_company*cbo_ltb_btb*txt_process_end_date*txt_process_date*cbo_floor*cbo_machine_name*cbo_result_name*txt_batch_ID','Load Unload*Batch No*Company*Service Source*Service Company*LTB/BTB*Production Date*Process Date*Floor*Machine*Result*Batch ID')==false )
				{
					return;
				} 
			}
			
			var end_hours=document.getElementById('txt_end_hours').value;	
			var end_minutes=document.getElementById('txt_end_minutes').value;
			if( end_hours==0 ||  end_minutes==0 )
			{
				alert('Hour & Minute Must Be fill Up');
				return;	
			} 
			var dyeing_started_date=$('#txt_dying_started').val();
			//alert(dyeing_started_date);return;
			var dyeing_end_date=$('#txt_process_date').val();
			var load_hr_min=$('#txt_dying_end_load').val();
			var end_hours=$('#txt_end_hours').val();
			var end_minutes=$('#txt_end_minutes').val();
			var unload_hr_min=end_hours+':'+end_minutes;
			
			if(date_compare(dyeing_started_date, dyeing_end_date)==false)
			{
				alert('Process End date & Time should be greater than start date & Time');
				return;	
			}
			
			if(dyeing_started_date==dyeing_end_date)
			{
				if(load_hr_min>unload_hr_min)
				{
					alert('Process End date & Time should be greater than start date & Time');
					return;	
				}
			}
			
			var batch_no=$('#txt_batch_no').val();
			var cbo_company_id = $('#cbo_company_id').val();
			var batch_id = $('#hidden_batch_id').val();
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no+"**"+batch_id, 'check_batch_no_load', '', 'requires/subcon_dyeing_production_controller');
			var response=response.split("_");
			if(response[0]==0)
			{
				alert('Without Load  Unload Not Allow ');
				return;	
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_load_unload*txt_process_end_date*txt_end_hours*txt_end_minutes*cbo_company_id*txt_batch_no*txt_batch_ID*hidden_batch_id*txt_process_id*cbo_machine_name*cbo_result_name*txt_remarks*txt_ext_id*txt_update_id*cbo_floor*cbo_ltb_btb*txt_water_flow*cbo_shift_name*txt_process_date*cbo_service_source*cbo_service_company*txt_recevied_chalan*txt_issue_chalan*txt_issue_mst_id*roll_maintained*cbo_fabric_type*txt_system_no',"../");
				//alert (data);
				freeze_window(operation);
				http.open("POST","requires/subcon_dyeing_production_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_pro_fab_subprocess_response;
			}
		}
	}
		
	function fnc_pro_fab_subprocess_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			//document.getElementById('txt_batch_no').value = reponse[1];
			document.getElementById('txt_update_id').value = reponse[1];
			document.getElementById('txt_system_no').value = reponse[3];
			if(reponse[0]==0 || reponse[0]==1)
			{
				set_button_status(1, permission, 'fnc_pro_fab_subprocess',1,1);
				release_freezing();
			}
			
			//reset_form('dyeingproduction_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
			if(reponse[0]==11)
			{
				alert('Duplicate Unload Data Found');
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(reponse[0]==13)
			{
				alert('Duplicate Load Data Found');
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			else if(reponse[0]==100)
			{
				alert('Without Load Unload Not Allow');
				//show_msg(response[0]);
				release_freezing();
				return;
			}
			
			$('#cbo_yesno').focus();
			
			
		}
	}
	
	function load_list_view (str)
	{
		$('#txt_batch_ID').val('');
		$('#hidden_batch_id').val(''); 
		$('#txt_job_no').val('');
		$('#txt_machine_no').val('');
		$('#txt_buyer').val('');
		$('#txt_mc_group').val('');
		$('#txt_order_no').val('');
		$('#txt_color').val('');
		$('#txt_ltb_btb').val('');
		$('#txt_file').val('');
		$('#txt_ref').val('');
		reset_form('','load_unload_container','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
		//txt_cons_comp_1,txt_gsm_1,txt_body_part_1,txt_dia_width_1,txtroll_1,txt_batch_qnty_1,txtlot_1,txtyarncount_1,txtbrand_1
		$('#txt_cons_comp_1').val('');
		$('#txt_gsm_1').val('');
		$('#txt_body_part_1').val('');
		$('#txt_dia_width_1').val('');
		$('#txtroll_1').val('');
		$('#txt_batch_qnty_1').val('');
		$('#txtlot_1').val('');
		
		$('#txtyarncount_1').val('');
		$('#txtbrand_1').val('');
	
		
		show_list_view(str,'on_change_data','load_unload_container','requires/subcon_dyeing_production_controller','');
		document.dyeingproduction_1.txt_batch_no.focus()
		set_all_onclick(); roll_maintain();
		if(str==1)
		{
			document.getElementById('batch_no_th').innerHTML='Batch No';
			$('#batch_no_th').css('color','blue');
			document.getElementById('company_th').innerHTML='Company';
			$('#company_th').css('color','blue');
			document.getElementById('service_source_caption').innerHTML='Service Source';
			$('#service_source_caption').css('color','blue');
			document.getElementById('service_company_caption').innerHTML='Service Company';
			$('#service_company_caption').css('color','blue');
			document.getElementById('ltb_ltb_caption').innerHTML='BTB LTB';
			$('#ltb_ltb_caption').css('color','blue');
			//document.getElementById('process_start_date').innerHTML='Process Start Date';
			$('#process_start_date').css('color','blue');
			//document.getElementById('hour_min_td').innerHTML='Process Start Time';
			$('#hour_min_td').css('color','blue');
			
			document.getElementById('floor_caption').innerHTML='Floor';
			$('#floor_caption').css('color','blue');
			document.getElementById('machine_caption').innerHTML='Machine Name';
			$('#machine_caption').css('color','blue');
		}
		else
		{
			document.getElementById('batch_no_th').innerHTML='Batch No';
			$('#batch_no_th').css('color','blue');
			document.getElementById('company_th').innerHTML='Company';
			$('#company_th').css('color','blue');
			document.getElementById('service_source_caption').innerHTML='Service Source';
			$('#service_source_caption').css('color','blue');
			document.getElementById('service_company_caption').innerHTML='Service Company';
			$('#service_company_caption').css('color','blue');
			document.getElementById('ltb_ltb_caption').innerHTML='BTB LTB';
			$('#ltb_ltb_caption').css('color','blue');
			document.getElementById('production_date_td').innerHTML='Productuon Date';
			$('#production_date_td').css('color','blue');
			document.getElementById('process_end_date').innerHTML='Process End Date';
			$('#process_end_date').css('color','blue');
			document.getElementById('process_end_time').innerHTML='Process End Time';
			$('#process_end_time').css('color','blue');
			document.getElementById('floor_caption').innerHTML='Floor';
			$('#floor_caption').css('color','blue');
			document.getElementById('machine_caption').innerHTML='Machine Name';
			$('#machine_caption').css('color','blue');
			document.getElementById('result_caption').innerHTML='Result';
			$('#result_caption').css('color','blue');
		}
		set_button_status(0, permission, 'fnc_pro_fab_subprocess',1);
	}
	
	function fnResetForm()
	{
		reset_form('dyeingproduction_1','load_unload_container','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
	}
	
	function fnc_move_cursor(val,id, field_id,lnth,max_val)
	{ //alert(val);
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
		if(val>max_val)
		{
			document.getElementById(id).value=max_val;
		}
		else
		{
			if(str_length==1)
			{
				if(val>max_val)
				{
					document.getElementById(id).value=max_val;
				}
				else
				{
					document.getElementById(id).value="0"+val;
				}
			}
		}
	}
	
	function scan_batchnumber(str)
	{
		var batch_no=$('#txt_batch_no').val();
		var cbo_company_id = $('#cbo_company_id').val();
	//	var response=return_global_ajax_value( cbo_company_id+"**"+str, 'check_batch_no_scan', '', 'requires/subcon_dyeing_production_controller');
		//var response=response.split("_");
		//check_batch();
		$('#txt_batch_no').val(str); 
		$('#cbo_company_id').focus();
		return;
	}
	
	$('#txt_batch_no').live('keydown', function(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
		// scan_batchnumber(this.value); 
		$('#txt_batch_no').removeAttr('onChange','onChange');// This function Call Off --onChange="check_batch()--;"
		$('#cbo_company_id').focus();
		 check_batch(); 
    }
});

	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/subcon_dyeing_production_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
		}
	}

	function openmypage_process_unload()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/subcon_dyeing_production_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup_unload';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
		}
	}
	
	function roll_maintain()
	{ 
		//get_php_form_data($('#cbo_company_id').val(),'roll_maintained_setting','requires/subcon_dyeing_production_controller' );
		var roll_maintained=$('#roll_maintained').val();
		if(roll_maintained==1)
		{
			$('#txt_issue_chalan').attr('placeholder','Write/Browse/Scan');
			$('#txt_issue_chalan').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_issue_chalan').attr('disabled','disabled');
		}
	}
	
	function openmypage_issue_challan()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		//var batch_no = $('#txt_batch_no').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/subcon_dyeing_production_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_popup','Issue Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			if(issue_id!="")
			{
				get_php_form_data(issue_id, "populate_data_from_data", "requires/subcon_dyeing_production_controller");
			}
		}
	}
	
	function check_issue_challan_scan(str) //Issue Challan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		if(issue_chalan!="")
		{
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/subcon_dyeing_production_controller');
			var response=response.split("_");
			//var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/heat_setting_controller');
			//var response=response.split("_");			
			if(response[0]==0)
			{
				alert('Issue Challan  not found.');
				$('#txt_issue_chalan').val('');
				$('#txt_issue_mst_id').val(''); 
				$('#txt_roll_id').val('');
				$('#cbo_service_source').val('');
				$('#cbo_service_company').val('');
				$('#txt_recevied_chalan').val('');
			}
			else
			{
				get_php_form_data(response[1], "populate_data_from_data", "requires/subcon_dyeing_production_controller" );
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(batch_id+'_'+hidden_roll_id,'issue_show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dyeing_production_controller','');
				$('#cbo_service_source').focus();
			}
		}
	}
	
	function check_issue_challan() //Issue Challan
	{
		var issue_chalan=$('#txt_issue_chalan').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_id=$('#hidden_batch_id').val();
		
		if(issue_chalan!="")
		{
			var response=return_global_ajax_value( cbo_company_id+"**"+issue_chalan, 'check_issue_challan_no', '', 'requires/subcon_dyeing_production_controller');
			var response=response.split("_");
			
			if(response[0]==0)
			{
				alert('Issue Challan  not found.');
				$('#txt_issue_chalan').val('');
				$('#txt_issue_mst_id').val(''); 
				$('#txt_roll_id').val('');
				$('#cbo_service_source').val('');
				$('#cbo_service_company').val('');
				$('#txt_recevied_chalan').val('');
				$('#'+txt_issue_chalan).focus();
			}
			else
			{
				get_php_form_data(response[1], "populate_data_from_data", "requires/subcon_dyeing_production_controller" );
				var row_num=$('#tbl_item_details tbody tr').length-1;
				
				var hidden_roll_id = $('#txt_roll_id').val();
				show_list_view(hidden_roll_id+'_'+batch_id,'issue_show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dyeing_production_controller','');
				
				$('#cbo_service_source').focus();
			}
		}
	}
	
	$('#txt_issue_chalan').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			check_issue_challan_scan(this.value); 
		}
	});
	
	function search_populate(str)
	{
		if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="Received Challan";
			$('#search_by_th_up').css('color','blue');
		}
		else
		{
			document.getElementById('search_by_th_up').innerHTML="Received Challan";
			$('#search_by_th_up').css('color','black');
		}
	}
	
function open_syspopup(page_link,title)
{
	if( form_validation('cbo_load_unload','Load Unload')==false )
	{
		return;
	}
	
	var title ='Batch System Popoup';
	var company = $("#cbo_company_id").val();
	var system_no = $("#txt_system_no").val();
	var load_unload = $("#cbo_load_unload").val();
	var batch_no = $("#txt_batch_no").val();
	page_link='requires/subcon_dyeing_production_controller.php?action=sys_popup&company='+company+'&batch_no='+batch_no+'&load_unload='+load_unload+'&system_no='+system_no;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px, height=400px, center=1, resize=0, scrolling=0','')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split("_"); // System No
		if (sysNumber!="")
		{
			
			freeze_window(5);
			
			//$("#update_id").val(sysNumber[0]);
			$("#txt_system_no").val(sysNumber[1]);
			$("#txt_batch_no").val(sysNumber[3]);
			//$("#cbo_load_unload").val(sysNumber[4]);
			//reset_form('','','txt_item_description*cbo_uom*txt_quantity*txt_rate*txt_amount','','','');
			//get_php_form_data(sysNumber[0], "populate_master_from_data", "requires/dyeing_production_controller" );
			
			//show_list_view( sysNumber[2]+'_'+sysNumber[4]+'_'+sysNumber[1],'show_dtls_batch_list_view','list_container','requires/subcon_dyeing_production_controller','');			//function disable_enable_fields( flds, operation, loop_flds, loop_leng )
			//disable_enable_fields( 'cbo_company_name*txt_pass_id*cbo_out_company*txt_receive_from*txt_challan_no', 1, "", "" );
			get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+sysNumber[2], "populate_data_from_batch", "requires/subcon_dyeing_production_controller" );
			show_list_view(sysNumber[2],'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dyeing_production_controller','');

get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/subcon_dyeing_production_controller' );
			
			//set_button_status(0, permission, 'fnc_getin_entry',1,1);
			release_freezing();	 
		}
	}		
}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../",$permission); ?>
    <form name="dyeingproduction_1" id="dyeingproduction_1" autocomplete="off" >
        <div style="width:1300px; float:left;">   
        <fieldset style="width:1250px;">
            <table cellpadding="0" cellspacing="1" width="1250" border="0" align="left" height="auto" id="master_tbl">
            	<tr>
            		<td width="29%" valign="top">
                    <fieldset>
                    <legend>Input Area</legend>  
           			<table width="130px" cellpadding="0" cellspacing="2" align="right"  >
                        <tr>
                            <td align="center" width="130" class="must_entry_caption"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Load/Un-load</b></td>
                                <td  style="float:left" width="130">
                                <?   
                                echo create_drop_down( "cbo_load_unload", 130, $loading_unloading,'', '1', '---Select---', '',"load_list_view(this.value)",'','','','','',1); 
                                ?>                
                             </td>
                        </tr>
                    </table>
            		<div style="width:auto; float:left; min-height:40px; margin:auto" align="center" id="load_unload_container"></div> 
            		</fieldset>
            		</td>
            		<td width="1%" valign="top">&nbsp;</td>
           			<td width="70%" valign="top">
                        <table cellpadding="0" cellspacing="1" width="100%" border="0" align="left">
                            <tr>
                            	<td colspan="3"> <center> <legend>Reference Display</legend></center> </td>
                            </tr>
							<tr>
                                    <td colspan="3" align="left"><strong>Functional Batch No :</strong> &nbsp;<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes_numeric" onDblClick="open_syspopup()" style="width:100px;" placeholder="Double Click To Browse" /> </td>
                             </tr>
                            <tr>
                            	<td width="45%" valign="top">
                            	<fieldset style="height:auto;">
                            	<table width="400" align="left" id="tbl_body1" >
                            		<tr>
                           				<td width="70">Batch ID</td>
                            				<td width="110">
                            					<input type="text" name="txt_batch_ID" id="txt_batch_ID" class="text_boxes" style="width:100px;" readonly />
                            				</td>
                            				<td width="90">Loading Date</td>
                            				<td width="110">
                            					<input type="text" name="txt_dying_started" id="txt_dying_started" class="text_boxes" style="width:100px;" readonly />
                            				</td>
                            			</tr>
                            			<tr>
                            				<td>Ext. No.</td>
                            				<td>
                            					<input type="text" name="txt_ext_id" id="txt_ext_id" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                            <td>Loading Time</td>
                                            <td>
                                            	<input type="text" name="txt_dying_end_load" id="txt_dying_end_load" class="text_boxes" style="width:100px;" readonly  />
                                            </td>
                            			</tr>
                                        <tr>
                                            <td>Job No</td>
                                            <td>
                                            	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                            <td >M/C Floor</td>
                                            <td id="machine_fg_td">
                                            	<input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td >Buyer</td>
                                            <td>
                                            	<input type="text" name="txt_buyer" id="txt_buyer" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                            <td >M/C Group</td>
                                            <td id="">
                                            	<input type="text" name="txt_mc_group" id="txt_mc_group" class="text_boxes" style="width:100px;" value="<? echo $data;?>" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Order No.</td>
                                            <td>
                                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                            <td>Color</td>
                                            <td>
                                                <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:100px;"  readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>BTB/LTB</td>
                                            <td>
                                                <input type="text" name="txt_ltb_btb" id="txt_ltb_btb" class="text_boxes" style="width:100px;" readonly />
                                            </td>
                                            <td>Trims Wgt:</td>
                                            <td>
                                            <input type="text" name="txt_trim_wgt" id="txt_trim_wgt" class="text_boxes" placeholder="Display" style="width:95px;"  readonly /> 
                                           </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"> <div id="batch_type" style="color:#F00"> </div> </td>
                                        </tr>
                                    </table>
            						</fieldset>
            						</td>
            						<td width="1%" valign="top">&nbsp;</td>
            						<td width="54%" valign="top">
                                    <fieldset>
                                    <table width="540" align="right" class="rpt_table" rules="all">
                                        <thead>
                                            <th>Const/Composition</th> 
                                            <th>GSM</th>
                                            <th>Dia/Width</th> 
                                            <th>D/W Type</th>
                                            <th>Roll</th>
                                            <th>Batch Qty</th>
                                            <th>Lot</th>
                                            <th>Yarn Count</th>
                                            <th>Brand</th>
                                        </thead>
                                        <tbody id="list_fabric_desc_container">
                                            <tr class="general" id="row_1">
                                                <td><input type="text" name="txt_cons_comp_1" id="txt_cons_comp_1" class="text_boxes" style="width:170px;" disabled /></td>
                                                <td><input type="text" name="txt_gsm_1" id="txt_gsm_1" class="text_boxes" style="width:40px;" disabled /> </td>
                                                <td><input type="text" name="txt_body_part_1" id="txt_body_part_1" class="text_boxes" style="width:40px;"  disabled/></td>
                                                <td><input type="text" name="txt_dia_width_1" id="txt_dia_width_1" class="text_boxes" style="width:70px;" disabled/></td>
                                                <td><input type="text" name="txtroll_1" id="txtroll_1" class="text_boxes" style="width:40px;"  disabled/></td>
                                                <td><input type="text" name="txt_batch_qnty_1" id="txt_batch_qnty_1" class="text_boxes" style="width:60px;" disabled/></td>
                                                <td><input type="text" name="txtlot_1" id="txtlot_1" class="text_boxes_numeric" style="width:40px;" disabled /></td>
                                                <td><input type="text" name="txtyarncount_1" id="txtyarncount_1" class="text_boxes_numeric" style="width:60px;" disabled /></td>
                                                <td><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes_numeric" style="width:60px;" disabled /> </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
                                                <td align="right"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </fieldset>
            						</td>
            					</tr>
            				</table>
            			</td>
            		</tr>
            		<tr>
                        <td colspan="3">
                        <fieldset style="width:600px;">
                            <table style="width:600">
                                <tr>
                                    <td width="100">Remarks:</td>
                                    <td>
                                    	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:600px;"    />
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                        </td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" colspan="4" class="button_container">
                        <?
                        	echo load_submit_buttons($permission, "fnc_pro_fab_subprocess", 0,0,"fnResetForm()",1);
                        ?>
                        </td>
            		</tr>
            	</table>
        	</fieldset>
        </div>
    </form>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>