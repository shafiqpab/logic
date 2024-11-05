<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sewing output

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	09-03-2013
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
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sewing Out Info","../../", 1, 1, $unicode,'','');
$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var str_supervisor = [<? echo substr(return_library_autocomplete( "select distinct(supervisor) as supervisor from pro_garments_production_mst", "supervisor"  ), 0, -1); ?>];

<?php
if($_SESSION['logic_erp']['data_arr'][724]){
	echo "var field_level_data= " . json_encode($_SESSION['logic_erp']['data_arr'][724]). ";\n";
}
?>

function dynamic_must_entry_caption(data)
{
	if(($("#txt_order_no").val()*1)==0)
	{
		alert("Order No is Blank.Please Browse Order No.");
		$("#cbo_source").val(0);
		return;
	}
	
	if(data==1)
	{
		$('#locations').css('color','blue');
		$('#floors').css('color','blue');
		
		$('#servicewo_td').css('color','black');
		$("#txt_wo_no").val('');
		$("#txt_wo_id").val('');
		$("#cbo_sewing_line").val(0);
		$("#txt_wo_no").attr("disabled",true);
		$("#cbo_sewing_line").attr("disabled",false);
	}
	else if(data==3)
	{
		$("#txt_wo_no").val('');
		$("#txt_wo_id").val('');
		$("#cbo_sewing_line").val(0);
		$('#locations').css('color','black');
		$('#floors').css('color','black');
		$('#servicewo_td').css('color','blue');
		$("#txt_wo_no").attr("disabled",false);
		$("#cbo_sewing_line").attr("disabled",true);
	}
	else
	{
		$("#txt_wo_no").val('');
		$("#txt_wo_id").val('');
		$("#cbo_sewing_line").val(0);
		$('#locations').css('color','black');
		$('#floors').css('color','black');
		$('#servicewo_td').css('color','black');
		$("#txt_wo_no").attr("disabled",true);
		$("#cbo_sewing_line").attr("disabled",false);
	}
}


function openmypage(page_link,title)
{
	//	if ( form_validation('cbo_company_name','Company Name')==false )
	//	{
	//		return; load_variable_settings
	//	}
	//	else
	//{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
            var company_id=this.contentDoc.getElementById("hidden_company_id").value;
           // alert(po_id);
			//     load_drop_down( 'requires/sewing_output_controller',company_id, 'load_drop_down_location', 'location_td' );
            get_php_form_data(company_id,'load_variable_settings','requires/sewing_output_controller');
			if (po_id!="")
			{
				//freeze_window(5);

				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				$("#cbo_company_name").val(company_id);

				childFormReset();//child from reset
				get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/sewing_output_controller" );

				var variableSettings=$('#sewing_production_variable').val();
				var variableSettingsReject=$('#sewing_production_variable_rej').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				if(variableSettings!=1){
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/sewing_output_controller" );
					$("#txt_sewing_qty").attr("readonly","readonly");
				}
				else
				{
					$("#txt_sewing_qty").removeAttr("readonly");
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

				show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
				show_list_view(po_id,'show_country_listview','list_view_country','requires/sewing_output_controller','setFilterGrid(\'country_list_search\',-1)');
				load_drop_down( 'requires/sewing_output_controller', po_id, 'load_drop_down_color_type', 'color_type_td');

				set_button_status(0, permission, 'fnc_sewing_output_entry',1,0);
				release_freezing();
			}
			$("#cbo_company_name").attr("disabled","disabled");
		}
//	}//end else
}//end function



