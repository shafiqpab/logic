<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Wash Entry
				
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin 
Creation date 	: 	28-04-2019
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
echo load_html_head_contents("wash Entry","../", 1, 1, $unicode,'','');
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var str_supervisor = [<? echo substr(return_library_autocomplete( "select distinct(supervisor) as supervisor from pro_garments_production_mst", "supervisor"), 0, -1); ?>]; 
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
	if ( form_validation('cbo_company_name*cbo_source*cbo_wash_company','Company Name*Production Source*Production Company')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id=this.contentDoc.getElementById("hidden_country_id").value; 
			var pack_type=this.contentDoc.getElementById("hidden_pack_type").value; 
					
			if (po_id!="")
			{
				//freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				$("#txt_pack_type").val(pack_type);
				$("#txt_pack_type").val(pack_type);
				
				childFormReset();//child from reset
				get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/wash_entry_controller" );
 				
				var variableSettings=$('#wash_production_variable').val();
				var variableSettingsReject=$('#wash_production_variable_rej').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				if(variableSettings!=1){ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+pack_type+'**'+variableSettings, "color_and_size_level", "requires/wash_entry_controller" ); 
					$("#txt_wash_qty").attr("readonly","readonly");
				}
				else
				{
					$("#txt_wash_qty").removeAttr("readonly");
				}
				
				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qnty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qnty").removeAttr("readonly");
				}
				
				var prod_reso_allo=$('#prod_reso_allo').val();
				show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+prod_reso_allo+'**'+pack_type+'**'+variableSettings,'show_dtls_listview','list_view_container','requires/wash_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
				show_list_view(po_id,'show_country_listview','list_view_country','requires/wash_entry_controller','');		
				set_button_status(0, permission, 'fnc_wash_output_entry',1,0);
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled"); 
		}
	}
}

function fnc_wash_output_entry(operation)
{
	var source=$("#cbo_source").val();
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$("#wash_production_variable").val(), "wash_output_print", "requires/wash_entry_controller" ) 
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
 		if ( form_validation('cbo_company_name*txt_order_no*cbo_source*cbo_wash_company*txt_wash_date*txt_challan','Company Name*Order No*Source*Wash Company*wash Date*Reporting Hour*txt_challan')==false )
		{
			return;
		}		
		else
		{
			if($("#cbo_source").val()==1)
			{
				if(form_validation('cbo_location*cbo_floor','Location*Floor*Line')==false)
				{
					return;
				}
			}
			if($('#txt_wash_qty').val()<1&&$('#txt_alter_qnty').val()<1&&$('#txt_spot_qnty').val()<1&&$('#txt_reject_qnty').val()<1)
			{
				alert("wash quantity or Alter quantity or Spot quantity or Reject quantity should be filled up.");
				return;
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_wash_date').val(), current_date)==false)
			{
				alert("wash Date Can not Be Greater Than Current Date");
				return;
			}	
			if($("#cbo_source").val()==1 && ($("#cbo_wash_line").val()==0 || $("#cbo_wash_line").val()=="") )
			{
				//alert("Please Select wash Line");return;
			}
			freeze_window(operation);			
			var wash_production_variable = $("#wash_production_variable").val();
			var variableSettingsReject=$('#wash_production_variable_rej').val();
			if(wash_production_variable=="" || wash_production_variable==0)
			{
 				wash_production_variable=3;
 				variableSettingsReject=3;
			}
			
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			
			var i=0;  var k=0; var colorIDvalue=''; var colorIDvalueRej='';
			if(wash_production_variable==2)//color level
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
			else if(wash_production_variable==3)//color and size level
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
			
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*wash_production_variable*wash_production_variable_rej*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_wash_company*cbo_location*cbo_floor*txt_wash_date*cbo_produced_by*cbo_wash_line*txt_reporting_hour*txt_super_visor*txt_wash_qty*txt_reject_qnty*txt_alter_qnty*txt_challan*txt_remark*txt_input_quantity*txt_cumul_wash_qty*txt_yet_to_wash*hidden_break_down_html*txt_mst_id*prod_reso_allo*txt_spot_qnty*save_data*defect_type_id*save_dataSpot*defectSpot_type_id*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate*cbo_work_order*txt_pack_type',"../");
 			// alert (data); return;
 			http.open("POST","requires/wash_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_wash_output_entry_Reply_info;
		}
	}
}
  
