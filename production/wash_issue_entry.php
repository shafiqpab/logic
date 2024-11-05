<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Wash Issue Entry

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	16-06-2020
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
echo load_html_head_contents("Wash Issue Entry","../", 1, 1, $unicode,'','');

if ($db_type == 0) {
    $sending_location="select concat(b.id,'*',a.id) id,concat(b.location_name,':',a.company_name) location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
} else if ($db_type == 2 || $db_type == 1) {
    $sending_location="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
}
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	//### coping from other file..
	function active_placeholder_qty(color_id) {
		$("#table_" + color_id).find("input[name=colorSize]").each(function(index, element) {
			if ($('#set_all_' + color_id).prop('checked') == true) {
				$(this).val($(this).attr('placeholder'));

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

	function dynamic_must_entry_caption(data)
	{
		if(data==1)
		{
			$('#locations').css('color','blue');
			$('#floors').css('color','blue');
		}
		else
		{
			$('#locations').css('color','black');
			$('#floors').css('color','black');
		}
	}

	function openmypage(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
			var company_id=this.contentDoc.getElementById("hidden_company_id").value;
			var country_ship_date=this.contentDoc.getElementById("country_ship_date").value;
			get_php_form_data(company_id,'load_variable_settings','requires/wash_issue_entry_controller');
			print_button_setting(company_id);
			if (po_id!="")
			{
				freeze_window(5);
				$("#country_ship_date").val(country_ship_date);
				$("#txt_order_qty").val(po_qnty);
				$('#cbo_item_name').val(item_id);
				$("#cbo_country_name").val(country_id);
				$("#cbo_company_name").val(company_id);
				childFormReset();//child form initialize
				$('#cbo_embel_name').val(3);
				$('#cbo_embel_type').val(0);
				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_issue_entry_controller" );
				
				load_drop_down( 'requires/wash_issue_entry_controller', $('#cbo_embel_name').val()+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_embro_issue_type', 'embro_type_td');
				
				get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**'+$("#country_ship_date").val(), 'color_and_size_level', 'requires/wash_issue_entry_controller' ); 

				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();

				if(variableSettings==1)
					$("#txt_issue_qty").removeAttr("readonly");
				else
					$('#txt_issue_qty').attr('readonly','readonly');

				show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/wash_issue_entry_controller','');
				setFilterGrid("tbl_search",-1);
				show_list_view(po_id,'show_country_listview','list_view_country','requires/wash_issue_entry_controller','');
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
				load_drop_down( 'requires/wash_issue_entry_controller', po_id, 'load_drop_down_color_type', 'color_type_td');
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled");
		}
	}//end function

	function fnc_issue_print_embroidery_entry(operation)
	{
		var source=$("#cbo_source").val();
		if(operation==4)
		{
			// var report_title=$("div.form_caption").html();
			 //print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print", "requires/wash_issue_entry_controller" )
			// return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][415]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][415]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][415]);?>')==false)
				{
					
					return;
				}
			}

			if ( form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date*txt_issue_qty','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date*Issue Quantity')==false )
			{
				return;
			}
			else
			{
				if(source==1)
				{
					if ( form_validation('cbo_location*cbo_floor','Location*Floor')==false )
					{
						return;
					}
				}
	
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_issue_date').val(), current_date)==false)
				{
					alert("Embel Issue Date Can not Be Greater Than Current Date");
					return;
				}
				var sewing_production_variable = $("#sewing_production_variable").val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
	
				var i=0; var colorIDvalue='';
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
	
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_issue_qty*txt_challan*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*hidden_break_down_html*txt_mst_id*cbo_sending_location*txt_manual_cut_no*cbo_color_type*txt_remark_dtls*country_ship_date',"../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","requires/wash_issue_entry_controller.php",true);
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
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			var item_id=$('#cbo_item_name').val();
			var country_id = $("#cbo_country_name").val();
	
			var reponse=http.responseText.split('**');
			if(reponse[0]==15)
			{
				 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',8000);
			}

			if(reponse[0]==99)
			{
				var okQty = reponse[1]*1;
				var rejQty = reponse[2]*1;
				var rcvQty = okQty + rejQty;
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

			/*if(reponse[0]==0)//insert
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/wash_issue_entry_controller','');
				setFilterGrid("tbl_search",-1);
				reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark_dtls*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','','','txt_cutting_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_issue_entry_controller" );
	
				if(variableSettings!=1) {
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/wash_issue_entry_controller" );
				}
				else
				{
					$("#txt_issue_qty").removeAttr("readonly");
				}
				release_freezing();
			}
			else if(reponse[0]==1)//update
			{
				*/var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/wash_issue_entry_controller','');
				setFilterGrid("tbl_search",-1);
				reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','','','txt_cutting_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_issue_entry_controller" );
	
				if(variableSettings!=1) {
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+$("#country_ship_date").val(), "color_and_size_level", "requires/wash_issue_entry_controller" );
				}
				else
				{
					$("#txt_issue_qty").removeAttr("readonly");
				}
				if(reponse[0]==1 || reponse[0]==2) set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
				release_freezing();
			/*}
			else if(reponse[0]==2)//delete
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/wash_issue_entry_controller','');
				reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*txt_remark*hidden_break_down_html*hidden_colorSizeID*txt_mst_id','','','txt_cutting_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_issue_entry_controller" );
	
				if(variableSettings!=1) {
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/wash_issue_entry_controller" );
				}
				else
				{
					$("#txt_issue_qty").removeAttr("readonly");
				}
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
				release_freezing();
			}
			else */
			if(reponse[0]==35)
			{
				$("#txt_issue_qty").val("");
				show_msg('25');
				alert(reponse[1]);
				release_freezing();
				return;
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
		$('#printing_production_list_view').html('');//listview container
		$("#breakdown_td_id").html('');
	
	}

	function fn_total(tableName,index) // for color and size level
	{
		var filed_value = $("#colSize_"+tableName+index).val();
		var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();
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
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();
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
	
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_issue_qty").val( $("#total_color").val() );
	}

	function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty,country_date)
	{
		freeze_window(5);
		
		childFormReset();//child from reset
		$("#cbo_item_name").val(item_id);
		$("#txt_order_qty").val(po_qnty);
		$("#cbo_country_name").val(country_id);
	
		$('#cbo_embel_name').val(3);
		$('#cbo_embel_type').val(0);
	
		get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_issue_entry_controller" );
	
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	
		if(variableSettings==1) $("#txt_issue_qty").removeAttr("readonly");
		else $('#txt_issue_qty').attr('readonly','readonly');
		
		if(variableSettings!=1)
		{
			get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+country_date, "color_and_size_level", "requires/wash_issue_entry_controller" );
		}
	
		show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/wash_issue_entry_controller','');
		setFilterGrid("tbl_search",-1);
		set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
		release_freezing();
	}
	
	function fn_with_source_report()
	{
		var mst_id=$('#txt_mst_id').val();
		if(mst_id=="")
		{
			alert('Please Select from list View first '); return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+mst_id+'*'+report_title, "emblishment_issue_print", "requires/wash_issue_entry_controller" )
		return;
	}
	
	function fn_without_source_report()
	{
		 var report_title=$( "div.form_caption" ).html();
		 var mst_id=$('#txt_mst_id').val();
		 if(mst_id=="")
		 {
			alert('Please Select from list View first '); return;
		 }
	
		 print_report( $('#cbo_company_name').val()+'*'+mst_id+'*'+report_title, "emblishment_without_print", "requires/wash_issue_entry_controller" )
		 return;
	}
	
	function fn_with_source_report2() // Print 2
	{
		var master_ids = ""; var total_tr=$('#tbl_search tr').length;
		for(i=1; i<total_tr; i++)
		{
			try
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					master_id = $('#mstidall_'+i).val();
					if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
				}
			}
			catch(e)
			{
				//got error no operation
			}
		}
		//alert(master_ids);
		if(master_ids=="")
		{
			alert("Please Select At Least One Item");
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print2", "requires/wash_issue_entry_controller" )
		 return;
	}

	function fn_with_source_report3() // Print 3
	{
		var master_ids = ""; var total_tr=$('#tbl_search tr').length;
		for(i=1; i<total_tr; i++)
		{
			try
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					master_id = $('#mstidall_'+i).val();
					if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
				}
			}
			catch(e)
			{
				//got error no operation
			}
		}
		//alert(master_ids);
		if(master_ids=="")
		{
			alert("Please Select At Least One Item");
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print3", "requires/wash_issue_entry_controller" )
		return;
	}

	//for print button
	function print_button_setting(data)
	{
		$('#data_panel').html('');
		get_php_form_data(data,'print_button_variable_setting','requires/wash_issue_entry_controller');
	}
	
	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==47)
			{
				$('#data_panel').append( '<input type="button"  id="print" class="formbutton" style="width:90px;" value="Print"  name="print"  onClick="fn_with_source_report()" />&nbsp;&nbsp;&nbsp;' );
			}
			else if(report_id[k]==48)
			{
				$('#data_panel').append( '<input type="button"  id="print2" class="formbutton" style="width:90px;" value="Without Source"  name="print2"  onClick="fn_without_source_report()" />&nbsp;&nbsp;&nbsp;' );
			}
			else if(report_id[k]==66)
			{
				$('#data_panel').append( '<input type="button"  id="print02" class="formbutton" style="width:90px;" value="Print 2"  name="print02"  onClick="fn_with_source_report2()" />&nbsp;&nbsp;&nbsp;' );
			}
		}
	}

	function fnc_checkbox_check(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var emblname=$('#emblname_'+rowNo).val();
		var embltype=$('#embltype_'+rowNo).val();
		var mst_source= $('#productionsource_'+rowNo).val();
	
		var serving_company= $('#serving_company_'+rowNo).val();
		var location= $('#location_'+rowNo).val();
	
		if(isChecked==true)
		{
			var tot_row=$('#tbl_search tr').length-1;
			for(var i=1; i<=tot_row; i++)
			{
				if(i!=rowNo)
				{
					try
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							var emblnameCurrent=$('#emblname_'+i).val();
							var embltypeCurrent=$('#embltype_'+i).val();
							var productionsourceCurrent=$('#productionsource_'+i).val();
	
							var serving_companyCurrent= $('#serving_company_'+i).val();
							var locationCurrent= $('#location_'+i).val();
	
							//alert(emblname+"_"+emblnameCurrent+"**"+mst_source+"_"+productionsourceCurrent+"**"+serving_company+"_"+serving_companyCurrent+"**"+location+"_"+locationCurrent+"**"+embltype+"_"+embltypeCurrent);
	
							if((emblname!=emblnameCurrent) || (mst_source!=productionsourceCurrent) || (serving_company!=serving_companyCurrent) || (location!=locationCurrent) || (embltype!=embltypeCurrent))
							{
								alert("Please Select Same Emblname, Source, Embel. Company, Location");
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
	
							// if((emblname!=emblnameCurrent) && (mst_source!=productionsourceCurrent) && (serving_company!=serving_companyCurrent) && (location!=locationCurrent) )
							// {
							// 	alert("Please Select Same Emblname, Source, Embel. Company, Location");
							// 	$('#tbl_'+rowNo).attr('checked',false);
							// 	return;
							// }
						}
					}
					catch(e)
					{
						//got error no operation
					}
				}
			}
		}
	}

	function fnc_load_from_dtls(data)
	{
		//alert(data); return;
		get_php_form_data(data,'populate_issue_form_data','requires/wash_issue_entry_controller');
	}

	function fn_chk_next_process_qty(tableName,index,sizeId) // for color and size level
	{
		// alert('ok');return;
		var data="action=chk_next_process_qty&colorId="+tableName+"&sizeId="+sizeId+get_submitted_data_string('cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_item_name',"../");
		//alert(data); return;
		var filed_value = $("#colSize_"+tableName+index).val()*1;
		var prev_value = $("#colSizeUpQty_"+tableName+index).val()*1;
		$.ajax({
			url: 'requires/wash_issue_entry_controller.php',
			type: 'POST',
			data: data,
			success: function(response)
			{
				var resData = trim(response).split("****");
				var rcvQty = resData[0];
				var issueQty = resData[1];
				// if((prev_value+(filed_value-rcvQty))*1 < rcvQty*1)
				if(filed_value*1 < rcvQty*1)
				{	
					// alert(prev_value+'+'+filed_value+'-'+rcvQty+'<'+rcvQty);
					alert('Sorry! Issue qnty will not less than receive qnty');			
					$("#colSize_"+tableName+index).val(prev_value);		 		
				}
			}
		});
	}
		

	function show_cost_details()
	{
		var system_id=$("#hidden_po_break_down_id").val();
		if(system_id=="")
		{
			alert('Order No Required!');
			return;
		}

		var page_link='requires/wash_issue_entry_controller.php?action=show_cost_details&sys_id='+system_id;
		var title='Cost Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=330px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
 		<fieldset style="width:930px;">
        <legend>Gmts. Issue to Wash</legend>
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
            <fieldset>
                <table width="100%">
                    <tr>
						<td width="110" class="must_entry_caption">Order No</td>
						<td width="200"><input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/wash_issue_entry_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:160px " readonly />
							<input type="hidden" id="hidden_po_break_down_id" value="" />
							<input type="hidden" id="country_ship_date">
						</td>
                        <td width="110" class="must_entry_caption">Company</td>
                        <td width="200"><? echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "",1 );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />
                            <input type="hidden" id="cbo_embel_name" value="3" />
                            <input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="report_ids" name="report_ids"/>
                            <input type="hidden" id="variable_is_controll" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
                        </td>
                        <td width="110">Country</td>
                        <td><? echo create_drop_down("cbo_country_name",170,"select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 ); ?></td>
                    </tr>
                    <tr>
                        <td>Buyer</td>
                        <td><? echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>
                        <td>Style</td>
                        <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:160px" disabled  readonly></td>
                        <td>Gmts. Item</td>
                        <td><? echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 ); ?></td>
                    </tr>
                    <tr>
                        <td>Order Qty</td>
                        <td><input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:160px" disabled readonly></td>
                        <td class="must_entry_caption">Wash Type</td>
                        <td id="embro_type_td"><? echo create_drop_down( "cbo_embel_type", 170, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                        <td class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/wash_issue_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' ); dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>
                    </tr>
                    <tr>
                          <td class="must_entry_caption">Wash Company</td>
                          <td id="emb_company_td"><? echo create_drop_down( "cbo_emb_company", 170, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                          <td id="locations">Location</td>
                          <td id="location_td"><? echo create_drop_down( "cbo_location", 170, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                          <td id="floors">Floor</td>
                          <td id="floor_td"><? echo create_drop_down( "cbo_floor", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                         <td>Sending Location</td>
                         <td><? echo create_drop_down( "cbo_sending_location", 170, $sending_location,"id,location_name", 1, "-- Select Sending Location --", $selected, "" ); ?></td>
	  					 <td>Remarks</td>
		                 <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:160px" title="450 Characters Only." /></td>
						 
						 <td></td>						  
                        	<td>
								<input type="button" id="wip_valuation_for_accounts_button" name="" style="width:90px;display:none;" class="formbutton" value="Cost Details" onClick="show_cost_details();">
							</td>
                    </tr>
                </table>
                </fieldset> <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                          <td width="35%" valign="top">
                               <fieldset>
                                  <legend>New Entry</legend>
                                   <table cellpadding="0" cellspacing="2" width="100%">
                                      <tr>
                                           <td width="80" class="must_entry_caption">Issue Date</td>
                                           <td colspan="3" width="110"><input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:100px;"  /></td>
                                      </tr>
                                      <tr>
                                            <td>Color Type</td>
                                            <td id="color_type_td" colspan="2"><? echo create_drop_down( "cbo_color_type", 110, $blank_array,"", 1, "Select Type", $selected, "",1,0 ); ?></td>
                                      </tr>
                                      <tr>
                                           <td class="must_entry_caption">Issue Qty</td>
                                           <td colspan="3">
                                               <input type="text" name="txt_issue_qty" id="txt_issue_qty"  class="text_boxes_numeric"  style="width:100px" readonly >
                                               <input type="hidden" id="hidden_break_down_html" value="" readonly disabled />
                                               <input type="hidden" id="hidden_colorSizeID" value="" readonly disabled />
                                           </td>
                                      </tr>
                                      <tr>
                                           <td>Challan No</td>
                                           <td><input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" /></td>
                                           <td>Iss. ID</td>
                                           <td><input type="text" name="txt_iss_id" id="txt_iss_id" class="text_boxes" style="width:50px" readonly /></td>
                                     </tr>
                                     <tr>
                                        <td>Manual Cut No</td>
                                        <td><input type="text" name="txt_manual_cut_no" id="txt_manual_cut_no" class="text_boxes" style="width:100px" /></td>
                                     </tr>
                                     <tr>
                                           <td>Remarks</td>
                                           <td colspan="3"><input type="text" name="txt_remark_dtls" id="txt_remark_dtls" class="text_boxes" style="width:212px" title="450 Characters Only." /></td>
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
                                        <td width="100">Sewing Qty</td>
                                        <td width="90"><input type="text" name="txt_cutting_qty" id="txt_cutting_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/></td>
                                    </tr>
                                    <tr>
                                        <td>Cuml. Issue Qty</td>
                                        <td><input type="text" name="txt_cumul_issue_qty" id="txt_cumul_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/></td>
                                    </tr>
                                    <tr>
                                        <td>Yet to Issue</td>
                                        <td><input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:80px" disabled readonly/></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="40%" valign="top">
                            <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,0 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','childFormReset()')",1);
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                             <!--<input type="button" name="button" id="button" class="formbutton" style="width:90px" onClick="fn_with_source_report()"  value="Print" >
                             <input type="button" name="button" id="button" class="formbutton" style="width:90px" onClick="fn_without_source_report()"  value="Without Source" >-->
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                     <tr>
                    	<td colspan="10" align="center" id="data_panel"></td>
                    </tr>
                    <tr>
                    	<td colspan="10" align="center"><input id="print03" class="formbutton" style="width:90px;" value="Print 3" name="print03" onClick="fn_with_source_report3()" type="button">
                    	</td>
                    </tr>
               </table>
               <div style="width:900px; margin-top:5px;" id="printing_production_list_view" align="center"></div>
        </form>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:390px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//$('#cbo_embel_name').val(3);
//$('#cbo_embel_name').trigger('change');
</script>
</html>