function fnc_sewing_output_entry(operation)
{
	var source=$("#cbo_source").val();
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$("#sewing_production_variable").val(), "sewing_output_print", "requires/sewing_output_controller" )
		 return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][500]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][500]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][500]); ?>')==false) {return;}
		}

 		if ( form_validation('cbo_company_name*txt_order_no*cbo_item_name*cbo_sewing_company*txt_sewing_date*txt_reporting_hour*txt_challan','Company Name*Order No*Item Name*Sewing Company*Sewing Date*Reporting Hour*txt_challan')==false )
		{
			return;
		}
		else
		{
			freeze_window(operation);
			if(source==1)
			{
				if ( form_validation('cbo_location*cbo_floor','Location*Floor')==false )
				{
					release_freezing();
					return;
				}

			}
			if($('#txt_sewing_qty').val()<1&&$('#txt_alter_qnty').val()<1&&$('#txt_spot_qnty').val()<1&&$('#txt_reject_qnty').val()<1)
			{
				release_freezing();
				alert("Sewing quantity or Alter quantity or Spot quantity or Reject quantity should be filled up.");
				return;
			}

			if($('#txt_sewing_qty').val()=="")
			{
				release_freezing();
				alert("Sewing quantity can not blank. Please enter break down value.");
				return;
			}			

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_sewing_date').val(), current_date)==false)
			{
				release_freezing();
				alert("Sewing Date Can not Be Greater Than Current Date");
				return;
			}
			if($("#cbo_source").val()==1 && ($("#cbo_sewing_line").val()==0 || $("#cbo_sewing_line").val()=="") )
			{
				release_freezing();
				alert("Please Select Sewing Line");
				return;
			}
			
			var sewing_production_variable = $("#sewing_production_variable").val();
			var variableSettingsReject=$('#sewing_production_variable_rej').val();
			if(sewing_production_variable=="" || sewing_production_variable==0)
			{
 				sewing_production_variable=3;
			}
			
			if(variableSettingsReject=="" || variableSettingsReject==0)
			{
 				variableSettingsReject=3;
			}
			
			var tot_sewing_qty = $('#txt_sewing_qty').val();
			var breakdown_qty = 0;
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
						breakdown_qty += parseInt($(this).val());
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
						breakdown_qty += parseInt($(this).val());
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
			
			if(sewing_production_variable==3 || sewing_production_variable==2)
			{
				if(tot_sewing_qty != breakdown_qty)
				{
					release_freezing();
					alert('Total sewing qty is not equal to break down qty. Sewing Qty='+tot_sewing_qty+', Breakdown Qty='+breakdown_qty);
					// $('#txt_sewing_qty').focus();
					return;
				}
			}
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*sewing_production_variable_rej*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_sewing_company*cbo_location*cbo_floor*txt_sewing_date*cbo_produced_by*cbo_shift_name*cbo_sewing_line*txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_challan*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*prod_reso_allo*txt_spot_qnty*save_data*defect_type_id*save_dataSpot*defectSpot_type_id*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate*cbo_work_order*cbo_color_type*save_dataBack*allBack_defect_id*defectBack_type_id*save_dataWest*allWest_defect_id*defectWest_type_id*save_dataMeasure*allMeasure_defect_id*defectMeasure_type_id*save_dataFront*allFront_defect_id*defectFront_type_id*txt_wo_no*txt_wo_id',"../../");

           

 			http.open("POST","requires/sewing_output_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sewing_output_entry_Reply_info;
		}
	}
}

