<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Issue Entry

Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	06-07-2013
Updated by 		: 	Kausar (Creating Report)
Update date		: 	14-12-2013
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//echo $_SESSION['logic_erp']['data_arr'][18]."=======================================";
//var_dump(!is_null($_SESSION['logic_erp']['data_arr'][18]) );
//print_r($_SESSION['logic_erp']);
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish Fabric Issue Info","../../", 1, 1, '','','');
?>

<script>
	var permission='<? echo $permission; ?>';

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	<?
	if(!is_null($_SESSION['logic_erp']['data_arr'][18]))
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][18]); 
		if($data_arr && is_null($data_arr)==false) echo "var field_level_data= ". $data_arr . ";\n";
	}
	
	//echo "alert(JSON.stringify(field_level_data));";
	?>
	function active_inactive(str,roll_field_reset)
	{
		$('#cbo_sample_type').val(0);
		$('#cbo_sewing_source').val(0);
		$('#cbo_sewing_company').val(0);
		$('#cbo_buyer_name').val(0);
		$('#txt_issue_qnty').val('');
		$('#txt_issue_req_qnty').val('');
		$('#hidden_prod_id').val('');
		$('#txt_batch_no').val('');
		$('#hidden_batch_id').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#save_string').val('');
		$('#txt_order_numbers').val('');
		$('#txt_fabric_received').val('');
		$('#txt_cumulative_issued').val('');
		$('#txt_yet_to_issue').val('');
		$('#previous_prod_id').val('');
		$('#txt_fabric_desc').val('');
		$('#list_fabric_desc_container').html('');
		$('#txt_global_stock').val('');
		$('#cbo_store_name').val(0);

		if(roll_field_reset==1)
		{
			$('#txt_no_of_roll').val('');
			$('#txt_no_of_roll').attr('disabled','disabled');
			$('#txt_no_of_roll').attr('placeholder','Display');
		}

		if(str==3 || str==10 || str==26 || str==29 || str==30 || str==31)
		{
			$('#cbo_sample_type').attr('disabled','disabled');
			$('#cbo_sewing_source').attr('disabled','disabled');
			$('#cbo_sewing_company').attr('disabled','disabled');
			$('#cbo_buyer_name').removeAttr('disabled','disabled');

			$('#txt_issue_qnty').removeAttr('readonly');
			$('#txt_issue_qnty').removeAttr('onDblClick');
			$('#txt_issue_qnty').removeAttr('placeholder');
			$('#txt_issue_qnty').attr('onkeyup','fnc_calculate_amount(this.value);');
		}
		else if(str==4 || str==8)
		{
			$('#cbo_sample_type').removeAttr('disabled','disabled');
			$('#cbo_sewing_source').removeAttr('disabled','disabled');
			$('#cbo_sewing_company').removeAttr('disabled','disabled');

			if(str==4)
			{
				$('#cbo_buyer_name').attr('disabled','disabled');
				$('#txt_issue_qnty').attr('readonly','readonly');
				$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');
				$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
				$('#txt_issue_qnty').removeAttr('onkeyup');
			}
			else
			{
				$('#cbo_buyer_name').removeAttr('disabled','disabled');
				$('#txt_issue_qnty').removeAttr('readonly');
				$('#txt_issue_qnty').removeAttr('onDblClick');
				$('#txt_issue_qnty').removeAttr('placeholder');
				$('#txt_issue_qnty').attr('onkeyup','fnc_calculate_amount(this.value);');
			}
		}
		else
		{
			$('#cbo_sample_type').attr('disabled','disabled');
			$('#cbo_sewing_source').removeAttr('disabled','disabled');
			$('#cbo_sewing_company').removeAttr('disabled','disabled');
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_issue_qnty').attr('readonly','readonly');
			$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');
			$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
			$('#txt_issue_qnty').removeAttr('onkeyup');
		}

		var requisition_mandatory = $("#requisition_mandatory").val()*1;
		var cbo_issue_purpose = $("#cbo_issue_purpose").val();

		if(requisition_mandatory == 1 && cbo_issue_purpose==9)
		{
			$('#cbo_store_name').attr('disabled','disabled');
			$('#txt_batch_no').attr('disabled','disabled');
			$('#txt_requisition_no').removeAttr('disabled','disabled');
		}
		else
		{
			$('#cbo_store_name').removeAttr('disabled','disabled');
			$('#txt_batch_no').removeAttr('disabled','disabled');
			$('#txt_requisition_no').attr('disabled','disabled');
		}

	}

	function fnc_calculate_amount(val)
	{
		var txt_rate=$('#txt_rate').val();
		$('#txt_amount').val((val*txt_rate).toFixed(2)).attr('disabled','disabled');
	}

	function po_fld_reset(str)
	{
		if(str==1)
		{
			$('#cbo_buyer_name').removeAttr('disabled','disabled');
			$('#txt_issue_qnty').removeAttr('readonly');
			$('#txt_issue_qnty').removeAttr('onDblClick');
			$('#txt_issue_qnty').removeAttr('placeholder');
			$('#txt_issue_qnty').attr('onkeyup','fnc_calculate_amount(this.value);');
		}
		else
		{
			$('#cbo_buyer_name').attr('disabled','disabled');
			$('#txt_issue_qnty').attr('readonly','readonly');
			$('#txt_issue_qnty').attr('onDblClick','openmypage_po();');
			$('#txt_issue_qnty').attr('placeholder','Double Click To Search');
			$('#txt_issue_qnty').removeAttr('onkeyup');
		}
	}

	function openmypage_batchnum()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var issue_purpose = $('#cbo_issue_purpose').val();
		var store_id = $('#cbo_store_name').val();
		var hidden_booking_no = $('#hidden_booking_no').val();
		var system_id = $('#txt_system_id').val();
		var recent_buyer = $('#cbo_buyer_name').val();

		if (form_validation('cbo_company_id*cbo_store_name','Company*Store Name')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&issue_purpose='+issue_purpose+'&store_id='+store_id+'&hidden_booking_no='+hidden_booking_no+'&system_id='+system_id+'&recent_buyer='+recent_buyer+'&action=batch_number_popup'; 
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=410px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				//var batch_no=this.contentDoc.getElementById("hidden_batch_no").value; //Access form field with id="emailfield"
				var hidden_booking_no=this.contentDoc.getElementById("hidden_booking_no").value;
				var hidden_booking_id=this.contentDoc.getElementById("hidden_booking_id").value;
				var without_order=this.contentDoc.getElementById("hidden_without_order").value;
				var buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value;
				var is_sales_order=this.contentDoc.getElementById("cbo_sales_order").value;
				//alert(buyer_id);

			//$('#txt_batch_no').val(batch_no);
			$('#hidden_booking_no').val(hidden_booking_no);
			$('#hidden_booking_id').val(hidden_booking_id);
			$('#hidden_batch_id_all').val(batch_id);
			$('#hidden_is_sales').val(is_sales_order);
			$('#cbo_buyer_name').val(buyer_id);
			$('#all_po_id').val('');
			$('#save_data').val('');
			$('#save_string').val('');
			$('#txt_order_numbers').val('');
			$('#txt_fabric_received').val('');
			$('#txt_cumulative_issued').val('');
			$('#txt_yet_to_issue').val('');
			$('#previous_prod_id').val('');
			$('#txt_fabric_desc').val('');
			$('#txt_global_stock').val('');

			if(issue_purpose==3 || issue_purpose==10 || issue_purpose==26 || issue_purpose==29 || issue_purpose==30 || issue_purpose==31)
			{
				po_fld_reset(without_order);
			}
			else if(issue_purpose==8)
			{
				$('#cbo_buyer_name').attr('disabled','disabled');
			}

			var roll_maintained=$('#roll_maintained').val();
			if(roll_maintained!=1)
			{
				//load_drop_down( 'requires/finish_fabric_issue_controller', batch_id+"**0", 'load_drop_down_fabric_desc', 'fabricDesc_td' );
				show_list_view(batch_id+'**'+cbo_company_id+'**'+store_id+'**'+hidden_booking_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_controller','setFilterGrid(\'table_body\',-1)');
			}

		}
	}
}

