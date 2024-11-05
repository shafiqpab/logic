<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	12-05-2014
Updated by 		: 	Kausar 	(Creating print report)/ Jahid Page Bug/ Rakib
Update date		: 	11-01-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
//echo $_SESSION['iso_string'];die;
//echo $permission."=".$mid."=".$fnat;die;
$_SESSION['page_permission']=$permission;
//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = '';
if ($company_id >0) {
    $company_credential_cond = "and id in($company_id)";
}

//----------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Receive Info","../", 1, 1, $unicode,1,1); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][363] );
		echo "var field_level_data= ". $data_arr . ";\n";
	?>
	$('#txt_pass_id').focus(); 

	var receive_from = [<? echo substr(return_library_autocomplete( "select receive_from from  inv_gate_in_mst where  status_active=1 and is_deleted=0 group by receive_from", "receive_from" ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_receive_from").autocomplete({
			source: receive_from
		});
	});
	var carried_by = [<? echo substr(return_library_autocomplete( "select carried_by  from inv_gate_in_mst where  status_active=1 and is_deleted=0 group by carried_by", "carried_by" ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_carried_by").autocomplete({
			source: carried_by
		});
	}); 	
	var attention = [<? echo substr(return_library_autocomplete( "select attention  from inv_gate_in_mst where  status_active=1 and is_deleted=0 group by attention", "attention" ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_attention").autocomplete({
			source: attention
		});
	}); 
	var attention = [<? echo substr(return_library_autocomplete( "select attention  from inv_gate_in_mst where  status_active=1 and is_deleted=0 group by attention", "attention" ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_attention").autocomplete({
			source: attention
		});
	}); 	

	function set_pi_id(id_type_pi)
	{
		get_php_form_data(id_type_pi, "child_form_item_list", "requires/get_in_entry_controller");
	}

	function set_child_id(id)
	{
		get_php_form_data(id, "child_form_input_data", "requires/get_in_entry_controller");
	}

	function open_syspopup(page_link,title)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var company = $("#cbo_company_name").val();
		var cbo_group = $("#cbo_group").val();
		page_link='requires/get_in_entry_controller.php?action=sys_popup&company='+company+'&cbo_group='+cbo_group;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split("_"); // wo/pi number
			if (sysNumber!="")
			{				
				freeze_window(5);
				$("#txt_system_id").val(sysNumber[1]);
				$("#update_id").val(sysNumber[0]);
				reset_form('','','cboitemcategory_1*cbosample_1*txtitemdescription_1*txtcalanquantity_1*txtquantity_1*txtRejQuantity_1*cbouom_1*txtrate_1*txtamount_1*txtorder_1*txtremarks_1*fabriccolorid_1','','$(\'#cut_details_container tr:not(:first)\').remove();');
				get_php_form_data(sysNumber[0], "populate_master_from_data", "requires/get_in_entry_controller" );				
				show_list_view(sysNumber[0],'show_dtls_list_view','list_container','requires/get_in_entry_controller','');
				//disable_enable_fields( 'cbo_company_name*txt_pass_id*cbo_out_company*txt_receive_from*txt_challan_no', 1, "", "" );
				disable_enable_fields( 'cbo_company_name*txt_pass_id*cbo_out_company*cbo_group*cbo_com_location_id*cbo_out_location_id*cbo_party_type', 1, "", "" );
				set_button_status(0, permission, 'fnc_getin_entry',1,1);
				release_freezing();	 
			}
		}		
	}

	function fnc_getin_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			// alert(report_title);
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_out_company').val()+'*'+report_title+'*'+$('#cbo_com_location_id').val(), "get_in_entry_print", "requires/get_in_entry_controller" ) 
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][363]);?>')
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][363]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][363]);?>')==false)
				{
					return;
				}
			}
			if(cbo_group==1)
			{
				if( form_validation('cbo_group*cbo_company_name*cbo_com_location_id*txt_pass_id*txt_in_date*txt_start_minuties*txt_pass_id','Within Group*Company Name*Location*System Pass ID*In Date*In Time* Gate Pass ID')==false )
				{
					return;
				}
				
				var start_hours = $('#txt_start_hours').val();
				if(start_hours =='')
				{
					if( form_validation('txt_start_hours','In Time')==false )
					return;
				}
			}
			else
			{
				if( form_validation('cbo_group*cbo_company_name*cbo_com_location_id*txt_in_date*txt_start_minuties','Within Group*Company Name*Location*In Date*In Time')==false )
				{
					return;
				}

				var start_hours = $('#txt_start_hours').val();
				if(start_hours =='')
				{
					if( form_validation('txt_start_hours','In Time')==false )
					return;
				}				
			}			

			var row_num=$('#tbl_order_details tbody tr').length;
			var dataString = "txt_system_id*cbo_company_name*txt_pass_id*txt_basis_id*update_id*cbo_department_name*cbo_group*cbo_party_type*cbo_section*txt_in_date*txt_receive_from*txt_start_minuties*txt_start_hours*cbo_out_company*txt_challan_no*txt_carried_by*txt_reference_id*cbo_com_location_id*cbo_out_location_id*txt_out_date*cbo_returnable*txt_return_date*txt_attention*txt_party_challan*txt_vehicle_no*txt_loaded_weight*txt_unloaded_weight*txt_net_weight";
			
			var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string(dataString,"../");
			//alert(data1);return;
			var data2='';
			
			for(var i=1; i<=row_num; i++)
			{		
				var category=$('#cboitemcategory_'+i).val();
				var sample=$('#cbosample_'+i).val();
				var cbo_group=$('#cbo_group').val();
				var basis_id=$('#txt_basis_id').val();
				//alert(cbo_group);
				if(cbo_group==2)
				{
					if(category==0)
					{
						if(sample==0 )
						{
							if (form_validation('cboitemcategory_'+i,'Item Category')==false)
							{
								return;
							}
						}
					}
					else if(sample==0 )
					{
						if(category==0)
						{
							if (form_validation('cbosample_'+i,'Sample')==false)
							{
								return;
							}
						}
					}
				}
				else if(basis_id==51)
				{
					if (form_validation('cbouom_'+i,'UOM')==false)
					{
						return;
					}
				}
				else
				{
					if (form_validation('txtitemdescription_'+i+'*cbouom_'+i,'Item Description*UOM')==false)
					{
						return;
					}	
				}

				data2+=get_submitted_data_string('updatedtlsid_'+i+'*cboitemcategory_'+i+'*cbosample_'+i+'*txtitemdescription_'+i+'*txtcalanquantity_'+i+'*txtquantity_'+i+'*txtRejQuantity_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtorder_'+i+'*txtremarks_'+i+'*getpassdtlsid_'+i+'*fabriccolorid_'+i,"../",i);
			}
			var data=data1+data2;
			//alert(data1);
			//================= dynamic qnty field validation ========================
			var quantitychck=0;
			$('.required').each(function()
			{
				if( $(this).val()== "" && quantitychck!=1)
				{
					quantitychck=0;
				}
				else
				{
					quantitychck=1
				}
			});

			var rejquantitychck=0;
			$('.rejrequired').each(function()
			{
				if( $(this).val()== "" && rejquantitychck!=1)
				{
					rejquantitychck=0;
				}
				else
				{
					rejquantitychck=1
				}
			});
			
			if(quantitychck==0 && rejquantitychck==0)
			{
				alert('Please fill quantity field');
				$(this).focus();
				return;				
			}
			else
			{
				freeze_window(operation);        	
				http.open("POST","requires/get_in_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_getin_entry_reponse;
			}
		}
	}

	function fnc_getin_entry_reponse()
	{
		if(http.readyState == 4) 
		{	 	
			var reponse=trim(http.responseText).split('**');
			//alert(reponse);
			if(reponse[0]==0)
			{
				//alert();
				show_msg(trim(reponse[0]));
				$("#txt_system_id").val(reponse[1]);
				$("#update_id").val(reponse[2]);
				$("#updatedtlsid_1").val(reponse[3]);
				var grp_id=$("#cbo_group").val();
				//alert(grp_id);
				if(grp_id==2)
				{
					reset_form( '', '', '', '', '','cboitemcategory_1*cbosample_1*txtitemdescription_1*txtcalanquantity_1*txtquantity_1*txtRejQuantity_1*cbouom_1*txtrate_1*txtamount_1*txtorder_1*txtremarks_1*fabriccolorid_1','','$(\'#cut_details_container tr:not(:first)\').remove();' ) 
				}
				else
				{
					reset_form('','','cboitemcategory_1*cbosample_1*txtitemdescription_1*txtcalanquantity_1*txtquantity_1*txtRejQuantity_1*cbouom_1*txtrate_1*txtamount_1*txtorder_1*txtremarks_1*fabriccolorid_1','','$(\'#cut_details_container tr:not(:first)\').remove();');
				}

				var row_num=$('#tbl_order_details tbody tr').length;				
				for(var i=1; i<=row_num; i++)
				{				
					$('#cboitemcategory_'+i).val(0);
					$('#cbosample_'+i).val(0);
					$('#txtitemdescription_'+i).val('');
					$('#txtcalanquantity_'+i).val('');
					$('#txtquantity_'+i).val('');
					$('#txtRejQuantity_'+i).val('');
					$('#cbouom_'+i).val(0);
					$('#txtrate_'+i).val('');
					$('#txtamount_'+i).val('');
					$('#txtorder_'+i).val('');
					$('#txtremarks_'+i).val('');				
				}
				$("#tbl_order_details tbody tr:not(:first)").remove();
			}
			else if(reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				$("#txt_system_id").val(reponse[1]);
				$("#update_id").val(reponse[2]);
				var grp_id=$("#cbo_group").val();
				//alert(grp_id);
				if(grp_id==2)
				{
					reset_form( '', '', '', '', '','cboitemcategory_1*cbosample_1*txtitemdescription_1*txtcalanquantity_1*txtquantity_1*txtRejQuantity_1*cbouom_1*txtrate_1*txtamount_1*txtorder_1*txtremarks_1*fabriccolorid_1','','$(\'#cut_details_container tr:not(:first)\').remove();' ) 
				}
				else
				{
					reset_form('','','cboitemcategory_1*cbosample_1*txtitemdescription_1*txtcalanquantity_1*txtquantity_1*txtRejQuantity_1*cbouom_1*txtrate_1*txtamount_1*txtorder_1*txtremarks_1*fabriccolorid_1','','$(\'#cut_details_container tr:not(:first)\').remove();');
				}	
				
				var row_num=$('#tbl_order_details tbody tr').length;				
				for(var i=1; i<=row_num; i++)
				{				
					$('#cboitemcategory_'+i).val(0);
					$('#cbosample_'+i).val(0);
					$('#txtitemdescription_'+i).val('');
					$('#txtcalanquantity_'+i).val('');
					$('#txtquantity_'+i).val('');
					$('#txtRejQuantity_'+i).val('');
					$('#cbouom_'+i).val(0);
					$('#txtrate_'+i).val('');
					$('#txtamount_'+i).val('');
					$('#txtorder_'+i).val('');
					$('#txtremarks_'+i).val('');				
				}

				$("#tbl_order_details tbody tr:not(:first)").remove();				
			}
			else if(reponse[0]==2)
			{
				//reset_form('get_in_1','list_container','','','','');
				disable_enable_fields( 'cbo_company_name*cbo_item_category*txt_pi_wo_req*cbo_supplier', 0, "", "" );
				//set_button_status(0, permission, 'fnc_getin_entry',1,1);
				show_msg(trim(reponse[0]));
				show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/get_in_entry_controller','');
				reset_form('','','cboitemcategory_1*cbosample_1*txtitemdescription_1*txtcalanquantity_1*txtquantity_1*txtRejQuantity_1*cbouom_1*txtrate_1*txtamount_1*txtorder_1*txtremarks_1*fabriccolorid_1','','$(\'#cut_details_container tr:not(:first)\').remove();');
				release_freezing();
				return;
			}
			else if(reponse[0]==10 || reponse[0]==15)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==30)
			{
				alert('Gate in Quantity is more than pass Quantity');
				release_freezing();
				return;
			}

			else if(reponse[0]==40)
			{
				alert('duplicate Item Category found...');
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/get_in_entry_controller','');			
			set_button_status(0, permission, 'fnc_getin_entry',1,1);
			release_freezing();
		}
	}

	function open_piworeq()
	{
		var cbo_group = $("#cbo_group").val();
		var company = $("#cbo_company_name").val();	
		var item_category = $("#cbo_item_category").val();	
		var page_link='requires/get_in_entry_controller.php?action=piworeq_popup&company='+company+'&item_category='+item_category+'&cbo_group='+cbo_group; 
		var title="Search PI/WO/REQ Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{			
			var theform=this.contentDoc.forms[0];
			
			var pi_wo_req_req_id=this.contentDoc.getElementById("hidden_tbl_id").value; // pi wo req id
			var hidden_pi_wo_req_id = pi_wo_req_req_id.split("_");
			//alert(pi_wo_req_req_id+'klkjj'+hidden_pi_wo_req_id[0]);
			$("#txt_pass_id").val(hidden_pi_wo_req_id[0]);
			//alert(pi_wo_req_id);
			$("#txt_basis_id").val(hidden_pi_wo_req_id[1]);
			disable_enable_fields( 'txt_pass_id*cbo_out_company*txt_receive_from*txt_challan_no*cbo_out_location_id*txt_return_date*txt_attention*txt_carried_by*cbo_department_name*txt_out_date*cbo_returnable*cbo_group', 1, "", "" );
			get_php_form_data(hidden_pi_wo_req_id[0]+'**'+company, "populate_main_from_data", "requires/get_in_entry_controller");
			show_list_view(hidden_pi_wo_req_id[0]+'**'+hidden_pi_wo_req_id[2],'show_product_listview','cut_details_container','requires/get_in_entry_controller','');				
		}
	}

	//amount calculate
	function fn_calculate_amount(id)
	{
		var id=id.split('_');
		var challan_quantity=$("#txtcalanquantity_"+id[1]).val();
		var quantity=$("#txtquantity_"+id[1]).val();
		var rate = $("#txtrate_"+id[1]).val();
		var  amount;

		if(challan_quantity!='')
		{
			var amount=challan_quantity*rate*1;
		}
		else if(quantity!='')
		{
			amount=quantity*rate*1;
		}
		//alert(amount);
		$("#txtamount_"+id[1]).val(number_format_common(amount,"","",7));
	}

	function fn_check_quantity(id)
	{
		var placeholder_value=$("#txtquantity_"+id).attr("placeholder");
		var field_value=$("#txtquantity_"+id).val();
		if(field_value*1 > placeholder_value*1)
		{
			alert("Qnty Excceded by"+(placeholder_value-field_value));
			$("#txtquantity_"+id).val('');
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

	//form reset/refresh function here
	function fnResetForm()
	{
		$("#tbl_master").find('input,select').attr("disabled", false);	
		disable_enable_fields( 'cbo_uom', 0, "", "" );
		set_button_status(0, permission, 'fnc_getin_entry',1,0);
		reset_form('get_in_1','list_container*items_list_view','','','','txt_start_hours*txt_start_minuties*txt_in_date*txt_out_date');		
	}

	function change_td_value(value)
	{
		//alert(value);
		$('#txt_pass_id').val('');
		$('#cbo_party_type').val(0);
		if(value==1)
		{
			$("#cbo_out_location_id").attr("disabled",false);
			$("#cbo_party_type").attr("disabled",false);
			$("#txt_pass_id").attr("disabled",false);
			//$("#txt_receive_from").prop("readonly",true);
			$("#txt_challan_no").prop("readonly",true);
			$("#txt_receive_from").prop("readonly",true);
			//$('#txt_receive_from').attr('disabled','disabled');
			$("#txt_party_challan").attr("disabled",true);
		}
		else 
		{			
			$("#cbo_out_location_id").attr("disabled",true);
			$("#cbo_party_type").attr("disabled",false);
			$("#txt_pass_id").attr("disabled",false);
			$("#cbosample_1").attr("disabled",false);
			$("#cbouom_1").attr("disabled",false);
			$("#cboitemcategory_1").attr("disabled",false);
			$("#txtitemdescription_1").attr("disabled",false);
			$("#txtcalanquantity_1").attr("disabled",false);
			$("#txtquantity_1").attr("disabled",false);
			$("#txtRejQuantity_1").attr("disabled",false);
			//$("#get_pass_td").removeClass();
			$("#txtrate_1").attr("disabled",false);
			$("#txtamount_1").attr("disabled",false);
			$("#txtorder_1").attr("disabled",false);
			$("#cbobuyer_1").attr("disabled",false);
			$("#txstyle_1").attr("disabled",false);
			$("#txtremarks_1").attr("disabled",false);
			$("#fabriccolorid_1").attr("disabled",false);
			$("#txt_challan_no").prop("readonly",false);
			$("#txt_receive_from").prop("readonly",false);
			$("#txt_party_challan").attr("disabled",false);
		}		
	}

	function gate_in_scan(str)
	{
		get_php_form_data(str, "populate_main_from_data", "requires/get_in_entry_controller");
		show_list_view(str,'show_product_listview','cut_details_container','requires/get_in_entry_controller','');
		disable_enable_fields( 'txt_pass_id*cbo_out_company*txt_receive_from*txt_challan_no', 1, "", "" );
	}

	$('#txt_pass_id').live('keydown', function(e){
		if (e.keyCode === 13) {
			e.preventDefault();
			gate_in_scan(this.value);
		}
	});

	function focace_change()
	{
		$('#txt_pass_id').focus();  
	}
	
	function gate_enable_disable(type)
	{
		var category=$("#cboitemcategory_1").val();
		var sample_id=$("#cbosample_1").val();
		// alert(category);
		var cbo_group=$('#cbo_group').val();
		
		if(cbo_group==2)
		{
			if(type==1)
			{
				if(category!=0)
				{
					$("#cbosample_1").attr("disabled",true); 
				}
				else
				{
					$("#cbosample_1").attr("disabled",false); 
				}
			}
			else
			{
				if(sample_id!=0)
				{
					$("#cboitemcategory_1").attr("disabled",true); 
				}
				else
				{
					$("#cboitemcategory_1").attr("disabled",false); 
				}
			}
		}
	}

	function open_refpopup(page_link,title)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var company = $("#cbo_company_name").val();
		var cbo_party_type = $("#cbo_party_type").val();
		var cbo_out_company = $("#cbo_out_company").val();
		page_link='requires/get_in_entry_controller.php?action=refpopup&company='+company+'&cbo_party_type='+cbo_party_type+'&cbo_out_company='+cbo_out_company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var sysNumber=(this.contentDoc.getElementById("hidden_sys_number").value).split("_"); // wo/pi number
			//alert(sysNumber);
			if (sysNumber!="")
			{
				freeze_window(5);
				disable_enable_fields( 'cbo_company_name*txt_pass_id*cbo_out_company', 1, "", "" );
				//alert(sysNumber[6]);
				show_list_view(sysNumber[0]+'*'+sysNumber[1]+'*'+sysNumber[2]+'*'+sysNumber[3]+'*'+sysNumber[4]+'*'+sysNumber[5]+'*'+sysNumber[6],'items_list_view_action','items_list_view','requires/get_in_entry_controller',''); //new for item list view
				//get_php_form_data(sysNumber[0], "data_populate_from_side_list", "requires/get_in_entry_controller" );
				$("#txt_reference_id").val(sysNumber[3]);
				release_freezing();
			}
		}		
	}

	/*function show_item_list(id)
	{
		var id=id;
		show_list_view(id,'items_list_view_action','items_list_view','requires/get_in_entry_controller','setFilterGrid("list_view",-1)');
	}*/
	
	function returnable_item_pupup() 
	{
		if( form_validation('txt_system_id*txt_pass_id','System ID*Gate Pass ID')==false )
		{
			return;
		}	
		
		if($("#cbo_returnable").val()==1)
		{
			var cbo_company = $("#cbo_company_name").val();
			var txt_pass_id = $("#txt_pass_id").val();
			var gate_in_system_id = $("#txt_system_id").val();

			page_link='requires/get_in_entry_controller.php?action=returnable_item_dtls_pupup&cbo_company='+cbo_company+'&txt_pass_id='+txt_pass_id+'&gate_in_system_id='+gate_in_system_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Returnable Item Popup', 'width=900px, height=350px, center=1, resize=0, scrolling=0','');
			emailwindow.onclose=function(){}
		}
		else
		{
			alert("Returnable=No");return;
		}
	}

	function calculateNetWeight()
	{
		var txt_loaded_weight = $('#txt_loaded_weight').val() * 1;
		var txt_unloaded_weight = $('#txt_unloaded_weight').val() * 1;

		if(txt_unloaded_weight > txt_loaded_weight )
		{
			alert('Unloaded Weight can not be greater than Loaded Weight');
			$('#txt_unloaded_weight').val(0);
			txt_unloaded_weight = 0;
			//return;
		}

		var txt_net_weight = txt_loaded_weight - txt_unloaded_weight ;

		$('#txt_net_weight').val(txt_net_weight * 1);
	}