function fnc_sewing_output_entry_Reply_info()
{
 	if(http.readyState == 4)
	{
		//console.log(http.responseText);
		//release_freezing();
		//alert(http.responseText);
		//return;
		var variableSettings=$('#sewing_production_variable').val();
		var variableSettingsReject=$('#sewing_production_variable_rej').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		var prod_reso_allo=$('#prod_reso_allo').val();

		var reponse=http.responseText.split('**');
		if(reponse[0]==111){
			release_freezing();
			alert('Update not allowed. Barcode Already generated');
			return;
		}else if(reponse[0]==112){
			release_freezing();
			alert('Delete not allowed. Barcode Already generated');
			return;
		}
		else if(reponse[0]==15)
		{
			 setTimeout('fnc_sewing_output_entry('+ reponse[1]+')',8000);
		}
		else if(reponse[0]==101)
		{
			alert("Sorry! This Order Found in Bundle Wise Sewing Output. Bundle List: "+reponse[1]+" Please Go to Bundle Wise Sewing Output Page for Update action");
			release_freezing();
			return false;
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings.");
		}

		else if(reponse[0]==0)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
			//cbo_produced_by*cbo_sewing_line*
			reset_form('','','txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id*save_dataBack*allBack_defect_id*defectBack_type_id*save_dataWest*allWest_defect_id*defectWest_type_id*save_dataMeasure*allMeasure_defect_id*defectMeasure_type_id*save_dataFront*allFront_defect_id*defectFront_type_id','','');// 'txt_sewing_date,<? echo date("d-m-Y"); ?>'
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/sewing_output_controller" );
 			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/sewing_output_controller" );
			}
			else
			{
				$("#txt_sewing_qty").removeAttr("readonly");
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
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
			//cbo_produced_by*cbo_sewing_line*
			reset_form('','','txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id*save_dataBack*allBack_defect_id*defectBack_type_id*save_dataWest*allWest_defect_id*defectWest_type_id*save_dataMeasure*allMeasure_defect_id*defectMeasure_type_id*save_dataFront*allFront_defect_id*defectFront_type_id','txt_challan,0',''); // 'txt_sewing_date,<? echo date("d-m-Y"); ?>*'
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/sewing_output_controller" );
			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/sewing_output_controller" );
			}
			else
			{
				$("#txt_sewing_qty").removeAttr("readonly");
			}

			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}

			set_button_status(0, permission, 'fnc_sewing_output_entry',1,0);
		}
		else if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
			//cbo_produced_by*cbo_sewing_line*
			reset_form('','','txt_reporting_hour*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id*save_dataBack*allBack_defect_id*defectBack_type_id*save_dataWest*allWest_defect_id*defectWest_type_id*save_dataMeasure*allMeasure_defect_id*defectMeasure_type_id*save_dataFront*allFront_defect_id*defectFront_type_id','txt_sewing_date,<? echo date("d-m-Y"); ?>*txt_challan,0','');
			get_php_form_data(po_id+'**'+$('#cbo_item_name').val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/sewing_output_controller" );
			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/sewing_output_controller" );
			}
			else
			{
				$("#txt_sewing_qty").removeAttr("readonly");
			}

			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qnty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qnty").removeAttr("readonly");
			}

			set_button_status(0, permission, 'fnc_sewing_output_entry',1,0);
		}
		else if(reponse[0]==25)
		{
			$("#txt_sewing_qty").val("");
			show_msg('26');
			release_freezing();
		}
		else if(reponse[0]==35)
		{
			$("#txt_sewing_qty").val("");
			//show_msg('25');
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==420)
		{
			alert("Color Size Breakdown ID Not Found.");
			release_freezing();
			return false;
		}
		//$("#cbo_sewing_line").val('');
		//var all_data=document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value;
		//load_drop_down( 'requires/sewing_output_controller',all_data, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );

		release_freezing();
 	}
}

