<?
/*--- ----------------------------------------- Comments
Purpose			: 					
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	26-08-2013
Updated by 		: 	Md: Didarul Alam	
Update date		: 	13-07-2016	   
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
echo load_html_head_contents("GSD Entry", "../", 1,1, $unicode,1,'');
$bulletin_copy_arr=array(1=>"New Bulletin",2=>"Extended Bulletin");

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
var permission='<? echo $permission; ?>';

function openmypage_sysnum()
{ 
		 
   if( form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/gsd_entry_controller.php?action=system_popup&data='+document.getElementById("cbo_company_id").value+'&buyer_id='+document.getElementById("cbo_buyer").value,'System No Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0','')
	
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("system_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("system_no").value=response[9];
			document.getElementById("update_id").value=response[0];
			document.getElementById("wo_po_id").value=response[1];
			document.getElementById("job_no").value=response[2];
			document.getElementById("txt_job_no").value=response[2];
			document.getElementById("cbo_buyer").value=response[3];
			document.getElementById("txt_style_ref").value=response[4];
			document.getElementById("cbo_gmt_item").value=response[5];
			document.getElementById("txt_order_no").value=response[6];
			document.getElementById("txt_ext_no").value=response[7];
			document.getElementById("cbo_bulletin_copy").value=response[8];
			$('#cbo_bulletin_copy').attr('disabled',false);
			//alert(response[0]+'_'+response[1]+'_'+response[2]);return;
			
			get_php_form_data( response[0], "load_php_data_to_form_style", "requires/gsd_entry_controller" );

			if(document.getElementById('update_id').value!=0 && document.getElementById('update_id').value!="")
			{
				show_list_view(document.getElementById('update_id').value,'load_php_dtls_form','new_tbl','requires/gsd_entry_controller','');
				set_button_status(1, permission, 'fnc_gsd_entry',1);
				counter=  $("#gsd_tbl tbody tr").length;
				 
				var operator_smv_tot=0;
				var helper_smv_tot=0;
				var smv_tot=0;
				for(var i=1;i<=counter; i++)
				{
					try{
						operator_smv_tot = operator_smv_tot*1+document.getElementById('txt_operator_'+i).value*1;
						helper_smv_tot = helper_smv_tot*1+document.getElementById('txt_helper_'+i).value*1;
						smv_tot = smv_tot*1+document.getElementById('txt_total_'+i).value*1;
					}
					catch(err){}
				}
				document.getElementById('txt_operator_tot').value=number_format(operator_smv_tot,3,'.','');
				document.getElementById('txt_helper_tot').value=number_format(helper_smv_tot,3,'.','');
				document.getElementById('txt_total_tot').value=number_format(smv_tot,3,'.',''); 
				$('#deleted_id').val( '' );
			}
			else
			{
				show_list_view(1+'_'+response[5],'load_php_dtls_item','gsd_entry_info_list','requires/gsd_entry_controller','');
				var row_num=$('#tbl_body_item tbody tr').length;
				if(row_num>0) { $("#txtAttachment_1").focus(); }	
				
			}
			release_freezing();
		}
	}

}
//System Popup End...

function fnc_copy_bulletin()
{
	if( form_validation('system_no*cbo_bulletin_copy','System ID*Copy')==false)
	{
		return;
	}
	var data="action=copy_bulletin"+get_submitted_data_string('update_id*cbo_bulletin_copy',"../");

	freeze_window(operation);
	http.open("POST","requires/gsd_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_copy_bulletin_response;
}
	
function fnc_copy_bulletin_response()
{
	if(http.readyState == 4) 
	{
		//release_freezing(); return;
		var response=trim(http.responseText).split('**');
		if(response[0]==100)
		{
			alert("Data Copied Successfully");
			document.getElementById('update_id').value = response[1];
			//show_list_view(response[1],'load_php_dtls_form','gsd_entry_info_list','requires/gsd_entry_controller','setFilterGrid(\'tbl_details\',-1);');
				get_php_form_data( response[1], "load_php_data_to_form_style", "requires/gsd_entry_controller" );
				show_list_view(document.getElementById('update_id').value,'load_php_dtls_form','new_tbl','requires/gsd_entry_controller','');
			/*reset_form('','reArrange_seqNo','txt_seqNo*hidden_operation*cbo_resource*txt_attachment*txt_attachment_id*txt_operator*txt_helper*txt_efficiency*txt_tgt_perc*txt_tgt_eff*txt_dtls_id*txt_operation_count*txt_mcOperationCount*txt_tot_smv*txt_mc_smv*txt_manual_smv*txt_finishing_smv','','');*/
			reset_form('','','update_id*wo_po_id*ord_id*job_no*txt_style_ref*cbo_buyer*txt_job_no*txt_order_no*cbo_gmt_item*txt_working_hour','','','');
			//document.getElementById("txt_seqNo").value=response[2];
			document.getElementById('system_no').value = response[3];
			document.getElementById('txt_ext_no').value = response[4];
			set_button_status(0, permission, 'fnc_gsd_entry',1);
		}
		else
		{
			alert("Invalid Operation");
		}
		release_freezing();
	}
}
//Copy End...
	
