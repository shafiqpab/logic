<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create print imbro issue

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	24-02-2013
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Embellishment Delivery Entry","../", 1, 1, $unicode,'','');
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function openmypage_sysNo()
{
	var cbo_company_name=$('#cbo_company_name').val();
	var title = 'Challan Selection Form';
	var page_link = 'requires/special_embro_delivery_entry_controller.php?cbo_company_name='+cbo_company_name+'&action=challan_no_popup';

	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			if(mst_id!="")
			{
				freeze_window(5);
				reset_form('printembro_1','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','cbo_company_name*sewing_production_variable*styleOrOrderWisw*delivery_basis');
				get_php_form_data(mst_id, "populate_data_from_challan_popup", "requires/special_embro_delivery_entry_controller" );

				var delivery_basis=$('#delivery_basis').val();
				if(delivery_basis==3)
				{
					var bundle_nos=return_global_ajax_value(mst_id, 'bundle_nos', '', 'requires/special_embro_delivery_entry_controller');
					var response_data=return_global_ajax_value(trim(bundle_nos)+"**0**"+mst_id+"**"+$('#cbo_company_name').val(), 'populate_bundle_data_update', '', 'requires/special_embro_delivery_entry_controller');
					$('#tbl_details tbody tr').remove();
					$('#tbl_details tbody').prepend(response_data);
					var tot_row=$('#tbl_details tbody tr').length;
					$('#txt_tot_row').val(tot_row);
					set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,0);
				}
				else
				{
					show_list_view(mst_id,'show_dtls_listview','printing_production_list_view','requires/special_embro_delivery_entry_controller','');
				}
				$("#btn_print,#btn_print3,#btn_print4").removeClass("formbutton_disabled");
				$("#btn_print,#btn_print3,#btn_print4").addClass("formbutton");
				release_freezing();
			}
		}
	}//end else
}//end function

function openmypage(page_link,title)
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_grmtItem_id").value;
			var po_qnty=this.contentDoc.getElementById("hidden_po_qnty").value;
			var country_id=this.contentDoc.getElementById("hidden_country_id").value;

			if (po_id!="")
			{
				freeze_window(5);
				$("#txt_order_qty").val(po_qnty);
				$('#cbo_item_name').val(item_id);
				$("#cbo_country_name").val(country_id);

				childFormReset();//child form initialize

				get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/special_embro_delivery_entry_controller" );

				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();

				if(variableSettings==1)
				{
					$("#txt_issue_qty").removeAttr("readonly");
				}
				else
				{
					$('#txt_issue_qty').attr('readonly','readonly');
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+$("#cbo_embel_name").val()+'**'+country_id, "color_and_size_level", "requires/special_embro_delivery_entry_controller" );
				}

				show_list_view(po_id,'show_country_listview','list_view_country','requires/special_embro_delivery_entry_controller','');
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
				release_freezing();
			}
		}
	}//end else
}//end function

function generate_report_file(data,action,page)
	{
		window.open("requires/special_embro_delivery_entry_controller.php?data=" + data+'&action='+action, true );
	}


