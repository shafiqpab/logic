<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Stationary Work Order Approval
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	17-11-2013
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
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
//---------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Purchase Requisition Approval", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	
	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var page_link = page_link + "&quo_id="+quo_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			if (cause!="")
			{
				fn_report_generated();
			}
		}
	 }
	
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
		// if (form_validation('cbo_company_name','Comapny Name')==false)
		// {
		// 	release_freezing();
		// 	return;
		// }
		// $('#txt_alter_user_id').val();
		var alter_user=$('#txt_alter_user_id').val();
		
		if(alter_user){
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_approval_type*txt_wo_no*txt_date_from*txt_date_to*txt_alter_user_id*cbo_supplier_id',"../");
		}else{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_approval_type*txt_wo_no*txt_date_from*txt_date_to*cbo_supplier_id',"../");
		}
		
		http.open("POST","requires/stationary_work_order_approval_controller.php",true);
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
			
			var tableFilters = { col_0: "none"}//,col_3: "select", display_all_text: " --- All Category ---" }
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
		
	/*function submit_approved(total_tr,type) // OLD
	{ 
		//var operation=4;
		var req_nos = "";
		
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Work Order No");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Work Order No");
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
				first_confirmation=confirm("Are You Want to Approved All Work Order No");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Work Order No");
					if(second_confirmation==false)
					{
						return;					
					}
				}
			}
			
		}
		// Confirm Message End ***************************************************************************************************
		
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				req_id = $('#req_id_'+i).val();
				if(req_nos=="") req_nos= req_id; else req_nos +=','+req_id;
			}

		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&req_nos='+req_nos+get_submitted_data_string('cbo_company_name',"../"); //alert(data);
	
		freeze_window(operation);
		
		http.open("POST","requires/stationary_work_order_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}*/	

	function submit_approved(total_tr,type) //New AS PER PURCHASE REQ
	{ 
		var req_nos = "";var approval_ids = ""; 
		freeze_window(0);
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Work Order No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Work Order No");
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
				first_confirmation=confirm("Are You Want to Approved All Work Order No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Work Order No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End ***************************************************************************************************
		
		var mst_id_company_ids='';
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				req_id = $('#req_id_'+i).val();
				if(req_nos=="") req_nos= req_id; else req_nos +=','+req_id;

				requisition_id = $('#requisition_id_'+i).val();
				if(approval_ids=="") approval_ids= requisition_id; else approval_ids +=','+requisition_id;

				var mst_id_company_id = $('#mst_id_company_id_'+i).val();
				if (mst_id_company_ids=="") mst_id_company_ids = mst_id_company_id;
				else mst_id_company_ids +=','+mst_id_company_id;
			}
		}

		if($('#txt_alter_user_id').val()){
		var data="action=approve&operation="+operation+'&approval_type='+type+'&req_nos='+req_nos+'&approval_ids='+approval_ids+'&mst_id_company_ids='+mst_id_company_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../"); 
		}else{
			var data="action=approve&operation="+operation+'&approval_type='+type+'&req_nos='+req_nos+'&approval_ids='+approval_ids+'&mst_id_company_ids='+mst_id_company_ids+get_submitted_data_string('cbo_company_name',"../"); 
		}
		// alert(data);	
		
		http.open("POST","requires/stationary_work_order_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
		
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
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

	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	
	function generate_worder_report(company_name,wo_number,item_category,supplier_id,wo_date,currency_id,wo_basis_id,pay_mode,source,delivery_date,attention,requisition_no,delivery_place,id,location_id,payterm_id,remarks,contact,tenor,sign_temp_id,format_ids,action_type)
	{
		// alert(company_name+'='+wo_number+'='+supplier_id+'='+wo_date+'='+currency_id+'='+wo_basis_id+'='+pay_mode+'='+source+'='+delivery_date+'='+attention+'='+requisition_no+'='+delivery_place+'='+id+'='+location_id+'='+format_ids+'='+action_type);

		var report_title
		if(format_ids==66){
			report_title='Stationary Purchase Order';
		}else{
			report_title='Stationary Purchase Order';
		}
		// action="+action_type
		if(format_ids==137)
		{
			var data=company_name+'*'+wo_number+'*'+item_category+'*'+supplier_id+'*'+wo_date+'*'+currency_id+'*'+wo_basis_id+'*'+pay_mode+'*'+source+'*'+delivery_date+'*'+attention+'*'+requisition_no+'*'+''+'*'+''+'*'+delivery_place+'*'+id+'*'+report_title+'*'+location_id+'*'+payterm_id+'*'+remarks+'*'+contact+'*'+tenor+'*'+sign_temp_id;
		}
		else if(format_ids==430)
		{
			print_report( company_name+'*'+id+'*'+report_title+'*'+requisition_no+'*1', "stationary_work_order_po_print2", "../commercial/work_order/requires/stationary_work_order_controller" );
			return;
		}
		else
		{
			var data=company_name+'*'+wo_number+'*'+item_category+'*'+supplier_id+'*'+wo_date+'*'+currency_id+'*'+wo_basis_id+'*'+pay_mode+'*'+source+'*'+delivery_date+'*'+attention+'*'+requisition_no+'*'+''+'*'+''+'*'+delivery_place+'*'+id+'*'+report_title+'*'+location_id+'*'+sign_temp_id+'*'+format_ids;
		}

		// alert(data);
		// alert(format_ids);
		print_report( $('#txt_req_numbers').val()+'*'+$('#txt_req_numbers_id').val()+'*'+$('#txt_delete_row').val()+'*'+$('#txt_delivery_place').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$("#cbo_location").val()+'*'+$("#cbo_payterm_id").val()+'*'+$("#txt_remarks_mst").val()+'*'+$("#txt_contact").val()+'*'+$("#txt_tenor").val()+'*'+$("#cbo_template_id").val(), "stationary_work_order_print4", "requires/stationary_work_order_controller" ) ;
			
		freeze_window(5);

		if(format_ids==134)
		{
			http.open("POST","../commercial/work_order/requires/stationary_work_order_controller.php",true);
		}
		else if(format_ids==66)
		{
			http.open("POST","../commercial/work_order/requires/stationary_work_order_controller.php",true);
		}			
		else
		{
			http.open("POST","../commercial/work_order/requires/stationary_work_order_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				window.open("../commercial/work_order/requires/stationary_work_order_controller.php?action="+action_type+'&data='+data, "_blank");
				/*var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();*/
				release_freezing();
		   }	
		}
	}	


	function change_user()
	{
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}		
		
		var title = 'Approval User Info';	
		var page_link = 'requires/stationary_work_order_approval_controller.php?action=user_popup&company_id='+$("#cbo_company_name").val();		  
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
	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:650px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:650px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="2">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                <th colspan="2"></th>
                                <th colspan="3">
                                   <?
                                   $user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{?>
                                    Alter User: 
                                    <input id="txt_alter_user" name="txt_alter_user" type="text" onDblClick="change_user();" class="text_boxes" style="width:150px" placeholder="Browse">
                                    <? } ?>
                                     <input id="txt_alter_user_id" name="txt_alter_user_id" type="hidden">
                                </th>
                            </tr>
                            <tr>
                                <th width="160" >Company Name</th>
								<th width="150">Supplier</th>
                                <th width="100">WO No</th>
                                <th width="130" colspan="2">WO Date Range</th>
                                <th width="140">Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?=create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/stationary_work_order_approval_controller',this.value, 'load_supplier_dropdown', 'supplier_td_id' );" ); ?></td>
								<td id="supplier_td_id"> 
									<?
                                       echo create_drop_down( "cbo_supplier_id", 152, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                                    ?>
                                </td>
								<td><input type="text" name="txt_wo_no"  id="txt_wo_no"  style="width:90px" class="text_boxes" placeholder="WO No" /></td>
                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From Date"/></td>					
                        		<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" placeholder="To Date" /></td>
                                <td><?=create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" ); ?></td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated();"/></td>                	
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
				$("#cbo_approval_type").val(0);			
		});
    </script>  
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>