function childFormReset()
{//cbo_produced_by*
	reset_form('','','cbo_sewing_line*txt_super_visor*txt_sewing_qty*txt_reject_qnty*txt_alter_qnty*txt_remark*txt_input_quantity*txt_cumul_sewing_qty*txt_yet_to_sewing*hidden_break_down_html*txt_mst_id*txt_spot_qnty*save_data*defect_type_id*all_defect_id*save_dataSpot*allSpot_defect_id*defectSpot_type_id','','');
 	$('#txt_input_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_sewing_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_sewing').attr('placeholder','');//placeholder value initilize
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
	$("input[name=colorSize]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	$("#txt_sewing_qty").val(totalVal);
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
	$("#txt_reject_qnty").val(totalValRej);
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
	$("#txt_sewing_qty").val( $("#total_color").val() );
}


function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qnty").val( $("#total_color_rej").val() );
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);

	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);

	childFormReset();//child from reset
	get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/sewing_output_controller" );

	var variableSettings=$('#sewing_production_variable').val();
	var variableSettingsReject=$('#sewing_production_variable_rej').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var prod_reso_allo=$('#prod_reso_allo').val();

	if(variableSettings!=1)
	{
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val(), "color_and_size_level", "requires/sewing_output_controller" );
		$("#txt_sewing_qty").attr("readonly","readonly");
	}
	else
	{
		$("#txt_sewing_qty").removeAttr("readonly");
	}

	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qnty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qnty").removeAttr("readonly");
	}

	show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_output_controller','setFilterGrid(\'tbl_list_search\',-1)');
	set_button_status(0, permission, 'fnc_sewing_output_entry',1,0);
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
		$('#cbo_produced_by').val(0);
		return;
	}
	else
	{
		var response=return_global_ajax_value( po_id+'**'+item_id, 'piece_rate_order_cheack', '', 'requires/sewing_output_controller');
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
		else if(type==2) //Spt
		{
			var save_data=$('#save_dataSpot').val();
			var all_defect_id=$('#allSpot_defect_id').val();
			var defect_qty=$('#txt_spot_qnty').val();
		}
		else if(type==3) //Reject hidden
		{
			var save_data=$('#save_dataSpot').val();
			var all_defect_id=$('#allSpot_defect_id').val();
			var defect_qty=$('#txt_spot_qnty').val();
		}
		else if(type==4) //Front part
		{
			var save_data=$('#save_dataFront').val();
			var all_defect_id=$('#allFront_defect_id').val();
			//var defect_qty=$('#txt_spot_qnty').val();
		}
		else if(type==5) //Back
		{
			var save_data=$('#save_dataBack').val();
			var all_defect_id=$('#allBack_defect_id').val();
			//var defect_qty=$('#txt_spot_qnty').val();
		}
		else if(type==6) //West
		{
			var save_data=$('#save_dataWest').val();
			var all_defect_id=$('#allWest_defect_id').val();
			//var defect_qty=$('#txt_spot_qnty').val();
		}
		else if(type==7) //Measure
		{
			var save_data=$('#save_dataMeasure').val();
			var all_defect_id=$('#allMeasure_defect_id').val();
			//var defect_qty=$('#txt_spot_qnty').val();
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

		var page_link = 'requires/sewing_output_controller.php?hidden_po_break_down_id='+hidden_po_break_down_id+'&txt_mst_id='+txt_mst_id+'&save_data='+save_data+'&defect_qty='+defect_qty+'&all_defect_id='+all_defect_id+'&type='+type+'&action=defect_data';
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
			else if(type==2) //Spt
			{
				$('#save_dataSpot').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allSpot_defect_id').val(all_defect_id);
				$('#defectSpot_type_id').val(defect_type_id);
			}
			if(type==4) //Front Back
			{
				$('#save_dataFront').val(save_string);
				//$('#txt_alter_qnty').val(tot_defectQnty);
				$('#allFront_defect_id').val(all_defect_id);
				$('#defectFront_type_id').val(defect_type_id);
			}
			else if(type==5)  // Back
			{
				$('#save_dataBack').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allBack_defect_id').val(all_defect_id);
				$('#defectBack_type_id').val(defect_type_id);
			}
			else if(type==6)  // West
			{
				$('#save_dataWest').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allWest_defect_id').val(all_defect_id);
				$('#defectWest_type_id').val(defect_type_id);
			}
			else if(type==7)  // Measure
			{
				$('#save_dataMeasure').val(save_string);
				//$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allMeasure_defect_id').val(all_defect_id);
				$('#defectMeasure_type_id').val(defect_type_id);
			}
			
			release_freezing();
		}
	}
}

function fnc_workorder_search(supplier_id)
{

	if( form_validation('cbo_company_name*txt_order_no*cbo_sewing_company','Company Name*Order No*Sewing Company')==false )
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
	load_drop_down( 'requires/sewing_output_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' );
	//alert($('#cbo_cutting_company option').size())
}

function fnc_workorder_rate(data,id)
{
	get_php_form_data(data+"_"+id, "populate_workorder_rate", "requires/sewing_output_controller" );
}
function fnc_all_system_id()
{
	var po_id=$("#hidden_po_break_down_id").val();
	 
	var page_link="requires/sewing_output_controller.php?action=all_system_id_popup&po_id="+po_id;
	var title="All Issue Id";
	 
	if(po_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=200px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{

			 
		}
	}
	else
	{
		alert("please browse order no popup!!");
		return;
	}
}

