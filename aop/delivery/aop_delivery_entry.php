<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create AOP Delivery Entry
Functionality	:
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	06-04-2019
Updated by 		:  Md Mahbubur Rahman
Update date		:
Oracle Convert 	:
Convert date	:
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
echo load_html_head_contents("AOP production", "../../",1, 1,$unicode,1,'');
//echo load_html_head_contents("AOP production","../../", 1, 1, '','','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
	function set_auto_complete(type)
	{
		if(type=='color_return')
		{
			$("#txt_color").autocomplete({
			source: str_color
			});
		}
	}

	function openmypage_doe()
	{
		if ( form_validation('cbo_company_id*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=$('#cbo_company_id').val()+"_"+$('#cbo_party_name').val()+"_"+$('#cbo_within_group').val();
			emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/aop_delivery_entry_controller.php?action=delv_popup&data='+data,'Delivery Popup Info', 'width=800px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("delivery_id");
				if (theemail.value!="")
				{
					freeze_window(5);
					var datas=theemail.value.split('_');

					//$('#txt_qc_id').val(theemail.value);
					//document.getElementById('txt_qc_id').val(theemail.value);
					get_php_form_data(theemail.value, "load_delv_data_to_form_mst", "requires/aop_delivery_entry_controller" );
					show_list_view(theemail.value,'fabric_finishing_list_view','aop_delevery_list_view','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
					show_list_view(theemail.value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
					if($('#cbo_within_group').val()==2){
						$('#cbo_delevery_name').attr('disabled',false);
					}

					//get_php_form_data(datas[0], "load_qc_data_to_form_mst", "requires/aop_delivery_entry_controller" );
					//show_list_view(datas[1],'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
					//show_list_view(datas[0],'aop_delevery_list_view','aop_delevery_list_view','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
					//reset_form('','','txt_batch_no*txt_batch_ext_no*order_no_id*txt_process_id*txt_description*txt_color*txt_gsm*txt_dia_width*txt_product_qnty*txt_reject_qty*txt_roll_no*cbo_machine_id');
					//document.getElementById('cbo_receive_basis').disabled=true;
					//document.getElementById('cbo_company_id').disabled=true;
					//document.getElementById('cbo_party_name').disabled=true;
					//$('#txt_process_id').focus();
					set_button_status(0, permission, 'aop_delevery_entry',1,1);
					release_freezing();
				}
			}
		}
	}

	function openmypage_qnty(order_no_id)
	{
		var data=order_no_id+"_"+document.getElementById('update_id_dtl').value+"_"+document.getElementById('update_check').value+"_"+document.getElementById('txt_receive_qnty').value;
		//alert (data);return;
		var title = 'Order Qnty Info';
		var page_link = 'requires/aop_delivery_entry_controller.php?action=order_qnty_popup&data='+data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var receive_qnty_tot=this.contentDoc.getElementById("hidden_qnty_tot");
			var receive_qnty=this.contentDoc.getElementById("hidden_qnty");
			var receive_tbl_id=this.contentDoc.getElementById("hidd_qnty_tbl_id");
			//alert (receive_tbl_id.value);return;
			$('#txt_product_qnty').val(receive_qnty_tot.value);
			$('#txt_receive_qnty').val(receive_qnty.value);
			$('#update_id_qnty').val(receive_tbl_id.value);

			if(document.getElementById('update_check').value==1)
			{
				document.getElementById('update_id_qnty').value="";
			}
		}
	}


	function fnc_print_Challan()
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#txt_delevery_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val()+'*'+$('#cbo_party_location').val(), "aop_delevery_entry_print_Challan", "requires/aop_delivery_entry_controller" )
		return;
	}

	function fnc_print_Challan3()
	{
		 	var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#txt_delevery_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "aop_delevery_entry_print_Challan3", "requires/aop_delivery_entry_controller" )
			 return;


	}

	function aop_delevery_entry(operation)
	{
		//alert (operation)
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#txt_delevery_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "aop_delevery_entry_print", "requires/aop_delivery_entry_controller" )
			 return;
		}
		else if(operation==2)
		{
			show_msg('13');
			return;
		}
		else if(operation==0 || operation==1)
		{
			if( form_validation('cbo_company_id*cbo_party_name*txt_delivery_date*txt_gsm*txt_dia_width','Company Name*Party Name*Production Date*GSM*Dia/Width')==false )
			{
				return;
			}
			else if ($('#txt_delevery_qnty').val()==0)
			{
				alert ("Delivery Qty Not Zero or Less.");
				return;
			}
			else if (($('#txt_balance').val()*1 - $('#txt_delevery_qnty').val()*1)<0)
			{
				alert("You are exceeding your balance.");
				return;
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_delevery_id*cbo_company_id*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_delevery_name*cbo_delivery_location*update_id*txt_delivery_date*txt_order_no*txt_order_id*txt_delevery_qnty*cbo_body_part*txt_reject_qty*txt_reject_id*txt_description*txt_fabric_qty*txt_roll_no*txt_gsm*cbo_uom*txt_color*hidden_color_id*txt_dia_width*txt_process_id*txt_buyer_po*txt_buyer_style*txt_remarks*txt_dyeing_batch*txt_batch_id*hidden_dia_type*order_no_id*item_order_id*update_id_qnty*update_check*process_id*txt_buyer_po_id*txt_qc_id*cbo_shiping_status*update_id_dtl',"../../");
				//alert (data);
				freeze_window(operation);
				http.open("POST","requires/aop_delivery_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = aop_delevery_entry_reponse;
			}
		}
	}

	function aop_delevery_entry_reponse()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText);//return;
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			if(reponse[0]*1==14*1)
			{
				release_freezing();
				alert(reponse[1]);
				return;
			}
			
			if(reponse[0]==18 || reponse[0]==11){
				alert(reponse[1]);
				release_freezing(); return;
			}

			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_delevery_id').value = reponse[2];
				document.getElementById('update_id_dtl').value = reponse[3];
				//show_list_view(reponse[1],'aop_delevery_list_view','aop_delevery_list_view','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
				//alert(reponse[1]);
				//echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_qc_id)."**".str_replace("'",'',$order_no_id)."**".str_replace("'",'',$txt_buyer_po_id);

				//$data = $row[csf("qc_id")] . "_" .$row[csf("within_group")]. "_" . $batch_array[$key]['company_id']. "_" .$row[csf("id")]."_" .$row[csf("buyer_po_id")]."";

				show_list_view(reponse[4]+'_'+0+'_'+0+'_'+reponse[1]+'_'+reponse[6],'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
				//$data = $row[csf("qc_id")] . "_" .$row[csf("within_group")]. "_" . $batch_array[$key]['company_id']. "_" .$row[csf("id")]."_" .$row[csf("buyer_po_id")]."";
				show_list_view(reponse[4]+'_'+0+'_'+0+'_'+reponse[1]+'_'+reponse[6],'fabric_finishing_list_view','aop_delevery_list_view','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
				fnResetForm();
				set_button_status(0, permission, 'aop_delevery_entry',1,1);
			}
			release_freezing();
		}
	}

	function set_form_data(data)
	{

		var data=data.split("**");
		//alert(data[2]);
		//var gsm_dia=data[1].split(",");
		$('#txt_qc_id').val(data[0]);
		$('#txt_description').val(data[1]);
		$('#txt_gsm').val(data[2]);
	    $('#txt_dia_width').val(data[3]);
		$('#hidden_color_id').val(data[4]);
		$('#txt_color').val(data[5]);
		$('#txt_process_id').val(data[6]);

		$('#txt_roll_no').val(data[7]);
		$('#cboShift').val(data[8]);
		$('#cbo_uom').val(data[9]);
		$('#txt_buyer_po').val(data[10]);
		$('#txt_buyer_style').val(data[11]);
		$('#txt_buyer_po_id').val(data[12]);
		$('#order_no_id').val(data[13]);
		$('#txt_batch_id').val(data[14]);
		$('#cbo_body_part').val(data[15]);
		$('#cbo_floor_name').val(data[16]);
		$('#txt_qc_qnty').val(data[17]);
		//$('#txt_delevery_qnty').val(data[17]);
		$('#txt_balance').val(data[18]);
		$('#cbo_shiping_status').val(data[19]);
		$('#txt_order_no').val(data[20]);
		set_multiselect('txt_process_id','0','1',data[6],'0')
		set_button_status(0, permission, 'aop_delevery_entry',1,1);
		$('#cbo_party_name').attr('disabled',true);
		$('#cbo_within_group').attr('disabled',true);
		$("#txt_reject_qty").val('');
		$("#txt_reject_id").val('');
		$("#txt_fabric_qty").val('');
		$("#txt_remarks").val('');
		$("#txt_dyeing_batch").val('');
		$("#hidden_dia_type").val('');
		$("#item_order_id").val('');
		$("#update_id_dtl").val('');
		$("#process_id").val('');
	}

	function openmypage_order()
	{
		//alert();
		var company = $('#cbo_company_id').val();
		var party_name = $('#cbo_party_name').val();
		var within_group = $('#cbo_within_group').val();


		if( form_validation('cbo_company_id*cbo_party_name','Company Name*Party')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value;
			var page_link = 'requires/aop_delivery_entry_controller.php?company='+company+'&within_group='+within_group+'&party_name='+party_name+'&action=order_number_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link,'Order No Selection Form', 'width=1100px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("production_id");
				//var ret_value=theemail.value.split("_");
				if (theemail.value!="")
				{
					freeze_window(5);
					//alert(theemail.value)
					//$('#txt_order_id').val(theemail.value);
					get_php_form_data(theemail.value, "load_php_data_to_form_mst", "requires/aop_delivery_entry_controller" );
					show_list_view(theemail.value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_delivery_entry_controller','setFilterGrid("list_view",-1)');
					release_freezing();
				}
			}
		}
	}

	function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_id','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_id').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();

		if(within_group==1 && type==1)
		{

			$('#txt_dyeing_batch').removeAttr("onDblClick").attr("onDblClick","openmypage_dyeing_batch();");
			$('#txt_dyeing_batch').attr('readonly',true);
			$('#txt_dyeing_batch').attr('placeholder','Browse');

			load_drop_down( 'requires/aop_delivery_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);

			$('#cbo_delevery_name').attr('disabled',false);
			$('#cbo_delivery_location').attr('disabled',false);

			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/aop_delivery_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			//$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");

			//$('#txt_dyeing_batch').removeAttr("onDblClick").attr("onDblClick","openmypage_dyeing_batch();");

			//$('#txt_dyeing_batch').removeAttr("onDblClick").attr("onDblClick","openmypage_dyeing_batch(1,'0',1)");
			//$('#txt_dyeing_batch').attr('readonly',true);
			$('#txt_dyeing_batch').attr('placeholder','write');
			$('#txt_dyeing_batch').attr('readonly',false);

			/*$('#txt_order_no').attr('readonly',false);
			$('#txt_order_no').attr('placeholder','Write');*/

			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);

			$('#cbo_delevery_name').val('0');
			$('#cbo_delivery_location').val('0');
			//$('#cbo_delevery_name').attr('disabled',true);
			$('#cbo_delevery_name').attr('disabled',false); // developed by metro
			$('#cbo_delivery_location').attr('disabled',true);

			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/aop_delivery_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' );
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
		}
	}
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange'));
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}
	}



	function openmypage_dyeing_batch()
	{
			var company = $('#cbo_company_id').val();
			var party_name = $('#cbo_party_name').val();
			var within_group = $('#cbo_within_group').val();
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/aop_delivery_entry_controller.php?action=dyeing_batch_popup&data='+data
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Dyeing Batch No.', 'width=300px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				var dtls_id=this.contentDoc.getElementById("txt_dtls_id").value;
				var batch_no=this.contentDoc.getElementById("txt_job_no").value;
				//alert(dtls_id);
				$("#txt_dyeing_batch").val(batch_no );
				release_freezing();
			}

	}


	function fnResetForm()
	{
		$("#txt_order_no").val('');
		$("#txt_order_id").val('');
		$("#txt_delevery_qnty").val('');
		$("#cbo_body_part").val(0);
		$("#txt_reject_qty").val('');
		$("#txt_reject_id").val('');
		$("#txt_description").val('');
		$("#txt_fabric_qty").val('');
		$("#txt_roll_no").val('');
		$("#txt_gsm").val('');
		$("#cbo_uom").val('');
		$("#txt_color").val('');
		$("#hidden_color_id").val('');
		$("#txt_dia_width").val('');
		$("#txt_process_id").val(0);
		$("#txt_buyer_po").val('');
		$("#txt_buyer_style").val('');
		$("#txt_remarks").val('');
		$("#txt_dyeing_batch").val('');
		$("#txt_batch_id").val('');
		$("#hidden_dia_type").val('');
		$("#order_no_id").val('');
		$("#item_order_id").val('');
		$("#update_id_qnty").val('');
		$("#update_check").val('');
		$("#process_id").val('');
		$("#txt_buyer_po_id").val('');
		$("#txt_qc_id").val('');
		$("#update_id_dtl").val('');
		//txt_delevery_id*cbo_company_id*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_delevery_name*cbo_delivery_location*update_id*txt_delivery_date*txt_order_no*txt_order_id*txt_delevery_qnty*cbo_body_part*txt_reject_qty*txt_reject_id*txt_description*txt_fabric_qty*txt_roll_no*txt_gsm*cbo_uom*txt_color*hidden_color_id*txt_dia_width*txt_process_id*txt_buyer_po*txt_buyer_style*txt_remarks*txt_batch_id*hidden_dia_type*order_no_id*item_order_id*update_id_qnty*update_check*process_id*txt_buyer_po_id*txt_qc_id*update_id_dtl
		//$("#tbl_master").find('input').attr("disabled", false);
		//disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_receive_purpose*cbo_store_name*txt_wo_pi*cbo_yarn_count*cbo_yarn_type*cbocomposition1*percentage1*cbo_color', 0, "", "" );
		//set_button_status(0, permission, 'fnc_general_item_receive_entry',1);
		//reset_form('','td_dtls','','','','');
		//$("#txt_rate").val(0);
	}



