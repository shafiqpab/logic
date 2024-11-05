<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create bundle wise sewing inpur
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	11-10-2015
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Embellishment Delivery Entry","../", 1, 1, $unicode,'','');
$mandatory_field_arr = json_encode($_SESSION['logic_erp']['mandatory_field'][460]);
?>
<script>
	var permission='<? echo $permission; ?>';
	var mandatoryFields = "<?php echo implode('*', $_SESSION['logic_erp']['mandatory_field'][460]);?>";
	var mandatoryFieldArr = mandatoryFields.split('*');
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_sysNo()
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_source=$('#cbo_source').val();
		var cbo_serving_company=$('#cbo_emb_company').val();
		var cbo_location=$('#cbo_location').val();
		var cbo_floor=$('#cbo_floor').val();
		var txt_issue_date=$('#txt_issue_date').val();
		var title = 'Challan Selection Form';	
		var page_link = 'requires/bundle_wise_sewing_output_controller.php?cbo_company_name='+cbo_company_name+'&cbo_source='+cbo_source+'&cbo_serving_company='+cbo_serving_company+'&cbo_location='+cbo_location+'&cbo_floor='+cbo_floor+'&txt_issue_date='+txt_issue_date+'&action=challan_no_popup';

		if(cbo_source==3)
		{
			if( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name*cbo_emb_company*cbo_location*cbo_floor','Company Name*Sewing Company*Location*Floor')==false )
			{
				return;
			}
		}
		
		if(cbo_company_name!="" && cbo_serving_company!="")	
		{
		
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=420px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
				if(mst_id!="")
				{ 
					freeze_window(5);
					reset_form('printembro_1','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','cbo_company_name*sewing_production_variable*styleOrOrderWisw*delivery_basis');
					get_php_form_data(mst_id, "populate_data_from_challan_popup", "requires/bundle_wise_sewing_output_controller" );
					
					var delivery_basis=$('#delivery_basis').val();
					if(delivery_basis==3)
					{
						var bundle_nos=return_global_ajax_value(mst_id, 'bundle_nos', '', 'requires/bundle_wise_sewing_output_controller');
						var response_data=return_global_ajax_value(trim(bundle_nos)+"**0**"+mst_id+"**"+cbo_company_name, 'populate_bundle_data_update', '', 'requires/bundle_wise_sewing_output_controller');
						$('#tbl_details tbody tr').remove();
						$('#tbl_details tbody').prepend(response_data);
						var tot_row=$('#tbl_details tbody tr').length; 
						$('#txt_tot_row').val(tot_row);

						var total_qty = 0;
						$("#tbl_details").find('tbody tr').each(function()
						{
							total_qty+=$(this).find('input[name="qty[]"]').val()*1;
						});
						$("#total_bndl_qty").text(total_qty);

						set_button_status(1, permission, 'fnc_sewing_bundle_output_entry',1,0);
					}
					else
					{
						show_list_view(mst_id,'show_dtls_listview','printing_production_list_view','requires/bundle_wise_sewing_output_controller','');
					}
					release_freezing();
				}
			}
		}
	}//end function

	function openmypage(page_link,title)
	{
		if ( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
				var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
				var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;	
				var country_id=this.contentDoc.getElementById("hidden_country_id").value;
					
				if (po_id!="")
				{ 
					freeze_window(5);
					$("#txt_order_qty").val(po_qnty);
					$('#cbo_item_name').val(item_id);
					$("#cbo_country_name").val(country_id);
					
					childFormReset();//child form initialize
	
					get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/bundle_wise_sewing_output_controller" );
					
					var variableSettings=$('#sewing_production_variable').val();
					var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				
					if(variableSettings==1)
					{
						$("#txt_issue_qty").removeAttr("readonly");
					}
					else
					{
						$('#txt_issue_qty').attr('readonly','readonly');
						get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/bundle_wise_sewing_output_controller" ); 
					}
					
					show_list_view(po_id,'show_country_listview','list_view_country','requires/bundle_wise_sewing_output_controller','');	
					set_button_status(0, permission, 'fnc_sewing_bundle_output_entry',1,0);
					release_freezing();
				}
			}
		}//end else
	}//end function

	function generate_report_file(data,action,page)
	{
		window.open("requires/bundle_wise_sewing_output_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_sewing_bundle_output_entry(operation)
	{
		freeze_window(operation);
		var company_id=$('#cbo_company_name').val();
		/*var working_company_mandatory=return_global_ajax_value(company_id, 'load_variable_settings_for_working_company', '', 'requires/bundle_wise_sewing_output_controller');
	
		if(working_company_mandatory==1)
		{
			if($('#cbo_working_company_name').val()==0)
				{
					alert('Working Company is Mandatory');
					return;
				}
				$('#working_company').css('color','blue');
		}*/
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/bundle_wise_sewing_output_controller'); 
			release_freezing();
			 return;
		}
		else if(operation==5)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 
			'emblishment_issue_print_2', 'requires/bundle_wise_sewing_output_controller');
			release_freezing();
			return;
		}else if(operation==6){
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 
			'emblishment_issue_print_3', 'requires/bundle_wise_sewing_output_controller');
			release_freezing();
			return;

		}else if(operation==7){
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 
			'emblishment_issue_print_4', 'requires/bundle_wise_sewing_output_controller');
			release_freezing();
			return;
		}else if(operation==8)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 
			'emblishment_issue_print_5', 'requires/bundle_wise_sewing_output_controller');
			release_freezing();
			return;
		}
	
		if(operation==0 || operation==1 || operation==2)
		{


			var delivery_basis=$('#delivery_basis').val();
			var isActualRejectSelected = true;
			var isActualSpotRejectSelected = true;

			
			
			if(delivery_basis==3)
			{


				var cbo_source=$('#cbo_source').val();
				if(cbo_source==1){
					if ( form_validation('cbo_company_name*cbo_source*cbo_emb_company*txt_issue_date*cbo_location*cbo_floor*cbo_line_no*txt_reporting_hour','Company Name*Source*Embel.Company*Issue Date*Location*Floor*Line No*Reporting Hour')==false )
					{
						release_freezing();
						return;
					}
				}
				else
				{
					if ( form_validation('cbo_company_name*cbo_source*cbo_emb_company*txt_issue_date*txt_reporting_hour','Company Name*Source*Embel.Company*Issue Date*Reporting Hour')==false )
					{
						release_freezing();
						return;
					}
				}


				for (var i = 0; i < mandatoryFieldArr.length; i++) {

					if(mandatoryFieldArr[i]=='cbo_shift_name') {
						if ( form_validation('cbo_shift_name','Shift Name')==false )
						{
							release_freezing();
							return;
						}
					}
				}



				if(!isActualRejectSelected || !isActualSpotRejectSelected) {
					release_freezing();
					return;
				}
				
				var sewing_output_date = $('#txt_issue_date').val();
				if(date_compare($('#txt_sewing_input_date').val(), sewing_output_date)==false)
				{
					alert("Sewing Output Date cannot be allowed before the Sewing Input Date");
					release_freezing();
					return;
				}

				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_issue_date').val(), current_date)==false)
				{
					alert("Print Delivery Date Can not Be Greater Than Current Date");
					release_freezing();
					return;
				}
				
				var j=0; var dataString='';
				$("#tbl_details").find('tbody tr').each(function()
				{
					var cutNo=$(this).find('input[name="cutNo[]"]').val();
					var bundleNo=$(this).find("td:eq(1)").text();
					var barcodeNo=$(this).find("td:eq(1)").attr('title');
					var isRescan=$(this).find('input[name="isRescan[]"]').val();
					var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
					var orderId=$(this).find('input[name="orderId[]"]').val();
					var gmtsitemId=$(this).find('input[name="gmtsitemId[]"]').val();
					var countryId=$(this).find('input[name="countryId[]"]').val();
					var colorId=$(this).find('input[name="colorId[]"]').val();
					var sizeId=$(this).find('input[name="sizeId[]"]').val();
					var qty=$(this).find('input[name="qty[]"]').val();
					
					var rejectQty=$(this).find('input[name="rejectQty[]"]').val();
					var alterQty=$(this).find('input[name="alterQty[]"]').val();
					var spotQty=$(this).find('input[name="spotQty[]"]').val();
					var replaceQty=$(this).find('input[name="replaceQty[]"]').val();
					var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
					var actual_reject=$(this).find('input[name="actual_reject[]"]').val();
					var actual_alter=$(this).find('input[name="actual_alter[]"]').val();
					var actual_spot=$(this).find('input[name="actual_spot[]"]').val();

					for (var i = 0; i < mandatoryFieldArr.length; i++) {
						if(mandatoryFieldArr[i]=='txt_alter_reject_record') {
							if( (alterQty*1 > 0) ) {
								if( !actual_alter ) {
									alert('Please fill up popup Reject Record for: ' + bundleNo);
									isActualRejectSelected = false;
									release_freezing();
									return;
								}
							}
						}

						if(mandatoryFieldArr[i]=='txt_spot_reject_record') {
							if( (spotQty*1 > 0) ) {
								if( !actual_spot ) {
									alert('Please fill up popup Reject Record for: ' + bundleNo);
									isActualSpotRejectSelected = false;
									release_freezing();
									return;
								}
							}
						}

					}
					
					try 
					{
						j++;
						
						dataString+='&bundleNo_' + j + '=' + bundleNo + '&orderId_' + j + '=' + orderId + '&gmtsitemId_' + j + '=' + gmtsitemId + '&countryId_' + j + '=' + countryId + '&colorId_' + j + '=' + colorId + '&sizeId_' + j + '=' + sizeId + '&colorSizeId_' + j + '=' + colorSizeId + '&qty_' + j + '=' + qty + '&dtlsId_' + j + '=' + dtlsId + '&rejectQty_' + j + '=' + rejectQty + '&alterQty_' + j + '=' + alterQty + '&spotQty_' + j + '=' + spotQty + '&replaceQty_' + j + '=' + replaceQty + '&actual_reject_' + j + '=' + actual_reject+ '&actual_alter_' + j + '=' + actual_alter+ '&actual_spot_' + j + '=' + actual_spot + '&cutNo_' + j + '=' + cutNo+ '&barcodeNo_' + j + '=' + barcodeNo+ '&isRescan_' + j + '=' + isRescan+ '&dtlsId_' + j + '=' + dtlsId;
					}
					catch(e) 
					{
						//got error no operation
					}
				});
				
				if(j<1)
				{
					alert('No data');
					release_freezing();
					return;
				}

				if(!isActualRejectSelected || !isActualSpotRejectSelected) {
					release_freezing();
					return;
				}

				var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('garments_nature*cbo_company_name*sewing_production_variable*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_organic*txt_system_id*delivery_basis*txt_challan_no*cbo_line_no*txt_reporting_hour*txt_wo_id*txt_wo_no*cbo_shift_name*txt_remark*cbo_working_company_name*cbo_working_location',"../")+dataString;
			}
			else
			{
				if ( form_validation('cbo_company_name*txt_order_no*cbo_source*cbo_emb_company*txt_issue_date*txt_issue_qty*cbo_line_no*txt_reporting_hour','Company Name*Order No*Source*Embel.Company*Issue Date*Issue Quantity*Line No.*Reporting Hour')==false )
				{
					release_freezing();
					return;
				}		
				else
				{
					var current_date='<? echo date("d-m-Y"); ?>';
					if(date_compare($('#txt_issue_date').val(), current_date)==false)
					{
						alert("Print Delivery Date Can not Be Greater Than Current Date");
						release_freezing();
						return;
					}	
					var sewing_production_variable = $("#sewing_production_variable").val();
					var colorList = ($('#hidden_colorSizeID').val()).split(",");
					
					var i=0;var colorIDvalue='';
					if(sewing_production_variable==2)//color level
					{
						$("input[name=txt_color]").each(function(index, element) {
							if( $(this).val()!='' )
							{
								if(i==0)
								{
									colorIDvalue = colorList[i]+"*"+$(this).val();
								}
								else
								{
									colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
								}
							}
							i++;
						});
					}
					else if(sewing_production_variable==3)//color and size level
					{
						$("input[name=colorSize]").each(function(index, element) {
							if( $(this).val()!='' )
							{
								if(i==0)
								{
									colorIDvalue = colorList[i]+"*"+$(this).val();
								}
								else
								{
									colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
								}
							}
							i++;
						});
					}
					
					var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_issue_qty*cbo_line_no*txt_challan*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*hidden_break_down_html*txt_mst_id*txt_organic*txt_challan_no*txt_system_id*delivery_basis*cbo_working_company_name*cbo_working_location',"../");
				}
			}
			
			//alert (data);return;
			
			http.open("POST","requires/bundle_wise_sewing_output_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
		}
	}
  
	function fnc_issue_print_embroidery_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			var item_id=$('#cbo_item_name').val();
			var country_id = $("#cbo_country_name").val();
			
			var reponse=http.responseText.split('**');		 
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_sewing_bundle_output_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				show_msg(trim(reponse[0]));
			
				document.getElementById('txt_system_id').value = reponse[1];
				document.getElementById('txt_challan_no').value = reponse[2];
				$('#txt_issue_date').attr('disabled','true'); 
				var delivery_basis=$('#delivery_basis').val();
				if(delivery_basis==3)
				{
					set_button_status(1, permission, 'fnc_sewing_bundle_output_entry',1,1);
				}
				else
				{
					reset_form('','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','cbo_company_name*sewing_production_variable*styleOrOrderWisw*cbo_source*cbo_emb_company*cbo_knitting_source*cbo_location*cbo_floor*txt_organic*txt_issue_date');
					show_list_view(reponse[1],'show_dtls_listview','printing_production_list_view','requires/bundle_wise_sewing_output_controller','');
					set_button_status(0, permission, 'fnc_sewing_bundle_output_entry',1,1);
				}

				if(reponse[0]==2)
				{
					$("#tbl_details tr").remove(); 
					// $("#printembro_1").trigger('reset');
				}
			}
			else if(reponse[0]==786)
			{
				alert("Projected PO is not allowed to production. Please check variable settings."); 
				release_freezing();
				return;
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]); 
				release_freezing();
				return;
			}
			if(reponse[0]!=15)
			{
			  release_freezing();
			}
		}
	} 

	function childFormReset()
	{
		reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*txt_mst_id','','');
		$('#txt_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cumul_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_to_issue').attr('placeholder','');//placeholder value initilize
		$("#breakdown_td_id").html('');
	}

	function fn_total(tableName,index) // for color and size level
	{
		var filed_value = $("#colSize_"+tableName+index).val();
		var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
		if(filed_value*1 > placeholder_value*1)
		{
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
				void(0);
			else
			{
				$("#colSize_"+tableName+index).val('');
			}
		}
		
		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_issue_qty").val(totalVal);
	}

	function fn_colorlevel_total(index) //for color level
	{
		var filed_value = $("#colSize_"+index).val();
		var placeholder_value = $("#colSize_"+index).attr('placeholder');
		if(filed_value*1 > placeholder_value*1)
		{
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
				void(0);
			else
			{
				$("#colSize_"+index).val('');
			}
		}
		
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_issue_qty").val( $("#total_color").val() );
	} 

	function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
	{
		freeze_window(5);
		
		//childFormReset();//child from reset
		$("#cbo_item_name").val(item_id);
		$("#txt_order_qty").val(po_qnty);
		$("#cbo_country_name").val(country_id);
		
		get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/bundle_wise_sewing_output_controller" );
		
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		
		if(variableSettings==1)
		{
			$("#txt_issue_qty").removeAttr("readonly");
		}
		else
		{
			$('#txt_issue_qty').attr('readonly','readonly');
		}
		
		set_button_status(0, permission, 'fnc_sewing_bundle_output_entry',1,0);
		release_freezing();
	}

	function load_html()
	{
		var delivery_basis=$('#delivery_basis').val(); 
		$('#printing_production_list_view').val('');
		
		if(delivery_basis==3)
		{
			$('#tbl_details_order').hide();
			$('#printing_production_list_view').hide();
			$('#tbl_details_bundle').show();
			$('#tbl_details tbody tr').remove();
			$('#bundle_list_view').show();
			$('#list_view_country').hide();
			$( "#txt_bundle_no" ).focus();
		}
		else
		{
			$('#tbl_details_order').show();
			$('#printing_production_list_view').show();
			$('#tbl_details_bundle').hide();
			$('#bundle_list_view').hide();
			$('#list_view_country').show();
			childFormReset();
		}
		set_button_status(0, permission, 'fnc_sewing_bundle_output_entry',1,1);
	}

	function openmypage_bundle(page_link,title)
	{
		if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
		{
			return;
		}
		else
		{
			var tot_row=$('#tbl_details tbody tr').length; 
			var line_id_val=$('#cbo_line_no').val();
			var line_id=0;	
			if( (tot_row*1) <1 && (line_id_val*1)==0)line_id=0; else line_id=$('#cbo_line_no').val();
			
			var bundleNo='';
			$("#tbl_details").find('tbody tr').each(function()
			{
				bundleNo+=$(this).find("td:eq(1)").text()+',';
			});
		
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo+'&line_id='+line_id, title, 'width=890px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_nos").value;//po id
				var hidden_bundle_line=this.contentDoc.getElementById("hidden_bundle_line").value;//po id
				//alert(hidden_bundle_nos)
				if (hidden_bundle_nos!="")
				{ 
					//fnc_duplicate_bundle(hidden_bundle_nos);
					
					var tot_row=$('#tbl_details tbody tr').length; 
						
					if( (tot_row*1) <1)
						get_php_form_data(hidden_bundle_nos+"__"+hidden_bundle_line+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_sewing_output_controller');
					create_row(hidden_bundle_nos,hidden_bundle_line);
				}
			}
		}//end else
	}//end function

	function fnc_duplicate_bundle(bundle_no)
	{
		var challan_duplicate=return_ajax_request_value( bundle_no+"__"+$('#cbo_company_name').val()+"__"+$('#cbo_line_no').val(),"challan_duplicate_check", "requires/bundle_wise_sewing_output_controller");
		
		var ex_challan_duplicate=challan_duplicate.split("_");
		
		if(trim(ex_challan_duplicate[0])!='') 
		{
			//var alt_str=ex_challan_duplicate[1].split("##");
			//var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
			 alert(trim(ex_challan_duplicate[0]));
			$('#txt_bundle_no').val('');
			return;
		}
		else
		{

			//alert( ex_challan_duplicate[2] );
			if( (ex_challan_duplicate[4]*1)>1 ) $('#cbo_line_no').val( ex_challan_duplicate[4] );
			
			//if( ex_challan_duplicate[2]!='' && $('#cbo_line_no').val()==0)
			if( (ex_challan_duplicate[3]*1)>1 &&  $('#cbo_line_no').val()==0 ) //(ex_challan_duplicate[3]*1)>1 
			{
				var page_link='requires/bundle_wise_sewing_output_controller.php?action=bundle_popup_line_select&data='+ex_challan_duplicate[2]+'&company_id='+$('#cbo_company_name').val();
				
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, '', 'width=590px,height=220px,center=1,resize=0,scrolling=0','')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_info").value;//po id
					$('#cbo_line_no').val(hidden_bundle_nos);
					 
					create_row(bundle_no,hidden_bundle_nos);
					var tot_row=$('#tbl_details tbody tr').length; 
				 
					if( (tot_row*1) <2)
						get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_sewing_output_controller');
				}
			}
			else
			{
				create_row(bundle_no);
				var tot_row=$('#tbl_details tbody tr').length; 
			 
				if( (tot_row*1) <2)
					get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_sewing_output_controller');
			}
		}
		$('#txt_bundle_no').val('');
	}

	$('#txt_bundle_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
			{
				return;
			}
			e.preventDefault();
			var txt_bundle_no=trim($('#txt_bundle_no').val().toUpperCase());
			var flag=1;
			$("#tbl_details").find('tbody tr').each(function()
			{
				var bundleNo=$(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(1)").attr('title');
				if(txt_bundle_no==barcodeNo){
					alert("Bundle No: "+bundleNo+" already scan, try another one.");
					$('#txt_bundle_no').val('');
					flag=0;
					return false;
				}
			});
			
			if(flag==1)
			{
				fnc_duplicate_bundle(txt_bundle_no);
			}
		}
	});

	function create_row(bundle_nos, vline_no)
	{
		if(!vline_no) var vline_no=$('#cbo_line_no').val();
		freeze_window(5);
		
		var row_num=$('#txt_tot_row').val();
		var response_data_msg=return_global_ajax_value(bundle_nos+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+vline_no, 'populate_bundle_data_check', '', 'requires/bundle_wise_sewing_output_controller');
		if(response_data_msg==2)
		{
			alert('All Bundle must be under Selected Company/line. Please Check');
			release_freezing();
			return;
		}
		else if(response_data_msg==3)
		{
			alert('Please Check Your Previous Production Process.');
			release_freezing();
			return;
		}
		var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+$('#cbo_line_no').val(), 'populate_bundle_data', '', 'requires/bundle_wise_sewing_output_controller');
		$('#tbl_details tbody').prepend(response_data);
		var tot_row=$('#tbl_details tbody tr').length; 
		$('#txt_tot_row').val(tot_row);
		// fn_sum_bundle_qty();
		var total_qty = 0;
		$("#tbl_details").find('tbody tr').each(function()
		{
			total_qty+=$(this).find('input[name="qty[]"]').val()*1;
		});
		$("#total_bndl_qty").text(total_qty);

		release_freezing();
	}


	// for rescan=================================================================================
	function openmypage_bundle_rescan(page_link,title)
	{
		 
		if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
		{
			return;
		}
		else
		{
			var tot_row=$('#tbl_details tbody tr').length; 
			var line_id_val=$('#cbo_line_no').val();
			var line_id=0;	
			if( (tot_row*1) <1 && (line_id_val*1)==0) line_id=0; else line_id=$('#cbo_line_no').val();
			var bundleNo='';
			$("#tbl_details").find('tbody tr').each(function()
			{
				bundleNo+=$(this).find("td:eq(1)").text()+',';
			});
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo+'&line_id='+line_id, title, 'width=990px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_nos").value;//po id
				var hidden_bundle_line=this.contentDoc.getElementById("hidden_bundle_line").value;//po id
				var is_defect=this.contentDoc.getElementById("is_defect").checked;//po id
				//alert(is_defect);
				
				
				if(hidden_bundle_nos!="")
				{ 
					var tot_row=$('#tbl_details tbody tr').length; 
					if( (tot_row*1) <1)
					get_php_form_data(hidden_bundle_nos+"__"+hidden_bundle_line+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_sewing_output_controller');
					create_row_rescan(hidden_bundle_nos,hidden_bundle_line,is_defect)
				}
			}
		}//end else
	}//end function

	function create_row_rescan(bundle_nos,vline_no,is_defect)
	{
		freeze_window(5);
		if(!vline_no) var vline_no=$('#cbo_line_no').val();
		var row_num=$('#txt_tot_row').val();
		var response_data_msg=return_global_ajax_value(bundle_nos+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+vline_no, 'populate_bundle_data_check', '', 'requires/bundle_wise_sewing_output_controller');
		if(response_data_msg==2)
		{
			alert('All Bundle must be under Selected Company. Please Check');
			release_freezing();
			return;
		}
		else if(response_data_msg==3)
		{
			alert('Please Check Your Previous Production Process.');
			release_freezing();
			return;
		}
		
		var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+$('#cbo_line_no').val()+"**"+is_defect, 'populate_bundle_data_rescan', '', 'requires/bundle_wise_sewing_output_controller');
		$('#tbl_details tbody').prepend(response_data);
		var tot_row=$('#tbl_details tbody tr').length; 
		$('#txt_tot_row').val(tot_row);
		release_freezing();
	}

	$('#txt_bundle_rescan').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			if ( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
			{
				return;
			}
			e.preventDefault();
			var txt_bundle_no=trim($('#txt_bundle_rescan').val().toUpperCase());
			var flag=1;
			$("#tbl_details").find('tbody tr').each(function()
			{
				var bundleNo=$(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(1)").attr('title');
				if(txt_bundle_no==barcodeNo){
					alert("Bundle No: "+bundleNo+" already scan, try another one.");
					$('#txt_bundle_rescan').val('');
					flag=0;
					return false;
				}
			});
			
			if(flag==1)
			{
				fnc_duplicate_bundle_rescan(txt_bundle_no);
			}
		}
	});
	
	function fnc_duplicate_bundle_rescan(bundle_no)
	{
		
		var challan_duplicate=return_ajax_request_value( bundle_no+"__"+$('#cbo_company_name').val(),"qty_rescan_check", "requires/bundle_wise_sewing_output_controller");
		var ex_challan_duplicate=challan_duplicate.split("_");
		
		if(ex_challan_duplicate[0]==4)
		{
			alert("Please Scan First.");
		}
		if(ex_challan_duplicate[0]==3)
		{
			alert("No Data Found.");
		}
		if(ex_challan_duplicate[0]==2) 
		{
			var alt_str=ex_challan_duplicate[1].split("##");
			var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
			alert(al_msglc);
			$('#txt_bundle_rescan').val('');
			return;
		}
		else
		{
			if( (ex_challan_duplicate[4]*1)>1 ) $('#cbo_line_no').val( ex_challan_duplicate[4] );
			 
			if( (ex_challan_duplicate[3]*1)>1 && $('#cbo_line_no').val()==0)
			{
				var page_link='requires/bundle_wise_sewing_output_controller.php?action=bundle_popup_line_select&data='+ex_challan_duplicate[2]+"&company_id="+$('#cbo_company_name').val();
				
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, '', 'width=590px,height=220px,center=1,resize=0,scrolling=0','')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_info").value;//po id
					$('#cbo_line_no').val(hidden_bundle_nos);
					
					var tot_row=$('#tbl_details tbody tr').length; 
					//alert(tot_row)	
					if( (tot_row*1) <2)
						get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_sewing_output_controller');
					create_row_rescan(bundle_no,hidden_bundle_nos);
					 
				}
			}
			else
			{
				var tot_row=$('#tbl_details tbody tr').length; 
					//alert(tot_row)	
					if( (tot_row*1) <2)
						get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_sewing_output_controller');
					create_row_rescan(bundle_no,$('#cbo_line_no').val());
					
				 +"__1"
			}
			
			
		}
		$('#txt_bundle_rescan').val('');
	}

	function fn_deleteRow( rid )
	{
		$("#tr_"+rid).remove();

		var total_qty = 0;
		$("#tbl_details").find('tbody tr').each(function()
		{
			total_qty+=$(this).find('input[name="qty[]"]').val()*1;
		});
		$("#total_bndl_qty").text(total_qty);
		
	}
	
	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
		
		var colon_contains=val.contains(":");
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
	
	function calculate_qcpasss(id,replace_field_disable=0)
	{ 
		//alert(23);
		var prodQty=$("#prodQty_"+id).text()*1;
		var rejectQty=$("#rejectQty_"+id).val()*1;
		
		var alterQty=$("#alterQty_"+id).val()*1;
		var spotQty=$("#spotQty_"+id).val()*1;
		if(replace_field_disable==1){alterQty=0;spotQty=0;}
		
		var totReject=(rejectQty+alterQty+spotQty);
		var replaceQty=$("#replaceQty_"+id).val()*1;
		var qc_qty=(prodQty-totReject)+replaceQty;
		
		if(prodQty<qc_qty)
		{
			qc_qty=qc_qty=(prodQty-totReject);
			$("#replaceQty_"+id).val('');
		}
		
		if(totReject>=prodQty)
		{
			$("#rejectQty_"+id).val('');
			$("#alterQty_"+id).val('');
			$("#spotQty_"+id).val('');
			$("#replaceQty_"+id).val('');
			$("#qcQty_"+id).text(prodQty);
		}
		else
		{
			$("#qty_"+id).val(qc_qty);
			$("#qcQty_"+id).text(qc_qty);
		}

		var total_qty = 0;
		$("#tbl_details").find('tbody tr').each(function()
		{
			total_qty+=$(this).find('input[name="qty[]"]').val()*1;
		});
		$("#total_bndl_qty").text(total_qty);
	}

	function pop_entry_reject(row_id,replace_field_disable=0)
	{
		var actual_infos=$("#actual_reject_"+row_id).val();
		
		var page_link='requires/bundle_wise_sewing_output_controller.php?action=reject_qty_popup&actual_infos='+actual_infos;
		var title='Reject Record Info';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var actual_infos=this.contentDoc.getElementById("actual_reject_infos").value; 
			var actual_qty=this.contentDoc.getElementById("actual_reject_qty").value;
			$("#actual_reject_"+row_id).val(actual_infos);
			// $("#rejectQty_"+row_id).val(actual_qty);
			calculate_qcpasss(row_id,replace_field_disable);

			//alert(actual_infos+'=='+actual_qty) 
		}
	}

	function pop_entry_alter(row_id)
	{
		var actual_infos=$("#actual_alter_"+row_id).val();
		
		var page_link='requires/bundle_wise_sewing_output_controller.php?action=alter_qty_popup&actual_infos='+actual_infos;
		var title='Alter Record Info';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var actual_infos=this.contentDoc.getElementById("actual_alter_infos").value; 
			var actual_qty=this.contentDoc.getElementById("actual_alter_qty").value;
			$("#actual_alter_"+row_id).val(actual_infos);
			// $("#alterQty_"+row_id).val(actual_qty);
			//alert(actual_infos+'=='+actual_qty) 
		}
	}

	function pop_entry_spot(row_id)
	{
		var actual_infos=$("#actual_spot_"+row_id).val();
		
		var page_link='requires/bundle_wise_sewing_output_controller.php?action=spot_qty_popup&actual_infos='+actual_infos;
		var title='Spot Record Info';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=300px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var actual_infos=this.contentDoc.getElementById("actual_spot_infos").value; 
			var actual_qty=this.contentDoc.getElementById("actual_spot_qty").value;
			$("#actual_spot_"+row_id).val(actual_infos);
			// $("#spotQty_"+row_id).val(actual_qty);
			//alert(actual_infos+'=='+actual_qty) 
		}
	}

	function change_mode(source_id)
	{
		if(source_id==1)
		{
			get_php_form_data($('#cbo_company_name').val(),'load_variable_settings','requires/bundle_wise_sewing_output_controller'); get_php_form_data($('#cbo_company_name').val(),'load_variable_settings_for_working_company','requires/bundle_wise_sewing_output_controller'); load_html();
				
		}
		else
		{
			get_php_form_data($('#cbo_company_name').val(),'load_variable_settings','requires/bundle_wise_sewing_output_controller'); get_php_form_data($('#cbo_emb_company').val(),'load_variable_settings_for_working_company','requires/bundle_wise_sewing_output_controller'); load_html();
		
		}
	}

	function pageReset()
	{
		reset_form('printembro_1','list_view_country*printing_production_list_view','','','txt_issue_date,<? echo date("d-m-Y"); ?>','');
		
		$('#cbo_company_name').prop('disabled','false');
		// document.getElementById("cbo_company_name").disabled == false;
		$('#tbl_details_order').show();
		$('#printing_production_list_view').show();
		$('#tbl_details_bundle').hide();
		$('#bundle_list_view').hide();
		disable_enable_fields( 'cbo_company_name*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date', 0, "", "" );
	}

	/*function working_com_fnc()
	{
		var company_id=$('#cbo_company_name').val();
		var working_company_mandatory=return_global_ajax_value(company_id, 'load_variable_settings_for_working_company', '', 'requires/bundle_wise_sewing_output_controller');
	
		if(working_company_mandatory==1)
		{
			$('#working_company').css('color','blue');
			//alert('Working Company is Mandatory');
			return;
		}
		else
		{
			$('#working_company').css('color','black');
		}
	}*/

	function openmypage_woNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var cbo_service_company = $('#cbo_emb_company').val()		

		if (form_validation('cbo_company_name*cbo_source*cbo_emb_company','Company*Source*Service Company')==false)
		{
			return;
		}
		else
	  	{			
			if (form_validation('cbo_emb_company','Service Company')==false)
			{
				return;
			}
			
			var page_link='requires/bundle_wise_sewing_output_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&supplier_id='+cbo_service_company+'&action=service_booking_popup';
			var title='WO Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];				
				var theemail=this.contentDoc.getElementById("selected_booking");
				if (theemail.value!="")
  				{	  				
					var wo_data=(theemail.value).split("_");
	  				var wo_no=wo_data[1];
	  				var wo_id=wo_data[0];
					$('#txt_wo_id').val(wo_id);
					$('#txt_wo_no').val(wo_no);
					$('#txt_wo_no').attr('disabled',true);
					
  				}
				
			}
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<table style="margin:0 auto"><tr><td>
 <div>
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div>
 		<fieldset>
        <legend>Production Module</legend>
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
            <fieldset style="width:930px; margin:0 auto;">
                <table width="100%">
                	<tr>
                        <td align="right" colspan="3">Entry No</td>
                        <td colspan="3"> 
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:167px" onDblClick="openmypage_sysNo();" placeholder="Double click to search" />
                          <input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                            	echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "" );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />	 
                            <input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="delivery_basis" />
                        </td>
                          <td class="must_entry_caption">Source</td>
                          <td>
                              <? 
                              	echo create_drop_down( "cbo_source", 180, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/bundle_wise_sewing_output_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );load_drop_down( 'requires/bundle_wise_sewing_output_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td' );change_mode(this.value);", 0, '1,3' );
                              ?>
                          </td>
                          <td class="must_entry_caption">Sewing Company</td>
                          <td id="emb_company_td">
                              <input name="cbo_emb_company_show" id="cbo_emb_company_show" class="text_boxes" type="text" style="width:167px"  placeholder="Dispay" readonly/>
                              <input name="cbo_emb_company" id="cbo_emb_company" class="text_boxes" type="hidden" style="width:167px" />
                          </td>
                          
                    </tr>
                    <tr>
                          <td class="must_entry_caption">Location</td>
                          <td id="location_td">
                              <? 
                              	echo create_drop_down( "cbo_location", 180, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                              ?>
                          </td>
                         <td class="must_entry_caption">Floor</td>
                         <td id="floor_td">
                             <input name="txt_floor_name" id="txt_floor_name" class="text_boxes" type="text" style="width:167px"  placeholder="Dispay" readonly/>
                              <input name="cbo_floor" id="cbo_floor" class="text_boxes" type="hidden" style="width:167px" />
                         </td>
                         <td>Organic</td>
                         <td>
                            <input name="txt_organic" id="txt_organic" class="text_boxes" type="text" style="width:167px" />
                         </td>
                    </tr>
                    <tr>
                         <td class="must_entry_caption">Sewing Date</td>
                         <td> 
                         	<input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;"  />
                         	<input type="hidden" name="txt_sewing_input_date" id="txt_sewing_input_date"  value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;"  />
                         </td>
                         <td class="must_entry_caption">Line No.</td>
                         <td id="line_td"> 
                             <? 
								echo create_drop_down( "cbo_line_no", 180, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                             ?>
                         </td>
                         <td class="must_entry_caption">Hour</td>
                         <td> 
                            <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:167px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" value="<?= date('H:i',time());?>" />
                         </td>
                    </tr>
                    <tr style="display:none;">
                     <td id="working_company">Working Company</td>
                        <td>
                        	<? 
								echo create_drop_down( "cbo_working_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --",$selected,"load_drop_down( 'requires/bundle_wise_sewing_output_controller', $('#cbo_working_company_name').val(), 'load_drop_down_working_location', 'working_location_td' );" );
                            ?>
                        </td>
                         <td>Working Location</td>
                          <td id="working_location_td">
                              <? 
                              	echo create_drop_down( "cbo_working_location", 180, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                              ?>
                          </td>
                          </tr>
                    <tr>
                    	<td>WO NO</td>
                        <td>
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:167px;" placeholder="Browse/Write/scan" onDblClick="openmypage_woNo();" />
                            <input type="hidden" id="txt_wo_id" value="0" />
                        </td>
                    	<td>Shift Name</td>
                        <td>                            
                            <?
                            	$sql=sql_select( "SELECT id, shift_name from shift_duration_entry where status_active=1 and production_type=3 order by shift_name");
                            	$shift_arr = array();
                            	foreach ($sql as $val) 
                            	{
                            		$shift_arr[$val['SHIFT_NAME']]  = $shift_name[$val['SHIFT_NAME']];
                            	}
                                    	
                              	echo create_drop_down( "cbo_shift_name", 180, $shift_arr,"", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                    	<td>Remarks</td>
                        <td>
                            <input name="txt_remark" id="txt_remark" class="text_boxes" type="text" style="width: 167px;" />
                        </td>
                    </tr>
                    
                </table>
                </fieldset> <br />
                <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_order">
                    <tr>
                          <td width="35%" valign="top">
                               <fieldset>
                                  <legend>New Entry</legend>
                                       <table  cellpadding="0" cellspacing="2" width="100%">
                                          <tr>
                                               <td width="80" class="must_entry_caption" id="td_caption">Order No</td>
                                               <td colspan="3" width="110"> 
                                                   <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/bundle_wise_sewing_output_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:212px" readonly />
                            <input type="hidden" id="hidden_po_break_down_id" value="" />
                                               </td>
                                          </tr> 
                                          <tr>
                                               <td class="must_entry_caption">Issue Qty</td> 
                                               <td colspan="3"> 
                                                   <input type="text" name="txt_issue_qty" id="txt_issue_qty"  class="text_boxes_numeric"  style="width:100px" readonly >
                                                   <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                                   <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                               </td> 
                                          </tr> 
                                          <tr>    
                                          	<td>Order Qnty</td>
                                         	<td>
                                          		<input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:100px" disabled readonly>
                                          	</td>
                                          </tr>
                                          <tr>   
                                          	<td>Buyer</td>
                                            <td>
                                                <? 
                                                	echo create_drop_down( "cbo_buyer_name", 112, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "Dispaly", $selected, "",1,0 );
                                                ?>	
                                            </td> 
                                          </tr>
                                          <tr>    
                                          	<td>Style</td>
                                         	<td>
                                          		<input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px" disabled  readonly>
                                          	</td>
                                          </tr> 
                                          <tr>    
                                          	<td>Item</td>
                                         	<td>
                                          		<? echo create_drop_down( "cbo_item_name", 110, $garments_item,"", 1, "Display", $selected, "",1,0 ); ?>
                                          	</td>
                                          </tr> 
                                          <tr>    
                                          	<td>Country</td>
                                         	<td>
                                          		<?
													echo create_drop_down('cbo_country_name',110,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);
												?> 
                                          	</td>
                                          </tr> 
                                          <tr>
                                               <td>Challan No</td> 
                                               <td>
                                               	<input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" disabled readonly />
                                               </td>
                                               <td>Iss. ID</td> 
                                               <td>
                                               	<input type="text" name="txt_iss_id" id="txt_iss_id" class="text_boxes" style="width:50px" disabled readonly />
                                               </td>
                                        </tr>
                                        <tr>
                                        	<td>Remarks</td> 
                                            <td colspan="3"> 
                                               <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:217px" title="450 Characters Only." />
                                           	</td>
                                    	</tr>
                                    </table>
                                </fieldset>
                          </td>
                          <td width="1%" valign="top"></td>
                          <td width="22%" valign="top">
                                <fieldset>
                                <legend>Display</legend>
                                    <table  cellpadding="0" cellspacing="2" width="100%" >
                                        <tr>
                                            <td width="100">Cutt. Qty</td>
                                            <td width="90"> 
                                            <input type="text" name="txt_cutting_qty" id="txt_cutting_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Cuml. Issue Qty</td>
                                            <td > 
                                            <input type="text" name="txt_cumul_issue_qty" id="txt_cumul_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Yet to Issue</td>
                                            <td> 
                                            <input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>	
                            </td>
                            <td width="40%" valign="top">
                                <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                            </td>
                    </tr>
               	</table>
                <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_bundle" style="display:none">
                	<tr>
                    	<td>
                            <fieldset>
                              <legend>New Entry</legend>
                               <table  cellpadding="0" cellspacing="2" width="100%">
                                  <tr>
                                       <td width="80" class="must_entry_caption" id="td_caption">Barcode No</td>
                                       <td colspan="3" width="110"> 
                                           <input name="txt_bundle_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle('requires/bundle_wise_sewing_output_controller.php?action=bundle_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Bundle Search')" id="txt_bundle_no" class="text_boxes" style="width:212px" />
                                       </td>
                                       <td width="100" class="must_entry_caption" id="td_caption">Re-Scan Barcode</td>
                                       <td colspan="2" width="110"> 
                                           <input name="txt_bundle_rescan" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle_rescan('requires/bundle_wise_sewing_output_controller.php?action=bundle_popup_rescan&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value+'&cbo_emb_company='+document.getElementById('cbo_emb_company').value,'Search Bundle For Rescan')"  id="txt_bundle_rescan" class="text_boxes" style="width:212px" />
                                       </td>
                                    </tr>
                                </table>
                            </fieldset>
                    	</td>
                    </tr>
                </table>
                <div id="bundle_list_view" style="display:none">

                	<table cellpadding="0" width="1180" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <th width="30">SL</th>
                            <th width="90">Bundle No</th>
                            <th width="50">Year</th>
                            <th width="60">Job No</th>
                            <th width="65">Buyer</th>
                            <th width="90">Order No</th>
                            <th width="120">Gmts. Item</th>
                            <th width="100">Country</th>
                            <th width="80">Color</th>
                            <th width="70">Size</th>
                            <th width="80">Output Qty</th>
                            <th width="50">Reject</th>
                            <th width="50">Alter</th>
                            <th width="50">Spot</th>
                            <th width="50">Replace</th>
                            <th width="50">QC Qty</th>
                            <th></th>
                        </thead>
              		</table>
                    <div style="width:1180px; max-height:250px; overflow-y:scroll" align="left">    
                    	<table cellpadding="0" width="1160" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody>
                            </tbody>
                        </table>
                	</div>
                	<table cellpadding="0" width="1180" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <tfoot>
                            <th width="30"></th>
                            <th width="90"></th>
                            <th width="50"></th>
                            <th width="60"></th>
                            <th width="65"></th>
                            <th width="90"></th>
                            <th width="120"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="70"></th>
                            <th width="80"></th>
                            <th width="50"></th>
                            <th width="50"></th>
                            <th width="50"></th>
                            <th width="50">Total</th>
                            <th width="50" id="total_bndl_qty"></th>
                            <th></th>
                        </tfoot>
              		</table>
                </div>
               	<table cellpadding="0" cellspacing="1" width="100%">
               		<tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_sewing_bundle_output_entry", 0,1 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','pageReset();')",1); 
                            ?>
                            <input id="Print2" class="formbutton" type="button" style="width:80px;" onClick="fnc_sewing_bundle_output_entry(5)" name="print" value="Print 2">

							<input id="Print2" class="formbutton" type="button" style="width:80px;" onClick="fnc_sewing_bundle_output_entry(6)" name="print" value="Print 3">

							<input id="Print2" class="formbutton" type="button" style="width:80px;" onClick="fnc_sewing_bundle_output_entry(7)" name="print" value="Print 4">

							<input id="Print2" class="formbutton" type="button" style="width:80px;" onClick="fnc_sewing_bundle_output_entry(8)" name="print" value="Print 5">

                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" value="0" readonly >
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
               	</table>
               	<div style="width:900px; margin-top:5px;" id="printing_production_list_view" align="center"></div>
        	</form>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</td></tr></table>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>