function fnc_issue_print_embroidery_entry(operation)
{
	var company_id=$('#cbo_company_name').val();
	var working_company_mandatory=return_global_ajax_value(company_id, 'load_variable_settings_for_working_company', '', 'requires/special_embro_delivery_entry_controller');

	if(working_company_mandatory==1)
	{
		if($('#cbo_working_company_name').val()==0)
			{
				alert('Working Company is Mandatory');
				return;
			}
			$('#working_company').css('color','blue');
	}

	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/special_embro_delivery_entry_controller');
		 return;
	}

	if(operation==0 || operation==1 || operation==2)
	{
		var delivery_basis=$('#delivery_basis').val();

		if(delivery_basis==3)
		{

			if($('#cbo_source').val()!=3)
			{
				if ( form_validation('cbo_company_name*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date*cbo_location*cbo_floor','Company Name*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date*Location*Floor')==false )
				{
					return;
				}
			}
			else
			{
				if ( form_validation('cbo_company_name*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date','Company Name*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date')==false )
				{
					return;
				}
			}

			/*
			if ( form_validation('cbo_company_name*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date*cbo_location*cbo_floor','Company Name*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date*Location*Floor')==false )
			{
				return;
			}	*/

			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Print Delivery Date Can not Be Greater Than Current Date");
				return;
			}

			var j=0; var dataString='';
			$("#tbl_details").find('tbody tr').each(function()
			{
				var cutNo=$(this).find('input[name="cutNo[]"]').val();
				var bundleNo=$(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(1)").attr('title');
				var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var gmtsitemId=$(this).find('input[name="gmtsitemId[]"]').val();
				var countryId=$(this).find('input[name="countryId[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var sizeId=$(this).find('input[name="sizeId[]"]').val();
				var qty=$(this).find('input[name="qty[]"]').val();
				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				var isrescan=$(this).find('input[name="isRescan[]"]').val();

				try
				{
					j++;

					dataString+='&bundleNo_' + j + '=' + bundleNo + '&orderId_' + j + '=' + orderId + '&gmtsitemId_' + j + '=' + gmtsitemId + '&countryId_' + j + '=' + countryId + '&colorId_' + j + '=' + colorId + '&sizeId_' + j + '=' + sizeId + '&colorSizeId_' + j + '=' + colorSizeId + '&qty_' + j + '=' + qty + '&dtlsId_' + j + '=' + dtlsId + '&cutNo_' + j + '=' + cutNo + '&barcodeNo_' + j + '=' + barcodeNo+ '&isRescan_' + j + '=' + isrescan;
				}
				catch(e)
				{
					//got error no operation
				}
			});

			if(j<1)
			{
				alert('No data');
				return;
			}

			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('garments_nature*cbo_company_name*sewing_production_variable*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_organic*txt_system_id*delivery_basis*txt_challan_no*cbo_body_part*cbo_working_company_name*cbo_working_location',"../")+dataString;
		}
		else
		{
			if ( form_validation('cbo_company_name*txt_order_no*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*txt_issue_date*txt_issue_qty','Company Name*Order No*Embel. Name* Embel. Type*Source*Embel.Company*Issue Date*Issue Quantity')==false )
			{
				return;
			}
			else
			{
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_issue_date').val(), current_date)==false)
				{
					alert("Print Delivery Date Can not Be Greater Than Current Date");
					return;
				}
				var sewing_production_variable = $("#sewing_production_variable").val();
				var colorList = ($('#hidden_colorSizeID').val()).split(",");

				var i=0;var colorIDvalue='';
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

				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('garments_nature*cbo_company_name*cbo_country_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_location*cbo_floor*txt_issue_date*txt_issue_qty*txt_challan*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*hidden_break_down_html*txt_mst_id*txt_organic*txt_challan_no*txt_system_id*delivery_basis*cbo_body_part*cbo_working_company_name*cbo_working_location',"../");
			}
		}

		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/special_embro_delivery_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_issue_print_embroidery_Reply_info;
	}
}

