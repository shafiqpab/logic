<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish Fabric Receive Entry

Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	08-05-2014
Updated by 		: 	Md Didarul Alam (Intreget Fabric Sales order entry page)
Update date		: 	28-01-2018
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
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, company_location_id, item_cate_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id";
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
//var_dump($item_cate_id);
$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($company_location_id !='') {
    $com_location_credential_cond = "and a.id in($company_location_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Knit Finish Fabric Receive Info","../../", 1, 1, '','','');

$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=37 and status_active=1 and is_deleted=0",'company_name','independent_controll');

?>


<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name and status_active=1", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_color").autocomplete({
			source: str_color
		});
	});

	function set_receive_basis()
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		$('#list_fabric_desc_container').html('');

		$('#buyer_id').val('');
		$('#buyer_name').val('');
		$('#txt_production_qty').val('');
		$('#txt_reject_qty').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#txt_booking_no').val('');
		$('#txt_booking_no_id').val('');
		$('#batch_booking_without_order').val('');
		$('#roll_details_list_view').html('');
		$("#txt_order_no").val('');
		$("#hidden_order_id").val('');
		$('#cbo_body_part').val(0);
		$("#txt_gsm").val('');
		$("#txt_dia_width").val('');
		//$("#finish_production_dtls_id").val('');
		$('#previous_prod_id').val('');
		$('#product_id').val('');
		$('#txt_deleted_id').val('');
		$('#txt_rate').val('');
		$('#txt_amount').val('');

		$('#cbo_dyeing_source').removeAttr('disabled','disabled');
		$('#cbo_dyeing_company').removeAttr('disabled','disabled');
		$('#cbo_dyeing_source').val(0);
		$('#cbo_dyeing_company').val(0);

		$('#txt_production_qty').attr('readonly','readonly');
		$('#txt_production_qty').attr('onClick','openmypage_po();');
		$('#txt_production_qty').attr('placeholder','Single Click to Search');
		$('#txt_reject_qty').attr('readonly','readonly');

		if($('#update_id').val() != "")
		{
			$('#txt_booking_no').attr('disabled','disabled');
			$('#cbo_receive_basis').attr('disabled','disabled');
		}


		if(recieve_basis == 9)
		{
			//$('#txt_booking_no').removeAttr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').attr('disabled','disabled');
			$('#txt_batch_no').attr("placeholder","Dispaly");
			$('#cbo_body_part').attr('disabled','disabled');
			$('#cbo_dia_width_type').attr('disabled','disabled');
			$('#txt_fabric_desc').val('');
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').attr('disabled','disabled');
			$('#txt_fabric_desc').removeAttr("onDblClick");
			$('#txt_fabric_desc').attr("placeholder","Dispaly");
			$('#txt_color').val('');
			$('#txt_color').attr('disabled','disabled');
			$("#txt_order_no").attr("placeholder","Display");
			$("#txt_order_no").removeAttr("onDblClick");
		}
		else if(recieve_basis == 4 || recieve_basis==6)
		{
			//$('#txt_booking_no').attr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').removeAttr('disabled','disabled');
			$('#txt_batch_no').attr("placeholder","Write");
			$('#cbo_body_part').removeAttr('disabled','disabled');
			$('#txt_fabric_desc').val('');
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').removeAttr('disabled','disabled');
			$('#txt_fabric_desc').attr("onDblClick","openmypage_fabricDescription();");
			$('#txt_fabric_desc').attr("placeholder","Double Click For Search");
			$('#txt_color').val('');
			$('#txt_color').removeAttr('disabled','disabled');
			$('#cbo_dia_width_type').removeAttr('disabled','disabled');
			$("#txt_order_no").attr("placeholder","Double Click");
			$('#txt_order_no').attr('onDblClick','openmypage_order();');
		}
		else
		{
			if(recieve_basis==1)
			{
				$('#cbo_body_part').removeAttr('disabled','disabled');
				$("#txt_order_no").attr("placeholder","Double Click");
				$('#txt_order_no').attr('onDblClick','openmypage_order();');
			}
			else
			{
				$('#cbo_body_part').attr('disabled','disabled');
				$("#txt_order_no").attr("placeholder","Display");
				$("#txt_order_no").removeAttr("onDblClick");
			}
			$('#cbo_dia_width_type').removeAttr('disabled','disabled');
			//$('#txt_booking_no').removeAttr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').removeAttr('disabled','disabled');
			$('#txt_batch_no').attr("placeholder","Write");
			$('#txt_fabric_desc').val('');
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').attr('disabled','disabled');
			$('#txt_fabric_desc').removeAttr("onDblClick");
			$('#txt_fabric_desc').attr("placeholder","Dispaly");
			$('#txt_color').val('');
			$('#txt_color').removeAttr('disabled','disabled');
		}
	}

	function openmypage_wo_pi_production_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var receive_basis=$('#cbo_receive_basis').val();
		var roll_maintained=$('#roll_maintained').val();

		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}

		if(receive_basis==11)
		{
			var title = 'WO/PI/Production Selection Form';
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&receive_basis='+receive_basis+'&action=wo_pi_production_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1, resize=1, scrolling=0', '../');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
				var booking_data = hidden_booking_data.split("**");
				var wo_id = booking_data[0];
				var wo_no = booking_data[1];
				var fso_id = booking_data[2];
				var fso_no = booking_data[3];
				var booking_no = booking_data[4];
				var dyeing_source = booking_data[5];
				var dyeing_company = booking_data[6];


				$("#txt_booking_no").val(wo_no);
				$('#txt_booking_no_id').val(wo_id);
				$("#txt_sales_booking_no").val(booking_no);
				$("#txt_sales_order_id").val(fso_id);
				$("#txt_sales_order_no").val(fso_no);
				
				$('#booking_without_order').val(0);
				$('#cbouom').attr('disabled','disabled');

	
				freeze_window(5);
				$('#buyer_id').val('');
				$('#txt_color').val('');
				$('#buyer_name').val('');
				$('#txt_batch_id').val('');
				$('#txt_batch_no').val('');
				$('#txt_production_qty').val('');
				$('#all_po_id').val('');
				$('#save_data').val('');
				$("#txt_order_no").val('');
				$("#hidden_order_id").val('');
				$('#cbo_body_part').val(0);
				$('#txt_fabric_desc').val('');
				$('#fabric_desc_id').val('');
				$("#txt_gsm").val('');
				$("#txt_dia_width").val('');
				$("#txt_used_qty").val('');
				$("#txt_rate").val('');
				$("#txt_amount").val('');
				$('#previous_prod_id').val('');
				$('#product_id').val('');

				$('#txt_production_qty').attr('readonly','readonly');
				$('#txt_production_qty').attr('onClick','openmypage_po();');
				$('#txt_production_qty').attr('placeholder','Single Click');
				$('#txt_production_qty').attr('readonly','readonly');
show_list_view(wo_id+"**0**"+receive_basis+'**'+wo_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
				$('#cbo_dyeing_source').val(3);
				load_drop_down( 'requires/knit_finish_fabric_receive_controller', $('#cbo_dyeing_source').val()+'_'+cbo_company_id, 'load_drop_down_dyeing_com','dyeingcom_td');
				$('#cbo_dyeing_company').val(dyeing_company);
				$('#cbo_dyeing_source').attr('disabled','disabled');
				$('#cbo_dyeing_company').attr('disabled','disabled');
				//$('#cbo_location').attr('disabled','disabled');

				release_freezing();
				
			}
		}
		else if(receive_basis==14)
		{
			var title = 'Fabric Sale Order Form';
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&receive_basis='+receive_basis+'&action=fabric_sales_order_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose = function ()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
				var booking_data = hidden_booking_data.split("**");
				var mst_id = booking_data[0];
				var sales_booking_no = booking_data[1];
				var companyId = booking_data[2];
				var withinGroup = booking_data[3];

				if(withinGroup==1)
				{
					var booking_id = booking_data[4];
					//var dyeingSource = 1;
					//$('#cbo_dyeing_source').val(dyeingSource);
				}else {
					//var dyeingSource = 3;
					var booking_id = mst_id;
					//$('#cbo_dyeing_source').val(dyeingSource);
				}

				var booking_without_order =  booking_data[5];
				var knitting_company = booking_data[6];
				var sales_job_no = booking_data[7];

				$("#txt_booking_no").val(sales_job_no);
				$("#txt_sales_booking_no").val(sales_booking_no);
				$("#txt_sales_order_id").val(mst_id);
				$('#txt_booking_no_id').val(booking_id);
				$('#booking_without_order').val(booking_without_order);
				$('#cbouom').attr('disabled','disabled');

				//$('#cbo_dyeing_source').attr('disabled','disabled');
				//$('#cbo_dyeing_company').attr('disabled','disabled');


				show_list_view(mst_id+"**"+sales_booking_no+"**"+receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');

				load_drop_down( 'requires/knit_finish_fabric_receive_controller', dyeingSource+'_'+companyId, 'load_drop_down_dyeing_com','dyeingcom_td');
				$('#cbo_dyeing_company').val(knitting_company);
			}
		}
		else if(receive_basis==10)
		{
			var title = 'Delivery Challan Form';
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&receive_basis='+receive_basis+'&action=delivery_challan_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose = function ()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
				var booking_data = hidden_booking_data.split("**");
				var mst_id = booking_data[0];
				var sales_booking_no = booking_data[1];
				var companyId = booking_data[2];
				var withinGroup = booking_data[3];
				var dyeingSource = booking_data[10];
				var dyeingCompany = booking_data[11];

				$('#cbo_dyeing_source').val(dyeingSource);
				$('#cbo_dyeing_company').val(dyeingCompany);

				if(withinGroup==1)
				{
					var booking_id = mst_id;
				}else {
					var booking_id = mst_id;
				}
				var booking_without_order =  booking_data[5];
				var knitting_company = booking_data[6];
				var batch_id = booking_data[8];
				var sales_job_no = booking_data[9];
				var is_sales = booking_data[7];

				$("#txt_booking_no").val(booking_data[4]);
				$("#txt_sales_booking_no").val(sales_booking_no);
				$("#txt_sales_order_no").val(sales_job_no);
				$('#txt_booking_no_id').val(booking_id);
				$('#booking_without_order').val(booking_without_order);
				$('#cbouom').attr('disabled','disabled');
				$('#cbo_dyeing_source').attr('disabled','disabled');
				$('#cbo_dyeing_company').attr('disabled','disabled');

				show_list_view(mst_id+"**"+sales_booking_no+"**"+receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');

				load_drop_down( 'requires/knit_finish_fabric_receive_controller', dyeingSource+'_'+companyId, 'load_drop_down_dyeing_com','dyeingcom_td');

				if($('#process_costing_maintain').val()==1)
				{
					$('#txt_used_qty').attr('readonly','readonly');
					$('#txt_used_qty').attr('placeholder', 'Browse');
					$('#txt_used_qty').attr('onclick', 'proces_costing_popup()');
				}
				else
				{
					$('#txt_used_qty').removeAttr('readonly','readonly');
					$('#txt_used_qty').removeAttr('placeholder', 'Browse');
					$('#txt_used_qty').removeAttr('onclick','proces_costing_popup()');
				}
				//$('#cbo_dyeing_company').val(knitting_company);
			}
		}
	}

	function openmypage_order()
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var receive_basis=$('#cbo_receive_basis').val();
		var hidden_order_id=$('#hidden_order_id').val();
		var buyer_name=$('#buyer_id').val();

		if(form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}

		if(receive_basis==1 || receive_basis==4 || receive_basis==6)
		{
			var title = 'PO Info';
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&hidden_order_id='+hidden_order_id+'&buyer_name='+buyer_name+'&action=po_search_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value;
				var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value;

				$("#txt_order_no").val(hidden_order_no);
				$("#hidden_order_id").val(hidden_order_id);
			}
		}
	}

	function put_data_dtls_part(id,type,page_path)
	{
		var company_id = $('#cbo_company_id').val();
		var roll_maintained=$('#roll_maintained').val();
		var barcode_generation = $('#barcode_generation').val();
		var booking_without_order = $('#booking_without_order').val();
		get_php_form_data(id+"**"+$('#roll_maintained').val()+"**"+$('#process_costing_maintain').val(), type, page_path );

		if(roll_maintained==1)
		{
			show_list_view("'"+id+"**"+barcode_generation+"**"+booking_without_order+"'",'show_roll_listview','roll_details_list_view','requires/knit_finish_fabric_receive_controller','');
		}
		else
		{
			$('#roll_details_list_view').html('');
		}
	}

	function openmypage_fabricDescription()
	{
		var title = 'Fabric Description Info';
		var page_link = 'requires/knit_finish_fabric_receive_controller.php?action=fabricDescription_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
			var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
			var fabric_desc_id=this.contentDoc.getElementById("fabric_desc_id").value; //Access form field with id="emailfield"

			$('#txt_fabric_desc').val(theename);
			$('#fabric_desc_id').val(fabric_desc_id);
			$('#txt_gsm').val(theegsm);
		}
	}

	function openmypage_po()
	{
		var receive_basis=$('#cbo_receive_basis').val();
		var roll_maintained = $('#roll_maintained').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var txt_production_qty = $('#txt_production_qty').val();
		var distribution_method = $('#distribution_method_id').val();
		var txt_booking_no=$("#txt_booking_no").val();
		var txt_booking_no_id=$("#txt_booking_no_id").val();
		var txt_sales_booking_no=encodeURIComponent($("#txt_sales_booking_no").val());
		var hidden_order_id=$("#hidden_order_id").val();
		var txt_fabric_desc=$('#txt_fabric_desc').val();
		var fabric_desc_id=$('#fabric_desc_id').val();
		var txt_batch_id=$('#txt_batch_id').val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var process_costing_maintain = $('#process_costing_maintain').val();
		var booking_without_order = $('#booking_without_order').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var product_id = $('#product_id').val();
		var txt_fabric_shade = $('#txt_fabric_shade').val();
		var fso_dtls_id = $("#txt_fso_dtls_id").val();
		var fso_company_id = $("#txt_fso_company_id").val();
		var cbouom = $("#cbouom").val();
		var txt_gsm = $("#txt_gsm").val();
		var txt_dia_width = $("#txt_dia_width").val();

		//var finish_production_dtls_id=$('#finish_production_dtls_id').val();

		if(form_validation('cbo_company_id*cbo_receive_basis*cbo_body_part*txt_fabric_desc','Company*Receive Basis*Fabric Description')==false)
		{
			return;
		}

		if(receive_basis==1 || receive_basis==4 || receive_basis==6)
		{
			if(form_validation('txt_order_no','Order Numbers')==false)
			{
				alert("Please Select Order Numbers.");
				return;
			}
		}
		else if((receive_basis==2 || receive_basis==11) && txt_booking_no_id=="")
		{
			alert("Please Select WO No.");
			$('#txt_booking_no').focus();
			return;
		}
		else if(receive_basis==9 && txt_fabric_desc=="")
		{
			alert("Please Select Fabric Description.");
			$('#txt_fabric_desc').focus();
			return false;
		}

		if(roll_maintained==1)
		{
			if(receive_basis==2 || receive_basis==9 || receive_basis==11) popup_width='1300px';
			else popup_width='1250px';
		}
		else
		{
			if(receive_basis==2 || receive_basis==9 || receive_basis==11) popup_width='1070x';
			else popup_width='1070px';
		}

		if(receive_basis==14)
		{
			var title = 'FSO Info';
		}else {
			var title = 'PO Info';
		}

		var pre_cost_fab_conv_cost_dtls_id =$("#txt_pre_cost_fab_conv_cost_dtls_id").val();
		var fabricColorId =$("#txt_fabric_color_id").val();
		var body_part_id = $("#cbo_body_part").val();

		if(receive_basis == 10){
			var action = "po_popup_sales_order";
		}
		else if(receive_basis == 11){
			var action = "po_popup_service_booking_wo";
		}
		else{
			var action = "po_popup";
		}

		var page_link = 'requires/knit_finish_fabric_receive_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&txt_booking_no='+txt_booking_no+'&txt_booking_no_id='+txt_booking_no_id+'&booking_without_order='+booking_without_order+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_production_qty='+txt_production_qty+'&prev_distribution_method='+distribution_method+'&hidden_order_id='+hidden_order_id+'&txt_batch_id='+txt_batch_id+'&fabric_desc_id='+fabric_desc_id+'&txt_deleted_id='+txt_deleted_id+'&fso_dtls_id='+fso_dtls_id+'&fso_company_id='+fso_company_id+'&txt_sales_booking_no='+txt_sales_booking_no+'&pre_cost_fab_conv_cost_dtls_id='+pre_cost_fab_conv_cost_dtls_id+'&fabricColorId='+fabricColorId+'&body_part_id='+body_part_id+'&product_id='+product_id+'&txt_fabric_shade='+txt_fabric_shade+'&cbouom='+cbouom+'&txt_gsm='+txt_gsm+'&txt_dia_width='+txt_dia_width+'&action='+action;//+'&finish_production_dtls_id='+finish_production_dtls_id


		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_finish_qnty=this.contentDoc.getElementById("tot_finish_qnty").value; //Access form field with id="emailfield"
			var tot_reject_qnty=this.contentDoc.getElementById("tot_reject_qnty").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var order_nos=this.contentDoc.getElementById("order_nos").value; //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("buyer_name").value; //Access form field with id="emailfield"
			var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;
			var number_of_roll=this.contentDoc.getElementById("number_of_roll").value;
			var sales_order_type=this.contentDoc.getElementById("sales_order_type").value;

			$('#save_data').val(save_string);
			$('#txt_production_qty').val(tot_finish_qnty);
			$('#txt_reject_qty').val(tot_reject_qnty);
			$('#all_po_id').val(all_po_id);
			$('#hidden_order_id').val(all_po_id);
			$('#txt_order_no').val(order_nos);
			$('#buyer_name').val(buyer_name);
			$('#buyer_id').val(buyer_id);
			$('#distribution_method_id').val(distribution_method);
			$('#txt_sales_order_type').val(sales_order_type);

			if(roll_maintained==1)
			{
				$('#txt_no_of_roll').val(number_of_roll);
				$('#txt_deleted_id').val(hide_deleted_id);
			}
			else
			{
				$('#txt_deleted_id').val('');
			}
			if(receive_basis != 10){
				get_php_form_data(all_po_id, 'load_color', 'requires/knit_finish_fabric_receive_controller');
			}
			if(process_costing_maintain==1)
			{
				$('#txt_used_qty').focus();
				if(receive_basis==11)
				{
					get_php_form_data(all_po_id, 'load_color_service_booking', 'requires/knit_finish_fabric_receive_controller');
				}
			}

			var amount=tot_finish_qnty*$('#txt_rate').val();
			var order_amount=tot_finish_qnty*$('#txt_order_rate').val();
			$('#txt_amount').val(number_format(amount,2,'.' , ""));
			$('#txt_order_amount').val(number_format(order_amount,2,'.' , ""));
		}
	}

	function openmypage_systemid()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'System ID Info';
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=1180px,height=390px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var finish_fabric_id=this.contentDoc.getElementById("hidden_sys_id").value;
				var hidden_booking_no=this.contentDoc.getElementById("hidden_booking_no").value;

				reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container','','','','roll_maintained*process_costing_maintain*textile_sales_maintain');
				get_php_form_data(finish_fabric_id, "populate_data_from_finish_fabric", "requires/knit_finish_fabric_receive_controller" );

				var booking_pi_production_no = $('#txt_booking_no').val();
				var sales_booking_no = $('#txt_sales_booking_no').val();
				var booking_pi_production_id = $('#txt_booking_no_id').val();
				var booking_without_order = $('#booking_without_order').val();
				var cbo_receive_basis = $('#cbo_receive_basis').val();

				if(cbo_receive_basis==1 || cbo_receive_basis==2)
				{
					show_list_view(sales_booking_no+"**"+booking_without_order+"**"+cbo_receive_basis+"**"+booking_pi_production_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
				}
				else if(cbo_receive_basis==9)
				{
					show_list_view(sales_booking_no+"**"+booking_without_order+"**"+cbo_receive_basis+"**"+booking_pi_production_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');

				}else if(cbo_receive_basis==11){
					show_list_view(booking_pi_production_id+"**"+booking_without_order+"**"+cbo_receive_basis+"**"+booking_pi_production_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
				}
				else if(cbo_receive_basis==10)
				{
					show_list_view(booking_pi_production_id+"**"+booking_without_order+"**"+cbo_receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
				}


				show_list_view(finish_fabric_id,'show_finish_fabric_listview','list_container_finishing','requires/knit_finish_fabric_receive_controller','');
				if(cbo_receive_basis==14 || cbo_receive_basis==11)
				{
					$('#cbouom').attr('disabled', 'disabled');
				}else if(cbo_receive_basis==10)
				{
					$('#cbouom').attr('disabled', 'disabled');
					$('#txt_batch_no').attr('disabled', 'disabled');
				}
				else
				{
					$('#txt_used_qty').removeAttr('placeholder');
					$('#txt_used_qty').removeAttr('onclick');
				}
				set_button_status(0, permission, 'fnc_finish_receive_entry',1,1);
			}
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/knit_finish_fabric_receive_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_finish_receive_entry( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val()+'*'+$('#txt_booking_no_id').val(),'finish_fabric_receive_print','requires/knit_finish_fabric_receive_controller');
			show_msg("3");
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			if(operation==1)
			{
				var cbo_dyeing_source=$('#cbo_dyeing_source').val();
				var update_dtls_id=$('#update_dtls_id').val();
				var cbo_location=$('#cbo_location').val();
				var txt_system_id=$('#txt_system_id').val();
				var cbo_dyeing_company=$('#cbo_dyeing_company').val();
				var cbo_company_id=$('#cbo_company_id').val();

				var po_id=$('#all_po_id').val();
				var fabric_desc_id=$('#fabric_desc_id').val();
				var cbo_body_part=$('#cbo_body_part').val();
				var product_id=$('#product_id').val();
				var color_id=$('#txt_color_id').val();
				var batch_id=$('#txt_batch_id').val();

				var response=return_global_ajax_value( cbo_company_id+"**"+cbo_dyeing_source+"**"+cbo_dyeing_company+"**"+cbo_location+"**"+txt_system_id+"**"+po_id+"**"+fabric_desc_id+"**"+cbo_body_part+"**"+product_id+"**"+color_id+"**"+batch_id, 'check_update_finishing_bill', '', 'requires/knit_finish_fabric_receive_controller');
				var response=response.split("_");
				if(response[0]==1)
				{
					alert('Update Not Allow, Already Bill Issued. '+response[2]);
					return;
				}
			}

			var cbo_receive_basis=$('#cbo_receive_basis').val();
			var cbo_dyeing_source=$('#cbo_dyeing_source').val();
			var cbo_location=$('#cbo_location').val();
			var process_costing_maintain=$('#process_costing_maintain').val();

			if((cbo_receive_basis==1 || cbo_receive_basis==2 || cbo_receive_basis==4 || cbo_receive_basis==6) && cbo_dyeing_source!=3)
			{
				if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_store_name*cbo_dyeing_source*cbo_dyeing_company*cbo_location','Company*Receive Basis*Receive Date*Challan No*Store Name*Dyeing Source*Dyeing Company*Location')==false )
				{
					return;
				}
			}
			else
			{
				if((cbo_receive_basis==9 || cbo_receive_basis==10) && cbo_dyeing_source==3)
				{
					if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_store_name*cbo_dyeing_source*cbo_dyeing_company','Company*Receive Basis*Receive Date*Challan No*Store Name*Dyeing Source*Dyeing Company')==false )
					{
						return;
					}
				}
				else
				{
					if(cbo_dyeing_source!=3)
					{
						if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_store_name*cbo_dyeing_source*cbo_dyeing_company*cbo_location','Company*Receive Basis*Receive Date*Challan No*Challan No*Store Name*Dyeing Source*Dyeing Company*Location')==false )
						{
							return;
						}
					}
					else
					{
						if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_store_name*cbo_dyeing_source*cbo_dyeing_company','Company*Receive Basis*Receive Date*Challan No*Store Name*Dyeing Source*Dyeing Company')==false )
						{
							return;
						}
					}
				}
			}
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][225]);?>')
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][225]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][225]);?>')==false)
				{
					
					return;
				}
			}


			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Recevie Date Can not Be Greater Than Current Date");
				return;
			}

			if(cbo_receive_basis==1 || cbo_receive_basis==2 || cbo_receive_basis==9)
			{
				if( form_validation('txt_booking_no','WO/PI/Production')==false )
				{
					alert("Please Select WO/PI/Production");
					return;
				}
			}
			if(process_costing_maintain==1)
			{
				if(cbo_receive_basis==11 || cbo_receive_basis==9)
				{
					if( form_validation('txt_used_qty','Grey used')==false )
					{
						alert("Please Select Grey Used Qty");
						return;
					}
					if($("#booking_without_order").val()!=0)
					{
						if($("#txt_production_qty").val()!=$("#check_production_qty").val())
						{
							alert("Please Select Grey Used Qty again");
							return;
						}
					}
				}
			}
			if(cbo_dyeing_source==1 && cbo_location==0)
			{
				alert("Please Select Location");
				$('#cbo_location').focus();
				return;
			}
			if($('#txt_grey_issue_challan_no').val()=="")
			{
				var r=confirm("Press \"OK\" to Insert Grey Issue Challan No\nPress \"Cancel\" to Insert Grrey Issue Challan No Blank");
				if (r==true)
				{
					$('#txt_grey_issue_challan_no').focus();
					return;
				}
			}
			if( form_validation('txt_batch_no*cbo_body_part*txt_fabric_desc*txt_production_qty*txt_color*cbouom','Batch No*Body Part*Fabric Description*Production Qnty*Color*UOM')==false )
			{
				return;
			}

			var txt_sales_order_type=$('#txt_sales_order_type').val();
			var textile_sales_maintain=$('#textile_sales_maintain').val();
			
			if(txt_sales_order_type ==2 && textile_sales_maintain ==2)
			{
				//N. B. Rate is not mandatory when sales order type is service and textile sales maintain is NO
			}
			else
			{
				if(cbo_receive_basis == 14 )
				{
					if( form_validation('txt_rate*txt_fab_rate','Total Rate*Fabric Rate')==false )
					{
						return;
					}
					if($("#txt_rate").val() == "0.00")
					{
						alert("Rate can not be zero.");
						$("#txt_order_rate").focus();
						return;
					}
				}
				else
				{
					if( form_validation('txt_rate','Rate')==false )
					{
						return;
					}
					if($("#txt_rate").val() == "0.00")
					{
						alert("Rate can not be zero.");
						$("#txt_rate").focus();
						return;
					}
				}
			}
			var pre_cost_fab_conv_cost_dtls_id = $("#txt_pre_cost_fab_conv_cost_dtls_id").val();
			var fabric_color_id = $("#txt_fabric_color_id").val();

			// Store upto validation start
			var store_update_upto=$('#store_update_upto').val()*1;
			var cbo_floor=$('#cbo_floor').val()*1;
			var cbo_room=$('#cbo_room').val()*1;
			var txt_rack=$('#txt_rack').val()*1;
			var txt_shelf=$('#txt_shelf').val()*1;
			
			if(store_update_upto > 1)
			{
				if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
				{
					alert("Up To Rack Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==3 && cbo_floor==0 || cbo_room==0)
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==2 && cbo_floor==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
			}
			// Store upto validation End


			var data="action=save_update_delete&operation="+operation+'&pre_cost_fab_conv_cost_dtls_id='+pre_cost_fab_conv_cost_dtls_id+'&fabric_color_id='+fabric_color_id+get_submitted_data_string('cbo_company_id*cbo_receive_basis*txt_system_id*txt_receive_date*cbo_dyeing_source*cbo_dyeing_company*cbo_dyeing_location*txt_challan_no*cbo_location*cbo_store_name*txt_booking_no*txt_booking_no_id*booking_without_order*txt_batch_no*txt_batch_id*cbo_body_part*txt_fabric_desc*fabric_desc_id*txt_color*txt_color_id*txt_gsm*txt_dia_width*txt_production_qty*txt_reject_qty*buyer_id*cbo_machine_name*cbo_floor*cbo_room*txt_rack*txt_shelf*update_id*update_dtls_id*save_data*all_po_id*update_trans_id*previous_prod_id*product_id*hidden_receive_qnty*roll_maintained*txt_no_of_roll*txt_deleted_id*cbo_dia_width_type*txt_grey_issue_challan_no*txt_rate*txt_amount*hidden_receive_amnt*hidden_dying_charge*save_rate_string*txt_used_qty*knitting_charge_string*process_string*process_costing_maintain*fin_prod_dtls_id*cbouom*txt_qc_name*txt_hidden_qc_name*txt_order_rate*txt_order_amount*cbo_fabric_type*txt_sales_booking_no*hdn_is_sales*hdn_currency_id*txt_sales_order_id*txt_sales_order_no*txt_fabric_shade*txt_fab_rate',"../../");
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/knit_finish_fabric_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_finish_receive_entry_reponse;
		}
	}

	function fnc_finish_receive_entry_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
			{
				show_msg(reponse[0]);
				if (reponse[0]==2 && reponse[4]==1) // is mst delete reset form
				{
					//alert(reponse[4]);
					reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container*roll_details_list_view','','','disable_enable_fields(\'cbo_company_id\');set_receive_basis();');
				}
				else
				{
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_system_id').value = reponse[2];
					$('#cbo_company_id').attr('disabled','disabled');
					$('#cbo_receive_basis').attr('disabled','disabled');
					$('#txt_booking_no').attr('disabled','disabled');
					show_list_view(reponse[1],'show_finish_fabric_listview','list_container_finishing','requires/knit_finish_fabric_receive_controller','');

					reset_form('finishFabricEntry_1','roll_details_list_view','','','','update_id*txt_system_id*cbo_receive_basis*cbo_company_id*txt_receive_date*cbo_dyeing_source*cbo_dyeing_company*cbo_dyeing_location*txt_challan_no*cbo_location*cbo_store_name*roll_maintained*txt_booking_no*txt_booking_no_id*booking_without_order*txt_grey_issue_challan_no*process_costing_maintain*txt_sales_booking_no*store_update_upto*textile_sales_maintain');
				}
				set_button_status(0, permission, 'fnc_finish_receive_entry',1,1);
				release_freezing();
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				$('#txt_receive_date').focus();
				return;
			}
			else if(reponse[0]==30)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				$('#txt_production_qty').focus();
				return;
			}
			else
			{
				show_msg(reponse[0]);
				release_freezing();
			}
		}
	}

	function set_form_data(data,fso_dtls_id="")
	{
		var receive_basis = $('#cbo_receive_basis').val();
		var roll_maintained = $('#roll_maintained').val();
		var process_costing_maintain=$('#process_costing_maintain').val();

		$('#save_data').val("");
		$('#txt_production_qty').val("");
		$('#txt_amount').val("");
		$('#txt_order_amount').val("");
		$('#txt_rate').val("");
		$('#txt_order_rate').val("");
		$('#txt_fab_rate').val("");
		$('#txt_reject_qty').val("");

		var data=data.split("**");
		var pi_basis=data[13];
		var pre_body_part_ids=data[11];

		$('#cbo_body_part').val(data[0]);

		$('#txt_fabric_desc').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_dia_width').val(data[3]);
		$('#fabric_desc_id').val(data[4]);
		$("#hdn_currency_id").val(data[6]);
		$('#hidden_order_id').val(data[7]);
		$('#txt_order_no').val(data[8]);
		$('#cbouom').val(data[9]);
		$('#txt_color').val(data[10]);
		
		//new development for balance qty
		$('#cbo_body_part').val(data[0]);
		$('#cbo_dia_width_type').val(data[14]);

		if(receive_basis==10)
		{
			$("#txt_batch_no").val(data[18]).attr('disabled','disabled');
			$("#txt_batch_id").val(data[19]);
			$("#hdn_is_sales").val(data[20]);
			$("#txt_fso_dtls_id").val(fso_dtls_id);
			$("#product_id").val(data[21]);
			$('#hidden_order_id').val(data[22]);
			$('#txt_fabric_color_id').val(data[23]);
			//$('#txt_order_no').val($("#txt_sales_order_no").val());
			$('#cbouom').val(data[9]);
			$('#cbo_fabric_type').val(data[24]);
			$('#txt_no_of_roll').val(data[25]);
			$('#txt_fabric_shade').val(data[26]);

			if($("#process_costing_maintain").val() != 1)
			{
				var rate = number_format(data[27],4,".","");
				if(rate == "0.0000")
				{
					$('#txt_fab_rate').attr('onclick',"openmypage_rate()");
				}
				else
				{
					$("#txt_fab_rate").removeAttr("onclick");
					$('#txt_fab_rate').val(rate);
				}
			}

			$('#txt_sales_booking_no').val(data[28]);
			//$('#txt_order_no').val(data[29]);

		}
		else if(receive_basis==11)
		{
			$('#txt_fabric_color_id').val(data[16]);
			$('#txt_fab_rate').val(data[12]);
			$("#txt_color").attr('disabled',true);
			$("#hdn_is_sales").val("1");
		}
		else if(receive_basis==14) 
		{
			if($("#process_costing_maintain").val() != 1)
			{
				$('#txt_fab_rate').val(data[12]);
			}

			$("#txt_fso_dtls_id").val(fso_dtls_id);
			$("#txt_fso_company_id").val(data[15]);
			$("#txt_fabric_color_id").val(data[18])
			$("#txt_color").attr('disabled',true);
			
			$("#hdn_is_sales").val("1");
		}

		if($("#process_costing_maintain").val() == 1)
		{
			$('#txt_order_rate').removeAttr('onclick');
		}
		else
		{
			$('#txt_order_rate').attr('onclick',"openmypage_api_rate()");
			$('#txt_order_rate').attr('placeholder',"click");

			var total_rate = $('#txt_fab_rate').val()*1+$('#txt_order_rate').val()*1;
			$('#txt_rate').val(total_rate);
			var tot_finish_qnty = $('#txt_production_qty').val();
			var amount=tot_finish_qnty*$('#txt_rate').val();
			$('#txt_amount').val(number_format(amount,4,'.' , ""));
			$('#txt_order_amount').val(number_format(amount,4,'.' , ""));
		}
		$("#txt_gsm, #txt_dia_width").attr('disabled','disabled');
		if(receive_basis==10 || receive_basis==14) 
		{
			$("#cbo_fabric_type").attr('disabled','disabled');
		}

		set_button_status(0, permission, 'fnc_finish_receive_entry',1,1);
		
		$('#update_dtls_id').val("");
		$('#update_trans_id').val("");
		
	}

	function openmypage_rate()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var fabric_color_id = $('#txt_fabric_color_id').val();
		var order_id = $('#hidden_order_id').val();
		var fabric_desc_id = $('#fabric_desc_id').val();
		var cbo_body_part = $('#cbo_body_part').val();

		var title = 'Rate Details Info';
		var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id=' + cbo_company_id + '&fabric_color_id=' + fabric_color_id + '&fabric_desc_id='+fabric_desc_id + '&order_id='+ order_id + '&cbo_body_part=' + cbo_body_part + '&action=rate_info_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=480px,height=390px,center=1,resize=0,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_rate=this.contentDoc.getElementById("hidden_rate").value;
			$('#txt_fab_rate').val(hidden_rate);
			hidden_rate = hidden_rate*1;

			var total_rate = hidden_rate+$('#txt_order_rate').val()*1;
			$('#txt_rate').val(total_rate);
			var tot_finish_qnty = $('#txt_production_qty').val();
			var amount=tot_finish_qnty*$('#txt_rate').val();
			//var order_amount=tot_finish_qnty*$('#txt_order_rate').val();
			$('#txt_amount').val(number_format(amount,4,'.' , ""));
			$('#txt_order_amount').val(number_format(amount,4,'.' , ""));
		}
	}

	function openmypage_api_rate()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var fabric_color_id = $('#txt_fabric_color_id').val();
		var order_id = $('#hidden_order_id').val();
		var fabric_desc_id = $('#fabric_desc_id').val();
		var cbo_body_part = $('#cbo_body_part').val(); 
		var cbo_receive_basis = $('#cbo_receive_basis').val();
		var txt_batch_id = $('#txt_batch_id').val(); 
		var txt_sales_order_id = $('#txt_sales_order_id').val(); 


		if(cbo_receive_basis ==14){
			order_id = txt_sales_order_id;
		}

		var title = 'Rate Details Info';
		var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id=' + cbo_company_id + '&fabric_color_id=' + fabric_color_id + '&fabric_desc_id='+fabric_desc_id + '&order_id='+ order_id + '&cbo_body_part=' + cbo_body_part + '&cbo_receive_basis=' + cbo_receive_basis + '&txt_batch_id=' + txt_batch_id + '&action=aop_rate_info_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=480px,height=390px,center=1,resize=0,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_rate=this.contentDoc.getElementById("hidden_rate").value;
			$('#txt_order_rate').val(hidden_rate);

			// Here
			hidden_rate = hidden_rate*1;
			var txt_fab_rate = $('#txt_fab_rate').val()*1;

			var total_rate = hidden_rate + txt_fab_rate;

			$('#txt_rate').val(total_rate);
			var tot_finish_qnty = $('#txt_production_qty').val();
			var amount=tot_finish_qnty*total_rate;
			//var order_amount=tot_finish_qnty*$('#txt_order_rate').val();
			$('#txt_amount').val(number_format(amount,4,'.' , ""));
			$('#txt_order_amount').val(number_format(amount,4,'.' , ""));
		}
	}

	function load_receive_basis()
	{
		var company_id = $("#cbo_company_id").val();
		var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
		$("#cbo_receive_basis").val(0);
		$("#cbo_receive_basis option[value='4']").show();
		if(independent_control_arr[company_id]==1)
		{
			$("#cbo_receive_basis option[value='4']").hide();
		}


		var roll_maintained=$('#roll_maintained').val();
		var process_costing_maintain=$('#process_costing_maintain').val();
		/*if(roll_maintained==1)
		{
			$("#cbo_receive_basis option[value='9']").remove();
		}
		else
		{
			$("#cbo_receive_basis option[value='9']").remove();
			$("#cbo_receive_basis option[value='11']").remove();
			$("#cbo_receive_basis").append('<option value="9">Production</option>');
			$("#cbo_receive_basis").append('<option value="11">Service Booking Based</option>');
		}*/
		if(process_costing_maintain==1)
		{
			$('#grey_used_td').css('color','blue');
		}
		else
		{
			$('#grey_used_td').css('color','black');
		}


		var data='cbo_company_id='+company_id+'&action=upto_variable_settings';
	    var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() 
	    {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("store_update_upto").value = this.responseText;
	            

				
	        }
	    }
	    xmlhttp.open("POST", "requires/knit_finish_fabric_receive_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);
	}

	function store_update_upto_disable() 
	{
		var store_update_upto=$('#store_update_upto').val()*1;	 
		if(store_update_upto==4)
		{
			$('#txt_shelf').prop("disabled", true);
		}
		else if(store_update_upto==3)
		{
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);
		}
		else if(store_update_upto==2)
		{	
			$('#cbo_room').prop("disabled", true);
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);	
		}
		else if(store_update_upto==1)
		{
			$('#cbo_floor').prop("disabled", true);
			$('#cbo_room').prop("disabled", true);
			$('#txt_rack').prop("disabled", true);
			$('#txt_shelf').prop("disabled", true);	
		}
	}

	function load_location()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_dyeing_source = $('#cbo_dyeing_source').val();
		var cbo_dyeing_company = $('#cbo_dyeing_company').val();//
		if(cbo_dyeing_source==1)
		{
			load_drop_down( 'requires/knit_finish_fabric_receive_controller',cbo_dyeing_company, 'load_drop_down_location', 'location_td_dyeing' );
		}
		/*else
		{
			load_drop_down( 'requires/knit_finish_fabric_receive_controller',cbo_company_id, 'load_drop_down_location', 'location_td_dyeing' );
		}*/
		check_location(cbo_dyeing_source);
	}
	function check_location(knit_source)
	{
		if(knit_source==3)
		{

			$('#cbo_dyeing_location').attr('disabled','disabled');
		}
		else
		{
			$('#cbo_dyeing_location').removeAttr('disabled','disabled');
		}
	}

	function issue_challan_no() //Grey Fab Issue Challan
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_challan_no_popup';
			var title='Issue Challan Info';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var issue_challan=this.contentDoc.getElementById("issue_challan").value;
				if(trim(issue_challan)!="")
				{
					freeze_window(5);
					$('#txt_grey_issue_challan_no').val(issue_challan);

					release_freezing();
				}
			}
		}
	}


	function qc_name_fnc() //Grey Fab Issue Challan
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=qc_name_popup';
			var title='QC Name Info';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=350px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var qc_name=this.contentDoc.getElementById("qc_name").value;
				data=qc_name.split("_");
				if(trim(qc_name)!="")
				{
					freeze_window(5);
					$('#txt_qc_name').val(data[1]);
					$('#txt_hidden_qc_name').val(data[0]);

					release_freezing();
				}
			}
		}
	}
	function fnc_check_issue(issue_num)
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}

		if(issue_num!="")
		{
			var issue_result = return_global_ajax_value(issue_num, 'issue_num_check', '', 'requires/knit_finish_fabric_receive_controller');
			if(issue_result=="" || issue_result==0)
			{
				alert("Challan Number Not Found");
				$('#txt_grey_issue_challan_no').val("");
			}
		}
	}

	function proces_costing_popup()
	{
		var txt_job_no= $('#txt_job_no').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var booking_id = $('#txt_booking_no_id').val();
		var fabric_description_id=$('#fabric_desc_id').val();
		var txt_receive_qnty=$('#txt_production_qty').val();
		var txt_receive_date=$('#txt_receive_date').val();
		var update_dtls_id=$('#update_dtls_id').val();
		var update_id=$('#update_id').val();
		var kitting_charge=$("#hidden_dying_charge").val();
		var booking_without_order = $('#booking_without_order').val();
		var hdn_is_sales = $('#hdn_is_sales').val();
		var save_data =$('#save_data').val();
		var name_color =$('#txt_color').val();
		var txt_batch_id = $('#txt_batch_id').val();
		if (form_validation('txt_booking_no*txt_production_qty*txt_receive_date*txt_color','WO/PI/Production*QC Pass Qty*Receive Date*Color')==false)
		{
			return;
		}

		var title = 'Fabric Cost Info';
		var page_link='requires/knit_finish_fabric_receive_controller.php?recieve_basis='+recieve_basis+'&booking_id='+booking_id+'&booking_without_order='+booking_without_order+'&fabric_description_id='+fabric_description_id+'&txt_receive_qnty='+txt_receive_qnty+'&txt_job_no='+txt_job_no+'&txt_receive_date='+txt_receive_date+'&update_dtls_id='+update_dtls_id+'&update_id='+update_id+'&kitting_charge='+kitting_charge+'&save_data='+save_data+'&name_color='+name_color+'&is_sales='+hdn_is_sales+'&txt_batch_id='+txt_batch_id+'&action=yarn_lot_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=250px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hidden_process_string").value;
			var theename=this.contentDoc.getElementById("hidden_knitting_rate").value;
			$('#knitting_charge_string').val(theename);
			$('#process_string').val(theemail);
			if(theename!="")
			{
				var popup_value=theename.split("*");
				var process_string=theemail.split("*");
				rate=(popup_value[0]*1)+(popup_value[1]*1);
				amount=	($('#txt_production_qty').val()*1)*rate;
				$('#hidden_dying_charge').val(popup_value[0]);
				$('#txt_used_qty').val(process_string[1]);
				$('#txt_rate').val(number_format(rate,4,'.' , ""));
				$('#txt_fab_rate').val(number_format(rate,4,'.' , ""));
				$('#txt_amount').val(number_format(amount,4,'.' , ""));
				$('#check_production_qty').val($('#txt_production_qty').val());
			}
		}
	}

	function fn_report_generated(type)
	{
		var rec_basic=$('#cbo_receive_basis').val();
		if(type==2){

			if(rec_basic==1){
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print_2','requires/knit_finish_fabric_receive_controller');
				return;
			}
			else{
				alert('Print 2 generate by PI Basis');
			}
		}

		if(type==3)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print_3','requires/knit_finish_fabric_receive_controller');

			return;
		}

	}

	function check_all_report()
	{
		$("input[name=chkBundle]").each(function(index, element)
		{
			if( $('#check_all').prop('checked')==true)
				$(this).attr('checked','true');
			else
				$(this).removeAttr('checked');
		});
	}

	function fnc_send_printer_text()
	{
		var dtls_id=$('#update_dtls_id').val();
		var mst_id=$('#update_id').val();
		var booking_no=$('#txt_booking_no').val();
		if(dtls_id=="")
		{
			alert("Save First");
			return;
		}
		var data="";
		var error=1;
		$("input[name=chkBundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				var roll_id=$('#txtRollTableId_'+idd[1] ).val();
				if(roll_id!="")
				{
					if(data=="") data=$('#txtRollTableId_'+idd[1] ).val(); else data=data+","+$('#txtRollTableId_'+idd[1] ).val();
				}
				else
				{
					$(this).prop('checked',false);
				}
			}
		});

		if( error==1 )
		{
			alert('No data selected');
			return;
		}

		data=data+"***"+dtls_id+"***"+booking_no+"******"+mst_id;
		var url=return_ajax_request_value(data, "report_barcode_text_file", "requires/knit_finish_fabric_receive_controller");
		window.open("requires/"+trim(url)+".zip","##");
	}

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:930px; float:left;" align="center">
				<fieldset style="width:920px;">
					<legend>Finish Fabric Receive Entry</legend>
					<fieldset>
						<table cellpadding="0" cellspacing="2" width="810" border="0">
							<tr>
								<td colspan="3" align="right"><strong>System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
								</td>
							</tr>
							<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company Name</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/knit_finish_fabric_receive_controller', this.value, 'load_drop_down_location_lc', 'location_td' );get_php_form_data(this.value,'roll_maintained','requires/knit_finish_fabric_receive_controller' );load_receive_basis();" );
									?>
								</td>
								<td class="must_entry_caption">Receive Basis</td>
								<td>
									<?
									//Service booking basis name changed to service booking(WO)
									$receive_basis_arr[11]='Service Booking (WO)'; 
									echo create_drop_down("cbo_receive_basis",160,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis();","",'14,10,11');//1,2,4,6,9,11,
									?>
								</td>

								<td class="must_entry_caption">Receive Date</td>
								<td>
									<input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:150px;" value="<? echo date("d-m-Y"); ?>" readonly>
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Receive Challan No.</td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" maxlength="20" title="Maximum 20 Character" />
								</td>
								<td>WO/PI/Production</td>
								<td>
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_production_popup();" readonly>
									<input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" class="text_boxes">
									<input type="hidden" name="booking_without_order" id="booking_without_order"/>
									<input type="hidden" name="txt_sales_booking_no" id="txt_sales_booking_no"/>
									<input type="hidden" name="txt_sales_order_no" id="txt_sales_order_no"/>
									<input type="hidden" name="txt_sales_order_id" id="txt_sales_order_id"/>
									<input type="hidden" name="hdn_is_sales" id="hdn_is_sales"/>

								</td>
								<td class="must_entry_caption">Location</td>
								<td id="location_td">
									<?
									echo create_drop_down("cbo_location", 162, $blank_array,"", 1,"-- Select Location --", 0,"");

									?>
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Dyeing Source</td>
								<td>
									<?
									echo create_drop_down("cbo_dyeing_source", 152, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/knit_finish_fabric_receive_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_dyeing_com','dyeingcom_td');load_location();check_location(this.value);","","1,3");
									?>
								</td>
								<td class="must_entry_caption">Dyeing Company</td>
								<td id="dyeingcom_td">
									<?
									echo create_drop_down("cbo_dyeing_company", 162, $blank_array,"", 1,"-- Select Dyeing Company --", 0,"");
									?>
								</td>
								<td>Dyeing Location</td>
								<td id="location_td_dyeing">
									<?
									echo create_drop_down("cbo_dyeing_location", 162, $blank_array,"", 1,"-- Select Location --", 0,"");
									?>
								</td>
							</tr>
							<tr>

								<td class="must_entry_caption"> Store Name </td>
								<td id="store_td">
									<?
									echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
									?>
								</td>
								<td>Order Numbers</td>
								<td colspan="">
									<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:140px" readonly placeholder="Display" />
									<input type="hidden" id="hidden_order_id" />
								</td>
								<td>QC Name</td>
								<td >
									<input type="text" name="txt_qc_name" id="txt_qc_name" class="text_boxes" style="width:150px"   readonly placeholder="Browse" onClick="qc_name_fnc()" />
									<input type="hidden" id="txt_hidden_qc_name" />
								</td>
							</tr>
							<tr>
								<td>Grey Issue Challan</td>
								<td>
									<input type="text" name="txt_grey_issue_challan_no" id="txt_grey_issue_challan_no" placeholder="Browse or Write" onDblClick="issue_challan_no();" class="text_boxes" style="width:140px" onBlur="fnc_check_issue(this.value);">
								</td>
							</tr>
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="910" border="0">
						<tr>
							<td width="60%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td width="100" class="must_entry_caption">Batch No.</td>
											<td>
												<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" placeholder="Write" />
												<input type="hidden" name="txt_batch_id" id="txt_batch_id" readonly />
											</td>
											<td class="must_entry_caption" width="80">Recv. Qnty</td>
											<td>
												<input type="hidden" name="txt_pre_cost_fab_conv_cost_dtls_id" id="txt_pre_cost_fab_conv_cost_dtls_id" readonly />
												<input type="hidden" name="txt_fabric_color_id" id="txt_fabric_color_id" readonly />
												<input type="hidden" name="txt_body_part_id" id="txt_body_part_id" readonly />
												<input type="hidden" name="txt_fabric_shade" id="txt_fabric_shade" readonly />

												<input type="hidden" name="txt_fso_dtls_id" id="txt_fso_dtls_id" readonly />
												<input type="hidden" name="txt_fso_company_id" id="txt_fso_company_id" readonly />
												<input type="text" name="txt_production_qty" id="txt_production_qty" class="text_boxes_numeric" placeholder="Single Click to Search" style="width:140px;" onClick="openmypage_po()" readonly />
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Body Part</td>
											<td id="body_td">
												<?
												echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 );
												?>
											</td>
											<td id="grey_used_td">Grey used</td>
											<td>
												<input type="text" name="txt_used_qty" id="txt_used_qty" class="text_boxes_numeric" style="width:140px;" readonly/>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Fabric Description</td>
											<td>
												<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:170px;" readonly disabled/>
												<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" readonly/>
											</td>
											<td colspan="2">AOP Rate &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txt_order_rate" id="txt_order_rate" class="text_boxes_numeric" placeholder="Display" style="width:52px;" readonly/>&nbsp; Fab. Rate:<input type="text" name="txt_fab_rate" id="txt_fab_rate" class="text_boxes_numeric" placeholder="Display" style="width:52px;"     readonly/><input type="hidden" name="hdn_currency_id" id="hdn_currency_id" readonly/></td>
											<td>
											</td>
										</tr>
										<tr>
											<td>GSM</td>
											<td>
												<input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:65px;" maxlength="10"/>
												<span class="must_entry_caption">UOM</span>
												<?
												echo create_drop_down( "cbouom", 73, $unit_of_measurement,'', 1, '-Uom-', 12, "",0,"1,12,23,27" );
												?>
											</td>

											<td colspan="2">Rate: &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" placeholder="Display" style="width:52px;"   readonly/>
											&nbsp; 
											Amount <input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" placeholder="Display" style="width:60px;" readonly/> 
												
												<input type="hidden" name="txt_order_amount" id="txt_order_amount" /></td>
											<td>
											</td>


										</tr>
										<tr>
											<td>Dia/Width</td>
											<td>
												<input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes_numeric" style="width:170px;" maxlength="10" />
											</td>
											<td>Reject Qty</td>
											<td>
												<input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:140px;" readonly/>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:170px;" disabled />
											</td>
											<td>Machine Name</td>
											<td>
												<?
												echo create_drop_down( "cbo_machine_name", 152, "select id,machine_no as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0 order by machine_no","id,machine_name", 1, "-- Select Machine --", 0, "","" );
												?>
											</td>
										</tr>
										<tr>
											<td>Buyer</td>
											<td>
												<input type="text" name="buyer_name" id="buyer_name" class="text_boxes" placeholder="Display" style="width:170px;" disabled="disabled" />
												<input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" disabled="disabled" />
											</td>
											<td>No Of Roll</td>
											<td>
												<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:140px;"/>
											</td>
										</tr>
										<tr>
											<td>Dia/ W. Type</td>
											<td>
												<?
												echo create_drop_down( "cbo_dia_width_type", 180, $fabric_typee,"",1, "-- Select --", 0, "" );
												?>
											</td>
											<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>

										</tr>
										<tr>
											<td>Balance Qty</td>
											<td>
												<input type="text" name="txt_balance_qty" id="txt_balance_qty" class="text_boxes_numeric" style="width:170px" readonly>
											</td>
											<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>


										</tr>
										<tr>
											<td>Fabric Shade</td>
											<td>
												<?
												echo create_drop_down( "cbo_fabric_type", 180, $fabric_shade,"",1, "-- Select --", 0, "" );
												?>
											</td>
											<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>

									</table>
								</fieldset>
							</td>
							<td width="1%" valign="top">&nbsp;</td>
							<td width="38%" valign="top">
								<div id="roll_details_list_view"></div>
								<fieldset style="display:none">
									<legend>Display</legend>
									<table>
										<tr>
											<td width="100">Batch Quantity</td>
											<td>
												<input type="text" name="txt_batch_qnty" id="txt_batch_qnty" class="text_boxes" style="width:100px;" disabled />
											</td>
										</tr>
										<tr>
											<td>Total Received</td>
											<td>
												<input type="text" name="txt_total_received" id="txt_total_received" class="text_boxes" style="width:100px;" disabled />
											</td>
										</tr>
										<tr>
											<td>Yet to Received</td>
											<td>
												<input type="text" name="txt_yet_receive" id="txt_yet_receive" class="text_boxes" style="width:100px;" disabled />
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="4" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_finish_receive_entry", 0,1,"reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container*roll_details_list_view','','','disable_enable_fields(\'cbo_company_id\');set_receive_basis();')",1);
								?>
								<input type="button" id="show_button" class="formbutton" style="width:80px" value="Print 2" onClick="fn_report_generated(2)" />
								<input type="button" id="show_button" class="formbutton" style="width:80px" value="Print 3 " onClick="fn_report_generated(3)" />
								<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
								<input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
								<input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
								<input type="hidden" name="product_id" id="product_id" readonly><!--For Receive Basis Production-->
								<input type="hidden" name="hidden_receive_qnty" id="hidden_receive_qnty" readonly>
								<input type="hidden" name="all_po_id" id="all_po_id" readonly>
								<input type="hidden" name="save_data" id="save_data" readonly>
								<input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
								<input type="hidden" name="barcode_generation" id="barcode_generation" readonly>
								<input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
								<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly />
								<input type="hidden" name="txt_color_id" id="txt_color_id" readonly />
								<input type="hidden" name="hidden_receive_amnt" id="hidden_receive_amnt" readonly>
								<input type="hidden" name="save_rate_string" id="save_rate_string" readonly>
								<input type="hidden" name="hidden_dying_charge" id="hidden_dying_charge" readonly>
								<input type="hidden" name="txt_job_no" id="txt_job_no" readonly>
								<input type="hidden" name="knitting_charge_string" id="knitting_charge_string" class="text_boxes">
								<input type="hidden" name="process_string" id="process_string"/>
								<input type="hidden" name="process_costing_maintain" id="process_costing_maintain" readonly>
								<input type="hidden" name="check_production_qty" id="check_production_qty" readonly>
								<input type="hidden" name="fin_prod_dtls_id" id="fin_prod_dtls_id" readonly>
								<input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
								<input type="hidden" name="textile_sales_maintain" id="textile_sales_maintain" readonly>
								<input type="hidden" name="txt_sales_order_type" id="txt_sales_order_type" readonly>
							</td>
						</tr>
					</table>
					<div style="width:820px;" id="list_container_finishing"></div>
				</fieldset>
			</div>
			<div id="list_fabric_desc_container" style="width:380px; margin-left:5px;float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			<br clear="all" />
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
