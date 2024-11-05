<?
/************************************************Comments************************************
Purpose			: 	This form will create Dyes And Chemical Receive Entry
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	17-08-2023
Updated by 		: 		
Update date		: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
**********************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

$user_id = $_SESSION['logic_erp']['user_id'];



// user credential data prepare start

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, company_location_id, supplier_id, buyer_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

$company_credential_cond=$store_location_credential_cond=$company_location_credential_cond="";
if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id) ";
}
if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}
if ($company_location_id !='') {
    $company_location_credential_cond = " and id in($company_location_id)";
}

echo load_html_head_contents("Dyes And Chemical Issue","../../", 1, 1, $unicode,1,1); 
//--------------------------------------------------------------------------------------------------------------------
// last yarn receive exchange rate, currency,store name
/*$sql = sql_select("select store_id,max(id) from inv_issue_master where item_category in(5,6,7)");
$storeName=$exchangeRate=$currencyID=0;
foreach($sql as $row)
{
	$storeName=$row[csf("store_id")];
}*/

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var mandatory_field_arr="";
	//field level access
	<? 
	if($_SESSION['logic_erp']['data_arr'][5] !="")
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][5] );
		echo "var field_level_data= ". $data_arr . ";\n";
	} 
	if($_SESSION['logic_erp']['mandatory_field'][5]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][5] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
	?>
	
	function rcv_basis_reset()
	{
		document.getElementById('cbo_issue_basis').value=0;
		reset_form('chemicaldyesissue_1','list_container_recipe_items*list_product_container','','','','txt_issue_date*cbo_dying_source*cbo_company_name*cbo_dying_company*cbo_issue_basis');
		fn_independent(0,0);
	}
	
	// popup for WO/PI----------------------	
	
	// enable disable field for independent
	function fn_independent(val,issue_purpose)
	{
		if(issue_purpose==69)
		{
			$("#txt_req_no").attr("disabled",false);
			$('#txt_req_no').removeAttr('placeholder','No Need');
			$('#txt_req_no').attr('placeholder','Double Click');
			
			$('#txt_batch_id').val("");
			$('#txt_batch_no').val("");
			$('#txt_ext_no').val("");
			$('#txt_batch_weight').val("");
			$('#txt_tot_liquor').val("");
			
			$("#txt_batch_no").attr("disabled",true);
			$('#txt_batch_no').attr('placeholder','No Need');
		}
		
		else if(val==0)
		{
			$('#txt_req_id').val("");
			$('#txt_req_no').val("");
			
			$("#txt_req_no").attr("disabled",false);
			$('#txt_req_no').removeAttr('placeholder','No Need');
			$('#txt_req_no').attr('placeholder','Double Click');
			
			$('#txt_batch_id').val("");
			$('#txt_ext_no').val("");
			$('#txt_batch_no').val("");
			$('#txt_batch_weight').val("");
			$('#txt_tot_liquor').val("");
			
			$("#txt_batch_no").attr("disabled",false);
			$('#txt_batch_no').removeAttr('placeholder','No Need');
			$('#txt_batch_no').attr('placeholder','Double Click');
		}
		else if(val==4)
		{
			$('#txt_req_id').val("");
			$('#txt_req_no').val("");
			
			$("#txt_req_no").attr("disabled",true);
			$('#txt_req_no').attr('placeholder','No Need');
			
			$('#txt_batch_id').val("");
			$('#txt_batch_no').val("");	
			$('#txt_ext_no').val("");
			$('#txt_batch_weight').val("");
			$('#txt_tot_liquor').val("");
			
			$("#txt_batch_no").attr("disabled",true);
			$('#txt_batch_no').attr('placeholder','No Need');
			$("#cbo_store_name").attr("disabled",false);
		}
		else if(val==7)
		{
			$("#txt_req_no").attr("disabled",false);
			$('#txt_req_no').removeAttr('placeholder','No Need');
			$('#txt_req_no').attr('placeholder','Double Click');
			
			$('#txt_batch_id').val("");
			$('#txt_batch_no').val("");
			$('#txt_ext_no').val("");
			$('#txt_batch_weight').val("");
			$('#txt_tot_liquor').val("");
			
			$("#txt_batch_no").attr("disabled",true);
			$('#txt_batch_no').attr('placeholder','No Need');
		}
		//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
		reset_form('chemicaldyesissue_1','list_container_recipe_items*list_product_container','','','','txt_issue_date*cbo_dying_source*cbo_company_name*cbo_dying_company*cbo_issue_basis*cbo_issue_purpose');
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/chemical_dyes_issue_v2_controller.php?data=" + data+'&action='+action, true );
	}
	function fnc_chemical_dyes_issue_entry(operation)
	{
		
		if(operation==4)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_print','requires/chemical_dyes_issue_v2_controller');
			return;
		}
		else if(operation==5)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_print_single_com','requires/chemical_dyes_issue_v2_controller');
			return;
		}
		else if(operation==6)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_print_single_com2','requires/chemical_dyes_issue_v2_controller');
			return;
		}
		else if(operation==7)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			if(confirm(' Press OK to show With rate and value.\n Press Cancel to show without rate and value.')){
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val()+'*'+1,'delivery_challan_print','requires/chemical_dyes_issue_v2_controller');
				
			}
			else{
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val()+'*'+0,'delivery_challan_print','requires/chemical_dyes_issue_v2_controller');
			}
			return;
		}
		 
		else if(operation==0 || operation==1 || operation==2)
		{
			 
			var row_num=$('#tbl_list_search tbody tr').length;
			//alert(row_num);return;
			if( $("#cbo_issue_basis").val()==7)
			{
				var k=0;var msg="";
				for(var i=1; i<row_num; i++)
				{
					if( ($('#txt_reqn_qnty_edit_'+i).val()*1)!=0)
					{
						if($('#transId_'+i).val()*1!="")
						{
							if(($('#stock_qty_'+i).val()*1+$('#hidtxt_reqn_qnty_edit_'+i).val()*1)<($('#txt_reqn_qnty_edit_'+i).val()*1))
							{
								msg ="Issue Qnty Over Stock Qnty Not Allowed \n";
							}
						}
						else
						{
							if(($('#stock_qty_'+i).val()*1)<($('#txt_reqn_qnty_edit_'+i).val()*1))
							{
								msg ="Issue Qnty Over Stock Qnty Not Allowed \n";
							}
						}
						
						if( ($('#hidtxt_reqn_qnty_edit_'+i).val()*1)==0)
						{
							msg ="Issue Qnty zero Not Allowed \n";
						}
						if(($('#txt_reqn_qnty_edit_'+i).val()*1)>($('#txt_reqn_qnty_'+i).val()*1))
						{
							msg ="Issue Qnty Over The Requisition Qnty Not Allowed \n";
						}
					}
				}
			}
			
			if( $("#cbo_issue_basis").val()==7 && $("#cbo_issue_purpose").val()==56)
			{
				if( form_validation('cbo_dying_company*cbo_company_name*cbo_issue_basis*txt_issue_date*cbo_store_name*cbo_issue_purpose','Company Name*LC Company*Issue Basis*Issue Date*Store Name*Issue Purpose')==false )
				{
					return;
				}
			}
			else 
			{
				if( form_validation('cbo_dying_company*cbo_company_name*cbo_issue_basis*txt_issue_date*cbo_store_name*cbo_issue_purpose','Company Name*LC Company*Issue Basis*Issue Date*Store Name*Issue Purpose')==false )
				{
					return;
				}	
			}
			
			

			//if('<?// echo implode('*',$_SESSION['logic_erp']['mandatory_field'][5]); ?>')
			//{
				//if (form_validation('<?// echo implode('*',$_SESSION['logic_erp']['mandatory_field'][5]); ?>','<?// echo implode('*',$_SESSION['logic_erp']['mandatory_message'][5]); ?>')==false) {return;}
			//}

			
			var current_date = '<? echo date("d-m-Y"); ?>';
			if (date_compare($('#txt_issue_date').val(), current_date) == false) 
			{
				alert("Issue Date Can not Be Greater Than Current Date");
				return;
			}
			
			const btn = (operation==0) ? document.getElementById('save1') : document.getElementById('update1');
			btn.disabled=true; 
			//*cbo_sub_process  
			
			var dataString = "txt_mrr_no*update_id*cbo_company_name*cbo_location_name*cbo_issue_basis*txt_req_no*txt_req_id*txt_batch_no*txt_batch_id*cbo_issue_purpose*cbo_loan_party*cbo_dying_source*cbo_dying_company*txt_challan_no*txt_issue_date*txt_recipe_no*txt_recipe_id*txt_order_no*hidden_order_id*cbo_buyer_name*txt_style_no*cbo_store_name*txt_machine_name*txt_machine_id*txt_return*txt_attention*variable_lot*cbo_issue_category";
			
			var dtls_data="";
			for (var i=1; i<row_num; i++)
			{
				if(($('#txt_reqn_qnty_edit_'+i).val()*1)>0)
				{
					//mst_data=mst_data+get_submitted_data_string('txt_prod_id_'+i+'*txt_lot_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*txt_reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*transId_'+i+'*hidtxt_reqn_qnty_edit_'+i+'*txt_remarks_'+i,"../../",2);
					if($('#cbo_issue_basis').val()==4)
					{
						dtls_data+='*txt_prod_id_'+i+'*txt_lot_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*txt_reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*transId_'+i+'*hidtxt_reqn_qnty_edit_'+i+'*txt_remarks_'+i+'*txt_sub_process_id_'+i+'*cbo_pack_type_'+i+'*no_pack_qty_'+i;
					}
					else{
						dtls_data+='*txt_prod_id_'+i+'*txt_lot_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*txt_reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*transId_'+i+'*hidtxt_reqn_qnty_edit_'+i+'*txt_remarks_'+i+'*txt_sub_process_id_'+i;
					}										
				}
			}
			
			if(dtls_data=="")
			{
				alert("No Data");return;
			}
			dataString=dataString+dtls_data;
			var mst_data=get_submitted_data_string(dataString,"../../");
			//alert(mst_data);return;
			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+mst_data;
			freeze_window(operation);
			http.open("POST","requires/chemical_dyes_issue_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_chemical_dyes_issue_entry_reponse;
		}
	}
	
	function fnc_chemical_dyes_issue_entry_reponse()
	{	
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			
			if (reponse[0] * 1 == 20 * 1) {
				alert(reponse[1]);
				const btn = (reponse[0]==1) ?  document.getElementById('update1') :document.getElementById('save1');
				btn.disabled=false;
				release_freezing();return;
			}
			show_msg(reponse[0]);
			if(reponse[0]==0 || reponse[0]==1)
			{
				disable_enable_fields( 'cbo_company_name*cbo_issue_basis*cbo_dying_source*cbo_dying_company*txt_req_no', 1, '', '' );
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_mrr_no').value = reponse[2];
				$('#cbo_company_name').attr('disabled','disabled');
				$('#txt_issue_date').attr('disabled','disabled');
				reset_form('','list_product_container','','','','');
				set_button_status(1, permission, 'fnc_chemical_dyes_issue_entry',1,1);	
			}
			else if(reponse[0]==2)
			{
				fnResetForm();
			}
			
			$("#btn_cost_print").removeClass("formbutton_disabled");
			$("#btn_cost_print").addClass("formbutton");
			const btn = (reponse[0]==1) ?  document.getElementById('update1') :document.getElementById('save1');
			btn.disabled=false;
			release_freezing();	
		}
		
	}
	
	function fnResetForm()
	{
		fn_independent(0,0)
		set_button_status(0, permission, 'fnResetForm',1);
		location.reload();
		//reset_form('chemicaldyesissue_1','recipe_items_list_view*list_product_container','','','','');
	}
	
	function open_reqpopup()
	{
		//reset_form('','list_container_recipe_items*recipe_items_list_view','','','','');
		var dying_source=$('#cbo_dying_source').val();
		if(dying_source==3)
		{
			alert('Out Bound Subcontract not allowed');
			$('#cbo_dying_source').val(0);
			$('#cbo_dying_company').val(0);
			return;
		}
		if( form_validation('cbo_dying_company*cbo_issue_basis*cbo_issue_purpose','Working Company*Issue Basis*Issue Purpose')==false )
		{
			return;
		}
		var company = $("#cbo_dying_company").val();
		var issue_purpose = $("#cbo_issue_purpose").val();	
		var issue_basis = $("#cbo_issue_basis").val();	
		var store_id = $("#cbo_store_name").val();
		if(issue_purpose==69 && issue_basis==4)
		{
			var page_link='requires/chemical_dyes_issue_v2_controller.php?action=lab_dip_popup&company='+company+'&issue_purpose='+issue_purpose+'&issue_basis='+issue_basis+'&store_id='+store_id;
			//alert(page_link);return; 
			var title="Search Requisition Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var reqNumber=this.contentDoc.getElementById("hidden_requ_no").value; // mrr number
				var reqId=this.contentDoc.getElementById("hidden_requ_id").value; // mrr number
				var lc_comp_id=this.contentDoc.getElementById("hidden_lc_company_id").value;
				var req_store_id=this.contentDoc.getElementById("hidden_store_id").value;
	
				$("#txt_req_no").val(reqNumber);
				$("#txt_req_id").val(reqId);
				$("#cbo_store_name").val(req_store_id);
				//alert(lc_comp_id);
				if(lc_comp_id) $("#cbo_company_name").val(lc_comp_id);
				$('#cbo_company_name').attr('disabled','disabled');
				document.getElementById("list_container_recipe_items").innerHTML = "";
				
				//show_list_view(reqId+'**'+company+"**"+issue_purpose+"**"+req_store_id+"**"+update_id+"**"+variable_lot+"**"+issue_basis, 'machine_item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_v2_controller', '');
				
				  
				
				var is_update=0;
				var sub_process_id= 0;
				var txt_recipe_id= $('#txt_recipe_id').val();
				var txt_batch_weight= $('#txt_batch_weight').val();
				var txt_tot_liquor= $('#txt_tot_liquor').val();
				var hidden_posted_account=$("#hidden_posted_account").val();
				var variable_lot=$("#variable_lot").val();
				
				show_list_view(company+'**'+sub_process_id+"**"+txt_recipe_id+"**"+txt_batch_weight+"**"+issue_purpose+"**"+issue_basis+"**"+is_update+"**"+reqId+"**"+req_store_id+"**"+hidden_posted_account+"**"+variable_lot, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_v2_controller', '');
				
				var tableFilters = 
				{
					col_0: "none",
					col_8: "none",
					col_9: "none",
					col_10: "none",
					col_11: "none",
					col_12: "none",
					col_13: "none",
					col_14: "none",
					col_15: "none",
					col_16: "none",
					col_17: "none"
				}
				setFilterGrid("tbl_list_search",-1,tableFilters);
			}
		}
		else
		{
			var page_link='requires/chemical_dyes_issue_v2_controller.php?action=req_popup&company='+company+'&issue_purpose='+issue_purpose+'&issue_basis='+issue_basis+'&store_id='+store_id;
			//alert(page_link);return; 
			var title="Search Requisition Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var reqNumber=this.contentDoc.getElementById("hidden_requ_no").value; // mrr number
				var reqId=this.contentDoc.getElementById("hidden_requ_id").value; // mrr number
				var batchId=this.contentDoc.getElementById("hidden_batch_id").value; // mrr number
				var receipeId=this.contentDoc.getElementById("hidden_receipe_id").value; // mrr number
				var mcId=this.contentDoc.getElementById("hidden_mc_id").value;
				var mcName=this.contentDoc.getElementById("hidden_mc_name").value;
				var lc_comp_id=this.contentDoc.getElementById("hidden_lc_company_id").value;
				var req_store_id=this.contentDoc.getElementById("hidden_store_id").value;
	
				$("#txt_req_no").val(reqNumber);
				$("#txt_req_id").val(reqId);
				$("#txt_batch_no").val(batchId);
				$("#txt_batch_id").val(batchId);
				$("#txt_recipe_no").val(receipeId);
				$("#txt_recipe_id").val(receipeId);
				$("#txt_machine_id").val(mcId);
				$("#txt_machine_name").val(mcName);
				$("#cbo_store_name").val(req_store_id);
				//alert(lc_comp_id);
				if(lc_comp_id) $("#cbo_company_name").val(lc_comp_id);
				$('#cbo_company_name').attr('disabled','disabled');
				document.getElementById("list_container_recipe_items").innerHTML = "";
				
				//load_drop_down( 'requires/chemical_dyes_issue_v2_controller', company, 'load_drop_down_store', 'store_td' );
				//load_drop_down( 'requires/chemical_dyes_issue_v2_controller', company, 'load_drop_down_loan_party', 'loan_party_td');
				var update_id=$('#update_id').val();
				var variable_lot = $("#variable_lot").val();
				$("#cbo_store_name").attr('disabled',true);
				if(issue_basis==7 && issue_purpose==56)
				{
					//get_php_form_data(company+"**"+reqId, "populate_machine_wish_data", "requires/chemical_dyes_issue_v2_controller");
					get_php_form_data(reqId, "populate_machine_wish_data", "requires/chemical_dyes_issue_v2_controller");			
					show_list_view(reqId+'**'+company+"**"+issue_purpose+"**"+req_store_id+"**"+update_id+"**"+variable_lot+"**"+issue_basis, 'machine_item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_v2_controller', '');
					$("#recipe_items_list_view").html('');
				}
				else
				{
					get_php_form_data(company+"**"+batchId+"**"+receipeId, "populate_batch_receipe_data", "requires/chemical_dyes_issue_v2_controller");
					
					show_list_view(reqId+'**'+company+"**"+issue_purpose+"**"+req_store_id+"**"+update_id+"**"+variable_lot+"**"+issue_basis, 'machine_item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_v2_controller', '');
				}
				var tableFilters = 
				{
					col_0: "none",
					col_8: "none",
					col_9: "none",
					col_10: "none",
					col_11: "none",
					col_12: "none",
					col_13: "none",
					col_14: "none",
					col_15: "none",
					col_16: "none",
					col_17: "none"
				}
				setFilterGrid("tbl_list_search",-1,tableFilters);
			}
		}
		
	}
	
	
	$('#txt_req_no').live('keydown', function(e){
		if (e.keyCode === 13)
		{
			fnc_requisition_by_scan(this.value);
			
		}
	});
	
	function fnc_requisition_by_scan(req_no)
	{
		var req_all_data = return_global_ajax_value(req_no, 'req_wise_all_data', '', 'requires/chemical_dyes_issue_v2_controller');
		var req_all_data_ref=req_all_data.split("**");
		if(req_all_data_ref[0]==0)
		{
			alert(req_all_data_ref[1]);return;
		}
		else
		{
			
			reset_form('chemicaldyesissue_1','list_container_recipe_items*list_product_container','','','','txt_issue_date*cbo_dying_source*cbo_company_name*cbo_dying_company*cbo_issue_basis');
			var req_all_data_ref_all=req_all_data_ref[1].split("_");
			
			var reqId=req_all_data_ref_all[0]; // mrr number
			var reqNumber=req_all_data_ref_all[1]; // mrr number
			var batchId=req_all_data_ref_all[2]; // mrr number
			var receipeId=req_all_data_ref_all[3]; // mrr number
			var mcId=req_all_data_ref_all[4];
			var mcName=req_all_data_ref_all[5];
			var req_store_id=req_all_data_ref_all[6];
			var lc_comp_id=req_all_data_ref_all[7];
			var req_from=req_all_data_ref_all[8];
			var company=req_all_data_ref_all[9];
			var variable_lot=req_all_data_ref_all[10];
			var issue_basis=7;
			set_dying_company(1);
			$('#variable_lot').val(variable_lot);
			
			$("#txt_req_no").val(reqNumber);
			$("#txt_req_id").val(reqId);
			$("#txt_batch_no").val(batchId);
			$("#txt_batch_id").val(batchId);
			$("#txt_recipe_no").val(receipeId);
			$("#txt_recipe_id").val(receipeId);
			$("#txt_machine_id").val(mcId);
			$("#txt_machine_name").val(mcName);
			$("#cbo_store_name").val(req_store_id);
			$("#cbo_dying_source").val(1);			
			$("#cbo_dying_company").val(company);
			$("#cbo_issue_basis").val(7);
			
			if(lc_comp_id) $("#cbo_company_name").val(lc_comp_id);
			
			//alert(req_store_id); return;
			
			document.getElementById("list_container_recipe_items").innerHTML = "";
			var update_id=0;
			if(req_from==4)
			{
				$("#cbo_issue_purpose").val(56);
				var issue_purpose=56;
				get_php_form_data(reqId, "populate_machine_wish_data", "requires/chemical_dyes_issue_v2_controller");			
			}
			else
			{
				$("#cbo_issue_purpose").val(54);
				var issue_purpose=54;
				get_php_form_data(company+"**"+batchId+"**"+receipeId, "populate_batch_receipe_data", "requires/chemical_dyes_issue_v2_controller");
			}
			
			show_list_view(reqId+'**'+company+"**"+issue_purpose+"**"+req_store_id+"**"+update_id+"**"+variable_lot
			+"**"+issue_basis, 'machine_item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_v2_controller', '');
			disable_enable_fields( 'cbo_dying_source*cbo_dying_company*cbo_company_name*cbo_issue_basis*cbo_issue_purpose*txt_batch_no*txt_req_no*cbo_store_name', 1, '', '' );
			
			var tableFilters = 
			{
				col_0: "none",
				col_8: "none",
				col_9: "none",
				col_10: "none",
				col_11: "none",
				col_12: "none",
				col_13: "none",
				col_14: "none",
				col_15: "none",
				col_16: "none",
				col_17: "none"
			}
			setFilterGrid("tbl_list_search",-1,tableFilters);
			
		}
		
	}

	
	function fnc_item_details(sub_process_id,is_update,store_id)
	{
		if (form_validation('cbo_dying_company*cbo_issue_basis*cbo_store_name','Company*Issue Basis*Store Name')==false)
		{
			return;
		}
		var is_update=0;  
		var txt_recipe_id= $('#txt_recipe_id').val();
		var sub_process_id= 0;
		var cbo_company_id= $('#cbo_dying_company').val();
		var cbo_store_name= $('#cbo_store_name').val();
		
		var txt_batch_weight= $('#txt_batch_weight').val();
		var txt_tot_liquor= $('#txt_tot_liquor').val();
		var cbo_issue_basis= $('#cbo_issue_basis').val();
		var txt_req_id= $('#txt_req_id').val();
		var cbo_store_name=$("#cbo_store_name").val();
		var hidden_posted_account=$("#hidden_posted_account").val();
		var variable_lot=$("#variable_lot").val();
		
		show_list_view(cbo_company_id+'**'+sub_process_id+"**"+txt_recipe_id+"**"+txt_batch_weight+"**"+txt_tot_liquor+"**"+cbo_issue_basis+"**"+is_update+"**"+txt_req_id+"**"+cbo_store_name+"**"+hidden_posted_account+"**"+variable_lot, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_v2_controller', '');
		
		if(is_update !="")
		{
			set_button_status(1, permission, 'fnc_chemical_dyes_issue_entry',1,1);
		}
		else
		{
			set_button_status(0, permission, 'fnc_chemical_dyes_issue_entry',1,0);
		}
		var tableFilters = 
		 {
			//col_1: "none",
			col_0: "none",
			col_8: "none",
			col_9: "none",
			col_10: "none",
			col_11: "none",
			col_12: "none",
			col_13: "none",
			col_14: "none",
			col_15: "none",
			col_16: "none",
			col_17: "none"
			
		 }
		
	   setFilterGrid("tbl_list_search",-1,tableFilters);
	
	}
	
	function calculate_receipe_qty(i)
	{
		var cbo_dose_base = $("#cbo_dose_base_"+i).val();
		var txt_ratio = $("#txt_ratio_"+i).val();	
		var txt_tot_liquor = $("#txt_tot_liquor").val();
		var txt_batch_weight = $("#txt_batch_weight").val();
		var txt_adj_per = $("#txt_adj_per_"+i).val();
		var cbo_adj_type = $("#cbo_adj_type_"+i).val();	
	    var recipe_qnty=0;
	    //alert(cbo_dose_base);
	    if(cbo_dose_base==1)
	    {
		  recipe_qnty=(txt_tot_liquor*txt_ratio) /1000;
	    }
	    if(cbo_dose_base==2)
	    {
		  recipe_qnty=(txt_batch_weight*txt_ratio) /100;
	    }
	    //alert(recipe_qnty+'='+txt_tot_liquor+'='+txt_batch_weight);
		var required_qty=recipe_qnty;
		$("#txt_recipe_qnty_"+i).val(number_format_common(recipe_qnty,5,0));
		 //alert(txt_adj_per);
		if(txt_adj_per !="")
		{
			var adjust_percent=recipe_qnty*txt_adj_per/100
			
			if(cbo_adj_type==1)
			{
				required_qty=required_qty+adjust_percent;
			}
			if(cbo_adj_type==2)
			{
				required_qty=required_qty-adjust_percent;
			}
		
		}
		$("#txt_reqn_qnty_"+i).val(number_format_common(required_qty,5,0));
		$("#txt_reqn_qnty_edit_"+i).val(number_format_common(required_qty,5,0));
	}
	
	function open_mrrpopup()
	{
		if( form_validation('cbo_dying_company','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_dying_company").val();	
		var page_link='requires/chemical_dyes_issue_v2_controller.php?action=mrr_popup&company='+company; 
		var title="Search Issue Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=460px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			reset_form('chemicaldyesissue_1','list_container_recipe_items*list_product_container','','','','txt_issue_date*cbo_dying_source*cbo_company_name*cbo_dying_company*cbo_issue_basis');
			mrrNumber = mrrNumber.split("_"); 
			$("#txt_mrr_no").val(mrrNumber[0]);
			$("#update_id").val(mrrNumber[1]);
			$("#hidden_posted_account").val(mrrNumber[4]);
			
			document.getElementById("list_container_recipe_items").innerHTML = "";
			get_php_form_data(mrrNumber[1], "populate_data_from_data", "requires/chemical_dyes_issue_v2_controller");
			var cbo_store_name = $("#cbo_store_name").val();
			var variable_lot = $("#variable_lot").val();
			$("#cbo_store_name").attr('disabled',true);
			var issue_basis = $("#cbo_issue_basis").val();
			var issue_purpose = $("#cbo_issue_purpose").val();
			show_list_view(mrrNumber[2]+'**'+company+"**"+mrrNumber[3]+"**"+cbo_store_name+"**"+mrrNumber[1]+"**"+variable_lot
			+"**"+issue_basis, 'machine_item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_v2_controller', '');
			var tableFilters = 
			{
				col_1: "none",
				col_0: "none",
				col_8: "none",
				col_9: "none",
				col_10: "none",
				col_11: "none",
				col_12: "none",
				col_13: "none",
				col_14: "none",
				col_15: "none",
				col_16: "none",
				col_17: "none"
				
			}
			setFilterGrid("tbl_list_search",-1,tableFilters);
			
			set_button_status(1, permission, 'fnc_chemical_dyes_issue_entry',1,1);
			$("#btn_cost_print").removeClass("formbutton_disabled");
			$("#btn_cost_print").addClass("formbutton");			
			disable_enable_fields( 'cbo_company_name*cbo_issue_basis*cbo_dying_source*cbo_dying_company*txt_batch_no*txt_req_no', 1, '', '' );
		}
	}
	
	
	function set_dying_company(param)
	{
		if(param==3)
		{
			load_drop_down( 'requires/chemical_dyes_issue_v2_controller',document.getElementById('cbo_company_name').value,'load_drop_down_dyeing_for_sub', 'dyeing_td');
		}
		else
		{
			load_drop_down( 'requires/chemical_dyes_issue_v2_controller','','load_drop_down_dyeing', 'dyeing_td');
		}
	}
	
	
	function check_data(id,stock_value)
	{
		var issure_value=$(id).val();
		if((parseInt(stock_value)*1)<parseInt(issure_value))
		{
			$(id).val(0);
			alert("Issue quantity over the Stock quantity not allowed");
			//$('#txt_reject_qnty').val(0);
		}
	}
	
	function openmypage_machine()
	{
		var cbo_issue_purpose = $('#cbo_issue_purpose').val();
		var cbo_company_name = $('#cbo_dying_company').val();
		//alert(cbo_company_name);
		var title = 'Machine Name Selection Form';	
		var page_link='requires/chemical_dyes_issue_v2_controller.php?action=machine_name_popup&cbo_company_name='+cbo_company_name+'&cbo_issue_purpose='+cbo_issue_purpose; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hide_machine_id=this.contentDoc.getElementById("hide_machine_id").value;	 //Access form field with id="emailfield"
			var hide_machine_name=this.contentDoc.getElementById("hide_machine_name").value;
			$('#txt_machine_id').val(hide_machine_id);
			$('#txt_machine_name').val(hide_machine_name);
		}
	}
	
	
	function company_onchange(com_id)
	{
		var com_all_data = return_global_ajax_value(com_id, 'com_wise_all_data', '', 'requires/chemical_dyes_issue_v2_controller');
		var com_all_data_arr=com_all_data.split("**");
		$('#variable_lot').val(com_all_data_arr[0]);
		var JSONObject_location = JSON.parse(com_all_data_arr[1]);
		$('#cbo_location_name').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_location).sort())
		{
			$('#cbo_location_name').append('<option value="'+key+'">'+JSONObject_location[key]+'</option>');
		}
		
		var JSONObject_loan = JSON.parse(com_all_data_arr[2]);
		$('#cbo_loan_party').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_loan).sort())
		{
			$('#cbo_loan_party').append('<option value="'+key+'">'+JSONObject_loan[key]+'</option>');
		}
		
		var JSONObject_store = JSON.parse(com_all_data_arr[3]);
		$('#cbo_store_name').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_store).sort())
		{
			$('#cbo_store_name').append('<option value="'+key+'">'+JSONObject_store[key]+'</option>');
		}
		var JSONObject_print_report = JSON.parse(com_all_data_arr[4]);
		$('#btn_print2').hide();
		$('#btn_print3').hide();
		$('#btn_print4').hide();
		for (var key of Object.keys(JSONObject_print_report).sort())
		{
			if(JSONObject_print_report[key]==84){$('#btn_print2').show();}
			if(JSONObject_print_report[key]==85){$('#btn_print3').show();}
			if(JSONObject_print_report[key]==89){$('#btn_print4').show();}
		}
	}
	
	
	function fnc_req_num_list()
	{
		var reqId=0;
		show_list_view(reqId, 'req_num_list', 'list_product_container', 'requires/chemical_dyes_issue_v2_controller', '');
		setFilterGrid("tbl_list_search",-1);
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="left">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="chemicaldyesissue_1" id="chemicaldyesissue_1" autocomplete="off" data-entry_form="5"> 
        <div style="width:75%;">  
            <table width="75%" cellpadding="0" cellspacing="2" align="left" >
                <tr>
                    <td width="80%" align="center" valign="top">   
                    <fieldset style="width:900px; float:left;">
                        <legend>Dyes And Chemical Issue</legend>
                            <fieldset style="width:900px;">                                       
                                <table  width="900" height="160" cellspacing="2" cellpadding="0" border="0" id="tbl_master"  >
                                    <tr >
                                        <td colspan="10" align="center" >&nbsp;<b>Issue Number</b>
                                        <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> 
                                        <input type="hidden" name="update_id" id="update_id" />
                                        <input type="hidden" name="hidden_posted_account" id="hidden_posted_account" />
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td width="100" align="right" id="basis_caption">Req./Lab Dip No </td>
                                        <td width="150">
                                        <input class="text_boxes"  type="text" name="txt_req_no" id="txt_req_no" onDblClick="open_reqpopup()" placeholder="Browse/Scan/Write" style="width:130px;"  /> 
                                        <input type="hidden" id="txt_req_id" name="txt_req_id" value="" />
                                        </td>
										<td width="100" align="right" class="must_entry_caption">Dyeing Source</td>
                                        <td width="150">
                                        <? //rcv_basis_reset();
                                        echo create_drop_down( "cbo_dying_source", 145, $knitting_source,"", 1, "- Select Dying Source -", $selected, "set_dying_company(this.value);","","1,2" );
                                        ?>
                                        </td>
										<td width="100" align="right"> Working Company<input type="hidden" id="variable_lot" name="variable_lot" /></td>
                                        <td width="150" id="dyeing_td">
                                        <?
                                        echo create_drop_down( "cbo_dying_company", 145, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_credential_cond $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "company_onchange(this.value);" ,1);
										?>
                                        
                                        </td>
                                        <td  width="100" align="right" > Location </td>
                                        <td width="150" id="location_td">
                                        <? 
                                        echo create_drop_down( "cbo_location_name", 145, $blank_array,"", 1, "-- Select Location --", 0, "" );
                                        ?>
                                        </td>
										
                                    </tr>
									
									 <tr>
									 <td align="right" class="must_entry_caption">LC Company </td>
                                        <td>
											<? 
											echo create_drop_down( "cbo_company_name", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/chemical_dyes_issue_v2_controller', this.value, 'load_drop_down_division','division_td');" );
											?>
                                        </td>
										
										<td align="right" class="must_entry_caption"> Issue Basis </td>
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_issue_basis", 145, $receive_basis_arr,"", 1, "- Select Issue Basis -", 7, "fn_independent(this.value,0)","","4,7" );
                                        ?>
                                        </td>
                                        <td align="right" class="must_entry_caption">Issue Purpose </td>
                                        <td>
                                        <? 
                                        echo create_drop_down( "cbo_issue_purpose", 145, $general_issue_purpose,"", 1, "-- Select Purpose --", 54, "fn_independent(document.getElementById('cbo_issue_basis').value,this.value)", "","1,2,3,5,8,11,15,18,37,38,43,44,49,53,54,56,57,60,61,63,64,65,66,69,71,74,79,84,87,88,89,90,91,92,96,97,98");
                                        ?>
                                        </td>
                                        <td align="right" class="must_entry_caption">Issue Date </td>
                                        <td>
                                        <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:130px;" value="<? echo date("d-m-Y");?>" placeholder="Select Date" />
                                        </td>
										
									 </tr>
                                
                                    <tr>
                                    	
										<td align="right">Batch No & Ext. </td>
                                        <td>
                                        <input class="text_boxes"  type="text" name="txt_batch_no" id="txt_batch_no"  style="width:100px;"    />
                                        <input class="text_boxes"  type="text" name="txt_ext_no" id="txt_ext_no"  style="width:20px;"  disabled="disabled"   />  
                                        <input type="hidden" name="txt_batch_id" id="txt_batch_id" />
                                       
                                        </td>

										<td align="right">Recipe No.</td>
                                        <td id="lc_no">
                                        <input class="text_boxes"  type="text" name="txt_recipe_no" id="txt_recipe_no"  style="width:130px;" disabled />
                                        <input type="hidden" name="txt_recipe_id" id="txt_recipe_id" /> 
                                        </td>
                                        <td align="right"> Batch Weight </td>
                                        <td>
                                        <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:130px;" placeholder="Display" disabled  />
                                        </td>
                                        <td align="right"> Total Liquor (ltr)</td>
                                        <td>
                                        <input type="text" name="txt_tot_liquor" id="txt_tot_liquor" class="text_boxes_numeric" style="width:130px;" placeholder="Display" disabled />
                                        </td>
										
                                    </tr>
                                
                                    <tr>
										
										<td align="right">Loan/Sales Party </td>
                                        <td id="loan_party_td">
                                        <? 
                                        echo create_drop_down( "cbo_loan_party", 150, $blank_array,"", 1, "- Select Loan Party -", $selected, "","","" );
                                        ?>
                                        </td>
										<td align="right" class="" id="buyer_order">Buyer Order No </td>
                                        <td>
                                        <input class="text_boxes"  type="text" name="txt_order_no" id="txt_order_no" style="width:130px;" placeholder="Display" readonly   /> 
                                        <input type="hidden" name="hidden_order_id" id="hidden_order_id" /> 
                                        <input type="hidden" name="hidden_booking_type" id="hidden_booking_type" /> 
                                        </td>
                                        <td align="right">Buyer Name </td>
                                        <td><? 
                                        echo create_drop_down( "cbo_buyer_name", 145, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,1);
                                        ?></td>


                                                                                
                                        <td align="right"> Style Ref. </td>
                                        <td>
                                        <input class="text_boxes"  type="text" name="txt_style_no" id="txt_style_no" style="width:130px;" placeholder="Display" readonly   /> 
                                        </td>
										
                                    </tr>

									<tr>
										<td align="right"> Color Range </td>
                                        <td>
                                        <input type="text" name="txt_color_range" id="txt_color_range" class="text_boxes_numeric" style="width:130px;" placeholder="Display" disabled  />
                                        </td>
										<td align="right"> Challan No </td>
                                        <td>
                                        <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:130px" >
                                        </td>
                                        <td align="right"> File No</td>
                                        <td>
                                        <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes_numeric" style="width:130px;" placeholder="Display" disabled />
                                        </td>
                                        <td align="right" >Ref. No</td>
                                        
                                        <td>
                                             <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes_numeric" style="width:130px;" placeholder="Display" disabled />
                                        </td>
									</tr>
                                
                                    <tr>
                                    	
                                    	   
										<td align="right" class="must_entry_caption">Store Name</td>
                                        <td id="store_td" >
                                            <? 
												echo create_drop_down( "cbo_store_name", 145, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(5,6,7,23) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "", "fnc_item_details(this.value,'','')", "","");
                                            ?>
                                        
                                        </td> 

										<td align="right">Machine</td>
                                        <td>
                                        <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes_numeric" style="width:130px;" onDblClick="openmypage_machine();" placeholder="Browse"  /> 
                                        <input type="hidden" name="txt_machine_id" id="txt_machine_id" />
                                        </td>
                                        <td align="right">Return</td>
                                        <td>
                                         
                                        <input type="text" name="txt_return" id="txt_return" class="text_boxes" style="width:130px" >
                                        
                                        </td>
                                        <td align="right">Attention</td>
										<td><input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:130px" ></td>

										
										 
										
                                    </tr>
									<tr>
									<td align="right">Issue Category</td>
										<td id="issue_category" >
                                            <? 
												$issue_cat_array=[1=>"Return",2=>"Sales",3=>"Loan",4=>"Returnable",5=>"Non-Returnable",6=>"Service",7=>"Exchange"];
												echo create_drop_down( "cbo_issue_category", 145, $issue_cat_array,"", 1, "-- Select --", "", "", "","");
                                            ?>
                                        
                                        </td> 
									</tr>
                                     
                                </table>
                            </fieldset>
                                    
                        <table cellpadding="0" cellspacing="1" width="100%">
                            <tr> 
                            <td colspan="8" align="center"></td>				
                            </tr>
                        
                            <tr> 
                            <td colspan="8" align="center"><div id="list_container_recipe_items" style="margin-top:10px"></div></td>				
                            </tr>
                            <tr>
                            <td align="center" colspan="8" valign="middle" class="button_container">
                            <!-- details table id for update -->
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_for_cost" id="update_for_cost" readonly>
                            <? echo load_submit_buttons( $permission, "fnc_chemical_dyes_issue_entry", 0,0,"fnResetForm()",1);?>
                            <input type="button" id="btn_print2" name="btn_print2"  style="width:80px;"  class="formbutton" value="Print2"  onClick="fnc_chemical_dyes_issue_entry(5);"/>
                            <input type="button" id="btn_print3" name="btn_print3"  style="width:80px;"  class="formbutton" value="Print3"  onClick="fnc_chemical_dyes_issue_entry(6);"/>

							<input type="button" id="btn_print4" name="btn_print4"  style="width:80px;"  class="formbutton" value="Print4"  onClick="fnc_chemical_dyes_issue_entry(7);"/>
							
                            <input type="button" id="btn_cost_print" name="btn_cost_print"   style="width:80px;"  class="formbutton_disabled" value="Cost"  onClick="generate_report_file($('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_cost_print','requires/chemical_dyes_issue_v2_controller');"/>
                            
                            </td>
                            </tr>
                            <tr> 
                            <td colspan="6" align="center"> <div id="recipe_items_list_view" style="margin-top:10px"> </div></td>				
                            </tr> 
                        </table>                 
                    </fieldset>
                    </td>
                </tr>
            </table>
        </div>
    <div id="list_product_container" style="max-height:500px; width:25%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"><input type="button" id="btn_print3" name="btn_print3"  style="width:64px;"  class="formbutton" value="View"  onClick="fnc_req_num_list();"/></div>  
    </form>
    </div>    
</body>  
<script>
	$(document).ready(function() {
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