function openmypage_style()
{ 
   if( form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/gsd_entry_controller.php?action=style_ref_popup&data='+document.getElementById("cbo_company_id").value+'&buyer_id='+document.getElementById("cbo_buyer").value,'Style Ref. Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0','')
	
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("style_ref_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("update_id").value=response[0];
			document.getElementById("wo_po_id").value=response[1];
			document.getElementById("job_no").value=response[2];
			document.getElementById("txt_job_no").value=response[2];
			document.getElementById("cbo_buyer").value=response[3];
			document.getElementById("txt_style_ref").value=response[4];
			document.getElementById("cbo_gmt_item").value=response[5];
			document.getElementById("txt_order_no").value=response[6];
			
			//alert(response[0]+'_'+response[1]+'_'+response[2]);return;
			
			get_php_form_data( response[0], "load_php_data_to_form_style", "requires/gsd_entry_controller" );

			if(document.getElementById('update_id').value!=0 && document.getElementById('update_id').value!="")
			{
				show_list_view(document.getElementById('update_id').value,'load_php_dtls_form','new_tbl','requires/gsd_entry_controller','');
				set_button_status(1, permission, 'fnc_gsd_entry',1);
				counter=  $("#gsd_tbl tbody tr").length;
				 
				var operator_smv_tot=0;
				var helper_smv_tot=0;
				var smv_tot=0;
				for(var i=1;i<=counter; i++)
				{
					try{
						operator_smv_tot = operator_smv_tot*1+document.getElementById('txt_operator_'+i).value*1;
						helper_smv_tot = helper_smv_tot*1+document.getElementById('txt_helper_'+i).value*1;
						smv_tot = smv_tot*1+document.getElementById('txt_total_'+i).value*1;
					}
					catch(err){}
				}
				document.getElementById('txt_operator_tot').value=operator_smv_tot;
				document.getElementById('txt_helper_tot').value=helper_smv_tot;
				document.getElementById('txt_total_tot').value=smv_tot; 
				$('#deleted_id').val( '' );
			}
			else
			{
				show_list_view(1+'_'+response[5],'load_php_dtls_item','gsd_entry_info_list','requires/gsd_entry_controller','');
				var row_num=$('#tbl_body_item tbody tr').length;
				if(row_num>0) { $("#txtAttachment_1").focus(); }	
				
			}
			release_freezing();
		}
	}
}

function openmypage_operation()
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/gsd_entry_controller.php?action=operation_popup','Operation Popup', 'width=850px,height=350px,center=1,resize=1,scrolling=0','')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("operation_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			
			freeze_window(5);
			document.getElementById("txt_operation").value=response[1];
			document.getElementById("cbo_resource").value=response[2];
			document.getElementById("txt_operator").value=response[3];
			document.getElementById("txt_helper").value=response[4];
			document.getElementById("txt_total").value=response[5];
			document.getElementById("hidden_operation").value=response[0];
			release_freezing();
		}
	}
}

function openmypage_attachment()
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/gsd_entry_controller.php?action=attachment_popup','Attachment Popup', 'width=400px,height=350px,center=1,resize=1,scrolling=0','')
	
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("attachment_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById("txt_attachment_id").value=response[0];
			document.getElementById("txt_attachment").value=response[1];
			//reset_form();
			//get_php_form_data( response[1], "load_php_data_to_form_attachment", "requires/gsd_entry_controller" );
			release_freezing();
		}
	}
}

function openmypage_attachment_multuple(id,show_id)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/gsd_entry_controller.php?action=attachment_popup','Attachment Popup', 'width=400px,height=350px,center=1,resize=1,scrolling=0','')
	
	emailwindow.onclose=function()
	{
		//alert(id)
		var theemail=this.contentDoc.getElementById("attachment_id");
		var response=theemail.value.split('_');
		if (theemail.value!="")
		{
			freeze_window(5);
			document.getElementById(id).value=response[0];
			document.getElementById(show_id).value=response[1];
			//reset_form();
			//get_php_form_data( response[1], "load_php_data_to_form_attachment", "requires/gsd_entry_controller" );
			release_freezing();
		}
	}
}

function fnc_move_cursor(val,id, field_id,lnth,max_val)
{
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
}

var operator_smv_total="";
var counter=1;

