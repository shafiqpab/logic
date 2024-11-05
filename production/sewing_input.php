<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sewing input
Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	25-02-2013
Updated by 		: 	Kausar (Creating Print Report )
Update date		: 	08-01-2014
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
echo load_html_head_contents("Sewing Input Info","../", 1, 1, $unicode,'','');

?>
<script>
var field_level_menual = 1;
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
function fnc_company_check(val)
{
	if(val==1)
	{
		if($("#cbo_company_name").val()==0)
		{
			alert("Please Select Company.");
			$("#cbo_source").val(0);
			$("#cbo_sewing_company").val(0);
			return;
		}
		else
		{
			get_php_form_data(document.getElementById('cbo_sewing_company').value+'**'+document.getElementById('garments_nature').value,'production_process_control','requires/sewing_input_controller' );
		}
	}
	else
	{
		get_php_form_data(document.getElementById('cbo_company_name').value+'**'+document.getElementById('garments_nature').value,'production_process_control','requires/sewing_input_controller' );
	}
}
<?php   
if($_SESSION['logic_erp']['data_arr'][95]){
	echo "var field_level_data= " . json_encode($_SESSION['logic_erp']['data_arr'][95]). ";\n";
}
?>

function openmypage(page_link,title)
{
	//	if ( form_validation('cbo_company_name*cbo_source*cbo_sewing_company','Company Name*Production Source*Production Company')==false )
	//	{
	//		return;
	//	}
	//else
	//{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1180px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{

			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;
			var company_id=this.contentDoc.getElementById("hidden_company_id").value;
			var prod_reso_allo=$('#prod_reso_allo').val();
			var country_ship_date=this.contentDoc.getElementById("hid_country_ship_date").value;
		   // load_drop_down( 'requires/sewing_input_controller',company_id, 'load_drop_down_location', 'location_td' );
			get_php_form_data(company_id,'load_variable_settings','requires/sewing_input_controller');
			if (po_id!="")
			{
				//freeze_window(5);

				$("#txt_order_qty").val(po_qnty);
				$("#cbo_item_name").val(item_id);
				$("#cbo_country_name").val(country_id);
				$("#cbo_company_name").val(company_id);
				$("#country_ship_date").val(country_ship_date);
				//alert(country_ship_date);
				fnc_company_check(3);
				get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+country_ship_date, "populate_data_from_search_popup", "requires/sewing_input_controller" );

				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				var garments_nature=$('#garments_nature').val();
				if(variableSettings!=1){
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature+'**'+country_ship_date, "color_and_size_level", "requires/sewing_input_controller" );
				}
				else
				{
					$("#txt_input_qnty").removeAttr("readonly");
				}

				show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_input_controller','setFilterGrid(\'tbl_list_search\',-1)');
				show_list_view(po_id,'show_country_listview','list_view_country','requires/sewing_input_controller','setFilterGrid(\'country_list_search\',-1)');
 				reset_form('','','txt_input_qnty*hidden_break_down_html*txt_remark*cbo_sewing_line*txt_mst_id','','');
				set_button_status(0, permission, 'fnc_sewing_input_entry',1,0);
				var sewing_date=$("#txt_sewing_date").val();
				var data=po_id+'**'+sewing_date;
 	 			var response_data = return_global_ajax_value( data, 'plan_data_action', '', 'requires/sewing_input_controller');
 	 			response_data=trim(response_data);
 	 			$("#plan_breakdown_td_id").html('');
 	 			if(response_data)
 	 			{
 	 				$("#plan_breakdown_td_id").html(response_data);
 	 			}
				setFieldLevelAccess(company_id);
				release_freezing();
 			}
 			$("#cbo_company_name").attr("disabled","disabled");
		}
	//}//end else
}//end function
function fnc_load_plan(value)
{
	var po_id=$("#hidden_po_break_down_id").val();
	var data=po_id+'**'+value;
	var response_data = return_global_ajax_value( data, 'plan_data_action', '', 'requires/sewing_input_controller');
	response_data=trim(response_data);
	$("#plan_breakdown_td_id").html('');
	if(response_data)
	{
		$("#plan_breakdown_td_id").html(response_data);
	}

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


function fnc_sewing_input_entry(operation)
{
	var source=$("#cbo_source").val();
	if(operation==4)
	{
			var master_ids = ""; var total_tr=$('#tbl_list_search tr').length;
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
		 // alert(master_ids);
		 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "sewing_input_print", "requires/sewing_input_controller" )
		 return;
	}
	else if(operation==5)
	{
			var master_ids = ""; var total_tr=$('#tbl_list_search tr').length;
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
		 // alert(master_ids);
		 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "sewing_input_print2", "requires/sewing_input_controller" )
		//  alert (print_report);
		//  print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "sewing_input_print3", "requires/sewing_input_controller" )
		 return;
	}
	else if(operation==6)
	{
			var master_ids = ""; var total_tr=$('#tbl_list_search tr').length;
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
		 // alert(master_ids);
		 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "sewing_input_print3", "requires/sewing_input_controller" )
		 return;
	}
	else if(operation==0 || operation==1 || operation==2) {
		if (form_validation('cbo_company_name*txt_order_no*cbo_item_name*cbo_source*cbo_sewing_company*txt_sewing_date*txt_input_qnty*txt_challan', 'Company Name*Order No*Item Name*Source*Sewing Company*Sewing Date*Input Quantity*Challan No') == false) {

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

			var current_date = '<? echo date("d-m-Y"); ?>';
			if (date_compare($('#txt_sewing_date').val(), current_date) == false) {
				release_freezing();
				alert("Input Date Can not Be Greater Than Current Date");
				return;
			}
			if ($("#cbo_source").val() == 1 && ($("#cbo_sewing_line").val() == 0 || $("#cbo_sewing_line").val() == "")) {
				release_freezing();
				alert("Please Select Sewing Line");
				return;
			}

			
			var sewing_production_variable = $("#sewing_production_variable").val();
			if(sewing_production_variable=="" || sewing_production_variable==0)
			{
 				sewing_production_variable=3;
 				 
			}

			var colorList = ($('#hidden_colorSizeID').val()).split(",");

			var i = 0; var k=0; var colorBundleVal="";
			var colorIDvalue = '';
			if (sewing_production_variable == 2)//color level
			{
				$("input[name=txt_color]").each(function (index, element) {
					if ($(this).val() != '') {
						if (i == 0) {
							colorIDvalue = colorList[i] + "*" + $(this).val();
						}
						else {
							colorIDvalue += "**" + colorList[i] + "*" + $(this).val();
						}
					}
					i++;
				});

				$("input[name=colorSizeBundleQnty]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						if(k==0)
						{
							colorBundleVal = colorList[k]+"*"+$(this).val();
						}
						else
						{
							colorBundleVal += "**"+colorList[k]+"*"+$(this).val();
						}
					}
					k++;
					
				});
			}
			else if (sewing_production_variable == 3)//color and size level
			{
				$("input[name=colorSize]").each(function (index, element) {
					if ($(this).val() != '') {
						color_size_breakdown_id = $(this).attr('data-colorSizeBreakdown');
						if (i == 0) {
							// colorIDvalue = colorList[i] + "*" + $(this).val();
							colorIDvalue = color_size_breakdown_id + "*" + $(this).val();
						}
						else {
							// colorIDvalue += "***" + colorList[i] + "*" + $(this).val();
							colorIDvalue += "***" + color_size_breakdown_id + "*" + $(this).val();
						}
						
					}
					i++;
					
				});

				$("input[name=colorSizeBundleQnty]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						color_size_breakdown_id = $(this).attr('data-colorSizeBreakdown');
						if(k==0)
						{
							// colorBundleVal = colorList[k]+"*"+$(this).val();
							colorBundleVal = color_size_breakdown_id+"*"+$(this).val();
						}
						else
						{
							// colorBundleVal += "***"+colorList[k]+"*"+$(this).val();
							colorBundleVal += "***"+color_size_breakdown_id+"*"+$(this).val();
						}
					}
					k++;
					
				});
			}
			// alert(colorBundleVal);
			// return;

			var data = "action=save_update_delete&operation=" + operation + "&colorIDvalue=" + colorIDvalue+ "&colorBundleVal=" + colorBundleVal + get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_sewing_company*cbo_location*cbo_floor*cbo_sewing_line*cbo_produced_by*txt_sewing_date*txt_challan*txt_man_cutting_no*txt_remark*txt_input_qnty*txt_cumul_input_qty*txt_yet_to_input*hidden_break_down_html*txt_mst_id*prod_reso_allo*country_ship_date', "../");

			http.open("POST", "requires/sewing_input_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sewing_input_entry_Reply_info;
		}
	}
}

