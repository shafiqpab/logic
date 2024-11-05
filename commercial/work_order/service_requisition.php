<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Service Requisition				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	24-4-2022
Updated by 		:	
Update date		: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

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
//========== user credential end ==========service_requisition

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Service Requisition Info","../../", 1, 1, $unicode);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	function fnc_service_req_entry( operation )
	{		
		var isFileMandatory = "";
		<?php 
			
			if(!empty($_SESSION['logic_erp']['mandatory_field'][621][1])) echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][621][1] . ";\n";
		?>
		// alert(isFileMandatory); return;
		if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#update_id').val()==''){

			document.getElementById("multiple_file_field").focus();
			var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
			alert("Please Add File in Master Part");
			return;	
		}

		if (form_validation('cbo_company_name*cbo_location_name*txt_req_date','Company Name*Location*WO Date')==false)
		{
			return;
		}

		var row_num = $('#tbl_dtls_item tbody tr').length;
		// alert(row_num);

		// for (var j=1; j<=row_num; j++)
		// {
		// 	if (form_validation('cboServiceFor_'+j+'*txtServiceDetails_'+j+'*txtQnty_'+j,'Service For*Service Details*Qnty')==false)
		// 	{
		// 		return;
		// 	}
		// }

		var j=0; var dataString=''; 
		$("#tbl_dtls_item").find('tbody tr').each(function()
		{
			// alert(row_num);
			var cboServiceFor=$(this).find('select[name="cboServiceFor[]"]').val();
			var txtServiceDetails=$(this).find('input[name="txtServiceDetails[]"]').val();
			var hdnServiceId=$(this).find('input[name="hdnServiceId[]"]').val();
			var txtItemId=$(this).find('input[name="txtItemId[]"]').val();
			var cboUom=$(this).find('select[name="cboUom[]"]').val();
			var cboItemCategory=$(this).find('select[name="cboItemCategory[]"]').val();
			var txtQnty=$(this).find('input[name="txtQnty[]"]').val();
			var txtRate=$(this).find('input[name="txtRate[]"]').val();
			var txtAmount=$(this).find('input[name="txtAmount[]"]').val();
			var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
			var txtTagMaterials=$(this).find('input[name="txtTagMaterials[]"]').val();
			var txtRowId=$(this).find('input[name="txtRowId[]"]').val();				

			if(txtQnty>0 && cboServiceFor>0 && txtServiceDetails!="")
			{
				j++;
				dataString+='&cboServiceFor_' + j + '=' + cboServiceFor + '&txtServiceDetails_' + j + '=' + txtServiceDetails  + '&cboUom_' + j + '=' + cboUom + '&cboItemCategory_' + j + '=' + cboItemCategory + '&txtQnty_' + j + '=' + txtQnty + '&txtRate_' + j + '=' + txtRate + '&txtAmount_' + j + '=' + txtAmount + '&txtRemarks_' + j + '=' + txtRemarks + '&txtTagMaterials_' + j + '=' + txtTagMaterials + '&txtItemId_' + j + '=' + txtItemId + '&txtRowId_' + j + '=' + txtRowId+ '&hdnServiceId_' + j + '=' + hdnServiceId;
			}

		});


		var data="action=save_update_delete&operation="+operation+'&total_row='+j+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_division_name*cbo_department_name*cbo_section_zname*txt_req_date*cbo_store_name*cbo_pay_mode*txt_req_by*cbo_currency*txt_delivery_date*cbo_ready_to_approved*txt_manual_req_no*txt_remarks*txt_req_no*update_id*txt_tag_req*txt_tag_req_id',"../../")+dataString;
		// alert(data);return;	
		freeze_window(operation);

		http.open("POST","requires/service_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_service_req_reponse;
	}

	function fnc_service_req_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==11)
			{
				alert('Update or Delete not allowed WO('+reponse[1]+') found');
				release_freezing();
				return;
			}
			if(reponse[0]==13)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==12)
			{
				alert('Amount can not be less than bill amount ('+reponse[1]+')');
				release_freezing();
				return;
			}
			show_msg(reponse[0]);			
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('txt_req_no').value = reponse[1];
				var check_system_id=$("#update_id").val();
				document.getElementById('update_id').value = reponse[2];
				if (check_system_id=="") uploadFile( $("#update_id").val());
				disable_enable_fields('cbo_company_name*cbo_location_name',1);
				show_list_view(reponse[2],'show_dtls_listview_update','details_part_list','requires/service_requisition_controller','');
				set_button_status(1, permission, 'fnc_service_req_entry',1,0);
			}
			if(reponse[0]==2)
			{
				reset_form('serviceRequisition_1','','','','disable_enable_fields(\'cbo_company_name*cbo_location_name\',0);','');
			}
						
			release_freezing();
		}
	}

	function uploadFile(mst_id)
	{
		$(document).ready(function() { 
			 
			var suc=0;
			var fail=0;
			for( var i = 0 ; i < $("#multiple_file_field")[0].files.length ; i++)
			{
				var fd = new FormData();
				console.log($("#multiple_file_field")[0].files[i]);
				var files = $("#multiple_file_field")[0].files[i]; 
				fd.append('file', files); 
				$.ajax({
					url: 'requires/service_requisition_controller.php?action=file_upload&mst_id='+ mst_id, 
					type: 'post', 
					data:fd, 
					contentType: false, 
					processData: false, 
					success: function(response){
						var res=response.split('**');
						if(res[0] == 0){ 
							
							suc++;
						}
						else if(fail==0)
						{
							alert('file not uploaded');
							fail++;
						}
					}, 
				}); 
			}

			if(suc > 0 )
			{
				 document.getElementById('multiple_file_field').value='';
			}
		}); 
	}

    function print_report_button_setting(report_ids)
    {
        var report_id=report_ids.split(",");
        for (var k=0; k<report_id.length; k++)
        {
            if(report_id[k]==109)
            {
                $('#button_data_panel')
                    .append( '<input type="button"  id="show_button" class="formbutton" style="width:80px; text-align:center;" value="Print"  name="Po_print"  onClick="fn_report_generated(1)" />&nbsp;&nbsp;' );
            }
            if(report_id[k]==110)
            {
                $('#button_data_panel').append( '<input type="button"  id="show_button2" class="formbutton" style="width:80px; text-align:center;" value="Print 2"  name="print_2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;' );
            }

			if(report_id[k]==732)
            {
                $('#button_data_panel').append( '<input type="button"  id="show_button3" class="formbutton" style="width:80px; text-align:center;" value="PO Print"  name="po_print"  onClick="fn_report_generated(3)" />&nbsp;&nbsp;' );
            }
        }
    }

	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtRemarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/service_requisition_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=450px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks").value;
			if (theemail!="")
			{
				$('#txtRemarks_'+id).val(theemail);
			}
		}
	}

    function openmypage_req()
	{

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company = $("#cbo_company_name").val();

		var page_link = 'requires/service_requisition_controller.php?action=req_popup&company='+company;
		var title = "Service Requisition No Search";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");

			$("#txt_req_no").val(hidden_wo_number[0]);
			$("#update_id").val(hidden_wo_number[1]);
			get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/service_requisition_controller" );
			disable_enable_fields( 'cbo_company_name*cbo_location_name', 1, '', '' );
			show_list_view(hidden_wo_number[1],'show_dtls_listview_update','details_part_list','requires/service_requisition_controller','');
			set_button_status(1, permission, 'fnc_service_req_entry',1);
		}
	}

    function openmypage_tag_req()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var company = $("#cbo_company_name").val();

        var page_link = 'requires/service_requisition_controller.php?action=tag_req_popup&company='+company;
        var title = "Requisition No Search";

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var txt_selected_numbers=this.contentDoc.getElementById("txt_selected_numbers").value.split("_");
            var txt_selected_ids=this.contentDoc.getElementById("txt_selected_ids").value.split("_");

            $("#txt_tag_req").val(txt_selected_numbers);
            $("#txt_tag_req_id").val(txt_selected_ids);
        }
    }

	function itemDetailsPopup(k)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
        var rowIdContainer = [];
        $("#tbl_dtls_item tbody tr").each(function (index){
            rowIdContainer.push($(this).attr('id').replace('tr_', ''));
        });

		var tot_row = Math.max(...rowIdContainer);
		var cboServiceFor=$("#cboServiceFor_"+k).val();
		var txtServiceDetails=$("#txtServiceDetails_"+k).val();

		var page_link = 'requires/service_requisition_controller.php?action=item_description_popup&company='+company;
		var title = 'Search Item Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_1").value;

	 		if(theemail!="")
			{

	 			var data=theemail+"**"+company+"**"+(tot_row);
				var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form_itemDtls', '', 'requires/service_requisition_controller');
				$("#tbl_dtls_item tbody tr:last").remove();
				$("#tbl_dtls_item tbody:last").append(list_view_orders);
				$("#cboServiceFor_"+k).val(cboServiceFor);
				$("#txtServiceDetails_"+k).val(txtServiceDetails);

                // $("#tbl_dtls_item tbody tr#tr_"+k).after(list_view_orders);
                // $("#tbl_dtls_item tbody tr#tr_"+k).remove();
				// $("#cboServiceFor_"+(parseInt(tot_row)+1)).val(cboServiceFor);
				// $("#txtServiceDetails_"+(parseInt(tot_row)+1)).val(txtServiceDetails);
				// set_all_onclick();
			}
			release_freezing();
		}
	}

	function openmypage_not_approve_cause()
	{
		if (form_validation('txt_req_no','Req. Number')==false)
		{
			return;
		}
		
		var txt_not_approve_cause=document.getElementById('txt_not_approve_cause').value;
		
		
		var data=txt_not_approve_cause;
		
		var title = 'Not Appv. Cause';	
		var page_link = 'requires/service_requisition_controller.php?data='+data+'&action=not_approve_cause_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			
		}
	}

	function fncServiceDetails(k)
	{
		if (form_validation('cboServiceFor_'+k,'Service For')==false)
		{
			return;
		}
		var service_details=$("#txtServiceDetails_"+k).val();
		var page_link = 'requires/service_requisition_controller.php?action=service_details_popup&service_details='+service_details;
		var title = 'Service Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=280px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var hdnServiceId=this.contentDoc.getElementById("hdnServiceId").value;
			var hdnServiceName=this.contentDoc.getElementById("hdnServiceName").value;
			if(hdnServiceId=="")
			{
				var hdnServiceName=this.contentDoc.getElementById("txt_service_details").value;
				$("#txtServiceDetails_"+k).val(hdnServiceName);
				$("#hdnServiceId"+k).val(0);
			}
			else
			{
				var service_for=$("#cboServiceFor_"+k).val();
				var data=k+"**"+hdnServiceId+"**"+service_for;
				var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_lib', '', 'requires/service_requisition_controller');
				//alert(list_view_orders);return;
				$("#tbl_dtls_item tbody tr:last").remove();
				$("#tbl_dtls_item tbody:last").append(list_view_orders);
				set_all_onclick();
			}
		}
	}
	
	function fncItemTagMaterials(k)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var prod_id = $("#txtTagMaterials_"+k).val();
		var page_link = 'requires/service_requisition_controller.php?action=tag_materials_popup&company='+company+'&prod_id='+prod_id;
		var title = 'Search Item Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_selected_ids").value;
			var item_name=this.contentDoc.getElementById("txt_selected_numbers").value;
	 		if(theemail!="")
			{
                $("#txtTagMaterials_"+k).val(theemail);
                $("#txtTagMaterialsName_"+k).val(item_name);
			}
		}
	}

	function add_dtls_tr(i) 
	{
        $('#tbl_dtls_item tbody').find('#first_row_add').remove();
		var row_num = $('#tbl_dtls_item tbody tr').length;
        if(row_num == 0)
		{
            var responseHtml = return_ajax_request_value(i, 'append_load_details_container', 'requires/service_requisition_controller');

            $("#tbl_dtls_item tbody").append(responseHtml);
            $('#increasehd_'+i).removeAttr("value").attr("value","+");
            $('#decreasehd_'+i).removeAttr("value").attr("value","-");
            $('#increasehd_'+i).removeAttr("onclick").attr("onclick","add_dtls_tr("+i+");");
            $('#decreasehd_'+i).removeAttr("onclick").attr("onclick","fnc_delet_dtls_tr("+i+");");

            // set_all_onclick();
        }
		else 
		{
            var lastId = $('#tbl_dtls_item tbody tr:last').attr('id');
            if (lastId !==  'tr_'+i) {
                return false;
            } else {
                var rowIdContainer = [];
                $("#tbl_dtls_item tbody tr").each(function (index){
                    rowIdContainer.push($(this).attr('id').replace('tr_', ''));
                });
                var i = Math.max(...rowIdContainer);
                i++;
                var responseHtml = return_ajax_request_value(i, 'append_load_details_container', 'requires/service_requisition_controller');
                $("#tbl_dtls_item tbody").append(responseHtml);

                $('#increasehd_' + i).removeAttr("value").attr("value", "+");
                $('#decreasehd_' + i).removeAttr("value").attr("value", "-");
                $('#increasehd_' + i).removeAttr("onclick").attr("onclick", "add_dtls_tr(" + i + ");");
                $('#decreasehd_' + i).removeAttr("onclick").attr("onclick", "fnc_delet_dtls_tr(" + i + ");");

                // set_all_onclick();
            }
        }
	}

	function fnc_delet_dtls_tr(i)
	{ 
		var selected_delete_id = new Array();

		var numRow = $('#tbl_dtls_item tbody tr').length;
		if(numRow === 1) {
            $('#tbl_dtls_item tbody').find('#tr_'+i).remove();
            var countColumn = $('#tbl_dtls_item thead tr:first').find('th').length;
            $('#tbl_dtls_item tbody').append('<tr id="first_row_add"><td colspan="'+countColumn+'" style="text-align: center;"><input type="button" value="Add Row" name="addrow" onclick="add_dtls_tr(1)" style="width:80px" class="formbutton"></td></tr>');
		}else{
            $('#tbl_dtls_item tbody').find('#tr_'+i).remove();
        }
	}

	function fn_report_generated(type)
	{
		if (form_validation('txt_req_no','Requisition No')==false)
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		if(type==1)
		{
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val(), "service_requisition_print", "requires/service_requisition_controller" );
		}
        if(type==2)
        {
            print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val(), "service_requisition_print2", "requires/service_requisition_controller" );
        }
		if(type==3)
		{
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val(), "service_requisition_po_print", "requires/service_requisition_controller" );
		}
	}

	function calculate_amount(i)
	{
        var quantity_val=parseFloat(Number($('#txtQnty_'+i).val()));
        var rate_val=parseFloat(Number($('#txtRate_'+i).val()));
        var attached_val=quantity_val*rate_val;
        document.getElementById('txtAmount_'+i).value = number_format (attached_val, 2,'.',"");
	}
    function print_button_setting(company)
    {
        $('#button_data_panel').html('');
        get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/service_requisition_controller' );
    }
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1150px;">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    </div>
		<fieldset style="width:1150px">
			<legend>General Service Requisition</legend>
			<form name="serviceRequisition_1" id="serviceRequisition_1" method="" >
				<table cellpadding="0" cellspacing="2" width="900" border="1" rules="all">
					<input type="hidden" name="is_approved" id="is_approved" value="">
					<tr>
					    <td colspan="3" align="right">Requisition No&nbsp;</td>
					    <td colspan="3" align="left">
							<input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:150px" placeholder="Double Click to Search" onDblClick="openmypage_req();" readonly />
                        	<input type="hidden" id="update_id" name="update_id">
                        </td>
				    </tr>
					<tr>
						<td width="90" class="must_entry_caption" align="right">Company Name&nbsp;</td>
						<td width="150">
                        	<?
							   	echo create_drop_down( "cbo_company_name", 162, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/service_requisition_controller', this.value, 'load_drop_down_location','location_td');load_drop_down( 'requires/service_requisition_controller', this.value, 'load_drop_down_division','division_td');print_button_setting(this.value);");
 							?>
						</td>
                        <td width="90" class="must_entry_caption" align="right">Location&nbsp;</td>
						<td width="150" id="location_td">
                        	<? echo create_drop_down( "cbo_location_name", 162, $blank_array, "", 1, "-- Select --", $selected, "" ); ?>
                        </td>
                        <td width="90" align="right">For Division&nbsp;</td>
						<td width="150" id="division_td">
                        	<? echo create_drop_down( "cbo_division_name", 162,$blank_array,"", 1, "-- Select --"); ?>
                        </td>
					</tr>
					<tr>
						<td width="90" align="right">For Department&nbsp;</td>
						<td width="150" id="department_td">
                        	<? echo create_drop_down( "cbo_department_name", 162,$blank_array,"", 1, "-- Select --" ); ?>
                        </td>
						<td width="90" align="right">For Section&nbsp;</td>
						<td width="150" id="section_td">
                        	<? echo create_drop_down( "cbo_section_zname", 162,$blank_array,"", 1, "-- Select --" ); ?>
                        </td>
						 <td width="90" class="must_entry_caption" align="right">Req. Date&nbsp;</td>
						<td width="150">
						  	<input type="text" id="txt_req_date" name="txt_req_date" class="datepicker" style="width:150px" value="<? echo date("d-m-Y");?>" />
						</td>
					</tr>
					<tr>
						<td align="right">Store Name&nbsp;</td>
						<td id="store_td">
							<? echo create_drop_down( "cbo_store_name", 162,$blank_array,"", 1, "-- Select --" ); ?> 	
 						</td>
						<td align="right">Pay Mode&nbsp;</td>
						<td>
							<? echo create_drop_down( "cbo_pay_mode", 162, $pay_mode,"", 1, "-- Select --", 0, "",0 ); ?> 						
 						</td>
						<td align="right">Req. By&nbsp;</td>
						<td>
							<input type="text" name="txt_req_by" id="txt_req_by" style="width:150px" class="text_boxes" />
						</td>
					</tr>
					<tr>
						<td align="right" >Currency&nbsp;</td>
						<td>
							<? echo create_drop_down( "cbo_currency", 162, $currency, "", 1, "-- Select --", 1,""  ); ?>
						</td>
						<td align="right">Delivery Date&nbsp;</td>
						<td>
							<input type="text" id="txt_delivery_date" name="txt_delivery_date" class="datepicker" style="width:150px" value="" />
						</td>
						<td align="right">Ready to Approve&nbsp;</td>
                        <td>
	                        <? echo create_drop_down( "cbo_ready_to_approved", 162, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?>
                        </td>
					</tr>
					<tr>
						<td align="right">Manual Req. No.&nbsp;</td>
						<td>
							<input type="text" name="txt_manual_req_no" id="txt_manual_req_no" style="width:150px" class="text_boxes" />
						</td>
						<td align="right">Un-approve request&nbsp;</td> 
						<td>
							<input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Browse" ID="txt_un_appv_request" style="width:150px" disabled="disabled">
						</td>
						<td align="right">Not Appv. Cause&nbsp;</td>
						<td>
							<Input name="txt_not_approve_cause" class="text_boxes" readonly placeholder="Double Click for Browse" id="txt_not_approve_cause" style="width:150px" onClick="openmypage_not_approve_cause()">
						</td>
					</tr>
					<tr>
						<td align="right">Remarks&nbsp;</td>
						<td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" style="width:470px" class="text_boxes" /></td>
                        <td align="right">Tag Purchase Req.&nbsp;</td>
                        <td>
                            <input type="text" name="txt_tag_req" id="txt_tag_req" style="width:150px" class="text_boxes"  onDblClick="openmypage_tag_req()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="txt_tag_req_id" id="txt_tag_req_id" style="width:150px" class="text_boxes" />
                        </td>
					</tr>
					<tr>
						<td align="right">File&nbsp;</td>
						<td>
							<input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:130px">

							<td><input type="button" class="image_uploader" style="width:110px" maxlength="300" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'service_requisition', 2 ,1)"></td>
						</td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>
					</tr>
                </table>
                <br />
                <table class="rpt_table" width="1150" cellspacing="0" cellpadding="0" id="tbl_dtls_item" border="1" rules="all">
                    <thead>
						<tr>
							<th width="90" class="must_entry_caption">Service For</th>
							<th width="80" class="must_entry_caption">Service Details</th>
							<th width="130">Item Description</th>
							<th width="100">Item Category</th>
							<th width="100">Item Group</th>
							<th width="70">Service UOM</th>
							<th width="60" class="must_entry_caption">Qnty</th>
							<th width="60">Rate</th>
							<th width="60">Amount</th>
							<th width="120">Remarks</th>
							<th width="80">Tag Materials</th>
							<th >Action</th>
						</tr>
                    </thead>
                    <tbody id="details_part_list">
                        <tr class="general" id="tr_1">
                            <td>
	                            <?								    
								   	echo create_drop_down( "cboServiceFor_1", 90, $service_for_arr, "", 1, "-- Select --",$selected, 0, "", "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_1" );							   	
	 							?>
                            </td>
                            <td align="center">
								<input type="text" name="txtServiceDetails[]" id="txtServiceDetails_1" class="text_boxes" style="width:90px;" onDblClick="fncServiceDetails(1)" placeholder="Double Click To Search" readonly/>
                                <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_1" />             
                            </td>
                            <td align="center">
								<input type="text" name="txtItemDescription[]" id="txtItemDescription_1" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(1)" placeholder="Double Click To Search" readonly />
								<input type="hidden" name="txtItemId[]" id="txtItemId_1" />
								<input type="hidden" name="txtRowId[]" id="txtRowId_1" value="" />
                            </td>
                            <td align="center">
                            	<? echo create_drop_down( "cboItemCategory_1", 120, $blank_array,"", 1, "--Select--", 0, "", 1, "", "", "", "", "", "", "cboItemCategory[]", "cboItemCategory_1" ); ?>
                            </td>
                            <td align="center">
                            	<? echo create_drop_down( "cboItemGroup_1", 120, $blank_array,"", 1, "--Select--", 0, "",1, "", "", "", "", "", "", "cboItemGroup[]", "cboItemGroup_1" ); ?>
                            </td>
                            <td align="center">
                                <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", 0, "",0, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
                            </td>
                            <td>
                                <input type="text" name="txtQnty[]" id="txtQnty_1" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(1)"/>
                            </td>
							<td>
								<input type="text" name="txtRate[]" id="txtRate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
							</td>
							<td><input type="text" name="txtAmount[]" id="txtAmount_1" class="text_boxes_numeric" style="width:60px;" readonly /></td>
                            <td><input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:120px;" onDblClick="openmypage_remarks(1)"/></td>
                            <td>
								<input type="text" name="txtTagMaterialsName[]" id="txtTagMaterialsName_1" class="text_boxes" style="width:90px;" onDblClick="fncItemTagMaterials(1)" placeholder="Double Click To Search" readonly/>  
								<input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_1" readonly/>  
							</td>
                            <td>
								<input type="button" name="increase[]" id="increase_1" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
								<input type="button" name="decrease[]" id="decrease_1" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
							</td>
                        </tr>
                    </tbody>
                </table>
				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container">
				  		<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
						  <? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
							<?
								echo load_submit_buttons( $permission, "fnc_service_req_entry", 0,0,"reset_form('serviceRequisition_1','','','','disable_enable_fields(\'cbo_company_name*cbo_location_name\',0);','');",1);
							?>
							<span id="button_data_panel"></span>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
    </div>
</body> 
<!-- For prevent special charecter start -->
<!-- <script>
	$('input').bind('input', function() {
	var c = this.selectionStart,
		r = /[^a-z0-9 .]/gi,
		v = $(this).val();
	if(r.test(v)) {
		$(this).val(v.replace(r, ''));
		c--;
	}
	this.setSelectionRange(c, c);
	});
</script> -->
<!-- For prevent special charecter end-->
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
