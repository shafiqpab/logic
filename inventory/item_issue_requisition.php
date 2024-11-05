<?
/*-------------------------------------------- Comments
Version          : 
Purpose			 : 
Functionality	 :	
JS Functions	 :
Created by		 : Monir Hossain
Creation date 	 : 20/07/2016
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//====user credentials===
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
$cred_company_id = $userCredential[0][csf('company_id')];
if ($cred_company_id != '') {$scred_company_id_cond = "and comp.id in($cred_company_id)";} else { $scred_company_id_cond = "";}

echo load_html_head_contents("Item Issue Requisition", "../", 1, 1,'','1','');
$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");

?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function fnc_item_issue_requisition_mst(operation)
{
	var txt_is_approved=$('#txt_is_approved').val();	
	if(operation==4)
	{ 
		 print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_indent_no').val()+'*'+$('#cbo_store_name').val()+'*'+$('#txt_is_approved').val()+'&template_id='+$('#cbo_template_id').val(), "print_item_issue_requisition", "requires/item_issue_requisition_controller" ) ;
		 return;
	}

	if(txt_is_approved  == 1 || txt_is_approved  == 3 )
	{
		alert('This Requisition is Approved. Change Cannot Be Allowed.');
		return;
	}
	

	
	
	if( form_validation('cbo_company_id*txt_indent_date*txt_required_date*cbo_location_name*cbo_store_name','Company*indent Date*Required Date*Location name*Store name')==false )
	{
		return;
	}
	else
	{
		
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][154]);?>'){
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][154]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][154]);?>')==false)
			{
				
				return;
			}
		}			
		
		var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('cbo_company_id*txt_indent_date*txt_required_date*cbo_delivery_point*txt_remarks*txt_manual_requisition_no*cbo_location_name*cbo_division_name*cbo_department_name*cbo_section_name*cbo_sub_section_name*txt_system_id*txt_indent_no*cbo_ready_to_approved*cbo_store_name*cbo_sewing_floor_name*cbo_sewing_floor_line*cbo_machine_no',"../");
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/item_issue_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_item_issue_requisition_mst_Reply_info;
	}
}

function fnc_item_issue_requisition_mst_Reply_info()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var response=trim(http.responseText).split('**');
		$('#txt_indent_no').val(response[2]);	
		show_msg(response[0]);
		
		if(response[0]==0 || response[1]==1 )
		{
			$('#txt_system_id').val(response[1]);
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_location_name').attr('disabled','disabled');
			$('#cbo_store_name').attr('disabled','disabled');
			set_button_status(1,permission,'fnc_item_issue_requisition_mst',1);
		}
		if(response[0]==20)
		{
			alert(response[1]);release_freezing();return;
		}
		if(response[0]==2)
		{
			reset_form('itemissuerequisition_1,itemissuerequisition_2','item_issue_listview','','txt_tot_row,0','$(\'#tbl_item_issue_list tbody tr:not(:first)\').remove();','hidden_selectedID')
			
		}

	}
	release_freezing();
}

function fnc_item_issue_requisition_dtls(operation)
{
	var txt_is_approved=$('#txt_is_approved').val();
	//+alert(txt_is_approved);return;
	if(txt_is_approved  == 1 || txt_is_approved  == 3)
	{
		alert('This Requisition is Approved. Change Cannot Be Allowed.');
		return;
	}
	if($('#txt_system_id').val()=='')
	{
		alert('Please,Fill up Requisition Reference Form.');
		if ( form_validation('cbo_company_id','Company')==false )
		{
			return;
		}
	}
	if( form_validation('txt_item_category_1','Item Category')==false )
	{
		alert('Please, Browse Item Account.');
		return;
	}	
	else
	{
		var row_num=$('#tbl_item_issue_list tbody tr').length;
		//alert(row_num);
		var multi_data="";
		//alert(row_num);
		if(operation==0 || operation==1 || operation==2)
		{
			const btn = (operation==0) ? document.getElementById('save2') : document.getElementById('update2');
			btn.disabled=true;  
			for (var j=1; j<=row_num; j++)
			{
				var qty=$('#txt_req_qty_'+j).val();
				//var quantity=confirm("Blank Qty item will not be saved");
				if(qty!='')
				{
					multi_data+="&txt_item_account_" + j + "='" + $('#txt_item_account_'+j).val()+"'"+"&hiddenitemgroupid_" + j + "='" + $('#hiddenitemgroupid_'+j).val()+"'"+"&txt_item_sub_" + j + "='" + $('#txt_item_sub_'+j).val()+"'"+"&txt_item_description_" + j + "='" + $('#txt_item_description_'+j).val()+"'"+"&txt_item_size_" + j + "='" + $('#txt_item_size_'+j).val()+"'"+"&txt_required_for_" + j + "='" + $('#txt_required_for_'+j).val()+"'"+"&hiddentxtuom_" + j + "='" + $('#hiddentxtuom_'+j).val()+"'"+"&txt_req_qty_" + j + "='" + $('#txt_req_qty_'+j).val()+"'"+"&txt_stock_" + j + "='" + $('#txt_stock_'+j).val()+"'"+"&txt_remarks_" + j + "='" + $('#txt_remarks_'+j).val()+"'"+"&txt_product_id_" + j + "='" + $('#txt_product_id_'+j).val()+"'"+"&txt_item_category_" + j + "='" + $('#txt_item_category_'+j).val()+"'"+"&txt_rtn_qty_" + j + "='" + $('#txt_rtn_qty_'+j).val()+"'"+"&txt_machine_category_" + j + "='" + $('#txt_machine_category_'+j).val()+"'"+"&txt_machine_no_" + j + "='" + $('#txt_machine_no_'+j).val()+"'";
				}
				else
				{
					var fieldid='txt_req_qty_'+j;
					//alert(fieldid);
					if ( form_validation(fieldid,'Req. Qty.')==false )
					{
						//$('#txt_req_qty_'+j).focus();
						return;
					}
				}
			}
		}
		//alert(multi_data); return;
		var data="action=save_update_delete_dtls&row_num="+row_num+"&operation="+operation+get_submitted_data_string('txt_system_id*update_id_dtls',"../")+multi_data;
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/item_issue_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_item_issue_requisition_dtls_Reply_info;
	}
}

function fnc_item_issue_requisition_dtls_Reply_info()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var response=trim(http.responseText).split('**');
		show_msg(response[0]);
		
		//show_list_view(response[1],'show_item_issue_listview','item_issue_listview','requires/item_issue_requisition_controller','');
		if(response[0]==0 || response[0]==1 || response[0]==2)
		{
			
			reset_form('itemissuerequisition_2','','','txt_tot_row,0','$(\'#tbl_item_issue_list tbody tr:not(:first)\').remove();','hidden_selectedID');
			show_list_view(response[1],'show_item_issue_listview','item_issue_listview','requires/item_issue_requisition_controller','');
			set_button_status(0,permission,'fnc_item_issue_requisition_dtls',2);
			if (response[0]==1 || response[0]==2) 
			{
				$('#txt_item_account_1').attr('disabled',false);
			}
			const btn = (response[0]==1) ?  document.getElementById('update2') :document.getElementById('save2');
			btn.disabled=false;
		}
		if(response[0]==20)
		{
			const btn = (response[0]==1) ?  document.getElementById('update2') :document.getElementById('save2');
			btn.disabled=false;
			alert(response[1]);release_freezing();return;
		}
		release_freezing();
	}
}


function fnc_items_sys_popup()
{
	// alert("Name");

	var cbo_company_name=$('#cbo_company_id').val();
	// var txt_is_approved=$('#txt_is_approved').val();

	var page_link='requires/item_issue_requisition_controller.php?cbo_company_name='+cbo_company_name+'&action=item_issue_requisition_popup_search';
	var title='Items Issue Requisition'
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=400px,center=1,resize=1,scrolling=0','');
	
	emailwindow.onclose=function()
	{   
		var theform=this.contentDoc.forms[0];
		var hidden_item_issue_id=this.contentDoc.getElementById("hidden_item_issue_id").value;
		// alert(hidden_item_issue_id); return;
		if(trim(hidden_item_issue_id)!="")
		{
			freeze_window(5);
			get_php_form_data(hidden_item_issue_id, "populate_data_from_item_issue_requisition", "requires/item_issue_requisition_controller" );
			show_list_view(hidden_item_issue_id,'show_item_issue_listview','item_issue_listview','requires/item_issue_requisition_controller','');
			get_php_form_data( $('#cbo_company_id').val(),'print_button_variable_setting','requires/item_issue_requisition_controller');
			var txt_is_approved=$('#txt_is_approved').val();
			if(txt_is_approved == 1)
			{
				$('#update1').removeClass('formbutton').addClass('formbutton_disabled');  
				$('#Delete1').removeClass('formbutton').addClass('formbutton_disabled'); 
			}
			else
			{
				$('#update1').removeClass('formbutton_disabled').addClass('formbutton');  
				$('#Delete1').removeClass('formbutton_disabled').addClass('formbutton'); 
			}
			
		}
		release_freezing();
	}

}


function fnc_item_account(row_num)
{
	var sys_id=$('#txt_system_id').val();
	if(sys_id=='')
	{
		alert('Pls,Browse Indent No. From Requisition Reference Form.');
		$('#txt_indent_no').focus();
		return;
	}
	var cbo_company_name=$('#cbo_company_id').val();
	var cbo_store_name=$('#cbo_store_name').val();
	var hidden_variable_setting=$('#hidden_variable_setting').val();
	var row_num=$('#tbl_item_issue_list tbody tr').length;
	var row_num_dtls=$('#item_issue_listview_dtls tbody tr').length;
	var prev_product_id='';
	for (var j=1; j<=row_num; j++)
	{
		var txt_product_id=$('#txt_product_id_'+j).val();
		if(txt_product_id!='' && txt_product_id!=null)
		{
			if(prev_product_id=="") prev_product_id=txt_product_id; else prev_product_id+=","+txt_product_id;
		}
	}
	if(row_num_dtls!=0)
	{
		for (var j=1; j<=row_num_dtls; j++)
		{
			var view_product_id=$('#view_product_id_'+j).val();
			if(view_product_id!='' && view_product_id!=null)
			{
				if(prev_product_id=="") prev_product_id=view_product_id; else prev_product_id+=","+view_product_id;
			}
		}
	}
	// alert(prev_product_id);return;
	var page_link='requires/item_issue_requisition_controller.php?cbo_company_name='+cbo_company_name+'&cbo_store_name='+cbo_store_name+'&hidden_variable_setting='+hidden_variable_setting+'&prev_product_id='+prev_product_id+'&action=item_account_popup';
	var title='Search Item Account';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=420px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{	
		var theform=this.contentDoc.forms[0];
		var item_issue_id=this.contentDoc.getElementById("txt_selected_id").value; 
		if(item_issue_id!="")
		{  	
			var pre_selectID = $("#hidden_selectedID").val();
			if(trim(pre_selectID)=="") $("#hidden_selectedID").val(item_issue_id);else $("#hidden_selectedID").val(pre_selectID+","+item_issue_id); 
			var tot_row=$('#txt_tot_row').val();
			//alert(tot_row);
			var data=item_issue_id+"**"+tot_row+"**"+cbo_store_name+"**"+cbo_company_name;
			//alert(data);
			var list_view_orders = return_global_ajax_value( data, 'item_issue_requisition_list', '', 'requires/item_issue_requisition_controller');				 
			var item_account=$('#txt_item_account_'+row_num).val();
			var txt_item_description=$('#txt_item_description_'+row_num).val();
			var txt_item_category=$('#txt_item_category_'+row_num).val();
			//alert(row_num+"Row check");
			var numRow = $('table#tbl_item_issue_list tbody tr').length;
			if(item_account=="" && numRow==1 && txt_item_description=="" && txt_item_category==0)
			{
				$("#tr_"+row_num).remove();
				numRow=0;
			}
			$("#tbl_item_issue_list tbody:last").append(list_view_orders);	
			numRow = $('table#tbl_item_issue_list tbody tr').length;
			//alert(numRow); 
			$('#txt_tot_row').val(numRow);
			set_all_onclick();
		}
	}
}

 /*function fnc_sub_section()
 {

		 $('#cbo_sub_section_name').css('display','none');
 }*/


