<?
/************************************************Comments************************************
Purpose			: 	This form will create Dyes And Chemical Receive Entry
Functionality	:	
JS Functions	:
Created by		:	MONZU 
Creation date 	: 	17-08-2013
Updated by 		: 	Kausar	
Update date		: 	10-12-2013 (Creating Report)	   
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
	function rcv_basis_reset()
	{
		document.getElementById('cbo_issue_basis').value=0;
		//reset_form('chemicaldyesissue_1','list_container_yarn*list_product_container','','','','cbo_company_name');
		reset_form('chemicaldyesissue_1','list_container_yarn*list_product_container','','','','txt_issue_date*cbo_dying_source*cbo_company_name*cbo_dying_company');
		fn_independent(0);
	}
	
	// popup for WO/PI----------------------	
	
	
	// enable disable field for independent
	function fn_independent(val)
	{
		//reset_form('chemicaldyesissue_1','list_container_yarn*list_product_container','','','','cbo_company_name*cbo_receive_basis');
		if(val==0)
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
		
		
		
		if(val==4)
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
		}
		
		if(val==5)
		{
			$('#txt_req_id').val("");
			$('#txt_req_no').val("");
			
			$("#txt_req_no").attr("disabled",true);
			$('#txt_req_no').attr('placeholder','No Need');
			
			$("#txt_batch_no").attr("disabled",false);
			$('#txt_batch_no').removeAttr('placeholder','No Need');
			$('#txt_batch_no').attr('placeholder','Double Click');
		}
		if(val==7)
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
	}
	
	/* function generate_report_file(data,action,page)
	{
		window.open("requires/chemical_dyes_issue_controller.php?data=" + data+'&action='+action, true );
	}*/
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/chemical_dyes_issue_controller.php?data=" + data+'&action='+action, true );
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
			generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_print','requires/chemical_dyes_issue_controller');
			return;
		}
		else if(operation==5)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_print_single_com','requires/chemical_dyes_issue_controller');
			return;
		}
		else if(operation==6)
		{
			if($('#update_id').val()<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_print_single_com2','requires/chemical_dyes_issue_controller');
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			var row_num=$('#tbl_list_search tbody tr').length;
			
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

				if($('#txt_isu_qnty').val()!="")
				{
					var hidden_issue_qnty=$('#txt_isu_qnty').val().split(",");					
					var hidden_issue_num=$('#txt_isu_num').val().split(",");
					if(msg!="")
					{
						for (var i=1; i<=hidden_issue_qnty.length; i++)
						{
							msg +="Issue Number : "+hidden_issue_num[k]+" &  Qnty : "+ hidden_issue_qnty[k]+"\n";
							k++;
						}
					}
					
				}
				if(msg!="")
				{
					alert(msg);
					return;
				}
			}
			
			if( $("#cbo_issue_basis").val()==7 && $("#cbo_issue_purpose").val()==13)
			{
				if( form_validation('cbo_dying_company*cbo_issue_basis*txt_issue_date*cbo_store_name*cbo_issue_purpose','Company Name*Issue Basis*Issue Date*Store Name*Issue Purpose')==false )
				{
					return;
				}
			}
			else if( $("#cbo_issue_purpose").val()==5)
			{
				if( form_validation('cbo_dying_company*cbo_issue_basis*txt_issue_date*cbo_sub_process*cbo_store_name*cbo_issue_purpose*cbo_loan_party','Company Name*Issue Basis*Issue Date*Sub-Process*Store Name*Issue Purpose*Loan Party')==false )
				{
					return;
				}
			}
			else 
			{
				if( form_validation('cbo_dying_company*cbo_issue_basis*txt_issue_date*cbo_sub_process*cbo_store_name*cbo_issue_purpose','Company Name*Issue Basis*Issue Date*Sub-Process*Store Name*Issue Purpose')==false )
				{
					return;
				}	
			}
                                
			var current_date = '<? echo date("d-m-Y"); ?>';
			if (date_compare($('#txt_issue_date').val(), current_date) == false) 
			{
				alert("Issue Date Can not Be Greater Than Current Date");
				return;
			}
			
			var dataString = "txt_mrr_no*update_id*cbo_company_name*cbo_location_name*cbo_issue_basis*txt_req_no*txt_req_id*txt_batch_no*txt_batch_id*cbo_issue_purpose*cbo_loan_party*cbo_dying_source*cbo_dying_company*txt_challan_no*txt_issue_date*txt_recipe_no*txt_recipe_id*txt_order_no*hidden_order_id*cbo_buyer_name*txt_style_no*cbo_sub_process*cbo_store_name*txt_machine_name*txt_machine_id*txt_return*txt_attention*variable_lot";
			
			var dtls_data="";
			for (var i=1; i<row_num; i++)
			{
				if(($('#txt_reqn_qnty_edit_'+i).val()*1)>0)
				{
					//mst_data=mst_data+get_submitted_data_string('txt_prod_id_'+i+'*txt_lot_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*txt_reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*transId_'+i+'*hidtxt_reqn_qnty_edit_'+i+'*txt_remarks_'+i,"../../",2);
					dtls_data+='*txt_prod_id_'+i+'*txt_lot_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*txt_reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*transId_'+i+'*hidtxt_reqn_qnty_edit_'+i+'*txt_remarks_'+i;
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
			http.open("POST","requires/chemical_dyes_issue_controller.php",true);
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
			release_freezing();
			
			if (reponse[0] * 1 == 20 * 1) {
				alert(reponse[1]);
				return;
			}
			show_msg(reponse[0]);
			if($('#cbo_issue_basis').val()==7 && $('#cbo_issue_purpose').val()==13)
			{
				if((reponse[0]==0 || reponse[0]==1))
				{
					disable_enable_fields( 'cbo_company_name*cbo_issue_basis*cbo_dying_source*cbo_dying_company*cbo_sub_process*txt_req_no', 1, '', '' );
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_mrr_no').value = reponse[2];
					$('#cbo_company_name').attr('disabled','disabled');
					set_button_status(1, permission, 'fnc_chemical_dyes_issue_entry',1,1);	
				}
				
			}
			else
			{
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					disable_enable_fields( 'cbo_company_name*cbo_issue_basis*cbo_dying_source*cbo_dying_company*txt_req_no', 1, '', '' ); //*cbo_sub_process
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_mrr_no').value = reponse[2];
					$('#cbo_company_name').attr('disabled','disabled');
					show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view','requires/chemical_dyes_issue_controller', '' );
					document.getElementById('cbo_sub_process').value=0;
					reset_form( '', 'list_container_recipe_items', '', '', '', '' ); 
					set_button_status(reponse[3], permission, 'fnc_chemical_dyes_issue_entry',1,1);		
				}
				
			}
			$("#btn_cost_print").removeClass("formbutton_disabled");
			$("#btn_cost_print").addClass("formbutton");
		
			//release_freezing();	
		}
		
	}
	
	function fnResetForm()
	{
		fn_independent(0)
		set_button_status(0, permission, 'fnResetForm',1);
		reset_form('chemicaldyesissue_1','list_container_yarn*list_product_container','','','','');
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
		if( form_validation('cbo_dying_company*cbo_issue_basis','Working Company*Issue Basis')==false )
		{
			return;
		}
		var company = $("#cbo_dying_company").val();
		var issue_purpose = $("#cbo_issue_purpose").val();	
		var issue_basis = $("#cbo_issue_basis").val();	
		var store_id = 0;
		if(issue_basis==7 && issue_purpose==13)
		{
			store_id = $("#cbo_store_name").val()*1;
			if(store_id == 0)
			{
				alert("Please Select Store Name");
				return;
			}
		}
		
		var page_link='requires/chemical_dyes_issue_controller.php?action=req_popup&company='+company+'&issue_purpose='+issue_purpose+'&issue_basis='+issue_basis+'&store_id='+store_id;
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
			$("#cbo_company_name").val(lc_comp_id);
			$('#cbo_company_name').attr('disabled','disabled');
			document.getElementById("list_container_recipe_items").innerHTML = "";
			
			//load_drop_down( 'requires/chemical_dyes_issue_controller', company, 'load_drop_down_store', 'store_td' );
			//load_drop_down( 'requires/chemical_dyes_issue_controller', company, 'load_drop_down_loan_party', 'loan_party_td');
			
			if(issue_basis==7 && issue_purpose==13)
			{
				var update_id=$('#update_id').val();
				var variable_lot = $("#variable_lot").val();
				//get_php_form_data(company+"**"+reqId, "populate_machine_wish_data", "requires/chemical_dyes_issue_controller");
				get_php_form_data(reqId, "populate_machine_wish_data", "requires/chemical_dyes_issue_controller");			
				show_list_view(reqId+'**'+company+"**"+issue_purpose+"**"+store_id+"**"+update_id+"**"+variable_lot, 'machine_item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_controller', '');
				$("#recipe_items_list_view").html('');
				$("#cbo_sub_process").attr('disabled',true);
				$("#cbo_store_name").attr('disabled',true);
				document.getElementById("recipe_items_list_view").innerHTML = "";
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
					col_15: "none"
				}
				setFilterGrid("tbl_list_search",-1,tableFilters);
			
			}
			else
			{
				$("#cbo_store_name").attr('disabled',true);
				get_php_form_data(company+"**"+batchId+"**"+receipeId, "populate_batch_receipe_data", "requires/chemical_dyes_issue_controller");
				get_php_form_data(company+"**"+batchId+"**"+receipeId, "populate_buyer_order_no_data", "requires/chemical_dyes_issue_controller");
				load_drop_down( 'requires/chemical_dyes_issue_controller', company+"**"+receipeId, 'load_drop_down_sub_process', 'sub_process_td');
			}
			

		}
	}

	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_dying_company').val();
		if (form_validation('cbo_dying_company','Working Company')==false)
		{
		return;
		}
		/*if (form_validation('cbo_company_name','Company')==false)
		{
		return;
		}
		else
		{ */	
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/chemical_dyes_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;;
				var batch_weight=this.contentDoc.getElementById('hidden_batch_weight').value;
		        var total_liquor=this.contentDoc.getElementById('hidden_total_liquor').value;
				 var hidden_lc_com=this.contentDoc.getElementById('hidden_lc_com').value;
				document.getElementById("list_container_recipe_items").innerHTML = "";
				get_php_form_data(cbo_company_id+"**"+batch_id, "populate_buyer_order_for_batch", "requires/chemical_dyes_issue_controller");
				if(batch_id!="")
				{
					freeze_window(5);
					document.getElementById('txt_batch_id').value=batch_id;
					document.getElementById('txt_batch_no').value=batch_no;
					document.getElementById('cbo_company_name').value=hidden_lc_com;
					document.getElementById('txt_batch_weight').value=batch_weight;
					document.getElementById('txt_tot_liquor').value=total_liquor;
					$('#cbo_company_name').attr('disabled','disabled');
					release_freezing();
				} 
			}
		//}
	}
	
	function fnc_item_details(sub_process_id,is_update,store_id)
	{  
		if(store_id!="") $('#cbo_store_name').val(store_id);
		var subId=''; var breakOut = true;
		$(".accordion_h").each(function() {
			 var tid=$(this).attr('id'); 
			 var sid=tid+"span";
			 var id=tid+"id";
			 $('#'+sid).html("+");
			 
			 subId=$('#'+id).html();
			 if(is_update=="" && sub_process_id==subId)
			 {
				 breakOut=false;
				 return false;
			 }
		});

		if(breakOut==false)
		{
			alert("This Sub-Process Already Saved.");
			$('#cbo_sub_process').val(0);
			return;	
		}
		
		$('#accordion_h'+sub_process_id+'span').html("-");
		var txt_recipe_id= $('#txt_recipe_id').val();
		//var cbo_company_id= $('#cbo_company_name').val();
		var cbo_company_id= $('#cbo_dying_company').val();
		var cbo_store_name= $('#cbo_store_name').val();
		
		if (form_validation('cbo_dying_company*cbo_issue_basis*cbo_store_name','Company*Issue Basis*Store Name')==false)
		{
			return;
		}
		
		var txt_batch_weight= $('#txt_batch_weight').val();
		var txt_tot_liquor= $('#txt_tot_liquor').val();
		var cbo_issue_basis= $('#cbo_issue_basis').val();
		var txt_req_id= $('#txt_req_id').val();
		$('#cbo_sub_process').val(sub_process_id);
		var cbo_store_name=$("#cbo_store_name").val();
		var hidden_posted_account=$("#hidden_posted_account").val();
		var variable_lot=$("#variable_lot").val();
		
		show_list_view(cbo_company_id+'**'+sub_process_id+"**"+txt_recipe_id+"**"+txt_batch_weight+"**"+txt_tot_liquor+"**"+cbo_issue_basis+"**"+is_update+"**"+txt_req_id+"**"+cbo_store_name+"**"+hidden_posted_account+"**"+variable_lot, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_controller', '');
		
		/*show_list_view(cbo_company_id+'**'+sub_process_id+"**"+txt_recipe_id+"**"+txt_batch_weight+"**"+txt_tot_liquor+"**"+cbo_issue_basis+"**"+is_update+"**"+txt_req_id+"**"+cbo_store_name+"**"+hidden_posted_account, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_controller', '');*/
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
			col_15: "none"
			
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
		var page_link='requires/chemical_dyes_issue_controller.php?action=mrr_popup&company='+company; 
		var title="Search Issue Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=460px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_"); 
			$("#txt_mrr_no").val(mrrNumber[0]);
			$("#update_id").val(mrrNumber[1]);
			$("#hidden_posted_account").val(mrrNumber[4]);
			$('#cbo_sub_process').attr('disabled',false);
			
			if(mrrNumber[2]==7 && mrrNumber[3]==13)
			{
				document.getElementById("list_container_recipe_items").innerHTML = "";
				document.getElementById("recipe_items_list_view").innerHTML = "";
				var cbo_store_name="";
				$('#cbo_sub_process').attr('disabled','disabled');
				get_php_form_data(mrrNumber[1], "populate_data_from_data", "requires/chemical_dyes_issue_controller");
				var cbo_store_name = $("#cbo_store_name").val();
				var variable_lot = $("#variable_lot").val();
				show_list_view(mrrNumber[2]+'**'+company+"**"+mrrNumber[3]+"**"+cbo_store_name+"**"+mrrNumber[1]+"**"+variable_lot, 'machine_item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_controller', '');
				$('#cbo_sub_process').attr('disabled','disabled');
				$("#cbo_store_name").attr('disabled',true);
				set_button_status(1, permission, 'fnc_chemical_dyes_issue_entry',1,1);
				$("#btn_cost_print").removeClass("formbutton_disabled");
				$("#btn_cost_print").addClass("formbutton");
			
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
					col_15: "none"
					
				}
				
				setFilterGrid("tbl_list_search",-1,tableFilters);
			}
			else
			{
				document.getElementById("list_container_recipe_items").innerHTML = "";
				show_list_view(mrrNumber[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/chemical_dyes_issue_controller', '' ) ;
				get_php_form_data(mrrNumber[1], "populate_data_from_data", "requires/chemical_dyes_issue_controller");
				var batch_id= document.getElementById('txt_batch_id').value;
				set_button_status(0, permission, 'fnc_chemical_dyes_issue_entry',1,1);	
				$("#btn_cost_print").removeClass("formbutton_disabled");
				$("#btn_cost_print").addClass("formbutton");
			
			}
			
			disable_enable_fields( 'cbo_company_name*cbo_issue_basis*cbo_dying_source*cbo_dying_company*txt_batch_no*txt_req_no', 1, '', '' );
		}
	}
	
	
	/*	function open_mrrpopup()
	{
		//reset_form('','list_container_recipe_items*recipe_items_list_view','','','','');
	
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/chemical_dyes_issue_controller.php?action=mrr_popup&company='+company; 
		var title="Search Issue Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=460px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			
			var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
			mrrNumber = mrrNumber.split("_"); 
			//var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number
	
			$("#txt_mrr_no").val(mrrNumber[0]);
			$("#update_id").val(mrrNumber[1]);
			document.getElementById("list_container_recipe_items").innerHTML = "";
			show_list_view(mrrNumber[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/chemical_dyes_issue_controller', '' ) ;
			get_php_form_data(mrrNumber[1], "populate_data_from_data", "requires/chemical_dyes_issue_controller");
			var batch_id= document.getElementById('txt_batch_id').value;
			//alert (batch_id);
			//get_php_form_data(company+"**"+batch_id, "populate_buyer_order_no_data", "requires/chemical_dyes_issue_controller");
			set_button_status(0, permission, 'fnc_chemical_dyes_issue_entry',1,1);	
			$("#btn_cost_print").removeClass("formbutton_disabled");
			$("#btn_cost_print").addClass("formbutton");
			// master part call here
			//get_php_form_data(mrrNumber, "populate_data_from_data", "requires/chemical_dyes_issue_requisition_controller");
			//$("#tbl_master").find('input,select').attr("disabled", true);	
			//disable_enable_fields( 'txt_mrr_no', 0, "", "" );	
		}
	}*/
	
	
	
	function set_dying_company(param)
	{
		if(param==3)
		{
			load_drop_down( 'requires/chemical_dyes_issue_controller',document.getElementById('cbo_company_name').value,'load_drop_down_dyeing_for_sub', 'dyeing_td');
		}
		else
		{
			load_drop_down( 'requires/chemical_dyes_issue_controller','','load_drop_down_dyeing', 'dyeing_td');
			//alert(param);
			//document.getElementById('cbo_dying_company').value=0;	
			//var response = return_global_ajax_value( compony_id, 'is_manula_approved', '', 'requires/quotation_entry_controller');		
			
			/*if(param==1 || param==2)
			{
				load_drop_down( 'requires/chemical_dyes_issue_controller','','load_drop_down_dyeing', 'dyeing_td');
			}
			else
			{
				$("#cbo_dying_company").attr('disabled',true);
			}
			var company_id = document.getElementById('cbo_company_name').value;
			if(company_id != 0)
			{
				load_drop_down( 'requires/chemical_dyes_issue_controller', company_id, 'load_drop_down_location', 'location_td');
				load_drop_down( 'requires/chemical_dyes_issue_controller', company_id, 'load_drop_down_store', 'store_td');
				load_drop_down( 'requires/chemical_dyes_issue_controller', company_id, 'load_drop_down_loan_party', 'loan_party_td');
			}*/
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
		var page_link='requires/chemical_dyes_issue_controller.php?action=machine_name_popup&cbo_company_name='+cbo_company_name+'&cbo_issue_purpose='+cbo_issue_purpose; 
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
	
	
	function check_machine_wash(id)
	{
		var cbo_company_id=$('#cbo_company_name').val();
		var cbo_issue_basis=$('#cbo_issue_basis').val();
		var cbo_store_name=$('#cbo_store_name').val();
		var update_id=$('#update_id').val();
		if(id==13 && cbo_issue_basis==7)
		{
			
		document.getElementById('sub_process_caption').innerHTML="Sub-Process";
		$('#sub_process_caption').css('color','black');
		$('#cbo_sub_process').val(0);
		$('#cbo_sub_process').attr('disabled','disabled');	
		}
		else
		{
		document.getElementById('sub_process_caption').innerHTML="Sub-Process";
		$('#sub_process_caption').css('color','blue');		
		}
		
	}
	
	function fn_sub_process_enable(sid)
	{
		if(sid>0)
		{
			$('#cbo_sub_process').attr('disabled',false);
		}
		else
		{
			$('#cbo_sub_process').val(0);
			reset_form('','list_container_recipe_items','','','','');
			$('#cbo_sub_process').attr('disabled',true);
		}
	}
	
	function rcv_variable_check(str)
	{
		var company_id=$('#cbo_dying_company').val();
		var lots_variable=return_global_ajax_value( company_id, 'populate_data_lib_data', '', 'requires/chemical_dyes_receive_return_controller');
		$('#variable_lot').val(lots_variable);
		/*if(lots_variable==1)
		{
			$('#lot_caption').css('color', 'blue');
		}
		else
		{
			$('#lot_caption').css('color', 'black');
		}*/
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="left">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="chemicaldyesissue_1" id="chemicaldyesissue_1" autocomplete="off" data-entry_form="5"> 
        <div style="width:80%;">  
            <table width="80%" cellpadding="0" cellspacing="2" align="left">
                <tr>
                    <td width="80%" align="center" valign="top">   
                        <fieldset style="width:1210px; float:left;">
                        <legend>Dyes And Chemical Issue</legend>
                        <br />
                            <fieldset style="width:950px;">                                       
                                <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                                    <tr>
                                        <td colspan="6" align="center">&nbsp;<b>Issue Number</b>
                                        <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> 
                                        <input type="hidden" name="update_id" id="update_id" />
                                        <input type="hidden" name="hidden_posted_account" id="hidden_posted_account" />
                                        </td>
                                    </tr>
                                    <tr>
                                         <td  width="130" align="right" class="must_entry_caption">Issue Date </td>
                                        <td width="170">
                                        <input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" placeholder="Select Date" />
                                        </td>
										 <td  width="130" align="right" class="must_entry_caption">Dyeing Source</td>
                                        <td width="170">
                                        <? 
                                        echo create_drop_down( "cbo_dying_source", 170, $knitting_source,"", 1, "- Select Dying Source -", $selected, "set_dying_company(this.value);rcv_basis_reset();","","1,2" );
                                        ?>
                                        </td>
										<td width="94" align="right"> Working Company<input type="hidden" id="variable_lot" name="variable_lot" /></td>
                                        <td width="160" id="dyeing_td">
                                        <?
                                        echo create_drop_down( "cbo_dying_company", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_credential_cond $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_variable_check(2);" ,1);
                                        
                                        //echo create_drop_down( "cbo_dying_company", 170, $blank_array,"", 1, "- Select Dying Source -", $selected, "","","" );
                                        ?>
                                        </td>
                                    </tr>
									
									 <tr>
									 	 <td  width="130" align="right" > Location </td>
                                        <td width="170" id="location_td">
                                        <? 
                                        echo create_drop_down( "cbo_location_name", 170, $blank_array,"", 1, "-- Select Location --", 0, "" );
                                        ?>
                                        </td>
										
										<td  width="130" align="right" class="must_entry_caption">LC Company </td>
                                        <td width="170">
                                        <? 
										//load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');
                                        echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                        ?>
                                        </td>
										<td width="94" align="right" class="must_entry_caption"> Issue Basis </td>
                                        <td width="160">
                                        <? 
                                        echo create_drop_down( "cbo_issue_basis", 170, $receive_basis_arr,"", 1, "- Select Issue Basis -", $selected, "fn_independent(this.value)","","4,5,7" );//fn_independent(this.value)
                                        ?>
                                        </td>
									 </tr>
                                
                                    <tr>
                                        
                                        
                                        
                                        <td  width="" align="right" class="must_entry_caption">Issue Purpose </td>
                                        <td width="">
                                        <? 
                                        
                                        echo create_drop_down( "cbo_issue_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", "", "check_machine_wash(this.value);", "","2,3,5,10,11,13,14,26,27,28,29,30,32,33,34,35,40,48,49,52,42,56,62,65,66,67,68,69,70,71,72,73,76");

                                        // echo create_drop_down( "cbo_issue_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", "", "load_drop_down( 'requires/chemical_dyes_issue_controller', this.value, 'load_drop_down_purpose','sub_process_td');check_machine_wash(this.value);", "","2,3,5,10,11,13,14,26,27,28,29,30,32,33,34,35,40");
                                        ?>
                                        </td>
                                        <td width="" align="right" id="basis_caption">Req. No </td>
                                        <td width="">
                                        <input class="text_boxes"  type="text" name="txt_req_no" id="txt_req_no" onDblClick="open_reqpopup()" placeholder="Double Click" style="width:160px;"  readonly  /> 
                                        <input type="hidden" id="txt_req_id" name="txt_req_id" value="" />
                                        </td>
										 <td width="94" align="right" >  Loan/Sales Party </td>
                                        <td width="160" id="loan_party_td">
                                        <? 
                                        echo create_drop_down( "cbo_loan_party", 170, $blank_array,"", 1, "- Select Loan Party -", $selected, "","","" );
                                        ?>
                                        </td>
                                    </tr>
                                
                                    <tr>
                                    
                                    <td width="" align="right">Batch No & Ext. </td>
                                        <td width="">
                                        <input class="text_boxes"  type="text" name="txt_batch_no" id="txt_batch_no" onDblClick="openmypage_batchNo()" placeholder="Double Click" style="width:120px;"    /> 
                                        <input class="text_boxes"  type="hidden" name="txt_batch_id" id="txt_batch_id"  style="width:60px;"    /> &nbsp; 
                                         <input class="text_boxes"  type="text" name="txt_ext_no" id="txt_ext_no"  style="width:20px;"  disabled="disabled"   /> 
                                        </td>
										 <td width="94" align="right" class="" > Challan No </td>
                                        <td width="160">
                                        <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" >
                                        </td>
                                        
                                        <td width="130" align="right">Recipe No.</td>
                                        <td id="lc_no" width="170">
                                        <input class="text_boxes"  type="text" name="txt_recipe_no" id="txt_recipe_no"  style="width:160px;" disabled /><!-- onDblClick="openmypage_labdipNo();"-->
                                        <input class="text_boxes"  type="hidden" name="txt_recipe_id" id="txt_recipe_id"style="width:160px;"  readonly/> 
                                        </td>
                                     
                                    </tr>
                                
                                    <tr>
                                        <td  width="130" align="right" class="" id="buyer_order">Buyer Order No </td>
                                        <td width="170">
                                        <input class="text_boxes"  type="text" name="txt_order_no" id="txt_order_no" style="width:160px;" placeholder="Display" readonly   /> 
                                        <input type="hidden" name="hidden_order_id" id="hidden_order_id" /> 
                                         <input type="hidden" name="hidden_booking_type" id="hidden_booking_type" /> 
                                        </td>
                                        <div id="buyer_name_id">
                                          <td  width="130" align="right" class="" >Buyer Name </td>
                                        </div>
                                        <td width="170"><? 
                                        echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,1);
                                        ?></td>
                                        
                                        <td  width="130" align="right" > Style Ref. </td>
                                        <td width="170">
                                        <input class="text_boxes"  type="text" name="txt_style_no" id="txt_style_no" style="width:160px;" placeholder="Display" readonly   /> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="130" align="right"> Batch Weight </td>
                                        <td width="170">
                                        <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:160px;" placeholder="Display" disabled  />
                                        </td>
                                        
                                        <td width="130" align="right"> Total Liquor (ltr)</td>
                                        <td width="170">
                                        <input type="text" name="txt_tot_liquor" id="txt_tot_liquor" class="text_boxes_numeric" style="width:160px;" placeholder="Display" disabled />
                                        </td>
                                        <td width="130" align="right" class="must_entry_caption">Store Name</td>
                                        
                                        <td width="170" id="store_td" >
                                            <? 
												echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(5,6,7) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "", "fn_sub_process_enable(this.value);", "","");
                                            ?>
                                        
                                        </td>
                                    
                                    </tr>
                                    <tr>
                                        <td width="130" align="right"> Color Range </td>
                                        <td width="170">
                                        <input type="text" name="txt_color_range" id="txt_color_range" class="text_boxes_numeric" style="width:160px;" placeholder="Display" disabled  />
                                        </td>
                                        
                                        <td width="130" align="right"> File No</td>
                                        <td width="170">
                                        <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes_numeric" style="width:160px;" placeholder="Display" disabled />
                                        </td>
                                        <td width="130" align="right" >Ref. No</td>
                                        
                                        <td width="170">
                                             <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes_numeric" style="width:160px;" placeholder="Display" disabled />
                                        
                                        </td>
                                    
                                    </tr>
                                    <tr>
                                    
                                        <td width="130" align="right" class="must_entry_caption" id="sub_process_caption">Sub-Process</td>
                                        <td width="170" id="sub_process_td" >
                                        <? 
                                        
                                        echo create_drop_down( "cbo_sub_process", 170, $dyeing_sub_process,"", 1, "-- Select--", "", "fnc_item_details(this.value,'','')",1,'','','',31 );
                                        ?>
                                        </td>
                                        
                                        <td width="130" align="right">Machine</td>
                                        <td width="170"  >
                                        <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes_numeric" style="width:160px;" onDblClick="openmypage_machine();" placeholder="Browse"  /> <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes_numeric" style="width:80px;" value="" />
                                        </td>
                                        <td width="130" align="right" >Return</td>
                                        <td width="170"  >
                                         
                                        <input type="text" name="txt_return" id="txt_return" class="text_boxes" style="width:160px" >
                                        
                                        </td>
                                    </tr>
									<tr>
										<td align="right">Attention</td>
										<td><input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:160px" ></td>
									</tr>
                                </table>
                            </fieldset>
                                    
                        <table cellpadding="0" cellspacing="1" width="100%">
                            <tr> 
                            <td colspan="6" align="center"></td>				
                            </tr>
                        
                            <tr> 
                            <td colspan="6" align="center">  <div id="list_container_recipe_items" style="margin-top:10px"></div></td>				
                            </tr>
                            <tr>
                            <td align="center" colspan="6" valign="middle" class="button_container">
                            <!-- details table id for update -->
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_for_cost" id="update_for_cost" readonly>
                            <? echo load_submit_buttons( $permission, "fnc_chemical_dyes_issue_entry", 0,1,"fnResetForm()",1);?>
                            <input type="button" id="btn_print2" name="btn_print2"  style="width:80px;"  class="formbutton" value="Print2"  onClick="fnc_chemical_dyes_issue_entry(5);"/>
                            <input type="button" id="btn_print3" name="btn_print3"  style="width:80px;"  class="formbutton" value="Print3"  onClick="fnc_chemical_dyes_issue_entry(6);"/>
							
                            <input type="button" id="btn_cost_print" name="btn_cost_print"   style="width:80px;"  class="formbutton_disabled" value="Cost"  onClick="generate_report_file($('#cbo_dying_company').val()+'*'+$('#update_id').val()+'*'+$('#txt_mrr_no').val()+'*'+$('#cbo_location_name').val(),'chemical_dyes_issue_cost_print','requires/chemical_dyes_issue_controller');"/>
                            
                            </td>
                            </tr>
                            <tr> 
                            <td colspan="6" align="center"> <div id="recipe_items_list_view" style="margin-top:10px"> </div></td>				
                            </tr> 
                        </table>                 
                        </fieldset>
                    <fieldset>
                    <div style="width:990px;" id="list_container_yarn"></div>
                    </fieldset>
                    </td>
                </tr>
            </table>
        </div>
    <div id="list_product_container" style="max-height:500px; width:20%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>  
    </form>
    </div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
