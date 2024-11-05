<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create garments finish entry
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	27-02-2013
Updated by 		: 	Kausar (Creating Print Report )	
Update date		: 	09-01-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");


//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Packing And Finishing Entry","../../", 1, 1, $unicode,'','');

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";


<?php   
if($_SESSION['logic_erp']['data_arr'][686]){
	echo "var field_level_data= " . json_encode($_SESSION['logic_erp']['data_arr'][686]). ";\n";
}
?>

	function dynamic_must_entry_caption(data)
	{
		if(data==1)
		{
			$('#locations').css('color','blue');
			$('#floors').css('color','blue');
			
			$('#servicewo_td').css('color','black');
			$("#txt_wo_no").val('');
			$("#txt_wo_id").val('');
			$("#txt_wo_no").attr("disabled",true);
		}
		else if(data==3)
		{
			$("#txt_wo_no").val('');
			$("#txt_wo_id").val('');
			$('#locations').css('color','black');
			$('#floors').css('color','black');
			$('#servicewo_td').css('color','blue');
			$("#txt_wo_no").attr("disabled",false);
		}
		else
		{
			$("#txt_wo_no").val('');
			$("#txt_wo_id").val('');
			$('#locations').css('color','black');
			$('#floors').css('color','black');
			$('#servicewo_td').css('color','black');
			$("#txt_wo_no").attr("disabled",true);
		}
	}


 
function openmypage(page_link,title)
{
	if ( form_validation('cbo_company_name*cbo_source*cbo_finish_company','Company Name*Production Source*Production Company')==false )
	{
		return;
	}

	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1260px,height=370px,center=1,resize=0,scrolling=0','../')
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
				
				childFormReset();//child from reset
				// get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
 				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				var txt_job_no=$('#txt_job_no').val();
				var variableSettingsReject=$('#finish_production_variable_rej').val();
				var txt_job_no=$('#txt_job_no').val();
				if(variableSettings!=1){ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/finishing_entry_controller");
					//get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject, "color_and_size_level", "requires/finishing_entry_controller");
				}
				else
				{
					get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
					$("#txt_finishing_qty").removeAttr("readonly");
					get_php_form_data(po_id+'**'+item_id+'**'+txt_job_no, "gross_level_entry", "requires/finishing_entry_controller");
				}
				
				if(variableSettingsReject!=1)
				{
					$("#txt_reject_qnty").attr("readonly");
				}
				else
				{
					$("#txt_reject_qnty").removeAttr("readonly");
				}
				
				var data = po_id+'**'+item_id+'**'+country_id+'**'+txt_job_no+'**'+pack_type;
				$.get("requires/finishing_entry_controller.php?action=show_all_listview&data="+data, function(data, status){
			      // alert("Data: " + data + "\nStatus: " + status);
			      dataEx = data.split('******');
			      document.getElementById('list_view_container').innerHTML=dataEx[0];
			      document.getElementById('list_view_country').innerHTML=dataEx[1];
			    });


				// show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+txt_job_no+'**'+pack_type,'show_dtls_listview','list_view_container','requires/finishing_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
				// show_list_view(po_id,'show_country_listview','list_view_country','requires/finishing_entry_controller','');
				
				set_button_status(0, permission, 'fnc_finishing_entry',1,0);
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled"); 
		}
	}//end else
}//end function