function add_to_gsd_list()
{
	//if( form_validation('cbo_company_id*cbo_body_part*txt_operation*cbo_resource*txt_operator*txt_helper','Company Name*Body Part*Operation*Resource*Operator SMV*Helper SMV')==false)
	if( form_validation('cbo_company_id*cbo_body_part*txt_operation*cbo_resource','Company Name*Body Part*Operation*Resource')==false)
	{
		return;
	}
	//var data=id.split('_');
	var tbl_counter_final="";
	var operation_name = document.getElementById('txt_operation').value;
	var operation_id = document.getElementById('hidden_operation').value;
	
	var cbo_body_part_name = document.getElementById('cbo_body_part');
	var cbo_body_part_name_text  =cbo_body_part_name.options[cbo_body_part_name.selectedIndex].text;
	var cbo_body_part_id = document.getElementById('cbo_body_part').value;
	//alert (cbo_body_part_id);
	var cbo_resource_name = document.getElementById('cbo_resource');
	var cbo_resource_name_text  =cbo_resource_name.options[cbo_resource_name.selectedIndex].text;
	var cbo_resource_id = document.getElementById('cbo_resource').value;
	
	var attachment = document.getElementById('txt_attachment').value;
	var attachment_id = document.getElementById('txt_attachment_id').value;
	var operator_smv = document.getElementById('txt_operator').value;
	var helper_smv = document.getElementById('txt_helper').value;
	var total_smv = document.getElementById('txt_total').value;
	var sewing_id = document.getElementById('hidden_operation').value;

	var cbo_operation_type_name = document.getElementById('cbo_operation_type');
	var cbo_operation_type_name_text  =cbo_operation_type_name.options[cbo_operation_type_name.selectedIndex].text;
	var cbo_operation_type_id = document.getElementById('cbo_operation_type').value;

	var table = document.getElementById('gsd_tbl');
	var num_row = table.rows.length;
	if(num_row>=1)
	{
		var counter123 = $('#gsd_tbl tbody tr:last').attr('id');
		counter123=counter123.split('_');
		counter=(counter123[1]*1)+1;
	}
	else if (num_row<1)
	{
		counter=1;
	}

	$('#gsd_tbl tbody').append(
	'<tr id="gsd_'+counter+'">'
				+ '<td align="center"><input type="text" name="txt_seq_[]" class="text_boxes" id="txt_seq_'+counter+'" value="'+counter+'" style="width:40px;" onBlur="duplication_check('+counter+')" /></td><td align="center"><input type="text" name="cbo_body_part_[]" style="width:83px;" class="text_boxes" id="cbo_body_part_' + counter + '"	value="'+cbo_body_part_name_text+'" readonly="readonly"/><input type="hidden" name="cbo_body_part_id_[]" class="text_boxes" id="cbo_body_part_id_'+counter+'" value="'+cbo_body_part_id+'" style="width:80px " /><input type="hidden" name="not_delete_row_[]" class="text_boxes" id="not_delete_row_'+counter+'" value="0" style="width:80px " /></td><td align="center"><input type="hidden" name="update_id_dtls_[]" class="text_boxes" id="update_id_dtls_'+counter+'" value="" style="width:40px;" /><input type="hidden" name="sewing_id_[]" class="text_boxes" id="sewing_id_'+counter+'" value="'+sewing_id+'" style="width:40px;" /><input type="text" name="txt_operation_[]" style="width:100px;" class="text_boxes" id="txt_operation_' + counter + '" value="'+operation_name+'" readonly="readonly"/><input type="hidden" name="operation_id_[]" class="text_boxes" id="operation_id_'+counter+'" value="'+operation_id+'" style="width:70px;" /></td><td align="center"><input type="text" name="txt_resource_[]" style="width:78px;" class="text_boxes" id="txt_resource_' + counter + '" value="'+cbo_resource_name_text+'" readonly="readonly"/><input type="hidden" name="txt_resource_id_[]" class="text_boxes" id="txt_resource_id_'+counter+'" value="'+cbo_resource_id+'" style="width:70px;" readonly="readonly"/></td><td align="center"><input type="text" name="txt_attachment_[]" class="text_boxes" id="txt_attachment_'+counter+'" value="'+attachment+'" style="width:73px;" /><input type="hidden" name="txt_attachment_id_[]" class="text_boxes" id="txt_attachment_id_'+counter+'" value="'+attachment_id+'"/></td><td align="center"><input type="text" name="txt_operator_[]" style="width:65px;" class="text_boxes_numeric" id="txt_operator_' + counter + '" value="'+operator_smv+'" readonly="readonly"/></td><td align="center"><input	type="text"	name="txt_helper_[]" style="width:65px;" class="text_boxes_numeric"	id="txt_helper_' + counter + '"	value="'+helper_smv+'" readonly="readonly"/></td><td align="center"><input	type="text"	name="txt_total_[]" style="width:70px;" class="text_boxes_numeric" id="txt_total_' + counter + '" value="'+total_smv+'" readonly="readonly"/></td><td align="center"><input type="text" name="cbo_operation_type_[]" style="width:90px;" class="text_boxes" id="cbo_operation_type_' + counter + '"	value="'+cbo_operation_type_name_text+'" readonly="readonly"/><input type="hidden"  name="cbo_operation_type_id_[]" class="text_boxes" id="cbo_operation_type_id_'+counter+'" value="'+cbo_operation_type_id+'" style="width:80px " /></td><td align="center"><input type="text" name="txt_remove[]" style="width:50px;" class="formbutton" onclick="remove_row('+counter+')" id="txt_remove' + counter + '" value="Remove" /></td>'
			+ '</tr>'
		);
	var operator_smv_tot=0;
	var helper_smv_tot=0;
	var smv_tot=0;
	for(var i=1;i<=counter;i++)
	{
		try{
			operator_smv_tot = operator_smv_tot*1+document.getElementById('txt_operator_'+i).value*1;
			helper_smv_tot = helper_smv_tot*1+document.getElementById('txt_helper_'+i).value*1;
			smv_tot = smv_tot*1+document.getElementById('txt_total_'+i).value*1;
		}
		catch(err){}
	}
	counter++;
	document.getElementById("txt_operator_tot").value=operator_smv_tot.toFixed( 3 );
	document.getElementById("txt_helper_tot").value=helper_smv_tot.toFixed( 3 );
	document.getElementById("txt_total_tot").value=smv_tot.toFixed( 3 );
	
	document.getElementById("txt_operation_count").value=num_row;
	
	var tbl_counter_final = $('#gsd_tbl tbody tr').length;
	
	document.getElementById("txt_operation_count").value=tbl_counter_final;

	if($("#txt_allowance").val()!="")
	{
		calculate_sam();
	}
	reset_form('','','txt_operation*cbo_resource*txt_attachment*txt_attachment_id*txt_operator*txt_helper*txt_total*cbo_operation_type*hidden_operation','','');

}