function fnc_wash_output_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var variableSettings=$('#wash_production_variable').val();
		var variableSettingsReject=$('#wash_production_variable_rej').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		var prod_reso_allo=$('#prod_reso_allo').val();
		var pack_type=$("#txt_pack_type").val();
		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_wash_output_entry('+ reponse[1]+')',4000); 
		}
		else if(reponse[0]==101)
		{
			alert("Sorry! This Order Found in Bundle Wise wash Output. Bundle List: "+reponse[1]+" Please Go to Bundle Wise wash Output Page for Update action");
			release_freezing();
			return false;	
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings.");
			release_freezing();
			return false;
		}
		
		else if(reponse[0]==0)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo+'**'+pack_type+'**'+variableSettings,'show_dtls_listview','list_view_container','requires/wash_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','cbo_produced_by*cbo_wash_line*txt_reporting_hour*txt_super_visor*txt_wash_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_wash_qty*txt_yet_to_wash*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id','txt_wash_date,<? echo date("d-m-Y"); ?>','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/wash_entry_controller" );
 			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/wash_entry_controller" ); 
			}
			else
			{
				$("#txt_wash_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_wash_output_entry',1,0);
		}
		else if(reponse[0]==1)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo+'**'+pack_type+'**'+variableSettings,'show_dtls_listview','list_view_container','requires/wash_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','cbo_produced_by*cbo_wash_line*txt_reporting_hour*txt_super_visor*txt_wash_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_wash_qty*txt_yet_to_wash*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id','txt_wash_date,<? echo date("d-m-Y"); ?>*txt_challan,0','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/wash_entry_controller" );
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/wash_entry_controller" ); 
			}
			else
			{
				$("#txt_wash_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			} 
			
			set_button_status(0, permission, 'fnc_wash_output_entry',1,0);
		}
		else if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo+'**'+pack_type+'**'+variableSettings,'show_dtls_listview','list_view_container','requires/wash_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','cbo_produced_by*cbo_wash_line*txt_reporting_hour*txt_super_visor*txt_wash_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_wash_qty*txt_yet_to_wash*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id','txt_wash_date,<? echo date("d-m-Y"); ?>*txt_challan,0','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/wash_entry_controller" );
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/wash_entry_controller" ); 
			}
			else
			{
				$("#txt_wash_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}
			
			set_button_status(0, permission, 'fnc_wash_output_entry',1,0);
		}
		else if(reponse[0]==25)
		{
			$("#txt_wash_qty").val("");
			show_msg('26');
			release_freezing();
		}
		else if(reponse[0]==35)
		{
			$("#txt_wash_qty").val("");
			//show_msg('25');
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
	reset_form('','','cbo_produced_by*cbo_wash_line*txt_reporting_hour*txt_super_visor*txt_wash_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_wash_qty*txt_yet_to_wash*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id','','');
 	$('#txt_input_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_wash_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_wash').attr('placeholder','');//placeholder value initilize
	$('#list_view_container').html('');//listview container
	$("#breakdown_td_id").html('');
}  
function fn_hour_check(val)
{
	if(val*1>12)
	{
		alert("You Cross 12!!This is 12 Hours.");
		$("#txt_reporting_hour").val('');
	}
}

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
 	var hidden_variable_cntl=$('#hidden_variable_cntl').val()*1;
 	if(filed_value*1 > placeholder_value*1)
	{
		if(hidden_variable_cntl==1 && txt_user_lebel!=2)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+tableName+index).val('');
			$("#txt_wash_qty").val('');
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
 	math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	var totalVal = 0;
	$("input[name=colorSize]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_wash_qty").val(totalVal);
}

function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val();
	
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);
	
	var totalValRej = 0;
	$("input[name=colorSizeRej]").each(function(index, element) {
        totalValRej += ( $(this).val() )*1;
    });
	$("#txt_reject_qnty").val(totalValRej);
}

