<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create SubCon Garments Receive from Wash
				
Functionality	:	
JS Functions	:
Created by		:	Md. Rakib Hasan Mondal
Creation date 	: 	12-09-2023
Updated  		: 	
Update date 	: 	
Purpose			:
QC Performed BY	:		
QC Date			:	
Comments		: 
Entry form 		: 
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
$u_id = $_SESSION['logic_erp']['user_id'];
$level = return_field_value("user_level", "user_passwd", "id='$u_id' and valid=1 ", "user_level");
$date = date('d-m-Y');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Garments issue to wash v2 Info", "../", 1, 1, $unicode, '', '');

function arrayExclude($array,Array $excludeKeys){
    foreach($array as $key => $value){
        if(!in_array($key, $excludeKeys)){
            $return[$key] = $value;
        }
    }
    return $return;
}
?>

<script>
	let tableFilters = {}
	let permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	function openmypage(page_link, title) 
	{ 
		
		if(form_validation('cbo_company_name*cbo_party_name', 'Company Name*Buyer') == false) 
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			let theform=this.contentDoc.forms[0];
			let po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			let item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			let company_id = $('#cbo_company_name').val();
			get_php_form_data(company_id,'load_variable_settings','requires/subcon_gmts_rcve_from_wash_controller');
			get_php_form_data(company_id,'load_variable_settings_reject','requires/subcon_gmts_rcve_from_wash_controller');
			if (po_id)
			{
				freeze_window(5); 
				$('#cbo_item_name').val(item_id); 
				$("#cbo_company_name").val(company_id);  
				$('#cbo_embel_name').val(3);
				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val(), "populate_data_from_search_popup", "requires/subcon_gmts_rcve_from_wash_controller" );

				get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+$('#embro_production_variable').val(), 'color_and_size_level', 'requires/subcon_gmts_rcve_from_wash_controller' );

				let variableSettings=$('#sewing_production_variable').val();
				var variableSettingsReject=$('#embro_production_variable').val();
				let styleOrOrderWisw=$('#styleOrOrderWisw').val();

				if(variableSettings==1)
					$("#txt_rcve_qty").removeAttr("readonly");
				else
					$('#txt_rcve_qty').attr('readonly','readonly');

				if(variableSettingsReject!=1)
					$("#txt_reject_qty").attr("readonly");
				else
					$("#txt_reject_qty").removeAttr("readonly");	
				
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0); 
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled");
			$("#cbo_party_name").attr("disabled","disabled");
		}
		
	}
	function fn_total(tableName,index) // for color and size level
	{
		let filed_value = $("#colSize_"+tableName+index).val();
		let placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
		let txt_user_lebel=$('#txt_user_lebel').val();
		let variable_is_controll=$('#variable_is_controll').val();
		if(filed_value*1 > placeholder_value*1)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by"+(placeholder_value-filed_value));
				$("#colSize_"+tableName+index).val('');
			}
			else
			{
				if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
					void(0);
				else
				{
					$("#colSize_"+tableName+index).val('');
				}
			}
		}
	
		let totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		let totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_rcve_qty").val(totalVal);
	}

	function fn_colorlevel_total(index) //for color level
	{
		let filed_value = $("#colSize_"+index).val();
		let placeholder_value = $("#colSize_"+index).attr('placeholder');
		let txt_user_lebel=$('#txt_user_lebel').val();
		let variable_is_controll=$('#variable_is_controll').val();
		if(filed_value*1 > placeholder_value*1)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded by"+(placeholder_value-filed_value));
				$("#colSize_"+index).val('');
			}
			else
			{
				if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
					void(0);
				else
					$("#colSize_"+index).val('');
			}
		}
	
		let totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_rcve_qty").val( $("#total_color").val() );
	}
	function fn_total_rej(tableName,index) // for color and size level Reject
	{
		var filed_value = $("#colSizeRej_"+tableName+index).val();
		var colsizes= $("#colSize_"+tableName+index).val();
	    if(colsizes=="" && filed_value !="")
	    {
	    	// this if condition add for when size null but reject qnty given scenery
	    	$("#colSize_"+tableName+index).val(0);
	    }
		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);

		var totalValRej = 0;
		$("input[name=colorSizeRej]").each(function(index, element) {
			totalValRej += ( $(this).val() )*1;
		});
		$("#txt_reject_qty").val(totalValRej);
	}

	function fn_colorRej_total(index) //for color level Reject
	{
		var filed_value = $("#colSizeRej_"+index).val();
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
		$("#txt_reject_qty").val( $("#total_color_rej").val() );
	}
	function fnc_issue_print_embroidery_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+$("#sewing_production_variable").val(), "wash_recv_print", "requires/subcon_gmts_rcve_from_wash_controller" ) 
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{

			if ( form_validation('cbo_company_name*cbo_location_name*txt_rcve_date*cbo_emb_company*cbo_wash_location*txt_order_no*txt_rcve_qty','Company Name*PO Location*Issue Date*Wash Company*Wash Location*Order No*Issue Quantity')==false )
			{
				return;
			}
			else
			{
				let current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_rcve_date').val(), current_date)==false)
				{
					alert("Embel Issue Date Can not Be Greater Than Current Date");
					return;
				}
				let sewing_production_variable = $("#sewing_production_variable").val();
				let variableSettingsReject=$('#embro_production_variable').val();
				let colorList = ($('#hidden_colorSizeID').val()).split(",");
	
				let i=0;let k=0; let colorIDvalue='';let colorIDvalueRej='';
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

				if(variableSettingsReject==2)//color level Reject
				{
					$("input[name=txtColSizeRej]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(k==0)
							{
								colorIDvalueRej = colorList[k]+"*"+$(this).val();
							}
							else
							{
								colorIDvalueRej += "**"+colorList[k]+"*"+$(this).val();
							}
						}
						k++;
					});
					//alert (colorIDvalueRej);return;
				}
				else if(variableSettingsReject==3)//color and size level Reject
				{
					$("input[name=colorSizeRej]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(k==0)
							{
								colorIDvalueRej = colorList[k]+"*"+$(this).val();
							}
							else
							{
								colorIDvalueRej += "***"+colorList[k]+"*"+$(this).val();
							}
						}
						k++;
					});
				}
				
				// alert (colorIDvalueRej);return;
				let data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('txt_system_no*txt_system_id*cbo_company_name*garments_nature*cbo_location_name*txt_rcve_date*cbo_emb_company*cbo_wash_location*txt_remark*hidden_po_break_down_id*txt_rcve_qty*cbo_item_name*cbo_party_name*txt_style_no*txt_job_no*txt_delivery_date*txt_cut_qty*txt_sewing_qty*txt_cum_rcve_qty*sewing_production_variable*hidden_colorSizeID*txt_yet_to_rcve*txt_mst_id*txt_reject_qty',"../");
				freeze_window(operation);
				http.open("POST","requires/subcon_gmts_rcve_from_wash_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
			}
		}
	}
	
	function fnc_issue_print_embroidery_Reply_info()
	{
		if(http.readyState == 4)
		{
			// alert(http.responseText);
			let variableSettings=$('#sewing_production_variable').val();
			var variableSettingsReject=$('#embro_production_variable').val();
			let styleOrOrderWisw=$('#styleOrOrderWisw').val();
			let item_id=$('#cbo_item_name').val();
			let country_id = $("#cbo_country_name").val();
	
			let reponse=http.responseText.split('**');
			if(reponse[0]==15)
			{
				 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',8000);
			}

			if(reponse[0]==99)
			{
				let okQty = reponse[1]*1;
				let rejQty = reponse[2]*1;
				let rcvQty = okQty + rejQty;
				alert('Receive found! So, delete restricted.(Receive Qty='+rcvQty+', OK Qty='+okQty+', Rej Qty='+rejQty+')');
				release_freezing();
				return;
			}
			else if(reponse[0]==786)
			{
				alert("Projected PO is not allowed to production. Please check variable settings.");
				release_freezing();
				return false;
			}
			let po_id = reponse[1];
			let system_mst_id = reponse[2];
			show_msg(trim(reponse[0]));
			show_list_view(system_mst_id,'show_dtls_listview_from_sys_popup','printing_production_list_view','requires/subcon_gmts_rcve_from_wash_controller','');
			setFilterGrid("tbl_search",-1);
			reset_form('','','txt_rcve_qty*txt_reject_qty');
			set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val(), "populate_data_from_search_popup", "requires/subcon_gmts_rcve_from_wash_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val(), "color_and_size_level", "requires/subcon_gmts_rcve_from_wash_controller" );
			}
			else
			{
				$("#txt_rcve_qty").removeAttr("readonly"); 
			}
			if(reponse[0]==1 || reponse[0]==2) set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
			if(reponse[0]==0 || reponse[0]==1)
			{
				$("#cbo_company_name").attr("disabled","disabled"); 
				$("#cbo_location_name").attr("disabled","disabled");
			}
			if(reponse[0]==0)
			{ 
				document.getElementById('txt_system_no').value = reponse[3];
				document.getElementById('txt_system_id').value = reponse[2];
			}
			if(reponse[4]==2)//Delete Operation
			{ 
				if($('#cbo_company_name').attr('disabled'))
				{
					$("#cbo_company_name").removeAttr("disabled"); 
				}
				reset_form('washoutput_1','','','txt_rcve_date,<?=$date?>','childFormReset()')
			}else{
				
			}
			release_freezing();
		}
	}
	function fnc_load_from_dtls(data)
	{
		//alert(data); return;
		get_php_form_data(data,'populate_receive_form_data','requires/subcon_gmts_rcve_from_wash_controller');
	}
	function location_select() 
	{
		if ($('#cbo_location_name option').length == 2) {
			if ($('#cbo_location_name option:first').val() == 0) {
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		} else if ($('#cbo_location_name option').length == 1) {
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}
	} 
	
	function reve_id_popup()
	{
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		let company = $("#cbo_company_name").val();
		let company_id = $("#cbo_company_name").val();
		let party_name = $("#cbo_party_name").val();
		let title = "Delivery System Popup";
		let page_link = 'requires/subcon_gmts_rcve_from_wash_controller.php?action=system_number_popup&company_id=' + company_id+'&party_name='+ party_name;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			
			var responseDataArr=this.contentDoc.getElementById("hidden_search_data").value.split('_');

			document.getElementById('txt_system_id').value=responseDataArr[0];
			document.getElementById('txt_system_no').value=responseDataArr[1];
			
			get_php_form_data(responseDataArr[0], "populate_mst_form_data", "requires/subcon_gmts_rcve_from_wash_controller" );			
			
			show_list_view(responseDataArr[0],'show_dtls_listview_from_sys_popup','printing_production_list_view','requires/subcon_gmts_rcve_from_wash_controller','');
			
			$("#cbo_company_name").attr("disabled","disabled");
			$("#cbo_location_name").attr("disabled","disabled");
			$("#cbo_party_name").attr("disabled","disabled");
			
			setFilterGrid("tbl_search",-1);
			set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
			release_freezing();
			
		}

	}
	function active_placeholder_qty(color_id) //color Size level
	{
		$("#table_" + color_id).find("input[name=colorSize]").each(function(index, element) {
			if ($('#set_all_' + color_id).prop('checked') == true) {
				if ($(this).attr('placeholder') != '' && $(this).attr('placeholder') > 0) {
					$(this).val($(this).attr('placeholder')); 
				}
			} else {
				$(this).val('');
			}
		});

		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
		$("#txt_rcve_qty").val(totalVal);		

		
	} 
	function active_placeholder_qty_color(color_id) //color level
	{
		$("#table_color").find("input[name=txt_color]").each(function(index, element) {
			if ($('#set_all').prop('checked') == true) 
			{
				if ($(this).attr('placeholder') != '' && $(this).attr('placeholder') > 0) {
					$(this).val($(this).attr('placeholder'));
				}
			} 
			else 
			{
				$(this).val('');
			}
		});

		var totalVal = 0;
		$("input[name=txt_color]").each(function(index, element) {
			totalVal += ($(this).val()) * 1;
		});
		$("#total_color").val(totalVal);	
		$("#txt_rcve_qty").val(totalVal); 
	}  
		
	function childFormReset()
	{
		reset_form('','','txt_rcve_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cum_rcve_qty*txt_yet_to_rcve*txt_cut_qty*txt_sewing_qty*txt_mst_id','','');
		$('#txt_rcve_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cum_rcve_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_to_rcve').attr('placeholder','');//placeholder value initilize
		$('#txt_sewing_qty').attr('placeholder','');//placeholder value initilize
		$("#breakdown_td_id").html('');
		$("#printing_production_list_view").html('');
	
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;">
		<? echo load_freeze_divs("../", $permission);  ?>
		<div style="width:1010px; float:left" align="center">
			<form name="washoutput_1" id="washoutput_1" autocomplete="off">
				<fieldset style="width:1010px;">
					<legend>SubCon Gmts. Receive From Wash</legend>
					<fieldset>
						<table width="1000px" border="0">
							<tr>
								<td align="right" colspan="3">Received ID</td>
								<td colspan="3">
									<input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:160px" onDblClick="reve_id_popup()" placeholder="Browse or Search" />
									<input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" style="width:160px" />
								</td>
							</tr>
							<tr>
								<td width="110" class="must_entry_caption">Company</td>
								<td width="210">
									<?
									$company_sql = "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
									echo create_drop_down("cbo_company_name", 170, $company_sql, "id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/subcon_gmts_rcve_from_wash_controller', this.value, 'load_drop_down_po_location', 'location_td' );load_drop_down( 'requires/subcon_gmts_rcve_from_wash_controller', this.value, 'load_drop_down_party_name', 'party_td' );", 0); ?>
									<input type="hidden" id="sewing_production_variable" />
									<input type="hidden" id="cbo_embel_name" value="3" />
									<input type="hidden" id="styleOrOrderWisw" />
									<input type="hidden" id="report_ids" name="report_ids"/>
									<input type="hidden" id="variable_is_controll" />
									<input type="hidden" id="embro_production_variable" />
									<input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
								</td>
								<td width="110" class="must_entry_caption">Location</td>
								<td width="210" id="location_td">
									<?=create_drop_down("cbo_location_name", 170, $blank_array, "", 1, "--Select Location--", $selected, ""); ?>
								</td>
                                <td width="110" class="must_entry_caption">Issue Date</td>
								<td>
									<input name="txt_rcve_date" id="txt_rcve_date" class="datepicker" style="width:155px;" value="<? echo date('d-m-Y', time()); ?>">
								</td>
							</tr>  
							<tr>
								<td  class="must_entry_caption">Wash Company</td>
								<td id="emb_company_td" width="210">
									<? echo create_drop_down( "cbo_emb_company", 170, $company_sql, "id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/gmts_rcve_from_wash_v2_controller', this.value, 'load_drop_down_wash_location', 'wash_location_td' );" ); ?>
								</td>
								<td id="locations"  class="must_entry_caption">Wash Location</td>
								<td id="wash_location_td" >
									<? echo create_drop_down( "cbo_wash_location", 170, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?>
								</td> 
								<td>Buyer</td>
								<td id="party_td">
									<? echo create_drop_down("cbo_party_name", 160, "","", 1, "-- Select Party --",'', "",1);
									?>
								</td>	
							</tr>
							<tr>
								<td>Remarks</td>
								<td colspan="5" ><input style="width:484px" type="text" name="txt_remark" id="txt_remark" class="text_boxes"  title="450 Characters Only." /></td>

							</tr>
						</table>
					</fieldset>
					<br />
					<table cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td width="30%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td width="170" class="must_entry_caption">Order No</td>
											<td>
												<input name="txt_order_no" id="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/subcon_gmts_rcve_from_wash_controller.php?action=order_popup&company='+$('#cbo_company_name').val()+'&garments_nature='+$('#garments_nature').val()+'&hidden_variable_cntl='+$('#hidden_variable_cntl').val()+'&hidden_preceding_process='+$('#hidden_preceding_process').val()+'&sewing_production_variable='+$('#sewing_production_variable').val()+'&buyer_id='+$('#cbo_party_name').val(),'Order Search')" class="text_boxes" style="width:150px " readonly />
												<input type="hidden" id="hidden_po_break_down_id" value="" />
											</td>
										</tr>
										<tr>
                                           <td class="must_entry_caption">Receive Qty</td>
                                           <td colspan="3">
                                               <input type="text" name="txt_rcve_qty" id="txt_rcve_qty"  class="text_boxes_numeric"  style="width:150px" readonly >
                                               <input type="hidden" id="hidden_break_down_html" value="" readonly disabled />
                                               <input type="hidden" id="hidden_colorSizeID" value="" readonly disabled />
                                           </td>
										</tr>
										<tr>
                                           <td class="must_entry_caption">Reject Qty.</td>
                                           <td colspan="3">
                                               <input type="text" name="txt_reject_qty" id="txt_reject_qty"  class="text_boxes_numeric"  style="width:150px" readonly >
                                           </td>
										</tr>
										<tr>
											<td>Item Name</td>
                        					<td>
												<? echo create_drop_down( "cbo_item_name", 160, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Style</td>
                        					<td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:150px" disabled  readonly></td>
										</tr>
										<tr>
											<td>Job No</td>
                        					<td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:150px" disabled  readonly></td>
										</tr>
										<tr>
											<td>Delivery Date</td>
                        					<td><input name="txt_delivery_date" id="txt_delivery_date" class="text_boxes" style="width:150px" disabled  readonly></td>
										</tr>
									</table>
								</fieldset>
							</td>
							<td width="1%" valign="top"></td>
							<td width="28%" valign="top">
								<fieldset>
									<legend>Display</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td width="110" id="cut_qty"> Cutt. Qty</td>
											<td>
												<input name="txt_cut_qty" id="txt_cut_qty" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly />
											</td>
										</tr>
										<tr>
											<td width="110" id="sewing_qty"> Sewing Qty</td>
											<td>
												<input name="txt_sewing_qty" id="txt_sewing_qty" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly />
											</td>
										</tr>
										<tr>
											<td width="110" id="sewing_qty"> Issue Qty</td>
											<td>
												<input name="ttl_issue_qty" id="ttl_issue_qty" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly />
											</td>
										</tr>
										<tr>
											<td width="110" id="cum_rcve_qty"> Cu. Receive Qty.</td>
											<td>
												<input name="txt_cum_rcve_qty" id="txt_cum_rcve_qty" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly />
											</td>
										</tr>
										<tr>
											<td width="110" id="yet_to_rcve"> Yet to Receive</td>
											<td>
												<input name="txt_yet_to_rcve" id="txt_yet_to_rcve" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly />
											</td>
										</tr>
										
									</table>
								</fieldset>
							</td>
							<td width="41%" valign="top">
								<div style="max-height:330px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
								<div style="max-height:130px; font-size:24px; color:red; overflow-y:scroll" id="posted_msg_td_id" align="center"></div>
							</td>
						</tr>
					</table>
					<br />
					<table cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td align="center" colspan="6" valign="middle" class="button_container">
								<?
									echo load_submit_buttons($permission, "fnc_issue_print_embroidery_entry", 0, 1, "reset_form('washoutput_1','','','txt_rcve_date,".$date."','childFormReset()')", 1);
								?>
								<input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly value="0">
							</td>
						</tr>
					</table>
					 <div style="width:100%; margin-top:5px;" id="printing_production_list_view" align="center"></div>
					</div>
				</fieldset>
			</form>
		</div>
		<div id="list_view_country" style="width:390px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>