function fnc_finishing_entry(operation)
{
	var source=$("#cbo_source").val();
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title, "finishing_entry_print", "requires/finishing_entry_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if ( form_validation('cbo_company_name*txt_order_no*cbo_finish_company*txt_finishing_date','Company Name*Order No*Finishing Company*Finishing Date')==false )
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


			if('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][686]); ?>') 
			{
				if (form_validation('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][686]); ?>','<? echo implode('*', $_SESSION['logic_erp']['mandatory_message'][686]); ?>')==false) {return;}
			}
			


			if($('#txt_finishing_qty').val()<1 && $('#txt_alter_qnty').val()<1 && $('#txt_spot_qnty').val()<1 && $('#txt_reject_qnty').val()<1)
			{
				alert("Finished quantity or Alter quantity or Spot quantity or Reject quantity should be filled up.");
				return;
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_finishing_date').val(), current_date)==false)
			{
				alert("Finishing Date Can not Be Greater Than Current Date");
				return;
			}	
			freeze_window(operation);			
			var sewing_production_variable = $("#sewing_production_variable").val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var variableSettingsReject=$('#finish_production_variable_rej').val();

			if(sewing_production_variable=="" || sewing_production_variable==0)
			{
 				sewing_production_variable=3;
 				variableSettingsReject=3;
			}
			
			var i=0; var k=0; var colorIDvalue=''; var colorIDvalueRej='';
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
			 
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*finish_production_variable_rej*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_finish_company*cbo_location*cbo_floor*txt_finishing_date*txt_reporting_hour*cbo_produced_by*txt_finishing_qty*txt_carton_qty*txt_alter_qnty*txt_spot_qnty*txt_reject_qnty*txt_challan*txt_remark*txt_finish_input_qty*txt_cumul_finish_qty*txt_yet_to_finish*hidden_break_down_html*txt_mst_id*save_data*defect_type_id*save_dataSpot*defectSpot_type_id*save_dataRej*defectRej_type_id*fabric_data*accessoric_data*emblishment_data*precost_data*txt_material_id*txt_pack_type*txt_wo_id*txt_wo_no',"../../");
			// alert (data);die;
 			
 			http.open("POST","requires/finishing_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_finishing_entry_Reply_info;
		}
	}
}
 

function fnc_finishing_entry_Reply_info()
{
 	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var variableSettingsReject=$('#finish_production_variable_rej').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		var pack_type=$("#txt_pack_type").val();
		
		var reponse=trim(http.responseText).split('**');
		

		if(reponse[0]==786)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		
		if(reponse[0]==25)
		{
			$("#txt_finishing_qty").val("");
			show_msg('28');
			release_freezing();
		}
		if(reponse[0]==35)
		{
			$("#txt_finishing_qty").val("");
			show_msg('25');
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==505)
		{
			alert("Full Shipment Order Can't be saved,updated!!");
			release_freezing();
			return;
		}
        if (reponse[0].length>2) reponse[0]=10;
		
		if(reponse[0]==0)
		{ 
			var po_id = reponse[1];
			show_msg(reponse[0]);
			var txt_job_no=$('#txt_job_no').val();
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+txt_job_no+'**'+pack_type,'show_dtls_listview','list_view_container','requires/finishing_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_finishing_qty*txt_carton_qty*txt_alter_qnty*txt_spot_qnty*txt_reject_qnty*txt_challan*txt_remark*txt_finish_input_qty*txt_cumul_finish_qty*txt_yet_to_finish*hidden_break_down_html*txt_mst_id*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id*save_dataRej*defectRej_type_id*allRej_defect_id','','','txt_finishing_date*txt_reporting_hour');
			// get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
			
				
			release_freezing();
 			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/finishing_entry_controller" ); 
			}
			else
			{
				get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
				$("#txt_finishing_qty").removeAttr("readonly");
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
		if(reponse[0]==1)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			var txt_job_no=$('#txt_job_no').val();
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+txt_job_no+'**'+pack_type,'show_dtls_listview','list_view_container','requires/finishing_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_finishing_qty*txt_carton_qty*txt_alter_qnty*txt_spot_qnty*txt_reject_qnty*txt_challan*txt_remark*txt_finish_input_qty*txt_cumul_finish_qty*txt_yet_to_finish*hidden_break_down_html*txt_mst_id*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id*save_dataRej*defectRej_type_id*allRej_defect_id','','','txt_finishing_date*txt_reporting_hour');
			// get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
			
			release_freezing();
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/finishing_entry_controller" ); 
			}
			else
			{
				get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
				$("#txt_finishing_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}

			set_button_status(0, permission, 'fnc_finishing_entry',1,0);
		}
		if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			var txt_job_no=$('#txt_job_no').val();
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+txt_job_no+'**'+pack_type,'show_dtls_listview','list_view_container','requires/finishing_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_finishing_qty*txt_carton_qty*txt_alter_qnty*txt_spot_qnty*txt_reject_qnty*txt_challan*txt_remark*txt_finish_input_qty*txt_cumul_finish_qty*txt_yet_to_finish*hidden_break_down_html*txt_mst_id*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id*save_dataRej*defectRej_type_id*allRej_defect_id','','','txt_finishing_date*txt_reporting_hour');
			// get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
			
			release_freezing();
			if(variableSettings!=1) { 
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "color_and_size_level", "requires/finishing_entry_controller" ); 
			}
			else
			{
				get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+pack_type, "populate_data_from_search_popup", "requires/finishing_entry_controller" );
				$("#txt_finishing_qty").removeAttr("readonly");
			}
			
			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_finishing_entry',1,0);
		}

		if(reponse[0]==10)
		{
			show_msg(trim(reponse[0]));
			release_freezing();
			return;
		}
		
		if(reponse[0]==420)
		{
			alert("Color Size Breakdown ID Not Found.");
			release_freezing();
			return false;
		}
		
 	}
} 


