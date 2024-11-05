<?
/*-- ------------------------------------------ Comments
Purpose			: 	This form will create Finish Fabric Delivery Roll Wise
Functionality	:
JS Functions	:
Created by		:	Jahid Hasan
Creation date 	: 	12-07-2018
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish Fabric Delivery To Garments","../../", 1, 1, $unicode,'','');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	function openmypage_fso() 
	{
		var title = 'Fabric Sale Order Form';
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_store_name = $('#cbo_store_name').val();

		if( form_validation('cbo_company_id*cbo_store_name','Company*Store Name')==false )
		{
			return;
		}
		var page_link = 'requires/finish_feb_delivery_to_garments_controller.php?cbo_company_id='+cbo_company_id+'&store_id='+cbo_store_name+'&action=fabric_sales_order_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=1,scrolling=0','../');		
		emailwindow.onclose = function () 
		{
			var theform=this.contentDoc.forms[0];
			var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
			var booking_data = hidden_booking_data.split("**");
			var fso_id = booking_data[0];
			var sales_booking_no = booking_data[1];
			var companyId = booking_data[2]; 
			var withinGroup = booking_data[3];
			var buyer_id = booking_data[4];			
			var fso_no =  booking_data[5];
			var po_job_no = booking_data[6];
			var po_company_id = booking_data[7];
			var booking_id = booking_data[9];
			var booking_without_order = booking_data[10];

			$("#txt_booking_no").val(sales_booking_no);
			$("#hdn_booking_id").val(booking_id);
			$("#hdn_fso_id").val(fso_id);
			$("#hdn_buyer_id").val(buyer_id);
			$("#txt_fso_no").val(fso_no);
			$("#hdn_within_group").val(withinGroup);			
			$('#txt_po_job').val(po_job_no);
			$('#hdn_booking_without_order').val(booking_without_order);
			
			$('#txt_booking_no').attr('disabled','disabled');
			$('#txt_batch_no').attr('disabled','disabled');
			
			if(withinGroup==1){
				load_drop_down('requires/finish_feb_delivery_to_garments_controller', po_company_id+"_"+withinGroup, 'load_drop_down_company', 'party_td' );				
			}else{
				load_drop_down('requires/finish_feb_delivery_to_garments_controller', buyer_id+"_"+withinGroup, 'load_drop_down_buyer', 'party_td' );
				$('#cbo_party').val(buyer_id);
			}
			var store_update_upto=$('#store_update_upto').val();
			$('#cbo_party').attr('disabled','disabled');
			show_list_view(fso_id+"**"+sales_booking_no+"**"+cbo_store_name+"**"+cbo_company_id+"**"+booking_without_order+"**"+store_update_upto,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_feb_delivery_to_garments_controller','');
		}
	}

	function set_form_data(data)
	{
		var formData = data.split("**");

		if($('#txt_system_id').val() != "")
		{
			var fso_id = $("#hdn_fso_id").val();
			var store_id = $("#cbo_store_name").val();
			var update_mst_id = $("#update_mst_id").val();

			var addi_data = "";
			if(formData[21] == 1)
			{
				addi_data = "**"+formData[22]+"**"+formData[23]+"**"+formData[24]+"**"+formData[25];
			}
			

			var ref_data = formData[21]+"**"+update_mst_id+"**"+fso_id+"**"+formData[10]+"**"+formData[15]+"**"+store_id+"**"+formData[19]+"**"+formData[8]+"**"+formData[0]+addi_data
			var batch_num=return_global_ajax_value( ref_data, 'chk_dtls_id_with_same_criteria', '', 'requires/finish_feb_delivery_to_garments_controller');
			batch_no=trim(batch_num);
			if(batch_no!='')
			{ 
				alert('This fabric already belong\'s to this system id;\n you can update the details part.');
				return;
			}
		}

		$('#cbo_body_part').val(formData[0]);
		var desc = formData[1] + ", " + formData[2] + ", " + formData[3];
		$('#txt_fabric_description').val(desc);		
		$('#txt_fabric_description_id').val(formData[4]);
		$('#txt_gsm').val(formData[2]);
		$('#txt_dia').val(formData[3]);
		$('#txt_color').val(formData[5]).attr('disabled','disabled');
		$('#txt_color_id').val(formData[12]);
		$('#txt_dia_width_type').val(formData[8]);
		$('#txt_batch_no').val(formData[9]);
		$('#hdn_batch_id').val(formData[10]);

		//$('#hidden_receive_id').val(formData[7]);
		//$('#hidden_receive_number').val(formData[15]);
		//$('#hidden_receive_dtls_id').val(formData[16]);
		$('#hidden_product_id').val(formData[15]);
		$('#hdn_knitting_company').val(formData[16]);
		$('#hdn_knitting_source').val(formData[17]);
		$('#hdn_uom').val(formData[18]).attr('disabled','disabled');
		$('#cbo_fabric_shade').val(formData[19]).attr('disabled','disabled');
		$('#hdn_receive_date').val(formData[20]);
		var variable_settings = formData[21];

		if(variable_settings==1)
		{
			$('#cbo_floor').val(formData[22]);
			$('#txt_rack').val(formData[23]);
			$('#txt_shelf').val(formData[24]);
			$('#cbo_room').val(formData[25]);
			disable_enable_fields('cbo_floor*txt_rack*txt_shelf*cbo_room', 1, "", "");
			$('#hidden_fabric_rate').val(formData[26]);
			$('#hidden_fabric_order_rate').val(formData[27]);
			$('#hidden_fabric_aop_rate').val(formData[28]);
		}else{
			$('#hidden_fabric_rate').val(formData[22]);
			$('#hidden_fabric_order_rate').val(formData[23]);
			$('#hidden_fabric_aop_rate').val(formData[24]);
		}

		
		var delivery_transout = formData[14].split("___");
		var delivery_qnty = delivery_transout[0]*1;
		var trans_out_qnty = delivery_transout[1]*1;


		$('#txt_sales_order_no').val($("#txt_fso_no").val());
		$('#txt_fabric_receive').val((formData[13]*1).toFixed(2));
		$('#txt_cumulative_delivery').val((delivery_qnty).toFixed(2));
		$('#txt_fabric_transout').val((trans_out_qnty).toFixed(2));
		$('#txt_yet_delivery').val((formData[13]*1 - (delivery_qnty+trans_out_qnty)).toFixed(2));

		$('#cbo_body_part').attr('disabled','disabled');


		var hdn_booking_without_order = $('#hdn_booking_without_order').val();
		var hdn_within_group = $('#hdn_within_group').val();
		//var hdn_fso_id = $('#hdn_fso_id').val();
		//var txt_po_job = $('#txt_po_job').val();
		var style_wise_popup = $('#style_wise_popup').val();

		if(hdn_booking_without_order ==0 && hdn_within_group!=2 && style_wise_popup==1)
		{
			$('#txt_Delivery_qnty').attr('onDblClick','fnc_style_wise_popup()');
			$('#txt_Delivery_qnty').attr('placeholder','Double click');
			$('#txt_Delivery_qnty').attr('readonly','readonly');
		}
		else
		{
			$('#txt_Delivery_qnty').removeAttr('onDblClick', 'onDblClick');
			$('#txt_Delivery_qnty').attr('placeholder','Delivery Qnty');
			$('#txt_Delivery_qnty').removeAttr('readonly','readonly');
		}

		set_button_status(0, permission, 'fnc_finish_delivery_entry',1);
	}

	function fnc_finish_delivery_entry( operation )
	{
		if( form_validation('cbo_company_id*cbo_location*cbo_store_name*txt_delivery_date*txt_fso_no*cbo_party*txt_booking_no*txt_fabric_description*txt_color*txt_Delivery_qnty','Company*Location*Store Name*Delivery Date*FSO No*Party* Booking No*Fabric Description*Color*Delivery Qnty')==false )
		{
			return;
		}
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][224]);?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][224]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][224]);?>')==false)
			{
				
				return;
			}
		}
		var txt_Delivery_qnty = $('#txt_Delivery_qnty').val()*1;
		var hdn_Delivery_qnty = $('#hdn_Delivery_qnty').val()*1;
		var fabric_transout = $('#txt_fabric_transout').val()*1;
		var txt_cumulative_delivery = $('#txt_cumulative_delivery').val()*1;
		var txt_fabric_receive = $('#txt_fabric_receive').val()*1;
		var hdn_booking_without_order = $('#hdn_booking_without_order').val();

		if((txt_Delivery_qnty + fabric_transout + (txt_cumulative_delivery-hdn_Delivery_qnty)) > txt_fabric_receive)
		{
			alert("Delivery quantity can not be greater than Receive quantity");
			return;
		}

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

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location*txt_system_id*txt_delivery_date*txt_fso_no*hdn_fso_id*hdn_buyer_id*txt_po_job*txt_fabric_description*txt_fabric_description_id*txt_gsm*txt_dia*hdn_batch_id*cbo_body_part*txt_color*txt_color_id*txt_Delivery_qnty*hidden_product_id*txt_dia_width_type*update_mst_id*hdn_knitting_company*hdn_knitting_source*hdn_within_group*cbo_party*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*hdn_uom*cbo_fabric_shade*txt_no_of_roll*hdn_receive_date*hidden_fabric_rate*hidden_fabric_order_rate*update_dtls_id*update_trans_id*hidden_pre_product_id*hdn_Delivery_qnty*hdn_Delivery_amount*hdn_booking_id*txt_booking_no*hdn_booking_without_order*store_update_upto*hidden_fabric_aop_rate*txt_remarks*save_data*txt_vehicle_no*txt_driver_name',"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_feb_delivery_to_garments_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function(operation){
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);
				if(reponse[0]*1==20 || reponse[0]*1==17)
				{
					alert(reponse[1]);
					release_freezing();
					return;
				}
				
				else if (reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2) 
				{
					document.getElementById('update_mst_id').value = reponse[1];
					document.getElementById('txt_system_id').value = reponse[2];
					if (reponse[0]==2 && reponse[3]==1) // is mst delete reset form
					{
						reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container*roll_details_list_view','','','');
					}
					else
					{
						$('#cbo_company_id').attr('disabled','disabled');
						$('#txt_fso_no').attr('disabled','disabled');
						$('#cbo_location').attr('disabled','disabled');
						$('#cbo_store_name').attr('disabled','disabled');

						var hdn_fso_id = $("#hdn_fso_id").val();
						var cbo_company_id = $('#cbo_company_id').val();
						var cbo_store_name = $('#cbo_store_name').val();
						var sales_booking_no = $('#txt_booking_no').val();
						show_list_view(hdn_fso_id+"**"+sales_booking_no+"**"+cbo_store_name+"**"+cbo_company_id+"**"+hdn_booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_feb_delivery_to_garments_controller','');
						// load saved delivery data
						show_list_view(reponse[1],'show_delivery_listview','list_container_finishing','requires/finish_feb_delivery_to_garments_controller','');

						reset_form('finishFabricEntry_1','roll_details_list_view','','','','cbo_company_id*cbo_location*txt_system_id*txt_delivery_date*txt_fso_no*hdn_fso_id*hdn_buyer_id*txt_po_job*cbo_party*hidden_product_id*txt_party*update_mst_id*txt_booking_no*cbo_store_name*hdn_booking_without_order*store_update_upto*style_wise_popup*hdn_within_group*txt_vehicle_no*txt_driver_name');
					}

					set_button_status(0, permission, 'fnc_finish_delivery_entry',1);
				}
				release_freezing();
			}
		}
	}

	function put_data_dtls_part(id,product_id)
	{
		var hdn_fso_id = $("#hdn_fso_id").val();
		var hdn_booking_without_order = $("#hdn_booking_without_order").val();
		var hdn_within_group = $("#hdn_within_group").val();
		var style_wise_popup = $("#style_wise_popup").val();
		$('#txt_sales_order_no').val($("#txt_fso_no").val());
		get_php_form_data(id+"**"+product_id+"**"+hdn_fso_id, "populate_delivery_dtls_data", "requires/finish_feb_delivery_to_garments_controller" );
		$('#txt_batch_no').attr('disabled','disabled');
		$('#txt_color').attr('disabled','disabled');
		$('#hdn_uom').attr('disabled','disabled');
		$('#txt_delivery_date').attr('disabled','disabled');
		$('#cbo_fabric_shade').attr('disabled','disabled');
		$('#cbo_floor').attr('disabled','disabled');
		$('#cbo_room').attr('disabled','disabled');
		$('#txt_shelf').attr('disabled','disabled');
		$('#txt_rack').attr('disabled','disabled');

		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_store_name = $('#cbo_store_name').val();
		var sales_booking_no = $('#txt_booking_no').val();
		show_list_view(hdn_fso_id+"**"+sales_booking_no+"**"+cbo_store_name+"**"+cbo_company_id+"**"+hdn_booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_feb_delivery_to_garments_controller','');

		if(hdn_booking_without_order ==0 && hdn_within_group!=2 && style_wise_popup==1)
		{
			$('#txt_Delivery_qnty').attr('onDblClick','fnc_style_wise_popup()');
			$('#txt_Delivery_qnty').attr('placeholder','Double click');
			$('#txt_Delivery_qnty').attr('readonly','readonly');
		}
		else
		{
			$('#txt_Delivery_qnty').removeAttr('onDblClick', 'onDblClick');
			$('#txt_Delivery_qnty').attr('placeholder','Delivery Qnty');
			$('#txt_Delivery_qnty').removeAttr('readonly','readonly');
		}

		set_button_status(1, permission, 'fnc_finish_delivery_entry',1);
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
			var page_link = 'requires/finish_feb_delivery_to_garments_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=1180px,height=390px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose = function()
			{
				var theform 		= this.contentDoc.forms[0];
				var system_id 		= this.contentDoc.getElementById("hidden_sys_id").value;

				var batch_id 		= this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no 		= this.contentDoc.getElementById("hidden_batch_no").value;
				var sales_id 		= this.contentDoc.getElementById("hidden_sales_id").value;
				var booking_no 		= this.contentDoc.getElementById("hidden_booking_no").value;
				var buyer_id 		= this.contentDoc.getElementById("hidden_buery_id").value;
				var po_company_id 	= this.contentDoc.getElementById("hidden_po_company_id").value;
				
				var fso_no 			= this.contentDoc.getElementById("hidden_fso_no").value;
				var job_no 			= this.contentDoc.getElementById("hidden_po_job_no").value;
				var location 		= this.contentDoc.getElementById("hidden_location").value;
				var sys_number 		= this.contentDoc.getElementById("hidden_sys_number").value;
				var within_group 	= this.contentDoc.getElementById("hidden_within_group").value;
				var store_id 		= this.contentDoc.getElementById("hidden_store_id").value;
				var booking_id 		= this.contentDoc.getElementById("hidden_booking_id").value;
				var hidden_booking_without_order 		= this.contentDoc.getElementById("hidden_booking_without_order").value;

				get_php_form_data(system_id, "populate_data_from_to_garments", "requires/finish_feb_delivery_to_garments_controller" );


				$("#txt_system_id").val(sys_number);
				$("#update_mst_id").val(system_id);
				$("#txt_booking_no").val(booking_no);
				$("#hdn_booking_id").val(booking_id);
				$("#hdn_fso_id").val(sales_id);
				$("#hdn_buyer_id").val(buyer_id);
				$("#txt_fso_no").val(fso_no);
				$('#hdn_batch_id').val(batch_id);
				$('#txt_batch_no').val(batch_no);
				$('#txt_po_job').val(job_no);		
				$('#cbo_location').val(location);
				$('#hdn_within_group').val(within_group);
				$('#hdn_booking_without_order').val(hidden_booking_without_order);


				$('#txt_booking_no').attr('disabled','disabled');
				$('#txt_batch_no').attr('readonly','readonly');
				$('#txt_fso_no').attr('readonly','readonly');
				$('#txt_fso_no').attr('disabled','disabled');
				$('#cbo_store_name').attr('disabled','disabled');
				$('#cbo_location').attr('disabled','disabled');

				load_drop_down('requires/finish_feb_delivery_to_garments_controller', po_company_id+"_"+within_group, 'load_drop_down_company', 'party_td' );
				$('#cbo_party').val(po_company_id).attr('disabled','disabled');

				//load_drop_down('requires/finish_feb_delivery_to_garments_controller', po_company_id, 'load_drop_down_store', 'store_td' );
				show_list_view(sales_id+"**"+booking_no+"**"+store_id+"**"+cbo_company_id+"**"+hidden_booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_feb_delivery_to_garments_controller','');

				show_list_view(system_id,'show_delivery_listview','list_container_finishing','requires/finish_feb_delivery_to_garments_controller','');
				set_button_status(0, permission, 'fnc_finish_delivery_entry',1);
			}
		}
	}
	function reset_on_change(id)
	{
		
		if(id =="cbo_store_name")
		{
			var unRefreshId = "cbo_company_id*cbo_location*cbo_store_name*txt_delivery_date*store_update_upto*style_wise_popup";
		}
		else if(id =="cbo_location")
		{
			var unRefreshId = "cbo_company_id*cbo_location*txt_delivery_date*store_update_upto*style_wise_popup";
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_floor','floor_td');
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_room','room_td');
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_rack','rack_td');
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_shelf','shelf_td');
		}
		else if(id =="cbo_company_id")
		{
			var unRefreshId = "cbo_company_id*txt_delivery_date*store_update_upto*style_wise_popup";
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_down_store','store_td');
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_floor','floor_td');
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_room','room_td');
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_rack','rack_td');
			load_drop_down('requires/finish_feb_delivery_to_garments_controller', '0', 'load_drop_shelf','shelf_td');
		}
		reset_form('finishFabricEntry_1', 'list_container_finishing*roll_details_list_view*list_fabric_desc_container', '', '', '', unRefreshId);

	}

	function company_on_change(company)
	{
	    var data='cbo_company_id='+company+'&action=upto_variable_settings';    
	    var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() 
	    {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("store_update_upto").value = this.responseText;
	        }
	    }
	    xmlhttp.open("POST", "requires/finish_feb_delivery_to_garments_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/finish_feb_delivery_to_garments_controller.php?data=" + data+'&action='+action, true );
	}
	function fn_report_generated(type)
	{
		var report_title=$( "div.form_caption" ).html();
		if (type==2) 
		{
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_mst_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print_2','requires/finish_feb_delivery_to_garments_controller');
		}
		else
		{
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_mst_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print_4','requires/finish_feb_delivery_to_garments_controller');
		}
		return;
	}
	function fnc_report_generated3(type)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_mst_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print_3','requires/finish_feb_delivery_to_garments_controller');
		return;
	}
	function fnc_report_generated(type)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_mst_id').val()+'*'+report_title+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print_1','requires/finish_feb_delivery_to_garments_controller');
		return;
	}


	

	function fnc_style_wise_popup()
	{
		//var fabricColorId =$("#txt_fabric_color_id").val();
		//var body_part_id = $("#cbo_body_part").val();
		
		var title = 'Style Info';
		var cbo_company_id = $('#cbo_company_id').val();
		var hdn_booking_without_order = $('#hdn_booking_without_order').val();
		var hdn_within_group = $('#hdn_within_group').val();
		var hdn_fso_id = $('#hdn_fso_id').val();
		var txt_po_job = $('#txt_po_job').val();
		var style_wise_popup = $('#style_wise_popup').val();
		var save_data = $('#save_data').val();
		var cbo_body_part = $('#cbo_body_part').val();
		var txt_color_id = $('#txt_color_id').val();
		var fabric_desc_id = $('#txt_fabric_description_id').val();
		var txt_gsm = $('#txt_gsm').val();
		var txt_dia = $('#txt_dia').val();
		var uom = $('#hdn_uom').val();
		var update_dtls_id = $('#update_dtls_id').val();

		if(hdn_booking_without_order ==0 && hdn_within_group!=2 && style_wise_popup==1)
		{
			var action = "style_wise_popup";
			var popup_width='1070x';

			var page_link = 'requires/finish_feb_delivery_to_garments_controller.php?cbo_company_id='+cbo_company_id+'&hdn_fso_id='+hdn_fso_id+'&txt_po_job='+txt_po_job+'&cbo_body_part='+cbo_body_part+'&txt_color_id='+txt_color_id+'&fabric_desc_id='+fabric_desc_id+'&txt_gsm='+txt_gsm+'&txt_dia='+txt_dia+'&uom='+uom+'&update_dtls_id='+update_dtls_id+'&save_data='+save_data+'&action='+action;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				
				var theform=this.contentDoc.forms[0];
				var save_string=this.contentDoc.getElementById("save_string").value;
				var tot_finish_qnty=this.contentDoc.getElementById("tot_finish_qnty").value;

				//var all_po_id=this.contentDoc.getElementById("all_po_id").value;

				var distribution_method=this.contentDoc.getElementById("distribution_method").value;
				var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;

				$('#save_data').val(save_string);
				$('#txt_Delivery_qnty').val(tot_finish_qnty);

				/*	$('#txt_production_qty').val(tot_finish_qnty);
					$('#txt_reject_qty').val(tot_reject_qnty);
					$('#all_po_id').val(all_po_id);
					$('#hidden_order_id').val(all_po_id);
					$('#txt_order_no').val(order_nos);
					$('#buyer_name').val(buyer_name);
					$('#buyer_id').val(buyer_id);
					$('#distribution_method_id').val(distribution_method);

				

				*/
			}
		}
		else
		{
			alert('style wise popup not allowed');
			return;
		}
	}

	function print_button_setting()
	{
		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/finish_feb_delivery_to_garments_controller');
	}