function add_all_gsd_list()
{

	$("#tbl_body_item").find('tbody tr').each(function()
	{
		
		if($(this).find('input[name="checkRow[]"]').is(':checked'))
		{
			var tbl_counter_final="";
			var operation_name =trim($(this).find("td:eq(2)").text());
			var operation_id =$(this).find('input[name="operation_id[]"]').val();
			// alert(operation_name);
			 
			var cbo_body_part_id =$(this).find('select[name="cbo_body_part_id[]"]').val();
			//alert (cbo_body_part_id);
			var cbo_resource_name_text =trim($(this).find("td:eq(3)").text());
			//var cbo_resource_name_text  =cbo_resource_name.options[cbo_resource_name.selectedIndex].text;
			var cbo_resource_id =$(this).find('input[name="txt_resource_id[]"]').val();
			
			var attachment=$(this).find('input[name="txt_attachment[]"]').val();
			var attachment_id =$(this).find('input[name="txt_attachment_id[]"]').val();
			var operator_smv =$(this).find('input[name="txt_operator[]"]').val();
			var helper_smv =$(this).find('input[name="txt_helper[]"]').val();
			var total_smv =$(this).find('input[name="txt_total[]"]').val();
			var sewing_id =$(this).find('input[name="txt_sewing_id[]"]').val(); 
			var operation_type_arr=new Array();
			<? $operation_type=array(0=>"--Select--",1=>"Body Part Starting",2=>"Body Part Ending",3=>"Gmt Last Operation"); ?>
			 var opt_name= <? echo json_encode($operation_type); ?>;
			 var body_part_arr= <? echo json_encode($body_part); ?>;
			 body_part_arr[0]="--Select--";
			
			var cbo_operation_type_id =$(this).find('select[name="cbo_operation_type[]"]').val();
			var cbo_operation_type_name_text =opt_name[cbo_operation_type_id];
			var cbo_body_part_name_text =body_part_arr[cbo_body_part_id]; 
			var table = document.getElementById('gsd_tbl'); 
			var num_row = table.rows.length;
			if(num_row>=1)
			{
				var counter123 = $('#gsd_tbl tbody tr:last').attr('id');
				counter123=counter123.split('_');
				counter=(counter123[1]*1)+1;
			}
			else if (num_row<1)
			{
				counter=1;
			}
		
			$('#gsd_tbl tbody').append(
			'<tr id="gsd_'+counter+'">'
						+ '<td align="center"><input type="text" name="txt_seq_[]" class="text_boxes" id="txt_seq_'+counter+'" value="'+counter+'" style="width:40px;" onBlur="duplication_check('+counter+')" /></td><td align="center"><input type="text" name="cbo_body_part_[]" style="width:83px;" class="text_boxes" id="cbo_body_part_' + counter + '"	value="'+cbo_body_part_name_text+'" readonly="readonly"/><input type="hidden" name="cbo_body_part_id_[]" class="text_boxes" id="cbo_body_part_id_'+counter+'" value="'+cbo_body_part_id+'" style="width:80px " /><input type="hidden" name="not_delete_row_[]" class="text_boxes" id="not_delete_row_'+counter+'" value="0" style="width:80px " /></td><td align="center"><input type="hidden" name="update_id_dtls_[]" class="text_boxes" id="update_id_dtls_'+counter+'" value="" style="width:40px;" /><input type="hidden" name="sewing_id_[]" class="text_boxes" id="sewing_id_'+counter+'" value="'+sewing_id+'" style="width:40px;" /><input type="text" name="txt_operation_[]" style="width:100px;" class="text_boxes" id="txt_operation_' + counter + '" value="'+operation_name+'" readonly="readonly"/><input type="hidden" name="operation_id_[]" class="text_boxes" id="operation_id_'+counter+'" value="'+operation_id+'" style="width:70px;" /></td><td align="center"><input type="text" name="txt_resource_[]" style="width:78px;" class="text_boxes" id="txt_resource_' + counter + '" value="'+cbo_resource_name_text+'" readonly="readonly"/><input type="hidden" name="txt_resource_id_[]" class="text_boxes" id="txt_resource_id_'+counter+'" value="'+cbo_resource_id+'" style="width:70px;" readonly="readonly"/></td><td align="center"><input type="text" name="txt_attachment_[]" class="text_boxes" id="txt_attachment_'+counter+'" value="'+attachment+'" style="width:73px;" /><input type="hidden" name="txt_attachment_id_[]" class="text_boxes" id="txt_attachment_id_'+counter+'" value="'+attachment_id+'"/></td><td align="center"><input type="text" name="txt_operator_[]" style="width:65px;" class="text_boxes_numeric" id="txt_operator_' + counter + '" value="'+operator_smv+'" readonly="readonly"/></td><td align="center"><input	type="text"	name="txt_helper_[]" style="width:65px;" class="text_boxes_numeric"	id="txt_helper_' + counter + '"	value="'+helper_smv+'" readonly="readonly"/></td><td align="center"><input	type="text"	name="txt_total_[]" style="width:70px;" class="text_boxes_numeric" id="txt_total_' + counter + '" value="'+total_smv+'" readonly="readonly"/></td><td align="center"><input type="text" name="cbo_operation_type_[]" style="width:90px;" class="text_boxes" id="cbo_operation_type_' + counter + '"	value="'+cbo_operation_type_name_text+'" readonly="readonly"/><input type="hidden"  name="cbo_operation_type_id_[]" class="text_boxes" id="cbo_operation_type_id_'+counter+'" value="'+cbo_operation_type_id+'" style="width:80px " /></td><td align="center"><input type="text" name="txt_remove[]" style="width:50px;" class="formbutton" onclick="remove_row('+counter+')" id="txt_remove' + counter + '" value="Remove" /></td>'
			+ '</tr>'
		);
		var operator_smv_tot=0;
		var helper_smv_tot=0;
		var smv_tot=0;
		for(var i=1;i<=counter;i++)
		{
			try{
				operator_smv_tot = operator_smv_tot*1+document.getElementById('txt_operator_'+i).value*1;
				helper_smv_tot = helper_smv_tot*1+document.getElementById('txt_helper_'+i).value*1;
				smv_tot = smv_tot*1+document.getElementById('txt_total_'+i).value*1;
			}
			catch(err){}
		}
		counter++;
			document.getElementById("txt_operator_tot").value=operator_smv_tot.toFixed( 3 );
			document.getElementById("txt_helper_tot").value=helper_smv_tot.toFixed( 3 );
			document.getElementById("txt_total_tot").value=smv_tot.toFixed( 3 );
			
			document.getElementById("txt_operation_count").value=num_row;
			
			var tbl_counter_final = $('#gsd_tbl tbody tr').length;
			
			document.getElementById("txt_operation_count").value=tbl_counter_final;
		
			if($("#txt_allowance").val()!="")
			{
				calculate_sam();
			}
			$(this).remove();
		}
		
	});
	
	var row_num=$('#tbl_body_item tbody tr').length;
	if(row_num<1) { $("#gsd_entry_info_list").html(""); }	
}