function fnc_sewing_input_entry_Reply_info()
{
 	if(http.readyState == 4)
	{
		//alert(http.responseText);
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();
		var prod_reso_allo=$('#prod_reso_allo').val();
		var garments_nature=$('#garments_nature').val();
		var country_ship_date=$('#country_ship_date').val();

		var reponse=http.responseText.split('**');
		
		if(reponse[0]==13)
		{
			alert(reponse[1])
			release_freezing();
		    return;
		}
		else if(reponse[0]==25)
		{
			$("#txt_input_qnty").val("");
			show_msg('25');
			release_freezing();
			return;
		}
		else if(reponse[0]==35)
		{
			$("#txt_input_qnty").val("");
			show_msg('25');
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==45)
		{
			$("#txt_input_qnty").val("");
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==786)
		{
			alert("Projected PO is not allowed to production. Please check variable settings.");
			release_freezing();
			return false;
		}
		
		else if(reponse[0]==420)
		{
			alert("Color Size Breakdown ID Not Found.");
			release_freezing();
			return false;
		}
		if(reponse[0]==15)
		{
			 setTimeout('fnc_fabric_cost_dtls('+ reponse[1]+')',8000);
		}
		else 
		{/*if(reponse[0]==0)
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
 			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_input_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_input_qnty*txt_challan*hidden_break_down_html*hidden_colorSizeID*txt_remark*cbo_sewing_line*txt_man_cutting_no*txt_mst_id','','');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/sewing_input_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level", "requires/sewing_input_controller" );
			}
			else
			{
				$("#txt_input_qnty").removeAttr("readonly");
			}
		}
		else if(reponse[0]==1)
		{
			*/var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_input_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_input_qnty*txt_challan*hidden_break_down_html*hidden_colorSizeID*txt_remark*cbo_sewing_line*txt_man_cutting_no*txt_mst_id','','');//'txt_sewing_date,<?// echo date("d-m-Y"); ?>'
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+country_ship_date, "populate_data_from_search_popup", "requires/sewing_input_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature+'**'+$('#country_ship_date').val(), "color_and_size_level", "requires/sewing_input_controller" );
			}
			else
			{
				$("#txt_input_qnty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_sewing_input_entry',1,0);
		/*}
		else if(reponse[0]==2)
		{
			var po_id = reponse[1];
			show_msg(trim(reponse[0]));
			show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_input_controller','setFilterGrid(\'tbl_list_search\',-1)');
			reset_form('','','txt_input_qnty*txt_challan*hidden_break_down_html*hidden_colorSizeID*txt_remark*cbo_sewing_line*txt_mst_id','txt_sewing_date,<? echo date("d-m-Y"); ?>','');
			get_php_form_data(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id+'**'+$('#hidden_preceding_process').val(), "populate_data_from_search_popup", "requires/sewing_input_controller" );

			if(variableSettings!=1) {
				get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature, "color_and_size_level", "requires/sewing_input_controller" );
			}
			else
			{
				$("#txt_input_qnty").removeAttr("readonly");
			}
			set_button_status(0, permission, 'fnc_sewing_input_entry',1,0);*/
		}
		
		
		$("#cbo_sewing_line").val('');
		var all_data=document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value;
		load_drop_down( 'requires/sewing_input_controller',all_data, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );
		release_freezing();
 	}
}


function childFormReset()
{
 	reset_form('','list_view_container','txt_input_qnty*cbo_sewing_line*txt_challan*hidden_break_down_html*hidden_colorSizeID*txt_receive_qnty*txt_cumul_input_qty*txt_yet_to_input','','');
 	$('#txt_receive_qnty').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_quantity').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_quantity').attr('placeholder','');//placeholder value initilize
	$("#list_view_container").html('');
	$("#breakdown_td_id").html('');
	$("#plan_breakdown_td_id").html('');
 }

