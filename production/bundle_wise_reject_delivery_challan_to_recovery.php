<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create bundle wise Reject Delivery Challan to Recovery
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq 
Creation date 	: 	20-04-2022
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
		var title = 'Challan Selection Form';	
		var page_link = 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller.php?cbo_company_name='+cbo_company_name+'&cbo_source='+cbo_source+'&cbo_serving_company='+cbo_serving_company+'&action=challan_no_popup';
		
		if( form_validation('cbo_company_name*cbo_emb_company','Company Name*Sewing Company')==false )
		{
			return;
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
					reset_form('printembro_1','','','','txt_issue_date,<? echo date("d-m-Y"); ?>','cbo_company_name');
					get_php_form_data(mst_id, "populate_data_from_challan_popup", "requires/bundle_wise_reject_delivery_challan_to_recovery_controller" );
					var list_view = trim(return_global_ajax_value(mst_id, 'populate_list_view', '', 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller'));
					// alert(list_view);
					$("#bundle_list_view").html('');
					$("#bundle_list_view").html(list_view);
					
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
	
					get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/bundle_wise_reject_delivery_challan_to_recovery_controller" );
					
					var variableSettings=$('#sewing_production_variable').val();
					var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				
					if(variableSettings==1)
					{
						$("#txt_issue_qty").removeAttr("readonly");
					}
					else
					{
						$('#txt_issue_qty').attr('readonly','readonly');
						get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/bundle_wise_reject_delivery_challan_to_recovery_controller" ); 
					}
					
					show_list_view(po_id,'show_country_listview','list_view_country','requires/bundle_wise_reject_delivery_challan_to_recovery_controller','');	
					set_button_status(0, permission, 'fnc_reject_delivery_challan_to_recovery_entry',1,0);
					release_freezing();
				}
			}
		}//end else
	}//end function

	function generate_report_file(data,action,page)
	{
		window.open("requires/bundle_wise_reject_delivery_challan_to_recovery_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_reject_delivery_challan_to_recovery_entry(operation)
	{
		freeze_window(operation);
		var company_id=$('#cbo_company_name').val();
		var system_id=$('#update_id').val();
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, 'challan_print', 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller'); 
			release_freezing();
			 return;
		}
	
		if(operation==0 || operation==1 || operation==2)
		{
			
			var cbo_source=$('#cbo_source').val();
			if(cbo_source==1)
			{
				if ( form_validation('cbo_company_name*cbo_source*cbo_emb_company*txt_issue_date*cbo_location','Company Name*Source*Embel.Company*Issue Date*Location')==false )
				{
					release_freezing();
					return;
				}
			}
			else
			{
				if ( form_validation('cbo_company_name*cbo_source*cbo_emb_company*txt_issue_date','Company Name*Source*Embel.Company*Issue Date')==false )
				{
					release_freezing();
					return;
				}
			}

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Delivery Date Can not Be Greater Than Current Date");
				release_freezing();
				return;
			}
			
			var j=0; 
			var dataString='';
			$("#tbl_details").find('tbody tr').each(function()
			{
				var jobNo=$(this).find('input[name="jobNo[]"]').val();
				var buyerId=$(this).find('input[name="buyerId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var itemId=$(this).find('input[name="itemId[]"]').val();
				var countryId=$(this).find('input[name="countryId[]"]').val();
				var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
				var lineId=$(this).find('input[name="lineId[]"]').val();
				var prodDate=$(this).find('input[name="prodDate[]"]').val();
				var shiftId=$(this).find('input[name="shiftId[]"]').val();
				var floorId=$(this).find('input[name="floorId[]"]').val();
				var outChallan=$(this).find('input[name="outChallan[]"]').val();
				var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
				var bundleNo=$(this).find('input[name="bundleNo[]"]').val();
				var qty=$(this).find('input[name="qty[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var sizeId=$(this).find('input[name="sizeId[]"]').val();
				var styleRef=$(this).find('input[name="styleRef[]"]').val();
				var poNumber=$(this).find('input[name="poNumber[]"]').val();
				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				
				try 
				{
					j++;
					
					dataString+='&jobNo_' + j + '=' + jobNo + '&buyerId_' + j + '=' + buyerId +'&orderId_' + j + '=' + orderId + '&gmtsitemId_' + j + '=' + itemId + '&countryId_' + j + '=' + countryId  + '&lineId_' + j + '=' + lineId + '&colorSizeId_' + j + '=' + colorSizeId + '&qty_' + j + '=' + qty + '&prodDate_' + j + '=' + prodDate + '&shiftId_' + j + '=' + shiftId + '&floorId_' + j + '=' + floorId + '&outChallan_' + j + '=' + outChallan + '&bundleNo_' + j + '=' + bundleNo+ '&barcodeNo_' + j + '=' + barcodeNo+ '&colorId_' + j + '=' + colorId+ '&sizeId_' + j + '=' + sizeId+ '&styleRef_' + j + '=' + styleRef+ '&poNumber_' + j + '=' + poNumber+ '&dtlsId_' + j + '=' + dtlsId;
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

			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('garments_nature*cbo_company_name*cbo_source*cbo_emb_company*cbo_location*txt_issue_date*update_id*txt_challan_no*txt_remark',"../")+dataString;
			
			
			// alert (dataString);release_freezing();return;
			
			http.open("POST","requires/bundle_wise_reject_delivery_challan_to_recovery_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_reject_delivery_challan_to_recovery_entry_Reply_info;
		}
	}
  
	function fnc_reject_delivery_challan_to_recovery_entry_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			// alert(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				show_msg(response[0]);
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_challan_no').value = response[2];
				var list_view = trim(return_global_ajax_value(response[1], 'populate_list_view', '', 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller'));
				$("#tbl_list_view tbody").html(list_view);
				$("#tbl_list_view").show();
				$('#cbo_company_name').attr('disabled','disabled');
				set_button_status(1, permission, 'fnc_reject_delivery_challan_to_recovery_entry',1);
			}
			release_freezing();
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
		set_button_status(0, permission, 'fnc_reject_delivery_challan_to_recovery_entry',1,1);
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
			var company_name=$('#cbo_company_name').val(); 
						
			var bundleNo='';
			$("#tbl_details").find('tbody tr').each(function()
			{
				bundleNo+=$(this).find("td:eq(16)").text()+',';
			});
		
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo+'&company_name='+company_name, title, 'width=1250px,height=370px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var hidden_data=this.contentDoc.getElementById("hidden_data").value;
				// alert(hidden_data);return;
				var data=hidden_data.split("_");
				var html=''; 
				var num_row=$('#tbl_details tbody tr').length+1;
				for(var k=0; k<data.length; k++)
				{
					if(num_row%2==0) var bgcolor="#E9F3FF"; else var bgcolor="#FFFFFF";

					var row_data=data[k].split("**");
					// alert(row_data.join('**'));
					// alert(row_data[34]);					

					var html=html+'<tr bgcolor="'+bgcolor+'" id="tr_'+num_row+'"><td width="30" style="word-break:break-all;">'+num_row+'</td><td width="50" style="word-break:break-all;">'+row_data[1]+'</td><td width="50" style="word-break:break-all;">'+row_data[3]+'</td><td width="90" style="word-break:break-all;">'+row_data[4]+'</td><td width="100" style="word-break:break-all;">'+row_data[6]+'</td><td width="100" style="word-break:break-all;">'+row_data[8]+'</td><td width="100" style="word-break:break-all;">'+row_data[10]+'</td><td width="60" style="word-break:break-all;">'+row_data[11]+'</td><td width="40" style="word-break:break-all;" id="gsm'+num_row+'">'+row_data[12]+'</td><td width="80" id="dia'+num_row+'" style="word-break:break-all;">'+row_data[14]+'</td><td width="60" style="word-break:break-all;">'+row_data[16]+'</td><td align="right" title="rej qty" width="60" style="word-break:break-all;">'+row_data[18]+'</td><td width="80" align="left" style="word-break:break-all;">'+row_data[19]+'</td><td width="50" align="left" style="word-break:break-all;">'+row_data[21]+'</td><td width="60" align="left" style="word-break:break-all;">'+row_data[23]+'</td><td width="90" align="left" style="word-break:break-all;">'+row_data[24]+'</td><td width="90" align="left" style="word-break:break-all;">'+row_data[25]+'</td><td align="center" width=""><input type="hidden" value="'+row_data[3]+'" id="jobNo'+num_row+'" name="jobNo[]"/><input type="hidden" value="'+row_data[2]+'" id="buyerId'+num_row+'" name="buyerId[]"/><input type="hidden" value="'+row_data[26]+'" id="orderId'+num_row+'" name="orderId[]"/><input type="hidden" value="'+row_data[7]+'" id="itemId'+num_row+'" name="itemId[]"/><input type="hidden" value="'+row_data[9]+'" id="countryId'+num_row+'" name="countryId[]"/><input type="hidden" value="'+row_data[27]+'" id="colorSizeId'+num_row+'" name="colorSizeId[]"/><input type="hidden" value="'+row_data[22]+'" id="lineId'+num_row+'" name="lineId[]"/><input type="hidden" value="'+row_data[11]+'" id="prodDate'+num_row+'" name="prodDate[]"/><input type="hidden" value="'+row_data[13]+'" id="shiftId'+num_row+'" name="shiftId[]"/><input type="hidden" value="'+row_data[20]+'" id="floorId'+num_row+'" name="floorId[]"/><input type="hidden" value="'+row_data[23]+'" id="outChallan'+num_row+'" name="outChallan[]"/><input type="hidden" value="'+row_data[24]+'" id="barcodeNo'+num_row+'" name="barcodeNo[]"/><input type="hidden" value="'+row_data[25]+'" id="bundleNo'+num_row+'" name="bundleNo[]"/><input type="hidden" value="'+row_data[18]+'" id="qty'+num_row+'" name="qty[]"/><input type="hidden" value="'+row_data[15]+'" id="colorId'+num_row+'" name="colorId[]"/><input type="hidden" value="'+row_data[17]+'" id="sizeId'+num_row+'" name="sizeId[]"/><input type="hidden" value="'+row_data[4]+'" id="poNumber'+num_row+'" name="poNumber[]"/><input type="hidden" value="'+row_data[6]+'" id="styleRef'+num_row+'" name="styleRef[]"/><input type="hidden" id="dtlsId'+num_row+'" name="dtlsId[]"/><input type="button" id="decrease'+num_row+'" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow('+num_row+')" /></td></tr>';
					num_row++;		
				}
				
				$("#tbl_details tbody:last").append(html);	
				set_all_onclick();
			}
		}//end else
	}//end function

	function fnc_duplicate_bundle(bundle_no)
	{
		var challan_duplicate=return_ajax_request_value( bundle_no+"__"+$('#cbo_company_name').val()+"__"+$('#cbo_line_no').val(),"challan_duplicate_check", "requires/bundle_wise_reject_delivery_challan_to_recovery_controller");
		
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
				var page_link='requires/bundle_wise_reject_delivery_challan_to_recovery_controller.php?action=bundle_popup_line_select&data='+ex_challan_duplicate[2]+'&company_id='+$('#cbo_company_name').val();
				
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, '', 'width=590px,height=220px,center=1,resize=0,scrolling=0','')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_info").value;//po id
					$('#cbo_line_no').val(hidden_bundle_nos);
					 
					create_row(bundle_no,hidden_bundle_nos);
					var tot_row=$('#tbl_details tbody tr').length; 
				 
					if( (tot_row*1) <2)
						get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_reject_delivery_challan_to_recovery_controller');
				}
			}
			else
			{
				create_row(bundle_no);
				var tot_row=$('#tbl_details tbody tr').length; 
			 
				if( (tot_row*1) <2)
					get_php_form_data(bundle_no+"__"+ex_challan_duplicate[4]+"__"+$('#txt_issue_date').val(),'load_mst_data','requires/bundle_wise_reject_delivery_challan_to_recovery_controller');
			}
		}
		$('#txt_bundle_no').val('');
	}

	function fn_deleteRow( rid )
	{
		$("#tr_"+rid).remove();

		var total_qty = 0;
		$("#tbl_details").find('tbody tr').each(function()
		{
			total_qty+=$(this).find('input[name="qty[]"]').val()*1;
		});
		// $("#total_bndl_qty").text(total_qty);
		
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


	function change_mode(source_id)
	{
		if(source_id==1)
		{
			get_php_form_data($('#cbo_company_name').val(),'load_variable_settings','requires/bundle_wise_reject_delivery_challan_to_recovery_controller'); get_php_form_data($('#cbo_company_name').val(),'load_variable_settings_for_working_company','requires/bundle_wise_reject_delivery_challan_to_recovery_controller'); load_html();
				
		}
		else
		{
			get_php_form_data($('#cbo_company_name').val(),'load_variable_settings','requires/bundle_wise_reject_delivery_challan_to_recovery_controller'); get_php_form_data($('#cbo_emb_company').val(),'load_variable_settings_for_working_company','requires/bundle_wise_reject_delivery_challan_to_recovery_controller'); load_html();
		
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
	                        <td align="right" colspan="3"><b>System Challan No</b></td>
	                        <td colspan="3"> 
	                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:167px" onDblClick="openmypage_sysNo();" placeholder="Double click to search" />
	                          <input name="update_id" id="update_id" class="text_boxes" type="hidden" />
	                        </td>
	                    </tr>
	                    <tr>
	                        <td width="110" class="must_entry_caption">LC Company</td>
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
	                              	echo create_drop_down( "cbo_source", 180, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/bundle_wise_reject_delivery_challan_to_recovery_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );", 0, '1,3' );
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
	                         <td class="must_entry_caption">Challan Date</td>
	                         <td> 
	                         	<input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;"  />
	                         	<input type="hidden" name="txt_sewing_input_date" id="txt_sewing_input_date"  value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;"  />
	                         </td>
	                         <td class="must_entry_caption">Remarks</td>
	                         <td id="floor_td">
	                             <input name="txt_remark" id="txt_remark" class="text_boxes" type="text" style="width:167px"  placeholder="Remarks"/>
	                         </td>
	                    </tr>
	                  	<tr>
	                       	<td colspan="3" width="80" class="must_entry_caption" align="right"><b>Barcode No</b></td>
	                       	<td colspan="3" width="110" align="left"> 
	                           <input name="txt_bundle_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle('requires/bundle_wise_reject_delivery_challan_to_recovery_controller.php?action=bundle_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Bundle Search')" id="txt_bundle_no" class="text_boxes" style="width:167px" />
	                       	</td>
	                    </tr>
	                    
	                </table>
                </fieldset>
                <div id="bundle_list_view" style="margin-top: 10px;">

                	<table cellpadding="0" width="1230" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <th width="30">SL</th>
                            <th width="50">Buyer</th>
                            <th width="50">Job No</th>
                            <th width="90">Order No</th>
                            <th width="100">Style</th>
                            <th width="100">Gmts. Item</th>
                            <th width="100">Country</th>
                            <th width="60">Prod. Date</th>
                            <th width="40">Shift</th>
                            <th width="80">Color Type</th>
                            <th width="60">Size</th>
                            <th width="60">Reject Qty</th>
                            <th width="80">Floor</th>
                            <th width="50">Line</th>
                            <th width="60">Output Challan</th>
                            <th width="90">Barcode</th>
                            <th width="90">Bundle</th>
                            <th></th>
                        </thead>
              		</table>
                    <div style="width:1250px; max-height:250px; overflow-y:scroll" align="left">    
                    	<table cellpadding="0" width="1230" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody>
                            </tbody>
                        </table>
                	</div>
                	<table cellpadding="0" width="1230" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <tfoot>
                            <th width="30"></th>
                            <th width="50"></th>
                            <th width="50"></th>
                            <th width="90"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="60"></th>
                            <th width="40"></th>
                            <th width="80"></th>
                            <th width="60"></th>
                            <th width="60"></th>
                            <th width="80"></th>
                            <th width="50"></th>
                            <th width="60"></th>
                            <th width="90"></th>
                            <th width="90"></th>
                            <th></th>
                        </tfoot>
              		</table>
                </div>
               	<table cellpadding="0" cellspacing="1" width="100%">
               		<tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_reject_delivery_challan_to_recovery_entry", 0,1 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','pageReset();')",1); 
                            ?>
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
               	</table>
               	
        	</form>
        	<div style="margin-top: 10px;display: none;">
            	<table cellpadding="0" width="1230" cellspacing="0" border="1" class="rpt_table" rules="all">
                    <thead>
                        <th width="30">SL</th>
                        <th width="50">Buyer</th>
                        <th width="50">Job No</th>
                        <th width="90">Order No</th>
                        <th width="100">Style</th>
                        <th width="100">Gmts. Item</th>
                        <th width="100">Country</th>
                        <th width="60">Prod. Date</th>
                        <th width="40">Shift</th>
                        <th width="80">Color Type</th>
                        <th width="60">Size</th>
                        <th width="60">Reject Qty</th>
                        <th width="80">Floor</th>
                        <th width="50">Line</th>
                        <th width="60">Output Challan</th>
                        <th width="90">Barcode</th>
                        <th width="90">Bundle</th>
                    </thead>
          		</table>
                <div style="width:1250px; max-height:250px; overflow-y:scroll" align="left">    
                	<table cellpadding="0" width="1230" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view">      
                        <tbody>
                        </tbody>
                    </table>
            	</div>
            	<table cellpadding="0" width="1230" cellspacing="0" border="1" class="rpt_table" rules="all">
                    <tfoot>
                        <th width="30"></th>
                        <th width="50"></th>
                        <th width="50"></th>
                        <th width="90"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="60"></th>
                        <th width="40"></th>
                        <th width="80"></th>
                        <th width="60"></th>
                        <th width="60"></th>
                        <th width="80"></th>
                        <th width="50"></th>
                        <th width="60"></th>
                        <th width="90"></th>
                        <th width="90"></th>
                    </tfoot>
          		</table>
            </div>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</td></tr></table>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>