function childFormReset()
{
	reset_form('','','txt_reporting_hour*txt_finishing_qty*txt_carton_qty*txt_alter_qnty*txt_spot_qnty*txt_reject_qnty*txt_challan*txt_remark*txt_finish_input_qty*txt_cumul_finish_qty*txt_yet_to_finish*hidden_break_down_html*txt_mst_id*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id*save_dataRej*defectRej_type_id*allRej_defect_id','','');
 	$('#txt_cumul_finish_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_finish').attr('placeholder','');//placeholder value initilize
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
    var filed_value_rej = $("#colSizeRej_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
	var hidden_variable_cntl=$('#hidden_variable_cntl').val()*1;
	
	if((filed_value*1)+(filed_value_rej*1) > placeholder_value*1)
	{
		if(hidden_variable_cntl==1 && txt_user_lebel!=2)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+tableName+index).val('');
			$("#txt_finishing_qty").val('');
		}
		else
		{
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )	
			{
				void(0);
			}
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
	var totalfinishAmount = 0;
	$("input[name=colorSize]").each(function(index, element) {
		var color_id=$(this).attr('id').split("_");
		var finish_amount=$("#colorSizefabricRate_"+color_id[1]).val()*( $(this).val() )*1;
		totalfinishAmount+=finish_amount;
        totalVal += ( $(this).val() )*1;
		
    });
	$("#txt_finishing_qty").val(totalVal);
	$("#fabric_data").val(totalfinishAmount);
}

function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val()*1;
	var colsizes= $("#colSize_"+tableName+index).val()*1;
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder')*1;
    if(colsizes=="" && filed_value !="")
    {
    	$("#colSize_"+tableName+index).val(0);
    }

    if(filed_value+colsizes > placeholder_value)
    {
    	alert("Qnty Excceded by"+(placeholder_value-(filed_value+colsizes)));
		$("#colSize_"+tableName+index).val('');
    	$("#colSizeRej_"+tableName+index).val('');
		$("#txt_finishing_qty").val('');
    }
	
	var totalRow = $("#table_"+tableName+" tr").length;
	// alert(tableName);
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
			$("#txt_finishing_qty").val('');
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
	$("#txt_finishing_qty").val( $("#total_color").val() );
	var total_fabric_amount=0;
	for(var j=1;j<=totalRow;j++)
	{
		total_fabric_amount+=($("#colSize_"+j).val()*1)*($("#colorSizefabricRate_"+j).val()*1)
	}
	//alert(total_fabric_amount)
	$("#fabric_data").val(total_fabric_amount);
} 

function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qnty").val( $("#total_color_rej").val() );
}

