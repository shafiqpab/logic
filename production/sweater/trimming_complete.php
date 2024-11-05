<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry

Functionality	:
JS Functions	:
Created by		:	Helal
Creation date 	: 	01-10-2020
Updated by 		: 	
Update date		: 	
Purpose			: 	
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
echo load_html_head_contents("Cutting Info","../../", 1, 1, $unicode,'','');

?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


<?php
if($_SESSION['logic_erp']['data_arr'][683]){
	echo "var field_level_data= " . json_encode($_SESSION['logic_erp']['data_arr'][683]). ";\n";
}
?>

 
function fnc_company_check(val)
{
	if(val==1)
	{
		if($("#cbo_company_name").val()==0)
		{
			alert("Please Select Company.");
			$("#cbo_source").val(0);
			$("#cbo_company_name").val(0);
			return;
		}
		else
		{
			get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/trimming_complete_controller' );
		}
	}
	else
	{
		get_php_form_data(document.getElementById('cbo_company_name').value,'production_process_control','requires/trimming_complete_controller' );
	}
}

function dynamic_must_entry_caption(data)
{
 	if(data==1)
	{
		$('#locations').css('color','blue');
		$('#floors').css('color','blue');
		
		$('#servicewo_td').css('color','black');
		$("#txt_wo_no").val('');
		$("#txt_wo_id").val('');
		$("#cbo_poly_line").val(0);
		$("#txt_wo_no").attr("disabled",true);
		$("#cbo_poly_line").attr("disabled",false);
	}
	else if(data==3)
	{
		$('#locations').css('color','black');
		$('#floors').css('color','black');
		
		$("#txt_wo_no").val('');
		$("#txt_wo_id").val('');
		$("#cbo_poly_line").val(0);
		$('#servicewo_td').css('color','blue');
		$("#txt_wo_no").attr("disabled",false);
		$("#cbo_poly_line").attr("disabled",true);
	}
	else
	{
		$("#txt_wo_no").val('');
		$("#txt_wo_id").val('');
		$("#cbo_poly_line").val(0);
		$('#locations').css('color','black');
		$('#floors').css('color','black');
		$('#servicewo_td').css('color','black');
		$("#txt_wo_no").attr("disabled",true);
		$("#cbo_poly_line").attr("disabled",false);
	}

}




function openmypage(page_link,title)
{
	//var company = $("#cbo_company_name").val();
	/*if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}*/
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id

		var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
		var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
		var trim_qnty=this.contentDoc.getElementById("hidden_plancut_qnty").value;
		var country_id=this.contentDoc.getElementById("hidden_country_id").value;
		var job_num=this.contentDoc.getElementById("hid_job_num").value;
		var company_id=this.contentDoc.getElementById("hid_company_id").value;

		if (po_id!="")
		{
			freeze_window(5);
			$("#cbo_item_name").val(item_id);
			$("#txt_order_qty").val(po_qnty);
			$("#txt_trim_qty").val(trim_qnty);
			$("#cbo_country_name").val(country_id);
			$("#hid_job_num").val(job_num);
			//$("#txt_job_no").val(job_num);
			$("#cbo_company_name").val(company_id);
			$("#hidden_po_break_down_id").val(po_id);
			fnc_company_check(3);

	//			load_drop_down( 'requires/trimming_complete_controller',company_id, 'load_drop_down_location', 'location_td' );
			get_php_form_data(company_id,'load_variable_settings','requires/trimming_complete_controller');
			get_php_form_data(company_id,'load_variable_settings_reject','requires/trimming_complete_controller');
			console.log('variable_reject');
			childFormReset();//child from reset
			console.log('reset');
			get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/trimming_complete_controller" );
			console.log('search_popup');
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			var variableSettingsReject=$('#cutting_production_variable_reject').val();
			var garments_nature=$('#garments_nature').val();

			console.log(variableSettings);

			if(variableSettings!=1)
			{
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level", "requires/trimming_complete_controller" );
			}
			else
			{
				$("#txt_trim_qty").removeAttr("readonly");
			}

			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qty").removeAttr("readonly");
			}

			show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/trimming_complete_controller','setFilterGrid(\'tbl_list_search\',-1)');
			show_list_view(po_id,'show_country_listview','list_view_country','requires/trimming_complete_controller','');
 			set_button_status(0, permission, 'fnc_cutting_update_entry',0);
 			load_drop_down( 'requires/trimming_complete_controller', po_id, 'load_drop_down_color_type', 'color_type_td');
 				release_freezing();
 			
		}
		$("#cbo_company_name").attr("disabled","disabled");
	}
}



