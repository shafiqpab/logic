<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Production Entry

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-05-2013
Updated by 		: 	Fuad Shahriar
Update date		: 	25-05-2013
QC Performed BY	:
QC Date			:
Comments		:11-08-2015 code no chang only test
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$batch_status_array=array(1=>"Complete",0=>"Incomplete");
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finish Production Entry Info","../", 1, 1, "",'1','');

?>

<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_color").autocomplete({
			source: str_color
		});

	});

	function set_production_basis()
	{
		var production_basis = $('#cbo_production_basis').val();
		$('#list_fabric_desc_container').html('');
		//document.getElementById('body_part_td').innerHTML='<? //echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 ); ?>';
		load_drop_down('requires/finish_fabric_receive_controller', '', 'load_drop_down_all_body_part','body_part_td');
		
		$('#buyer_id').val('');
		$('#buyer_name').val('');
		$('#txt_production_qty').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#hidden_wgt_qty').val('');
		$('#batch_booking_without_order').val('');
		//$('#cbo_batch_status').val(0);
		$('#txt_production_qty').attr('readonly','readonly');
		$('#txt_production_qty').attr('onClick','openmypage_po();');
		$('#txt_production_qty').attr('placeholder','Single Click to Search');
		$('#txt_batch_qnty').val('');
		$('#txt_total_received').val('');
		$('#txt_yet_receive').val('');
		$('#txt_rate').val('');
		$('#txt_amount').val('');
		$('#hdn_currency_id').val('');

		if(production_basis == 4)
		{
			$('#cbo_body_part').val(0);
			//$('#cbo_body_part').removeAttr('disabled','disabled');
			$('#txt_fabric_desc').val('');
			$('#txt_fabric_desc').removeAttr('disabled','disabled');
			$('#txt_color').val('');
			$('#txt_color').removeAttr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').removeAttr("onDblClick");
			$('#txt_batch_no').attr("placeholder","Write");
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').attr("onDblClick","openmypage_fabricDescription();");
			$('#txt_fabric_desc').attr("placeholder","Double Click For Search");
			$('#cbo_dia_width_type').removeAttr('disabled','disabled');
			$('#txt_booking_no').removeAttr('disabled','disabled');
		}
		else if(production_basis == 5)
		{


			$('#cbo_body_part').val(0);
			//$('#cbo_body_part').attr('disabled','disabled');
			$('#txt_fabric_desc').val('');
			$('#txt_fabric_desc').attr('disabled','disabled');
			$('#txt_color').val('');
			$('#txt_color').attr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').attr("onDblClick","openmypage_batchnum();");
			$('#txt_batch_no').attr("placeholder","Write / Browse");
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').removeAttr("onDblClick");
			$('#txt_fabric_desc').removeAttr("placeholder");
			$('#txt_booking_no').attr('disabled','disabled');
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
			var page_link = 'requires/finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var finish_fabric_id=this.contentDoc.getElementById("hidden_sys_id").value;

				reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container','','','','roll_maintained*fabric_store_auto_update*process_costing_maintain');
				get_php_form_data(finish_fabric_id, "populate_data_from_finish_fabric", "requires/finish_fabric_receive_controller" );
				if($("#cbo_production_basis").val()==5){ $('#cbo_dia_width_type').attr('disabled',true);}
				else {$('#cbo_dia_width_type').removeAttr('disabled','disabled');}
				show_list_view(finish_fabric_id,'show_finish_fabric_listview','list_container_finishing','requires/finish_fabric_receive_controller','');
				set_button_status(0, permission, 'fnc_finish_production',1,1);
			}
		}
	}

	function openmypage_batchnum()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_dyeing_company = $('#cbo_dyeing_company').val();

		if (form_validation('cbo_company_id*cbo_dyeing_company','Company*Dyeing Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&cbo_dyeing_company='+cbo_dyeing_company+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=420px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				var job_no=this.contentDoc.getElementById("job_no").value;
				var booking_no=this.contentDoc.getElementById("booking_no").value;
				var is_sales=this.contentDoc.getElementById("is_sales").value;
				$("#is_sales").val(is_sales);
				if(batch_id!="")
				{
					freeze_window(5);
					set_production_basis();

					//Batch Based Means Inhouse. Inhouse Id=1
					$('#cbo_dyeing_source').val(1);
					//load_drop_down( 'requires/finish_fabric_receive_controller','1_'+document.getElementById('cbo_company_id').value, 'load_drop_down_dyeing_com','dyeingcom_td');
					load_drop_down( 'requires/finish_fabric_receive_controller','1_'+document.getElementById('cbo_company_id').value, 'load_drop_down_machine_name','machine_name_td');
					var dataVal = batch_id+'_'+is_sales+'_'+cbo_company_id;

					
					$('#txt_batch_id').val(batch_id);
					$('#txt_batch_no').val(batch_no);
					//batch id + determination + body part + width type
					//get_php_form_data(batch_id, "populate_data_from_batch", "requires/finish_fabric_receive_controller" );
					//show_list_view(job_no+"**"+booking_no,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_receive_controller','');
					show_list_view(dataVal,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_receive_controller','');
					release_freezing();
				}
			}
		}
	}

	function openmypage_fabricDescription()
	{
		var title = 'Fabric Description Info';
		var page_link = 'requires/finish_fabric_receive_controller.php?action=fabricDescription_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
			var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
			//var theeDiaWith=this.contentDoc.getElementById("hidden_dia_width").value; //Access form field with id="emailfield"
			var fabric_desc_id=this.contentDoc.getElementById("fabric_desc_id").value; //Access form field with id="emailfield"

			$('#txt_fabric_desc').val(theename);
			$('#fabric_desc_id').val(fabric_desc_id);
			$('#txt_gsm').val(theegsm);
			//$('#txt_dia_width').val(theeDiaWith);
		}
	}

	function openmypage_po()
	{
		var production_basis=$('#cbo_production_basis').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var txt_batch_no = $('#txt_batch_no').val();
		var txt_batch_id = $('#txt_batch_id').val();
		var dtls_id = $('#update_dtls_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var txt_production_qty = $('#txt_production_qty').val();
		var txt_reject_qty = $('#txt_reject_qty').val();
		var distribution_method = $('#distribution_method_id').val();
		var update_id = $('#update_id').val();
		var is_sales = $('#is_sales').val();
		var cbo_body_part = $('#cbo_body_part').val();
		var fabric_desc_id = $('#fabric_desc_id').val();
		//var txt_original_dia_width = $('#txt_original_dia_width').val();
		var txt_original_gsm = $('#txt_original_gsm').val();
		var txt_grey_used = $('#txt_grey_used').val();
		var txt_original_dia_width=encodeURIComponent($('#txt_original_dia_width').val());
		var txt_booking_no = $('#txt_booking_no').val();

		var process_costing_maintain = $('#process_costing_maintain').val();
		var fabric_store_auto_update = $('#fabric_store_auto_update').val();


		if(production_basis==4 && cbo_company_id==0)
		{
			alert("Please Select Company.");
			$('#cbo_company_id').focus();
			return false;
		}
		else if(production_basis==5 && txt_batch_no=="")
		{
			alert("Please Select Batch No.");
			$('#txt_batch_no').focus();
			return false;
		}

		if(cbo_body_part == 0 && fabric_desc_id == "")
		{
			alert("Please Select Fabric Body Part and Description");
			$('#cbo_body_part').focus();
			$('#fabric_desc_id').focus();
			return false;
		}

		if(txt_booking_no=="" && production_basis ==4){
			alert("Please select fabric booking for independant basis");
			return false;
			$('#txt_booking_no').focus();
		}

		var title = 'PO Info'; var rate = "";
		var page_link = 'requires/finish_fabric_receive_controller.php?production_basis='+production_basis+'&cbo_company_id='+cbo_company_id+'&txt_batch_id='+txt_batch_id+'&dtls_id='+dtls_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_production_qty='+txt_production_qty+'&txt_reject_qty='+txt_reject_qty+'&update_id='+update_id+'&prev_distribution_method='+distribution_method+'&cbo_body_part='+cbo_body_part+'&fabric_desc_id='+fabric_desc_id+'&txt_original_dia_width='+txt_original_dia_width+'&txt_original_gsm='+txt_original_gsm+'&is_sales='+is_sales+'&txt_grey_used='+txt_grey_used+'&booking_no='+txt_booking_no+'&process_costing_maintain='+process_costing_maintain+'&fabric_store_auto_update='+fabric_store_auto_update+'&action=po_popup';
		// alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_finish_qnty=this.contentDoc.getElementById("tot_finish_qnty").value; //Access form field with id="emailfield"
			var tot_reject_qnty=this.contentDoc.getElementById("tot_reject_qnty").value; //Access form field with id="emailfield"
			var tot_grey_qnty=this.contentDoc.getElementById("tot_grey_qnty").value;
			var tot_wgtlost_qnty=this.contentDoc.getElementById("tot_wgtlost_qnty").value;
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("buyer_name").value; //Access form field with id="emailfield"
			var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			// alert(tot_finish_qnty);
			$('#save_data').val(save_string);
			$('#txt_production_qty').val(roundN(tot_finish_qnty,2));
			$('#txt_reject_qty').val(roundN(tot_reject_qnty,2));
			//$('#txt_grey_used').val(roundN(tot_grey_qnty,2));
			$('#hidden_wgt_qty').val(tot_wgtlost_qnty);
			$('#all_po_id').val(all_po_id);
			$('#buyer_name').val(buyer_name);
			$('#buyer_id').val(buyer_id);
			$('#distribution_method_id').val(distribution_method);

			rate = $('#txt_rate').val()*1;
			$('#txt_amount').val(roundN(tot_finish_qnty*rate,2));


			if(production_basis ==5)
			{
				if($('#process_costing_maintain').val()==1 && $('#fabric_store_auto_update').val()==1 && $('#batch_booking_without_order').val() !=1)
				{
					$('#txt_grey_used').attr('readonly','readonly');
					$('#txt_grey_used').attr('placeholder', 'Browse');
					$('#txt_grey_used').attr('onclick', 'proces_costing_popup()');
				}
				else
				{
					$('#txt_grey_used').removeAttr('readonly','readonly');
					$('#txt_grey_used').removeAttr('placeholder', 'Browse');
					$('#txt_grey_used').removeAttr('onclick','proces_costing_popup()');
					$('#txt_grey_used').val(roundN(tot_grey_qnty,2));
				}
			}
			else
			{
				$('#txt_grey_used').removeAttr('placeholder');
				$('#txt_grey_used').removeAttr('onclick');
				$('#txt_grey_used').val(roundN(tot_grey_qnty,2));
			}

			if(production_basis==4)
			{
				get_php_form_data(all_po_id, 'load_color', 'requires/finish_fabric_receive_controller');
			}
		}
	}

	function roundN(num,n){
	  return parseFloat(Math.round(num * Math.pow(10, n)) /Math.pow(10,n)).toFixed(n);
	}

	function openmypage_serviceBooking(type) //Service Booking Knitting
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_production_basis').val();
		var cbo_knitting_source = $('#cbo_dyeing_source').val();
		var cbo_knitting_company = $('#cbo_dyeing_company').val();

		if (form_validation('cbo_company_id*cbo_dyeing_source','Company*Dyeing Source')==false)
		{
			return;
		}

		var title = 'Booking Selection Form';
		var page_link = 'requires/finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&cbo_knitting_source='+cbo_knitting_source+'&cbo_knitting_company='+cbo_knitting_company+'&recieve_basis='+recieve_basis+'&action=serviceBooking_popup';
		var popup_width="1070px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("service_hidden_booking_id").value;
			var theename=this.contentDoc.getElementById("service_hidden_booking_no").value;
			var service_booking_without_order=this.contentDoc.getElementById("service_booking_without_order").value;
			var hidden_knitting_company=this.contentDoc.getElementById("hidden_knitting_company").value;
			var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
			var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;

			if(theemail!="")
			{
				freeze_window(5);

				if(recieve_basis==5)
				{
					
					$('#txt_batch_id').val(batch_id);
					$('#txt_batch_no').val(batch_no);

					//get_php_form_data(batch_id, "populate_data_from_batch", "requires/finish_fabric_receive_controller" );
					show_list_view(batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_receive_controller','');
				}
				
				release_freezing();
			}
		}
	}

	function fnc_finish_production( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title, "finish_fab_production_print", "requires/finish_fabric_receive_controller")
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}

			if( form_validation('cbo_company_id*cbo_location*cbo_dyeing_source*cbo_dyeing_company*txt_production_date*txt_batch_no*cbo_body_part*txt_fabric_desc*txt_production_qty*txt_color*cbouom','Company*Location*Dyeing Source*Dyeing Company*Production Date*Body Part*Fabric Description*Production Qnty*Color*UOM')==false )
			{
				return;
			}

			var cbo_dyeing_source = $('#cbo_dyeing_source').val();
			if(cbo_dyeing_source==1)
			{
				if( form_validation('cbo_location_dyeing','Dyeing Location')==false )
				{
					return;
				}
			}

			var store_auto_update = $('#fabric_store_auto_update').val();
			var is_sales = $('#is_sales').val();
			if(store_auto_update==1)
			{
				if( form_validation('cbo_store_name','Store Name')==false )
				{
					return;
				}

				if(is_sales == 1)
				{
					if( form_validation('txt_rate*txt_amount','Rate*Amount')==false )
					{
						return;
					}
					if(($("#txt_rate").val()*1 < 0) || ($("#txt_rate").val()*1 == 0))
					{
						alert("Rate can not be zero.");
						$("#txt_rate").focus();
						return;
					}
				}
				else
				{
					if($("#process_costing_maintain").val()==1)
					{
						if($("#cbo_production_basis").val() ==5)
						{
							if( form_validation('txt_grey_used','Grey used')==false )
							{
								alert("Please Select Grey Used Qty");
								return;
							}
							if($("#batch_booking_without_order").val()!=1)
							{
								if($("#txt_production_qty").val()!=$("#check_production_qty").val())
								{
									alert("Please Select Grey Used Qty again");
									return;
								}
							}
						}
					}
				}
			}

			var validation_variable = 1;
			var cbouom = $('#cbouom').val();
			if(validation_variable==1)
			{
				var if_over_production_unlimited = return_global_ajax_value($('#cbo_company_id').val(),'chk_if_over_production_unlimited','','requires/finish_fabric_receive_controller');
				if(if_over_production_unlimited == 0)
				{
					if(($("#txt_production_qty").val()*1 > $("#txt_yet_receive").val()*1+$("#hidden_receive_qnty").val()*1) && $("#cbo_production_basis").val()==5)
					{
						/*if(!confirm("QC Pass Quantity Exceeds Batch Quantity.Do you want to continue?")){
							return;
						}*/
						alert("Production Quantity Exceeds Batch Quantity.");
						return;
					}
				}
			}

			if($("#cbo_production_basis").val()==5 && ($("#txt_grey_used").val()*1!=$("#txt_batch_qnty").val()))
			{
				if( form_validation('txt_remarks','Remarks')==false )
				{
					return;
				}
			}
			if(operation==1)
			{
				var update_id =  $("#update_id").val();
				var update_dtls_id =  $("#update_dtls_id").val();
				var batch_id =  $("#txt_batch_id").val();
				var fabric_store_auto_update =  $("#fabric_store_auto_update").val();
				var recv_data = return_global_ajax_value(update_id+'**'+update_dtls_id+'**'+fabric_store_auto_update+'**'+batch_id,'check_fin_fab_dlv_action','','requires/finish_fabric_receive_controller');

				var receiveDataArr = recv_data.split("**");

				if(receiveDataArr[0]==1)
				{
					if(fabric_store_auto_update==1)
					{
						alert("Issue Found\nIssue No is ' "+ receiveDataArr[1] + " '");
						return;
					}
					else
					{
						if(receiveDataArr[2]==1) 
						{
							alert("Delivery Found\nDelivery No is ' "+ receiveDataArr[1] + " '"+"\nDelivery Quantity = "+ receiveDataArr[3] );
								return;
							/*var production_qty =  $("#txt_production_qty").val()*1; 
							if (receiveDataArr[3]>production_qty) 
							{
								alert("You can not decrease Quantity from Current Delv Quantity.\nDelivery Found\nDelivery No is ' "+ receiveDataArr[1] + " '"+"\nDelivery Quantity = "+ receiveDataArr[3] );
								return;
							}*/
						}
						else
						{
							alert("Receive Found\nReceive No is ' "+ receiveDataArr[1] + " '");
							return;
						}
					}
					//alert(txtMsg+ " Found\n" +txtMsg + " No is ' "+ receiveDataArr[1]+"/"+receiveDataArr[2] + " '");
					//return;
				}
			}

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_production_basis*cbo_company_id*txt_system_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*cbo_location_dyeing*cbo_store_name*txt_batch_no*txt_batch_id*cbo_body_part*txt_fabric_desc*fabric_desc_id*txt_color*txt_gsm*txt_dia_width*txt_production_qty*txt_reject_qty*txt_no_of_roll*buyer_id*cbo_machine_name*txt_rack*txt_shelf*update_id*update_dtls_id*save_data*all_po_id*update_trans_id*previous_prod_id*hidden_receive_qnty*roll_maintained*fabric_store_auto_update*batch_booking_without_order*cbo_batch_status*cbo_shift_name*cbo_dia_width_type*txt_process_id*txt_remarks*cbo_fabric_shade*txt_grey_used*hidden_wgt_qty*is_sales*cbouom*cbo_room*cbo_floor*txt_original_gsm*txt_original_dia_width*txt_service_booking*service_booking_id*txt_qc_qty*txt_rate*txt_amount*hdn_currency_id*txt_booking_no*txt_booking_id*txt_hidden_rate*txt_hidden_amount*hidden_dying_charge*txt_grey_used*knitting_charge_string*process_string*process_costing_maintain',"../");

			freeze_window(operation);
			http.open("POST","requires/finish_fabric_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_finish_production_reponse;
		}
	}

	function fnc_finish_production_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			if(reponse[0]==20)
			{
				alert("Multiple Order Not Allowed.");
				release_freezing();
				return;
			}
			else if(reponse[0]==30)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_msg(reponse[0]);

			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				$('#cbo_production_basis').attr('disabled','disabled');
				show_list_view(reponse[1],'show_finish_fabric_listview','list_container_finishing','requires/finish_fabric_receive_controller','');

				var cbo_production_basis=$('#cbo_production_basis').val();
				if(cbo_production_basis==4)
				{
					reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_production_basis*cbo_company_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*cbo_location_dyeing*cbo_store_name*roll_maintained*fabric_store_auto_update*txt_booking_no*txt_booking_id');
				}
				else
				{
					reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_production_basis*cbo_company_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*cbo_location_dyeing*cbo_store_name*roll_maintained*fabric_store_auto_update*txt_batch_no*txt_process_name*txt_process_id*txt_batch_id*batch_booking_without_order*txt_color*is_sales*txt_service_booking*service_booking_id*txt_booking_no*txt_booking_id*process_costing_maintain');
					var batch_id=$('#txt_batch_id').val();
					get_php_form_data(batch_id, "batch_data_display", "requires/finish_fabric_receive_controller" );
					//document.getElementById('body_part_td').innerHTML='<? //echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 ); ?>';
				}

				set_button_status(0, permission, 'fnc_finish_production',1,1);
			}
			release_freezing();
		}
	}

	function set_form_data(data) //
	{
		if($('#update_dtls_id').val() != "")
		{
			alert("Fabric update is not allowed");
			return;
		}

		var data=data.split("**");
		$('#txt_fabric_desc').val(data[0]);
		$('#fabric_desc_id').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_dia_width').val(data[3]);
		$('#txt_original_gsm').val(data[2]);
		$('#txt_original_dia_width').val(data[3]);
		$('#cbo_dia_width_type').val(data[5]);
		//$('#txt_grey_used').val(data[6]);
		$('#cbouom').val(data[7]);
		var cbo_production_basis = $('#cbo_production_basis').val();

		rate = number_format(data[10],2,".","");

		//data[9]==1 is sales order flag chk
		if(data[9]==1)
		{
			if(rate == "0.00")
			{
				$('#txt_rate').attr('onclick',"openmypage_rate()");
				$('#txt_rate').attr('placeholder', 'Browse');
			}
			else
			{
				$("#txt_rate").removeAttr("onclick");
				$('#txt_rate').val(rate);
				$('#txt_rate').removeAttr('placeholder', 'Browse');
			}
		}
		$('#hdn_currency_id').val(data[11]);

		$('#cbo_dia_width_type').attr("disabled",true);
		var txt_batch_id = $('#txt_batch_id').val();
		get_php_form_data(txt_batch_id+"**"+data[9], "check_batch_no_in_delivery", "requires/finish_fabric_receive_controller" );
		if(data[7]!=""){
			if(data[9]==1){ // if sales order
				$('#cbouom').attr("disabled",false);
			}
		}
		var prod_id=data[4];

		load_drop_down('requires/finish_fabric_receive_controller', prod_id+"**"+txt_batch_id, 'load_drop_down_body_part','body_part_td');
		var body_part_length=$("#cbo_body_part option").length;
		if(body_part_length==2)
		{
			$('#cbo_body_part').val($('#cbo_body_part option:last').val());
		}
		$('#cbo_body_part').val(data[8]);
		
		if(cbo_production_basis == 5)
		{
			$('#cbo_body_part').attr("disabled",true);
			$('#txt_gsm').attr("disabled",true);
			$('#txt_dia_width').attr("disabled",true);
		}

		//batch id + determination + body part + width type + gsm + dia_width
		//data[1]+ '_'+ $('#cbo_body_part').val() + '_' + data[5];

		get_php_form_data(txt_batch_id+'_'+data[1]+ '_'+ $('#cbo_body_part').val() + '_' + data[5] + '_' + data[2] + '_' + data[3], "populate_data_from_batch", "requires/finish_fabric_receive_controller" );

		if(cbo_production_basis ==5)  
		{
			if($('#process_costing_maintain').val()==1 && $('#fabric_store_auto_update').val()==1 && $('#batch_booking_without_order').val() !=1)
			{
				$('#txt_grey_used').attr('readonly','readonly');
				$('#txt_grey_used').attr('placeholder', 'Browse');
				$('#txt_grey_used').attr('onclick', 'proces_costing_popup()');
			}
			else
			{
				$('#txt_grey_used').removeAttr('readonly','readonly');
				$('#txt_grey_used').removeAttr('placeholder', 'Browse');
				$('#txt_grey_used').removeAttr('onclick','proces_costing_popup()');
				$('#txt_grey_used').val(data[6]);
			}
		}
		else
		{
			$('#txt_grey_used').removeAttr('placeholder');
			$('#txt_grey_used').removeAttr('onclick');
			$('#txt_grey_used').val(data[6]);
		}
	}

	function openmypage_rate()
	{
		if( form_validation('txt_production_qty','Production Qnty')==false )
		{
			return;
		}

		var cbo_company_id = $('#cbo_company_id').val();
		var fabric_color = $('#txt_color').val();
		var order_id = $('#all_po_id').val();
		var fabric_desc_id = $('#fabric_desc_id').val();
		var cbo_body_part = $('#cbo_body_part').val();

		var title = 'Rate Details Info';
		var page_link = 'requires/finish_fabric_receive_controller.php?cbo_company_id=' + cbo_company_id + '&fabric_color=' + fabric_color + '&fabric_desc_id='+fabric_desc_id + '&order_id='+ order_id + '&cbo_body_part=' + cbo_body_part + '&action=rate_info_popup';

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title,'width=480px,height=390px,center=1,resize=0,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_rate=this.contentDoc.getElementById("hidden_rate").value;
			$('#txt_rate').val(hidden_rate);

			var tot_finish_qnty = $('#txt_production_qty').val();
			var amount=tot_finish_qnty*$('#txt_rate').val();
			$('#txt_amount').val(number_format(amount,2,'.' , ""));
		}
	}

	function put_data_dtls_part(id,type,page_path)
	{
		get_php_form_data(id+"**"+$('#roll_maintained').val()+"**"+$('#cbo_company_id').val()+"**"+$('#process_costing_maintain').val(), type, page_path );
	}

	function set_auto_complete()
	{
		$("#txt_color").autocomplete({
			source: str_color
		});
	}

	function check_batch(data,extention_no)
	{

		if(data=="") return;

		var production_basis=$('#cbo_production_basis').val();
		var cbo_company_id=$('#cbo_company_id').val();
		if(production_basis==5)
		{
			if (form_validation('cbo_company_id','Company')==false)
			{
				$('#txt_batch_no').val('');
				$('#txt_batch_id').val('');
				return;
			}

			var batch_id=return_global_ajax_value( data+"**"+cbo_company_id+"**"+extention_no, 'check_batch_no', '', 'requires/finish_fabric_receive_controller');
			if(batch_id==0)
			{
				alert("Batch No Found");
				reset_form('','list_fabric_desc_container','txt_batch_no*txt_batch_id*txt_batch_qty*batch_booking_without_order*txt_fabric_desc*fabric_desc_id*txt_color*txt_production_qty*buyer_name*buyer_id*txt_dia_width*txt_gsm*all_po_id*save_data*distribution_method_id*txt_batch_qnty*txt_total_received*txt_yet_receive','','','');
				return;
			}
			else
			{
				freeze_window(5);

				$('#txt_batch_id').val(batch_id);

				//get_php_form_data(batch_id, "populate_data_from_batch", "requires/finish_fabric_receive_controller" );
				show_list_view(batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/finish_fabric_receive_controller','');
				release_freezing();
			}
		}
	}

	function check_batch_scan(data)
	{
		var batch_no=return_global_ajax_value( data, 'check_batch_no_scan', '', 'requires/finish_fabric_receive_controller');
		$('#txt_batch_no').val(trim(batch_no));
		$('#cbo_body_part').focus();

	}

	$('#txt_batch_no').live('keydown', function(e) {
		if (e.keyCode === 13) {
			e.preventDefault();
			check_batch_scan(this.value);
		}
	});

	function load_location()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_dyeing_source = $('#cbo_dyeing_source').val();
		var cbo_dyeing_company = $('#cbo_dyeing_company').val();
		if(cbo_dyeing_source==1)
		{
			load_drop_down( 'requires/finish_fabric_receive_controller',cbo_dyeing_company, 'load_drop_down_location', 'location_td_dyeing' );
		}
		else
		{

			load_drop_down( 'requires/finish_fabric_receive_controller',cbo_company_id, 'load_drop_down_location', 'location_td_dyeing' );
		}
	}

	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';
		var page_link = 'requires/finish_fabric_receive_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
		}
	}
	function check_store()
	{
		var store_auto_update = $('#fabric_store_auto_update').val();

		if(store_auto_update==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Store Name";
			$('#search_by_th_up').css('color','blue');
		}
		else
		{
			document.getElementById('search_by_th_up').innerHTML="Store Name";
			$('#search_by_th_up').css('color','black');
		}

	}
	function check_location()
	{
		var cbo_dyeing_source = $('#cbo_dyeing_source').val();
		if(cbo_dyeing_source==1)
		{
			document.getElementById('search_location_th').innerHTML="Dyeing Location";
			$('#search_location_th').css('color','blue');
		}
		else
		{
			document.getElementById('search_location_th').innerHTML="Dyeing Location";
			$('#search_location_th').css('color','black');
		}
	}

	function fn_knit_defect(type)
	{
		var dtls_id=$('#update_dtls_id').val();
		var batch_id=$('#txt_batch_id').val()*1;
		var roll_maintained=$('#roll_maintained').val();
		var company_id=$('#cbo_company_id').val();
		var buyer_id=$('#buyer_id').val();
		var no_of_roll=$('#txt_no_of_roll').val();
		var qc_pass_qty=($('#txt_production_qty').val()*1)+($('#txt_reject_qty').val()*1);
		if(dtls_id=="")
		{
			alert("Select Data First.");return;
		}
		else
		{
			var title = 'Knitting Defect Info';
			if(type==1)
			{
				var page_link='requires/finish_fabric_receive_controller.php?update_dtls_id='+dtls_id+'&roll_maintained='+roll_maintained+'&company_id='+company_id+'&action=knit_defect_popup';
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=500px,center=1,resize=1,scrolling=0','');
			}
			else
			{
				var page_link='requires/finish_fabric_receive_controller.php?update_dtls_id='+dtls_id+'&roll_maintained='+roll_maintained+'&company_id='+company_id+'&buyer_id='+buyer_id+'&action=knit_defect_popup2'+'&no_of_roll='+no_of_roll+'&qc_pass_qty='+qc_pass_qty+'&batch_id='+batch_id;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=500px,center=1,resize=1,scrolling=0','');
			}

			emailwindow.onclose=function()
			{

			}
		}
	}

	function openmypage_booking()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_production_basis').val();
		
		if (form_validation('cbo_company_id*cbo_production_basis','Company*Basis')==false)
		{
			return;
		}
		if(recieve_basis==4)
		{
			var title = 'Booking Selection Form';
			var page_link = 'requires/finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&action=booking_popup';
			var popup_width="1070px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];

				var booking_data=this.contentDoc.getElementById("hidden_booking_data").value;
				if (booking_data!="")
				{
					var exdata=booking_data.split("__");
					$('#txt_booking_no').val(exdata[1]);
					$('#txt_booking_id').val(exdata[0]);
				}
			}
		}
	}

	function proces_costing_popup()
	{
		var cbo_company_id= $('#cbo_company_id').val();
		var txt_job_no= $('#txt_job_no').val();
		var recieve_basis = '9';

		var fabric_description_id=$('#fabric_desc_id').val();
		var txt_receive_qnty=$('#txt_production_qty').val();
		var txt_receive_date=$('#txt_production_date').val();
		var update_dtls_id=$('#update_dtls_id').val();
		var update_id=$('#update_id').val();
		var kitting_charge=$("#hidden_dying_charge").val();
		var kitting_charge_2nd='';//$("#hidden_dying_charge_2nd").val();
		//var cbo_currency=$("#cbo_currency").val();
		//var txt_exchange_rate=$("#txt_exchange_rate").val()*1;
		var txt_sales_booking_no = $('#txt_sales_booking_no').val();
		var hdn_is_sales = $('#is_sales').val();
		var save_data =$('#save_data').val();
		var name_color =$('#txt_color').val();
		var txt_batch_id = $('#txt_batch_id').val();
		var txt_process_id = $('#txt_process_id').val();

		var txt_service_booking = $('#txt_service_booking').val();
		var service_booking_id = $('#service_booking_id').val();
		var service_booking_without_order = $('#service_booking_without_order').val();

		if (form_validation('txt_production_qty*txt_production_date*txt_batch_no','QC Pass Qty*Receive Date*Batch')==false)
		{
			return;
		}

		var title = 'Grey Cost Info';
		var page_link='requires/finish_fabric_receive_controller.php?recieve_basis='+recieve_basis+'&booking_id='+service_booking_id+'&booking_without_order='+service_booking_without_order+'&fabric_description_id='+fabric_description_id+'&txt_receive_qnty='+txt_receive_qnty+'&txt_job_no='+txt_job_no+'&txt_receive_date='+txt_production_date+'&update_dtls_id='+update_dtls_id+'&update_id='+update_id+'&kitting_charge='+kitting_charge+'&kitting_charge_2nd='+kitting_charge_2nd+'&save_data='+save_data+'&name_color='+name_color+'&is_sales='+hdn_is_sales+'&txt_batch_id='+txt_batch_id + '&cbo_company_id='+cbo_company_id + '&txt_process_id=' + "'" + txt_process_id + "'" + '&booking_no='+ txt_service_booking + '&action=yarn_lot_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=250px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("hidden_process_string").value;
			var theename=this.contentDoc.getElementById("hidden_knitting_rate").value;
			$('#knitting_charge_string').val(theename);
			$('#process_string').val(theemail);
			var rate="";var amount = "";
			if(theename!="")
			{
				var popup_value=theename.split("*");
				var process_string=theemail.split("*");
				rate=(popup_value[0]*1)+(popup_value[1]*1);

				// As per decision currency is TAKA and Exchange rate is 1. which is also implied in garments receive page also

				var txt_exchange_rate = 1;//$('#txt_exchange_rate').val()*1;
				rate = rate/txt_exchange_rate;
				//hidden_dying_charge*txt_grey_used*txt_hidden_rate*txt_hidden_amount

				amount=	($('#txt_production_qty').val()*1)*rate;
				$('#hidden_dying_charge').val(popup_value[0]);
				$('#txt_grey_used').val(process_string[1]);
				$('#txt_rate').val(number_format(rate,4,'.' , ""));
				$('#txt_hidden_rate').val(rate);
				$('#txt_amount').val(number_format(amount,2,'.' , ""));
				$('#txt_hidden_amount').val(amount);
				$('#check_production_qty').val($('#txt_production_qty').val());
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">

		<? echo load_freeze_divs ("../",$permission); ?>
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:830px; float:left;" align="center">
				<fieldset style="width:820px;">
					<legend>Finish Fabric Entry</legend>
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
								<td>Production Basis</td>
								<td>
									<?
									echo create_drop_down("cbo_production_basis", 150, $receive_basis_arr,"", 0,"", 5,"set_production_basis();","","4,5","","","");
									?>
								</td>
								<td class="must_entry_caption">Company Name</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/finish_fabric_receive_controller', this.value, 'load_drop_down_location_lc', 'location_td' );get_php_form_data(this.value,'roll_maintained','requires/finish_fabric_receive_controller' );check_store();load_room_rack_self_bin('requires/finish_fabric_receive_controller*2', 'store','store_td', this.value);" );
									?>
								</td>
								<td id="" class="must_entry_caption">Location</td>
								<td id="location_td">
									<?
									echo create_drop_down("cbo_location", 150, $blank_array,"", 1,"--Select --", 0,"");
									?>
								</td>

							</tr>
							<tr>
								<td class="must_entry_caption">Dyeing Source</td>
								<td>
									<?
									echo create_drop_down("cbo_dyeing_source", 150, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/finish_fabric_receive_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_dyeing_com','dyeingcom_td');load_drop_down( 'requires/finish_fabric_receive_controller', this.value+'_'+$('#cbo_dyeing_company').val(), 'load_drop_down_machine_name','machine_name_td');check_location()","","1,3");
									?>
								</td>
								<td class="must_entry_caption">Dyeing Company</td>
								<td id="dyeingcom_td">
									<?
									echo create_drop_down("cbo_dyeing_company", 160, $blank_array,"", 1,"-- Select Dyeing Company --", 0,"");
									?>
								</td>
								<td id="search_location_th">Dyeing Location</td>
								<td id="location_td_dyeing">
									<?
									echo create_drop_down("cbo_location_dyeing", 150, $blank_array,"", 1,"--Select --", 0,"");
									?>
								</td>

							</tr>
							<tr>
								<td>Challan No.</td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" maxlength="20" title="Maximum 20 Character" />
								</td>
								<td id="search_by_th_up"> Store Name </td>
								<td id="store_td">
									<?
									echo create_drop_down( "cbo_store_name", 160, $blank_array,"",1, "--Select--", 1, "" );
									?>
								</td>
								<td class="must_entry_caption">Production Date</td>
								<td>
									<input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" value="<? echo date('d-m-Y');?>" style="width:140px;" readonly >
								</td>
							</tr>
							<tr>
								<td>Booking No</td>
								<td>
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:140px;" placeholder="Browse" ondblclick="openmypage_booking()" disabled readonly />
									<input type="hidden" name="txt_booking_id" id="txt_booking_id" value="" />
								</td>
							</tr>
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="810" border="0">
						<tr>
							<td width="70%" valign="top">
								<fieldset>
									<legend>New Entry</legend>
									<table cellpadding="0" cellspacing="2" width="100%">
										<tr>
											<td width="110" class="must_entry_caption">Batch No.</td>
											<td>
												<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:120px;" placeholder="Write / Browse" onDblClick="openmypage_batchnum();" onChange="check_batch(this.value,document.getElementById('txt_batch_extantion').value);" />
												<input type="text" name="txt_batch_extantion" id="txt_batch_extantion" class="text_boxes" style="width:35px;" placeholder="Ext." onChange="check_batch(document.getElementById('txt_batch_no').value,this.value);"/>
												<input type="hidden" name="txt_batch_id" id="txt_batch_id" readonly />
												<input type="hidden" name="txt_batch_qty" id="txt_batch_qty" readonly />
												<input type="hidden" name="batch_booking_without_order" id="batch_booking_without_order"/>
												<input type="hidden" name="is_sales" id="is_sales"/>
												<input type="hidden" name="hdn_currency_id" id="hdn_currency_id" readonly/></td>
											</td>
											<td class="must_entry_caption" width="90">QC Pass Qty</td>
											<td>
												<input type="text" name="txt_production_qty" id="txt_production_qty" class="text_boxes_numeric" placeholder="Single Click" style="width:130px;" onClick="openmypage_po()" readonly />
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Body Part</td>
											<td id="body_part_td">
												<?
												echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",0 );
												?>
											</td>
											<td>Reject Qty</td>
											<td>
												<input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:34px;" readonly />
												<span>Qc Qnty</span>
												<input type="text" name="txt_qc_qty" id="txt_qc_qty" class="text_boxes_numeric" style="width:41px;"  />
											</td>
										</tr>
										<tr>
											<td class="must_entry_caption">Fabric Description</td>
											<td>
												<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:170px;" readonly disabled/>
												<input type="hidden" name="fabric_desc_id" id="fabric_desc_id" readonly/>
											</td>
											<td class="must_entry_caption">Color</td>
											<td>
												<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:130px;" disabled />
											</td>
										</tr>
										<tr>
											<td>GSM</td>
											<td>
												<input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:85px;" maxlength="10"/>
												<input type="hidden" name="txt_original_gsm" id="txt_original_gsm" />
												<span class="must_entry_caption">UOM</span>
												<?
												echo create_drop_down( "cbouom", 50, $unit_of_measurement,'', 1, '-Uom-', 12, "",0,"1,12,23,27" );
												?>
											</td>
											<td>No Of Roll</td>
											<td>
												<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:130px;"/>
											</td>
										</tr>
										<tr>
											<td>Dia/Width</td>
											<td>
												<input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes" style="width:170px;" maxlength="10" />
												<input type="hidden" name="txt_original_dia_width" id="txt_original_dia_width" />
											</td>
											<td>Machine Name</td>
											<td id="machine_name_td">
												<?
												echo create_drop_down( "cbo_machine_name", 142, $blank_array,"", 1, "-- Select Machine --", 0, "","" );
												?>
											</td>
										</tr>
										<tr>
											<td>Buyer</td>
											<td>
												<input type="text" name="buyer_name" id="buyer_name" class="text_boxes" placeholder="Display" style="width:170px;" disabled="disabled" />
												<input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" disabled="disabled" />
											</td>
											<td>Batch Status</td>
											<td>
												<? echo create_drop_down( "cbo_batch_status", 142,$batch_status_array,"", 0, "--  --", 1, "",0 ); ?>
											</td>
										</tr>

										<tr>
											<td>Shift Name</td>
											<td>
												<? echo create_drop_down( "cbo_shift_name", 182,$shift_name,"", 1, "-- Select --", 0, "",0 ); ?>
											</td>
											<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 142,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
										</tr>
										<tr>
											<td>Process Name</td>
											<td>
												<input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:170px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" readonly />
												<input type="hidden" name="txt_process_id" id="txt_process_id" value="" />
											</td>
											<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 142,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>

										</tr>
										<tr>
											<td>Fabric Shade</td>
											<td>
												<? echo create_drop_down( "cbo_fabric_shade", 180,$fabric_shade,"", 1, "-- Select Shade--", 0, "",0 ); ?>
											</td>
											<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 142,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>

										</tr>
										<tr>
											<td>Dia/ W. Type</td>
											<td>
												<? echo create_drop_down( "cbo_dia_width_type", 180, $fabric_typee,"",1, "-- Select --", 0, "" ); ?>
											</td>
											<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 142,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>

										</tr>
										<tr>
											<td>Grey Used</td>
											<td>
												<input type="text" name="txt_grey_used" id="txt_grey_used" class="text_boxes_numeric" style="width:170px;"   />
											</td>
											<td>Service Booking</td>
											<td>
												<input type="text" name="txt_service_booking" id="txt_service_booking" class="text_boxes" style="width:130px" placeholder="Double Click to Search" onDblClick="openmypage_serviceBooking(2);">
												<input type="hidden" name="service_booking_without_order" id="service_booking_without_order" class="text_boxes" style="width:40px">
												<input type="hidden" name="service_booking_id" id="service_booking_id">
											</td>
										</tr>
										<tr>
											<td>Rate</td>
											<td>
												<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:170px;" readonly />
												<input type="hidden" name="txt_hidden_rate" id="txt_hidden_rate" class="text_boxes_numeric">
											</td>
											<td>Amount</td>
											<td>
												<input type="text" name="txt_amount" id="txt_amount" class="text_boxes" style="width:130px" readonly />
												<input type="hidden" name="txt_hidden_amount" id="txt_hidden_amount" class="text_boxes_numeric">
											</td>
										</tr>
										<tr>
											<td>Remarks</td>
											<td colspan="3">
												<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:420px;"   />
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
							<td width="2%" valign="top">&nbsp;</td>
							<td width="28%" valign="top">
								<fieldset>
									<legend>Display <small id="over_production"></small> </legend>
									<table>
										<tr>
											<td width="100">Batch Quantity</td>
											<td>
												<input type="hidden" name="txt_batch_qnty" id="txt_batch_qnty" class="text_boxes" style="width:100px;" disabled />
												<input type="text" name="show_txt_batch_qnty" id="show_txt_batch_qnty" class="text_boxes" style="width:100px;" disabled />
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
												<input type="hidden" name="txt_yet_receive" id="txt_yet_receive" class="text_boxes" style="width:100px;" disabled />
												<input type="text" name="show_txt_yet_receive" id="show_txt_yet_receive" class="text_boxes" style="width:100px;" disabled />
											</td>
										</tr>
										<tr>
											<td colspan="4" align="right">
												<input id="knit_defect" name="knit_defect" class="formbuttonplasminus" style="width:200px;" value="QC Result" onClick="fn_knit_defect(1)" type="button">

												<input id="knit_defect2" name="knit_defect2" class="formbuttonplasminus" style="width:200px;" value="QC Result2" onClick="fn_knit_defect(2)" type="button">
											</td>
										</tr>

									</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="4" class="button_container">
								<?
								echo load_submit_buttons($permission, "fnc_finish_production", 0,1,"reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container','','cbo_production_basis,5','disable_enable_fields(\'cbo_company_id\');set_production_basis();set_auto_complete();')",1);
								?>
								<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
								<input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
								<input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
								<input type="hidden" name="hidden_receive_qnty" id="hidden_receive_qnty" readonly>
								<input type="hidden" name="hidden_wgt_qty" id="hidden_wgt_qty" readonly>
								<input type="hidden" name="all_po_id" id="all_po_id" readonly>
								<input type="hidden" name="save_data" id="save_data" readonly>
								<input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
								<input type="hidden" name="fabric_store_auto_update" id="fabric_store_auto_update" readonly>
								<input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
								<input type="hidden" name="accessoric_data" id="accessoric_data" readonly>
								<input type="hidden" name="emblishment_data" id="emblishment_data" readonly>
								<input type="hidden" name="fabric_data" id="fabric_data" readonly />
								<input type="hidden" name="precost_data" id="precost_data" readonly />
								<input type="hidden" name="process_costing_maintain" id="process_costing_maintain" readonly>
								<input type="hidden" name="hidden_dying_charge" id="hidden_dying_charge" readonly>
								<input type="hidden" name="knitting_charge_string" id="knitting_charge_string" readonly>
								<input type="hidden" name="process_string" id="process_string" readonly>
								<input type="hidden" name="save_rate_string" id="save_rate_string" readonly>
								<input type="hidden" name="check_production_qty" id="check_production_qty" readonly>
							</td>
						</tr>
					</table>
					<div style="width:820px;" id="list_container_finishing"></div>
				</fieldset>
			</div>
			<div style="width:10px; overflow:auto; float:left; position:relative;"></div>
			<div id="list_fabric_desc_container" style="width:360px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
		</form>
	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>