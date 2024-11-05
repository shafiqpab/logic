<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Raw Material Issue Requisition
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	24-10-2019
Updated by 		: 		
Update date		: 
Oracle Convert 	:		
Convert date	: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Raw Material Issue Requisition Info", "../../", 1, 1,$unicode,'','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
    if($_SESSION['logic_erp']['data_arr'][427])
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][427] );
		echo "var field_level_data= ". $data_arr . ";\n";
	}
 	?>
	
	var str_supervisor = [<? echo substr(return_library_autocomplete( "select distinct(operator_name) as supervisor from subcon_embel_production_dtls", "operator_name"  ), 0, -1); ?>];
	
	function fnc_embel_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "raw_mat_issue_requisition_print", "requires/raw_material_issue_requisition_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(!form_validation('cbo_company_name*txt_job_no','Company*Job No.'))
			{
				return;
			}
			
			var j=0; var dataString=''; //var all_barcodes='';
			$("#emb_details_container").find('tr').each(function()
			{
				///var index = j + 1;
				//var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
				var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
				var hdnBreakId=$(this).find('input[name="hdnBreakId[]"]').val();
				var rcvDtlsId=$(this).find('input[name="hdnRcvDtlsId[]"]').val();
				//var itemGroupId=$('#hdnItemGroupId_' + index).val();
				
				var hdnItemGroupId=$(this).find('input[name="hdnItemGroupId[]"]').val();
				
				// var txtProdQty=$(this).find('input[name="txtProdQty[]"]').val();
				// var txtRejQty=$(this).find('input[name="txtRejQty[]"]').val();
				var hidOrderQty=$(this).find('input[name="hidOrderQty[]"]').val();
				// var txtProcesId=$(this).find('select[name="txtProcesId[]"]').val();
				// var txtWashType=$(this).find('select[name="txtWashType[]"]').val();
				//var txtReWashQty=$(this).find('input[name="txtReWashQty[]"]').val();
				var txtRequQty=$(this).find('input[name="txtRequQty[]"]').val();
				var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
				var txtLot=$(this).find('input[name="txtLot[]"]').val();
				var txtcolor=$(this).find('input[name="txtcolor[]"]').val();
				
				var productId=$(this).find('input[name="productId[]"]').val();
				var sectionId=$(this).find('input[name="sectionId[]"]').val();
				var trimsBreakId=$(this).find('input[name="trimsBreakId[]"]').val();
 				var cboUom 		= $(this).find('select[name="cboUom[]"]').val();
				
				// if( txtProdQty*1>0)
				// {
				// 	j++;
				// 	// dataString += '&hdnBreakId_' + j + '=' + hdnBreakId + '&txtPoId_' + j + '=' + txtPoId  + '&txtProcesId_' + j + '=' + txtProcesId  + '&txtWashType_' + j + '=' + txtWashType  + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtProdQty_' + j + '=' + txtProdQty + '&hidOrderQty_' + j + '=' + hidOrderQty+ '&txtRejQty_' + j + '=' + txtRejQty+ '&txtRemarks_' + j + '=' + txtRemarks;

					
				// }

				if( txtRequQty*1>0)
				{
					j++;
					dataString += '&hdnBreakId_' + j + '=' + hdnBreakId + '&hdnItemGroupId_' + j + '=' + hdnItemGroupId+ '&hdnRcvDtlsId_' + j + '=' + rcvDtlsId + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtRemarks_' + j + '=' + txtRemarks+ '&txtRequQty_' + j + '=' + txtRequQty+ '&productId_' + j + '=' + productId+ '&sectionId_' + j + '=' + sectionId+ '&trimsBreakId_' + j + '=' + trimsBreakId+ '&cboUom_' + j + '=' + cboUom+ '&txtLot_' + j + '=' + txtLot+ '&txtcolor_' + j + '=' + txtcolor;
				}
			});
			
			if(j<1)
			{
			 	alert('Please Insert Qty At Least One Row.');
			 	return;
			}
			// alert(dataString);return;
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('update_id*cbo_company_name*txt_issue_date*cbo_location_name*hid_job_id*hid_order_id*txt_job_no*cbo_issue_basis*cbo_section*txt_targeted_prod_qty*cbo_uom*cbo_store_name*txt_production_id',"../../")+dataString+'&total_row='+j;
			// alert (data);return;
			freeze_window(operation);
			
			http.open("POST","requires/raw_material_issue_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_embel_entry_response;
		}
	}	 
	 
	function fnc_embel_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			
			if(response[0]*1==20*1)
			{
				alert(response[1]);
				release_freezing(); return;
			}	
			
			if(trim(response[0])=='emblQc')
			{
				alert("QC Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			show_msg(response[0]);
			
			if( response[0]==0 || response[0]==1 )
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_production_id').value = response[2];

				setDtlsIds(response[3]);
				document.getElementById('cbo_store_name').setAttribute('disabled', true);
				document.getElementById('txt_job_no').setAttribute('disabled', true);

				// show_list_view(response[1]+'_'+response[3],'dry_production_list_view','dry_production_list_conainer','requires/raw_material_issue_requisition_controller','setFilterGrid("list_view",-1)');
				//var batch_id = $('#txt_batch_id').val();
				//fnc_dtls_data_load(batch_id,response[1]);
				//reset_form( '', 'tbl_item_details', '', '' )
				// fnc_reset_dtls_part(response[0]);
				// show_list_view(2+'**'+response[1],'order_dtls_list_view','emb_details_container','requires/raw_material_issue_requisition_controller','setFilterGrid(\'list_view\',-1)');

				set_button_status(1, permission, 'fnc_embel_entry', 1, 1);
                if($('#print2').length > 0){
                    $('#print2').removeClass('formbutton_disabled');
                    $('#print2').addClass('formbutton');
                }
				// set_button_status(is_update, permission, submit_func, btn_id, show_print)
			}
			if( response[0]==2 )
			{
				location.reload();
			}
			release_freezing();
		}
	}

	function setDtlsIds(idsStr) {
		var dtlsIdsArr = idsStr.split(',');
		for (var i = 0; i < dtlsIdsArr.length; i++) {
			var index = i+1;
			document.getElementById('updateIdDtls_'+index).value = dtlsIdsArr[i];
		}
	}
	 
	function fnc_reset_dtls_part(save_update)
	{
		/*$('#txt_prod_date').val('');
		$('#txt_reporting_hour').val('');
		$('#txt_super_visor').val('');
		$('#cbo_sub_operation').val('');
		$('#cboShift').val('');*/
		$('#txtBuyerStyle_1').val('');
		$('#txtBuyerPO_1').val('');
		$('#txtGmtsItem_1').val('');
		$('#txtGmtsColor_1').val('');
		$('#txtProcesId_1').val(0);
		type_select(0);
		$('#txtWashType_1').val(0);
		// $('#txtProdQty_1').val('');
		// $('#txtRejQty_1').val('');
		$('#txtRemarks_1').val('');
		$('#updateIdDtls_1').val('');
		$('#hdnBreakId_1').val('');
		$('#txtPoId_1').val('');
	}

	function set_form_data_update(data)
	{
		//alert(data);
		var data=data.split("**");
		$('#txtPoId_1').val(data[0]);
		$('#txtBuyerStyle_1').val(data[1]);
		$('#txtBuyerPO_1').val(data[2]);
		//alert(data[3]);
	    $('#hdnBreakId_1').val(data[3]);
	  	//alert( $('#hdnBreakId_1').val());
		$('#txtPO_1').val(data[4]);
		$('#txtGmtsItem_1').val(data[5]);
		$('#txtGmtsColor_1').val(data[6]);
		// $('#txtProdQty_1').val(data[7]);
		// $('#hidOrderQty_1').val(data[7]);
		//alert(trim(data[8]));
		$('#txtProcesId_1').val(trim(data[8]));
		//alert( $('#txtProcesId_1').val());
		type_select(data[8]);
		// $('#txtWashType_1').val(data[9]);
		// $('#txtProdQty_1').val(data[10]);
		// $('#txtRejQty_1').val(data[11]);
		$('#txtRemarks_1').val(data[12]);
		$('#updateIdDtls_1').val(data[13]);
		set_button_status(1, permission, 'fnc_embel_entry',1,0);
		//openmypage_qnty(data[6]);	
	}
	function openmypage_batch()
	{
		var cbo_company_name = $('#cbo_company_name').val();
		
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch Pop-up';	
			var page_link = 'requires/raw_material_issue_requisition_controller.php?cbo_company_name='+cbo_company_name+'&action=batch_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var str_data=this.contentDoc.getElementById("selected_str_data").value;	 //Access form field with id="emailfield"
				
				if(update_id!="")
				{
					freeze_window(5);
					var estr_data=str_data.split("___");
					
					$('#txt_batch_id').val(estr_data[0]);
					$('#txt_batch_no').val( estr_data[1] );
					
					$('#txt_job_no').val(estr_data[2]);
					$('#txt_order_id').val(estr_data[3]);
					$('#txt_order').val(estr_data[4]);
					
					//$('#hdnBreakId').val(estr_data[8]);
					$('#txtbuyerPo').val(estr_data[9]);
					$('#txtstyleRef').val(estr_data[10]);
					/*$('#cbo_operation').val(estr_data[11]);
					$('#cbo_sub_operation').val(estr_data[12]);
					$('#cbo_sub_operation').attr('readonly',true);
					$('#cbo_operation').attr('disabled',true);*/
					
					load_drop_down( 'requires/raw_material_issue_requisition_controller', cbo_company_name+'_'+estr_data[6]+'_'+estr_data[5], 'load_drop_down_buyer', 'party_td');
					$('#txt_targeted_prod_qty').val(estr_data[7]);
					
					fnc_dtls_data_load(estr_data[0],0);
					
					release_freezing();
				} 
			}
		}
	}
	
	function fnc_dtls_data_load(batch_id,uid)
	{
		//alert(batch_id+'_'+uid); return;
		var cbo_company_name = $('#cbo_company_name').val();
		var list_view_orders = return_global_ajax_value( cbo_company_name+'***'+batch_id+'***'+uid, 'order_details', '', 'requires/raw_material_issue_requisition_controller');
		if(list_view_orders!='')
		{
			$("#emb_details_container").html(list_view_orders);
		}
		fnc_total_calculate();
	}
	 
	function fnc_embel_prod_id()
	{
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 
			var company_id = $('#cbo_company_name').val();
			var title = 'Production ID Selection Form';	
			var page_link = 'requires/raw_material_issue_requisition_controller.php?cbo_company_name='+company_id+'&action=embel_production_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//Access the form inside the modal window
				var mstId=this.contentDoc.getElementById("hidden_production_data").value;
				var jobNo=document.getElementById("txt_job_no").value;
				// alert(mstId);return;
				// var emb_data = emblishment_data.split("***");
				// if(emb_data[0]!="")
				// {
					/*freeze_window(5);
					$('#update_id').val(emb_data[0]);
					$('#txt_production_id').val(emb_data[1]);
					$('#cbo_location_name').val(emb_data[2]);
					$('#txt_job_no').val(emb_data[3]);
					$('#txt_order_id').val(emb_data[4]);
					$('#txt_order_no').val(emb_data[5]);
					load_drop_down( 'requires/raw_material_issue_requisition_controller', company_id+'_'+emb_data[6]+'_'+emb_data[7], 'load_drop_down_buyer', 'buyer_td');
					$('#txt_targeted_prod_qty').val(emb_data[8]);
					$('#txt_prod_date').val(emb_data[9]);
					$('#txt_reporting_hour').val(emb_data[10]);
					$('#txt_super_visor').val(emb_data[11]);
					$('#cboShift').val(emb_data[12]);
					load_drop_down( 'requires/raw_material_issue_requisition_controller', company_id+'__'+emb_data[2], 'load_drop_down_floor', 'floor_td');
					load_drop_down( 'requires/raw_material_issue_requisition_controller',company_id+'_'+emb_data[13], 'load_drop_down_machine', 'machine_td' );
					$('#cbo_floor_id').val(emb_data[13]);
					$('#cbo_machine_id').val(emb_data[14]);
					$('#hid_job_id').val(emb_data[15]);*/
					
					// show_list_view(emb_data[15],'show_fabric_desc_listview','list_fabric_desc_container','requires/raw_material_issue_requisition_controller','setFilterGrid("list_view",-1)');
					// show_list_view(emb_data[0]+'_'+emb_data[15],'dry_production_list_view','dry_production_list_conainer','requires/raw_material_issue_requisition_controller','setFilterGrid("list_view",-1)');

					/*$('#cbo_sub_operation').attr('readonly',true);
					$('#cbo_operation').attr('disabled',true);*/
					//alert(3);
					//fnc_dtls_data_load(emb_data[3],emb_data[0]);
					
				// }

				get_php_form_data('2**'+mstId, 'load_mst_php_data_to_form', 'requires/raw_material_issue_requisition_controller');
				
				
            	// show_list_view('2**'+mstId, 'order_dtls_list_view', 'delivery-details', 'requires/raw_material_issue_requisition_controller', '');
            	show_list_view('2**'+mstId+'**'+jobNo,'order_dtls_list_view','emb_details_container','requires/raw_material_issue_requisition_controller', '');
				var company_id = $('#cbo_company_name').val();
				var location_name = $('#cbo_location_name').val();
				var cbo_store = $('#cbo_store_name').val();
				var values=company_id+'_'+location_name+'_'+cbo_store;
				loadStock(values);

            	document.getElementById('txt_targeted_prod_qty').setAttribute('disabled', true);
            	document.getElementById('cbo_uom').setAttribute('disabled', true);
            	document.getElementById('cbo_section').setAttribute('disabled', true);
				document.getElementById('cbo_store_name').setAttribute('disabled', true);
				document.getElementById('txt_job_no').setAttribute('disabled', true);            	

				set_button_status(1, permission, 'fnc_embel_entry',1,1);
                if($('#print2').length > 0){
                    $('#print2').removeClass('formbutton_disabled');
                    $('#print2').addClass('formbutton');
                }
				release_freezing();
			}
		}
	}

	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
	
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
	
			if(hour>23)
			{
				hour=23;
			}
	
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}
			}
	
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}
	
	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function fn_autocomplete()
	{
		 $("#txt_super_visor").autocomplete({
			 source: str_supervisor
		  });
	}
	
	function load_machine()
	{
		//var cbo_company_name = $('#cbo_company_name').val();
		var cbo_source =1; //$('#cbo_knitting_source').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		if(cbo_source==1)
		{
			load_drop_down( 'requires/raw_material_issue_requisition_controller',cbo_company_name+'_'+cbo_floor_id, 'load_drop_down_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/raw_material_issue_requisition_controller',0+'_'+0, 'load_drop_down_machine', 'machine_td' );
		}
	}

	function type_select(process_id)
	{
		load_drop_down( 'requires/raw_material_issue_requisition_controller',process_id, 'load_drop_down_type', 'wash_type_td' );
	}
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#emb_details_container tr').length;
		//alert(rowCount)
		// math_operation( "txtTotProdQty", "txtProdQty_", "+", rowCount );
		// math_operation( "txtTotRejQty", "txtRejQty_", "+", rowCount );
	}

	function openmypage_job()
	{
		if ( form_validation('cbo_company_name*cbo_section','Company,Section')==false ) { return; }

		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_section').value;
		//var data=document.getElementById('cbo_company_name').value;
		page_link='requires/raw_material_issue_requisition_controller.php?action=job_popup&data='+data;
		title='JOB Search';

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data('1**'+ex_data[0], "load_mst_php_data_to_form", "requires/raw_material_issue_requisition_controller" );
				// var within_group = $('#cbo_within_group').val();
				var job_no = $('#txt_job_no').val();
				var cbo_section = $('#cbo_section').val();
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/raw_material_issue_requisition_controller','setFilterGrid("list_view",-1)');
				show_list_view(1+'**'+ex_data[0]+'**'+job_no+'**'+cbo_section,'order_dtls_list_view','emb_details_container','requires/raw_material_issue_requisition_controller','setFilterGrid(\'list_view\',-1)');

				document.getElementById('txt_targeted_prod_qty').setAttribute('disabled', true);
            	document.getElementById('cbo_uom').setAttribute('disabled', true);
            	document.getElementById('cbo_section').setAttribute('disabled', true);

				set_button_status(0, permission, 'fnc_embel_entry',1);
				release_freezing();
			}
		}
	} 

	function set_form_data(data)
	{
		var data=data.split("**");
		$('#txtPoId_1').val(data[0]);
		$('#txtBuyerStyle_1').val(data[1]);
		$('#txtBuyerPO_1').val(data[2]);
		//alert(data[3]);
	    $('#hdnBreakId_1').val(data[3]);
	   // alert( $('#hdnBreakId_1').val());
		$('#txtPO_1').val(data[4]);
		$('#txtGmtsItem_1').val(data[5]);
		$('#txtGmtsColor_1').val(data[6]);
		//$('#txtProdQty_1').val(data[7]);
		$('#hidOrderQty_1').val(data[7]);
		set_button_status(0, permission, 'fnc_embel_entry',1,0);
		//openmypage_qnty(data[6]);	
	}
	function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/raw_material_issue_requisition_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/raw_material_issue_requisition_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/raw_material_issue_requisition_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
		} 
	}

	function checkStoreSelection() {
		return form_validation('cbo_store_name', 'Store');
	}

	function calculateBalance(requiredQty, requisitionQty, balanceField) {
		//alert(requiredQty+'=='+requisitionQty);
		//var balance = requiredQty.replace(',', '');
		//requiredQty.replace(",", "");
		//alert(balance+'=='+requiredQty+'=='+requisitionQty);
		document.getElementById(balanceField).value = ((requiredQty - requisitionQty).toFixed(3));
		
	}

	function loadStock(values) {
		var rowCount = document.getElementById('emb_details_container').childElementCount;
		var itemGroupIds = ''; var productIds = '';		

		for (var i = 1; i <= rowCount; i++) 
		{
			var tempId = document.getElementById('hdnItemGroupId_'+i).value;
			itemGroupIds += tempId + ',';
			var productId = document.getElementById('productId_'+i).value;
			productIds += productId + ',';
		}
		itemGroupIds = itemGroupIds.slice(0, -1);
		productIds = productIds.slice(0, -1);
		// console.log(itemGroupIds);
		var args = values+'**'+rowCount+'**'+itemGroupIds+'**'+productIds;
		get_php_form_data(args, 'load_stock_by_store', 'requires/raw_material_issue_requisition_controller' );
	}

	function checkBalance(requisitionEle, balance, balanceEle, stockEle) 
	{
		//alert(requisitionEle+'=='+balance+'=='+balanceEle);
 		var requisitionQty = parseFloat(requisitionEle.value);
		var tmpBalance = parseFloat(balance);
		var balanceEleQty = parseFloat(balanceEle.value);
		var stockEleQty = parseFloat(stockEle.value);
  		balance = tmpBalance - requisitionQty;
 		var stockbalance = stockEleQty - requisitionQty;
 		if(stockbalance < 0) 
		{
			alert('Requisition Quantity cannot be greater then Stock Quantity');
			requisitionEle.value = 0;
			balanceEle.value = tmpBalance;
		}

		if(balance < 0) 
		{
			alert('Requ. Qty cannot be greater then Balance');
			requisitionEle.value = 0;
			balanceEle.value = tmpBalance;
		}
	}

    function print_report_button_setting(report_ids)
    {
        var report_id=report_ids.split(",");
        for (var k=0; k<report_id.length; k++)
        {
            if(report_id[k]==66)
            {
                $('#Print1').after( '&nbsp;&nbsp;<input type="button"  id="print2" class="formbutton_disabled" style="width:75px; text-align:center;" value="Print 2"  name="Print2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;' );
            }
        }
    }
    function print_button_setting()
    {
        if($('#print2').length > 0){
            $('#print2').remove();
        }
        get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/raw_material_issue_requisition_controller' );
    }

    function fn_report_generated(type){
        var report_title=$( "div.form_caption" ).html();
        print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "raw_mat_issue_requisition_print2", "requires/raw_material_issue_requisition_controller");
        show_msg("3");
    }