function fnc_cutting_update_entry(operation)
{
	/*if(operation==2)
	{
		show_msg('13');
		return;
	}*/
	var source=$("#cbo_source").val();

	if ( form_validation('cbo_company_name*txt_order_no*cbo_cutting_company*txt_trim_date','Company Name*Order No*Cutting Comapny*Trim Date')==false )
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

			if('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][683]);?>')
			{
				if (form_validation('<? echo implode('*', $_SESSION['logic_erp']['mandatory_field'][683]);?>','<? echo implode('*', $_SESSION['logic_erp']['mandatory_message'][683]);?>')==false)
				{ 
					release_freezing();
					return;
				}
			}

			if($('#txt_trim_qty').val()<1&&$('#txt_reject_qty').val()<1)
			{
				alert("Trim quantity Or Reject quantity should be filled up.");
				return;
			}

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_trim_date').val(), current_date)==false)
			{
				alert("Trim Date Can not Be Greater Than Today");
				return;
			}
			var sewing_production_variable = $("#sewing_production_variable").val();
			var variableSettingsReject=$('#cutting_production_variable_reject').val();
			if(sewing_production_variable=="" || sewing_production_variable==0)
			{
 				sewing_production_variable=3;
 				variableSettingsReject=3;
			}

			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var i=0; var k=0; var colorIDvalue=''; var colorIDvalueRej='';
			if(sewing_production_variable==2)//color level
			{
				$("input[name=txt_color]").each(function(index, element) {
 					if( $(this).val()!='' )
					{
						/*if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{
							colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
						}*/

						if(colorIDvalue=="") colorIDvalue = colorList[i]+"*"+$(this).val(); else colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
					}
					i++;
				});
			}
			else if(sewing_production_variable==3)//color and size level
			{
				$("input[name=colorSize]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						/*if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{

							colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
						}*/

						if(colorIDvalue=="") colorIDvalue = colorList[i]+"*"+$(this).val(); else colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
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

			/*var bundle_mst_data="";
			var bundle_dtls_data="";
			$("input[name=bundlemst]").each(function(index, element) {

				if( $(this).val()!='' )
				{
					if(bundle_mst_data=="")
						bundle_mst_data = $(this).val();
					else
						bundle_mst_data += "*****"+$(this).val();
				}

			});
			$("input[name=bundledtls]").each(function(index, element) {

				if( $(this).val()!='' )
				{
					if(bundle_dtls_data=="")
						bundle_dtls_data = $(this).val();
					else
						bundle_dtls_data += "*****"+$(this).val();
				}
			});*/
			//+'&bundle_mst_data='+bundle_mst_data+'&bundle_dtls_data='+bundle_dtls_data


			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*cutting_production_variable_reject*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_cutting_company*cbo_location*cbo_floor*txt_trim_date*txt_reporting_hour*txt_trim_qty*txt_reject_qty*txt_challan_no*txt_remark*txt_cumul_cutting*txt_yet_cut*hidden_break_down_html*txt_mst_id*hid_job_num*hidden_currency_id*hidden_exchange_rate*hidden_piece_rate*cbo_color_type*txt_super_visor*defectSpot_type_id*allSpot_defect_id*save_dataSpot*defect_type_id*all_defect_id*save_data*txt_alter_qnty*txt_spot_qnty*cbo_poly_line*txt_wo_no*txt_wo_id',"../../");
			//alert(data); return;
 			freeze_window(operation);
 			http.open("POST","requires/trimming_complete_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = cutting_update_entry_Reply_info;
		}
}

function cutting_update_entry_Reply_info()
{
 	if(http.readyState == 4)
	{
		//alert(http.responseText);
		console.log(http.responseText);
		//release_freezing();
		//return;
		var variableSettings=$('#sewing_production_variable').val();
		var variableSettingsReject=$('#cutting_production_variable_reject').val();
		var garments_nature=$('#garments_nature').val();
		var job_num=$("#hid_job_num").val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id = $("#cbo_item_name").val();
		var country_id = $("#cbo_country_name").val();

		var reponse=http.responseText.split('**');

		if(reponse[0]==169)
		{
			alert("Delete Not Allowed");

		}
		else if(reponse[0]==15)
		{
			 setTimeout('fnc_cutting_update_entry('+ reponse[1]+')',8000);
		}
		else if(reponse[0]==167)
		{
			alert("Data found in next process for this PO/Item/Country");
		}
		else if(reponse[0]==168)
		{
			alert("Cutting quantity is not less than sewing quantity");
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings.");
		}
		else if(reponse[0]==0)//insert
		{
			var po_id = reponse[1];

			//alert(reponse[0]+'_'+po_id);

			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/trimming_complete_controller','setFilterGrid(\'tbl_list_search\',-1)');
			 	//cbo_produced_by*
			reset_form('','','txt_trim_qty*txt_reject_qty*txt_alter_qnty*txt_spot_qnty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_mst_id','','','txt_trim_date*txt_reporting_hour');
 			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/trimming_complete_controller" );

			if(variableSettings!=1) {

				var variableSettingsReject=$('#cutting_production_variable_reject').val();
				var garments_nature=$('#garments_nature').val();
				var job_num=$("#hid_job_num").val();

				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level", "requires/trimming_complete_controller" );
			}
			else
			{
				$("#txt_trim_qty").removeAttr("readonly");
			}

			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qty").removeAttr("readonly");
			}

			$('#txt_trim_qty').attr('placeholder','');
		}
		else if(reponse[0]==1)//update
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/trimming_complete_controller','setFilterGrid(\'tbl_list_search\',-1)');
			//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
			//cbo_produced_by*
			reset_form('','','txt_trim_qty*txt_reject_qty*txt_alter_qnty*txt_spot_qnty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_mst_id','','','txt_trim_date*txt_reporting_hour');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/trimming_complete_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level", "requires/trimming_complete_controller" );
			}
			else
			{
				$("#txt_trim_qty").removeAttr("readonly");
			}

			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qty").removeAttr("readonly");
			}

			$('#txt_trim_qty').attr('placeholder','');
			set_button_status(0, permission, 'fnc_cutting_update_entry',0);
		}
		else if(reponse[0]==2)//delete
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/trimming_complete_controller','setFilterGrid(\'tbl_list_search\',-1)');
			//cbo_produced_by*
			reset_form('','','txt_reporting_hour*txt_trim_qty*txt_reject_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_mst_id','txt_trim_date,<? echo date("d-m-Y"); ?>','');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/trimming_complete_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level", "requires/trimming_complete_controller" );
			}
			else
			{
				$("#txt_trim_qty").removeAttr("readonly");
			}

			if(variableSettingsReject!=1)
			{
				$("#txt_reject_qty").attr("readonly");
			}
			else
			{
				$("#txt_reject_qty").removeAttr("readonly");
			}

 			set_button_status(0, permission, 'fnc_cutting_update_entry',0);
 			window.location.reload();
		}
		release_freezing();

 	}
}