function populate_row_dte()
{
	$('#tbl_item_issue_list tbody tr:not(:first)').remove();
}

function trans_history_popup(product_id)
{
	//alert (product_id);
	var cbo_company_name=$('#cbo_company_id').val();
	var page_link='requires/item_issue_requisition_controller.php?action=stock_popup&product_id='+product_id+'&cbo_company_name='+cbo_company_name;
	var title='Tansaction Stock History';
	
	 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=200px,center=1,resize=1,scrolling=0','');
}

function chk_item_issue_requisition(company)
{
  load_drop_down( 'requires/item_issue_requisition_controller',company, 'load_drop_down_store_company', 'store_td' );
}

function fnc_print(type)
{
	if (form_validation('txt_system_id','Save Data First')==false)
	{
		alert("Save Data First");
		return;
	}
	else
	{
		if(type==1)
		{ 
			
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_indent_no').val()+'*'+$('#cbo_store_name').val()+'*'+$('#txt_is_approved').val()+'*'+$('#cbo_location_name').val()+'&template_id='+$('#cbo_template_id').val(), "print_item_issue_requisition", "requires/item_issue_requisition_controller" ) ;
			return;
		}
		if(type==2)
		{ 
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_indent_no').val()+'*'+$('#cbo_store_name').val()+'*'+$('#txt_is_approved').val()+'*'+$('#cbo_location_name').val()+'&template_id='+$('#cbo_template_id').val(), "print_item_issue_requisition_print2", "requires/item_issue_requisition_controller" ) ;
			return;
		}
		if(type==3)
		{ 
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_indent_no').val()+'*'+$('#cbo_store_name').val()+'*'+$('#txt_is_approved').val()+'&template_id='+$('#cbo_template_id').val(), "print_item_issue_requisition_print3", "requires/item_issue_requisition_controller" ) ;
			return;
		}
		if(type==4)
		{ 
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_indent_no').val()+'*'+$('#cbo_store_name').val()+'*'+$('#txt_is_approved').val()+'&template_id='+$('#cbo_template_id').val(), "print_item_issue_requisition_print4", "requires/item_issue_requisition_controller" ) ;
			return;
		}
		if(type==5)
		{ 
			print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_indent_no').val()+'*'+$('#cbo_store_name').val()+'*'+$('#txt_is_approved').val()+'&template_id='+$('#cbo_template_id').val(), "print_item_issue_requisition_print5", "requires/item_issue_requisition_controller" ) ;
			return;
		}
	}

}

