<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create sample delivery entry
Functionality	:
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	09-04-2017
Updated by 		: 	Zakaria joy
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//print_r($sample_delivery_basis);

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Delivery Info","../", 1, 1, $unicode,1,1);
//$sent_to = array(1=>'BH Qty',2=>'Plan',3=>'Dyeing',4=>'Test',5=>'Self');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function get_company_config(company)
	{
		$('#txt_sample_requisition_id').val('');
		$('#hidden_requisition_id').val('');
		$('#list_view_country').text('');
		
		var variable =return_global_ajax_value( company, 'get_qty_source_sample', '', 'requires/sample_delivery_entry_buyer_controller');
		var variable =trim(variable);
		if(variable != '')
		{
			$('#sample_req_source').val(variable);
		}
	}

	function function_delivery_basis(data)
	{
		if(data==1)
		{
			$("#dynamic_msg").text('Sample Req. No').css("color","blue");
			$("#txt_sample_requisition_id").attr('onDblClick',"openmypage_requisition_popup('requires/sample_delivery_entry_buyer_controller.php?action=sample_requisition_popup&company='+document.getElementById('cbo_company_name').value,'Sample Requisition ID')");
			$("#shipment_status_id").css("visibility","visible");
			$("#cbo_shipping_status").val('0');
			$("#display_info").css("visibility","visible");
		}
		else if(data==2)
		{
			$("#dynamic_msg").text('Order No').css("color","blue");
			$("#shipment_status_id").css("visibility","hidden");
			$("#cbo_shipping_status").val('0');
			$("#display_info").css("visibility","hidden");

			$("#txt_sample_requisition_id").attr('onDblClick',"openmypage_order_popup('requires/sample_delivery_entry_buyer_controller.php?action=order_popup&company='+document.getElementById('cbo_company_name').value,'Order Popup')");
		}
		else if(data==3)
		{
			$("#dynamic_msg").text('Booking No').css("color","blue");
			$("#txt_sample_requisition_id").attr('onDblClick',"openmypage_booking_popup('requires/sample_delivery_entry_buyer_controller.php?action=booking_popup&company='+document.getElementById('cbo_company_name').value,'Booking Popup')");
			$("#shipment_status_id").css("visibility","hidden");
			$("#cbo_shipping_status").val('0');
			$("#display_info").css("visibility","hidden");
		}
		else
		{
			$("#dynamic_msg").text('Sample Req. No').css("color","blue");
			$("#txt_sample_requisition_id").attr('onDblClick',"openmypage_requisition_popup('requires/sample_delivery_entry_buyer_controller.php?action=sample_requisition_popup&company='+document.getElementById('cbo_company_name').value&basis='+document.getElementById('cbo_delivery_basis').value,'Sample Requisition ID')");
			$("#shipment_status_id").css("visibility","hidden");
			$("#cbo_shipping_status").val('0');
			$("#display_info").css("visibility","visible");

		}
	}

	function fnc_sample_delivery_entry(operation)
	{
		if(operation==4)
		{
			print_report( $('#mst_update_id').val()+'*'+$('#dtls_update_id').val()+'*'+$('#cbo_company_name').val()+'*'+$('#cbo_sample_name').val()+'*'+$('#cbo_item_name').val()+'*'+$('#hidden_requisition_id').val()+'*'+$('#hidden_sample_dtls_tbl_id').val(), "delivery_print", "requires/sample_delivery_entry_buyer_controller" )
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			var is_posted=$("#is_posted").val();
			if(is_posted==1)
			{
				alert('Already Posted in Accounts. Update Not Allowed.');
				return;
			}

			if (form_validation('cbo_company_name*txt_delivery_date*cbo_delivery_basis*txt_sample_requisition_id*cbo_sample_name*cbo_item_name*txt_delivery_qty','Company Name*Delivery Date*Delivery Basis*Sample Requisition ID*Sewing Date*Sample Name*Item Name*Delivery Quantity')==false)
			{
				return;
			}
			else
			{
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
				//alert(colorList);return;
				var i=0;  var k=0; var colorIDvalue=''; var colorIDvalueRej='';

				$("input[name=colSizeQty]").each(function(index, element) {
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

				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+"&colorIDvalueRej="+colorIDvalueRej+get_submitted_data_string('mst_update_id*dtls_update_id*cbo_company_name*cbo_location_name*cbo_delivery_to*cbo_delivery_basis*txt_delivery_date*txt_gp_no*txt_final_destination*txt_received_by*txt_sent_by*txt_sample_requisition_id*cbo_sample_name*cbo_item_name*txt_delivery_qty*txt_carton_qnty*txt_remark*cbo_shipping_status*hidden_requisition_id*hidden_sample_dtls_tbl_id*hidden_previous_delv_qty*sample_req_source*txt_invoice_id*txt_invoice_no',"../");
			   // alert(data);return;
			   freeze_window(operation);
				http.open("POST","requires/sample_delivery_entry_buyer_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_sample_delivery_entry_Reply_info;
			}
		}
	}

	function fnc_sample_delivery_entry_Reply_info()
	{
		if(http.readyState == 4)
		{
			var response=http.responseText.split('**');
			//alert(response[5]);return;
			if(response[0]*1==20*1)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			if(response[0]*1==18*1)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			if(response[0]==0)//insert response;
			{
				show_msg(trim(response[0]));
				show_list_view(response[2]+'*'+response[1],'show_dtls_listview','list_view_container','requires/sample_delivery_entry_buyer_controller','setFilterGrid(\'tbl_list_search\',-1);');
				$('#mst_update_id').val(response[1]);
				$('#txt_challan_no').val(response[3]);
				$('#breakdown_td_id').html('');
				var val =return_global_ajax_value( response[1]+"__"+response[4]+"__"+response[2]+'__'+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val()+'__'+response[5], 'populate_data_yet_to_cut', '', 'requires/sample_delivery_entry_buyer_controller');
				//alert(val); return;
				var total_cut=$("#txt_total_finished_qty").val();
				$("#txt_cumul_delivery_qty").val(val);
				if(response[5] == '3'){
					//alert(response[5]);
					 total_cut = $("#txt_sample_quantity").val(); //Sample delivery as soon as requisition variable sample delivery
				}
				
				$("#txt_yet_to_delivery").val(total_cut*1 - val*1);
				$("#cbo_delivery_basis").attr("disabled","disabled");
				$("#print_2").css("visibility","visible");

				childFormReset();
			}
			else if(response[0]==1)//update response;
			{
				show_msg(trim(response[0]));
				show_list_view(response[2]+'*'+response[1],'show_dtls_listview','list_view_container','requires/sample_delivery_entry_buyer_controller','setFilterGrid(\'tbl_list_search\',-1);');
				$('#breakdown_td_id').html('');
			   var val =return_global_ajax_value( response[1]+"__"+response[4]+"__"+response[2]+'__'+$('#cbo_sample_name').val()+"__"+$('#cbo_item_name').val()+'__'+response[5], 'populate_data_yet_to_cut', '', 'requires/sample_delivery_entry_buyer_controller');
				var total_cut=$("#txt_total_finished_qty").val();
				$("#txt_cumul_delivery_qty").val(val);
				if(response[5] == '3'){
					 total_cut = $("#txt_sample_quantity").val(); //Sample delivery as soon as requisition variable sample delivery
				}
				$("#txt_yet_to_delivery").val(total_cut*1 - val*1);
				$("#cbo_delivery_basis").attr("disabled","disabled");
				$("#print_2").css("visibility","visible");
				set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
				childFormReset();
			}
			else if(response[0]==2)//delete response;
			{
				show_msg(trim(response[0]));

				show_list_view(response[2]+'*'+response[1],'show_dtls_listview','list_view_container','requires/sample_delivery_entry_buyer_controller','setFilterGrid(\'tbl_list_search\',-1);');
				childFormReset();
				set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
			}
			release_freezing();
		}
	}

	function openmypage_requisition_popup(page_link,title)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=940px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var smp_id=this.contentDoc.getElementById("selected_id").value;//requisition id
			var req_no=this.contentDoc.getElementById("req_no").value;//requisition no

			if (smp_id!="")
			{
				$("#txt_sample_requisition_id").val(req_no);
				$("#hidden_requisition_id").val(smp_id);
				var variable = $('#sample_req_source').val();
				//alert(variable); return;
				var mst_id = return_global_ajax_value(smp_id, 'get_ex_fac_id', '', 'requires/sample_delivery_entry_buyer_controller');
				var mst_id_arr = mst_id.split('*');
				//get_php_form_data(mst_id, "populate_data_from_search_popup", "requires/sample_delivery_entry_buyer_controller" );
				show_list_view(smp_id+'*'+variable,'show_sample_item_listview','list_view_country','requires/sample_delivery_entry_buyer_controller','');
				//show_list_view(smp_id+'*'+mst_id_arr[0],'show_dtls_listview','list_view_container','requires/sample_delivery_entry_buyer_controller','');
				$("#cbo_delivery_basis").attr("disabled","disabled");
				set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
				release_freezing();
			}
		}
	}

	function openmypage_order_popup(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("selected_id").value;//requisition id
			var po_no=this.contentDoc.getElementById("selected_value").value;//requisition id

			if (po_id!="")
			{
				$("#txt_sample_requisition_id").val(po_no);
				$("#hidden_requisition_id").val(po_id);
				$("#cbo_delivery_basis").attr("disabled","disabled");
				//get_php_form_data(po_id, "populate_po_data_from_search_popup", "requires/sample_delivery_entry_buyer_controller" );

				show_list_view(po_id,'show_po_item_listview','list_view_country','requires/sample_delivery_entry_buyer_controller','');

				//show_list_view(po_id,'show_dtls_listview','list_view_container','requires/sample_delivery_entry_buyer_controller','');

				set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
				release_freezing();
			}
		}
	}

	function openmypage_booking_popup(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("selected_id").value;//requisition id
			var po_no=this.contentDoc.getElementById("selected_value").value;//requisition id
			//alert(po_no);
			if (po_id!="")
			{
				$("#txt_sample_requisition_id").val(po_no);
				$("#hidden_requisition_id").val(po_id);
				$("#cbo_delivery_basis").attr("disabled","disabled");
				//get_php_form_data(po_id, "populate_booking_data_from_search_popup", "requires/sample_delivery_entry_buyer_controller" );

				show_list_view(po_id,'show_booking_item_listview','list_view_country','requires/sample_delivery_entry_buyer_controller','');
				/*
				show_list_view(po_id,'show_dtls_listview','list_view_container','requires/sample_delivery_entry_buyer_controller','');*/
				set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
				release_freezing();
			}
		}
	}

	function put_sample_item_data(sample_dtls_part_tbl_id,smp_id,gmts)
	{
		var mst_id=$("#mst_update_id").val();
		var req_id=$("#hidden_requisition_id").val();
		var req_src=$("#sample_req_source").val();
		get_php_form_data(sample_dtls_part_tbl_id+'**'+smp_id+'**'+req_id+'**'+gmts+'**'+req_src+'**'+mst_id, "color_and_size_level", "requires/sample_delivery_entry_buyer_controller" );
		set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
	}

	function put_po_item_data(po_id,po_number,gmts)
	{
		var req_id=$("#hidden_requisition_id").val();
		get_php_form_data(po_id+'**'+po_number+'**'+req_id+'**'+gmts, "color_and_size_level_po", "requires/sample_delivery_entry_buyer_controller" );
		set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
	}

	function put_booking_item_data(book_id,sample,sample)
	{
		var req_id=$("#hidden_requisition_id").val();
		//alert(mst_id+' '+smp_id+' '+gmts+' '+req_id);return;
		//freeze_window(5);
		get_php_form_data(book_id+'**'+sample+'**'+req_id+'**'+sample, "color_and_size_level_booking", "requires/sample_delivery_entry_buyer_controller" );
		set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
		//release_freezing();
	}

	function fn_total(tableName,index) // for color and size level
	{
		var filed_value = $("#colSizeQty_"+tableName+index).val();
		//var placeholder_value = 0;
		var placeholder_value = $("#colSizeQty_"+tableName+index).attr('placeholder');
		var totalRow = $("#table_"+tableName+" tr").length;
		math_operation( "total_"+tableName, "colSizeQty_"+tableName, "+", totalRow);
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}

		var totalVal = 0;
		$("input[name=colSizeQty]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_delivery_qty").val(totalVal);
		var cbo_delivery_basis=$("#cbo_delivery_basis").val()*1;
		var cbo_delivery_to=$("#cbo_delivery_to").val()*1;
		if(cbo_delivery_basis==1)
		{
			if(filed_value*1 > placeholder_value*1)
			{
				if( confirm("Qnty Excceded by "+(placeholder_value-filed_value)) )
				{
					//$("#txt_delivery_qty").val('');
					//$("#colSizeQty_"+tableName+index).val('');
				}
				else
				{
					$("#txt_delivery_qty").val('');
					$("#colSizeQty_"+tableName+index).val('');
				}
			}
		}
		/*if(cbo_delivery_basis==1 && cbo_delivery_to != 0)
		{
			var req_id = $("#hidden_requisition_id").val();
			var sample_id = $("#cbo_sample_name").val()*1;
			//var sample_id = document.getElementById("cbo_sample_name").value;
			var dtls_id = $("#dtls_update_id").val();
			var item_id = $("#cbo_item_name").val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			var i=0;  var k=0; var colorIDvalue='';
			colorIDvalue = colorList[i]+"*"+filed_value;
			var data = req_id+'_'+sample_id+'_'+dtls_id+'_'+colorIDvalue+'_'+item_id+'_'+cbo_delivery_to;
			var val =return_global_ajax_value( data, 'qty_validation', '', 'requires/sample_delivery_entry_buyer_controller');
			if(val != '')
			{
				if (confirm(val)) {
				  //console.log('Thing was saved to the database.');

				}
				else {
				  $("#txt_delivery_qty").val('');
				  $("#colSizeQty_"+tableName+index).val('');
				}
			}

		}*/
	}

	function fn_total_rej(tableName,index) // for color and size level
	{
		var filed_value = $("#colSizeRej_"+tableName+index).val();
		var totalRow = $("#table_"+tableName+" tr").length;
		math_operation( "total_"+tableName, "colSizeRej_"+tableName, "+", totalRow);
		var totalValRej = 0;
		$("input[name=colorSizeRej]").each(function(index, element) {
			totalValRej += ( $(this).val() )*1;
		});
		$("#txt_reject_qnty").val(totalValRej);
	}

	function childFormReset()
	{
		reset_form('','','txt_challan_no*txt_delivery_qty*txt_carton_qnty*txt_remark');
		disable_enable_fields('cbo_company_name*cbo_delivery_basis*cbo_sample_name*cbo_item_name',0)
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

	function ex_factory_sys_popup()
	{
		/*if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		*/
		var page_link="requires/sample_delivery_entry_buyer_controller.php?action=sys_search_popup&company="+$("#cbo_company_name").val();
		var title="Sample Delivery Info";

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width=1040px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var smp_id=this.contentDoc.getElementById("selected_id").value;
			var smp_id_arr=smp_id.split('*');
			freeze_window(5);
			$("#txt_development_sample_id").val(smp_id_arr[0]);

			$("#list_view_country").html();

			//show_list_view(smp_id_arr[0],'show_sample_item_listview','list_view_country','requires/sample_delivery_entry_buyer_controller','');

			get_php_form_data(smp_id, "populate_data_from_search_popup", "requires/sample_delivery_entry_buyer_controller" );
			show_list_view(smp_id,'show_dtls_listview','list_view_container','requires/sample_delivery_entry_buyer_controller','setFilterGrid(\'tbl_list_search\',-1);');
			var basis=$("#cbo_delivery_basis").val()*1;
			var variable = $("#sample_req_source").val();
			/*var variable =return_global_ajax_value( company, 'get_qty_source_sample', '', 'requires/sample_delivery_entry_buyer_controller');
	*/		if(basis ==1)
			{
				show_list_view(smp_id_arr[0]+'*'+variable,'show_sample_item_listview','list_view_country','requires/sample_delivery_entry_buyer_controller','');
			}
			function_delivery_basis(basis);
			$("#cbo_delivery_basis").attr("disabled","disabled");
			$("#print_2").css("visibility","visible");
			set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
			release_freezing();
		}
	}

	function generate_delivery_report(type)
	{
		if ( form_validation('txt_challan_no','Challan No')==false )
		{
			return;
		}
		else
		{
			window.open("requires/sample_delivery_entry_buyer_controller.php?data=" + $('#txt_challan_no').val()+'*'+$('#mst_update_id').val()+'*'+$("#cbo_company_name").val()+'*'+$('#cbo_delivery_basis').val()+'&action='+type, true );
		}
	}

	function clearChallan()
	{

		$("#list_view_container").html("");
		$("#txt_challan_no").val("");
		$("#mst_update_id").val("");
		set_button_status(0, permission, 'fnc_sample_delivery_entry',1,0);
	}

	function fn_invoice()
	{
		if( form_validation('cbo_company_name*txt_sample_requisition_id','Company Name*requisiton')==false )
		{
			return;
		}
		var company_id=document.getElementById('cbo_company_name').value;
		var requisition_id=document.getElementById('hidden_requisition_id').value;
		//alert(requisition_id+"="+$("#hidden_requisition_id").val());
		var title="Invoice Popup";
		var page_link='requires/sample_delivery_entry_buyer_controller.php?action=invoice_popup&company_id='+company_id+'&requisition_id='+requisition_id;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var inv_id=this.contentDoc.getElementById("txt_inv_id").value;//requisition id
			var inv_no=this.contentDoc.getElementById("txt_inv_no").value;//requisition no
			$("#txt_invoice_id").val(inv_id);
			$("#txt_invoice_no").val(inv_no);
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left;">
        <fieldset style="width:930px;">
        <legend>Sample Production</legend>
			<form name="sampleDelivery_1" id="sampleDelivery_1" autocomplete="off" >
                <fieldset>
                    <table width="100%" border="0">
                    <tr>
                        <td align="right" colspan="3">Challan No</td>
                        <td colspan="3"><input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text"  style="width:160px" onDblClick="ex_factory_sys_popup()" placeholder="Browse or Search" /></td>
                    </tr>
                    <tr>
                        <td align="left" class="must_entry_caption">Company Name </td>
                        <td><?=create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/sample_delivery_entry_buyer_controller', this.value, 'load_drop_down_location', 'location_td' ); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/sample_delivery_entry_buyer_controller');get_company_config(this.value)",0 ); ?></td>
                        <td align="left">Location</td>
                        <td id="location_td"><?=create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td align="left"> Sent To</td>
                        <td><?=create_drop_down( "cbo_delivery_to", 172, $sample_sent_to_list,"", 1, "Select", $selected, "clearChallan()",0 ); ?></td>
                    </tr>
                    <tr>
                        <td align="left" class="must_entry_caption">Delivery Date</td>
                        	<td>
                        	<input name="txt_delivery_date" id="txt_delivery_date" class="datepicker"  style="width:160px;" value="<? echo date('d-m-Y');?>" >
                        	<input type="hidden" name="txt_gp_no" id="txt_gp_no" class="text_boxes" style="width:160px;" >
                        	</td>
							<td>Final Destination</td>
	                        <td><input type="text" name="txt_final_destination" id="txt_final_destination" class="text_boxes" style="width:160px;" maxlength="50"></td>
	                        <td align="left">Received By</td>
                            <td><input name="txt_received_by" id="txt_received_by" class="text_boxes"  style="width:160px "></td>
                        </tr>
                        <tr>
                        	<td class="must_entry_caption">Delivery Basis</td>
                        	<td>
                        		<?=create_drop_down( "cbo_delivery_basis", 172, $sample_delivery_basis,"", 1, "-- Select Delivery Basis--", 1, "function_delivery_basis(this.value)",0 ); ?>
                        		<input type="hidden" name="sample_req_source" id="sample_req_source" value="">
                        	</td>
                        	<td>Sent By</td>
                        	<td ><input name="txt_sent_by" id="txt_sent_by" class="text_boxes"  style="width:160px "></td>
                        </tr>
                    </table>
                </fieldset>
                <br>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="35%" valign="top">
                            <fieldset>
                            <legend>New Entry </legend>
                            <table  cellpadding="0" cellspacing="1" width="100%">
                                <tr>
                                	<td align="left"  class="must_entry_caption" id="dynamic_msg">Sample Req. No</td>
                                    <td>
                                        <input name="txt_sample_requisition_id" placeholder="Double Click to Search" id="txt_sample_requisition_id"   class="text_boxes" style="width:138px " readonly>
                                        <input type="hidden" id="mst_update_id"  value="" />
                                        <input type="hidden" id="hidden_requisition_id" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" class="must_entry_caption">Sample Name</td>
                                    <td><?=create_drop_down( "cbo_sample_name", 150,"select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", 1, "Select Sample", $selected, "",0,0 ); ?></td>
                            		<input type="hidden" name="hidden_sample_dtls_tbl_id" id="hidden_sample_dtls_tbl_id" value="">
                                    <input type="hidden" name="dtls_update_id" id="dtls_update_id" />
                               </tr>
                               <tr>
                                   	<td align="left" class="must_entry_caption"> Item Name</td>
		                             <td><?=create_drop_down( "cbo_item_name", 150, $garments_item,"", 1, "-- Select Item --", $selected, "",0,0 ); ?></td>
                               </tr>
                               <tr>
                                    <td align="left" class="must_entry_caption">Delivery Quantity</td>
                                    <td width="" valign="top"><input name="txt_delivery_qty" id="txt_delivery_qty" class="text_boxes_numeric"  style="width:138px" readonly >
                                    	<input type="hidden" name="hidden_previous_delv_qty" id="hidden_previous_delv_qty">
                                        <input type="hidden" id="hidden_colorSizeID"  value=""/>
                                    </td>
                               </tr>
                               <tr>
                                    <td align="left">Total Carton Qty</td>
                                    <td><input type="text" name="txt_carton_qnty" id="txt_carton_qnty" class="text_boxes_numeric" style="width:138px;" /></td>
                               </tr>
                               <tr>
                                 	<td align="left">Remarks</td>
                                 	<td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:138px;" /></td>
                               </tr>
                               <tr>
                                 	<td align="left">Invoice No</td>
                                 	<td>
                                     <input type="text" name="txt_invoice_no" id="txt_invoice_no" class="text_boxes" onDblClick="fn_invoice()" placeholder="Double Click to Search" style="width:138px;" readonly />
                                     <input type="hidden" name="txt_invoice_id" id="txt_invoice_id" />
                                    </td>
                               </tr>
                               <tr id="shipment_status_id">
                                     <td>Delivery Status<span id="completion_perc"></span></td>
                                     <td><?=create_drop_down( "cbo_shipping_status", 150, $shipment_status,"", 0, "-- Select --", 2, "",0,'2,3','','','','' ); ?></td>
                               </tr>
                          </table>
                        </fieldset>
                        </td>
                        <td width="1%" valign="top"></td>
                         <td width="22%" id="display_info" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table cellpadding="0" cellspacing="2" width="100%" >
								<input type="hidden" name="txt_sample_quantity" id="txt_sample_quantity" class="text_boxes_numeric" style="width:80px" />
                                	<tr>
                                        <td align="left" width="110" id="dynamic_cut_qty">Finished Qty</td>
                                        <td><input type="text" name="txt_total_finished_qty" id="txt_total_finished_qty" class="text_boxes_numeric" style="width:80px" readonly disabled /></td>
                                    </tr>
                                    <tr>
                                        <td align="left" width="110">Cumul. Delivery. Qty</td>
                                        <td><input type="text" name="txt_cumul_delivery_qty" id="txt_cumul_delivery_qty" class="text_boxes_numeric" style="width:80px"  disabled /></td>
                                    </tr>
                                     <tr>
                                        <td align="left" width="110">Yet to Delivery</td>
                                        <td><input type="text" name="txt_yet_to_delivery" id="txt_yet_to_delivery" class="text_boxes_numeric" style="width:80px"  disabled /></td>
                                    </tr>
                                </table>
                            </fieldset>

                            <fieldset>
                            <legend>Requisition info</legend>
                             <table  cellpadding="0" cellspacing="2" width="100%" >
                              <tr>
								<td width="110">Sample Stage</td>
								<td><?=create_drop_down( "cbo_sample_stage", 93, $sample_stage, "", 1, "--display --", $selected, "", 1, "" ); ?></td>
							  </tr>
							<tr>
								<td width="110">Style Ref</td>
								<td><input name="txt_style_no" id="txt_style_no" class="text_boxes" type="text" value="" style="width:80px;" disabled="" /> </td>
							</tr>
							<tr>
								<td width="110">Buyer Name</td>
								<td>
									<?=create_drop_down( "cbo_buyer_name", 93, "select id,buyer_name from lib_buyer buy where status_active=1 and is_deleted=0","id,buyer_name", 1, "-- display --", $selected, "",1 ); ?>
								</td>
                            </tr>
                            </table>
                            </fieldset>
                        </td>
                        <td width="40%" valign="top" >
                            <div style="max-height:550px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                     </tr>
                     <tr>



		   				<td align="center" colspan="9" valign="middle" class="button_container">

							<?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_sample_delivery_entry", 0,0,"reset_form('sampleDelivery_1','list_view_country','','','childFormReset()')",1);
                            ?>
                            <input type="button" value="Print" onClick="generate_delivery_report('print_delivery_2');" style="width:60px; visibility: :hidden;" name="" id="print" class="formbutton" />
                            <input type="button" value="Print2" onClick="generate_delivery_report('print_delivery_3');" style="width:60px; visibility: :hidden;" name="" id="print2" class="formbutton" />
							<input type="button" value="Print3" onClick="generate_delivery_report('print_delivery_4');" style="width:60px; visibility: :hidden;" name="" id="print3" class="formbutton" />
							<input type="button" value="With Gate Pass" onClick="generate_delivery_report('print_delivery_5');" style="width:85px; visibility: :hidden;" name="" id="print4" class="formbutton" />
           				 <br><div id="posted_account_td" style="float:left; color:red; font-size:14px;"></div>
                         <input name="is_posted" id="is_posted" class="text_boxes" type="hidden"  style="width:80px;" disabled=""
                        </td>

		  			</tr>
                </table>
            </form>
        	</fieldset>
            <div style="float:left;"id="list_view_container"></div>
            <br>
            <div id="data_panel" style="display:none"></div>
        </div>
        <br>
		<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	var data=$("#cbo_delivery_basis").val()*1;
	function_delivery_basis(data);
</script>
</html>