function fnc_issue_print_embroidery_Reply_info()
{
 	if(http.readyState == 4)
	{
		//release_freezing();return;
		var variableSettings=$('#sewing_production_variable').val();
		var styleOrOrderWisw=$('#styleOrOrderWisw').val();
		var item_id=$('#cbo_item_name').val();
		var country_id = $("#cbo_country_name").val();

		var reponse=http.responseText.split('**');
		if(reponse[0]==15)
		{
			 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',4000);
		}
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			if(reponse[3]){alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
			show_msg(trim(reponse[0]));

			document.getElementById('txt_system_id').value = reponse[1];
			document.getElementById('txt_challan_no').value = reponse[2];
			$('#cbo_company_name').attr('disabled','true');
			$('#cbo_embel_type').attr('disabled','true');
			$('#cbo_source').attr('disabled','true');
			$('#cbo_emb_company').attr('disabled','true');
			$('#cbo_embel_name').attr('disabled','true');
			$('#cbo_location').attr('disabled','true');
			$('#cbo_floor').attr('disabled','true');

			var delivery_basis=$('#delivery_basis').val();
			if(delivery_basis==3)
			{
				set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);
			}
			else
			{
				reset_form('','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','cbo_company_name*sewing_production_variable*styleOrOrderWisw*cbo_embel_name*cbo_embel_type*cbo_source*cbo_emb_company*cbo_knitting_source*cbo_location*cbo_floor*txt_organic*txt_issue_date');
				show_list_view(reponse[1],'show_dtls_listview','printing_production_list_view','requires/special_embro_delivery_entry_controller','');
				set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
			}
		}
		//$("#btn_print").removeClass("formbutton_disabled");
		//$("#btn_print").addClass("formbutton");

		$("#btn_print,#btn_print3,#btn_print4").removeClass("formbutton_disabled");
		$("#btn_print,#btn_print3,#btn_print4").addClass("formbutton");
		if(reponse[0]!=15)
		{
		  release_freezing();
		}
 	}
}


function childFormReset()
{
	reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*txt_mst_id','','');
	$('#txt_issue_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_cumul_issue_qty').attr('placeholder','');//placeholder value initilize
	$('#txt_yet_to_issue').attr('placeholder','');//placeholder value initilize
	$("#breakdown_td_id").html('');

}

function fn_total(tableName,index) // for color and size level
{
    var filed_value = $("#colSize_"+tableName+index).val();
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	if(filed_value*1 > placeholder_value*1)
	{
		if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
			void(0);
		else
		{
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
	$("#txt_issue_qty").val(totalVal);
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
	$("#txt_issue_qty").val( $("#total_color").val() );
}

function put_country_data(po_id, item_id, country_id, po_qnty, plan_qnty)
{
	freeze_window(5);

	//childFormReset();//child from reset
	$("#cbo_item_name").val(item_id);
	$("#txt_order_qty").val(po_qnty);
	$("#cbo_country_name").val(country_id);

	get_php_form_data(po_id+'**'+item_id+'**'+$('#cbo_embel_name').val()+'**'+country_id, "populate_data_from_search_popup", "requires/special_embro_delivery_entry_controller" );

	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();

	if(variableSettings==1)
	{
		$("#txt_issue_qty").removeAttr("readonly");
	}
	else
	{
		$('#txt_issue_qty').attr('readonly','readonly');
	}

	set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);
	release_freezing();
}

function pageReset()
{
	reset_form('printembro_1','list_view_country*printing_production_list_view','','','txt_issue_date,<? echo date("d-m-Y"); ?>','');

	$('#cbo_company_name').attr('disabled','false');
	$('#tbl_details_order').show();
	$('#printing_production_list_view').show();
	$('#tbl_details_bundle').hide();
	$('#bundle_list_view').hide();
}

function load_html()
{
	var delivery_basis=$('#delivery_basis').val();
	$('#printing_production_list_view').val('');

	if(delivery_basis==3)
	{
		$('#tbl_details_order').hide();
		$('#printing_production_list_view').hide();
		$('#tbl_details_bundle').show();
		$('#tbl_details tbody tr').remove();
		$('#bundle_list_view').show();
		$('#list_view_country').hide();
	}
	else
	{
		$('#tbl_details_order').show();
		$('#printing_production_list_view').show();
		$('#tbl_details_bundle').hide();
		$('#bundle_list_view').hide();
		$('#list_view_country').show();
		childFormReset();
	}

	set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,1);
}

