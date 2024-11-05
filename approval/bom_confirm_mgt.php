<?

/*-------------------------------------------- Comments
Purpose			: 	This form will create Bom Confirmation Before Approval
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	02-12-2019
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
echo load_html_head_contents("Bom Confirmation Before Approval", "../", 1, 1,'','','');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		freeze_window(3);
		
		/*if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}*/
		
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_job_no*cbo_year',"../");
		
		http.open("POST","requires/bom_confirm_mgt_controller.php",true);
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
		var cause=document.getElementById("txtCause_"+quo_id).value;
		
		var page_link = page_link + "&quo_id="+quo_id + "&cause="+cause;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			document.getElementById("txtCause_"+quo_id).value=cause;
			/*if (cause!="")
			{
				fn_report_generated();
			}*/
		}
	}

	function fn_report_generated1()
	{
		freeze_window(3);
		
		/*if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}*/
		
		
		var data="action=report_generate_2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_internal_ref*txt_file_no*cbo_get_upto*txt_date*cbo_approval_type*txt_job_no*cbo_year',"../");
		
		http.open("POST","requires/bom_confirm_mgt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse1;
	}
	
	function fn_report_generated_reponse1()
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
	
	function check_on_scan(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var tbl_len=$("#tbl_list_search tbody tr").length;
		$('#tbl_'+row_no).attr('checked', true);
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
	var cause_arr=Array();	
	function submit_approved(total_tr,type)
	{ 
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = "";  var hidddtlsdatas = "";
		freeze_window(0);
		// confirm message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Confirm All Job");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Job");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// confirm message finish ***************************************************************************************************
		var unappv_cause_arr=Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{  
				 var unappv_cause = $('#txtCause_'+i).val();
				if(unappv_cause!=''){unappv_cause_arr.push(unappv_cause);}

				var cm_cost_id = $('#tdCm_'+i).text()*1;
				var cm_compulsory=$('#txt_cm_compulsory').val()*1;
					
				/*if($('#cbo_approval_type').val()==2)
				{
					if(cm_compulsory==1)
					{
						//alert('MM');
						if(cm_cost_id<0 || cm_cost_id==0)
						{
							alert('Without CM Cost Approving not allowed');
							release_freezing();	
							return;
						}
					}
				}*/
				
				var booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				var hidddtlsdata = $('#hidddtlsdata_'+i).val();
				if(hidddtlsdatas=="") { hidddtlsdatas= hidddtlsdata; }
				else{ hidddtlsdatas +=','+hidddtlsdata; }
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}

		cause_arr.push(unappv_cause_arr);
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Job");
			release_freezing();
			return;
		}

		if(type==5 && unappv_cause_arr.length==0)
		{
			alert("Deny is not allowed without Refusing cause");
			release_freezing();
			return;
		}

		// $('#txt_selected_id').val(booking_ids);
		// fnSendMail('../','',1,0,0,1,type);

		$('#txt_selected_id').val(booking_ids);
		fnSendMail('../', '', 1, 0, 0, 1, type, $('#cbo_company_name').val()+'_141_1');
		
		var data="action=save_update_delete&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&hidddtlsdatas='+hidddtlsdatas+get_submitted_data_string('cbo_company_name',"../");
	   //alert(data);
		
		http.open("POST","requires/bom_confirm_mgt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_confirm_approval_Reply_info;
	}	
	
	function fnc_confirm_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==50))
			{
				fnc_remove_tr();
			}
			/*if(reponse[0]==25)
			{
				fnc_remove_tr();
				alert("You Have No Authority To Approved this.");
			}*/		
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
	
	function generate_worder_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id)
	{
		$("#txt_style_ref").val(style_ref);
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		if(type=="summary" || type=="budget3_details")
		{
			if(type=='summary')
			{
				var rpt_type=3;var comments_head=0;
			}
			else if(type=='budget3_details')
			{
				var rpt_type=4;var comments_head=1;
			}
			
			var report_title="Budget/Cost Sheet";
			//var comments_head=0;
			var txt_style_ref_id='';
			var sign=0;

			var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
			var data="action="+type+
			'&reporttype='+"'"+rpt_type+"'"+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref='+"'"+style_ref+"'"+
			'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
			'&txt_order='+"'"+txt_order+"'"+
			'&txt_order_id='+"'"+txt_order_id+"'"+
			'&txt_season='+"'"+txt_season+"'"+
			'&sign='+"'"+sign+"'"+
			'&txt_season_id='+"'"+txt_season_id+"'"+
			'&txt_file_no='+"'"+txt_file_no+"'"+
			'&txt_quotation_id='+quotation_id+
			'&txt_hidden_quot_id='+quotation_id+
			'&comments_head='+"'"+comments_head+"'"+
			'&report_title='+"'"+report_title+"'"+
			'&path=../../../';
		//	alert(data)
			http.open("POST","../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}
		else
		{
			if(entry_form==425)
			{
				var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&path=../';
					//'&txt_style_ref='+"'"+style_ref+"'"+
					'&txt_costing_date='+"'"+txt_costing_date+"'"+get_submitted_data_string('txt_style_ref',"../../");
					//alert(data)
					http.open("POST","../order/woven_gmts/requires/pre_cost_entry_report_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
			}
			else
			{
				var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					//'&txt_style_ref='+"'"+style_ref+"'"+
					'&path=../';
					'&txt_costing_date='+"'"+txt_costing_date+"'"+get_submitted_data_string('txt_style_ref',"../../");
					//alert(data)
					http.open("POST","../order/woven_order/requires/pre_cost_entry_report_controller_v2",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
			}
		}
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
	
	function openImgFile(id,action)
	{
		var page_link='requires/bom_confirm_mgt_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}

	function call_print_button_for_mail(mail,mail_body,type)
	{
		/// alert(cause_arr);return;
		//var alterUserID = $('#txt_alter_user_id').val();$company_name
		var booking_id = $('#txt_selected_id').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var sysIdArr = booking_id.split(',');
		var mail = mail.split(',');
		var ret_data = return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*')+'__'+cbo_company_name+'__'+type+'__'+mail_body+'__'+cause_arr, 'bom_approval_mail', '', 'requires/bom_confirm_mgt_controller');
		comment_empty();
		//alert(ret_data);
		//var cause_arr=Array();
		// if(type==5){//type ==5 deny
		// 	var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*')+'__'+type+'__'+$('#txt_alter_user_id').val(), 'deny_pi_approval_mail', '', '../auto_mail/approval/pi_approval_new_auto_mail');
		// }
		// else{
		// 	var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*')+'__'+type+'__'+$('#txt_alter_user_id').val(), 'pi_approval_mail', '', '../auto_mail/approval/pi_approval_new_auto_mail');	
		// }
	}

	function comment_empty(){
	   cause_arr = [];
	//    console.log(cause_arr);
	}

	function generat_print_report(type,company_name,buyer_name,date_from,date_to,job_no,job_id,order_id,order_no,year,order_status,search_date,season,season_id,file_no,internal_ref)
	{
		var data="action=report_generate"+
			'&reporttype='+type+
			'&cbo_company_name='+"'"+company_name+"'"+
			'&cbo_buyer_name='+"'"+buyer_name+"'"+
			'&txt_date_from='+"'"+date_from+"'"+
			'&txt_date_to='+"'"+date_to+"'"+
			'&txt_job_no='+"'"+job_no+"'"+
			'&txt_job_id='+"'"+job_id+"'"+
			'&txt_order_id='+"'"+order_id+"'"+
			'&txt_order_no='+"'"+order_no+"'"+
			'&cbo_year='+"'"+year+"'"+
			'&cbo_order_status='+"'"+order_status+"'"+
			'&cbo_search_date='+"'"+search_date+"'"+
			'&txt_season='+"'"+season+"'"+
			'&txt_season_id='+"'"+season_id+"'"+
			'&txt_file_no='+"'"+file_no+"'"+
			'&txt_internal_ref='+"'"+internal_ref+"'";
					
		freeze_window(3);
		if(type==1 || type==2 || type==3 || type==4 || type==7)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report_controller.php",true);
		}
		else if (type==5 || type==6)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report2_controller.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generat_print_report_reponse;
	}
	
	function generat_print_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]); 	
			
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'<link rel="stylesheet" href="../css/style_common.css" type="text/css" /></body</html>');//
			d.close();
			
			$('#report_container2').html(''); 	
			release_freezing();
			show_msg('3');
		}
	}
	function report_button_setting(report_ids) 
	{
		$("#show").hide(); 
		$("#show_button2").hide(); 
		var report_id= report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==178) $("#show").show(); 
			if(report_id[k]==195) $("#show_button2").show(); 
		}
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1">
         <h3 style="width:1211px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1200px;">
             	<input type="hidden" name="txt_style_ref" id="txt_style_ref">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th width="150">Company Name</th>
                                <th width="150">Buyer</th>
                                <th width="70">Year</th>
                                <th width="75">Job No.</th>
                                <th width="75">Internal Ref.</th>
                   				<th width="75">File No</th>
                                <th width="120">Get Upto</th>
                                <th width="80">Costing  Date</th>
                                <th width="120">Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:80px" /> <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- All --", $selected, "load_drop_down( 'requires/bom_confirm_mgt_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );get_php_form_data(this.value,'button_variable_setting','requires/bom_confirm_mgt_controller');" ); ?></td>
                                <td id="buyer_td_id"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", 0, "" ); ?></td>
                                <td> <? echo create_drop_down( "cbo_year", 70, $year,"", 1, "-- Select --", 0, "" ); ?></td>
                                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:65px"></td>
                                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:65px"></td>
                      			<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:65px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                        echo create_drop_down( "cbo_get_upto", 120, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:70px"/></td>
                                <td> 
                                    <?
									  	$pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                        echo create_drop_down( "cbo_approval_type", 120, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:80px;display:none" onClick="fn_report_generated()"/>

								<input type="button" id="show_button2" class="formbutton" style="width:70px;display:none" value="Show 2" onClick="fn_report_generated1();"title="As per RG formula" />
								
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
    <div id="report_container2" align="center"></div>
    
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>