function fn_colorlevel_total(index) //for color level
{
	
	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
 	var hidden_variable_cntl=$('#hidden_variable_cntl').val()*1;
	if(filed_value*1 > placeholder_value*1)
	{
		if(hidden_variable_cntl==1 && txt_user_lebel!=2)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+index).val('');
			$("#txt_wash_qty").val('');
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
	$("#txt_wash_qty").val( $("#total_color").val() );
} 


function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qnty").val( $("#total_color_rej").val() );
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty, pack_type)
{
	if($('#cbo_wash_company').val()==0){alert("Please Select  Wash Company");return;}
	freeze_window(5);
	
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
	$("#txt_pack_type").val(pack_type);
	$("#selected_proudction_company").val($('#cbo_wash_company').val());
	
	childFormReset();//child from reset
	get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/wash_entry_controller" );
	
	var variableSettings=$('#wash_production_variable').val();
	var variableSettingsReject=$('#wash_production_variable_rej').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var prod_reso_allo=$('#prod_reso_allo').val();
	
	if(variableSettings!=1)
	{ 
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/wash_entry_controller" ); 
		$("#txt_wash_qty").attr("readonly","readonly");
	}
	else
	{
		$("#txt_wash_qty").removeAttr("readonly");
	}
	
	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qnty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qnty").removeAttr("readonly");
	}
	
	show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+prod_reso_allo+'**_'+'**'+variableSettings,'show_dtls_listview','list_view_container','requires/wash_entry_controller','');
	set_button_status(0, permission, 'fnc_wash_output_entry',1,0);
	release_freezing();
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

function check_produced_by(val)
{
	//alert (val)
	var order_no=$('#txt_order_no').val();
	var po_id=$('#hidden_po_break_down_id').val();
	var item_id=$('#cbo_item_name').val();
	var order_qty=$('#txt_order_qty').val();
	if(order_no=="")
	{
		alert ("Order Number is Empty! Plase Browse 1st.");
		$('#cbo_produced_by').val('');
		return;
	}
	else
	{
		var response=return_global_ajax_value( po_id+'**'+item_id, 'piece_rate_order_cheack', '', 'requires/wash_entry_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			if (response[2]>=order_qty)
			{
				if(val==1)
				{
					alert ("This Order Fully Produced By Piece Rate Worker But Selected Salary Based. Plese Check Piece Rate WO No :-"+response[1]);
					$('#cbo_produced_by').val(2);
				}
			}
			else
			{
				if(val!=0)
				{
					if(val==1) 
					{
						var worker_type='Salary Based Worker.';
					}
					else
					{
						var worker_type='Piece Rate Worker.';
					}
					
					var bal_qty=order_qty-response[2];
					
					var r=confirm("Press \"OK\" You Select "+ worker_type + " \nPress \"Cancel\" Select New Produced by.");
					if (r==true)
					{
						alert ("Total Salary Based Cutting Qty Balance :- "+ bal_qty);
					}
					else
					{
						alert ("Total Salary Based Cutting Qty Balance :- "+ bal_qty);
						if(val==1)
						{
							$('#cbo_produced_by').val(2);
						}
						else
						{
							$('#cbo_produced_by').val(1);
						}
					}
				}
			}
		}
		else if (response[0]==0)
		{
			if(val==2)
			{
				alert ("This order fully produced by salary based worker, but selected piece rate worker");	
				$('#cbo_produced_by').val(1);
			}
		}
	}
}


function fn_autocomplete()
{
	 $("#txt_super_visor").autocomplete({
		 source: str_supervisor
	  });
}
   