function clockTick() {
  var currentTime = new Date(),
      month = currentTime.getMonth() + 1,
      day = currentTime.getDate(),
      year = currentTime.getFullYear(),
      hours = currentTime.getHours(),
      minutes = currentTime.getMinutes(),
      seconds = currentTime.getSeconds(),
      // text = (month + "/" + day + "/" + year + ' ' + hours + ':' + minutes + ':' + seconds);
      text = (hours + ':' + minutes);
  // here we get the element with the id of "date" and change the content to the text variable we made above
  document.getElementById('txt_reporting_hour').value = text; 
}

// here we run the clockTick function every 1000ms (1 second)
// setInterval(function(){clockTick();}, 1000);
function isNumber(evt) 
{
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

	function fnc_wo_no()
	{
		if ( form_validation('cbo_company_name*cbo_source*cbo_sewing_company','Company Name*Production Source*Sewing Company')==false )
		{
			return;
		}
		else
		{
			var company_id=$("#cbo_company_name").val();
			var service_company_id=$("#cbo_sewing_company").val();
			var txt_job_no=$("#txt_job_no").val();
			
			var title = 'Service WO Selection Popup';
			
			var page_link="requires/sewing_output_controller.php?action=wo_no_popup&company_id="+company_id+'&service_company_id='+service_company_id+'&txt_job_no='+txt_job_no;
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
	<div style="width:950px; float:left" align="center">
        <fieldset style="width:950px;">
        <legend>Production Module</legend>
			<form name="sewingoutput_1" id="sewingoutput_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                        <tr>
                            <td width="102" class="must_entry_caption">Order No</td>
                            <td width="170">
                                <input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage('requires/sewing_output_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')"  class="text_boxes" style="width:90px " readonly><input type="button"   class="formbutton" onClick="fnc_all_system_id();"  style="width: 67px" value="View Sys.Ch">
                                <input type="hidden" id="hidden_po_break_down_id" value="" />
                            </td>
                            <td width="102" class="must_entry_caption">Company</td>
                            <td width="170"><? echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 1 ); ?>
                                <input type="hidden" id="sewing_production_variable" />
                                <input type="hidden" id="styleOrOrderWisw" />
                                <input type="hidden" id="prod_reso_allo" />
                                <input type="hidden" id="sewing_production_variable_rej" />
                                <input type="hidden" id="variable_is_controll" />
                                <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />

                                <input type="hidden" id="hidden_currency_id" />
                                <input type="hidden" id="hidden_exchange_rate" />
                                <input type="hidden" id="hidden_piece_rate" />
                             	<input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
            					<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
                            </td>
                            <td width="102" >Country</td>
                            <td width="170"><? echo create_drop_down( "cbo_country_name", 170, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 ); ?></td>
                        </tr>
                        <tr>
                            <td>Buyer</td>
                            <td><? echo create_drop_down( "cbo_buyer_name", 170, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 ); ?></td>
                            <td>Job No</td>
                            <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:155px " disabled readonly></td>
                            <td>Style</td>
                            <td><input name="txt_style_no" id="txt_style_no" class="text_boxes"  style="width:158px " disabled readonly></td>
                        </tr>
                        <tr>
                        	 <td class="must_entry_caption"> Item </td>
                             <td><? echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 ); ?></td>
                             <td>Order Qty</td>
                             <td><input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:155px " disabled readonly></td>
                             <td class="must_entry_caption">Source</td>
                             <td><? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "dynamic_must_entry_caption(this.value); load_drop_down( 'requires/sewing_output_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_output', 'sew_company_td' );get_php_form_data(this.value,'line_disable_enable','requires/sewing_output_controller');get_php_form_data($('#cbo_company_name').val()+'**'+this.value+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(), 'display_bl_qnty', 'requires/sewing_output_controller');", 0, '1,3' ); ?></td>
                        </tr>
                        <tr>
                         	 <td class="must_entry_caption">Sewing Company</td>
                             <td id="sew_company_td" ><? echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "" ); ?></td>
                             <td id="locations">Location</td>
                             <td id="location_td"><? echo create_drop_down( "cbo_location", 168,$blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                             <td id="floors">Floor</td>
                             <td id="floor_td"><? echo create_drop_down( "cbo_floor", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                         	 <td class="">Work Order</td>
                             <td id="workorder_td"><? echo create_drop_down( "cbo_work_order",170, $blank_array,"", 1, "-- Select Work Order--", $selected, "",0 ); ?></td>
                             <td id="servicewo_td">Service WO No</td>
                                <td>
                                    <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:155px" placeholder="Browse" onDblClick="fnc_wo_no();" readonly disabled />
                                    <input type="hidden" name="txt_wo_id" id="txt_wo_id" class="text_boxes" style="width:50px" />
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
                                        <td width="120" class="must_entry_caption">Produced By</td>
                                        <td width="120" colspan="2"><? echo create_drop_down( "cbo_produced_by", 110, $worker_type,"", 1, "--Select Type--", 1, "",0 );//check_produced_by(this.value) ?></td>
                                    </tr>
                                    <tr>
                                    	<?
                                    	$sql=sql_select( "SELECT id, shift_name from shift_duration_entry where status_active=1 and production_type=3 order by shift_name");
                                    	$shift_arr = array();
                                    	foreach ($sql as $val) 
                                    	{
                                    		$shift_arr[$val['SHIFT_NAME']]  = $shift_name[$val['SHIFT_NAME']];
                                    	}
                                    	?>
                                        <td width="120" class="">Shift Name</td>
                                        <td width="120" colspan="2"><? echo create_drop_down( "cbo_shift_name", 110, $shift_arr,"", 1, "-- Select --", $selected, "",0 );?></td>
                                    </tr>
                                    <tr>
                                    	<td class="must_entry_caption">Sewing Date</td>
                                         <td colspan="2">
                                         	<input name="txt_sewing_date" id="txt_sewing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"  onChange="load_drop_down( 'requires/sewing_output_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );" />
                                        </td>
                                     </tr>
                                     <tr>
                                        <td class="must_entry_caption">Sewing Line No</td>
                                        <td id="sewing_line_td" colspan="2"><? echo create_drop_down( "cbo_sewing_line", 110, $blank_array,"", 1, "Select Line", $selected, "",1,0 ); ?> </td>
                                   </tr>
                                   <tr>
                                        <td class="">Color Type</td>
                                        <td id="color_type_td"><? echo create_drop_down( "cbo_color_type", 110, $blank_array,"", 1, "Select Type", $selected, "",1,0 ); ?></td> 
                                        <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Front Check" style="width:70px" onClick="openmypage_defectQty(4);"/></td>
                                   </tr>
                                     <tr>
                                       <td class="must_entry_caption">Reporting Hour</td>
                                       <td><input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" value="<?= date('H:i',time());?>" /></td> <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Back Check" style="width:70px" onClick="openmypage_defectQty(5);"/></td>
                                     </tr>
                                   <tr>
                                     <td>Supervisor</td>
                                     <td><input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();"  style="width:100px"></td>
                                    <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Westband Check" style="width:70px" onClick="openmypage_defectQty(6);"/></td>
                                   </tr>
                                   <tr>
                                        <td>QC Pass Qty</td>
                                        <td valign="top">
                                        	<input name="txt_sewing_qty" id="txt_sewing_qty" class="text_boxes_numeric"  style="width:100px" readonly >
                                            <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                            <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                        </td>
                                         <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="MEASUREMENT" style="width:70px" onClick="openmypage_defectQty(7);"/></td>
                                   </tr>
                                   <tr>
                                     <td>Alter Qty </td>
                                     <td><input type="text" name="txt_alter_qnty" id="txt_alter_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                     <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Alt Defect" style="width:70px" onClick="openmypage_defectQty(1);"/></td>
                                   </tr>
                                   <tr>
                                     <td >Spot Qty </td>
                                     <td><input type="text" name="txt_spot_qnty" id="txt_spot_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
                                     <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Spt Defect" style="width:70px" onClick="openmypage_defectQty(2);"/></td>
                                   </tr>
                                   <tr>
                                     <td>Reject Qty</td>
                                     <td><input type="text" name="txt_reject_qnty" id="txt_reject_qnty" class="text_boxes_numeric" style="width:100px;" readonly /></td>
                                     <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="Rjt Defect" style="width:70px" onClick="openmypage_defectQty(3);"/></td>
                                   </tr>
                                   <tr>
                                     <td class="must_entry_caption">Challan No</td>
                                     <td colspan="2">
                                       <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" value="0" style="width:55px" />
                                       Sys. Chln.<input type="text" name="txt_sys_chln" id="txt_sys_chln" class="text_boxes" style="width:45px" placeholder="Display" disabled />
                                     </td>
                                   </tr>
                                   <tr>
                                     <td>Remarks</td>
                                     <td colspan="2"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:165px;" /></td>
                                   </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top">
                        </td>
                         <td width="22%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table cellpadding="0" cellspacing="2" width="100%" >
                                    <tr>
                                        <td width="110">Input Quantity</td>
                                        <td><input type="text" name="txt_input_quantity" id="txt_input_quantity" class="text_boxes_numeric" style="width:80px" readonly disabled  /></td>
                                    </tr>
                                    <tr>
                                        <td width="110">Cumul. Sew. Qty</td>
                                        <td><input type="text" name="txt_cumul_sewing_qty" id="txt_cumul_sewing_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /></td>
                                    </tr>
                                     <tr>
                                        <td width="110">Yet to Sewing</td>
                                        <td><input type="text" name="txt_yet_to_sewing" id="txt_yet_to_sewing" class="text_boxes_numeric" style="width:80px" / readonly disabled ></td>
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
								echo load_submit_buttons( $permission, "fnc_sewing_output_entry", 0,1,"reset_form('sewingoutput_1','list_view_country','','txt_sewing_date,".$date."*txt_challan,0','childFormReset()')",1);
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
                            <input type="hidden" name="save_data" id="save_data" readonly />
                            <input type="hidden" name="all_defect_id" id="all_defect_id" readonly />
                            <input type="hidden" name="defect_type_id" id="defect_type_id" readonly />
                            <input type="hidden" name="save_dataSpot" id="save_dataSpot" readonly />
                            <input type="hidden" name="allSpot_defect_id" id="allSpot_defect_id" readonly />
                            <input type="hidden" name="defectSpot_type_id" id="defectSpot_type_id" readonly />
                            
                            <input type="hidden" name="save_dataFront" id="save_dataFront" readonly />
                            <input type="hidden" name="allFront_defect_id" id="allFront_defect_id" readonly />
                            <input type="hidden" name="defectFront_type_id" id="defectFront_type_id" readonly />
                            
                            <input type="hidden" name="save_dataBack" id="save_dataBack" readonly />
                            <input type="hidden" name="allBack_defect_id" id="allBack_defect_id" readonly />
                            <input type="hidden" name="defectBack_type_id" id="defectBack_type_id" readonly />
                            
                            <input type="hidden" name="save_dataWest" id="save_dataWest" readonly />
                            <input type="hidden" name="allWest_defect_id" id="allWest_defect_id" readonly />
                            <input type="hidden" name="defectWest_type_id" id="defectWest_type_id" readonly />
                            
                            <input type="hidden" name="save_dataMeasure" id="save_dataMeasure" readonly />
                            <input type="hidden" name="allMeasure_defect_id" id="allMeasure_defect_id" readonly />
                            <input type="hidden" name="defectMeasure_type_id" id="defectMeasure_type_id" readonly />
                            
           				</td>
           				<td>&nbsp;</td>
		  			</tr>
                </table>
            </form>
        	</fieldset>
            <div style="width:1050px; margin-top:5px;"  id="list_view_container" align="center"></div>
        </div>
		<div id="list_view_country" style="width:375px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:absolute;left: 970px;"></div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