function remove_row(id)
{
	var not_delete_row=$("#not_delete_row_"+id).val();
	if(not_delete_row==1)
	{
		alert("Barcode Generated. Operation Change Not Allowed.");
		return;
	}
	
	operator_smv_tot=$("#txt_operator_tot").val();
	helper_smv_tot=$("#txt_helper_tot").val();
	smv_tot=$("#txt_total_tot").val();
	
	operator_smv_tot=operator_smv_tot-$("#txt_operator_"+id).val();
	document.getElementById("txt_operator_tot").value=operator_smv_tot.toFixed( 3 );
	
	helper_smv_tot=helper_smv_tot-$("#txt_helper_"+id).val();
	document.getElementById("txt_helper_tot").value=helper_smv_tot.toFixed( 3 );
	
	smv_tot=smv_tot-$("#txt_total_"+id).val();
	document.getElementById("txt_total_tot").value=smv_tot.toFixed( 3 );
	
	var update_id_dtls=$('#update_id_dtls_'+id).val();
	var deleted_id=$('#deleted_id').val();
	var selected_id='';
	
	if(update_id_dtls!='')
	{
		if(deleted_id=='') deleted_id=update_id_dtls; else deleted_id+=','+update_id_dtls;
		$('#deleted_id').val( deleted_id );
	}
	
	$("#gsd_"+id).remove();
	document.getElementById("txt_operation_count").value=$('#gsd_tbl tbody tr').length;
	
	if($("#txt_allowance").val()!="")
	{
		calculate_sam();
	}
	
}
	
function calculate_sam()
{
	document.getElementById('txt_sam_for_style').value="";
	var total_grand=document.getElementById('txt_total_tot').value;
	var allowance=document.getElementById('txt_allowance').value/100;
	var sam=(total_grand*1)+(total_grand*allowance);
	document.getElementById('txt_sam_for_style').value=sam.toFixed(4);
	document.getElementById('txt_pitch_time').value=(sam/document.getElementById('txt_operation_count').value).toFixed(4);
}

function fnc_gsd_entry( operation )
{
	alert(1);
	
	var txt_allowance=document.getElementById('txt_allowance').value;
	if(txt_allowance=="")
	{
		if( form_validation('cbo_company_id*txt_style_ref*txt_working_hour*txt_allowance','Company Name*Style Ref.*Working Hour*Allowance')==false)
		{
			return;
		}
	}
	else
	{
		if( form_validation('cbo_company_id*txt_style_ref*txt_working_hour','Company Name*Style Ref.*Working Hour')==false)
		{
			return;
		}
	}
	
	arrange_table();
	
	var data2=''; var i=0;
	
	$("#gsd_tbl").find('tbody tr').each(function()
	{
		i++;
		
		var txt_seq_=$(this).find('input[name="txt_seq_[]"]').val();
		var cbo_body_part_id_=$(this).find('input[name="cbo_body_part_id_[]"]').val();
		var operation_id_=$(this).find('input[name="operation_id_[]"]').val();
		var txt_resource_id_=$(this).find('input[name="txt_resource_id_[]"]').val();
		var txt_attachment_=$(this).find('input[name="txt_attachment_[]"]').val();
		var txt_operator_=$(this).find('input[name="txt_operator_[]"]').val();
		var txt_helper_=$(this).find('input[name="txt_helper_[]"]').val();
		var txt_total_=$(this).find('input[name="txt_total_[]"]').val();
		var cbo_operation_type_id_=$(this).find('input[name="cbo_operation_type_id_[]"]').val();
		var txt_attachment_id_=$(this).find('input[name="txt_attachment_id_[]"]').val();
		var sewing_id_=$(this).find('input[name="sewing_id_[]"]').val();
		var update_id_dtls_=$(this).find('input[name="update_id_dtls_[]"]').val();

		data2+="&txt_seq_" + i + "='" + txt_seq_+"'"+"&cbo_body_part_id_" + i + "='" + cbo_body_part_id_+"'"+"&operation_id_" + i + "='" + operation_id_+"'"+"&txt_resource_id_" + i + "='" + txt_resource_id_+"'"+"&txt_attachment_" + i + "='" + txt_attachment_+"'"+"&txt_operator_" + i + "='" + txt_operator_+"'"+"&txt_helper_" + i + "='" + txt_helper_+"'"+"&txt_total_" + i + "='" + txt_total_+"'"+"&cbo_operation_type_id_" + i + "='" + cbo_operation_type_id_+"'"+"&txt_attachment_id_" + i + "='" + txt_attachment_id_+"'"+"&sewing_id_" + i + "='" + sewing_id_+"'"+"&update_id_dtls_" + i + "='" + update_id_dtls_+"'";
	});
	
	var data1="action=save_update_delete&operation="+operation+"&num_row="+i+get_submitted_data_string('cbo_company_id*txt_style_ref*ord_id*cbo_buyer*job_no*txt_order_no*cbo_gmt_item*wo_po_id*txt_working_hour*cbo_action*cbo_ready_to_approved*txt_allowance*txt_sam_for_style*txt_operation_count*txt_pitch_time*txt_total_tot*txt_where_man_power*txt_where_man_power1*update_id*deleted_id',"../");

	/*for(var i=1; i<=num_row; i++)
	{
				data2+=get_submitted_data_string('txt_seq_'+i+'*cbo_body_part_id_'+i+'*operation_id_'+i+'*txt_resource_id_'+i+'*txt_attachment_'+i+'*txt_operator_'+i+'*txt_helper_'+i+'*txt_total_'+i+'*cbo_operation_type_id_'+i+'*txt_attachment_id_'+i+'*sewing_id_'+i+'*update_id_dtls_'+i,"../",i);
	}*/
	
	//alert(data2);return;
	var data=data1+data2;
	freeze_window(operation);
	http.open("POST","requires/gsd_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_gsd_entry_response;

}

function fnc_gsd_entry_response()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		show_msg(response[0]);
		
		if(response[0]==0 || response[0]==1 )
		{
			document.getElementById('update_id').value = response[1];
			document.getElementById('system_no').value = response[1];
			show_list_view(document.getElementById('update_id').value,'load_php_dtls_form','new_tbl','requires/gsd_entry_controller','');
			set_button_status(1, permission, 'fnc_gsd_entry',1);
		}
		
		release_freezing();
	}
}

