<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create iron output
				
Functionality	:	This form is finish input entry
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	25-03-2013
Updated by 		: 	Kausar (Creating Print Report )	
Update date		: 	09-01-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Iron Output Info","../", 1, 1, $unicode,'','');

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

<?php   
if($_SESSION['logic_erp']['data_arr'][721]){
	echo "var field_level_data= " . json_encode($_SESSION['logic_erp']['data_arr'][721]). ";\n";
}
?>

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
	
	if ( form_validation('cbo_company_name*cbo_source*cbo_iron_company','Company Name*Production Source*Production Company')==false )
	{
		return;
	}

	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;	
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
				
			if (po_id!="")
			{
				//freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				
				childFormReset();//child from reset
				get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/iron_output_controller" );
 				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				var variableSettingsReject=$('#iron_production_variable_rej').val();
				var preceding_process=$('#hidden_preceding_process').val();
				if(variableSettings!=1){ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/iron_output_controller" ); 
				}
				else
				{
					$("#txt_iron_qty").removeAttr("readonly");
				}
				
				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qnty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qnty").removeAttr("readonly");
				}
				
				show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','list_view_container','requires/iron_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
				show_list_view(po_id+'_'+preceding_process,'show_country_listview','list_view_country','requires/iron_output_controller','');
				set_button_status(0, permission, 'fnc_iron_input',1,0);
				release_freezing();
			}
		}
		$("#cbo_company_name").attr("disabled","disabled"); 
	}//end else
}//end function



function fnc_iron_input(operation)
{
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title, "iron_output_print", "requires/iron_output_controller" ) 
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
 		if ( form_validation('cbo_company_name*txt_order_no*cbo_iron_company*txt_iron_date','Company Name*Order No*Iron Company*Input Date')==false )
		{
			return;
		}		
		else
		{     
			if($("#cbo_source").val()==1)
			{
				if(form_validation('cbo_location*cbo_floor','Location*Floor')==false)// 
				{
					return;
				}
			}

			
			if($('#txt_iron_qty').val()<1&&$('#txt_reiron_qty').val()<1&&$('#txt_reject_qnty').val()<1)
			{
				alert("Iron quantity or Reiron quantity or Reject quantity should be filled up");
				return;
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_iron_date').val(), current_date)==false)
			{
				alert("Iron Date Can not Be Greater Than Current Date");
				return;
			}	
						
			var sewing_production_variable = $("#sewing_production_variable").val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var variableSettingsReject=$('#iron_production_variable_rej').val();
			if(sewing_production_variable=="" || sewing_production_variable==0)
			{
				sewing_production_variable  = 3;
			}
			if(variableSettingsReject=="" || variableSettingsReject==0)
			{
				variableSettingsReject  = 3;
			}
			if(variableSettingsReject != sewing_production_variable){
				alert("Please check your variable sattings");
				return;
			}
			freeze_window(operation);
			var i=0; var k=0;r=0; var colorIDvalue=''; var colorIDvalueRej=''; var colorIDvalueReiron='';
			// ============================ for good gmts ================================
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
						color_size_breakdown_id = $(this).attr('data-colorSizeBreakdown');
						if(i==0)
						{
							// colorIDvalue = colorList[i]+"*"+$(this).val();
							colorIDvalue = color_size_breakdown_id+"*"+$(this).val();
						}
						else
						{
							// colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
							colorIDvalue += "***"+color_size_breakdown_id+"*"+$(this).val();
						}
					}
 					i++;
				});
			}
			// =============================== for famage gmts =========================
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
						color_size_breakdown_id = $(this).attr('data-colorSizeBreakdown');
						if(k==0)
						{
							// colorIDvalueRej = colorList[k]+"*"+$(this).val();
							colorIDvalueRej = color_size_breakdown_id+"*"+$(this).val();
						}
						else
						{
							// colorIDvalueRej += "***"+colorList[k]+"*"+$(this).val();
							colorIDvalueRej += "***"+color_size_breakdown_id+"*"+$(this).val();
						}
					}
 					k++;
				});
			}
			// ============================= for re-iron gmts ==============================
			if(sewing_production_variable==2)//color level
			{
				$("input[name=txtColSizeReiron]").each(function(index, element) {
 					if( $(this).val()!='' )
					{
						if(r==0)
						{
							colorIDvalueReiron = colorList[r]+"*"+$(this).val();
						}
						else
						{
							colorIDvalueReiron += "**"+colorList[r]+"*"+$(this).val();
						}
					}
					r++;
				});
				//alert (colorIDvalueReiron);return;
			}
			else if(sewing_production_variable==3)//color and size level
			{
				$("input[name=colorSizeReiron]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						color_size_breakdown_id = $(this).attr('data-colorSizeBreakdown');
						if(r==0)
						{
							// colorIDvalueReiron = colorList[r]+"*"+$(this).val();
							colorIDvalueReiron = color_size_breakdown_id+"*"+$(this).val();
						}
						else
						{
							// colorIDvalueReiron += "***"+colorList[r]+"*"+$(this).val();
							colorIDvalueReiron += "***"+color_size_breakdown_id+"*"+$(this).val();
						}
					}
 					r++;
				});
			}
				
			 
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+"&colorIDvalueReiron="+colorIDvalueReiron+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*iron_production_variable_rej*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_iron_company*cbo_location*cbo_floor*txt_iron_date*cbo_produced_by*txt_reporting_hour*txt_iron_qty*txt_reiron_qty*txt_challan*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id*txt_reject_qnty*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate*cbo_work_order*cbo_table_name*txt_internal_ref',"../");
 			
 			http.open("POST","requires/iron_output_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_iron_input_Reply_info;
		}
	}
}
  