function fnc_load_from_dtls(id)
{
	//alert(id); return;
	get_php_form_data(id,'populate_input_form_data','requires/sewing_input_controller');
}

function fnc_checkbox_check(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var servingCompany=$('#servingCompany_'+rowNo).val();
		var servingLocation=$('#servingLocation_'+rowNo).val();
		var mst_source= $('#productionsource_'+rowNo).val();

		if(isChecked==true)
		{
			var tot_row=$('#tbl_list_search tr').length-1;
			for(var i=1; i<=tot_row; i++)
			{
				if(i!=rowNo)
				{
					try
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							//var emblnameCurrent=$('#emblname_'+i).val();
							var productionsourceCurrent=$('#productionsource_'+i).val();
							var servingLocationCurrent=$('#servingLocation_'+i).val();
							var servingCompanyCurrent=$('#servingCompany_'+i).val();
							if( (mst_source!=productionsourceCurrent) || (servingCompany!=servingCompanyCurrent) || ( servingLocation !=servingLocationCurrent) )
							{
								alert("Please Select Same  Source and Serving Company and Location");
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
			set_button_status(0, permission, 'fnc_sewing_input_entry',1,1);
		}
	}

	function checkAll()
	{
        // console.log(`checked : ${$('#checkedAll').is(":checked")}`);
        if($("#checkedAll").is(":checked"))
        {
            $('input:checkbox').removeAttr('checked').attr("checked","checked");  
        }
        else
        {
            $('input:checkbox').removeAttr('checked');  
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
	$("#txt_input_qnty").val(totalVal);
}

function fn_colorlevel_total(index) //for color level
{
	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
	var variable_is_controll=$('#variable_is_controll').val();
	var hidden_variable_cntl=$('#hidden_variable_cntl').val()*1;
	if(filed_value*1 > placeholder_value*1)
	{
		if(hidden_variable_cntl==1 && txt_user_lebel!=2)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+index).val('');
			$("#txt_input_qnty").val('');
		}
		else
		{
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
			{
				void(0);
			}
			else
			{
				$("#colSize_"+index).val('');
			}
		}


	}

    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color", "colSize_", "+", totalRow);
	$("#txt_input_qnty").val( $("#total_color").val() );
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty , country_ship_date)
{
	freeze_window(5);
    //alert(country_ship_date);
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);
	$("#country_ship_date").val(country_ship_date);
   
	get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+country_ship_date, "populate_data_from_search_popup", "requires/sewing_input_controller" );

	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var prod_reso_allo=$('#prod_reso_allo').val();
	var garments_nature=$('#garments_nature').val();


	
	if(variableSettings!=1)
	{
		get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id+'**'+$('#hidden_preceding_process').val()+'**'+garments_nature+'**'+country_ship_date, "color_and_size_level", "requires/sewing_input_controller" );
	}
	else
	{
		$("#txt_input_qnty").removeAttr("readonly");
	}

	show_list_view(po_id+'**'+item_id+'**'+country_id+'**'+prod_reso_allo,'show_dtls_listview','list_view_container','requires/sewing_input_controller','setFilterGrid(\'tbl_list_search\',-1)');
	reset_form('','','txt_input_qnty*hidden_break_down_html*txt_remark*cbo_sewing_line*txt_mst_id','','');
	set_button_status(0, permission, 'fnc_sewing_input_entry',1,0);
	release_freezing();
}
function fnc_all_system_id()
{
	var po_id=$("#hidden_po_break_down_id").val();
	 
	var page_link="requires/sewing_input_controller.php?action=all_system_id_popup&po_id="+po_id;
	var title="All Issue Id";
	 
	if(po_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=330px,height=200px,center=1,resize=0,scrolling=0','')
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

function fn_chk_next_process_qty(tableName,index,sizeId) // for color and size level
{
	// alert('ok');return;
	var data="action=chk_next_process_qty&colorId="+tableName+"&sizeId="+sizeId+get_submitted_data_string('cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_item_name',"../");
	//alert(data); return;
	var curent_input_qty = $("#colSize_"+tableName+index).val()*1;
	var prev_input_qty = $("#colSizeUpQty_"+tableName+index).val()*1;
	$.ajax({
		url: 'requires/sewing_input_controller.php',
		type: 'POST',
		data: data,
		success: function(response)
		{
			var resData = trim(response).split("****");
			var totOutQty = resData[0]*1;
			var totInputQty = resData[1]*1;
			// alert(totOutQty);
			// alert(totInputQty+'+('+curent_input_qty+'-'+prev_input_qty+')<'+totOutQty);
			if((totInputQty+(curent_input_qty-prev_input_qty))*1 < totOutQty*1)
			{	
				alert('Sorry! Input qnty will not less than Output qnty');			
				$("#colSize_"+tableName+index).val(prev_input_qty);		 		
			}
		}
	});
}

	function openmypage_woNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val();
		var cbo_service_company = $('#cbo_sewing_company').val();		
		var po_order_id = $('#hidden_po_break_down_id').val();		
		var po_order_no = $('#txt_order_no').val();
		if (form_validation('cbo_company_name*cbo_source*cbo_sewing_company*txt_order_no','Company*Source*Service Company*Order No')==false)
		{
			return;
		}
		else
	  	{			
			if (form_validation('cbo_sewing_company','Service Company')==false)
			{
				return;
			}
			
			var page_link='requires/sewing_input_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&supplier_id='+cbo_sewing_company+'&po_order_id='+po_order_id+'&po_order_no='+po_order_no+'&action=service_booking_popup';
			var title='WO Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1320px,height=390px,center=1,resize=1,scrolling=0','../');
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

	function load_sewing_company(source_id)
	{
		if($('#cbo_sewing_company option').length==2)
		{
			if($('#cbo_sewing_company option:first').val()==0)
			{
				var cbo_sewing_company_id=$('#cbo_sewing_company option:last').val();
				$('#cbo_sewing_company').val(cbo_sewing_company_id);
				if(source_id==1)
				{
					load_drop_down( 'requires/sewing_input_controller', cbo_sewing_company_id, 'load_drop_down_location', 'location_td' );
					fnc_company_check(source_id);
				}
			}
		}
	}

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
		$("#txt_input_qnty").val(totalVal);
	}
	
	function fnc_line_disable_enable(source)
	{
		if(source==1)
		{
			$('#cbo_sewing_line').removeAttr('disabled');
		}
		else
		{
			$('#cbo_sewing_line').val(0);
			$('#cbo_sewing_line').attr('disabled',true);
		}
	}

	function fn_check_drag_and_drop(event)
	{
		event.preventDefault();
		alert('Drag and drop not allow!');
	}

	/* $(document).ready(function()
	{
		$('.text_boxes_numeric').on('paste', function(e) {
    		e.preventDefault();
		});
	}) */
	/* const pasteBox = document.getElementsByClassName("text_boxes_numeric");
	pasteBox.onpaste = e => {
		e.preventDefault();
		return false;
	}; */
	
	
</script>
<style>
	/* .text_boxes_numeric{pointer-events: none;} */
</style>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
 	<div style="width:920px; float:left" align="center">
        <fieldset style="width:1000px;">
        <legend>Production Module</legend>
            <form name="sewinginput_1" id="sewinginput_1" autocomplete="off" >
            	<fieldset>
            		<table width="100%">
            			<tr>
            				<input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
            				<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process">
            				<td width="100" class="must_entry_caption">Order No</td>
            				<td width="">
            					<input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/sewing_input_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:100px " readonly /><input type="button"   class="formbutton" onClick="fnc_all_system_id();"  style="width: 77px" value="View Sys.Ch.">
            					<input type="hidden" id="hidden_po_break_down_id" value="" />
            				</td>

            				<td width="100" class="must_entry_caption">Source</td>
            				<td width="">
            					<?
            					echo create_drop_down( "cbo_source", 180, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/sewing_input_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_sewing_input', 'sew_company_td' );dynamic_must_entry_caption(this.value);load_sewing_company(this.value); fnc_line_disable_enable(this.value);", 0, '1,3' );//get_php_form_data(this.value,'line_disable_enable','requires/sewing_input_controller');
            					?>
								
            				</td>

            				<td width="100" class="must_entry_caption">Sewing Company</td>
            				<td id="sew_company_td" width="150">
            					<?
            					echo create_drop_down( "cbo_sewing_company", 140, $blank_array,"", 1, "-- Select --", $selected, "" );
            					?>
            				</td>

            			</tr>
            			<tr>
            				<td width="100" class="must_entry_caption">Company</td>
            				<td>
            					<?
            					echo create_drop_down( "cbo_company_name", 190, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "get_php_form_data(this.value,'load_variable_settings','requires/sewing_input_controller');",1 );
            					?>
            					<input type="hidden" id="sewing_production_variable" />
            					<input type="hidden" id="styleOrOrderWisw" />
            					<input type="hidden" id="prod_reso_allo" />
            					<input type="hidden" id="variable_is_controll" />
            					<input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
								<input type="hidden" id="country_ship_date" />
            				</td>


            				<td width="100" >Country</td>
            				<td width="170">
            					<?
            					echo create_drop_down( "cbo_country_name", 180, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
            					?>
            				</td>

            				<td width="100">Buyer</td>
            				<td width="150">
            					<?
            					echo create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );
            					?>
            				</td>


            			</tr>
            			<tr>

            				<td width="100">Job No</td>
            				<td width="170">
            					<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:180px " disabled readonly />
            				</td>
            				<td width="100">Style</td>
            				<td width="">
            					<input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:170px " disabled  readonly />
            				</td>
            				<td width="100" class="must_entry_caption"> Item </td>
            				<td width="150">
            					<?
            					echo create_drop_down( "cbo_item_name", 140, $garments_item,"", 1, "-- Select --", $selected, "",1,0 );
            					?>
            				</td>

            			</tr>
            			<tr>

            				<td width="100">Order Qnty</td>
            				<td width="170">
            					<input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:180px " disabled readonly />
            				</td>

            				<td width="100" id="locations">Location</td>
            				<td width="170" id="location_td">
            					<?
            					echo create_drop_down( "cbo_location", 180,$blank_array,"", 1, "-- Select Location --", $selected, "" );
            					?>
            				</td>
            				<td width="100" id="floors">Floor</td>
            				<td width="150" id="floor_td">
            					<?
            					echo create_drop_down( "cbo_floor", 140, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
            					?>
            				</td>
            			</tr>
									<tr>
										<td>WO NO</td>
                        <td>
                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:167px;" placeholder="Browse/Write/scan" onDblClick="openmypage_woNo();" />
                            <input type="hidden" id="txt_wo_id" value="0" />
                        </td>
									</tr>
            		</table>
            	</fieldset>
             <table cellpadding="0" cellspacing="1" width="100%">
                <tr>
                    <td width="30%" valign="top">
                        <fieldset>
                        <legend>New Entry</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%">
                            	<tr>
                            		<td width="120" class="">Produced By</td>
                            		<td width="120" colspan="2">
                            			<?
                            			echo create_drop_down( "cbo_produced_by", 110, $worker_type,"", 1, "--Select Type--", 1, "",0 ); 
                            			?>
                            		</td>
                            	</tr>

                                <tr>
                                      <td width="100" class="must_entry_caption">Sewing Date</td>
                                      <td width="200">
                                        <input name="txt_sewing_date" id="txt_sewing_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:100px;" onChange="load_drop_down( 'requires/sewing_input_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+this.value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );fnc_load_plan(this.value);"  />
                                      </td>
                                </tr>
                                <tr>
                                     <td width="">Sewing Line</td>
                                      <td width="" id="sewing_line_td" >
                                          <?
                                          echo create_drop_down( "cbo_sewing_line", 110, $blank_array,"", 1, "--- Select ---", $selected, "",1 );
                                          ?>
                                      </td>
                                </tr>
                                <tr>
                                     <td width="" class="must_entry_caption">Sewing Quantity</td>
                                     <td width="">
                                        <input type="text" name="txt_input_qnty" id="txt_input_qnty" class="text_boxes_numeric" style="width:100px"  readonly="readonly" />
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                     </td>
                                </tr>
                                <tr>
                                     <td width="" class="must_entry_caption">Challan No</td>
                                     <td>
                                       <input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:60px" value="0" />
                                        Sys. Chln. &nbsp;<input type="text" name="txt_iss_id" id="txt_iss_id" class="text_boxes" style="width:40px" readonly />
                                     </td>
                                </tr>
                                <tr>
                                     <td width="120">Manual Cut.No</td>
                                     <td>
                                       <input type="text" name="txt_man_cutting_no" id="txt_man_cutting_no" class="text_boxes_numeric" style="width:100px" />
                                     </td>
                                </tr>
                                <tr>
                                    <td width="">Remarks</td>
                                    <td width="" colspan="4">
                                        <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:150px" />
                                    </td>
                                </tr>
                            </table>
                      </fieldset>
                  </td>
                  <td width="1%" valign="top">
                  </td>
                  <td width="20%" valign="top">
                          <fieldset>
                          <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="1" width="100%" >
                                    <tr>
                                       <td width="50%" id="dynamic_msg">Total Plan Cut Quantity</td>
                                       <td width="50%">
                                            <input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" style="width:80px"  readonly disabled />
                                       </td>
                                    </tr>
                                    <tr>
                                      <td width="">Cumul. Sewing Qty</td>
                                      <td>
                                      		<input type="text" name="txt_cumul_input_qty" id="txt_cumul_input_qty" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                      </td>
                                    </tr>
                                    <tr>
                                      <td width="150">Yet to Sewing</td>
                                      <td>
                                            <input type="text" name="txt_yet_to_input" id="txt_yet_to_input" class="text_boxes_numeric" style="width:80px" readonly disabled />
                                      </td>
                                    </tr>
                               </table>
                        </fieldset>
                </td>
                <td width="36%" valign="top" >
                    <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                 </td>
                 <td width="13%" valign="top"> 
                 	<div style="max-height:350px; overflow-y:scroll" id="plan_breakdown_td_id" align="center"></div>
                 </td>
                </tr>
                <tr>
                   <td align="center" colspan="9" valign="middle" class="button_container">
                       <?
					   $date=date('d-m-Y');
                       echo load_submit_buttons( $permission, "fnc_sewing_input_entry", 0, 1,"reset_form('sewinginput_1','list_view_country','','txt_sewing_date,".$date."','childFormReset()')",1);
                       ?>

                       <input value="Print 2" name="print2" onClick="fnc_sewing_input_entry(5)" style="width:80px" id="Print2" class="formbutton" type="button">

					   <input value="Print 3" name="print 3" onClick="fnc_sewing_input_entry(6)" style="width:80px" id="Print3" class="formbutton" type="button">

                       <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                   </td>
                   <td>&nbsp;</td>
               </tr>
            </table>

            </form>
        </fieldset>
        <div style="width:1050px; margin-top:5px;" id="list_view_container"></div>
    </div>
	<div id="list_view_country" style="width:380px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:absolute;left: 1020px;"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