</script>
</head>
<body>
<div align="left" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="dryProduction_1" id="dryProduction_1" autocomplete="off" >
    <div style="width:950px;">
        <fieldset style="width:920px; float:left;">
        <legend>Raw Material Issue Requisition</legend>
        <fieldset>
            <table width="100%" cellpadding="1" cellspacing="1" border="0" > 
		        <tr>
		            <td colspan="3" align="right"><strong>Requisition ID</strong></td>
		            <td colspan="3">
		                <input type="text" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="fnc_embel_prod_id();" readonly />
		                <input type="hidden" name="update_id" id="update_id" />		                
		                <input type="hidden" name="hid_job_id" id="hid_job_id" />
		                <input type="hidden" name="hid_order_id" id="hid_order_id" />
		            </td>
		        </tr>
		        <tr>
		            <td width="100" class="must_entry_caption">Company Name</td>
		            <td width="160">
		            	<? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", 0, "load_drop_down('requires/raw_material_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td'); location_select();set_field_level_access(this.value);print_button_setting();"); ?>
		            </td>
		            <!-- <td style="display:none;" id="buyer_td"><? // echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?>
		            </td> -->
		            <td width="100">Location</td>
		            <td width="160" id="location_td">
		            	<? echo create_drop_down("cbo_location_name", 150, $blank_array,"", 1,"-Select Location-", 0,""); ?>
		            </td>
		            <td width="100" class="must_entry_caption">Issue Date</td>
		            <td width="" id="issue_purpose_td"><input type="text" name="txt_issue_date" value="<? echo date("d-m-Y");?>" id="txt_issue_date" class="datepicker" style="width:140px;" /></td>		            
		        </tr>
		        <tr>
		        	<td width="100">Issue Basis </td>
		            <td width="160">
		            <?
		            echo create_drop_down( "cbo_issue_basis", 150, $receive_basis_arr,"", 0, "-- Select --", $selected, "","","15" );
		            ?>
		            </td>
		            <td  class="must_entry_caption" >Section</td>
		            <td>
		            	<?php echo create_drop_down('cbo_section', 150, $trims_section, '', 1, '- Section -', $selected, '', 0); ?>
		            </td>
		            <td  class="must_entry_caption" >Job No.</td>
		            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_job();" placeholder="Browse" readonly />
		        </tr>
		        <tr>
		            <td>Target Production Qty</td>
		            <td>
		            	<input type="text" name="txt_targeted_prod_qty" id="txt_targeted_prod_qty" class="text_boxes_numeric" style="width:55px;" placeholder="Display" readonly />
		            	<?php echo create_drop_down('cbo_uom', 80, $unit_of_measurement, '', 1, '- UOM -', $selected, '', 0); ?>
		            	
		            </td>
		            <td>Order No.</td>
		            <td>
		                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" value="" style="width:140px;" placeholder="Display" />
		            </td>
		            <td class="must_entry_caption">Store</td>
		            <td id="store_td">
		                <?php echo create_drop_down('cbo_store_name', 150, $blank_array, '', 1, '-- Select --', $selected, ''); ?>
		            </td>
		        </tr>
    		</table>
		</fieldset>
        <br />
        <fieldset style="width:920px;">
           <legend>Requisition Details Info</legend>
             <table cellpadding="0" cellspacing="0" width="920" class="rpt_table" border="1" rules="all" id="tbl_item_details">
        	<thead>
            <tr style="display:none;">
				<td style="display: none;" id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 ); ?></td>
            <td style="display: none;">
                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;"  onDblClick="openmypage_batch();" readonly />
                <input type="hidden" name="txt_batch_id" id="txt_batch_id" value="0" />
            </td>
            <td>Floor</td>
            <td id="floor_td"><? echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", $selected, "",1 ); ?></td>
            <td>Machine</td>
            <td id="machine_td"><? echo create_drop_down( "cbo_machine_id", 150, $blank_array,"", 1, "--Select Machine--", $selected, "",1 ); ?></td>
                <th class="must_entry_caption">Date</th>
                <th><input type="text" name="txt_prod_date" id="txt_prod_date" class="datepicker" style="width:80px;" placeholder="Write" readonly/></th>
                <th class="must_entry_caption">Reporting Hour</th>
                <th>
                    <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:90px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" /></th>
                <th colspan="2">Operator / Superviser</th>
                <th colspan="2"><input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();" style="width:160px">
                </th>
                
                <th>Shift</th>
                <th> <? echo create_drop_down( "cboShift", 100, $shift_name,"", 1, 'Select', 0,"",'','','','','','','',''); ?></th>
            </tr>
            <tr>
                <th>Item Group</th>
                <th>Material Description</th>
                <th>Color</th>
                <th>LOT</th>
                <th>Brand</th>
                <th>UOM</th>
                <th>Req. Qty.</th>
                <th class="must_entry_caption">Requ. Qty.</th>
                <th>Balance</th>
                <th>Stock</th>
                <th>Remarks</th>
            </tr>
        </thead> 
        <tbody id="emb_details_container">
            <tr name="tr[]" id="tr_1">
                <td>
                	<?php echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category in (4,101,22) and status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>
                </td>
                <td>
                	<input id="txtdescription_1" name="txtdescription[]" type="text" class="text_boxes" placeholder="Display" style="width: 90%;" />
                </td>
                <td align="right">
                    <input type="text" name="txtcolor[]" id="txtcolor_1" class="text_boxes" placeholder="Write" style="width: 90%;" />
                </td>
				<td align="right">
                    <input type="text" name="txtLot[]" id="txtLot_1" class="text_boxes" placeholder="Write" style="width: 90%;" />
                </td>
				<td align="right">
                    <input type="text" name="txtBrand[]" id="txtBrand_1" class="text_boxes" placeholder="Write" style="width: 90%;" />
                </td>
                <td>
                	<?php echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>
                </td>
                <td align="right">
                	<input type="text" name="txtBalance[]" id="txtBalance_1" class="text_boxes_numeric" placeholder="Write" style="width: 90%;" />
                </td>
                <td align="right">
                	<input type="text" name="txtStock[]" id="txtStock_1" class="text_boxes_numeric" placeholder="Write" style="width: 90%;" />
                </td>
                <td align="right">
                	<input type="text" name="txtReqQty[]" id="txtReqQty_1" class="text_boxes_numeric" placeholder="Write" onBlur="fnc_total_calculate();" style="width: 90%;" />
                </td>
                <td align="right">
                	<input type="text" name="txtRequQty[]" id="txtRequQty_1" class="text_boxes_numeric" placeholder="Write" onBlur="fnc_total_calculate();" style="width: 90%;" />
                </td>
                <td>
                    <input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" placeholder="Write" style="width: 90%;" />
                    <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" />
                    <input type="hidden" name="hdnBreakId[]" id="hdnBreakId_1" />
                    <input type="hidden" name="txtPoId[]" id="txtPoId_1" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_1" />
                    <input type="hidden" name="productId[]" id="productId_1" />
                    <input type="hidden" name="sectionId[]" id="sectionId_1" />
                    <input type="hidden" name="trimsBreakId[]" id="trimsBreakId_1" />
                </td>
            </tr>
        </tbody>
        <tfoot style="display: none">
            <tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                <td colspan="7">Total:</td>
                <td align="center"><input type="text" name="txtTotProdQty" id="txtTotProdQty" class="text_boxes_numeric" style="width:80px" placeholder="Display" readonly /></td>
                <td align="center"><input type="text" name="txtTotRejQty" id="txtTotRejQty" class="text_boxes_numeric" style="width:70px" placeholder="Display" readonly /></td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
    </table>
    <table cellpadding="0" cellspacing="1" width="830">
            <tr>
                 <td align="center" colspan="10" valign="middle" class="button_container">
                    <? echo load_submit_buttons($permission, "fnc_embel_entry", 0,1,"reset_form('dryProduction_1','list_fabric_desc_container*dry_production_list_conainer','','','');",1); ?> 
                </td>	  
            </tr>
        </table>
         </fieldset>
          <br />
    </div>
    <div id="list_fabric_desc_container" style="max-height:500px; width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div style="clear:both"></div>
     <br />
    <div style="width:500px;" id="dry_production_list_conainer"></div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>