function openmypage_defectQty(type)
{
	var txt_mst_id=$("#txt_sys_chln").val();
	var company_name=$("#cbo_company_name").val();
	var txt_job_no=$('#txt_job_no').val();
	var txt_order_no=$('#txt_order_no').val();
	var hidden_po_break_down_id=$('#hidden_po_break_down_id').val();
	if(txt_order_no=='')
	{
		alert('Please Order No Browse First.');
		return;
	}
	else
	{
		if(type==1)
		{
			var save_data=$('#save_data').val();
			var all_defect_id=$('#all_defect_id').val();
			var defect_qty=$('#txt_alter_qnty').val();
		}
		else if(type==2)
		{
			var save_data=$('#save_dataSpot').val();
			var all_defect_id=$('#allSpot_defect_id').val();
			var defect_qty=$('#txt_spot_qnty').val();
		}
		
		var defect_qty=0; var title = '';
		if (type==1)
		{
			defect_qty=$('#txt_alter_qnty').val();
			title = 'Alter Qty Info';
		}
		else if (type==2)
		{
			defect_qty=$('#txt_spot_qnty').val();
			title = 'Spot Qty Info';
		}
		
		var page_link = 'requires/wash_entry_controller.php?hidden_po_break_down_id='+hidden_po_break_down_id+'&txt_mst_id='+txt_mst_id+'&save_data='+save_data+'&defect_qty='+defect_qty+'&all_defect_id='+all_defect_id+'&type='+type+'&action=defect_data';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=400px,center=1,resize=1,scrolling=0','');
	
		emailwindow.onclose=function()
		{
			var save_string=this.contentDoc.getElementById("save_string").value;
			var tot_defectQnty=this.contentDoc.getElementById("tot_defectQnty").value;
			var all_defect_id=this.contentDoc.getElementById("all_defect_id").value;
			var defect_type_id=this.contentDoc.getElementById("defect_type_id").value;
			
			if(type==1) 
			{
				$('#save_data').val(save_string);
				//$('#txt_alter_qnty').val(tot_defectQnty);
				$('#all_defect_id').val(all_defect_id);
				$('#defect_type_id').val(defect_type_id);
			}
			else if(type==2) 
			{
				$('#save_dataSpot').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allSpot_defect_id').val(all_defect_id);
				$('#defectSpot_type_id').val(defect_type_id);
			}
			release_freezing();
		}
	}
}

function fnc_workorder_search(supplier_id)
{
	
	if( form_validation('cbo_company_name*txt_order_no*cbo_wash_company','Company Name*Order No*Wash Company')==false )
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
	load_drop_down( 'requires/wash_entry_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' ); 
	//alert($('#cbo_cutting_company option').size())
}

function fnc_workorder_rate(data,id)
{
	get_php_form_data(data+"_"+id, "populate_workorder_rate", "requires/wash_entry_controller" );
}

function fnc_company_check(val)
{
	if(val==1)
	{
		if($("#cbo_company_name").val()==0)
		{
			alert("Please Select Company.");
			$("#cbo_source").val(0);
			$("#cbo_wash_company").val(0);
			return;
		}
		else
		{
			get_php_form_data(document.getElementById('cbo_wash_company').value,'production_process_control','requires/wash_entry_controller' );
		}
	}
	else
	{
		get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/wash_entry_controller' );
	}
 }