function fnc_company_check(val)  
{
	if(val==1)
	{
		if($("#cbo_company_name").val()==0)
		{
			alert("Please Select Company.");
			$("#cbo_source").val(0);
			$("#cbo_finish_company").val(0);
			return;
		}
		else
		{
			get_php_form_data(document.getElementById('cbo_finish_company').value,'production_process_control','requires/finishing_entry_controller' );
		}
	}
	else
	{
		get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/finishing_entry_controller' );
	}
}



function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);
	
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
 				
	childFormReset();//child from reset
	// get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/finishing_entry_controller" );
	
	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var variableSettingsReject=$('#finish_production_variable_rej').val();
	var txt_job_no=$('#txt_job_no').val();
	if(variableSettings!=1)
	{ 
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+txt_job_no+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/finishing_entry_controller");
	}
	else
	{
		get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/finishing_entry_controller" );
		$("#txt_finishing_qty").removeAttr("readonly");
	}
	
	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qnty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qnty").removeAttr("readonly");
	}
			
	show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+txt_job_no,'show_dtls_listview','list_view_container','requires/finishing_entry_controller','');
	set_button_status(0, permission, 'fnc_finishing_entry',1,0);
	release_freezing();
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
		else if(type==3)
		{
			var save_data=$('#save_dataRej').val();
			var all_defect_id=$('#allRej_defect_id').val();
			var defect_qty=$('#txt_reject_qnty').val();
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
		else if (type==3)
		{
			defect_qty=$('#txt_reject_qnty').val();
			title = 'Rej Qty Info';
		}
		
		var page_link = 'requires/finishing_entry_controller.php?hidden_po_break_down_id='+hidden_po_break_down_id+'&txt_mst_id='+txt_mst_id+'&save_data='+save_data+'&defect_qty='+defect_qty+'&all_defect_id='+all_defect_id+'&type='+type+'&action=defect_data';
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
			else if(type==3) 
			{
				// alert(type);
				$('#save_dataRej').val(save_string);
				//$('#txt_reject_qnty').val(tot_defectQnty);
				$('#allRej_defect_id').val(all_defect_id);
				$('#defectRej_type_id').val(defect_type_id);
			}
			release_freezing();
		}
	}
}