</script>
</head>

<body onLoad="set_hotkey();focace_change();change_td_value(document.getElementById('cbo_group').value);">
<div style="width:1310px;" align="left">
	<? echo load_freeze_divs ("../",$permission);  ?><br />    		 
    <form name="get_in_1" id="get_in_1" autocomplete="off" > 
    <div style="width:100%;">     
		<fieldset style="width:1300px; float:left;">
			<legend>Gate In</legend>
			<div style="width:65%; float: left;">
				<fieldset style="width:880px;" style="float:left;">
					<table  width="870" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
						<tr>
							<td colspan="6" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>System ID</b>
								<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_syspopup()" readonly />
							</td>
						</tr>
						<tr>
							<td align="right" class="must_entry_caption">Within Group</td>
							<td >
								<? 
									echo create_drop_down( "cbo_group", 170, $yes_no,"", 1, "-- Select Group --", 2, "change_td_value(this.value)",0 );
								?>
							</td>
						
							<td width="130" align="right" class="must_entry_caption" id='get_pass_td'>Gate Pass ID</td>
							<td width="170">
							<input type="text" name="txt_pass_id" id="txt_pass_id" class="text_boxes" style="width:160px" placeholder="Double Click Or Scan" onDblClick="open_piworeq()" disabled />
							<input type="hidden" name="txt_basis_id" id="txt_basis_id" class="text_boxes" style="width:160px"  disabled />
							</td>
							<td width="100" align="right">Party Type</td>
							<td width="170" id="">
							<? 
							$party_type_arr=array(1=>"Buyer",2=>"Supplier",3=>"Other Party");
							echo create_drop_down( "cbo_party_type", 170, $party_type_arr,"", 1, "-- Select Party Type --", $selected, "load_drop_down( 'requires/get_in_entry_controller', this.value, 'load_drop_down_sent', 'sent_td');",0 );
							?>
							</td>
						</tr>
						<tr>
							<td  width="130" align="right" class="must_entry_caption">Company Name </td>
							<td width="170">
								<?										
								echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/get_in_entry_controller',this.value, 'load_drop_down_com_location', 'com_location_td' );focace_change()" );
                                ?>
							</td>
								<td align="right" class="must_entry_caption">Location</td>
								<td id="com_location_td" >
								<? 
									echo create_drop_down( "cbo_com_location_id", 170, $blank_array,"", 1, "-- Select  --", 0, "",0 );
									?>
								</td>									
							<td  width="130" align="right">Receive From</td>
							<td width="170">
								<input type="text" name="txt_receive_from" id="txt_receive_from" class="text_boxes" style="width:160px" placeholder="write"    />
									<input type="hidden" id="update_id" name="update_id" value="" />
							</td>
						</tr>
						<tr>									
							<td width="100" align="right">Out Company</td>
							<td width="170" id="sent_td">
							<? 
								echo create_drop_down( "cbo_out_company", 172, $blank_array,"", 1, "-- Select  --", 0, "",0 );
							?>
							</td>
							<td align="right" >Out Location</td>
							<td id="out_location_td" >
							<? 
								echo create_drop_down( "cbo_out_location_id", 170, $blank_array,"", 1, "-- Select  --", 0, "",1 );
								?>
							</td>
							<td width="94" align="right" >Department</td>
							<td width="160" >
							<? 
								echo create_drop_down( "cbo_department_name", 172, "select id,department_name from  lib_department  where status_active=1 and is_deleted=0  order by department_name","id,department_name", 1, "-- Select Department --", $selected,"","0" );
							?>
							</td>									
						</tr>
						<tr>								
							<td width="130" align="right" class="must_entry_caption">In  Date</td>
							<td id="lc_no" width="170"><input type="text" name="txt_in_date" id="txt_in_date" class="datepicker" style="width:160px;" value="<? echo date("d-m-Y"); ?>" placeholder="Select Date" /></td>
							<td width="130" align="right" class="must_entry_caption">In Time</td>
							<td width="170">
									<input type="text" name="txt_start_hours" id="txt_start_hours" class="text_boxes_numeric" placeholder="Hours" style="width:70px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_hours','txt_start_minuties',2,23);"  value="<? echo date('H');?>"  />
									<input type="text" name="txt_start_minuties" id="txt_start_minuties" class="text_boxes_numeric" placeholder="Minutes" style="width:70px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_minuties','txt_start_date',2,59)"  value="<? echo date('i');?>"  />
									
							</td>
							<td width="130" align="right" >Section</td>
							<td width="170" id="section_td">
								<? 
									echo create_drop_down( "cbo_section", 170, "select id,section_name from  lib_section where status_active=1 order by section_name","id,section_name",1, "-- Select --", 0, "" ); 
								?>
							</td>
						</tr>
						<tr>									
							<td width="130" align="right" class="must_entry_caption">Out  Date</td>
							<td id="lc_no" width="170"><input type="text" name="txt_out_date" id="txt_out_date" class="datepicker" style="width:160px;" value="<? echo date("d-m-Y"); ?>" placeholder="Select Date" /></td>
								
							<td align="right">Returnable</td>
							<td>
								<? 
									echo create_drop_down( "cbo_returnable", 170, $yes_no,"", 1, "-- Select  --", 2, "",0 );
								?>
							</td>
							<td align="right" >Est. Return Date</td>
							<td >
								<input class="datepicker" type="text" style="width:160px;" name="txt_return_date" id="txt_return_date"  placeholder="Select Date" />
							</td>										
						</tr>
						<tr>									
							<td align="right" >Attention</td>
							<td >
								<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:160px;"  />
							</td>
							
							<td width="94" align="right" >Challan no</td>
							<td width="160">
								<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Write"   />
							</td>
							
								<td width="94" align="right" >Carried By</td>
							<td width="160">
								<input type="text" name="txt_carried_by" id="txt_carried_by" class="text_boxes" style="width:160px" placeholder="Write"   />
							</td>
						</tr>
						<tr>
							<td align="right">PI/WO/REQ Reference </td>
							<td width="160">
							<input type="text" name="txt_reference_id" id="txt_reference_id" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_refpopup();"/>
							</td>
							<td align="right">Party Challan </td>
							<td width="160">
							<input type="text" name="txt_party_challan" id="txt_party_challan" class="text_boxes" style="width:160px" />
							</td>
							<td align="right"> Vehicle No </td>
							<td width="160">
							<input type="text" name="txt_vehicle_no" id="txt_vehicle_no" class="text_boxes" style="width:160px" />
							</td>
						</tr>
						<tr>
							<td align="right">Loaded Weight</td>
							<td width="160">
								<input type="text" name="txt_loaded_weight" id="txt_loaded_weight" class="text_boxes_numeric" style="width:160px" placeholder="Write" onKeyUp="calculateNetWeight()">
							</td>
							<td align="right">Unloaded Weight</td>
							<td width="160">
								<input type="text" name="txt_unloaded_weight" id="txt_unloaded_weight" class="text_boxes_numeric" style="width:160px" placeholder="Write" onKeyUp="calculateNetWeight()" />
							</td>
							<td align="right">Net Weight</td>
							<td width="160">
								<input type="text" name="txt_net_weight" id="txt_net_weight" class="text_boxes" style="width:160px" readonly disabled />
							</td>
						</tr>									
					</table>							
				</fieldset>
			</div>
			<div style="width:35%; float: right;">
				<div id="items_list_view" style="float:right;overflow-x: scroll;">
				</div>
			</div>
			<table width="1200" cellpadding="0" cellspacing="2" border="0" class="rpt_table" align="center" id="tbl_order_details">
				<thead>
					<th width="120" align="center" >Item Category</th>
					<th width="100" align="center" >Sample</th>
					<th width="200" align="center" class="must_entry_caption" >Item Description</th>
					<th width="60" align="center" >Challan Qty.</th>
					<th width="60" align="center" class="must_entry_caption">Quantity</th>
					<th width="60" align="center">Reject Qty</th>
					<th width="60" align="center" class="must_entry_caption">UOM</th>
					<!--  <th width="60" align="center">UOM Qty</th>-->
					<th width="60" align="center" >Rate</th>
					<th width="80" align="center">Amount</th>
					<th width="80" align="center">Buyer Order</th>
					<th width="80" align="center">Buyer</th>
					<th width="80" align="center">Style</th>
					<th width="150" align="center">Remarks</th>
				</thead>
				<tbody id="cut_details_container">
					<tr id="tr_1">
					<td>
						<? 
							echo create_drop_down( "cboitemcategory_1", 120,$item_category,"",1, "-- Select --", 0, "gate_enable_disable(1)",0 ); 
						?>
					</td>
					<td>
						<? 
						echo create_drop_down( "cbosample_1", 100, "select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name",1, "-- Select --", 0, "gate_enable_disable(2)" ,0); 
						?> 
					</td>
					<td><input type="text" name="txtitemdescription_1" id="txtitemdescription_1" class="text_boxes" style="width:200px;" ></td>
					<td><input type="text" name="txtcalanquantity_1" id="txtcalanquantity_1" class="text_boxes_numeric" onKeyUp="fn_calculate_amount(this.id)" style="width:60px;" ></td>
					<td><input type="text" name="txtquantity_1" id="txtquantity_1" class="text_boxes_numeric required" onKeyUp="fn_calculate_amount(this.id)"  style="width:60px;" ></td>
					<td><input type="text" name="txtRejQuantity_1" id="txtRejQuantity_1" class="text_boxes_numeric rejrequired" style="width:60px;" ></td>
					<td><? echo create_drop_down( "cbouom_1", 60, $unit_of_measurement,"", 1, "Select", $selected, "",0 ); ?></td>
					<!--<td><input type="text" name="txtuomqty_1" id="txtuomqty_1" class="text_boxes"  style="width:60px;" ></td>-->
					<td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" onKeyUp="fn_calculate_amount(this.id)" style="width:60px" ></td>
					<td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:80px"  ></td>
					<td><input type="text" name="txtorder_1" id="txtorder_1" class="text_boxes" style="width:80px"   ></td>

					<td><input type="text" name="cbobuyer_1" id="cbobuyer_1" class="text_boxes" style="width:80px"   ></td>
					<td><input type="text" name="txstyle_1" id="txstyle_1" class="text_boxes" style="width:80px"   ></td>

					<td><input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" style="width:150px">
						<input type="hidden" id="updatedtlsid_1" name="updatedtlsid_1" value="" />
						<input type="hidden" id="getpassdtlsid_1" name="getpassdtlsid_1" value="" />
						<input type="hidden" id="fabriccolorid_1" name="fabriccolorid_1" value="" />
					</td>
					</tr>
				</tbody>
			</table>
			<table cellpadding="0" cellspacing="1" width="100%">
				<tr> 
					<td colspan="6" align="center"></td>				
				</tr>
				<tr>
					<td align="center" colspan="6" valign="middle" class="button_container" style="border-top: 0px;!important">
						<? echo load_submit_buttons( $permission, "fnc_getin_entry", 0,1,"fnResetForm()",1);?>
						<input type="button" name="returnable_item_dtls" value="Returnable Item Details" id="returnable_item_dtls" class="formbutton_disabled" onClick="returnable_item_pupup()"/>
					</td>
				</tr> 
			</table>
		</fieldset>
		<fieldset>
			<div style="width:1080px;" id="list_container"></div>
		</fieldset>
    </div>  
	</form>
</div>    
</body>  
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