function fnc_iron_input_Reply_info()
{
 	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var variableSettingsReject=$('#iron_production_variable_rej').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000); 
		}
		if(reponse[0]==0)
		{ 
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','list_view_container','requires/iron_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_iron_qty*txt_reiron_qty*txt_reject_qnty*txt_challan*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','','txt_iron_date*txt_reporting_hour');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/iron_output_controller" );
 			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/iron_output_controller" ); 
			}
			else
			{
				$("#txt_iron_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}
			
			
		}
		else if(reponse[0]==1)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','list_view_container','requires/iron_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_iron_qty*txt_reiron_qty*txt_reject_qnty*txt_challan*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','','txt_iron_date*txt_reporting_hour');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/iron_output_controller" );
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/iron_output_controller" ); 
			}
			else
			{
				$("#txt_iron_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}
			
			set_button_status(0, permission, 'fnc_iron_input',1,0);
			release_freezing();
		}
		else if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','list_view_container','requires/iron_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_iron_qty*txt_reiron_qty*txt_reject_qnty*txt_challan*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','','txt_iron_date*txt_reporting_hour');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/iron_output_controller" );
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/iron_output_controller" ); 
			}
			else
			{
				$("#txt_iron_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}
			
			set_button_status(0, permission, 'fnc_iron_input',1,0);
			release_freezing();
		}
		else if(reponse[0]==25)
		{
			show_msg('27');
			$("#txt_iron_qty").val("");
			release_freezing();
		}
		
		else if(reponse[0]==35)
		{
			$("#txt_iron_qty").val("");
			show_msg('25');
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings.");
		}
		release_freezing();
		
		
		
 	}
} 

function childFormReset()
{
	//txt_iron_date  txt_reporting_hour cbo_time txt_iron_qty txt_remark txt_sewing_quantity txt_cumul_iron_qty txt_yet_to_iron
	reset_form('','','txt_reporting_hour*txt_iron_qty*txt_reiron_qty*txt_challan*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id','','');
 	$('#txt_sewing_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_iron_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_iron').attr('placeholder','');//placeholder value initilize
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
			$("#txt_iron_qty").val('');
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
	$("#txt_iron_qty").val(totalVal);
}

function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val()*1;
	var colsizes= $("#colSize_"+tableName+index).val()*1;
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
	$("#txt_reject_qnty").val(totalValRej);
}

function fn_total_reiron(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeReiron_"+tableName+index).val();
	var colsizes= $("#colSize_"+tableName+index).val();
    if(colsizes=="" && filed_value !="")
    {
    	// this if condition add for when size null but reject qnty given scenery 
    	$("#colSize_"+tableName+index).val(0);
    }
	
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSizeReiron_"+tableName, "+", totalRow);
	
	var totalValRej = 0;
	$("input[name=colorSizeReiron]").each(function(index, element) {
        totalValRej += ( $(this).val() )*1;
    });
	$("#txt_reiron_qty").val(totalValRej);
}

function fnc_company_check(val)  
{
	if(val==1)
	{
		if($("#cbo_company_name").val()==0)
		{
			alert("Please Select Company.");
			$("#cbo_source").val(0);
			$("#cbo_iron_company").val(0);
			return;
		}
		else
		{
			get_php_form_data(document.getElementById('cbo_iron_company').value,'production_process_control','requires/iron_output_controller' );
		}
	}
	else
	{
		get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/iron_output_controller' );
	}
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
			$("#txt_iron_qty").val('');
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
	$("#txt_iron_qty").val( $("#total_color").val() );
} 

function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qnty").val( $("#total_color_rej").val() );
}

