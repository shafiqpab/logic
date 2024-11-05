<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gmts.Receive From Wash

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	17-06-2020
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
echo load_html_head_contents("Gmts.Receive From Wash","../", 1, 1, $unicode);

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
		$("#txt_receive_qty").val(totalVal);
	}

	// order popup function here
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
//		if ( form_validation('cbo_company_name','Company Name')==false )
//		{
//			return;
//		}
//		else
//		{
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

				get_php_form_data(company_id,'load_variable_settings','requires/wash_receive_entry_controller');
				get_php_form_data(company_id,'load_variable_settings_reject','requires/wash_receive_entry_controller');

				if (po_id!="")
				{
					freeze_window(5);
					$("#txt_order_qty").val(po_qnty);
					$('#cbo_item_name').val(item_id);
					$("#cbo_country_name").val(country_id);
					$("#cbo_company_name").val(company_id);
					$("#country_ship_date").val(country_ship_date);
					childFormReset();//child form initialize
					$('#cbo_embel_name').val(3);
					$('#cbo_embel_type').val(0);
					get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_receive_entry_controller" );

					load_drop_down( 'requires/wash_receive_entry_controller', $('#cbo_embel_name').val()+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_emb_receive_type', 'emb_type_td' );

					get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val()+'**'+$('#embro_production_variable').val()+'**'+$('#country_ship_date').val(), 'color_and_size_level', 'requires/wash_receive_entry_controller' );

					var variableSettings=$('#sewing_production_variable').val();
					var variableSettingsReject=$('#embro_production_variable').val();
					var styleOrOrderWisw=$('#styleOrOrderWisw').val();
					/*if(variableSettings!=1){
						get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+variableSettingsReject, "color_and_size_level", "requires/wash_receive_entry_controller" );
					}
					else
					{
						$("#txt_receive_qty").removeAttr("readonly");
					}*/

					if(variableSettings==1)
						$("#txt_receive_qty").removeAttr("readonly");
					else
						$('#txt_receive_qty').attr('readonly','readonly');

					if(variableSettingsReject!=1)
						$("#txt_reject_qty").attr("readonly");
					else
						$("#txt_reject_qty").removeAttr("readonly");

					$('#cbo_embel_name').val(3);
					//$('#cbo_embel_name').trigger('change');

					show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/wash_receive_entry_controller','');
					// show_list_view(po_id,'show_cost_listview','printing_cost_list_view','requires/wash_receive_entry_controller','');
					setFilterGrid("tbl_search",-1);
					show_list_view(po_id,'show_country_listview','list_view_country','requires/wash_receive_entry_controller','');
					set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
					load_drop_down( 'requires/wash_receive_entry_controller', po_id, 'load_drop_down_color_type', 'color_type_td');

					release_freezing();
				}
				$("#cbo_company_name").attr("disabled","disabled");
			}