function check_variable_setting(company_id)
{
	var response=return_global_ajax_value(company_id, 'company_variable_setting_check', '', 'requires/item_issue_requisition_controller');	
	if (response == 1){
		$('#hidden_variable_setting').val(response);
	}
}



function openmypage_not_approve_cause()
	{
		if (form_validation('txt_indent_no','Req. Number')==false)
		{
			return;
		}
		
		var txt_not_approve_cause=document.getElementById('txt_not_approve_cause').value;
		
		var data=txt_not_approve_cause;
		
		var title = 'Not Appv. Cause';	
		var page_link = 'requires/item_issue_requisition_controller.php?data='+data+'&action=not_approve_cause_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<? echo load_freeze_divs ("../",$permission); ?>
<div align="center">
  	<fieldset style=" width:815px">
	<form  name="itemissuerequisition_1" id="itemissuerequisition_1" >
		<legend>Requisition Reference</legend>
		<table  align="center" cellspacing="3" border="0" cellpadding="5">
			<tr>
            	<td align="right" colspan="3">Indent No</td><td>
                <input type="text" name="txt_indent_no" id="txt_indent_no" class="text_boxes" style="width:132px" placeholder="Browse"  onDblClick="fnc_items_sys_popup()" readonly>
                <input type="hidden" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:132px" placeholder="Hidden id" readonly>
                </td>
           </tr>
           <tr>
				<td align="right" class="must_entry_caption">Company</td>
				<td> 
					<? 
						$company="select comp.id,comp.company_name from lib_company comp where   comp.status_active=1 and comp.is_deleted=0 $company_cond $scred_company_id_cond order  by company_name";
						echo create_drop_down("cbo_company_id",144,$company,"id,company_name",1,"--select--",0,"get_php_form_data( this.value,'print_button_variable_setting','requires/item_issue_requisition_controller');load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_location','location_td');load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_division','division_td');chk_item_issue_requisition(this.value);check_variable_setting(this.value);load_drop_down( 'requires/item_issue_requisition_controller', this.value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor','sewing_td');load_drop_down( 'requires/item_issue_requisition_controller', this.value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_sewing_floor_name').value, 'load_drop_down_sewing_line','line_td');load_drop_down( 'requires/item_issue_requisition_controller', this.value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_sewing_floor_name').value, 'load_drop_down_machine','machine_td');");
					?>
					<input type="hidden" name="hidden_variable_setting" id="hidden_variable_setting" value="">
		        </td>
				
				<td align="right" class="must_entry_caption">Indent Date</td>
		        <td><input type="text" name="txt_indent_date" id="txt_indent_date" class="datepicker" style="width:132px" value="<?=date('d-m-Y');?>" readonly disabled></td>
				<td align="right" class="must_entry_caption">Required Date</td>
				<td ><input type="text" name="txt_required_date" id="txt_required_date" class="datepicker" style="width:132px" value="<?=date('d-m-Y');?>" readonly></td>
			</tr>
            <tr>
	            <td width="110" align="right" class="must_entry_caption">Location</td>
	            <td id="location_td" width="160">
					<?php 
						 echo create_drop_down( "cbo_location_name", 145,$blank_array,"", 1, "-- Select --" );
	                 ?> 	
	            </td>
	            <td width="110" align="right">Division</td>
	            <td id="division_td" width="160">
				   <?php 
						echo create_drop_down( "cbo_division_name", 145,$blank_array,"", 1, "-- Select --");
	               ?> 	
	            </td>
	            <td width="110" align="right" >Department</td>
	            <td id="department_td" width="145">
				   <?php 
						echo create_drop_down( "cbo_department_name", 145,$blank_array,"", 1, "-- Select --" );
	               ?> 	
	            </td>
	            
	          </tr>
						<tr>
	            <td  align="right">Section</td>
	            <td id="section_td" width="132">
					<?php 
						echo create_drop_down( "cbo_section_name", 145,$blank_array,"", 1, "-- Select --",'fnc_sub_section();' );
	              	?> 	
	            </td>
	            <td  align="right">Sewing Floor</td>
	            <td id="sewing_td" width="132">
								<?php 
									echo create_drop_down( "cbo_sewing_floor_name", 145,$blank_array,"", 1, "-- Select Sewing Floor --",'' );
	               ?> 	
	            </td>
	            <td  align="right">Sewing Line</td>
	            <td id="line_td" width="132">
								<?php 
									echo create_drop_down( "cbo_sewing_floor_line", 145,$blank_array,"", 1, "-- Select --",'' );
	               ?> 	
	            </td>
	            <td  align="right" id="sb_section" style="display:none;">Sub Section</td>
	            <td id="sub_section_td" width="132" style="display:none;">
								<?php 
									echo create_drop_down( "cbo_sub_section_name", 145,$sub_sec_array,"", 1, "-- Select --" );
	              ?> 	 
	            </td>
			</tr>
			 <tr>
	 		    <td  align="right">Machine No</td>
          <td id="machine_td" width="132">
						<?php 
							echo create_drop_down( "cbo_machine_no", 145,$blank_array,"", 1, "-- Select --",'' );
             ?> 	
          </td>
			 	<td align="right">Delivery Point</td>
				<td><input type="text" name="cbo_delivery_point" id="cbo_delivery_point" style="width:135px" class="text_boxes"></td>
				<td align="right">Ready To Approved</td>  
				<td>
				    <?
				    echo create_drop_down("cbo_ready_to_approved", 145, $yes_no, "", 1, "-- Select--", 2, "", "", "");
				    ?>
				</td>
           </tr>
			<tr>
				<td align="right">Manual Requisition No</td>  
				<td><input type="text" name="txt_manual_requisition_no" id="txt_manual_requisition_no" style="width:135px" class="text_boxes" ></td>
				<td align="right" class="must_entry_caption">Store Name</td>  
				<td id="store_td">
                <? 
					echo create_drop_down( "cbo_store_name", 145,$blank_array,"", 1, "-- Select Store --", "", "", "","" );
					//echo create_drop_down( "cbo_store_name", 145, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name","id,store_name",1, "-- Select Store --", "", "", "","");
				?>
                </td>
				<td  align="right">Not Appv. Cause</td>
         		<td >
          		 <input name="txt_not_approve_cause" class="text_boxes" readonly placeholder="Double Click for Browse" id="txt_not_approve_cause" style="width:135px"  onClick="openmypage_not_approve_cause()" >
				</td>
					
			</tr>
			<tr>
				<td align="right">Remarks</td>
				<td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks" style="width:700px" class="text_boxes" ></td>
			</tr>

            <tr>
				<td colspan="6" height="30" valign="middle" align="center">
					<input type="hidden" name="txt_is_approved" id="txt_is_approved" value="">
					<span id="approval_status_tr" style="color: red;font-size: 20px;"></span>	
				</td>
           </tr>
            <tr>
				<td colspan="7" height="50" valign="middle" align="center" class="button_container">
					<?
					echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, "");
					echo load_submit_buttons( $permission,"fnc_item_issue_requisition_mst",0,0,"reset_form('itemissuerequisition_1,itemissuerequisition_2','item_issue_listview','','txt_tot_row,0','disable_enable_fields(\'cbo_company_id\');$(\'#tbl_item_issue_list tbody tr:not(:first)\').remove();','')") ; 
					?>
					<span id="button_data_panel"></span>
				</td>
           </tr>
		</table>
		
	</form>
    </fieldset>
    </div>
	<div style="margin-top:30px;" id="Warning"></div>
    <div align="center">
    <fieldset style="width:1300px;">
	<form id="itemissuerequisition_2" name="itemissuerequisition_2" autocomplete="off">
		<legend>Item Details</legend>
		<table width="1280" s class="rpt_table" cellspacing="0" rules="all" id="tbl_item_issue_list" >
		<thead>
			<tr>
				<th width="80">Item Account</th>
                <th width="100" class="must_entry_caption">Item Category</th>
				<th width="80">Item Group</th>
				<th width="80">Item Sub. Group</th>
				<th width="200">Item Description</th>
				<th width="100">Machine Category</th>
				<th width="90">Machine No</th>
				<th width="80">Item Size</th>
				<th width="80">Required For</th>
				<th width="40">UOM</th>
				<th width="80" class="must_entry_caption">Req. Qty.</th>
                <th width="80">Replace Qty.</th>
                <?php	
				$table_row =1;		

				if($user_lavel==2 || $user_lavel==1)
				{
				?>
				<th width="60">Stock</th>
				<?php
				}
				else
				{
				?>
				<th></th>
				<?
				}
				?>
                <th>Remarks</th>
			</tr>
		</thead>
		<tbody>
		  	<tr class="general" id="tr_1">
				<td>
                  <input type="text" name="txt_item_account_1" id="txt_item_account_1" placeholder="Browse" class="text_boxes" onDblClick="fnc_item_account(1)" style="width:80px;" readonly >
                   <input type="hidden" name="txt_product_id_1" id="txt_product_id_1" placeholder="browse" class="text_boxes" style="width:75px;" readonly >
                </td>
                <td>
					<?
                    echo create_drop_down( "txt_item_category_1", 90,$item_category,"", 1, "-- Select --", $selected, "",1,"","","","1,2,3,12,13,14,24,25,101");
                    ?> 
                </td>
			    <td>
			        <input type="text" name="txt_item_group_1" id="txt_item_group_1" class="text_boxes" style="width:75px;" disabled>
	                <input type="hidden" name="hiddenitemgroupid_<? echo $table_row; ?>" id="hiddenitemgroupid_1" class="text_boxes" value="" style="width:75px;" />
			    </td>
			   	<td><input type="text" name="txt_item_sub_1" id="txt_item_sub_1" class="text_boxes" style="width:75px;" disabled></td>
			    <td><input type="text" name="txt_item_description_1" id="txt_item_description_1" class="text_boxes" style="width:190px" disabled  ></td>
			    
				<td align="center">
                    <? echo create_drop_down( "txt_machine_category_1", 90, $machine_category,"", 1, "--Select--", $selected, "load_drop_down( 'requires/item_issue_requisition_controller', this.value+'_'+$table_row, 'load_drop_down_machine_no','machine_no_td_".$table_row."' );",0, "", "", "", "", "", "", "txt_machine_category[]", "txt_machine_category_".$table_row ); ?>
                </td>
                <td align="center" id="machine_no_td_1">
                    <?
                        echo create_drop_down( "txt_machine_no_1", 90, $blank_array, "", 1, "-- Select --", $selected, "", 0, "", "", "", "", "", "", "txt_machine_no[]", "txt_machine_no_".$table_row );
                    ?>
                </td>
				
				<td><input type="text" name="txt_item_size_1" id="txt_item_size_1" class="text_boxes" style="width:75px;" disabled></td>
			    <td><input type="text" name="txt_required_for_1" id="txt_required_for_1" class="text_boxes" placeholder="Write" style="width:70px;"></td>
			    <td align="right">
	            <input type="text" name="txt_uom_1" id="txt_uom_1" class="text_boxes_numeric" style=" width:40px" readonly >
	            <input type="hidden" name="hiddentxtuom_1" id="hiddentxtuom_1" class="text_boxes" value="" style="width:40px;" />
	            <?
			    /*  echo create_drop_down("cbo_uom_1",50,$unit_of_measurement,"",1,"--select--",'','',1);*/
				?>
	            </td>
			    <td><input type="text" name="txt_req_qty_1" id="txt_req_qty_1" class="text_boxes_numeric" style="width:60px;" placeholder="Write" ></td>
                <td><input type="text" name="txt_rtn_qty_1" id="txt_rtn_qty_1" class="text_boxes_numeric" style="width:60px;" placeholder="Write" ></td>
                <?
                if($user_lavel==2 || $user_lavel==1)
				{
				?>
			    <td><input type="text" name="txt_stock_1" id="txt_stock_1" class="text_boxes_numeric" style="width:40px;" readonly></td>
			    <? 
				} 
                else
				{
				?>
			    <td><input type="hidden" name="txt_stock_1" id="txt_stock_1" class="text_boxes_numeric" readonly></td>
			    <? } ?>
	            <td>
                <input type="text" name="txt_remarks_1" id="txt_remarks_1" class="text_boxes"  placeholder="Write" style="width:80px;" >
	            <input type="hidden" id="hidden_selectedID" readonly= "readonly" />
	            <input type="hidden" id="update_id_dtls" name="update_id_dtls" disabled />
	            <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric" />
	            </td>
			 </tr>
		</tbody>
	      <tfoot>
	         <tr>
	 			<td colspan="13" height="50" valign="middle" align="center" class="button_container">
					<? 
	                    echo load_submit_buttons( $permission,"fnc_item_issue_requisition_dtls",0,0,"reset_form('itemissuerequisition_2','','','txt_tot_row,0','$(\'#tbl_item_issue_list tbody tr:not(:first)\').remove();','')",2) ; 
	                ?>
	            </td>
               </tr>
            </tfoot>
		</table>
        
		<div style="width:100%; margin-top:10px;" id="item_issue_listview" ></div>
	</form>
    </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
/*if($('#cbo_location_name').val()>0){
load_drop_down( 'requires/item_issue_requisition_controller',$('#cbo_location_name').val(), 'load_drop_down_store','store_td');
}*/
$('#cbo_location_name').val(0);
$('#cbo_division_name').val(0);
$('#cbo_department_name').val(0);
$('#cbo_section_name').val(0);
</script>
</html>