function openmypage_bundle(page_link,title)
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		var bundleNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			bundleNo+=$(this).find("td:eq(1)").text()+',';

		});

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo, title, 'width=890px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_nos").value;//po id
			var hidden_source_cond=this.contentDoc.getElementById("hidden_source_cond").value;//bundle no

			if (hidden_bundle_nos!="")
			{
				//fnc_duplicate_bundle(hidden_bundle_nos);
				create_row(hidden_bundle_nos,"Browse",hidden_source_cond);
			}
		}
	}//end else
}//end function

	function fnc_duplicate_bundle(bundle_no)
	{
		var challan_duplicate=return_ajax_request_value( bundle_no+"__"+$('#cbo_company_name').val(),"challan_duplicate_check", "requires/special_embro_delivery_entry_controller");
		var ex_challan_duplicate=challan_duplicate.split("_");
		if(ex_challan_duplicate[0]==2)
		{
			var alt_str=ex_challan_duplicate[1].split("##");
			var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
			alert(al_msglc);
			$('#txt_bundle_no').val('');
			return;
		}
		else
		{
			create_row(bundle_no,'scan','');
		}
		$('#txt_bundle_no').val('');
	}

$('#txt_bundle_no').live('keydown', function(e) {
	if (e.keyCode === 13)
	{
		e.preventDefault();
		var txt_bundle_no=trim($('#txt_bundle_no').val().toUpperCase());
		var flag=1;
		$("#tbl_details").find('tbody tr').each(function()
		{
			var bundleNo=$(this).find("td:eq(1)").text();
			var barcode_no=$(this).find("td:eq(1)").attr('title');
			if(txt_bundle_no==barcode_no){
				alert("Bundle No: "+bundleNo+" already scan, try another one.");
				$('#txt_bundle_no').val('');
				flag=0;
				return false;
			}
		});

		if(flag==1)
		{
			fnc_duplicate_bundle(txt_bundle_no);
		}
	}
});

function create_row(bundle_nos,vscan,hidden_source_cond)
{
	freeze_window(5);

	var row_num=$('#txt_tot_row').val();
	var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+vscan+"**"+hidden_source_cond, 'populate_bundle_data', '', 'requires/special_embro_delivery_entry_controller');

	if(trim(response_data)=='')
	{
		alert("No Data Found. Please Check Pre-Costing Or Order Entry For Bundle Previous Process.");
		release_freezing();
		return;
	}
	$('#tbl_details tbody').prepend(response_data);
	var tot_row=$('#tbl_details tbody tr').length;
	$('#txt_tot_row').val(tot_row);
	release_freezing();
}

function fn_deleteRow( rid )
{
	$("#tr_"+rid).remove();
}

function working_com_fnc()
{
	var company_id=$('#cbo_company_name').val();
	var working_company_mandatory=return_global_ajax_value(company_id, 'load_variable_settings_for_working_company', '', 'requires/special_embro_delivery_entry_controller');

	if(working_company_mandatory==1)
	{
		$('#working_company').css('color','blue');
		//alert('Working Company is Mandatory');
		return;
	}
	else
	{
		$('#working_company').css('color','black');
	}
}

// for rescan bundle==================================================================================================================================

function openmypage_bundle_rescan(page_link,title)
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		var bundleNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			bundleNo+=$(this).find("td:eq(1)").text()+',';
		});

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo, title, 'width=890px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_bundle_nos=this.contentDoc.getElementById("hidden_bundle_nos").value;//bundle no
			var hidden_source_cond=this.contentDoc.getElementById("hidden_source_cond").value;//bundle no
			//alert(hidden_source_cond+"**hhh")
			if (hidden_bundle_nos!="")
			{
				create_rescanrow(hidden_bundle_nos,"Browse",hidden_source_cond);
			}
		}
	}//end else
}//end function

function create_rescanrow(bundle_nos,vscan,hidden_source_cond)
{
	var error=0;
	var bundle_arr=bundle_nos.split(",");
	for(var i=0;i<bundle_arr.length;i++)
	{
		$("#tbl_details").find('tbody tr').each(function()
		{
			var bundleNo=$(this).find("td:eq(1)").text();
			var barcodeNo=$(this).find("td:eq(1)").attr('title');
			if(bundle_arr[i]==barcodeNo){
				alert("Bundle No: "+bundleNo+" already scan, try another one.");
				$('#txt_bundle_rescan').val('');
				error=1;
				flag=0;
				return false;
			}
		});
	}
	if(error==0)
	{
		freeze_window(5);
		var row_num=$('#txt_tot_row').val();
		var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"****"+$('#cbo_company_name').val()+"**"+vscan+"**"+hidden_source_cond, 'populate_bundle_data_rescan', '', 'requires/special_embro_delivery_entry_controller');
		$('#tbl_details tbody').prepend(response_data);
		var tot_row=$('#tbl_details tbody tr').length;
		$('#txt_tot_row').val(tot_row);
		release_freezing();
	}
}

