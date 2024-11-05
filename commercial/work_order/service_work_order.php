<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Service Work Order
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	14-06-2021
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
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Service Work Order Info","../../", 1, 1, $unicode);
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

	function fnc_service_work_order_entry( operation )
	{

		if (form_validation('cbo_company_name*cbo_location_name*cbo_wo_basis*txt_wo_date*cbo_supplier*cbo_pay_mode*cbo_currency*txt_exchange_rate','Company Name*Location*Basis*WO Date*Supplier Name*Pay Mode*Currency*Exchange Rate')==false)
		{
			return;
		}

		try
		{
			var row = $("#tbl_dtls_item tbody tr:last").attr('id').replace('tr_', '');
			if(row<=0) throw "Save Not Possible!!Input Item Details For Save";
		}
		catch(err)
		{
			alert("Error : "+err);
			return;
		}

		var row_num = $('#tbl_dtls_item tbody tr').length;
		//alert(row_num);
		/* $('#tbl_dtls_item tbody tr').each(function (index){
            var i = $(this).attr('id').replace('tr_', '');
            if (form_validation('cboServiceFor_'+i+'*txtServiceDetails_'+i+'*txtqnty_'+i+'*txtrate_'+i,'Service For*Service Details*Qnty*Rate')==false)
            {
                return;
            }
        }); */

		/* for (var j=1; j<=row_num; j++)
		{
			if (form_validation('cboServiceFor_'+j+'*txtServiceDetails_'+j+'*txtqnty_'+j+'*txtrate_'+j,'Service For*Service Details*Qnty*Rate')==false)
			{
				return;
			}
		} */

		var j=0; var dataString=''; check_field=0;
		$("#tbl_dtls_item").find('tbody tr').each(function()
		{
			var cboServiceFor=$(this).find('select[name="cboServiceFor[]"]').val();
			var txtServiceDetails=$(this).find('input[name="txtServiceDetails[]"]').val();
			var hdnServiceId=$(this).find('input[name="hdnServiceId[]"]').val();
			var txtItemDescription=$(this).find('input[name="txtItemDescription[]"]').val();
			var txt_item_id=$(this).find('input[name="txt_item_id[]"]').val();
			var cboItemCategory=$(this).find('select[name="cboItemCategory[]"]').val();

			var cboItemGroup=$(this).find('select[name="cboItemGroup[]"]').val();
			var cboMachineCategory=$(this).find('select[name="cboMachineCategory[]"]').val();
			var cboMachineNo=$(this).find('select[name="cboMachineNo[]"]').val();

			var cboUom=$(this).find('select[name="cboUom[]"]').val();
			var txtqnty=$(this).find('input[name="txtqnty[]"]').val();
			var txtrate=$(this).find('input[name="txtrate[]"]').val();
			var txtamount=$(this).find('input[name="txtamount[]"]').val();
            var txt_service_number=$(this).find('input[name="txt_service_number[]"]').val();
			var txtremarks=$(this).find('input[name="txtremarks[]"]').val();
			var txt_row_id=$(this).find('input[name="txt_row_id[]"]').val();
			var txt_req_no_id=$(this).find('input[name="txt_req_no_id[]"]').val();
			var txt_req_dtls_id=$(this).find('input[name="txt_req_dtls_id[]"]').val();
			var txtTagMaterials=$(this).find('input[name="txtTagMaterials[]"]').val();

			if( txtqnty<=0 || txtqnty=="" || txtrate<=0 || txtrate=="" || cboServiceFor==0 || txtServiceDetails=="")
			{
				if(cboServiceFor==0){
					alert('Please Select Service For');
					check_field=1 ; return;
				}else if(txtServiceDetails==""){
					alert('Please Please Fill up Service Details');
					check_field=1 ; return;
				}else if(txtqnty<=0 || txtqnty==""){
					alert('Please Please Fill up Qnty');
					check_field=1 ; return;
				}else if(txtrate<=0 || txtrate==""){
					alert('Please Please Fill up Rate');
					check_field=1 ; return;
				}
			}


			if(check_field==0)
			{
				j++;
				dataString+='&cboServiceFor_' + j + '=' + cboServiceFor + '&txtServiceDetails_' + j + '=' + txtServiceDetails + '&hdnServiceId_' + j + '=' + hdnServiceId  +'&txtItemDescription_' + j + '=' + txtItemDescription + '&cboItemCategory_' + j + '=' + cboItemCategory+ '&cboItemGroup_' + j + '=' + cboItemGroup + '&cboUom_' + j + '=' + cboUom + '&txtqnty_' + j + '=' + txtqnty  + '&txtrate_' + j + '=' + txtrate+ '&txtamount_' + j + '=' + txtamount + '&txt_service_number_' + j + '=' + txt_service_number + '&txtremarks_' + j + '=' + txtremarks + '&txt_item_id_' + j + '=' + txt_item_id + '&txt_row_id_' + j + '=' + txt_row_id+ '&txt_req_no_id_' + j + '=' + txt_req_no_id + '&txt_req_dtls_id_' + j + '=' + txt_req_dtls_id + '&txtTagMaterials_' + j + '=' + txtTagMaterials + '&cboMachineCategory_' + j + '=' + cboMachineCategory + '&cboMachineNo_' + j + '=' + cboMachineNo;
			}
		});

		if(check_field==0)
		{
			var data="action=save_update_delete&operation="+operation+'&total_row='+j+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_wo_basis*txt_wo_date*txt_req_numbers*txt_req_numbers_id*cbo_supplier*txt_attention*txt_attention_to*cbo_pay_mode*cbo_source*txt_tenor*txt_delivery_date*cbo_currency*txt_exchange_rate*txt_tag_req*txt_tag_req_id*txt_quot_ref*txt_quot_date*cbo_ready_to_approved*cbo_fixed_asset*txt_entry_no*txt_scope_beneficiary*txt_scope_service_provider*txt_wo_number*update_id*cbo_pay_term*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*txt_up_remarks*txt_discount_remarks*txt_remarks*cbo_division_name*cbo_department_name*cbo_section_name*cbo_lc_type*txt_place_of_delivery*hidden_delivery_info_dtls',"../../")+dataString;
			// alert(data);return;
			freeze_window(operation);

			http.open("POST","requires/service_work_order_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_service_work_order_reponse;
		}
		else
		{
			return;
		}
	}

	function fnc_service_work_order_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==11)
			{
				alert('Delete not allowed WO('+reponse[1]+') found In Next Transaction');
				release_freezing();
				return;
			}
			else if(reponse[0]==12)
			{
				alert('Amount can not be less than bill amount ('+reponse[1]+')');
				release_freezing();
				return;
			}
			if(reponse[0]==20){
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			/*document.getElementById('txt_wo_number').value = reponse[1];
			document.getElementById('update_id').value = reponse[2];
			disable_enable_fields('cbo_company_name*cbo_supplier*cbo_location_name*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_wo_date*cbo_source',1);
			show_list_view(reponse[2],'show_dtls_listview_update','details_part_list','requires/service_work_order_controller','');
			set_button_status(1, permission, 'fnc_service_work_order_entry',1,0);*/
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('txt_wo_number').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];
				var wo_basis=$("#cbo_wo_basis").val();
				disable_enable_fields('cbo_company_name*cbo_location_name*cbo_wo_basis*txt_exchange_rate*txt_wo_date*cbo_source',1);
				show_list_view(reponse[2]+"**"+wo_basis,'show_dtls_listview_update','details_container','requires/service_work_order_controller','');
				set_button_status(1, permission, 'fnc_service_work_order_entry',1,0);
			}

			if(reponse[0]==50)
            {
                alert(reponse[1]);release_freezing();return;
            }

			if(reponse[0]==2)
			{
				reset_form('serviceWorkOrder_1','','','','disable_enable_fields(\'cbo_company_name*cbo_location_name*cbo_wo_basis*txt_exchange_rate*txt_wo_date*cbo_source\',0);','');
			}
			release_freezing();
		}
	}

	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtremarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/service_work_order_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=450px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks").value;
			if (theemail!="")
			{
				$('#txtremarks_'+id).val(theemail);
			}
		}
	}

	function openmypage_wo()
	{

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company = $("#cbo_company_name").val();

		var page_link = 'requires/service_work_order_controller.php?action=wo_popup&company='+company;
		var title = "WO Search";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");
			//alert(hidden_wo_number[0]);alert(hidden_wo_number[1]);
			//reset_form('serviceWorkOrder_1','details_part_list','','','','');
			$("#txt_wo_number").val(hidden_wo_number[0]);
			$("#update_id").val(hidden_wo_number[1]);
			get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/service_work_order_controller" );
			var wo_basis=$("#cbo_wo_basis").val();
			disable_enable_fields( 'cbo_company_name*cbo_location_name*cbo_wo_basis*txt_exchange_rate*txt_wo_date*cbo_source', 1, '', '' );
			// show_list_view(hidden_wo_number[1],'show_dtls_listview_update','details_part_list','requires/service_work_order_controller','');
			show_list_view(hidden_wo_number[1]+"**"+wo_basis,'show_dtls_listview_update','details_container','requires/service_work_order_controller','');

			const comId = $("#cbo_company_name").val();
			load_drop_down( 'requires/service_work_order_controller',comId+'*'+hidden_wo_number[1], 'load_drop_down_supplier_new', 'cbo_supplier' );

			
			set_button_status(1, permission, 'fnc_service_work_order_entry',1);

			//release_freezing();
		}
	}

	function calculate_amount(i)
	{
        var quantity_val=parseFloat(Number($('#txtqnty_'+i).val()));
        var rate_val=parseFloat(Number($('#txtrate_'+i).val()));
        var attached_val=quantity_val*rate_val;
        document.getElementById('txtamount_'+i).value = number_format (attached_val, 2,'.',"");
		calculate_total_amount(1);
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

		var page_link = 'requires/service_work_order_controller.php?action=item_description_popup&company='+company;
		var title = 'Search Item Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_1").value;

	 		if(theemail!="")
			{

	 			var data=theemail+"**"+company+"**"+(parseInt(tot_row));
				var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form_itemDtls', '', 'requires/service_work_order_controller');
	  			 //alert (list_view_orders);
				/*$("#tbl_details tbody tr").remove();
	 			$("#tbl_details tbody").append(list_view_orders);*/
                // $("#tbl_dtls_item tbody tr#tr_"+k).after(list_view_orders);
                // $("#tbl_dtls_item tbody tr#tr_"+k).remove();
				// $("#tbl_dtls_item tbody").append(list_view_orders);
				$("#tbl_dtls_item tbody tr:last").remove();
				$("#tbl_dtls_item tbody:last").append(list_view_orders);

				// $("#cboServiceFor_"+(parseInt(tot_row)+1)).val(cboServiceFor);
				// $("#txtServiceDetails_"+(parseInt(tot_row)+1)).val(txtServiceDetails);
				$("#cboServiceFor_"+(parseInt(tot_row)+0)).val(cboServiceFor);
				$("#txtServiceDetails_"+(parseInt(tot_row)+0)).val(txtServiceDetails);
				set_all_onclick();
			}
			release_freezing();
		}
	}


	function add_dtls_tr(i)
	{
        $('#tbl_dtls_item tbody').find('#first_row_add').remove();
		var row_num = $('#tbl_dtls_item tbody tr').length;
        if(row_num == 0){
            var responseHtml = return_ajax_request_value(i, 'append_load_details_container', 'requires/service_work_order_controller');

            $("#tbl_dtls_item tbody").append(responseHtml);
            $('#increasehd_'+i).removeAttr("value").attr("value","+");
            $('#decreasehd_'+i).removeAttr("value").attr("value","-");
            $('#increasehd_'+i).removeAttr("onclick").attr("onclick","add_dtls_tr("+i+");");
            $('#decreasehd_'+i).removeAttr("onclick").attr("onclick","fnc_delet_dtls_tr("+i+");");

            set_all_onclick();
        }else {
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
                var responseHtml = return_ajax_request_value(i, 'append_load_details_container', 'requires/service_work_order_controller');
                $("#tbl_dtls_item tbody").append(responseHtml);

                $('#increasehd_' + i).removeAttr("value").attr("value", "+");
                $('#decreasehd_' + i).removeAttr("value").attr("value", "-");
                $('#increasehd_' + i).removeAttr("onclick").attr("onclick", "add_dtls_tr(" + i + ");");
                $('#decreasehd_' + i).removeAttr("onclick").attr("onclick", "fnc_delet_dtls_tr(" + i + ");");

                set_all_onclick();
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

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var wo_date = $('#txt_wo_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+wo_date, 'check_conversion_rate', '', 'requires/service_work_order_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
		$('#txt_exchange_rate').attr('disabled','disabled');
	}

	function is_text_field()
	{
		var cbo_fixed_asset = $("#cbo_fixed_asset").val();
		if (cbo_fixed_asset == 1)
		{
			$("#asset_no_td").removeAttr("style");
			$("#asset_no_val_td").removeAttr("style");
		}
		else
		{
			$("#asset_no_td").css("display", "none");
			$("#asset_no_val_td").css("display", "none");
		}
	}

	function search_asset()
	{
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/service_work_order_controller.php?action=search_asset_entry' + '&cbo_company_name=' + $('#cbo_company_name').val(), 'Asset Acquisition Search', 'width=1085px,height=400px,center=1,resize=0,scrolling=0', '../')
		emailwindow.onclose = function ()
		{
			var theform = this.contentDoc.forms[0];
			var data = this.contentDoc.getElementById("hidden_system_number").value;
			$("#txt_entry_no").val(data);
			//alert(data);
			//get_php_form_data(data, "populate_asset_details_form_data", "requires/asset_acquisition_controller");
			//show_list_view(data, 'show_asset_active_listview', 'asset_list_view', 'requires/asset_acquisition_controller', 'setFilterGrid(\'list_view\',-1)');
		}
	}

	function fn_report_generated(type)
	{
		if ( $('#txt_wo_number').val()=='')
		{
			alert ('Work Order No Not Save.');
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		if(type==1)
		{
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val(), "service_work_order_print", "requires/service_work_order_controller" );
		}
		if(type==2)
		{
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val(), "service_work_order_po_print", "requires/service_work_order_controller" );
		}
		if(type==3)
		{
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_lc_type').val()+'*'+$('#cbo_pay_term').val(), "service_work_order_print_2", "requires/service_work_order_controller" );
		}
		if(type==4)
		{
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val(), "service_work_order_print_3", "requires/service_work_order_controller" );
		}

	}

	function openmypage_req()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var txt_req_numbers_id = $("#txt_req_numbers_id").val();
		var txt_req_numbers = $("#txt_req_numbers").val();

		var page_link = 'requires/service_work_order_controller.php?action=requitision_popup&company='+company+'&txt_req_numbers_id='+txt_req_numbers_id+'&txt_req_numbers='+txt_req_numbers;
		var title = "Requisition No Search";

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var txt_req_numbers=this.contentDoc.getElementById("hidden_req_number").value;
			var txt_req_numbers_id=this.contentDoc.getElementById("hidden_req_id").value;

			$("#txt_req_numbers").val(txt_req_numbers);
			$("#txt_req_numbers_id").val(txt_req_numbers_id);

			if(txt_req_numbers!="")
			{
				//freeze_window(5);
				var row = 0;
				var responseHtml = return_ajax_request_value(txt_req_numbers_id+'**'+row, 'show_dtls_listview', 'requires/service_work_order_controller');
				$('#tbl_dtls_item tr:not(:first)').remove();
				$("#tbl_dtls_item").append(responseHtml);
				calculate_total_amount(1);
				//release_freezing();
			}
			else
			{
				$("#details_container").html('');
			}
		}
	}

	function openmypage_tag_req()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();

		var page_link = 'requires/service_work_order_controller.php?action=tag_req_popup&company='+company;
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

	function fn_disable_enable(str)
	{
		if(str==1)
		{
			$("#txt_req_numbers").attr("disabled",false);
			$("#txt_tag_req").attr("disabled",false);
		}
		else
		{
			$("#txt_req_numbers_id").val('');
			$("#txt_req_numbers").val('');
			$("#txt_req_numbers").attr("disabled",true);
			$("#txt_tag_req_id").val('');
			$("#txt_tag_req").val('');
			$("#txt_tag_req").attr("disabled",true);
		}
	}

	function fnc_matrial_list(k)
	{
		var tagMaterials = $("#txtTagMaterials_"+k).val();
		var page_link = 'requires/service_work_order_controller.php?action=tag_materials_popup&tagMaterials='+tagMaterials;
		var title = 'Search Item Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}

	function fnc_scope()
	{
		var scope_beneficiary = $("#txt_scope_beneficiary").val();
		var scope_service_provider = $("#txt_scope_service_provider").val();
		var page_link = 'requires/service_work_order_controller.php?action=scope_popup&scope_beneficiary='+scope_beneficiary+'&scope_service_provider='+scope_service_provider;
		var title = 'Scope Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hdn_scope_beneficiary=this.contentDoc.getElementById("hdn_scope_beneficiary").value;
			var hdn_scope_service_provider=this.contentDoc.getElementById("hdn_scope_service_provider").value;
			$("#txt_scope_beneficiary").val(hdn_scope_beneficiary);
			$("#txt_scope_service_provider").val(hdn_scope_service_provider);
		}
	}

	/*function fnc_service_details(k)
	{
		var service_details=$("#txtServiceDetails_"+k).val();
		var page_link = 'requires/service_work_order_controller.php?action=service_details_popup&service_details='+service_details;
		var title = 'Service Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=280px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_service_details").value;
	 		if(theemail!="")
			{
                $("#txtServiceDetails_"+k).val(theemail);
			}
		}
	}*/
	
	function fnc_service_details(k)
	{
		if (form_validation('cboServiceFor_'+k,'Service For')==false)
		{
			return;
		}
		var service_details=$("#txtServiceDetails_"+k).val();
		var page_link = 'requires/service_work_order_controller.php?action=service_details_popup&service_details='+service_details;
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
				$("#hdnServiceId_"+k).val(0);
			}
			else
			{
				var service_for=$("#cboServiceFor_"+k).val();
				var data=k+"**"+hdnServiceId+"**"+service_for;
				var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_lib', '', 'requires/service_work_order_controller');
				//alert(list_view_orders);return;
				$("#tbl_dtls_item tbody tr:last").remove();
				$("#tbl_dtls_item tbody:last").append(list_view_orders);
				set_all_onclick();
			}
		}
	}

	function print_button_setting(company)
	{
		$('#button_data_panel').html('');
		// alert(company);
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/service_work_order_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{

			if(report_id[k]==86)
			{
				$('#button_data_panel')
					.append( '<input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="Print"  name="Po_print"  onClick="fn_report_generated(1)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==732)
			{
				$('#button_data_panel').append( '<input type="button"  id="show_button2" class="formbutton" style="width:100px; text-align:center;" value="PO Print"  name="Po_print"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==84)
			{
				$('#button_data_panel').append( '<input type="button"  id="show_button2" class="formbutton" style="width:100px; text-align:center;" value="Print 2"  name="Po_print"  onClick="fn_report_generated(3)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==85)
			{
				$('#button_data_panel').append( '<input type="button"  id="show_button3" class="formbutton" style="width:100px; text-align:center;" value="Print 3"  name="Po_print"  onClick="fn_report_generated(4)" />&nbsp;&nbsp;&nbsp;' );
			}


		}
	}

	function disable_enable_charge_remarks(type)
	{
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();
		if (type==1)
		{
			if (txt_upcharge != "") $('#txt_up_remarks').removeAttr('disabled');
			else $('#txt_up_remarks').val('').attr('disabled','disabled');

		}
		else if (type==2)
		{
			if (txt_discount != "") $('#txt_discount_remarks').removeAttr('disabled');
			else $('#txt_discount_remarks').val('').attr('disabled','disabled');
		}
	}

	function calculate_total_amount(type)
	{
		if(type==1)
		{
			var ddd={ dec_type:5, comma:0, currency:''}
			var numRow = $('table#tbl_dtls_item tbody tr').length;
			math_operation( "txt_total_amount", "txtamount_", "+", numRow,ddd );
		}

		var txt_total_amount=$('#txt_total_amount').val()*1;
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();

		if(txt_total_amount>0)
		{
			var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
			$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
		}
	}

	function fn_delivery_info()
   {
		var hidden_delivery_info_dtls=$('#hidden_delivery_info_dtls').val();
		var page_link='requires/dyes_and_chemical_work_order_controller.php?action=delivery_info_popup&hidden_delivery_info_dtls='+hidden_delivery_info_dtls;
		var title="Place Of Delivery Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=420px,height=250px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hdn_delivery_info_dtls").value;
			document.getElementById('hidden_delivery_info_dtls').value=theemail;
		}
   }

</script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
    <div style="width:1100px;">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
    </div>
		<fieldset style="width:1200px">
			<legend>General Service Work Order</legend>
			<form name="serviceWorkOrder_1" id="serviceWorkOrder_1" method="" >
				<table cellpadding="0" cellspacing="2" width="1150" border="1" rules="all">
					<input type="hidden" name="is_approved" id="is_approved" value="">
					<tr>
					    <td colspan="4" align="right">Work Order No&nbsp;</td>
					    <td colspan="4" ><input type="text" name="txt_wo_number" id="txt_wo_number" class="text_boxes" style="width:150px" placeholder="Double Click to Search" onDblClick="openmypage_wo();" readonly />
                        <input type="hidden" id="update_id" name="update_id">
                        </td>
				    </tr>
					<tr>
						<td width="90" class="must_entry_caption" align="right">Company Name&nbsp;</td>
						<td width="150">
						<input type="hidden" id="report_ids" >
                        	<?
							   	echo create_drop_down( "cbo_company_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/service_work_order_controller', this.value, 'load_drop_down_location','location_td');load_drop_down( 'requires/service_work_order_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );print_button_setting(this.value);load_drop_down( 'requires/service_work_order_controller', this.value, 'load_drop_down_division','division_td');");
 							?>
						</td>
                        <td width="90" class="must_entry_caption" align="right">Location&nbsp;</td>
						<td width="150" id="location_td">
                        	<?
							   	echo create_drop_down( "cbo_location_name", 162, $blank_array, "", 1, "-- Select --", $selected, "" );
 							?>
                        </td>
                        <td width="90" class="must_entry_caption" align="right">Basis&nbsp;</td>
						<td width="150" >
                        	<?
								echo create_drop_down( "cbo_wo_basis", 162, $wo_basis,"", 1, "-- Select --", 0, "fn_disable_enable(this.value);load_drop_down( 'requires/service_work_order_controller', $('#cbo_wo_basis').val(), 'load_details_container', 'details_container' );",0,'','','','3' );
 							?>
                        </td>
						<td width="90" class="must_entry_caption" align="right">WO Date&nbsp;</td>
						<td width="150">
						  	<input type="text" id="txt_wo_date" name="txt_wo_date" class="datepicker" style="width:150px" value="<? echo date("d-m-Y");?>" />
						</td>
					</tr>
					
					<tr>
                        <td align="right">Requisition No&nbsp;</td>
						<td>
							<input type="text" name="txt_req_numbers" id="txt_req_numbers" style="width:150px" class="text_boxes" placeholder="Double Click To Search" onDblClick="openmypage_req()" readonly disabled/>
							<input type="hidden" name="txt_req_numbers_id" id="txt_req_numbers_id" style="width:150px" class="text_boxes" />
						</td>
                        <td align="right">Attention By&nbsp;</td>
						<td><input type="text" name="txt_attention" id="txt_attention" style="width:150px" class="text_boxes" /></td>
						<td class="must_entry_caption" align="right">Supplier&nbsp;</td>
						<td id="supplier_td">
							<?
								echo create_drop_down( "cbo_supplier", 162, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
 							?>
 						</td>
                        <td align="right">Attention To&nbsp;</td>
						<td><input type="text" name="txt_attention_to" id="txt_attention_to" style="width:150px" class="text_boxes" /></td>
					</tr>
					<tr>
						<td align="right">Source&nbsp;</td>
						<td>
							<?
							   	echo create_drop_down( "cbo_source", 162, $source,"", 1, "-- Select --", 0, "",0 );
 							?>
 						</td>
						<td class="must_entry_caption" align="right">Pay Mode&nbsp;</td>
						<td>
							<?
							   echo create_drop_down( "cbo_pay_mode", 162, $pay_mode,"", 1, "-- Select --", 0, "",0 );
 							?>
 						</td>
                        <td align="right" class="must_entry_caption">Currency&nbsp;</td>
						<td>
							<?
								echo create_drop_down( "cbo_currency", 162, $currency, "", 1, "-- Select --", $currencyID, "check_exchange_rate();", "" );
								//echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select Currency --",$currencyID, "check_exchange_rate();",1 );
							?>
						</td>
						<td align="right" class="must_entry_caption">Exchange Rate&nbsp;</td>
						<td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:150px" readonly /></td>
					</tr>
					<tr>
						<td align="right">Tag Purchase Req.&nbsp;</td>
						<td>
							<input type="text" name="txt_tag_req" id="txt_tag_req" style="width:150px" class="text_boxes"  onDblClick="openmypage_tag_req()" placeholder="Double Click To Search" readonly disabled/>
							<input type="hidden" name="txt_tag_req_id" id="txt_tag_req_id" style="width:150px" class="text_boxes" />
						</td>
						<td align="right">Pay Term&nbsp;</td>
                        <td><?php echo create_drop_down("cbo_pay_term", 162, $pay_term, '', 1, '-- Select --', 0, "", 0, ''); ?></td>
						<td align="right">Tenor&nbsp;</td>
						<td><input type="text" name="txt_tenor" id="txt_tenor" class="text_boxes_numeric" style="width:150px"/></td>
						<td align="right">Delivery Date&nbsp;</td>
						<td><input type="text" id="txt_delivery_date" name="txt_delivery_date" class="datepicker" style="width:150px" value="" /></td>
					</tr>
					<tr>
						<td align="right">Fixed Assets&nbsp;</td>
                        <td id="fixed_asset_td">
	                        <?
	                        	//echo create_drop_down( "cbo_fixed_asset", 162, $issue_basis,"", 0, "-- Select--", 1, "fnc_booking_td_change()","","","","","3" );
	                        	echo create_drop_down( "cbo_fixed_asset", 162, $yes_no, "", 1, "-- Select--", 2, "is_text_field()","","" );
	                        ?>
                        </td>
						<td align="right">Quot. Ref.&nbsp;</td>
						<td><input type="text" name="txt_quot_ref" id="txt_quot_ref" class="text_boxes" style="width:150px"/></td>
						<td align="right">Quot. Date&nbsp;</td>
						<td><input type="text" name="txt_quot_date" id="txt_quot_date" class="datepicker" style="width:150px"/></td>
						<td align="right">Ready to Approve&nbsp;</td>
                        <td>
	                        <?
	                        	echo create_drop_down( "cbo_ready_to_approved", 162, $yes_no,"", 1, "-- Select--", "", "","","" );
	                        ?>
                        </td>
					</tr>
					<tr>
						<td align="right">For Division&nbsp;</td>
						<td id="division_td" width="150">
						<? 
							echo create_drop_down( "cbo_division_name", 162,$blank_array,"", 1, "-- Select --", $selected, "" );
						?> 	
						</td>
						<td align="right">For Department&nbsp;</td>
						<td width="150" id="department_td">
						<? 
							echo create_drop_down( "cbo_department_name", 162,$blank_array,"", 1, "-- Select --", $selected, "" );
						?> 	
						</td>
						<td align="right">For Section&nbsp;</td>
						<td width="150" id="section_td">
							<? 
								echo create_drop_down( "cbo_section_name", 162,$blank_array,"", 1, "-- Select --", $selected, "" );
							?> 	
						</td>
						<td align="right">L/C Type&nbsp;</td>
						<td width="150">
							<? 
								$lcDataArray=[4=>'TT/Pay Order',5=>'FDD/RTGS',6=>'FTT'];
								echo create_drop_down( "cbo_lc_type", 162,$lcDataArray,"", 1, "-- Select --", $selected, "" );
							?> 	
						</td>
					</tr>
					<tr>
						<td align="right">Remarks&nbsp;</td>
						<td colspan="2"><textarea name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:320px"/></textarea></td>
						<td>
							<input type='button' name="btn_scope"  id='btn_scope' class='image_uploader' style='width:160px;' value='Scope Popup' onClick="fnc_scope()" />
							<input type='hidden' name="txt_scope_beneficiary"  id='txt_scope_beneficiary' />
							<input type='hidden' name="txt_scope_service_provider"  id='txt_scope_service_provider' />
						</td>
						<td align="right">Terms & Condition/Notes&nbsp;</td>
						<td>
							<?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(484,'txt_wo_number','../../');
                            ?>
                        </td>
						<td align="right" id="asset_no_td" style="display: none;">Asset No&nbsp;</td>
                        <td id="asset_no_val_td" style="display: none;"><input type="text" name="txt_entry_no" id="txt_entry_no" class="text_boxes" style="width:150px" placeholder="Double Click To Search" onDblClick="search_asset()" readonly /></td>
						<td width="80">Place Of Delivery</td>
						<td width="">
							<input type="text" name="txt_place_of_delivery"  id="txt_place_of_delivery"  style="width:149px" class="text_boxes" onDblClick="fn_delivery_info()" placeholder="Write or Browse"/>
							<input type="hidden" name="hidden_delivery_info_dtls" id="hidden_delivery_info_dtls" />
						</td>
					</tr>
                </table>
                <br />
				<div style="width:1200px" id="details_container" align="center"></div>
				<table cellpadding="0" cellspacing="2" width="100%">
                	<tr>
				  		<td align="center" colspan="6" valign="middle" class="button_container">
				  		<div id="approved" style="float:left; font-size:24px; color:#FF0000; font-weight: bold;"></div>
						    <? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
							<?
								echo load_submit_buttons( $permission, "fnc_service_work_order_entry", 0,0 ,"reset_form('serviceWorkOrder_1','','','','','');",1);
							?>

						</td>

					</tr>
					<tr>
						<td id="button_data_panel" align="center"> </td>
				    </tr>
				</table>
			</form>
		</fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
		$("#cbo_division_name").val(0);
		
    });
</script>
</html>