function childFormReset()
{//cbo_produced_by*
	reset_form('','cutting_production_list_view','txt_reporting_hour*txt_challan_no*txt_trim_qty*txt_reject_qty*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cumul_cutting*txt_yet_cut*txt_mst_id','','');
	$('#txt_trim_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_cutting').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_cut').attr('placeholder','');//placeholder value initilize
	$("#cutting_production_list_view").html('');
	$("#breakdown_td_id").html('');
}


function fn_hour_check(val)
{

	var hours = $("#txt_reporting_hour").val();
	var hoursArr = hours.split(".");
  	if( hoursArr[1] ) {
 		$("#txt_reporting_hour").val(hoursArr[0]);
		return;
	}

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
	var variable_is_controll=$('#hidden_variable_cntl').val();
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
	$("#txt_trim_qty").val(totalVal);
}

function fn_total_rej(tableName,index) // for color and size level
{
    var filed_value = $("#colSizeRej_"+tableName+index).val();
    var colsizes= $("#colSize_"+tableName+index).val();
    if(colsizes=="" && filed_value !="")
    {
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
	$("#txt_trim_qty").val( $("#total_color").val() );
}

function fn_colorRej_total(index) //for color level
{
	var filed_value = $("#colSizeRej_"+index).val();
    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color_rej", "colSizeRej_", "+", totalRow);
	$("#txt_reject_qty").val( $("#total_color_rej").val() );
}


function openmypage_bandle( row_id, field_id, colsize)
{
	var txt_mst_id=$("#txt_mst_id").val();
	var company_name=$("#cbo_company_name").val();
	var hid_job_num=$('#hid_job_num').val();
	var hidden_po_break_down_id=$('#hidden_po_break_down_id').val();

	if(txt_mst_id=='')
	{
		alert('Please Save First.');
		return;
	}

	if($('#colSize_'+field_id).val()=="" ||$('#colSize_'+field_id).val()==0)
	{
		alert('Please Input Some Quantity.');
		return;
	}
	var ext_data=$('#cbo_buyer_name').val()+"__"+$('#txt_style_no').val()+"__"+$('#cbo_item_name').val()+"__"+$('#txt_cutting_date').val()+"__"+$('#hid_job_num').val()+"__"+$('#txt_style_no').val();
	//+'&bundle_mst='+$('#bundle_mst_'+field_id).val()+'&bundle_dtls='+$('#bundle_dtls_'+field_id).val()
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/trimming_complete_controller.php?action=bundle_preparation&cutting_qnty='+$('#colSize_'+field_id).val()+'&fld_id='+field_id+'&colsize='+colsize+'&row_id='+row_id+'&ext_data='+ext_data+'&txt_mst_id='+txt_mst_id+'&company_name='+company_name+'&hid_job_num='+hid_job_num+'&hidden_po_break_down_id='+hidden_po_break_down_id,'Bundle Preparation', 'width=900px,height=350px,center=1,resize=1,scrolling=0','');

	/*emailwindow.onclose=function()
	{
		var txt_details_row=this.contentDoc.getElementById("txt_details_row");
		var mst_info=this.contentDoc.getElementById("mst_info");

		$('#bundle_mst_'+field_id).val(mst_info.value);
		$('#bundle_dtls_'+field_id).val(txt_details_row.value);
		release_freezing();
	}*/
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);

	childFormReset();//child from reset
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#txt_plancut_qty").val(plan_qnty);
	$("#cbo_country_name").val(country_id);

	get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/trimming_complete_controller" );
	var variableSettings=$('#sewing_production_variable').val();
	var variableSettingsReject=$('#cutting_production_variable_reject').val();
	var garments_nature=$('#garments_nature').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var job_num=$("#hid_job_num").val();

	if(variableSettings!=1)
	{
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+job_num+'**'+variableSettingsReject+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level","requires/trimming_complete_controller");
	}
	else
	{
		$("#txt_trim_qty").removeAttr("readonly");
	}

	if(variableSettingsReject!=1)
	{
		$("#txt_reject_qty").attr("readonly");
	}
	else
	{
		$("#txt_reject_qty").removeAttr("readonly");
	}

	show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','cutting_production_list_view','requires/trimming_complete_controller','');
	set_button_status(0, permission, 'fnc_cutting_update_entry',0);
	release_freezing();
}