function duplication_check(row_id)
{
	var row_num=$('#gsd_tbl tbody tr').length;
	var txt_seq=$('#txt_seq_'+row_id).val();
	
	for(var j=1; j<=row_num; j++)
	{
		if(j==row_id)
		{
			continue;
		}
		else
		{
			var txt_seq_check=$('#txt_seq_'+j).val();

			if(txt_seq==txt_seq_check)
			{
				alert("Duplicate Seq No. "+txt_seq);
				$('#txt_seq_'+row_id).val('');
				return;
			}
		}
	}
}

function arrange_table()
{
	table_arr     = [];
	table_tr_id   = [];
	var new_table_data="";
	var tr_next_id = $('#gsd_tbl tbody tr:last').attr('id');
	
	var next_id=tr_next_id.split('_');
	tr_count=next_id[1];
	//alert (tr_count);
	for(i=1; i<=tr_count;i++)
	{
		if($('#txt_seq_'+i).val()!=undefined)
		{
			user_tr_index = $('#txt_seq_'+i).val();
			tr_id         = $('#gsd_'+i).val();
			table_arr.push(user_tr_index);
		}
	}
	
	table_arr = table_arr.sort(function(a,b){return a-b;});
	
	count_sorted_array = table_arr.length;
	//alert(count_sorted_array);
	var t=1;
	for(j=0; j<count_sorted_array;j++)
	{
		new_tr = table_arr[j];
		for(i=1; i<=tr_count;i++)
		{		
			user_tr_index = $('#txt_seq_'+i).val();
			//alert(user_tr_index+"="+new_tr);
			if(user_tr_index==new_tr)
			{ 
				 new_table_data += '<tr id="gsd_' +  t  + '"><td width=53 align="center"><input type="text" name="txt_seq_[]" class="text_boxes" id="txt_seq_'+ t +'" value="'+ t +'" style="width:40px;" onBlur="duplication_check('+t+')"/></td>';
				 $('#gsd_'+i +' td').each(function(index, element) {
					 //alert(index);
					 if(index == 0)
					 {
					 }
					 else if(index == 9)
					 {
						 new_table_data += '<td align="center"><input type="text" name="txt_remove[]" style="width:50px; " class="formbutton" onclick="remove_row('+t+')"	id="txt_remove' + t + '" value="Remove" /></td>';
					 }
					 else
					 {
						td_data = $(this).html();
						new_table_data += '<td align="center">'+td_data+'</td>';
					 }
				});
				new_table_data += '</tr>';
				
				t++;		
			}		
		}
	}
	
	$('#gsd_tbl tbody').html('');
	$('#gsd_tbl tbody').append(new_table_data);
}

function print_gsd_report()
{
	print_report( $('#cbo_company_id').val()+'*'+$('#txt_job_no').val()+'*'+$('#update_id').val(), "print_gsd_report", "requires/gsd_entry_controller") 
	//return;
	show_msg("3");
}
//var ddd={ dec_type:2, comma:0, currency:1}
var ddd={ dec_type:8, comma:0}
/*
function calculate_sam()
{
	document.getElementById('txt_net_sam').value="";
	document.getElementById('txt_pitch_time').value="";
	var txt_allowance=document.getElementById('txt_allowance').value/100;
	var smv_grand_total=document.getElementById('smv_grand_total').value;
	var sam=(smv_grand_total*1)+(smv_grand_total*txt_allowance);
	document.getElementById('txt_net_sam').value=sam.toFixed(4);
	document.getElementById('txt_pitch_time').value=(sam/document.getElementById('txt_operation_count').value).toFixed(4);
	 
}*/
function load_body_part_value()
{
	var bodypart=$("#cbo_body_part").val();
	show_list_view(0+'_'+bodypart,'load_php_dtls_item','gsd_entry_info_list','requires/gsd_entry_controller','');
	var row_num=$('#tbl_body_item tbody tr').length;
	if(row_num>0) { $("#txtAttachment_1").focus(); }

}
	