function fnc_loadvariable(val)
{
	if($('#selected_proudction_company').val()!=0){
		if(confirm("If you change Production Company your gaven data will be reset.")==true){
			$("#breakdown_td_id").html('');
			$("#txt_yet_to_wash").val(0);
			$("#txt_cumul_wash_qty").val(0);
			$("#txt_input_quantity").val(0);
			$('#selected_proudction_company').val(0);
			$("#txt_qty_source").val('');
		}
		else
		{
			$("#cbo_wash_company").val($('#selected_proudction_company').val());return;
		}
	}
	$("#txt_qty_source").val('');
	get_php_form_data( $("#cbo_company_name").val()+'**'+val,'load_variable_settings','requires/wash_entry_controller');
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs("../",$permission);  ?>
	<div style="width:800px; float:left;">
        <fieldset style="width:800px;">
        <legend>Production Module</legend>  
			<form name="washoutput_1" id="washoutput_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td width="100" class="must_entry_caption">Company</td>
                            <td width="150">                                
								<? 
                                 echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "fnc_loadvariable(this.value);" );
                                ?>
                                <input type="hidden" id="wash_production_variable" />	 
                                <input type="hidden" id="styleOrOrderWisw" />
                                <input type="hidden" id="prod_reso_allo" />
                                <input type="hidden" id="wash_production_variable_rej" />
                                <input type="hidden" id="variable_is_controll" />
                                <input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />
                                <input type="hidden" id="txt_qty_source" />
                                <input type="hidden" id="hidden_currency_id" />
                                <input type="hidden" id="hidden_exchange_rate" />
                                <input type="hidden" id="hidden_piece_rate" /> 
                                <input type="hidden" id="selected_proudction_company" value="0" /> 
							</td>
                            
                        	<td class="must_entry_caption">Source</td>
                             <td>
								 <?
                                 echo create_drop_down( "cbo_source", 150, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value); load_drop_down( 'requires/wash_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_wash_output', 'sew_company_td' ); dynamic_must_entry_caption(this.value);", 0, '1,3' );
								 //get_php_form_data($('#cbo_company_name').val()+'**'+this.value+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val()+'**'+$('#txt_pack_type').val(), 'display_bl_qnty', 'requires/wash_entry_controller');
                                 ?>
                             </td>
                            <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
                           <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
                         	 <td class="must_entry_caption">Wash Company</td>
                             <td id="sew_company_td" >
								 <?
                                 echo create_drop_down( "cbo_wash_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "" );
                                 ?>
						     </td>
                         </tr>
                         <tr>
                            <td width="100" class="must_entry_caption">Order No</td>
                            <td width="150">
								<input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage('requires/wash_entry_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value+'&production_company='+document.getElementById('cbo_wash_company').value+'&hidden_variable_cntl='+document.getElementById('hidden_variable_cntl').value+'&hidden_preceding_process='+document.getElementById('hidden_preceding_process').value,'Order Search')"  class="text_boxes" style="width:140px " readonly>
                                <input type="hidden" id="hidden_po_break_down_id" value="" />
							</td>
                            
                            <td width="100" >Country</td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_country_name", 150, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                ?> 
                            </td>
                            <td>Buyer</td>
                            <td>
								<?
                                echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                ?>
							</td>
                            
                        </tr>
                        <tr>    
                            <td>Job No</td>
                            <td>
                                <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px " disabled readonly>	
							</td>
                            <td>Style</td>
                            <td>
                                <input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:140px " disabled readonly>
							</td>
                        	 <td>Item</td>
                             <td>
								 <?
                                 echo create_drop_down( "cbo_item_name", 150, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                 ?>
							 </td> 
                             
                        </tr>
                        <tr>    
                             <td>Order Qty</td>
                             <td>
                                 <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:140px " disabled readonly>
							 </td>
                             <td>Pack Type</td>
                             <td>
                                 <input name="txt_pack_type" id="txt_pack_type" class="text_boxes" style="width:140px " disabled readonly>
							 </td>
                             <td id="locations">Location</td>
                             <td id="location_td">
								 <?
                                 echo create_drop_down( "cbo_location", 150,$blank_array,"", 1, "-- Select Location --", $selected, "" );
                                 ?>
							 </td>
                        </tr>
                        <tr>
                        	<td id="floors">Floor</td>
                             <td id="floor_td">
								 <? 
                                 echo create_drop_down( "cbo_floor", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                                 ?>
                             </td>
                         	 <td class="">Work Order</td>
                             <td  id="workorder_td">
                                 <?
                                 echo create_drop_down( "cbo_work_order",150, $blank_array,"", 1, "-- Select Work Order--", $selected, "",0 );	
                                 ?>
                             </td>
                             <td  id="workorder_rate_id" style=" color:red; font-size:12px" colspan="2"></td>
                        </tr>
                    </table>
                </fieldset>
                <br>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="35%" valign="top">
                            <fieldset>
                            <legend>New Entry</legend>
                                <table  cellpadding="0" cellspacing="1" width="100%">
                                	<tr>
                                        <td width="100" class="must_entry_caption">Produced By</td>
                                        <td width="110" colspan="2">
                                            <?
                                                echo create_drop_down( "cbo_produced_by", 110, $worker_type,"", 1, "--Select Type--", 1, "check_produced_by(this.value)",0 );	
                                            ?> 
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td class="must_entry_caption">Wash Date</td>
                                         <td colspan="2"> 
                                         	<input name="txt_wash_date" id="txt_wash_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"  onChange="load_drop_down( 'requires/wash_entry_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value, 'load_drop_down_wash_line_floor', 'wash_line_td' );" />
                                        </td>
                                     </tr>
                                     <tr  style="display: none;">
                                        <td class="must_entry_caption">Wash Line No</td> 
                                        <td id="wash_line_td" colspan="2">            
											<?
                                            	echo create_drop_down( "cbo_wash_line", 110, $blank_array,"", 1, "Select Line", $selected, "",1,0 );		
                                            ?>	
                              			</td> 
                                   </tr>
                                     <tr  style="display: none;">
                                       <td class="must_entry_caption">Reporting Hour</td> 
                                       <td colspan="2">
                                       	<input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                                       </td>
                                     </tr>
                                   <tr style="display: none;">
                                     <td>Supervisor</td> 
                                     <td colspan="2"> 
                                     	<input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();"  style="width:100px">
                                     </td>
                                   </tr>
                                   <tr>
                                        <td class="must_entry_caption">QC Pass Qty</td> 
                                        <td colspan="2" valign="top">
                                        	<input name="txt_wash_qty" id="txt_wash_qty" class="text_boxes_numeric"  style="width:100px" readonly >
                                            <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                            <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                        </td>
                                   </tr>
                                   <tr  style="display: none;">
                                     <td>Alter Qty </td>
                                     <td><input type="text" name="txt_alter_qnty" id="txt_alter_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                     <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="Alt Defect" style="width:60px" onClick="openmypage_defectQty(1);"/></td>
                                   </tr>
                                   <tr  style="display: none;">
                                     <td >Spot Qty </td>
                                     <td><input type="text" name="txt_spot_qnty" id="txt_spot_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                     <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="Spt Defect" style="width:60px" onClick="openmypage_defectQty(2);"/></td>
                                   </tr>
                                   <tr>
                                     <td>Reject Qty</td>
                                     <td><input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:100px;" readonly /></td>
                                     <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="Rjt Defect" style="width:60px" onClick="openmypage_defectQty(3);"/></td>
                                   </tr>
                                   <tr>
                                         <td class="must_entry_caption">Challan No</td> 
                                         <td colspan="2">
                                           <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" value="0" style="width:50px" />
                                           Sys. Chln<input type="text" name="txt_sys_chln" id="txt_sys_chln" class="text_boxes" style="width:45px" placeholder="Display" disabled />
                                         </td>
                                   </tr>
                                   <tr>
                                     <td>Remarks</td>
                                     <td colspan="2"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:150px;" /></td>
                                   </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                         <td width="22%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="100">Input Quantity</td>
                                        <td>
                                            <input type="text" name="txt_input_quantity" id="txt_input_quantity" class="text_boxes_numeric" style="width:70px" readonly disabled  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Cumul. wash Qty</td>
                                        <td>
                                            <input type="text" name="txt_cumul_wash_qty" id="txt_cumul_wash_qty" class="text_boxes_numeric" style="width:70px" readonly disabled />
                                        </td>
                                    </tr>
                                     <tr>
                                        <td>Yet to wash</td>
                                        <td>
                                            <input type="text" name="txt_yet_to_wash" id="txt_yet_to_wash" class="text_boxes_numeric" style="width:70px" / readonly disabled >
                                        </td>
                                        <td id="workorder_rate_id" style=" color:red; font-size:12px" colspan="2"> </td>
                                    </tr>
                                </table>
                            </fieldset>	
                        </td>
                        <td width="40%" valign="top" >
                            <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>                         
                     </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
							<?
								$date=date('d-m-Y');
								echo load_submit_buttons( $permission, "fnc_wash_output_entry", 0,1,"reset_form('washoutput_1','list_view_country','','txt_wash_date,".$date."*txt_challan,0','childFormReset()')",1); 
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
                            <input type="hidden" name="save_data" id="save_data" readonly />
                            <input type="hidden" name="all_defect_id" id="all_defect_id" readonly />
                            <input type="hidden" name="defect_type_id" id="defect_type_id" readonly />
                            <input type="hidden" name="save_dataSpot" id="save_dataSpot" readonly />
                            <input type="hidden" name="allSpot_defect_id" id="allSpot_defect_id" readonly />
                            <input type="hidden" name="defectSpot_type_id" id="defectSpot_type_id" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
                <div style="width:800px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
        	</fieldset>
        </div>
		<div id="list_view_country" style="width:400px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