//		}//end else
	}//end function
    function openmypage_defectQty(type)
   {
	    var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var po_order_id = $('#hidden_po_break_down_id').val();
		var po_order_no = $('#txt_order_no').val();
		if (form_validation('cbo_company_name*cbo_source*txt_order_no','Company*Source*Order No')==false)
		{
			return;
		}
		else
	  	{
			if (form_validation('cbo_company_name','cbo_source','Company','Source')==false)
			{
				return;
			}
			if(txt_order_no=='')
	        {
	    	alert('Please Order No Browse First.');
		    return;
        	}
			else if(type==1)
	       	{
			var save_data=$('#save_dataReject').val();
			var all_defect_id=$('#allReject_defect_id').val();
			// var defect_qty=$('#txt_spot_qnty').val();
	     	}
			var page_link='requires/wash_receive_entry_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&po_order_id='+po_order_id+'&po_order_no='+po_order_no+'&action=defect_data&save_data='+save_data;
			var title='Reject Qty Info';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var save_string=this.contentDoc.getElementById("save_string").value;
			    var tot_defectQnty=this.contentDoc.getElementById("tot_defectQnty").value;
			    var all_defect_id=this.contentDoc.getElementById("all_defect_id").value;
			    var defect_type_id=this.contentDoc.getElementById("defect_type_id").value;
				 if(type==1) //Reject
			   {
				   $('#save_dataReject').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				   $('#allReject_defect_id').val(all_defect_id);
			       $('#defectReject_type_id').val(type);
		    	}

			}
		}

   }
	//embrodery receive save here
	function fnc_receive_print_embroidery_entry(operation)
	{
		var source=$("#cbo_source").val();
		if(operation==4)
		{ //embro_production_variable
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
			// alert($('#txt_mst_id_all').val());
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title+'*'+$("#sewing_production_variable").val(), "emblishment_receive_print", "requires/wash_receive_entry_controller" )
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][416]);?>'){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][416]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][416]);?>')==false)
				{

					return;
				}
			}

			if ( form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_receive_date*txt_receive_qty*txt_challan','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Receive Date*Receive Quantity*Challan No')==false )
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
				if(date_compare($('#txt_receive_date').val(), current_date)==false)
				{
					alert("Embel Receive Date Can not Be Greater Than Current Date");
					return;
				}

				freeze_window(operation);
				var sewing_production_variable = $("#sewing_production_variable").val();
				var variableSettingsReject=$('#embro_production_variable').val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");

				var i=0;  var k=0; var colorIDvalue=''; var colorIDvalueRej='';
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
				if(variableSettingsReject==2)//color level
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
				else if(variableSettingsReject==3)//color and size level
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
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*embro_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_receive_date*txt_receive_qty*txt_challan*txt_remark*txt_issue_qty*txt_reject_qty*txt_cumul_receive_qty*txt_yet_to_receive*hidden_break_down_html*txt_mst_id*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate*cbo_work_order*cbo_sending_location*cbo_color_type*save_dataReject*allReject_defect_id*defectReject_type_id*country_ship_date',"../");
				//alert (data);return;
				http.open("POST","requires/wash_receive_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_receive_print_embroidery_Reply_info;
			}
		}
	}

	function fnc_receive_print_embroidery_Reply_info()
	{
		if(http.readyState == 4)
		{
			// alert(http.responseText);
			var variableSettings=$('#sewing_production_variable').val();
			var variableSettingsReject=$('#embro_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			var item_id=$('#cbo_item_name').val();
			var country_id = $("#cbo_country_name").val();

			var reponse=http.responseText.split('**');
			if(reponse[0]==15)
			{
				 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
			}

			if(reponse[0]==10)
			{
				//alert(`${reponse[1]}`);
				release_freezing();
				return;
			}

			/*if(reponse[0]==0)//insert
			{
				//alert(reponse[1]);
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/wash_receive_entry_controller','');
				setFilterGrid("tbl_search",-1);
				//reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id','txt_receive_date,<? echo date("d-m-Y"); ?>','');
				reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id','','','txt_receive_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_receive_entry_controller" );

				if(variableSettings!=1)
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/wash_receive_entry_controller" );
				else
					$("#txt_receive_qty").removeAttr("readonly");

				if(variableSettingsReject!=1)
					$("#txt_reject_qty").attr("readonly");
				else
					$("#txt_reject_qty").removeAttr("readonly");
				release_freezing();
			}
			if(reponse[0]==1)//update
			{
				*/var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/wash_receive_entry_controller','');
				// show_list_view(po_id,'show_cost_listview','printing_cost_list_view','requires/wash_receive_entry_controller','');
				setFilterGrid("tbl_search",-1);
				reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id*save_dataReject*allReject_defect_id','','','txt_receive_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_receive_entry_controller" );

				if(variableSettings!=1)
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+variableSettingsReject+'**'+$("#country_ship_date").val(), "color_and_size_level", "requires/wash_receive_entry_controller" );
				else
					$("#txt_receive_qty").removeAttr("readonly");

				if(variableSettingsReject!=1)
					$("#txt_reject_qty").attr("readonly");
				else
					$("#txt_reject_qty").removeAttr("readonly");

				if(reponse[0]==1 || reponse[0]==2) set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
				release_freezing();
			/*}
			if(reponse[0]==2)//delete
			{
				var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id,'show_dtls_listview','printing_production_list_view','requires/wash_receive_entry_controller','');
				setFilterGrid("tbl_search",-1);
				reset_form('','','txt_receive_qty*txt_challan*txt_reject_qty*hidden_break_down_html*txt_remark*txt_mst_id','','','txt_receive_date');
				get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+$("#cbo_embel_name").val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_receive_entry_controller" );

				if(variableSettings!=1) {
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+variableSettingsReject, "color_and_size_level", "requires/wash_receive_entry_controller" );
				}
				else
				{
					$("#txt_receive_qty").removeAttr("readonly");
				}
				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qty").removeAttr("readonly");
				}
				set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
				release_freezing();
			}else*/
			 if(reponse[0]==35)
			{
				$("#txt_receive_qty").val("");
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
		reset_form('','','txt_receive_qty*txt_reject_qty*txt_challan*hidden_break_down_html*txt_remark*txt_receive_qty*txt_cumul_receive_qty*txt_yet_to_receive*txt_mst_id','','');
		$('#txt_receive_qty').attr('placeholder','')//placeholder value initilize
		$('#txt_cumul_receive_qty').attr('placeholder','')//placeholder value initilize
		$('#txt_yet_to_receive').attr('placeholder','')//placeholder value initilize
		$('#printing_production_list_view').html('')//listview container
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
		$("#txt_receive_qty").val(totalVal);
	}

	function fn_total_rej(tableName,index) // for color and size level
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
				{
					$("#colSize_"+index).val('');
				}
			}
		}
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_receive_qty").val( $("#total_color").val() );
	}

	function fn_colorRej_total(index) //for color level
	{
		var filed_value = $("#colSizeRej_"+index).val();
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
		$("#txt_reject_qty").val( $("#total_color_rej").val() );
	}

	function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty,country_ship_date)
	{
		freeze_window(5);
        //alert(country_ship_date);
		childFormReset();//child from reset
		$("#cbo_item_name").val(item_id);
		$("#txt_order_qty").val(po_qnty);
		$("#cbo_country_name").val(country_id);

		$('#cbo_embel_name').val(3);
		$('#cbo_embel_type').val(0);

		get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/wash_receive_entry_controller" );

		var variableSettings=$('#sewing_production_variable').val();
		var variableSettingsReject=$('#embro_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();

		if(variableSettings==1)
			$("#txt_receive_qty").removeAttr("readonly");
		else
			$('#txt_receive_qty').attr('readonly','readonly');

		if(variableSettingsReject!=1)
			$("#txt_reject_qty").attr("readonly");
		else
			$("#txt_reject_qty").removeAttr("readonly");

		if(variableSettings!=1)
		{
			get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id+'**'+variableSettingsReject+'**'+country_ship_date, "color_and_size_level", "requires/wash_receive_entry_controller" );
		}

		show_list_view(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id+'**1','show_dtls_listview','printing_production_list_view','requires/wash_receive_entry_controller','');
		set_button_status(0, permission, 'fnc_receive_print_embroidery_entry',1,0);
		release_freezing();
	}

	function fnc_checkbox_check(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var emblname=$('#emblname_'+rowNo).val();
		var mst_source= $('#productionsource_'+rowNo).val();

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
							var productionsourceCurrent=$('#productionsource_'+i).val();
							if((emblname!=emblnameCurrent) || (mst_source!=productionsourceCurrent) )
							{
								alert("Please Select Same Emblname Or Source ");
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
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

	function fnc_checkbox_check3(k)
	{
		var row_num=$('#tbl_search  tr').length-1;
		var mst_embel_name= $('#cbo_embel_name').val()*1;
		var mst_source= $('#cbo_source').val()*1;
		var all_id="";

		var isChecked=$('#checkedId_'+k).is(":checked");
		//if(isChecked==true)
		//{
		for (var i=1; i<=row_num; i++)
		{
			var embel_name= $('#emblname_'+i).val()*1;
			var source= $('#productionsource_'+i).val()*1;

			if(mst_source!=source)
			{
				alert('Same Embel Name and Source are Alowed');
				//$('checkedId_'+i).prop('checked', false);
				$('#checkedId_'+i).attr('checked',false);
				return;
			}
			if (document.getElementById('checkedId_'+i).checked==true)
			{
				document.getElementById('checkedId_'+i).value=1;
				var mst_all_id= $('#mstidall_'+i).val()*1;
				if(all_id=="")
				{
					var all_id=mst_all_id;
				}
				else
				{
					 var all_id =all_id+"_"+mst_all_id;
				}
				//alert(all_id );
				$('#txt_mst_id_all').val(all_id)
			}
			else
			{
				document.getElementById('checkedId_'+i).value=0;
				//document.getElementById('checkedId_'+i).value=1;
				var mst_all_id= $('#mstidall_'+i).val()*1;
				if(all_id=="")
				{
					var all_id=mst_all_id;
				}
				else
				{
					 var all_id =all_id+"_"+mst_all_id;
				}//alert(document.getElementById('checkedId_'+i).value);
			}//$('#mst_id_all_'+i).val(all_id)//$('#id_all').val(all_id)
		} //alert(all_id);
	}

	function load_location()
	{
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_source = $('#cbo_source').val();
		var cbo_emb_company = $('#cbo_emb_company').val();
		if(cbo_source==1)
		{
			load_drop_down( 'requires/wash_receive_entry_controller',cbo_emb_company, 'load_drop_down_location', 'location_td');
		}
		else
		{
			load_drop_down( 'requires/wash_receive_entry_controller',cbo_company_name, 'load_drop_down_location', 'location_td');
		}
	}

	function fnc_workorder_search(supplier_id)
	{

		if( form_validation('cbo_company_name*txt_order_no*cbo_emb_company*cbo_embel_name','Company Name*Order No*Embel.Company*Embel.Name')==false )
		{
			return;
		}

		if($("#cbo_source").val()!=3)
		{
			return;
		}
		//alert(supplier_id)
		var company = $("#cbo_company_name").val();
		var po_break_down_id = $("#hidden_po_break_down_id").val();
		var cbo_embel_name = $("#cbo_embel_name").val();
		//var cbo_embel_type = $("#cbo_embel_type").val();
		var gmt_item = $("#cbo_item_name").val();
		load_drop_down( 'requires/wash_receive_entry_controller', company+"_"+supplier_id+"_"+po_break_down_id+"_"+cbo_embel_name+"_"+gmt_item, 'load_drop_down_workorder', 'workorder_td' );
		//alert($('#cbo_cutting_company option').size())
	}

	function fnc_workorder_rate(data,id)
	{
		get_php_form_data(data+"_"+id, "populate_workorder_rate", "requires/wash_receive_entry_controller" );
	}

	function fn_with_source_report2() // Print 3
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
		 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print2", "requires/wash_receive_entry_controller" )
		 return;
	}

	function show_cost_details()
	{
		var system_id=$("#hidden_po_break_down_id").val();
		if(system_id=="")
		{
			alert('Challan No Required!');
			return;
		}

		var page_link='requires/wash_receive_entry_controller.php?action=show_cost_details&sys_id='+system_id;
		var title='Cost Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=330px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{

		}
	}


	function show_cost_details()
	{
		var system_id=$("#hidden_po_break_down_id").val();
		if(system_id=="")
		{
			alert('Order No Required!');
			return;
		}

		var page_link='requires/wash_receive_entry_controller.php?action=show_cost_details&sys_id='+system_id;
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
                <legend>Gmts.Receive From Wash</legend>
                <form name="printembroreceive_1" id="printembroreceive_1" method="" autocomplete="off" >
                    <fieldset>
                        <table width="100%">
                            <tr>
								<td width="110" class="must_entry_caption">Order No</td>
								<td width="200">
									<input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/wash_receive_entry_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:160px " readonly />
									<input type="hidden" id="hidden_po_break_down_id" value="" />
									<input type="hidden" id="country_ship_date" value="" />
								</td>
                                <td width="110" class="must_entry_caption">Company</td>
                                <td width="200">
                                    <?
                                    	echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "",1 );
                                    ?>
                                    <input type="hidden" id="sewing_production_variable" />
                                    <input type="hidden" id="styleOrOrderWisw" />
                                    <input type="hidden" id="embro_production_variable" />
                                    <input type="hidden" id="variable_is_controll" />
                            		<input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
                                    <input type="hidden" id="hidden_currency_id" />
                            		<input type="hidden" id="wip_valuation_for_accounts" />
                                    <input type="hidden" id="hidden_exchange_rate" />
                                    <input type="hidden" id="hidden_piece_rate" />
                                    <input type="hidden" id="cbo_embel_name" value="3" />
                                </td>
                                <td width="110">Country</td>
                                <td><? echo create_drop_down( "cbo_country_name", 170, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 ); ?></td>
                           </tr>
                           <tr>
                                <td>Buyer</td>
                                <td><? echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>
                                <td>Style</td>
                                <td><input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:160px" disabled readonly></td>
                                <td>Item</td>
                                <td><? echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 ); ?></td>
                           </tr>
                           <tr>
                                <td>Order Qty.</td>
                                <td><input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:160px" disabled readonly></td>
                                <td class="must_entry_caption">Wash Type</td>
                                <td id="emb_type_td"><? echo create_drop_down( "cbo_embel_type", 170, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                                <td class="must_entry_caption">Source</td>
                                <td><? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/wash_receive_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_emb_receive', 'emb_company_td' );dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>
                            </tr>
                            <tr>
                                 <td class="must_entry_caption">Wash Company</td>
                                 <td id="emb_company_td"><? echo create_drop_down( "cbo_emb_company", 170, $blank_array,"", 1, "-- Select Embel.Company --", $selected, "" ); ?></td>
                                 <td id="locations">Location</td>
                                 <td id="location_td"><? echo create_drop_down("cbo_location", 170, $blank_array,"", 1,"-- Select Location --", $selected,""); ?></td>
                                 <td id="floors">Floor</td>
                                 <td id="floor_td"><? echo create_drop_down( "cbo_floor", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                            </tr>
                            <tr>
                               	 <td>Work Order</td>
                                 <td id="workorder_td"><? echo create_drop_down( "cbo_work_order", 170, $blank_array,"", 1, "-- Select Work Order--", $selected, "",0 ); ?></td>
                                 <td>Receiving Location</td>
		                         <td><? echo create_drop_down( "cbo_sending_location", 170, $sending_location,"id,location_name", 1, "-- Select Receiving Location --", $selected, "" ); ?></td>
                                 <td>&nbsp;</td>

								<td>
									<input type="button" id="wip_valuation_for_accounts_button" name="" style="width:90px;display:none;" class="formbutton" value="Cost Details" onclick="show_cost_details();">

								</td>
                            </tr>
                    </table>
                </fieldset>
                <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                  <td width="35%" valign="top">
                        <fieldset>
                           <legend>New Entry</legend>
                             <table cellpadding="0" cellspacing="2" width="350px">
                                <tr>
                                    <td width="100" class="must_entry_caption">Receive Date</td>
                                    <td width="250"><input name="txt_receive_date" id="txt_receive_date" class="datepicker"  type="text" value="<? echo date("d-m-Y")?>" style="width:100px;" /></td>
                                </tr>
                                <tr>
                                    <td>Color Type</td>
                                    <td id="color_type_td" colspan="2"><? echo create_drop_down( "cbo_color_type", 110, $blank_array,"", 1, "Select Type", $selected, "",1,0 ); ?></td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Receive Qty</td>
                                    <td>
                                    <input type="text" name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric"  style="width:100px" readonly >
                                    <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                    <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Reject Qty</td>
                                    <td><input type="text" name="txt_reject_qty" id="txt_reject_qty"  class="text_boxes_numeric"  style="width:100px" readonly ></td>
                                    <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Reject Chk" style="width:70px" onClick="openmypage_defectQty(1);"/>
									<input type="hidden" name="save_dataReject" id="save_dataReject" readonly />
                            		<input type="hidden" name="allReject_defect_id" id="allReject_defect_id" readonly />
                            		<input type="hidden" name="defectReject_type_id" id="defectReject_type_id" readonly value="1" />
								</td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Challan No</td>
                                    <td>
                                        <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" />
                                        Sys. Chln.<input type="text" name="txt_sys_chln" id="txt_sys_chln" class="text_boxes" style="width:45px" placeholder="Display" disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Remarks</td>
                                    <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:220px" /></td>
                                </tr>
                            </table>
                        </fieldset>
                  </td>
                  <td width="1%" valign="top"></td>
                  <td width="25%" valign="top">
                        <fieldset>
                            <legend>Display</legend>
                             <table cellpadding="0" cellspacing="2" width="250px" >
                                <tr>
                                    <td width="100">Issue Qty</td>
                                    <td width="90"><input type="text" name="txt_issue_qty" id="txt_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly /></td>
                                </tr>
                                <tr>
                                    <td>Cumul.Receive Qty</td>
                                    <td><input type="text" name="txt_cumul_receive_qty" id="txt_cumul_receive_qty" class="text_boxes_numeric" style="width:80px" disabled readonly /> </td>
                                </tr>
                                <tr>
                                    <td>Yet to Receive </td>
                                    <td><input type="text" name="txt_yet_to_receive" id="txt_yet_to_receive" class="text_boxes_numeric" style="width:80px" disabled readonly /></td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td width="33%" valign="top" >
                        <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        <br />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="9" valign="middle" class="button_container">
                        <?
						$date=date('d-m-Y');
                        echo load_submit_buttons( $permission, "fnc_receive_print_embroidery_entry", 0, 1,"reset_form('printembroreceive_1','list_view_country','','txt_receive_date,".$date."','childFormReset()')",1);
                        ?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        <input id="print02" class="formbutton" style="width:90px;" value="Print 2" name="print02" onClick="fn_with_source_report2()" type="button">

                    </td>
                	<td>&nbsp;</td>
                </tr>
            </table>
            <div style="width:900px; margin-top:5px;"  id="printing_production_list_view" align="center"></div>
               	<div style="width:900px; margin-top:5px;" id="printing_cost_list_view" align="center"></div>
            </form>
            </fieldset>
        </div>
		<div id="list_view_country" style="width:380px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:13px"></div>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//$('#cbo_embel_name').val(3);
//$('#cbo_embel_name').trigger('change');
</script>
</html>
