<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Issue to Wash V2
				
Functionality	:	
JS Functions	:
Created by		:	Md. Rakib Hasan Mondal
Creation date 	: 	21-08-2023
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
		if (form_validation('cbo_company_name*cbo_buyer_name', 'Company Name*Buyer') == false) 
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			let theform=this.contentDoc.forms[0];
			let po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			let item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			// let po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			let country_id=this.contentDoc.getElementById("hidden_country_id").value;
			let company_id=this.contentDoc.getElementById("hidden_company_id").value;
			get_php_form_data(company_id,'load_variable_settings','requires/gmts_issue_to_wash_v2_controller');
			// alert(item_id);
			if (po_id!="")
			{
				freeze_window(5);
				// $("#txt_order_qty").val(po_qnty);
				$('#cbo_item_name').val(item_id); 
				$("#cbo_country_name").val(country_id);
				$("#cbo_company_name").val(company_id);  
				$('#cbo_embel_name').val(3);
				$('#cbo_embel_type').val(0);
				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/gmts_issue_to_wash_v2_controller" );
				
				load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', $('#cbo_embel_name').val()+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_embro_issue_type', 'embro_type_td');
				
				get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, 'color_and_size_level', 'requires/gmts_issue_to_wash_v2_controller' ); 

				let variableSettings=$('#sewing_production_variable').val();
				let styleOrOrderWisw=$('#styleOrOrderWisw').val();

				if(variableSettings==1)
					$("#txt_issue_qty").removeAttr("readonly");
				else
					$('#txt_issue_qty').attr('readonly','readonly');
				
				show_list_view(po_id,'show_country_listview','list_view_country','requires/gmts_issue_to_wash_v2_controller','');
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0); 
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled");
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
		$("#txt_issue_qty").val(totalVal);
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
		$("#txt_issue_qty").val( $("#total_color").val() );
	}
	function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
	{
		freeze_window(5); 
		// childFormReset();//child from reset
		$("#cbo_item_name").val(item_id);
		$("#txt_order_qty").val(po_qnty);
		$("#cbo_country_name").val(country_id);
	
		$('#cbo_embel_name').val(3);
		$('#cbo_embel_type').val(0);
	
		get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/gmts_issue_to_wash_v2_controller" );
	
		let variableSettings=$('#sewing_production_variable').val();
		let styleOrOrderWisw=$('#styleOrOrderWisw').val();
	
		if(variableSettings==1) $("#txt_issue_qty").removeAttr("readonly");
		else $('#txt_issue_qty').attr('readonly','readonly');
		
		if(variableSettings!=1)
		{
			get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/gmts_issue_to_wash_v2_controller" );
		}
	
		set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
		release_freezing();
	}
	function fnc_issue_print_embroidery_entry(operation)
	{
		let source=$("#cbo_source").val();
			if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+$("#sewing_production_variable").val(), "wash_issue_print", "requires/gmts_issue_to_wash_v2_controller" ) 
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][645]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][645]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][645]);?>')==false)
				{
					
					return;
				}
			}

			if ( form_validation('cbo_company_name*cbo_location_name*txt_issue_date*cbo_source*cbo_emb_company*txt_order_no*txt_issue_qty*cbo_embel_type','Company Name*PO Location*Issue Date*Source*Wash Company*Order No*Issue Quantity*Embel. Type')==false )
			{
				return;
			}
			else
			{
				/* if(source==1)
				{
					if ( form_validation('cbo_wash_location','Wash Location')==false )
					{
						return;
					}
				}
				 */
				let current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_issue_date').val(), current_date)==false)
				{
					alert("Embel Issue Date Can not Be Greater Than Current Date");
					return;
				}
				let sewing_production_variable = $("#sewing_production_variable").val();
				let colorList = ($('#hidden_colorSizeID').val()).split(",");
	
				let i=0; let colorIDvalue='';
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
				
				let data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('txt_system_no*txt_system_id*cbo_company_name*garments_nature*cbo_location_name*txt_issue_date*cbo_source*cbo_emb_company*cbo_wash_location*txt_remark*cbo_sending_location*cbo_floor*hidden_po_break_down_id*txt_issue_qty*cbo_embel_type*cbo_country_name*cbo_item_name*cbo_buyer_name*txt_style_no*txt_job_no*txt_ir*txt_country_ship_date*txt_pack_type*txt_cut_qty*txt_sewing_qty*txt_cum_issue_qty*sewing_production_variable*hidden_colorSizeID*yet_to_issue*txt_mst_id',"../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","requires/gmts_issue_to_wash_v2_controller.php",true);
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
			let delevary_mst_id = reponse[2];
			show_msg(trim(reponse[0]));
			show_list_view(delevary_mst_id,'show_dtls_listview_from_sys_popup','printing_production_list_view','requires/gmts_issue_to_wash_v2_controller','');
			setFilterGrid("tbl_search",-1);
			reset_form('','','txt_issue_qty');
			set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/gmts_issue_to_wash_v2_controller" );
			 
			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/gmts_issue_to_wash_v2_controller" );
			}
			else
			{
				$("#txt_issue_qty").removeAttr("readonly"); 
			}
			if(reponse[0]==1 || reponse[0]==2) set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
			release_freezing();
			if(reponse[0]==0)
			{ 
				document.getElementById('txt_system_no').value = reponse[3];
				document.getElementById('txt_system_id').value = reponse[2];
			}
			if(reponse[0]==0 || reponse[0]==1 )
			{ 
				$("#txt_issue_date").attr('disabled','disabled');

				//disabled For avoid to mix master part info 
				$("#cbo_source").attr('disabled','disabled');
				$("#cbo_emb_company").attr('disabled','disabled');
				$("#cbo_wash_location").attr('disabled','disabled');
				$("#cbo_sending_location").attr('disabled','disabled');
				$("#cbo_floor").attr('disabled','disabled');
			}
			if(reponse[4]==2)//Delete Operation
			{ 
				if($('#cbo_company_name').attr('disabled'))
				{
					$("#cbo_company_name").removeAttr("disabled"); 
				}
				reset_form('washoutput_1','list_view_country','','txt_issue_date,<?=$date?>','childFormReset()')
			}
			release_freezing();
		}
	}
	function fnc_load_from_dtls(data)
	{
		//alert(data); return;
		get_php_form_data(data,'populate_issue_form_data','requires/gmts_issue_to_wash_v2_controller');
		//disabled For avoid to mix master part info 
		$("#cbo_source").attr('disabled','disabled');
		$("#cbo_emb_company").attr('disabled','disabled');
		$("#cbo_wash_location").attr('disabled','disabled');
		$("#cbo_sending_location").attr('disabled','disabled');
		$("#cbo_floor").attr('disabled','disabled');
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
	
	function issue_id_popup()
	{
		if (form_validation('cbo_company_name*cbo_source', 'Company Name*Source') == false) {
			return;
		}
		let company = $("#cbo_company_name").val();
		let company_id = $("#cbo_company_name").val();
		let source = $("#cbo_source").val();
		let buyer = $("#cbo_buyer_name").val();
		let title = "Delivery System Popup";
		let page_link = 'requires/gmts_issue_to_wash_v2_controller.php?action=system_number_popup&company_id=' + company_id+'&source='+source+'&buyer_id='+buyer;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=370px,center=1,resize=0,scrolling=0', '')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			
			var responseDataArr=this.contentDoc.getElementById("hidden_search_data").value.split('_');
			 
			document.getElementById('txt_system_id').value=responseDataArr[0];
			document.getElementById('txt_system_no').value=responseDataArr[1];
			
			get_php_form_data(responseDataArr[0], "populate_mst_form_data", "requires/gmts_issue_to_wash_v2_controller" );			
			
			show_list_view(responseDataArr[0],'show_dtls_listview_from_sys_popup','printing_production_list_view','requires/gmts_issue_to_wash_v2_controller','');
			setFilterGrid("tbl_search",-1);

			//disabled For avoid to mix master part info 
			$("#cbo_source").attr('disabled','disabled');
			$("#cbo_emb_company").attr('disabled','disabled');
			$("#cbo_wash_location").attr('disabled','disabled');
			$("#cbo_sending_location").attr('disabled','disabled');
			$("#cbo_floor").attr('disabled','disabled');
		
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
		$("#txt_issue_qty").val(totalVal);		

		
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
		$("#txt_issue_qty").val(totalVal); 
	}  
		
	function childFormReset()
	{
		reset_form('','','txt_issue_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cum_issue_qty*yet_to_issue*txt_cut_qty*txt_sewing_qty*txt_mst_id','','');
		$('#txt_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cum_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#yet_to_issue').attr('placeholder','');//placeholder value initilize
		$('#txt_sewing_qty').attr('placeholder','');//placeholder value initilize
		$("#breakdown_td_id").html('');
		$("#printing_production_list_view").html('');
		$("#cbo_source").val(1);
	
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;">
		<? echo load_freeze_divs("../", $permission);  ?>
		<div style="width:1010px; float:left" align="center">
			<form name="washoutput_1" id="washoutput_1" autocomplete="off">
				<fieldset style="width:1010px;">
					<legend>Gmts. Issue to Wash V2</legend>
					<fieldset>
						<table width="1000px" border="0">
							<tr>
								<td align="right" colspan="3">Issue ID</td>
								<td colspan="3">
									<input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:160px" onDblClick="issue_id_popup()" placeholder="Browse or Search" />
									<input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" style="width:160px" />
								</td>
							</tr>
							<tr>
								<td width="110" class="must_entry_caption">PO Company</td>
								<td width="210">
									<?
									$company_sql = "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
									echo create_drop_down("cbo_company_name", 170, $company_sql, "id,company_name", 1, "-- Select Company --", '', "load_drop_down_multiple( 'requires/gmts_issue_to_wash_v2_controller', this.value, 'load_drop_down_po_location', 'location_td' );load_drop_down_multiple( 'requires/gmts_issue_to_wash_v2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );", 0); ?>
									<input type="hidden" id="sewing_production_variable" />
									<input type="hidden" id="cbo_embel_name" value="3" />
									<input type="hidden" id="styleOrOrderWisw" />
									<input type="hidden" id="report_ids" name="report_ids"/>
									<input type="hidden" id="variable_is_controll" />
									<input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
								</td>
								<td width="110" class="must_entry_caption">PO Location</td>
								<td width="210" id="location_td">
									<?=create_drop_down("cbo_location_name", 170, $blank_array, "", 1, "--Select Location--", $selected, ""); ?>
								</td>
								<td width="110" class="must_entry_caption">Buyer</td>
								<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 170, "","", 1, "-- Select Buyer --", $selected, "",0,0 ); ?></td>  
							</tr>  
							<tr>
								<td class="must_entry_caption">Issue Date</td>
								<td width="210">
									<input name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:155px;" value="<? echo date('d-m-Y', time()); ?>">
								</td>
								<td class="must_entry_caption">Source</td>
								<td width="210">
									<? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", 1, "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_wash_company', 'emb_company_td' );", 0, '1,3' ); ?>
								</td>
								<td class="must_entry_caption" class="must_entry_caption">Wash Company</td>
								<td id="emb_company_td" width="210">
									<? echo create_drop_down( "cbo_emb_company", 170, $company_sql, "id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', this.value, 'load_drop_down_wash_location', 'wash_location_td' );" ); ?>
								</td> 
							</tr>
							<tr>
								<td id="locations">Wash Location</td>
								<td id="wash_location_td" >
									<? echo create_drop_down( "cbo_wash_location", 170, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?>
								</td> 
								<td>Sending Location</td>
								<td>
									<? 
									$sending_sql="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
									echo create_drop_down( "cbo_sending_location", 170, $sending_sql,"id,location_name", 1, "-- Select Sending Location --", $selected, "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );  
									?>
								</td>
								<td id="floors">Floor</td>
								<td id="floor_td" width="210">
									<? echo create_drop_down( "cbo_floor", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?>
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
												<input name="txt_order_no" id="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/gmts_issue_to_wash_v2_controller.php?action=order_popup&company='+$('#cbo_company_name').val()+'&garments_nature='+$('#garments_nature').val()+'&hidden_variable_cntl='+$('#hidden_variable_cntl').val()+'&hidden_preceding_process='+$('#hidden_preceding_process').val()+'&sewing_production_variable='+$('#sewing_production_variable').val()+'&buyer_id='+$('#cbo_buyer_name').val(),'Order Search')" class="text_boxes" style="width:150px " readonly />
												<input type="hidden" id="hidden_po_break_down_id" value="" />
											</td>
										</tr>
										<tr>
                                           <td class="must_entry_caption">Issue Qty</td>
                                           <td colspan="3">
                                               <input type="text" name="txt_issue_qty" id="txt_issue_qty"  class="text_boxes_numeric"  style="width:150px" readonly >
                                               <input type="hidden" id="hidden_break_down_html" value="" readonly disabled />
                                               <input type="hidden" id="hidden_colorSizeID" value="" readonly disabled />
                                           </td>
										</tr>
										<tr>
											<td class="must_entry_caption">Wash Type</td>
											<td id="embro_type_td"><? echo create_drop_down( "cbo_embel_type", 160, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
										</tr>
										<tr>
											<td>Country</td>
                        					<td><? echo create_drop_down("cbo_country_name",160,"select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 ); ?></td>
										</tr>
										<tr>
											<td>Item Name</td>
                        					<td>
												<? echo create_drop_down( "cbo_item_name", 160, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 ); ?>
											</td>

										</tr>
										<!-- <tr>
											<td>Acc.Order No</td>
											<td>
												<input name="txt_actual_po" id="txt_actual_po" type="text" class="text_boxes" style="width:150px;" onClick="actual_po_popup();" placeholder="Click" />
												<input type="hidden" name="hidden_actual_po" id="hidden_actual_po">
											</td>
										</tr> --> 
										<tr>
											<td>Style</td>
                        					<td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:150px" disabled  readonly></td>
										</tr>
										<tr>
											<td>Job No</td>
                        					<td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:150px" disabled  readonly></td>
										</tr>
										<tr>
											<td>IR No</td>
                        					<td><input name="txt_ir" id="txt_ir" class="text_boxes" style="width:150px" disabled  readonly></td>
										</tr>
										<tr>
											<td>Country  Ship Date</td>
                        					<td><input name="txt_country_ship_date" id="txt_country_ship_date" class="text_boxes" style="width:150px" disabled  readonly></td>
										</tr>
										<tr>
											<td>Pack Type</td>
											<td>
											<? echo create_drop_down( "txt_pack_type", 160, $packing,"", 1, "-- Select --", $selected, "",1,0 ); ?></td> 
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
											<td width="110" id=" cum_issue_qty"> Cu. Issue Qty.</td>
											<td>
												<input name="txt_cum_issue_qty" id="txt_cum_issue_qty" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly />
											</td>
										</tr>
										<tr>
											<td width="110" id=" cum_issue qty"> Yet to Issue</td>
											<td>
												<input name="yet_to_issue" id="yet_to_issue" class="text_boxes_numeric" type="text" style="width:100px" disabled readonly />
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
									echo load_submit_buttons($permission, "fnc_issue_print_embroidery_entry", 0, 1, "reset_form('washoutput_1','list_view_country','','txt_issue_date,".$date."','childFormReset()')", 1);
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