function openmypage_fabricDescription()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var save_string = $('#save_string').val();
	var hidden_prod_id = $('#hidden_prod_id').val();
	var txt_fabric_desc = $('#txt_fabric_desc').val();
	var hidden_batch_id = $('#hidden_batch_id').val();

	if (form_validation('cbo_company_id*txt_batch_no','Company*Batch')==false)
	{
		return;
	}

	var title = 'Fabric Description Info';
	var page_link = 'requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&save_string='+save_string+'&hidden_prod_id='+hidden_prod_id+'&txt_fabric_desc='+txt_fabric_desc+'&hidden_batch_id='+hidden_batch_id+'&action=fabricDescription_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
		var product_details=this.contentDoc.getElementById("product_details").value; //Access form field with id="emailfield"
		var number_of_roll=this.contentDoc.getElementById("number_of_roll").value; //Access form field with id="emailfield"
		var hidden_roll_issue_qnty=this.contentDoc.getElementById("hidden_roll_issue_qnty").value; //Access form field with id="emailfield"
		var save_string=this.contentDoc.getElementById("save_string").value; //Access form field with id="emailfield"

		$('#save_string').val( save_string );
		$('#txt_issue_req_qnty').val(hidden_roll_issue_qnty);
		$('#txt_issue_qnty').val('');
		$('#txt_no_of_roll').val( number_of_roll );
		$('#hidden_prod_id').val(product_id);
		$('#txt_fabric_desc').val(product_details);
	}
}