function show_report( str )
{
	if(str==1)
	 	return_ajax_request_value(1, "print_report_bundle_barcode", "requires/trimming_complete_controller");
	else
		return_ajax_request_value(1, "print_report_operation_barcode", "requires/trimming_complete_controller");

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
		
		var page_link = 'requires/trimming_complete_controller.php?hidden_po_break_down_id='+hidden_po_break_down_id+'&txt_mst_id='+txt_mst_id+'&save_data='+save_data+'&defect_qty='+defect_qty+'&all_defect_id='+all_defect_id+'&type='+type+'&action=defect_data';
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
				$('#txt_alter_qnty').val(tot_defectQnty);
				$('#all_defect_id').val(all_defect_id);
				$('#defect_type_id').val(defect_type_id);
			}
			else if(type==2) 
			{
				$('#save_dataSpot').val(save_string);
				$('#txt_spot_qnty').val(tot_defectQnty);
				$('#allSpot_defect_id').val(all_defect_id);
				$('#defectSpot_type_id').val(defect_type_id);
			}
			release_freezing();
		}
	}
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
		$('#cbo_produced_type').val(0);
		return;
	}
	else
	{
		var response=return_global_ajax_value( po_id+'**'+item_id, 'piece_rate_order_cheack', '', 'requires/trimming_complete_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			if (response[2]>=order_qty)
			{
				if(val==1)
				{
					alert ("This Order Fully Produced By Piece Rate Worker But Selected Salary Based. Plese Check Piece Rate WO No :-"+response[1]);
					$('#cbo_produced_type').val(2);
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
							$('#cbo_produced_type').val(2);
						}
						else
						{
							$('#cbo_produced_type').val(1);
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
				$('#cbo_produced_type').val(1);
			}
		}
	}
}