</script>
<body onLoad="set_hotkey();set_auto_complete('color_return');">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
	<div style="width:780px; float: left;">
	    <fieldset>
		    <legend>AOP Delivery Entry</legend>
		    <form name="aopdelevery_1" id="aopdelevery_1">
		        <table cellpadding="0" cellspacing="1" width="100%">
		            <tr>
		                <td colspan="3">
		                <fieldset>
		                    <table cellpadding="0" cellspacing="2" width="100%">
		                        <tr>
		                            <td align="right" colspan="3"><strong>Delivery ID </strong></td>
		                            <td width="140" align="justify">
		                                <input type="hidden" name="update_id" id="update_id" />
		                                <input type="text" name="txt_delevery_id" id="txt_delevery_id" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_doe();" readonly tabindex="1" >
		                            </td>
		                        </tr>
		                        <tr>
		                            <td width="120" class="must_entry_caption">Company Name</td>
		                            <td width="140">
										<?php
											echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/aop_delivery_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select(); fnc_load_party(1,document.getElementById('cbo_within_group').value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/aop_delivery_entry_controller' );");
		                                ?>
		                            </td>
		                            <td width="120">Location </td>
		                            <td width="140" id="location_td">
										<?
											echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 );
		                                ?>
		                            </td>
		                            <td width="110" class="must_entry_caption">Within Group</td>
                        			<td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" ); ?>
                                    </td>

		                        </tr>
		                        <tr>
		                        	<td class="must_entry_caption">Party Name</td>
		                        	<td id="buyer_td">
										<?
											echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);");
		                                ?>
		                            </td>
		                            <td>Party Location</td>
                        			<td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                                	<td>Delivery Party</td>
		                        	<td id="buyer_td">
										<?
										echo create_drop_down( "cbo_delevery_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/aop_delivery_entry_controller', this.value, 'load_drop_down_delv_location', 'delv_location_td');");
		                                ?>
		                            </td>
                        		</tr>
                        		<tr>
                                    <td>Del.Party Location</td>
                                    <td id="delv_location_td"><? echo create_drop_down( "cbo_delivery_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 );  ?></td>
		                            <td class="must_entry_caption">Delivery Date</td>
		                            <td>
                                    <input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="<? echo date('d-m-Y'); ?>" />
		                            </td>
		                        </tr>
		                    </table>
		                </fieldset>
		                </td>
		            </tr>
		            <tr align="center" id="td_dtls">
		                <td width="75%" valign="top" style="margin-left:10px;">
		                <fieldset style="width:600px">
		                <legend>New Entry</legend>
		                    <table  cellpadding="0" cellspacing="2" width="100%" align="center">
		                        <tr>
		                            <td width="120">Order No</td>
		                            <td width="140" >
		                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_order()" tabindex="10" />
		                                <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes"/>
		                                <input type="hidden" name="txt_qc_id" id="txt_qc_id" class="text_boxes"/>
		                            </td>
		                            <td class="must_entry_caption">Delivery Qty</td>
		                            <td><input type="text" name="txt_delevery_qnty" id="txt_delevery_qnty" class="text_boxes_numeric" style="width:130px;" />
		                            <input type="hidden" name="txt_qc_qnty" id="txt_qc_qnty" class="text_boxes_numeric" style="width:130px;" />
		                            <input type="hidden" name="txt_balance" id="txt_balance" class="text_boxes_numeric" style="width:130px;" />
		                        </tr>
		                        <tr>
									<td>Reject Qty</td>
		                            <td>
										<input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:130px;" tabindex="16" />
		                            	<input type="hidden" name="txt_reject_id" id="txt_reject_id" class="text_boxes_numeric" style="width:130px;" />
		                            </td>
		                            <td>Delivery Status</td>
									<td>
										<? echo create_drop_down( "cbo_shiping_status",140, $shipment_status,'', 0, '--Select--',"$delivery_status","",0,"","","","0,1","","","cboshipingStatus[]"); ?>
									</td>

		                        </tr>
		                        <tr>
		                        	<td>Body Part</td>
									<td>
										<? echo create_drop_down( "cbo_body_part", 140, $body_part,"", 1, "--Select--",0,"", 1 ); ?>
									</td>
		                        	<td style="width:120px ;">Fabric Description</td>
		                        	<td>
		                        		<Input name="txt_description" ID="txt_description"  style="width:130px" class="text_boxes" placeholder="Write" disabled >
		                        	</td>
			                    </tr>
			                    <tr>
			                    	<td>Fabric Used</td>
		                            <td>
										<input type="text" name="txt_fabric_qty" id="txt_fabric_qty" class="text_boxes_numeric" style="width:130px;" tabindex="16" />
		                            </td>
		                            <td  style="width:120px ;">GSM</td>
		                            <td>
		                                <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:130px ;text-align:right" tabindex="14" />
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td>No. Of Roll</td>
		                            <td><input type="text" name="txt_roll_no" id="txt_roll_no" class="text_boxes_numeric" style="width:130px;" /></td>
		                        	<td>Color</td>
			                        <td>
			                            <input type="text" name="txt_color" id="txt_color" class="text_boxes" value="" style="width:130px;" readonly/>
			                            <input type="hidden" value="" id="hidden_color_id">
			                        </td>
		                        </tr>
		                        <tr>
		                        	<td  style="width:120px ;" class="must_entry_caption">UOM</td>
		                            <td>
		                               <? echo create_drop_down( "cbo_uom", 140, $unit_of_measurement,'', 1, '-Select-', $uom, "","1","1,12,15,23,27" ); ?>
		                            </td>
                                	<td class="must_entry_caption">Dia/Width</td>
		                            <td>
		                                <input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes" style="width:130px;" tabindex="15" />
		                            </td>
		                        </tr>
		                        <tr>
									<td>Process Name</td>
			                        <td>
			                        	<? echo create_drop_down( "txt_process_id", 140, $conversion_cost_head_array,"", 1, "-- Select Location --", $selected, "" ); ?>
			                        </td>
                                	<td >Buyer PO</td>
		                            <td id="order_numbers">
		                                <input type="text" name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:130px" readonly tabindex="11"/>
		                                <input type="hidden" name="txt_buyer_po_id" id="txt_buyer_po_id" class="text_boxes"/>
		                            </td>
		                        </tr>
                                <tr>
                                	<td >Buyer Style</td>
		                        	<td>
		                                <input type="text" name="txt_buyer_style" id="txt_buyer_style" class="text_boxes" style="width:130px" readonly tabindex="11"/>
		                            </td>
                                 	<td>Dyeing Batch No.</td>
		                            <td>
		                                <input type="text" name="txt_dyeing_batch" id="txt_dyeing_batch" class="text_boxes" style="width:130px"  placeholder="Browse" onDblClick="openmypage_dyeing_batch()"  tabindex="11"/>
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td >Remarks</td>
		                        	<td colspan="3">
		                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:395px" tabindex="11"/>
                                        <input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" style="width:20px"/>
		                            </td>
		                        </tr>
		                    </table>
		                </fieldset>
		                </td>
		            </tr>
		            <tr>
		                <td colspan="6" align="center" class="button_container">
							<?
								$date=date('d-m-Y');
								echo load_submit_buttons($permission, "aop_delevery_entry", 0,0,"",1);
		                    ?>
                              <input type="button" name="print" id="Printt1" value="Print" onClick="aop_delevery_entry(4)" style="width: 80px; display:none;" class="formbutton">
                            
                            <input type="button" value="Print2" id="Print2" name="Print2" class="formbutton" style="width:100px;display:none;" onClick="fnc_print_Challan()" />

                            <input type="button" value="Print3" id="Print3" name="Print3" class="formbutton" style="width:100px;display:none;" onClick="fnc_print_Challan3()" />

		                    <input type="hidden" name="update_id_dtl" id="update_id_dtl" />
		                    <input type="hidden" name="hidden_dia_type" id="hidden_dia_type" />
		                    <input type="hidden" name="order_no_id" id="order_no_id" />
		                    <input type="hidden" name="item_order_id" id="item_order_id" />
		                    <input type="hidden" name="update_id_qnty" id="update_id_qnty" />
		                    <input type="hidden" name="update_check" id="update_check" />
		                    <input type="hidden" name="process_id" id="process_id" />
		                </td>
		            </tr>
		        </table>
		    </form>
	    </fieldset>
	</div>
	<div id="list_fabric_desc_container" style="max-height:500px; width:470px; overflow:auto; float:left; margin:0px 5px 5px 15px;"> <!-- content would load here  --> </div>
	<div style="width:800px; margin-top:10px; float:left;" id="aop_delevery_list_view" align="center"> <!-- content would load here --> </div>
</div>
</body>
<script>
	set_multiselect('txt_process_id','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>