function openmypage_po()
{
	var cbo_company_id 		= $('#cbo_company_id').val();
	var cbo_issue_purpose 	= $('#cbo_issue_purpose').val();
	var roll_maintained 	= $('#roll_maintained').val();
	var save_data 			= $('#save_data').val();
	var all_po_id 			= $('#all_po_id').val();
	var txt_issue_req_qnty 	= $('#txt_issue_req_qnty').val();
	var distribution_method = $('#distribution_method_id').val();
	var cbo_buyer_name 		= $('#cbo_buyer_name').val();
	var hidden_batch_id 	= $('#hidden_batch_id').val();
	var dtls_tbl_id 		= $('#update_dtls_id').val();
	var prod_id 			= $("#hidden_prod_id").val();
	var cbo_store_name 		= $("#cbo_store_name").val();
	var txt_floor 			= $("#txt_floor").val();
	var txt_room 			= $("#txt_room").val();
	var txt_rack 			= $("#txt_rack").val();
	var txt_shelf 			= $("#txt_shelf").val();
	var txt_bin 			= $("#txt_bin").val();
	var fabric_shade		= $("#cbo_fabric_type").val();
	var cbouom				= $("#cbouom").val();
	var cbo_body_part		= $("#cbo_body_part").val();
	var txt_requisition_job	= $("#txt_requisition_job").val();


	if (form_validation('cbo_company_id*txt_batch_no*txt_fabric_desc*cbo_store_name','Company*Batch No*Fabric Description*Store Name')==false)
	{
		return;
	}
	var hidden_detarmination_id= $("#hidden_detarmination_id").val();
	var title = 'PO Info';
	var page_link = 'requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&cbo_issue_purpose='+cbo_issue_purpose+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_issue_req_qnty='+txt_issue_req_qnty+'&prev_distribution_method='+distribution_method+'&cbo_buyer_name='+cbo_buyer_name+'&dtls_tbl_id='+dtls_tbl_id+'&prod_id='+prod_id+'&action=po_popup'+'&batch_id='+hidden_batch_id+'&cbo_store_name='+cbo_store_name+'&txt_floor='+txt_floor+'&txt_room='+txt_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&txt_bin='+txt_bin+'&fabric_shade='+fabric_shade+'&cbouom='+cbouom+'&hidden_detarmination_id='+hidden_detarmination_id+'&cbo_body_part='+cbo_body_part;
	$('#cbo_issue_purpose').attr('disabled','disabled');
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_data=this.contentDoc.getElementById("save_data").value;	 //Access form field with id="emailfield"
		var hide_issue_qnty=this.contentDoc.getElementById("tot_issue_qnty").value*1; //Access form field with id="emailfield"
		var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
		var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
		var distribution_method=this.contentDoc.getElementById("distribution_method").value;
		var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
		var tot_issue_qnty=hide_issue_qnty.toFixed(2);

		$('#cbo_issue_purpose').attr('disabled',false);
		$('#save_data').val(save_data);
		$('#txt_issue_qnty').val(tot_issue_qnty);
		$('#txt_issue_req_qnty').val(tot_issue_qnty );
		$('#cbo_buyer_name').val(buyer_id);
		$('#all_po_id').val(all_po_id);
		$('#txt_order_numbers').val(all_po_no);
		$('#distribution_method_id').val(distribution_method);

		if(all_po_id!="")
		{
			var prod_id=$('#hidden_prod_id').val();
			var bodypart_id=$('#cbo_body_part').val();
			//get_php_form_data(all_po_id+"**"+prod_id+"**"+hidden_batch_id+"**"+bodypart_id+"**"+cbo_store_name, "populate_data_about_order", "requires/finish_fabric_issue_controller" );
			get_php_form_data(all_po_id+"**"+prod_id+"**"+hidden_batch_id+"**"+bodypart_id+"**"+cbo_store_name+"**"+txt_floor+"**"+txt_room+"**"+txt_rack+"**"+txt_shelf+"**"+fabric_shade+"**"+txt_bin, "populate_data_about_order", "requires/finish_fabric_issue_controller" );

			var txt_rate=$('#txt_rate').val();
			$('#txt_amount').val((tot_issue_qnty*txt_rate).toFixed(2)).attr('disabled','disabled');
			load_drop_down( 'requires/finish_fabric_issue_controller',all_po_id, 'load_drop_down_gmt_item', 'gmt_item_td' );
		}
	}
}

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}

	var title = 'Finish Fabric Issue Info';
	var page_link = 'requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=finishFabricIssue_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=390px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var datas=this.contentDoc.getElementById("finish_fabric_issue_id").value; //Access form field with id="emailfield"
		var datas=datas.split("_");
		var finish_fabric_issue_id=datas[0];
		var batch_ids=datas[1];
		var company_id=datas[2];
		var store_ids=datas[3];


		var posted_in_account=datas[4]; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else  document.getElementById("accounting_posted_status").innerHTML="";

		reset_form('finishFabricEntry_1','div_details_list_view','','cbo_issue_purpose,9','','roll_maintained');
		get_php_form_data(finish_fabric_issue_id, "populate_data_from_issue_master", "requires/finish_fabric_issue_controller" );
		show_list_view(finish_fabric_issue_id,'show_finish_fabric_issue_listview','div_details_list_view','requires/finish_fabric_issue_controller','');

		var saved_booking_no = $("#hidden_booking_no").val();

		var requisition_no = $("#txt_requisition_no").val();
		var requisition_id = $("#txt_requisition_id").val();

		if(requisition_id =="")
		{
			show_list_view(batch_ids+'**'+company_id+'**'+store_ids+'**'+saved_booking_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_controller','setFilterGrid(\'table_body\',-1)');
		}
		else
		{
			show_list_view(company_id+'**'+requisition_id+'**'+requisition_no,'populate_list_view_requisition','list_requisition_container','requires/finish_fabric_issue_controller','setFilterGrid(\'table_body\',-1)');
		}

		set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);
	}
}

function generate_report_file(data,action,page)
{
	window.open("requires/finish_fabric_issue_controller.php?data=" + data+'&action='+action, true );
}