</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:690px; float:left;" align="center">   
				<fieldset style="width:690px;">
					<legend>Finish Fabric Delivery Entry</legend>
					<fieldset>
						<table cellpadding="0" cellspacing="2" width="680" border="0" style="margin-bottom: 20px">
							<tr>
								<td colspan="3" align="right"><strong>System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
								</td>
							</tr>
							<tr>
								<td colspan="6"></td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company Name</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0,"reset_on_change(this.id);load_drop_down('requires/finish_feb_delivery_to_garments_controller', this.value, 'load_drop_down_location','location_td');print_button_setting();company_on_change(this.value);get_php_form_data(this.value,'load_variable_qnty_popup','requires/finish_feb_delivery_to_garments_controller' );" );
									?>
								</td>
								<td class="must_entry_caption">Location</td>
								<td id="location_td">
									<? echo create_drop_down("cbo_location", 152, $blank_array,"", 1,"-- Select Location --", 0,""); ?>
								</td>
								
								<td class="must_entry_caption"> Store Name </td>
								<td id="store_td">
									<?
									echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
									?>
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">FSO No</td>
								<td>
									<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:130px;" placeholder="Browse" onDblClick="openmypage_fso();" readonly />
									<input type="hidden" name="hdn_fso_id" id="hdn_fso_id" class="text_boxes" value="" />
									<input type="hidden" name="hdn_within_group" id="hdn_within_group" class="text_boxes" value="" />
									<input type="hidden" name="hdn_booking_without_order" id="hdn_booking_without_order" class="text_boxes" readonly />							
									<input type="hidden" name="hdn_buyer_id" id="hdn_buyer_id" class="text_boxes" value="" />
									<input type="hidden" name="txt_po_job" id="txt_po_job" class="text_boxes" readonly />
									
								</td>
								<td>Party</td>
								<td id="party_td">
									<? echo create_drop_down("cbo_party", 152, $blank_array,"", 1,"-- Select Party --", 0,""); ?>
								</td>
								<td class="must_entry_caption"> Booking No </td>
								<td>
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:140px" placeholder="Booking No" disabled="disabled" />
									<input type="hidden" name="hdn_booking_id" id="hdn_booking_id"  />
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Delivery Date</td>
								<td>
									<input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:130px;" value="<? echo date("d-m-Y"); ?>" readonly>
									<input type="hidden" name="hdn_receive_date" id="hdn_receive_date" readonly>
								</td>

								<td>Vehicle No.</td>
								<td>
									<input type="text" name="txt_vehicle_no" id="txt_vehicle_no" class="text_boxes" style="width:140px;" value="">
								</td>
								<td>Driver Name</td>
								<td>
									<input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:140px;" value="">
								</td>
							</tr>
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="680" border="0">
						<tr>
							<td width="60%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td class="must_entry_caption">Fabric Description</td>
											<td id="fabric_desc">
												<input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:300px;" placeholder="Fabric Description" readonly />
												<input type="hidden" name="txt_fabric_description_id" id="txt_fabric_description_id" readonly />
												<input type="hidden" name="txt_gsm" id="txt_gsm" readonly />
												<input type="hidden" name="txt_dia" id="txt_dia" readonly />
												<input type="hidden" name="txt_dia_width_type" id="txt_dia_width_type" readonly />
												<input type="hidden" name="hdn_knitting_company" id="hdn_knitting_company" readonly />
												<input type="hidden" name="hdn_knitting_source" id="hdn_knitting_source" readonly/>
												<input type="hidden" name="hidden_fabric_rate" id="hidden_fabric_rate" readonly/>
												

											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Batch No</td>
											<td>
												<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" placeholder="Batch No" readonly />
												<input type="hidden" name="hdn_batch_id" id="hdn_batch_id" class="text_boxes" value="" />
											</td>
										</tr>
										<tr>
											<td>Body Part</td>
											<td>
												<? echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:80px;" placeholder="Color" readonly="readonly"/>
												<input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes" readonly />

												<span class="must_entry_caption">UOM</span>
												<? echo create_drop_down( "hdn_uom", 60, $unit_of_measurement,'', 1, '-Uom-', 12, "",0,"1,12,23,27" ); ?>
											</td>
										</tr>
										<tr>
											<td>Delivery Qnty</td>
											<td>
												<input type="text" name="txt_Delivery_qnty" id="txt_Delivery_qnty" class="text_boxes_numeric" style="width:170px;" placeholder="Delivery Qnty"/>
												<input type="hidden" name="hdn_Delivery_qnty" id="hdn_Delivery_qnty" />
												<input type="hidden" name="hdn_Delivery_amount" id="hdn_Delivery_amount" />
											</td>
										</tr>
										<tr>
											<td>Rate</td>
											<td>
												<input type="text" name="hidden_fabric_order_rate" id="hidden_fabric_order_rate" class="text_boxes text_boxes_numeric" placeholder="Write" style="width:170px;" readonly="readonly" disabled />
												
												<input type="hidden" name="hidden_fabric_aop_rate" id="hidden_fabric_aop_rate"  value=""  />
											</td>
										</tr>
										<tr>
											<td>Fabric Shade</td>
											<td>
												<?
												echo create_drop_down( "cbo_fabric_shade", 180, $fabric_shade,"",1, "-- Select --", 0, "" );
												?>
											</td>
										</tr>
										<tr>
											<td>No Of Roll</td>
											<td>
												<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:170px;"/>
											</td>
										</tr>
										<tr>
											<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 182,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 182,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 182,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 182,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Remarks</td>
											<td >
												<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:300px;" placeholder="Write"/>
											</td>
										</tr>
									</table>
								</fieldset>
							</td>

							<td width="1%" valign="top">&nbsp;</td>
							<td width="38%" valign="top">
								<div id="roll_details_list_view"></div>
								<fieldset>
									<legend>Display</legend>
									<table>
										<tr>
											<td>FSO No</td>
											<td>
												<input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td>Fabric Received</td>
											<td>
												<input type="text" name="txt_fabric_receive" id="txt_fabric_receive" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td>Transfer Out</td>
											<td>
												<input type="text" name="txt_fabric_transout" id="txt_fabric_transout" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td width="120">Cumulative Delivery</td>
											<td>
												<input type="text" name="txt_cumulative_delivery" id="txt_cumulative_delivery" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly"/>
											</td>
										</tr>
										<tr>
											<td>Yet to Delivery</td>
											<td>
												<input type="text" name="txt_yet_delivery" id="txt_yet_delivery" class="text_boxes" style="width:100px;" readonly="" placeholder="Display" readonly="readonly" />
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="4" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_finish_delivery_entry", 0,0,"reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container*roll_details_list_view','','','')",1);
								?>
								<br/>
                                 <span style="width:400px; text-align: center;" id="button_data_panel"></span>                           								
								<input type="hidden" id="update_mst_id" name="update_mst_id" value="" />
								<input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" />
								<input type="hidden" id="hidden_pre_product_id" name="hidden_pre_product_id" value="" />
								<input name="update_dtls_id" id="update_dtls_id" readonly type="hidden">
								<input name="update_trans_id" id="update_trans_id" readonly type="hidden">
								<input type="hidden" name="store_update_upto" id="store_update_upto">
								<input type="hidden" name="style_wise_popup" id="style_wise_popup">
								<input type="hidden" name="save_data" id="save_data">
							</td>
						</tr>
					</table>
					<div style="width:620px;" id="list_container_finishing"></div>
				</fieldset>
			</div>
			<div id="list_fabric_desc_container" style="width:380px; margin-left:5px;float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			<br clear="all" />
		</form>
	</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