</script>
</head>
<body onLoad="set_hotkey()">
 <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>    
    <form name="gsdentry_1" id="gsdentry_1"  autocomplete="off"  >
    	<div  style="width:900px; " align="center">    		
	        <fieldset style="width:880px;">
	        <legend>GSD Entry Info </legend>
	            <table cellpadding="0" cellspacing="2" width="100%">
	                <tr>
	             	    <td><strong>System ID</strong></td>
	                    <td align="left">
	                 		<input type="text" id="system_no" class="text_boxes_numeric" style="width:140px;" placeholder="Browse" onDblClick="openmypage_sysnum();" readonly />
	                    </td>
	                    <td><strong>Extention No.</strong></td>
	                    <td align="left"><input type="text" id="txt_ext_no" class="text_boxes_numeric" style="width:140px;" placeholder="Display" readonly /></td>
	                    <td><strong>Copy</strong></td>
	                    <td align="left"><? echo create_drop_down( "cbo_bulletin_copy", 150, $bulletin_copy_arr, "", 1, "--  Select --", 0, "", 1); ?></td>
	                </tr>
	                <tr>
	                    <td width="120" class="must_entry_caption">Company</td>
	                    <td width="150">
	                    <input type="hidden" id="update_id" style="width:140px;" />
	                    <input type="hidden" id="wo_po_id" style="width:140px;" />
	                    <input type="hidden" id="ord_id" style="width:140px;" />
	                    <input type="hidden" id="job_no" style="width:140px;" />
	                    <?
	                        echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
	                    <td width="120" class="must_entry_caption">Style Ref.</td>                                              
	                    <td width="140">
	                         <input type="text" name="txt_style_ref" id="txt_style_ref"  class="text_boxes" style="width:138px" placeholder="Browse" onDblClick="openmypage_style();" readonly />
	                    </td>
	                    <td width="120">Buyer Name</td>                                              
	                    <td width="140">
	                        <?
	                            echo create_drop_down( "cbo_buyer", 150, "select id,buyer_name from lib_buyer", "id,buyer_name", 1, " Display ", 0, "", 1);	
	                        ?>
	                    </td>
	                </tr> 
	                <tr>
	                    <td width="120">Job No.</td>
	                    <td width="140">
	                         <input type="text" name="txt_job_no" id="txt_job_no"  class="text_boxes" style="width:138px" readonly />
	                    </td>
	                    <td width="120">Order No.</td>
	                    <td width="140">
	                         <input type="text" name="txt_order_no" id="txt_order_no"  class="text_boxes" style="width:138px" readonly />
	                    </td>
	                    <td width="120">Garment Item</td>
	                    <td width="140">
	                        <?
	                            echo create_drop_down( "cbo_gmt_item", 150, $garments_item, "", 1, " Display ", 0, "", 1);	
	                        ?>
	                    </td>
	                </tr>
	                <tr>
	                    <td width="120" class="must_entry_caption">Working Hour</td>
	                    <td width="140">
	                         <input type="text" name="txt_working_hour" id="txt_working_hour"  class="text_boxes_numeric" style="width:138px" onKeyUp="fnc_move_cursor(this.value,'txt_working_hour','cbo_action',2,23)"/>
	                    </td>
	                    <td width="120">Action</td>
	                    <td width="150">
	                        <?
	                            echo create_drop_down( "cbo_action",150,$row_status,"", 1, "--Select Action--", 1, "","","","","","","");	
	                        ?>
	                    </td>                 
	                    <td width="270" colspan="2">
	                          <input type="button" class="image_uploader" style="width:285px" value="DISPLAY GERMENTS IMAGE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'gsd_entry', 0 ,1)">
	                    </td>
	                </tr>
	                
	                <tr>
	                    <td align="left">Ready To Approved</td>  
	                    <td width="150" height="10">
	                        <?
	                        echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","","" );
	                        ?>
	                    </td>  
	                </tr>
	                
	                <tr height="5"><td colspan="6">&nbsp;</td></tr>
	                
	                <tr>
	                    <td colspan="6" align="center"><input type="button" name="button" class="formbuttonplasminus" id="resetBtn" style="width:120px;" value="Copy GSD" onClick="fnc_copy_bulletin();" /></td>
	                </tr>
	            </table>
	        </fieldset>
	            <br>
	        <div style="width:900px;">
	        <fieldset style="width:880px;">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="100%">
	                <thead class="form_table_header">
	                    <th width="115" align="center" class="must_entry_caption">Body Part</th>
	                    <th width="70" align="center" class="must_entry_caption">Operation</th>
	                    <th width="100" align="center" class="must_entry_caption">Resource</th>
	                    <th width="70" align="center">Attachment</th>
	                    <th width="70" align="center" class="must_entry_caption">Operator SMV</th>
	                    <th width="70" align="center" class="must_entry_caption">Helper SMV</th>
	                    <th width="80" align="center">Total SMV</th>
	                    <th width="120" align="center">Operation Type</th>
	                    <th width="50" align="center">&nbsp;</th>
	                </thead>
	                <tbody>
	                    <tr class="general">
	                        <td>
	                            <?
	                                asort($body_part);
	                               echo create_drop_down( "cbo_body_part",110,$body_part,"", 1, "--Select--", 0, "load_body_part_value()","","28,10,197,2,3,106,11,6,60,59,92,40,196,7,9,63,26,53,79","","","","");
	                            ?>
	                        </td>
	                        <td>
	                             <input type="text" name="txt_operation" id="txt_operation"  class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_operation();" readonly />
	                        </td>
	                        <td>
	                            <?
	                               asort($production_resource);
								   echo create_drop_down( "cbo_resource",99,$production_resource,"", 1, "--Select--", 0, "","","","","","","");
	                            ?>
	                        </td>
	                        <td>
	                             <input type="text" name="txt_attachment" id="txt_attachment"  class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_attachment();" readonly />
	                             <input type="hidden" name="txt_attachment_id" id="txt_attachment_id" />
	                        </td>
	                        <td>
	                             <input type="text" name="txt_operator" id="txt_operator" onKeyUp="math_operation( 'txt_total', 'txt_operator*txt_helper', '+', '', ddd)"  class="text_boxes_numeric" style="width:70px" />
	                        </td>
	                        <td>
	                             <input type="text" name="txt_helper" id="txt_helper" onKeyUp="math_operation( 'txt_total', 'txt_operator*txt_helper', '+', '', ddd)"  class="text_boxes_numeric" style="width:70px" />
	                        </td>
	                        <td>
	                             <input type="text" name="txt_total" id="txt_total"  class="text_boxes_numeric" style="width:80px" onDblClick="" disabled />
	                        </td>
	                        <td>
	                            <?
	                            $operation_type=array(1=>"Body Part Starting",2=>"Body Part Ending",3=>"Gmt Last Operation");
	                               echo create_drop_down( "cbo_operation_type",115,$operation_type,"", 1, "--Select--", 0, "","","","","","","");
	                            ?>
	                        </td>
	                        <td>
	                            <input type="hidden" id="hidden_operation" style="width:50px;" >
	                            <input type="button" id="add_button" class="formbuttonplasminus" style="width:50px" value="Add" onClick="add_to_gsd_list();" />
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	            <br>
	            <div style="width:880px;" id="gsd_list_view">
	                <table width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
	                        <thead>

	                            <th width="53" align="center">Seq.No</th>
	                            <th width="97" align="center">Body Part</th>
	                            <th width="116" align="center">Operation</th>
	                            <th width="92" align="center">Resource</th>
	                            <th width="88" align="center">Attachment</th>
	                            <th width="80" align="center">Operator SMV</th>
	                            <th width="79" align="center">Helper SMV</th>
	                            <th width="83" align="center">Total SMV</th>
	                            <th width="106" align="center">Operation Type</th>
	                            <th align="center"><input type="hidden" name="deleted_id" id="deleted_id" readonly></th>
	                            <!--<th width="58" align="center">&nbsp;</th>-->
	                        </thead>
	                    </table>
	                </div>
	                
	                <div style="width:880px; height:250px; overflow-y:scroll"  id="new_tbl">
	                    <table id="gsd_tbl" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
	                        <tbody id="return_tbl"></tbody>
	                     </table>
	                </div>
	            <table>
	                <tfoot id="">
	                    <tr>
	                        <td width="70" align="center">&nbsp;</td>
	                        <td width="90" align="center">&nbsp;</td>
	                        <td width="110" align="center">&nbsp;</td>
	                        <td width="82" align="center">&nbsp;</td>
	                        <td width="82" align="center">&nbsp;</td>
	                        <td width="65" align="center">
	                            <input type="text" name="txt_operator_tot" id="txt_operator_tot"  class="text_boxes_numeric" placeholder="Sum" style="width:65px" disabled/>
	                        </td>
	                        <td width="70" align="center">
	                            <input type="text" name="txt_helper_tot" id="txt_helper_tot"  class="text_boxes_numeric" placeholder="Sum" style="width:65px" disabled/>
	                        </td>
	                        <td width="81" align="center">
	                            <input type="text" name="txt_total_tot" id="txt_total_tot"  class="text_boxes_numeric" placeholder="Sum" style="width:65px" disabled/>
	                        </td>
	                        <td width="100" align="center">&nbsp;</td>
	                        <td width="50" align="center">&nbsp;</td>
	                       
	                    </tr>
	                </tfoot>
	            </table>
	        </fieldset>
	        </div>
	             
	        <fieldset style="width:880px;">
	            <table cellpadding="0" cellspacing="2" width="100%">
	                <tr>
	                    <td width="120" class="must_entry_caption">Allowance %</td>                                              
	                    <td width="140">
	                         
	                         <input type="text" name="txt_allowance" id="txt_allowance"  class="text_boxes_numeric" onKeyUp="calculate_sam();" style="width:140px" />
	                    </td>
	                    <td width="120">SAM for the style</td>                                              
	                    <td width="140">
	                         <input type="text" name="txt_sam_for_style" id="txt_sam_for_style"  class="text_boxes_numeric" style="width:140px"  readonly />
	                    </td>
	                    <td width="120">Operation Count</td>                                              
	                    <td width="140">
	                         <input type="text" name="txt_operation_count" id="txt_operation_count"  class="text_boxes_numeric" style="width:140px" readonly />
	                    </td>
	                </tr>
	                <tr>
	                    <td width="120">Pitch TIme</td>                                              
	                    <td width="140">

	                         <input type="text" name="txt_pitch_time" id="txt_pitch_time"  class="text_boxes_numeric" style="width:140px" readonly />
	                    </td>
	                    <td width="120">Where Man Power</td>                                              
	                    <td width="140">
	                         <input type="text" name="txt_where_man_power" id="txt_where_man_power" class="text_boxes_numeric" style="width:140px" />
	                    </td>
	                    <td width="120">Where Man Power</td>                                              
	                    <td width="140">
	                         <input type="text" name="txt_where_man_power1" id="txt_where_man_power1" class="text_boxes_numeric" style="width:140px" />
	                    </td>
	                </tr>
	            </table>
	        </fieldset>
	            <table  style="border:none; width:880px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_gsd_entry",0,0,"reset_form('gsdentry_1','gsd_entry_info_list','','','$(\'#gsd_tbl tr:not(:first)\').remove();')",1);
	                        ?>
	                        <input type="button" name="button" class="formbutton" value="Assending"  onClick="arrange_table();" />
	                        <input type="button" name="button" class="formbutton" value="Print GSD"  onClick="print_gsd_report();" />
	                        <!--<input type="button" name="button" class="formbutton" value="Print Line Layout" onClick="" />-->
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
        
        
            
    	<div id="gsd_entry_info_list" style="width:800px;">
        </div>
        <br>
     </form>
   </div>
  
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			