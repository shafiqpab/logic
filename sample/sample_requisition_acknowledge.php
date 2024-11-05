<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yern Requisition Approval
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	01-02-2017
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$acknowledge_type_arr=array(0=>"Un-Acknowledge",1=>"Acknowledge"); 
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Requisition Acknowledge", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["sample_qty"],
					   col: [11],
					   operation: ["sum"],
					   write_method: ["innerHTML"]
					},
						
				 }

	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
 		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_approval_type*cbo_buyer_name*cbo_season_name*txt_style_ref*txt_requisition_no*txt_st_date*txt_end_date*cbo_brand_id',"../");
 		freeze_window(3);
		http.open("POST","requires/sample_requisition_acknowledge_controller.php",true);
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
			// var tableFilters = { col_12: "none"}//,col_3: "select", display_all_text: " --- All Category ---" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
		
	}
	function generate_fabric_booking_report(type,txt_booking_no,cbo_company_name,id_approved_id,cbo_fabric_natu,hidden_requisition_id)
	{ 	

				$report_title='Sample Requisition Fabric Booking -Without order';
					var show_comment = '';
					var r = confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
					if (r == true) {
						show_comment = "1";
					}
					else {
						show_comment = "0";
					}
			
					freeze_window(5);
					var data='action='+type+'&txt_booking_no='+"'"+txt_booking_no+"'"+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&cbo_fabric_natu='+cbo_fabric_natu+'&hidden_requisition_id='+hidden_requisition_id+'&report_title='+$report_title+show_comment;
					http.open("POST","../order/woven_order/requires/sample_requisition_booking_non_order_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fnc_generate_report_reponse;
		
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
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
		var operation=4; var req_nos = "";
		
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnAcknowledged All Requisition No");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnAcknowledged All Requisition No");
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
				first_confirmation=confirm("Are You Want to Acknowledged All Requisition No");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Acknowledged All Requisition No");
					if(second_confirmation==false)
					{
						return;					
					}
				}
			}
			
		}
		// Confirm Message End ***************************************************************************************************
		var confirm_delivery_end_dates=""; var z=1; var data_all="";
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				if(type==1){
					req_id = $('#requisition_id_'+i).val();
				}
				else{
					req_id = $('#req_id_'+i).val();
				}
				
				if(req_nos=="") req_nos= req_id; else req_nos +=','+req_id;

				var confirm_del_end_date = $('#txt_confirm_del_end_date_'+i).val();
				//alert(confirm_del_end_date);return;
				if(type !=1 && confirm_del_end_date=="")
				{
					if (form_validation('txt_confirm_del_end_date_'+i,'Confirm Delivery End Date')==false)
					{
						return;
					}
				}
				if(confirm_delivery_end_dates =="" ) var confirm_delivery_end_dates = confirm_del_end_date+"_"+req_id; 
				else confirm_delivery_end_dates +=','+confirm_del_end_date+"_"+req_id;

				data_all+="&sampleplan_" + z + "='" + $('#sampleplandata_'+req_id).val()+'_'+req_id+"'";
				z++;
			}

		}
		//alert(confirm_delivery_end_dates);return;
		var data="action=approve&operation="+operation+'&approval_type='+type+'&req_nos='+req_nos+'&confirm_del_end_date='+confirm_delivery_end_dates+data_all+'&total_tr='+total_tr;
		freeze_window(operation);
		http.open("POST","requires/sample_requisition_acknowledge_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[2])
			{
				alert('Ready to approved found No in requsition no : '+reponse[2]);
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='rec1'){
				alert("Receive  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			show_msg(reponse[0]);

			if((reponse[0]==32 || reponse[0]==33))
			{
				fnc_remove_tr();
			}
			
			release_freezing();	
		}
	}
	
	function fnc_remove_tr()
	{	var req_id_arr=Array();
		var tot_row=$('#tbl_list_search tbody tr').length;
		for(var i=1;i<=tot_row;i++)
		{
			if($('#tbl_'+i).is(':checked'))
			{
				req_id_arr.push($('#requisition_id_'+i).val());
				$('#tr_'+i).remove();
			}
		}
		
		sendMail(req_id_arr.join(','));
	}
	
	
	function sendMail(req_id_str)
	{
		var operation=4;
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_approval_type=$('#cbo_approval_type').val();
		var data="action=sample_requisition_print&company_id="+cbo_company_name+"&approval_type="+cbo_approval_type+"&req_id="+req_id_str;
		//alert(data);
		
 		//freeze_window(operation); //off for error showing
		http.open("POST","../auto_mail/woven/sample_requisition_acknowledge_auto_mail_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fnc_btb_mst_reponse()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText);
				//alert(reponse);
				//release_freezing(); //off for error showing
			}
		}

	}	
	function openmypage_checklist(chk_req,action,type)
	{
  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sample_requisition_acknowledge_controller.php?chk_req='+chk_req+'&action='+action+'&type='+type, ' Checklist', 'width=370px,height=250px,center=1,resize=0,scrolling=0','');
	}
	
	
	
	
	
</script>
</head>
<body>
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",''); ?>
		<form name="requisitionApproval_1" id="requisitionApproval_1"> 
         	<h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         	<div id="content_search_panel">      
             	<fieldset style="width:1000px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
							<th class="must_entry_caption">Company Name</th>
							<th>Buyer</th>
							<th>Brand</th>
							<th width="100">Season</th>
							<th width="70">Style Ref</th>
							<th width="70">Req. No</th>
							<th>Delv St Date</th>
							<th>Delv End Date</th>
							<th>Acknowledge Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                           	<tr class="general">
								<td><? echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_requisition_acknowledge_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id');" ); ?></td>
								<td id="buyer_td_id"><? echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" ); ?></td>
								<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 110, $blank_array,'', 1, "--Brand--",$selected, "" ); ?>  
								<td id="season_td"><? echo create_drop_down( "cbo_season_name", 100,$blank_array ,"", 1, "-- Select Season --", $selected, "" ); ?></td>
								<td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:65px"></td>
								<td><input name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:65px"></td>
								<td><input type="text" name="txt_st_date" id="txt_st_date" class="datepicker" readonly style="width:80px"/></td>
								<td><input type="text" name="txt_end_date" id="txt_end_date" class="datepicker" readonly style="width:80px"/></td>
								<td><? echo create_drop_down( "cbo_approval_type", 140, $acknowledge_type_arr,"", 1, "", $selected,"","", "" ); ?></td>
								<td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                           	</tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <script type="text/javascript">
 		setInterval(function () {document.getElementById("show").click();}, 600000);
 
    </script>
    <script src="../includes/functions_bottom.js" type="text/javascript"></script>
</body>
</html>