function openmypage_woNo_13092023()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var cbo_service_company = $('#cbo_finish_company').val()		

		if (form_validation('cbo_company_name*cbo_source*cbo_finish_company','Company*Source*Service Company')==false)
		{
			return;
		}
		else
	  	{			
			if (form_validation('cbo_finish_company','Service Company')==false)
			{
				return;
			}
			
			var page_link='requires/finishing_entry_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&supplier_id='+cbo_service_company+'&action=service_booking_popup';
			var title='WO Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=390px,center=1,resize=1,scrolling=0','../../');
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
	
	function fnc_wo_no()
	{
		if ( form_validation('cbo_company_name*cbo_source*cbo_finish_company','Company Name*Production Source*Finishing Company')==false )
		{
			return;
		}
		else
		{
			var company_id=$("#cbo_company_name").val();
			var service_company_id=$("#cbo_finish_company").val();
			var txt_job_no=$("#txt_job_no").val();
			
			var title = 'Service WO Selection Popup';
			
			var page_link="requires/finishing_entry_controller.php?action=wo_no_popup&company_id="+company_id+'&service_company_id='+service_company_id+'&txt_job_no='+txt_job_no;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=370px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var wodata=this.contentDoc.getElementById("hidden_sys_data").value;
				var exwodata=wodata.split("_");
				
				if(exwodata[0]!="")
				{
					$('#txt_wo_no').val(exwodata[1]);
					$('#txt_wo_id').val(exwodata[0]);
				}
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
 	<div style="width:900px; float:left" align="center">
        <fieldset style="width:930px;">
        <legend>Packing And Finishing Production</legend>  
            <form name="finishingentry_1" id="finishingentry_1" autocomplete="off" >
 				<fieldset>
                <table width="100%" border="0">


                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td><? echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'load_variable_settings','requires/finishing_entry_controller');setFieldLevelAccess(this.value);" ); ?>	 
                            <input type="hidden" id="sewing_production_variable" />	 
                            <input type="hidden" id="styleOrOrderWisw" /> 
                            <input type="hidden" id="finish_production_variable_rej" />
                            <input type="hidden" id="txt_qty_source" />
                            <input type="hidden" id="variable_is_controll" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                        </td>

                         <td class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/finishing_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_source', 'finishing_td' );dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>
                        <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
                         <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">

                        <td class="must_entry_caption">Finish. Company</td>
                        <td id="finishing_td"><? echo create_drop_down( "cbo_finish_company", 170, $blank_array,"", 1, "-Select finishing Company-", $selected, "" );?></td>
                    </tr>

                <tr>
					<td id="locations">Location</td>
					<td id="location_td"><? echo create_drop_down( "cbo_location", 167, $blank_array, "", 1, "-- Select Location --", $selected, "" ); ?></td>
					<td width="100" class="must_entry_caption">Order No</td>
					<td width="175">
					<input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage('requires/finishing_entry_controller.php?action=order_popup&company='+$('#cbo_company_name').val()+'&garments_nature='+$('#garments_nature').val()+'&production_company='+$('#cbo_finish_company').val()+'&hidden_variable_cntl='+$('#hidden_variable_cntl').val()+'&hidden_preceding_process='+$('#hidden_preceding_process').val(),'Order Search')"  class="text_boxes" style="width:155px " readonly />
					<input type="hidden" id="hidden_po_break_down_id" value="" />
					</td>
					<td width="130">Country</td>
					<td width="170"><? echo create_drop_down( "cbo_country_name", 170, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 ); ?> 
					</td>

                	
                </tr>

                 <tr>    
                        <td>Buyer</td>
                        <td><? echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>
                        <td>Job No</td>
                        <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:155px " disabled readonly /></td>
                        <td>Style</td>
                        <td><input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:158px " disabled readonly /></td>
                    </tr>

                     <tr>  
                        <td>Item</td>
                        <td><? echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	?></td>  
                        <td>Order Qnty</td>
                        <td><input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:155px" disabled readonly></td>
                        <td>Pack Type</td>
                        <td><input name="txt_pack_type" id="txt_pack_type" class="text_boxes" style="width:155px " disabled readonly></td>
                    </tr>

                    <tr>
                        <td id="floors">Floor</td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor", 170, $blank_array, "", 1, "-- Select Floor --", $selected, "" ); ?></td>
                        <td id="servicewo_td">Service WO No</td>
                        <td>
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:155px" placeholder="Browse" onDblClick="fnc_wo_no();" readonly disabled />
                            <input type="hidden" name="txt_wo_id" id="txt_wo_id" class="text_boxes" style="width:50px" />
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                </table>
                </fieldset>
                <br />                 
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="35%" valign="top">
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
                                    <td width="90" class="must_entry_caption">Finishing Date</td>
                                    <td width="100" colspan="2">
                                    	<input name="txt_finishing_date" id="txt_finishing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:90px;"  /> </td>
                                </tr>
                                <tr>
                                    <td width="">Reporting Hour</td>
                                    <td colspan="2">  
                                    	<input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:90px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                                    </td>
                                </tr>
                                <tr>
                                    <td >Finishing Qty</td>
                                    <td colspan="2"><input name="txt_finishing_qty" id="txt_finishing_qty" class="text_boxes_numeric"  style="width:90px; text-align:right" readonly />
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Carton Qty</td>
                                    <td colspan="2"><input type="text" name="txt_carton_qty" id="txt_carton_qty" class="text_boxes_numeric"   style="width:90px; text-align:right" /></td>
                                </tr>
                                <tr>
                                    <td>Alter Qty</td>
                                    <td><input type="text" name="txt_alter_qnty" id="txt_alter_qnty" class="text_boxes_numeric" style="width:90px; text-align:right" /></td>
                                    <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Alt Defect" style="width:70px" onClick="openmypage_defectQty(1);"/></td>
                                </tr>
                                <tr>
                                    <td >Spot Qty </td>
                                    <td><input type="text" name="txt_spot_qnty" id="txt_spot_qnty" class="text_boxes_numeric" style="width:90px; text-align:right" /></td>
                                    <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Spt Defect" style="width:70px" onClick="openmypage_defectQty(2);"/></td>
                                </tr>                                     
                                <tr>
                                    <td>Reject Qty</td>
                                    <td><input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:90px; text-align:right" /></td>
                                    <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Rjt Defect" style="width:70px" onClick="openmypage_defectQty(3);"/></td>
                                </tr>
                                <tr>
                                    <td>Challan No</td> 
                                    <td colspan="2">
                                       <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" value="0" style="width:55px" />
                                       Sys. Chln.<input type="text" name="txt_sys_chln" id="txt_sys_chln" class="text_boxes" style="width:45px" placeholder="Display" disabled />
                                    </td>
                               </tr>
                                <tr>
                                    <td>Remarks</td> 
                                    <td colspan="2"> 
                                    <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:170px" />
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
                                    <td width="120"> Iron Qty</td> 
                                    <td><input type="text" name="txt_finish_input_qty" id="txt_finish_input_qty" class="text_boxes_numeric" style="width:80px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Cumul. Finish Qty</td>
                                    <td><input type="text" name="txt_cumul_finish_qty" id="txt_cumul_finish_qty" class="text_boxes_numeric" style="width:80px" disabled /></td>
                                </tr>
                                <tr>
                                    <td width="">Yet to Finish</td>
                                    <td><input type="text" name="txt_yet_to_finish" id="txt_yet_to_finish" class="text_boxes_numeric" style="width:80px" disabled /></td>
                                </tr>
                                
                            </table>
                            </fieldset>
                            <div id="posted_account_td" style=" margin-top:20px; font-size:20px; color:#FF0000"></div>	
                        </td>
                        <td width="43%" valign="top" >
                            <div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                    </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
           				<?
							$date=date('d-m-Y');
							echo load_submit_buttons( $permission, "fnc_finishing_entry", 0,1 ,"reset_form('finishingentry_1','list_view_country','','txt_finishing_date,".$date."','childFormReset()')",1); 
		   				?>
                        <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
                        <input type="hidden" name="txt_material_id" id="txt_material_id" readonly />
                        <input type="hidden" name="save_data" id="save_data" readonly />
                        <input type="hidden" name="all_defect_id" id="all_defect_id" readonly />
                        <input type="hidden" name="defect_type_id" id="defect_type_id" readonly />
                        <input type="hidden" name="save_dataSpot" id="save_dataSpot" readonly />
                        <input type="hidden" name="allSpot_defect_id" id="allSpot_defect_id" readonly />
                        <input type="hidden" name="defectSpot_type_id" id="defectSpot_type_id" readonly />
						<input type="hidden" name="save_dataRej" id="save_dataRej" readonly />
                        <input type="hidden" name="allRej_defect_id" id="allRej_defect_id" readonly />
                        <input type="hidden" name="defectRej_type_id" id="defectRej_type_id" readonly />
                        <input type="hidden" name="fabric_data" id="fabric_data" readonly />
                        <input type="hidden" name="accessoric_data" id="accessoric_data" readonly />
                        <input type="hidden" name="emblishment_data" id="emblishment_data" readonly />
                        <input type="hidden" name="precost_data" id="precost_data" readonly />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
                <div style="width:930px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
        </fieldset>
	</div>
	<!-- <div id="list_view_country" style="width:400px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:40px"></div>         -->
	<div id="list_view_country" style="width:400px; overflow:auto; padding-top:5px; position:absolute; left:950px"></div>        
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>