function fnc_fabric_issue_entry(operation)
{
	if(operation==4)
	{
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat,'finish_fabric_issue_print','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==5)
	{
		if ($("#txt_system_id").val()=="")
		{
			alert ("Please Save First.");
			return;
		}
		var print_with_vat=1;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat,'finish_fabric_issue_print','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==6)
	{
		if ($("#txt_system_id").val()=="")
		{
			alert ("Please Save First.");
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_issue_print2','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==7)
	{
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat,'finish_fabric_issue_print_4','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==8)
	{
		var show_buyer = '';
		var r=confirm("Press  \"Ok\"  to show  Buyer Name\nPress  \"Cancel\"  to hide Buyer Name");
		if (r==true) show_buyer="0"; else show_buyer="1";
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat+'*'+show_buyer,'finish_fabric_issue_print_5','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==9)
	{
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat,'finish_fabric_issue_print_6','requires/finish_fabric_issue_controller');
		/*print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#cbo_cutting_floor').val()+'*'+report_title, "finish_fabric_issue_print", "requires/finish_fabric_issue_controller" ) */
		return;
	}
	else if(operation==10)
	{
		var print_with_vat=0;
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+print_with_vat,'finish_fabric_issue_print_7','requires/finish_fabric_issue_controller');
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		if($("#is_posted_account").val()==1)
		{
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}

		var cbo_issue_purpose=$('#cbo_issue_purpose').val();

		if( form_validation('cbo_company_id*txt_issue_date*cbo_buyer_name','Company*Issue Date*Buyer Name')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Current Date");
			return;
		}

		if(cbo_issue_purpose==4 || cbo_issue_purpose==8)
		{
			if( form_validation('cbo_sample_type','Sample Type')==false )
			{
				return;
			}
		}

		if(cbo_issue_purpose==4 || cbo_issue_purpose==8 || cbo_issue_purpose==9)
		{
			if(form_validation('cbo_sewing_source*cbo_sewing_company','Service Source*Service Company')==false )
			{
				return;
			}
		}

		if(form_validation('cbo_store_name*txt_batch_no*txt_fabric_desc*cbo_body_part*txt_issue_qnty','Store Name*Batch No*Fabric Description*Body Part*Issue Qnty')==false )
		{
			return;
		}

		if(($("#txt_issue_qnty").val()*1 > $("#txt_yet_to_issue").val()*1+$("#hidden_issue_qnty").val()*1))
		{
			alert("Issue Quantity is not available.");
			return;
		}


		var service_source=$('#cbo_sewing_source').val();


		if('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][18]);?>')
		{

			if(service_source==1)
			{
				if (form_validation('<?php echo chop(implode('*',$_SESSION['logic_erp']['mandatory_field'][18]),'*');?>','<?php echo chop(implode('*',$_SESSION['logic_erp']['mandatory_message'][18]),'*');?>')==false)
				{
					
					return;
				}
			}
			else
			{
				<? $arr = array(3 => 'cbo_sewing_company_location');
				
				?>

				if('<?php echo chop(implode('*',array_diff_key($_SESSION['logic_erp']['mandatory_field'][18],$arr)),'*');?>')
				{
					if (form_validation('<?php echo chop(implode('*',array_diff_key($_SESSION['logic_erp']['mandatory_field'][18],$arr)),'*');?>','<?php echo chop(implode('*',array_diff_key($_SESSION['logic_erp']['mandatory_message'][18],$arr)),'*');?>')==false)
					{
						
						return;
					}
				}
				
			}
			
		}
		
		if ($('#cbo_sewing_source').val()==1) 
		{
			if( form_validation('cbo_cutting_floor','Cutting Unit No')==false )
			{
				return;
			}
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var txt_floor=$('#txt_floor_name').val()*1;
		var txt_room=$('#txt_room_name').val()*1;
		var txt_rack=$('#txt_rack_name').val()*1;
		var txt_shelf=$('#txt_shelf_name').val()*1;
		var txt_bin=$('#txt_bin_name').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0 || txt_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && txt_floor==0 || txt_room==0 || txt_rack==0)
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && txt_floor==0 || txt_room==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && txt_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End

		var dataString = "txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_sample_type*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_buyer_name*txt_cut_req*cbo_store_name*txt_batch_no*hidden_batch_id*txt_fabric_desc*txt_issue_qnty*txt_no_of_roll*txt_remarks*txt_rack*txt_shelf*txt_bin*cbo_cutting_floor*hidden_prod_id*previous_prod_id*update_id*save_data*save_string*update_dtls_id*update_trans_id*hidden_issue_qnty*txt_issue_req_qnty*all_po_id*roll_maintained*cbo_body_part*cbo_item_name*hidden_fabric_rate*cbouom*cbo_fabric_type*txt_floor*txt_room*cbo_sewing_company_location*txt_rate*txt_amount*hidden_booking_no*hidden_booking_id*txt_wo_no*txt_wo_id*txt_requisition_no*txt_requisition_id*txt_requisition_job*cbo_extra_status";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");

		freeze_window(operation);
		http.open("POST","requires/finish_fabric_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_issue_entry_reponse;
	}
}

function fnc_fabric_issue_entry_reponse()
{
	if(http.readyState == 4)
	{
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		show_msg(reponse[0]);

		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			if (reponse[0]==2 && reponse[4]==1) // is mst delete reset form
			{
				reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','disable_enable_fields(\'cbo_company_id*cbo_issue_purpose*txt_issue_date\');active_inactive(9,1);')
			}
			else
			{
				$("#update_id").val(reponse[2]);
				$("#txt_system_id").val(reponse[3]);
				$('#cbo_company_id').attr('disabled','disabled');
				$('#cbo_issue_purpose').attr('disabled','disabled');
				$('#txt_issue_date').attr('disabled','disabled');
				$('#txt_requisition_no').attr('disabled','disabled');

				reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*txt_issue_date*txt_challan_no*cbo_sewing_source*txt_wo_no*cbo_sewing_company*txt_cut_req*cbo_store_name*txt_batch_no*hidden_batch_id*roll_maintained*cbo_buyer_name*hidden_booking_no*hidden_booking_id*store_update_upto*requisition_mandatory*txt_requisition_no*txt_requisition_id*txt_requisition_job*cbo_extra_status');

				show_list_view(reponse[2],'show_finish_fabric_issue_listview','div_details_list_view','requires/finish_fabric_issue_controller','');
				$('#txt_fabric_desc').focus();

				var cbo_company_id = $('#cbo_company_id').val();
				var batch_id = $('#hidden_batch_id').val();
				var store_id = $('#cbo_store_name').val();
				var hidden_booking_no = $('#hidden_booking_no').val();

				var requisition_id = $("#txt_requisition_id").val();

				if(requisition_id !=""){
					show_list_view(batch_id+'**'+cbo_company_id+'**'+store_id+'**'+hidden_booking_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_controller','setFilterGrid(\'table_body\',-1)');
				}
			}
			set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);
		}
		else if(reponse[0]==17)
		{
			alert(reponse[1]);
		}

		release_freezing();
	}
}

function set_form_data(data)
{
	//N.B  this function is used in two action 1.show_fabric_desc_listview, 2.show_fabric_desc_listview_requ
	reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_sewing_company_location*cbo_buyer_name*txt_cut_req*txt_wo_no*hidden_booking_no*hidden_booking_id*hidden_batch_id_all*roll_maintained*cbo_store_name*is_posted_account*store_update_upto*requisition_mandatory*txt_requisition_no*txt_requisition_id*txt_requisition_job*cbo_extra_status');

	var cbo_issue_purpose=$('#cbo_issue_purpose').val();
	var data=data.split("**");
	$('#hidden_prod_id').val(data[0]);
	$('#txt_fabric_desc').val(data[1]);
	$('#txt_rack').val(data[2]);
	$('#txt_shelf').val(data[3]);
	$('#txt_bin').val(data[22]);
	$('#txt_global_stock').val(data[4]);
	$('#txt_color').val(data[5]);
	$('#cbouom').val(data[6]);
	$('#cbo_fabric_type').val(data[7]);
	$('#cbo_sample_type').val(data[8]);

	$('#txt_floor_name').val(data[9]);
	$('#txt_room_name').val(data[10]);
	$('#txt_rack_name').val(data[11]);
	$('#txt_shelf_name').val(data[12]);
	$('#txt_bin_name').val(data[23]);
	$('#txt_floor').val(data[13]);
	$('#txt_room').val(data[14]);
	$('#hidden_batch_id').val(data[15]);
	$('#txt_batch_no').val(data[16]);
	$('#cbo_store_name').val(data[17]);
	var bodyPart=data[18];
	$('#txt_rate').val(data[19]);
	$('#hidden_detarmination_id').val(data[20]);
	/*if(cbo_issue_purpose==8)
	{
		$('#txt_rate').val(data[21]);
	}*/
	
	$('#hidden_fabric_rate').val(data[19]);
	

	var hidden_batch_id=data[15];

	if($('#txt_requisition_id').val())
	{
		$('#cbo_store_name').attr('disabled','disabled');
		$('#txt_requisition_job').val(data[24]); // from show_fabric_desc_listview_requ
	}

	reset_form('','','save_data*txt_issue_qnty*txt_issue_req_qnty*all_po_id*txt_order_numbers*distribution_method_id*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue','','','');
	load_drop_down( 'requires/finish_fabric_issue_controller', data[0]+"**"+hidden_batch_id, 'load_drop_down_body_part', 'body_part_td' );
	var body_part_length=$("#cbo_body_part option").length;
	if(body_part_length==2)
	{
		$('#cbo_body_part').val($('#cbo_body_part option:last').val());
	}
	if(bodyPart>0)
	{
		$('#cbo_body_part').val(data[18]);
		$('#cbo_body_part').attr('disabled','disabled');
	}
	
	if($("#txt_issue_qnty").attr('readonly') == undefined)
	{
		get_php_form_data(hidden_batch_id+"**"+data[0]+"**"+data[17], "populate_data_about_sample", "requires/finish_fabric_issue_controller" );
	}
	else
	{
		openmypage_po();
		/*if(cbo_issue_purpose==4 || cbo_issue_purpose==9)
		{
			openmypage_po();
		}*/
	}
	set_button_status(0, permission, 'fnc_fabric_issue_entry',1,1);
}

function check_batch(data)
{
	return;
	var cbo_company_id=$('#cbo_company_id').val();
	var roll_maintained=$('#roll_maintained').val();
	var cbo_issue_purpose=$('#cbo_issue_purpose').val();
	var cbo_store_name=$('#cbo_store_name').val();

	if(form_validation('cbo_company_id','Company')==false)
	{
		$('#txt_batch_no').val('');
		$('#hidden_batch_id').val('');
		return;
	}

	var batch_data=return_global_ajax_value( data+"**"+cbo_company_id+"**"+cbo_issue_purpose, 'check_batch_no', '', 'requires/finish_fabric_issue_controller');
	batch_data=batch_data.split("**");
	var batch_id=trim(batch_data[0]);
	var without_order=batch_data[1];
	var booking_no=batch_data[2];
	var booking_id=batch_data[3];
	if(batch_id==0)
	{
		alert("Batch No Found");
		reset_form('','list_fabric_desc_container','txt_batch_no*hidden_batch_id*save_data*txt_issue_qnty*txt_issue_req_qnty*cbo_buyer_name*txt_cut_req*all_po_id*txt_order_numbers*distribution_method_id*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_fabric_desc*txt_rack*txt_shelf*txt_bin*hidden_prod_id*txt_global_stock*cbo_body_part*cbo_fabric_type*store_update_upto*requisition_mandatory','','','');
		return;
	}
	else
	{
		freeze_window(5);
		reset_form('','list_fabric_desc_container','txt_order_numbers*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_global_stock*store_update_upto*requisition_mandatory','','','');
		$('#hidden_batch_id').val(batch_id);

		if(cbo_issue_purpose==3 || cbo_issue_purpose==10 || cbo_issue_purpose==26 || cbo_issue_purpose==29 || cbo_issue_purpose==30 || cbo_issue_purpose==31)
		{
			po_fld_reset(without_order);
		}

		if(roll_maintained!=1)
		{
			$("#hidden_booking_no").val(booking_no);
			$("#hidden_booking_id").val(booking_id);
			show_list_view(batch_id+'**'+cbo_company_id+'**'+cbo_store_name+'**'+booking_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_issue_controller','setFilterGrid(\'table_body\',-1)');
		}
		release_freezing();
	}

}

function js_set_value(id,detarminationID)
{
	$('#hidden_detarmination_id').val(detarminationID);
	var cbo_company_id=$('#cbo_company_id').val();
	var roll_maintained=$('#roll_maintained').val();
	var booking_no=$('#hidden_booking_no').val();
	get_php_form_data(id+"**"+roll_maintained+"**"+cbo_company_id+"**"+booking_no,'populate_issue_details_form_data', 'requires/finish_fabric_issue_controller');
	var txt_issue_qnty=$('#txt_issue_qnty').val();
	var txt_rate=$('#txt_rate').val();
	$("#txt_amount").val((txt_issue_qnty*txt_rate).toFixed(2)).attr('disabled','disabled');
}

function fn_sewing_com_location(id)
{

	if(id != 1){
		$('#cbo_sewing_company_location').val('0');
		$('#cbo_sewing_company_location').attr('disabled','disabled');
	}
}

function details_reset()
{
	$("#list_fabric_desc_container").html("");

	reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_sewing_company_location*cbo_buyer_name*txt_cut_req*hidden_booking_no*hidden_booking_id*hidden_batch_id_all*cbo_store_name*store_update_upto*requisition_mandatory*cbo_extra_status');
}
function load_cutting_unit()
{
	var cbo_sewing_company = 0
	if($("#cbo_sewing_source").val() ==1)
	{
		cbo_sewing_company = $("#cbo_sewing_company").val();
	}
	load_drop_down( 'requires/finish_fabric_issue_controller', cbo_sewing_company, 'load_drop_down_cutting_unit', 'cutting_unit' );
}

function company_on_change(company)
{
    /* var data='cbo_company_id='+company+'&action=upto_variable_settings';    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto").value = this.responseText;
        }
    }
    xmlhttp.open("POST", "requires/finish_fabric_issue_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data); */

	get_php_form_data( company,'company_wise_load' ,'requires/finish_fabric_issue_controller');

	var requisition_mandatory = $("#requisition_mandatory").val()*1;
	var cbo_issue_purpose = $("#cbo_issue_purpose").val();

	if(requisition_mandatory == 1 && cbo_issue_purpose==9)
	{
		$('#cbo_store_name').attr('disabled','disabled');
		$('#txt_batch_no').attr('disabled','disabled');
		$('#txt_requisition_no').removeAttr('disabled','disabled');
	}
	else
	{
		$('#cbo_store_name').removeAttr('disabled','disabled');
		$('#txt_batch_no').removeAttr('disabled','disabled');
		$('#txt_requisition_no').attr('disabled','disabled');
	}
}

function openmypage_woNo()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_service_source = $('#cbo_sewing_source').val();
	var cbo_service_company = $('#cbo_sewing_company').val()		

	if (form_validation('cbo_company_id*cbo_sewing_source*cbo_sewing_company','Company*Source*Service Company')==false)
	{
		return;
	}
	else
	{			
		var page_link='requires/finish_fabric_issue_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&supplier_id='+cbo_service_company+'&action=service_booking_popup';
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

function openmypage_requisition()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var issue_purpose = $('#cbo_issue_purpose').val();
	var store_id = $('#cbo_store_name').val();
	var hidden_booking_no = $('#hidden_booking_no').val();
	var system_id = $('#txt_system_id').val();
	var recent_buyer = $('#cbo_buyer_name').val();

	if (form_validation('cbo_company_id*cbo_issue_purpose','Company*Issue Purpose')==false)
	{
		return;
	}
	else
	{
		var page_link='requires/finish_fabric_issue_controller.php?cbo_company_id='+cbo_company_id+'&issue_purpose='+issue_purpose+'&hidden_booking_no='+hidden_booking_no+'&recent_buyer='+recent_buyer+'&action=requisition_number_popup'; 
		var title='Batch Number Popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1040px,height=410px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var requisition_id=this.contentDoc.getElementById("txt_selected_id").value;
			var requisition_no=this.contentDoc.getElementById("txt_selected_no").value;

			$('#txt_requisition_no').val(requisition_no);
			$('#txt_requisition_id').val(requisition_id);

			$('#txt_batch_no').attr("disabled","disabled");
			
			show_list_view(cbo_company_id+'**'+requisition_id+'**'+requisition_no,'populate_list_view_requisition','list_requisition_container','requires/finish_fabric_issue_controller','setFilterGrid(\'table_body\',-1)');

			
			
		}
	}
}

function requisition_set_data(data)
{
	var data=data.split("**");
	var buyer_name =$('#cbo_buyer_name').val();
	var cbo_issue_purpose = $('#cbo_issue_purpose').val();
	$('#cbo_store_name').val(0);

	var JOB_NO=data[0];
	var PROD_ID=data[1];
	var COMPANY_ID=data[2];
	var BODY_PART=data[3];
	var FAB_COLOR_ID=data[4];
	var DETERMINATION_ID=data[5];
	var GSM=data[6];
	var DIA=data[7];
	var requ_job_buyer_id=data[8];
	var requ_batch_booking=data[9];
	var requ_batch_id=data[10];
	var without_order=data[11];
	var requ_batch_booking_id=data[12];
	var requ_mst_id=data[13];

	if($('#update_id').val() != "" && requ_job_buyer_id != buyer_name)
	{
		alert("Buyer Mixing not allowed");
		return;
	}


	$('#hidden_booking_no').val(requ_batch_booking);
	$('#hidden_booking_id').val(requ_batch_booking_id);
	$('#hidden_batch_id_all').val(requ_batch_id);
	$('#hidden_is_sales').val(0);
	$('#cbo_buyer_name').val(requ_job_buyer_id);
	$('#txt_requisition_job').val(JOB_NO);



	reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_company_id*cbo_issue_purpose*txt_issue_date*txt_challan_no*cbo_sewing_source*cbo_sewing_company*cbo_sewing_company_location*cbo_buyer_name*txt_cut_req*txt_wo_no*hidden_booking_no*hidden_booking_id*hidden_batch_id_all*roll_maintained*cbo_store_name*is_posted_account*store_update_upto*requisition_mandatory*txt_requisition_no*txt_requisition_id');

	show_list_view(JOB_NO+'_'+PROD_ID+'_'+COMPANY_ID+'_'+BODY_PART+'_'+FAB_COLOR_ID+'_'+DETERMINATION_ID+'_'+GSM+'_'+DIA+'_'+requ_mst_id+'_'+cbo_issue_purpose+'_'+buyer_name,'show_fabric_desc_listview_requ','list_fabric_desc_container','requires/finish_fabric_issue_controller','setFilterGrid(\'fabric_listview\',-1)');
	//

}
function change_color(v_id,e_color)
{
	if( $('#req_tr_'+v_id).attr('bgcolor')=='#FF9900')
		$('#req_tr_'+v_id).attr('bgcolor',e_color)
	else
		$('#req_tr_'+v_id).attr('bgcolor','#FF9900')
}

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?><br />
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:740px; float:left; padding-right: 5px;" align="center">
				<fieldset style="width:730px;">
					<legend>Finish Fabric Issue Entry</legend>
					<fieldset style="width:730px;">
						<table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
							<tr>
								<td colspan="3" align="right"><strong>Issue No</strong></td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
								</td>
							</tr>
							<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_on_change(this.value);" );
									?>
								</td>
								<td class="must_entry_caption">Issue Purpose</td>
								<td>
									<?
									echo create_drop_down("cbo_issue_purpose", 150,$yarn_issue_purpose,"", 0,"",'9',"active_inactive(this.value,0);load_cutting_unit();",'','3,4,8,9,10,26,29,30,31,44');
									?>
								</td>
								<td class="must_entry_caption">Issue Date</td>
								<td>
									<input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:138px;" value="<? echo date("d-m-Y"); ?>" placeholder="Select Date"/>
								</td>

							</tr>
							<tr>

								<td>Challan No.</td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:138px;" maxlength="20" title="Maximum 20 Character" />
								</td>
								<td class="must_entry_caption">Service Source</td>
								<td>
									<?
									echo create_drop_down("cbo_sewing_source", 150, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/finish_fabric_issue_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_sewing_com','sewingcom_td');load_drop_down( 'requires/finish_fabric_issue_controller',document. getElementById('cbo_company_id').value ,'load_drop_down_location','sewingcomlocation_td');fn_sewing_com_location(this.value);load_cutting_unit();","","","","","2");
									?>
								</td>
								<td class="must_entry_caption">Service Company</td>
								<td id="sewingcom_td">
									<?
									echo create_drop_down("cbo_sewing_company", 150, $blank_array,"", 1,"-- Select Service Company --", 0,"");
									?>
								</td>
							</tr>
							<tr>
								<td class="">Service Location</td>
								<td id="sewingcomlocation_td">
									<?
									echo create_drop_down("cbo_sewing_company_location", 150, $blank_array,"", 1,"-- Select Service Location --", 0,"");
									?>
								</td>
								<td class="must_entry_caption">Buyer Name</td>
								<td id="buyer_td_id">
									<?
									echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1 );
									?>
								</td>
								<td>Cutt. Req. No</td>
								<td>
									<input type="text" name="txt_cut_req" id="txt_cut_req" class="text_boxes_numeric" style="width:138px;" />
									<input type="hidden" name="hidden_booking_id" id="hidden_booking_id"  />
									<input type="hidden" name="hidden_booking_no" id="hidden_booking_no"  />
									<input type="hidden" name="hidden_batch_id_all" id="hidden_batch_id_all"  />

								</td>

							</tr>
							<tr>
								<td>WO NO</td>
		                        <td>
		                            <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:138px;" placeholder="Browse/Write/scan" onDblClick="openmypage_woNo();" />
		                            <input type="hidden" id="txt_wo_id" value="0" />
		                        </td>
		                        <td>Requistion No.</td>
								<td>
									<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:140px;" placeholder="Browse/Write/scan" onDblClick="openmypage_requisition();" />
									<input type="hidden" name="txt_requisition_id" id="txt_requisition_id"  />
									
								</td>
		                       	<td>Additional/Extra</td>
								<td>
									<?
										echo create_drop_down("cbo_extra_status", 150, $yes_no,"", 1,"-- Select --", 2,"");
									?>
								</td>
							</tr>
						</table>
					</fieldset>
					<table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
						<tr>
							<td width="68%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%">
										<tr>
											<td class="must_entry_caption" width="30%">Store Name</td>
											<td id="store_td">
												<?
												echo create_drop_down( "cbo_store_name", 170, "select id, store_name from lib_store_location where find_in_set(2,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "-- Select store --", 0, "details_reset();" );
												?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Batch No.</td>
											<td>
												<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:158px;" placeholder=" Browse" onDblClick="openmypage_batchnum();" onChange="check_batch(this.value);"/>
												<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" readonly />
												<input type="hidden" name="hidden_is_sales" id="hidden_is_sales" readonly />
												<input type="hidden" name="txt_requisition_job" id="txt_requisition_job"  />
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Fabric Description</td>
											<td id="fabricDesc_td">
												<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:200px;" placeholder="Display" disabled />
												<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" readonly>
												<input type="hidden" name="hidden_detarmination_id" id="hidden_detarmination_id" readonly>
												<span class="must_entry_caption">UOM</span>
												<?
												echo create_drop_down( "cbouom", 70, $unit_of_measurement,'', 1, '-Uom-', 12, "",1,"1,12,23,27" );
												?>
											</td>
										</tr>
										<tr>
											<td>Sample Type</td>
											<td id="">
												<?
												echo create_drop_down( "cbo_sample_type", 170, "select id, sample_name from lib_sample where status_active=1 and is_deleted=0 order by sample_name","id,sample_name", 1, "--Select Sample Type--", 0, "",0 );
												?>
											</td>
										</tr>
										<tr>
											<td>Body Part</td>
											<td id="body_part_td">
												<?
												echo create_drop_down( "cbo_body_part", 170, $body_part,"", 1, "-- Select Body Part --", 0, "",0 );
												?>
											</td>
										</tr>
										<tr>
											<td>Garments Item</td>
											<td id="gmt_item_td">
												<?
												echo create_drop_down( "cbo_item_name", 170, $garments_item,"", 1, "-- Select Gmt. Item --", "", "",0,0 );
												?>
											</td>
										</tr>
										<tr>
											<td>Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:158px" placeholder="Display" disabled />
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Issue Qnty</td>
											<td>
												<input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:158px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_po();" /></td>
											</tr>
											<tr>
												<td>Rate</td>
												<td>
													<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:158px;" readonly /></td>
												</tr>
												<tr>
													<td>Amount</td>
													<td>
														<input type="text" name="txt_amount" class="text_boxes_numeric" id="txt_amount" style="width:158px;" readonly /></td>
													</tr>
													<tr>
														<td>No. of Roll</td>
														<td>
															<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:158px" /> <!--disabled="disabled" placeholder="Display"-->
														</td>
													</tr>
													<tr>
														<td>Floor</td>
														<td>
															<input type="text" name="txt_floor_name" id="txt_floor_name" class="text_boxes" style="width:158px" placeholder="Display" disabled />
															<input type="hidden" name="txt_floor" id="txt_floor" class="text_boxes" style="width:158px"/>
														</td>
													</tr>
													<tr>
														<td>Room</td>
														<td>
															<input type="text" name="txt_room_name" id="txt_room_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
															<input type="hidden" name="txt_room" id="txt_room" class="text_boxes" style="width:158px"/>
														</td>
													</tr>
													<tr>
														<td>Rack</td>
														<td>
															<input type="text" name="txt_rack_name" id="txt_rack_name" class="text_boxes" style="width:158px" placeholder="Display" disabled />
															<input type="hidden" name="txt_rack" id="txt_rack" class="text_boxes" style="width:158px"/>
														</td>
													</tr>
													<tr>
														<td>Shelf</td>
														<td>
															<input type="text" name="txt_shelf_name" id="txt_shelf_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
															<input type="hidden" name="txt_shelf" id="txt_shelf" class="text_boxes" style="width:158px" />
														</td>
													</tr>
													<tr>
														<td>Bin Box</td>
														<td>
															<input type="text" name="txt_bin_name" id="txt_bin_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
															<input type="hidden" name="txt_bin" id="txt_bin" class="text_boxes" style="width:158px" />
														</td>
													</tr>
													<tr>
														<td>Cutting Unit No</td>
														<td id="cutting_unit">
															<?
															echo create_drop_down("cbo_cutting_floor", 170, $blank_array,"", 1,"-- Select Floor --", 0,"");
															?>
														</td>
													</tr>
													<tr>
														<td>Fabric Shade</td>
														<td>
															<?
															echo create_drop_down( "cbo_fabric_type", 180, $fabric_shade,"",1, "-- Select --", 0, "",1 );
															?>
														</td>
													</tr>
													<tr>
														<td>Remarks</td>
														<td>
															<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:300px" />
														</td>
													</tr>
									</table>
								</fieldset>
							</td>
							<td width="2%" valign="top"></td>
							<td width="30%" valign="top">
								<fieldset>
									<legend>Display</legend>
									<table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >
										<tr>
											<td>Order Numbers</td>
											<td>
												<input type="text" name="txt_order_numbers" id="txt_order_numbers" class="text_boxes" style="width:100px" disabled />
											</td>
										</tr>
										<tr>
											<td>Fabric Received</td>
											<td><input type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes_numeric" style="width:100px" disabled /></td>
										</tr>
										<tr>
											<td>Cumulative Issued</td>
											<td><input type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes_numeric" style="width:100px" disabled /></td>
										</tr>
										<tr>
											<td>Yet to Issue</td>
											<td><input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:100px" disabled /></td>
										</tr>
										<tr>
											<td>Global Stock</td>
											<td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes" style="width:100px" readonly disabled /></td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="3" class="button_container" width="100%">
								<input type="hidden" id="update_id" name="update_id" value="" >
								<input type="hidden" name="save_data" id="save_data" readonly>
								<input type="hidden" name="save_string" id="save_string" readonly>
								<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
								<input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
								<input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
								<input type="hidden" name="hidden_issue_qnty" id="hidden_issue_qnty" readonly>
								<input type="hidden" name="txt_issue_req_qnty" id="txt_issue_req_qnty" readonly>
								<input type="hidden" name="all_po_id" id="all_po_id" readonly>
								<input type="hidden" name="roll_maintained" id="roll_maintained" value="0" readonly>
								<input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
								<input type="hidden" name="hidden_fabric_rate" id="hidden_fabric_rate" readonly />
								<input type="hidden" id="is_posted_account" name="is_posted_account" value="" />
								<input type="hidden" id="store_update_upto" name="store_update_upto" value="" />
								<input type="hidden" id="requisition_mandatory" name="requisition_mandatory" value="" />

								<?
								echo load_submit_buttons($permission, "fnc_fabric_issue_entry", 0,1,"reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','disable_enable_fields(\'cbo_company_id*cbo_issue_purpose\');active_inactive(9,1);')",1);
								?>

								<input type="button" name="print_vat" id="print_vat" value="Print With VAT" onClick="fnc_fabric_issue_entry(5)" style="width:100px" class="formbutton" />
								<input type="button" name="print_3" id="print_3" value="Print 3" onClick="fnc_fabric_issue_entry(6)" style="width:70px" class="formbutton" />
								<input type="button" name="print_4" id="print_4" value="Print 4" onClick="fnc_fabric_issue_entry(7)" style="width:70px" class="formbutton" />
								<input type="button" name="print_5" id="print_5" value="Print 5" onClick="fnc_fabric_issue_entry(8)" style="width:70px" class="formbutton" />
								<input type="button" name="print_6" id="print_6" value="Print 6" onClick="fnc_fabric_issue_entry(9)" style="width:70px" class="formbutton" />
								<input type="button" name="print_7" id="print_7" value="Print 7" onClick="fnc_fabric_issue_entry(10)" style="width:70px" class="formbutton" />
								<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
							</td>
						</tr>
					</table>
					<div style="width:730px;" id="div_details_list_view"></div>
				</fieldset>
			</div>
			<div id="list_requisition_container" style="width:670px; margin-left:10px; overflow:auto;  padding-top:5px; margin-top:5px; "></div>
			<div id="list_fabric_desc_container" style="width:670px; margin-left:10px; overflow:auto;  padding-top:5px; margin-top:5px; "></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