function fnc_workorder_search(supplier_id)
{

	if( form_validation('cbo_company_name*txt_order_no*cbo_cutting_company','Company Name*Order No*Cutt. Company')==false )
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
	load_drop_down( 'requires/trimming_complete_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' );
	//alert($('#cbo_cutting_company option').size())
}

function fnc_workorder_rate(data,id)
{
	get_php_form_data(data+"_"+id, "populate_workorder_rate", "requires/trimming_complete_controller" );
}

function show_print_report(type,title)
{
	if($('#txt_mst_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}
	else
	{
		var report_title='Cutting Reject Challan';
		var data= $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$('#cbo_location').val()+'*'+$('#cbo_source').val()+'*'+$('#cbo_cutting_company').val()+'*'+$('#txt_cutting_date').val()+'*'+$('#cbo_item_name').val()+'*'+$('#txt_order_no').val()+'*'+$('#cbo_buyer_name').val()+'*'+$('#hid_job_num').val()+'*'+$('#txt_style_no').val()+'*'+$('#txt_remark').val()+'*'+$('#txt_challan_no').val();
		var action='cutting_reject_challan&type='+type+'&title='+title;
		window.open("requires/trimming_complete_controller.php?data=" + data+'&action='+action, true );
                return;
	}
}

function fn_chk_next_process_qty(tableName,index,sizeId) // for color and size level
{
	// alert('ok');return;
	var data="action=chk_next_process_qty&colorId="+tableName+"&sizeId="+sizeId+get_submitted_data_string('cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_item_name',"../../");
	//alert(data); return;
	var filed_value = $("#colSize_"+tableName+index).val()*1;
	var prev_value = $("#colSizeUpQty_"+tableName+index).val()*1;
	$.ajax({
		url: 'requires/trimming_complete_controller.php',
		type: 'POST',
		data: data,
		success: function(response)
		{
			var resData = trim(response).split("****");
			var cutQty = resData[0];
			var inputQty = resData[1];
			if((filed_value+(cutQty-prev_value))*1 < inputQty*1)
			{	
				alert('Sorry! Cutting qnty will not less than input qnty');			
				$("#colSize_"+tableName+index).val(prev_value);		 		
			}
		}
	});
}

	function fnc_wo_no()
	{
		if ( form_validation('cbo_company_name*cbo_source*cbo_cutting_company','Company Name*Production Source*Trim. Company')==false )
		{
			return;
		}
		else
		{
			var company_id=$("#cbo_company_name").val();
			var service_company_id=$("#cbo_cutting_company").val();
			var txt_job_no=$("#hid_job_num").val();
			
			var title = 'Service WO Selection Popup';
			
			var page_link="requires/trimming_complete_controller.php?action=wo_no_popup&company_id="+company_id+'&service_company_id='+service_company_id+'&txt_job_no='+txt_job_no;
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
        <div style="width:850px;" align="center">
             <? echo load_freeze_divs ("../../",$permission); ?>
        </div>
        <div style="width:1030px; float:left" align="center">
            <fieldset style="width:1030px">
            <legend>Production Module</legend>
            	<form name="cuttingupdate_1" id="cuttingupdate_1" action=""  autocomplete="off">
                    <fieldset>
                        <table width="100%">

                            <tr>
                             	<input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
            					<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
								 <td width="100" onClick="show_report(2)" class="must_entry_caption">Order No</td>
								 <td width="150">
									 <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/trimming_complete_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:130px " readonly />
									 <input type="hidden" id="hidden_po_break_down_id" value="" />
									 <input type="hidden" id="hid_job_num" value="" />
								 </td>
<!--                                   <td width="130" onClick="show_report(1)" class="must_entry_caption">Company </td>-->

                                   <td width="100"   class="must_entry_caption">Company </td>

                                    <td width="150">
                                        <?
                                            echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, " get_php_form_data(this.value,'load_variable_settings','requires/trimming_complete_controller'); get_php_form_data(this.value,'load_variable_settings_reject','requires/trimming_complete_controller');",1 );
                                        ?>
                                        <input type="hidden" id="sewing_production_variable" />
                                        <input type="hidden" id="styleOrOrderWisw" />
                                         <input type="hidden" id="prod_reso_allo" />
                                        <input type="hidden" id="cutting_production_variable_reject" />
                                        <input type="hidden" id="sewing_production_variable" />
                                        <input type="hidden" id="styleOrOrderWisw" />
                                        <input type="hidden" id="cutting_production_variable_reject" />
            							<input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />

                                        <input type="hidden" id="hidden_currency_id" />
                                        <input type="hidden" id="hidden_exchange_rate" />
                                        <input type="hidden" id="hidden_piece_rate" />
                                    </td>

                                    <td width="100" >Country</td>
                                    <td width="150">
                                        <?
                                            echo create_drop_down( "cbo_country_name", 140, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                        ?>
                                    </td>
                                    <td width="100">Buyer Name</td>
                                    <td width="150" id="buyer_td">
                                        <?
                                            echo create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
                                        ?>
                                     </td>
                              </tr>
                              <tr>
                                    <td width="100">Job No</td>
                                     <td width="150">
                                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:130px" disabled readonly />
                                     </td>
                                     <td width="100">Style</td>
                                     <td width="150">
                                        <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:130px" disabled readonly />
                                     </td>
                                     <td width="100"> Item </td>
                                     <td width="150">
                                          <?
                                            echo create_drop_down( "cbo_item_name", 140, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );
                                          ?>
                                     </td>
                                     <td width="100">Order Qnty</td>
                                     <td width="150">
                                        <input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:130px" disabled readonly  />
                                     </td>
                              </tr>
                             
                              <tr>
                              	 	<td width="100" class="must_entry_caption">Source</td>
                                     <td width="150">
                                         <?
                                            echo create_drop_down( "cbo_source", 140, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/trimming_complete_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_cutt_company', 'cutt_company_td' );dynamic_must_entry_caption(this.value);",0,'1,3' );
                                         ?>
                                     </td>
                                     <td width="100" class="must_entry_caption">Trim.  Company</td>
                                     <td width="150" id="cutt_company_td">
                                         <?
                                            echo create_drop_down( "cbo_cutting_company", 140, $blank_array,"", 1, "--- Select Trimming Company ---", $selected, "",0 );
                                         ?>
                                     </td>
                                     <td width="100" id="locations">Location</td>
                                     <td width="150" id="location_td">
                                         <?
                                         echo create_drop_down( "cbo_location", 140, $blank_array,"", 1, "-- Select Location --", $selected, "",0 );
                                         ?>
                                     </td>
                                     <td width="100" id="floors">Floor</td>
                                     <td width="150" id="floor_td">
                                         <?
                                         echo create_drop_down( "cbo_floor", 140, $blank_array,"", 1, "-- Select Floor--", $selected, "",0 );
                                         ?>
                                     </td>
                              </tr>
                              <tr>
                                <td id="servicewo_td">Service WO No</td>
                                <td>
                                    <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="fnc_wo_no();" readonly disabled />
                                    <input type="hidden" name="txt_wo_id" id="txt_wo_id" class="text_boxes" style="width:50px" />
                                </td>
                                <td>Remarks</td>
                                <td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:382px" /></td>
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
		                                    	<td class="must_entry_caption">Trim. Date</td>
		                                         <td colspan="2"> 
		                                         	<input name="txt_trim_date" id="txt_trim_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:100px;"   />
		                                        </td>
		                                     </tr>
		                                     <tr>
		                                        <td class="must_entry_caption">Trim Line No</td> 
		                                        <td id="poly_line_td" colspan="2">            
													<?
		                                            	echo create_drop_down( "cbo_poly_line", 110, $blank_array,"", 1, "Select Line", $selected, "",1,0 );		
		                                            ?>	
		                              			</td> 
		                                   </tr>
		                                     <tr>
		                                       <td class="must_entry_caption">Reporting Hour</td> 
		                                       <td colspan="2">
		                                       	<input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:100px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
		                                       </td>
		                                     </tr>
		                                   <tr>
		                                     <tr>
		                                        <td>Color Type</td> 
		                                        <td id="color_type_td" colspan="2">            
													<?
		                                            	echo create_drop_down( "cbo_color_type", 110, $blank_array,"", 1, " -- Select -- ", $selected, "");
		                                            ?>	
		                              			</td> 
		                                   </tr>
		                                     <td>Supervisor</td> 
		                                     <td colspan="2"> 
		                                     	<input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();"  style="width:100px">
		                                     </td>
		                                   </tr>
		                                   <tr>
		                                        <td class="must_entry_caption">QC Pass Qty</td> 
		                                        <td colspan="2" valign="top">
		                                        	<input name="txt_trim_qty" id="txt_trim_qty" class="text_boxes_numeric"  style="width:100px" readonly >
		                                            <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
		                                            <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
		                                        </td>
		                                   </tr>
		                                   <tr>
		                                     <td>Alter Qty </td>
		                                     <td><input type="text" name="txt_alter_qnty" id="txt_alter_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
		                                     <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Alt Defect" style="width:60px" onClick="openmypage_defectQty(1);"/></td>
		                                   </tr>
		                                   <tr>
		                                     <td >Spot Qty </td>
		                                     <td><input type="text" name="txt_spot_qnty" id="txt_spot_qnty" class="text_boxes_numeric" style="width:100px; text-align:right" /></td>
		                                     <td><input type="button" name="btn" id="btn" class="formbuttonplasminus" value="Spt Defect" style="width:60px" onClick="openmypage_defectQty(2);"/></td>
		                                   </tr>
		                                   <tr>
		                                     <td>Reject Qty</td>
		                                     <td><input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:100px;"  /></td>
		                                     <td><input type="hidden" name="btn" id="btn" class="formbuttonplasminus" value="Rjt Defect" style="width:60px" onClick="openmypage_defectQty(3);"/></td>
		                                   </tr>
		                                   <tr>
		                                         <td class="must_entry_caption">Challan No</td> 
		                                         <td colspan="2">
		                                           <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" value="0" style="width:50px" />
		                                           Sys. Chln.<input type="text" name="txt_sys_chln" id="txt_sys_chln" class="text_boxes" style="width:45px" placeholder="Display" disabled />
		                                         </td>
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
		                                        <td>Cumul. Trim Qty</td>
		                                        <td>
		                                            <input type="text" name="txt_cumul_cutting" id="txt_cumul_cutting" class="text_boxes_numeric" style="width:70px" readonly disabled />
		                                        </td>
		                                    </tr>
		                                     <tr>
		                                        <td>Yet to Trim</td>
		                                        <td>
		                                            <input type="text" name="txt_yet_cut" id="txt_yet_cut" class="text_boxes_numeric" style="width:70px" / readonly disabled >
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
									echo load_submit_buttons( $permission, "fnc_cutting_update_entry", 0,0,"reset_form('polyoutput_1','list_view_country','','txt_trim_date,".$date."*txt_challan,0','childFormReset()')",1); 
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
		                <div style="width:1010px; margin-top:5px;margin-left: 10px;"  id="cutting_production_list_view" align="center"></div>
              	    </form>
                </fieldset>
            </div>
        <div id="list_view_country" style="width:390px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