$('#txt_bundle_rescan').live('keydown', function(e) {
	if (e.keyCode === 13)
	{
		e.preventDefault();
		var txt_bundle_no=trim($('#txt_bundle_rescan').val().toUpperCase());
		var flag=1;
		$("#tbl_details").find('tbody tr').each(function()
		{
			var bundleNo=$(this).find("td:eq(1)").text();
			var barcodeNo=$(this).find("td:eq(1)").attr('title');
			if(txt_bundle_no==barcodeNo){
				alert("Bundle No: "+bundleNo+" already scan, try another one.");
				$('#txt_bundle_rescan').val('');
				flag=0;
				return false;
			}
		});

		if(flag==1)
		{
			create_rescanrow(txt_bundle_no,"scan",'');
			$('#txt_bundle_rescan').val('');
		}
	}
});


</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
 		<fieldset style="width:930px;">
        <legend>Production Module</legend>
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
            <fieldset>
                <table width="100%">
                	<tr>
                        <td align="right" colspan="3">Challan No</td>
                        <td colspan="3">
                          <input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:167px" onDblClick="openmypage_sysNo()" placeholder="Double click to search" />
                          <input name="txt_system_id" id="txt_system_id" class="text_boxes" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company</td>
                        <td>
                            <?
                            	echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "get_php_form_data(this.value,'load_variable_settings','requires/special_embro_delivery_entry_controller'); load_drop_down( 'requires/special_embro_delivery_entry_controller', this.value, 'load_drop_down_body_part', 'body_part_td' ); working_com_fnc(); load_html();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/special_embro_delivery_entry_controller' );" );//get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/special_embro_delivery_entry_controller');
                            ?>
                            <input type="hidden" id="sewing_production_variable" />
                            <input type="hidden" id="styleOrOrderWisw" />
                            <input type="hidden" id="delivery_basis" />
                        </td>
                         <td class="must_entry_caption">Embel. Name</td>
                         <td id="embel_name_td">
                             <?
							 	echo create_drop_down( "cbo_embel_name", 180, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "",1,4 );
                             ?>
                         </td>
                         <td class="must_entry_caption">Embel. Type</td>
                         <td id="embro_type_td" width="170">
                             <?
								echo create_drop_down( "cbo_embel_type", 180, $emblishment_spwork_type,"", 1, "--- Select Special Works ---", $selected, "" );
                             ?>
                         </td>
                    </tr>
                    <tr>
                          <td class="must_entry_caption">Source</td>
                          <td>
                              <?
                              	echo create_drop_down( "cbo_source", 180, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/special_embro_delivery_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_embro_issue_source', 'emb_company_td' );load_drop_down( 'requires/special_embro_delivery_entry_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td' );", 0, '1,3' );
                              ?>
                          </td>
                          <td class="must_entry_caption">Embel.Company</td>
                          <td id="emb_company_td">
                              <?
                             	 echo create_drop_down( "cbo_emb_company", 180, $blank_array,"", 1, "-- Select --", $selected, "" );
                              ?>
                          </td>
                          <td class="must_entry_caption">Location</td>
                          <td id="location_td">
                              <?
                              	echo create_drop_down( "cbo_location", 180, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                              ?>
                          </td>
                    </tr>
                    <tr>
                         <td class="must_entry_caption">Floor</td>
                         <td id="floor_td">
                             <?
                             	echo create_drop_down( "cbo_floor", 180, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
                             ?>
                         </td>
                         <td>Organic</td>
                         <td>
                            <input name="txt_organic" id="txt_organic" class="text_boxes" type="text" style="width:167px" />
                         </td>
                         <td class="must_entry_caption">Delivery Date</td>
                         <td>
                         	<input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:167px;"  />
                         </td>
                    </tr>
                    <tr>
                    	<td>Body Part</td>
                        <td id="body_part_td">
                        	<?
								echo create_drop_down( "cbo_body_part", 180, $blank_array,"", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td id="working_company">Working Company</td>
                        <td id="body_part_td">
                        	<?
								echo create_drop_down( "cbo_working_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --",$selected,"load_drop_down( 'requires/special_embro_delivery_entry_controller', $('#cbo_working_company_name').val(), 'load_drop_down_working_location', 'working_location_td' );" );
                            ?>
                        </td>
                         <td>Working Location</td>
                          <td id="working_location_td">
                              <?
                              	echo create_drop_down( "cbo_working_location", 180, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                              ?>
                          </td>
                    </tr>
                </table>
                </fieldset> <br />
                <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_order"  style="display:none">
                    <tr>
                          <td width="35%" valign="top">
                               <fieldset>
                                  <legend>New Entry</legend>
                                       <table  cellpadding="0" cellspacing="2" width="100%">
                                          <tr>
                                               <td width="80" class="must_entry_caption" id="td_caption">Order No</td>
                                               <td colspan="3" width="110">
                                                   <input name="txt_order_no" placeholder="Double Click to Search" onDblClick="openmypage('requires/special_embro_delivery_entry_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Order Search')" id="txt_order_no" class="text_boxes" style="width:212px" readonly />
                            <input type="hidden" id="hidden_po_break_down_id" value="" />
                                               </td>
                                          </tr>
                                          <!--<tr>
                                               <td width="80" class="must_entry_caption">Issue Date</td>
                                               <td colspan="3" width="110">
                                                    <input type="text" name="txt_issue_date" id="txt_issue_date" value="<?echo date("d-m-Y")?>" class="datepicker" style="width:100px;"  />
                                               </td>
                                          </tr> -->
                                          <tr>
                                               <td class="must_entry_caption">Issue Qty</td>
                                               <td colspan="3">
                                                   <input type="text" name="txt_issue_qty" id="txt_issue_qty"  class="text_boxes_numeric"  style="width:100px" readonly >
                                                   <input type="hidden" id="hidden_break_down_html"  value="" readonly disabled />
                                                   <input type="hidden" id="hidden_colorSizeID"  value="" readonly disabled />
                                               </td>
                                          </tr>
                                          <tr>
                                          	<td>Order Qnty</td>
                                         	<td>
                                          		<input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:100px" disabled readonly>
                                          	</td>
                                          </tr>
                                          <tr>
                                          	<td>Buyer</td>
                                            <td>
                                                <?
                                                	echo create_drop_down( "cbo_buyer_name", 112, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "Dispaly", $selected, "",1,0 );
                                                ?>
                                            </td>
                                          </tr>
                                          <tr>
                                          	<td>Style</td>
                                         	<td>
                                          		<input name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px" disabled  readonly>
                                          	</td>
                                          </tr>
                                          <tr>
                                          	<td>Item</td>
                                         	<td>
                                          		<? echo create_drop_down( "cbo_item_name", 110, $garments_item,"", 1, "Display", $selected, "",1,0 ); ?>
                                          	</td>
                                          </tr>
                                          <tr>
                                          	<td>Country</td>
                                         	<td>
                                          		<?
													echo create_drop_down('cbo_country_name',110,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);
												?>
                                          	</td>
                                          </tr>
                                          <tr>
                                               <td>Challan No</td>
                                               <td>
                                               	<input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:100px" disabled readonly />
                                               </td>
                                               <td>Iss. ID</td>
                                               <td>
                                               	<input type="text" name="txt_iss_id" id="txt_iss_id" class="text_boxes" style="width:50px" disabled readonly />
                                               </td>
                                        </tr>
                                        <tr>
                                        	<td>Remarks</td>
                                            <td colspan="3">
                                               <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:217px" title="450 Characters Only." />
                                           	</td>
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
                                            <td width="100">Cutt. Qty</td>
                                            <td width="90">
                                            <input type="text" name="txt_cutting_qty" id="txt_cutting_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Cuml. Issue Qty</td>
                                            <td >
                                            <input type="text" name="txt_cumul_issue_qty" id="txt_cumul_issue_qty" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Yet to Issue</td>
                                            <td>
                                            <input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:80px" disabled readonly/>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                            <td width="40%" valign="top">
                                <div style="max-height:350px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                            </td>
                    </tr>
               	</table>
                <table cellpadding="0" cellspacing="1" width="100%" id="tbl_details_bundle">
                	<tr>
                    	<td>
                            <fieldset>
                              <legend>New Entry</legend>
                               <table  cellpadding="0" cellspacing="2" width="100%">
                                  <tr>
                                       <td width="80" class="must_entry_caption" id="td_caption">Barcode No</td>
                                       <td colspan="2" width="110">
                                           <input name="txt_bundle_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle('requires/special_embro_delivery_entry_controller.php?action=bundle_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Bundle Search')" id="txt_bundle_no" class="text_boxes" style="width:212px" />
                                       </td>
                                       <td width="100" class="must_entry_caption" id="td_caption">Re-Scan Barcode</td>
                                       <td colspan="2" width="110">
                                           <input name="txt_bundle_rescan" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle_rescan('requires/special_embro_delivery_entry_controller.php?action=bundle_popup_rescan&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Search Bundle For Rescan')"  id="txt_bundle_rescan" class="text_boxes" style="width:212px" />
                                       </td>
                                    </tr>
                                </table>
                            </fieldset>
                    	</td>
                    </tr>
                </table>
                <div id="bundle_list_view">
                	<table cellpadding="0" width="920" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <th width="30">SL</th>
                            <th width="90">Bundle No</th>
                            <th width="50">Year</th>
                            <th width="60">Job No</th>
                            <th width="65">Buyer</th>
                            <th width="90">Order No</th>
                            <th width="120">Gmts. Item</th>
                            <th width="100">Country</th>
                            <th width="80">Color</th>
                            <th width="70">Size</th>
                            <th width="80">Qty.</th>
                            <th></th>
                        </thead>
              		</table>
                    <div style="width:920px; max-height:250px; overflow-y:scroll" align="left">
                    	<table cellpadding="0" width="900" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">
                            <tbody>
                            </tbody>
                        </table>
                	</div>
                </div>
               	<table cellpadding="0" cellspacing="1" width="100%">
               		<tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,1 ,"reset_form('printembro_1','list_view_country','','txt_issue_date,".$date."','pageReset();')",1);
                            ?>
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" value="0" readonly >
                            <input type="button" id="btn_print" name="btn_print"   style="width:120px;"  class="formbutton_disabled" value="Size Wise Print"  onClick="generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+$('#cbo_working_company_name').val(),'embrodary_color_wise_print','requires/special_embro_delivery_entry_controller');"/>
                            <input type="button" id="btn_print3" name="btn_print3"   style="width:140px;"  class="formbutton_disabled" value="Color & Size Wise Print"  onClick="generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val(),'embrodary_color_size_wise_print','requires/special_embro_delivery_entry_controller');"/>
                          <input type="button" id="btn_print4" name="btn_print4"   style="width:140px;"  class="formbutton" value="Color & Size Wise Print 2"  onClick="generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val(),'embrodary_color_size_wise_print2','requires/special_embro_delivery_entry_controller');"/>

                        </td>
                        <td>&nbsp;</td>
                    </tr>
               	</table>
               	<div style="width:900px; margin-top:5px;" id="printing_production_list_view" align="center"></div>
        	</form>
        </fieldset>
    </div>
	<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>