function fn_colorReiron_total(index) //for color level
{
	var filed_value = $("#colSizeReiron_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_reiron", "colSizeReiron_", "+", totalRow);
	$("#txt_reiron_qty").val( $("#total_color_reiron").val() );
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);
	
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
 				
	childFormReset();//child from reset
	get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/iron_output_controller" );
	
	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var variableSettingsReject=$('#iron_production_variable_rej').val();
	if(variableSettings!=1)
	{ 
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/iron_output_controller" ); 
	}
	else
	{
		$("#txt_iron_qty").removeAttr("readonly");
	}
	
	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qnty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qnty").removeAttr("readonly");
	}
	
	show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','list_view_container','requires/iron_output_controller','');
	set_button_status(0, permission, 'fnc_iron_input',1,0);
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
 
function fnc_workorder_search(supplier_id)
{
	
	if( form_validation('cbo_company_name*txt_order_no*cbo_iron_company','Company Name*Order No*Iron Company')==false )
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
	load_drop_down( 'requires/iron_output_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' ); 
	//alert($('#cbo_cutting_company option').size())
}

function fnc_workorder_rate(data,id)
{
	get_php_form_data(data+"_"+id, "populate_workorder_rate", "requires/iron_output_controller" );
}
	function load_iron_company(source_id)
	{
		if($('#cbo_iron_company option').length==2)
		{
			if($('#cbo_iron_company option:first').val()==0)
			{
				var cbo_iron_company_id=$('#cbo_iron_company option:last').val();
				$('#cbo_iron_company').val(cbo_iron_company_id);
				if(source_id==1)
				{
					load_drop_down( 'requires/iron_output_controller',cbo_iron_company_id, 'load_drop_down_location', 'location_td' );
					fnc_company_check(source_id);
				}
				if(source_id==3)
				{
					fnc_workorder_search(cbo_iron_company_id);
				}
			}
		}
	}

	
	function active_placeholder_qty(color_id) 
	{
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
		$("#txt_iron_qty").val(totalVal);
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left" align="center">
        <fieldset style="width:930px;">
        <legend>Production Module</legend>  
            <form name="ironoutput_1" id="ironoutput_1" autocomplete="off" >
  				<fieldset> 
                    <table width="100%" border="0">
                        <tr>
                            <td width="110" class="must_entry_caption">Company</td>
                            <td>                                
								<?
								echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'load_variable_settings','requires/iron_output_controller');setFieldLevelAccess(this.value)" );
								?>	
                                <input type="hidden" id="sewing_production_variable" />	 
                                <input type="hidden" id="styleOrOrderWisw" /> 
                                <input type="hidden" id="iron_production_variable_rej" />
                                <input type="hidden" id="variable_is_controll" />
                                <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                                
                                <input type="hidden" id="hidden_currency_id" />
                                <input type="hidden" id="hidden_exchange_rate" />
                                <input type="hidden" id="hidden_piece_rate" /> 	
							</td>

							<td width="" class="must_entry_caption">&nbsp;Source</td>
                             <td width="">
								 <?
								 echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/iron_output_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_source', 'iron_company_td' );dynamic_must_entry_caption(this.value);load_iron_company(this.value);", 0, '1,3' );
								 ?>
                             </td>
                          <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
                          <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">

                          <td width="" class="must_entry_caption">Iron Company</td>
                             <td id="iron_company_td" width="170">
								 <?
                                 echo create_drop_down( "cbo_iron_company", 170, $blank_array,"", 1, "-- Select --", $selected, "" );
                                 ?>
						     </td>

						    <tr>

                            <td width="90" class="must_entry_caption">Order No</td>
                            <td width="170">
                                <input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage('requires/iron_output_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value+'&production_company='+document.getElementById('cbo_iron_company').value+'&hidden_variable_cntl='+document.getElementById('hidden_variable_cntl').value+'&hidden_preceding_process='+document.getElementById('hidden_preceding_process').value,'Order Search')"  class="text_boxes" style="width:160px " readonly />
                                <input type="hidden" id="hidden_po_break_down_id" value="" />
							</td>
							 <td width="" id="locations" >Location</td>
                             <td width="" id="location_td">
								 <?
                                 echo create_drop_down( "cbo_location", 172, $blank_array, "", 1, "-- Select Location --", $selected, "" );
                                 ?>
							 </td>
                             <td width="" id="floors" >Floor</td>
                              <td width="" id="floor_td">
								  <? 
                                  echo create_drop_down( "cbo_floor", 170, $blank_array, "",1, "-- Select Floor --", $selected, "" );
                                  ?>
                              </td>

							</tr>
							<tr> 
                            <td width="130" >Country</td>
                            <td width="170">
                                <?
                                    echo create_drop_down( "cbo_country_name", 170, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                ?> 
                            </td>

                             <td width="">Buyer</td>
                            <td width="170">
								<? 
                                echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                ?>
							</td>
                             <td width="100">Job No</td>
                            <td width="170">
                            	<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:160px " disabled readonly>	
							</td>


                        </tr>
                        <tr>    
                           
                            <td width="">Style</td>
                            <td width="">
								<input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:158px " disabled readonly>
							</td>

							<td width=""> Item </td>
                             <td width="170">
								 <?
                                 echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                 ?>
							 </td>  
                             <td width="">Order Qnty</td>
                             <td width="">
							 	<input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:160px "  disabled readonly>
							 </td>


                        </tr>
                        
                        <tr>
							<td>Work Order</td>
							<td  id="workorder_td">
							<?
							echo create_drop_down( "cbo_work_order",170, $blank_array,"", 1, "-- Select Work Order--", $selected, "",0 );	
							?>
							</td>
							<td>Table Name</td>
							<td id="table_td">
								<?php
									echo create_drop_down( 'cbo_table_name', 170, $blank_array, '', 1, '-- Select --', $selected, '', 0 );
									// echo create_drop_down( 'cbo_table_name', 170, "select id, table_name from lib_table_entry where is_deleted=0 and status_active=1 order by table_name", 'id,table_name', 1, '-- Select --', $selected, '', 0 );
								?>
							</td>
							<td>IR/IB</td>
                            <td>
                                <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:160px " disabled readonly>	
							</td>
							<td>&nbsp;</td>
                        </tr>
                    </table>

                </fieldset>
                <br />
                 <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="30%" valign="top">
                            <fieldset>
                            <legend>New Entry</legend>
                                 <table  cellpadding="0" cellspacing="2" width="100%">
                                 	<tr>
                                        <td width="120" class="must_entry_caption">Produced By</td>
                                        <td width="120" colspan="2">
                                            <?
                                                echo create_drop_down( "cbo_produced_by", 110, $worker_type,"", 1, "--Select Type--", 1, "",0 );//check_produced_by(this.value)
                                            ?>
                                        </td>
                                    </tr>
                                    	<tr>
                                            <td width="120">Iron. Output Date</td>
                                             <td width=""> 
                                              	<input name="txt_iron_date" id="txt_iron_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"  />
                                            </td>
                                    	</tr>
                                         <tr>
                                            <td width="">Reporting Hour</td> 
                                            <td width=""> 
                               <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                                              
                                            </td>
                                         </tr> 
                                     <tr>  
                                         <td width="" class="must_entry_caption">Iron. Output Qty</td> 
                                         <td width=""> 
                                            <input name="txt_iron_qty" id="txt_iron_qty" class="text_boxes_numeric"  style="width:100px" readonly  />
                                            <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                            <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                        </td>
                                    </tr>
                                     <tr>  
                                         <td width="">Re-Iron. Qty</td> 
                                         <td width=""> 
                                            <input name="txt_reiron_qty" id="txt_reiron_qty" class="text_boxes_numeric"  style="width:100px" />
                                        </td>
                                    </tr>
                                    <tr>  
                                         <td width="">Reject Qty</td> 
                                         <td width=""> 
                                            <input name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric"  style="width:100px" type="text" />
                                        </td>
                                    </tr>
                                    <tr>
                                         <td width="">Challan No</td> 
                                         <td>
                                           <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" />
                                         </td>
                                    </tr>
                                    <tr>
                                    	<td width="">Remarks</td> 
                                        <td width="" > 
                                        	<input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:100px" />
                                        </td>
                                   </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                        <td width="25%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="140" id="caption_msg_id">Iron Qty</td>
                                        <td>
                                         <input type="text" name="txt_sewing_quantity" id="txt_sewing_quantity" class="text_boxes_numeric" style="width:80px"  readonly disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width=""> Cumul. Iron.  Qty</td>
                                        <td>
                                         <input type="text" name="txt_cumul_iron_qty" id="txt_cumul_iron_qty" class="text_boxes_numeric" style="width:80px"  readonly disabled />
                                        </td>
                                    </tr>
                                     <tr>
                                        <td width="">Yet to Iron Output</td>
                                        <td>
                                         <input type="text" name="txt_yet_to_iron" id="txt_yet_to_iron" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>	
                        </td>
                        <td width="40%" valign="top" >
                            <div style="max-height:300px; overflow-y:auto" id="breakdown_td_id" align="center"></div>
                        </td>
                    </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
							<?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_iron_input", 0, 1,"reset_form('ironoutput_1','list_view_country','','txt_iron_date,".$date."','childFormReset()')",1); 
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
         	<div style="width:930px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:450px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
	<br clear="all">
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>