<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Issue Requistion V2

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	23-05-2023
Purpose			:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
//$working_company_sql= sql_select("SELECT WORKING_UNIT_ID FROM user_passwd WHERE id = '$user_id' AND valid = 1");
//if(count($working_company_sql)){$working_cond=" and id in(".$working_company_sql[0]['WORKING_UNIT_ID'].")" ;}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Issue Requistion V2","../", 1, 1, $unicode,'','');

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	
	function fn_order()
	{
		var company = $("#cbo_working_company").val();
		var store_id = $("#cbo_store_name").val();
		var update_id = $("#update_id").val();
		var cbo_trim_type = $("#cbo_trim_type").val();
		var floor_name = $("#cbo_floor_name").val();
		var sewing_line = $("#cbo_sewing_line").val();
		var hdn_reqsn_basis_variable = $("#hdn_reqsn_basis_variable").val();
		var hdn_reqsn_data_source_variable = $("#hdn_reqsn_data_source_variable").val();
		var hdn_data_generate_level = $("#hdn_data_generate_level").val();
		if(hdn_reqsn_data_source_variable==2)
		{
			if( form_validation('cbo_working_company*cbo_store_name*cbo_trim_type','Company Name*Store*Basis')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_working_company*cbo_trim_type','Company Name*Basis')==false )
			{
				return;
			}
		}
		
		

		if(hdn_reqsn_basis_variable==2) cbo_trim_type=2;
		var title = 'PO Info';	
		// var page_link = 'requires/trims_issue_requisition_v2_controller.php?cbo_company_id='+company+'&update_id='+update_id+'&cbo_trim_type='+cbo_trim_type+'&action=po_search_popup';
		var page_link = 'requires/trims_issue_requisition_v2_controller.php?cbo_company_id='+company+'&update_id='+update_id+'&cbo_trim_type='+cbo_trim_type+'&floor_name='+floor_name+'&sewing_line='+sewing_line+'&action=po_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1155px,height=420px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_job_no=this.contentDoc.getElementById("hidden_job_no").value; 
			var hidden_color_id=this.contentDoc.getElementById("hidden_color_id").value;
			var hidden_po_id=this.contentDoc.getElementById("hidden_po_id").value;
			var hidden_challan_no=this.contentDoc.getElementById("hidden_challan_no").value;
			var hidden_size_id=this.contentDoc.getElementById("hidden_size_id").value;

			var hidden_search_id = this.contentDoc.getElementById("hidden_search_id").value;
			var hidden_cutNo = this.contentDoc.getElementById("hidden_cutNo").value;

			var hidden_job_id = this.contentDoc.getElementById("hidden_job_id").value;
			var hidden_job_no_data = this.contentDoc.getElementById("hidden_job_no_data").value;
            //alert(hidden_job_id+hidden_job_no_data)

			//alert(hidden_job_no+"__"+hidden_color_id);return;
			var list_view_po =return_global_ajax_value( hidden_job_no+"__"+hidden_color_id+"__"+cbo_trim_type+"__"+company+"__"+hdn_data_generate_level+"__"+hidden_po_id+"__"+hidden_challan_no+"__"+hidden_search_id+"__"+hidden_cutNo, 'cut_item_details', '', 'requires/trims_issue_requisition_v2_controller');
			$('#txt_order_no').val(hidden_job_no);
			$('#hdnColorIdData').val(hidden_color_id);
			$('#hdn_job_id').val(hidden_job_id);
			$('#hdn_job_no').val(hidden_job_no_data);

			//alert(hdn_data_generate_level);
			if(hdn_data_generate_level==2)
			{
				$("#cbo_working_location").attr("disabled",true);
				$("#cbo_store_name").attr("disabled",true);
				//var all_po_id =return_global_ajax_value( hidden_job_no+"__"+hidden_po_id, 'job_wise_po_data', '', 'requires/trims_issue_requisition_v2_controller');
				fn_item_details(hidden_po_id, hidden_color_id, 0, hidden_size_id, hidden_challan_no);
			}
			
			$('#tbl_size_item_details').html(list_view_po);
			disable_enable_fields('cbo_working_company*cbo_trim_type',1);
		}
	}
	
	function fn_item_details(po_id, gmst_color_id, mst_id, size_id, inputno)
	{
		var store_id = $("#cbo_store_name").val();
		var hdn_reqsn_data_source_variable = $("#hdn_reqsn_data_source_variable").val();
		var store_id = $("#cbo_store_name").val();
		
		if(mst_id==0)
		{
			var list_view_po =return_global_ajax_value( po_id+"__"+gmst_color_id+"__"+mst_id+"__"+hdn_reqsn_data_source_variable+"__"+store_id+"__"+size_id+"__"+inputno, 'product_details', '', 'requires/trims_issue_requisition_v2_controller');
			set_button_status(0, permission, 'fnc_requisition_entry',1,1);

			$("#po_id_data").val(po_id);
			$("#gmst_color_id_data").val(gmst_color_id);
			$("#hdn_reqsn_data_source_variable_data").val(hdn_reqsn_data_source_variable);
			$("#store_id_data").val(store_id);
			$("#size_id_data").val(size_id);
			$("#inputno_data").val(inputno);
		}
		else
		{
			var list_view_po =return_global_ajax_value( po_id+"__"+gmst_color_id+"__"+mst_id+"__"+hdn_reqsn_data_source_variable+"__"+store_id+"__"+inputno, 'product_details_update_input', '', 'requires/trims_issue_requisition_v2_controller');
			$("#txt_order_no").attr("disabled",true);
			set_button_status(1, permission, 'fnc_requisition_entry',1,1);
		}
		
		//alert(list_view_po);return;
		$('#requisition_details_container').html(list_view_po);
	}
	
	function fnc_requisition_entry(operation)
	{
		var hdnColorIdData = $("#hdnColorIdData").val();
		var PoIdForPrint = $("#PoIdForPrint").val();
			var gmtColorIdForPrint = $("#gmtColorIdForPrint").val();
			var variableSourceForPrint = $("#variableSourceForPrint").val();
			var storeIdForPrint = $("#storeIdForPrint").val();
			var sizeIdForPrint = $("#sizeIdForPrint").val();
			var inputChallanForPrint = $("#inputChallanForPrint").val();
			var mstIdForPrint = $("#mstIdForPrint").val();
		if(operation==4)
		{
		
		
			
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_working_company').val()+'*'+$('#cbo_working_location').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+hdnColorIdData+'*'+PoIdForPrint+'*'+gmtColorIdForPrint+'*'+'*'+variableSourceForPrint+'*'+storeIdForPrint+'*'+sizeIdForPrint+'*'+inputChallanForPrint+'*'+mstIdForPrint, "garments_exfactory_print", "requires/trims_issue_requisition_v2_controller" )

			 return;
		}
		if(operation==5)
		{
			if($('#update_id').val()=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_working_company').val()+'*'+$('#cbo_working_location').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+hdnColorIdData+'*'+PoIdForPrint+'*'+gmtColorIdForPrint+'*'+'*'+variableSourceForPrint+'*'+storeIdForPrint+'*'+sizeIdForPrint+'*'+inputChallanForPrint+'*'+mstIdForPrint, "garments_exfactory_print_job", "requires/trims_issue_requisition_v2_controller" )

			 return;
		}
		if(operation==6)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_working_company').val()+'*'+$('#cbo_working_location').val()+'*'+$('#update_id').val()+'*'+report_title, "garments_exfactory_print3", "requires/trims_issue_requisition_v2_controller" )

			 return;
		}
		
		if(operation==0 || operation==1 || operation==2)
		{
			// if(operation==2)
			// {
			// 	show_msg(13);alert("Not Allow now");return;
			// }

			if ( form_validation('cbo_working_company*txt_req_date*cbo_trim_type','Company Name*Requisition Date*Trims Type')==false )
			{
				return;
			}
			else
			{
				var row_num=$('#tbl_item_details tbody tr').length;
				var dataString="";
				var j=0; var i=1;var booking_validation_check=0;
				$("#tbl_item_details").find('tbody tr').each(function()
				{
					//var job_no=$('#tdJob_'+i).html();
					//var buyer_id=$('#tdBuyer_'+i).attr('title');
					//var styleref=$('#tdStyle_'+i).html();
					var job_no=$(this).find('input[name="hdnJobNo[]"]').val();
					
				
					
					var order_id=$(this).find('input[name="hdnPoId[]"]').val();
					var book_dtls_id=$(this).find('input[name="hdnBookDtlsId[]"]').val();
					var pre_cost_dtls_id=$(this).find('input[name="hdnPrecosDtlsId[]"]').val();
					var hdn_job_id = $("#hdn_job_id").val();
					var hdn_job_no = $("#hdn_job_no").val();
					
					//var orderQnty=$('#tdOrderQnty_'+i).html();
					var item_group=$('#tdItemGroup_'+i).attr('title');
					var itemdescription=encodeURIComponent($('#tdItemDescrip_'+i).attr('title'));
					//var product_id=$('#tdItemDescrip_'+i).attr('title');
					//var gtms_color_id=$('#tdGmtsColor_'+i).attr('title');
					//var itemcolorid=$('#tdColor_'+i).attr('title');
					//var itemsizeid=$('#tdSize_'+i).html();
					var cbouom=$('#tdUom_'+i).attr('title');
					//var rcv_qnty=$('#tdRcvQnty_'+i).html();
					//var stock_qnty=$('#tdInhand_'+i).html();
					var reruired_qnty=$('#tdRrequiredQnty_'+i).html()*1;
					var prev_req_qnty=$('#tdPrevReqQnty_'+i).html()*1;
					var bookQnty=$('#tdBookQnty_'+i).html()*1;
					var reqQnty=$(this).find('input[name="txtReqQnty[]"]').val()*1;
					var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
					var updateId=$(this).find('input[name="hdnUpdateDtlsId[]"]').val();
					var prodId=$(this).find('input[name="hdnProdId[]"]').val();
					var all_po_id = $(this).find('input[name="all_po_id[]"]').val();
					var stock_qnty=$('#tdStockQnty_'+i).html()*1;
					var store_rcv_qnty=$('#tdStoreRcvQnty_'+i).html()*1;

					var gmts_color_id=$(this).find('input[name="hdnColorId[]"]').val();
					var hdngmts_sizes=$(this).find('input[name="hdngmts_sizes[]"]').val();
					var ItmDesc=$(this).find('input[name="ItmDesc[]"]').val();
					//alert(ItemDescription)
                 

					if((prev_req_qnty+reqQnty)>bookQnty)
					{
						booking_validation_check=1;
						alert("Requisition Quantity Not Allow Over Booking Quantity");return;
					}
					
					if(reqQnty>0)	
					{
						j++;
						dataString+='&job_no' + j + '=' + job_no + '&gmts_color_id' + j + '=' + gmts_color_id + '&order_id' + j + '=' + order_id + '&book_dtls_id' + j + '=' + book_dtls_id + '&pre_cost_dtls_id' + j + '=' + pre_cost_dtls_id+ '&item_group' + j + '=' + item_group + '&itemdescription' + j + '=' + itemdescription+ '&cbouom' + j + '=' + cbouom + '&bookQnty' + j + '=' + bookQnty + '&reqQnty' + j + '=' + reqQnty+ '&txtRemarks' + j + '=' + txtRemarks+ '&updateId' + j + '=' + updateId+ '&reruired_qnty' + j + '=' + reruired_qnty+ '&prev_req_qnty' + j + '=' + prev_req_qnty+ '&prodId' + j + '=' + prodId+ '&stock_qnty' + j + '=' + stock_qnty+ '&store_rcv_qnty' + j + '=' + store_rcv_qnty + '&hdn_job_id' + j + '=' + hdn_job_id + '&hdn_job_no' + j + '=' + hdn_job_no+'&hdngmts_sizes'+j + '=' +hdngmts_sizes+ '&all_po_id'+j+ '='+ all_po_id + '&ItmDesc'+ j + '=' + ItmDesc ;
					}
					i++;
				});
			
				var data="action=save_update_delete&operation="+operation+"&row_num="+j+get_submitted_data_string('txt_req_no*update_id*cbo_working_company*cbo_working_location*cbo_floor_name*cbo_sewing_line*txt_req_date*txt_delivery_date*cbo_trim_type*txt_remarks*cbo_store_name*po_id_data*gmst_color_id_data*hdn_reqsn_data_source_variable_data*store_id_data*size_id_data*inputno_data*txt_order_no*hdnColorIdData',"../")+dataString;
				
				if(booking_validation_check==1) return;
				//alert(data);return;

	 			freeze_window(operation);
	 			http.open("POST","requires/trims_issue_requisition_v2_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_requisition_entry_Reply_info;
			}
		}
	}

	function fnc_requisition_entry_Reply_info()
	{
	 	if(http.readyState == 4)
		{
			//alert(http.responseText);return;
			var reponse=http.responseText.split('**');

			if(reponse[0]==20)
			{
				alert(reponse[1]);release_freezing(); return;
			}
			else if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(reponse[0]);
				$("#update_id").val(reponse[1]);
				$("#txt_req_no").val(reponse[2]);
				show_list_view(reponse[3],'product_details','requisition_details_container','requires/trims_issue_requisition_v2_controller','');
				//$('#requisition_details_container').html("");
				release_freezing();
				set_button_status(1, permission, 'fnc_requisition_entry',1,1);
				disable_enable_fields('cbo_working_company*cbo_trim_type',1);
				$("#txt_order_no").attr("disabled",false);
			}
			else if(reponse[0]==2)
			{
				location.reload();
				release_freezing();
				set_button_status(0, permission, 'fnc_requisition_entry',1,1);
			}
			else
			{
				show_msg(reponse[0]);
				release_freezing();
			}
	 	}
	}


	function return_system_popup() //Return PopUp
	{
		/*if( form_validation('cbo_working_company','Company Name')==false )
		{
			return;
		}*/
		var page_link='requires/trims_issue_requisition_v2_controller.php?action=delivery_system_popup&company='+document.getElementById('cbo_working_company').value;
		var title="System Popup";
		var company = $("#cbo_working_company").val();
		var txt_challan_no=$("#txt_challan_no").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var return_id=this.contentDoc.getElementById("hidden_return_id").value;
			var p_id=this.contentDoc.getElementById("po_id").value;
			var g_id=this.contentDoc.getElementById("g_id").value;
			var hreq_id=this.contentDoc.getElementById("hreq_id").value;
			var store_id=this.contentDoc.getElementById("store_id").value;
			//alert(delivery_id);return;
			if(return_id !="")
			{

			
		//	var store_id = $("#store_id_data").val();
			var size_id = $("#size_id_data").val();
			var input_id = $("#inputno_data").val();
			
				get_php_form_data(return_id, "populate_master_from_data", "requires/trims_issue_requisition_v2_controller" );
				show_list_view(p_id +'__'+g_id+'__'+hreq_id+'__'+store_id+'__'+'__'+size_id+'__'+input_id+'__'+return_id,'product_details','requisition_details_container','requires/trims_issue_requisition_v2_controller','');
				set_button_status(1, permission, 'fnc_requisition_entry',1,1);
			}
		}
	}

	
	function return_prod_qty_row(id)
	{
		//alert(id);
		var id=id.split('_');
		var return_qty = $("#txtreturnqty_"+id[1]).val()*1;
		//var delivery_qty = $("#txtexfactoryqty_"+id[1]).val()*1;
		var delivery_qty = $("#txtreturnqty_"+id[1]).attr('placeholder');
		if(return_qty>delivery_qty)
		{
			alert('Return qty. over is not allow than delivery qty.');
			$("#txtreturnqty_"+id[1]).val('');
			$("#txtreturnqty_"+id[1]).focus();
			return;
		}
	}

	function chk_stock(i)
	{
		var quantity=$('#txtReqQnty_'+i).val()*1;
		var inHand_qty = $("#txtReqQnty_"+i).attr('placeholder')*1;
		
		if(quantity>inHand_qty)
		{
			alert ("Req. Qty. Can not Exeed In Hand Qty.");
			$('#txtReqQnty_'+i).val('');
			return;
		}
	}
	function disable_anable()
	{
		$('#cbo_working_company').attr('disabled',false);
		$('#cbo_trim_type').attr('disabled',false);
		load_drop_down( 'requires/trims_issue_requisition_v2_controller', 0, 'load_drop_down_location', 'location_td' );
		load_drop_down( 'requires/trims_issue_requisition_v2_controller', 0+'__'+0, 'load_drop_down_store', 'store_id' );
	}
	
	
	function fn_variable_data(com_id)
	{
		var prod_variable_data =return_global_ajax_value( com_id, 'com_wise_variable_data', '', 'requires/trims_issue_requisition_v2_controller');
		prod_variable_data=prod_variable_data.split("**");
		$("#hdn_reqsn_basis_variable").val(prod_variable_data[0]);
		$("#hdn_reqsn_data_source_variable").val(prod_variable_data[1]);
		$("#hdn_data_generate_level").val(prod_variable_data[2]);
		//cbo_trim_type
		$("#cbo_trim_type option").remove();
		
		if(prod_variable_data[0]==2)
		{
			$('#cbo_trim_type').append('<option value="0">Select</option>');
			$('#cbo_trim_type').append('<option value="1">Sewing</option>');
			$("#cbo_store_name").attr('disabled',false);
		}else if(prod_variable_data[0]==1 && prod_variable_data[1]==1 )
		{
			$('#cbo_trim_type').append('<option value="0">Select</option>');
			$('#cbo_trim_type').append('<option value="1">Sewing</option>');
			$("#cbo_store_name").attr('disabled',true);
		}
		
		else
		{
			$('#cbo_trim_type').append('<option value="0">Select</option>');
			$('#cbo_trim_type').append('<option value="1">Sewing</option>');
			$('#cbo_trim_type').append('<option value="2">Packing/Finishing</option>');
			$("#cbo_store_name").attr('disabled',false);
		}
		
			
		
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<?  echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:1450px; float:left">
        <form name="exFactory_1" id="exFactory_1" autocomplete="off" >
        <fieldset style="width:1400px;">
            <legend>Requisition Master</legend>
                <fieldset>
                <table width="100%" border="0">
                	<tr>
                        <td align="right" colspan="4">Requisition No</td>
                        <td colspan="4">
						
							<input type="hidden" id="po_id_data">
							<input type="hidden" id="gmst_color_id_data">
							<input type="hidden" id="hdn_reqsn_data_source_variable_data">
							<input type="hidden" id="store_id_data">
							<input type="hidden" id="size_id_data">
							<input type="hidden" id="inputno_data">
		
                          	<input name="txt_req_no" id="txt_req_no" class="text_boxes" type="text"  style="width:160px" onDblClick="return_system_popup()" placeholder="Browse or Search"  readonly="readonly" />
                          	<input name="update_id" id="update_id" class="text_boxes" type="hidden"  style="width:60px"/>
                        </td>
                    </tr>
					<tr>
                        <td width="100" align="right" class="must_entry_caption">Working Company </td>
                        <td width="160">
                        <?
							echo create_drop_down( "cbo_working_company", 152, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $working_cond order by company_name","id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'requires/trims_issue_requisition_v2_controller', this.value, 'load_drop_down_working_location', 'working_location_td'); fn_variable_data(this.value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/trims_issue_requisition_v2_controller' );",0 ); 
						?>
                        <input type="hidden" id="hdn_reqsn_basis_variable" name="hdn_reqsn_basis_variable" />
                        <input type="hidden" id="hdn_reqsn_data_source_variable" name="hdn_reqsn_data_source_variable" />
                        <input type="hidden" id="hdn_data_generate_level" name="hdn_data_generate_level" />
                        </td>
						<td width="100" align="right">Location</td>
                        <td width="160" id="working_location_td">
                        <? echo create_drop_down( "cbo_working_location", 152, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td width="100" align="right"> Floor</td>
                        <td width="160" id="floor_td">
                        <? echo create_drop_down( "cbo_floor_name", 152, $blank_array,"", 1, "-- Select Floor --", $selected, "" );?>
                        </td>
						<td width="100" align="right"> Sewing Line</td>
                        <td id="sewing_td">
                        <?
                        	echo create_drop_down( "cbo_sewing_line", 152, $blank_array,"", 1, "--- Select ---", $selected, "",1 );
						?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Requisition Date</td>
                        <td>
                        	<input name="txt_req_date" id="txt_req_date" class="datepicker"  style="width:140px;" placeholder="Display" readonly />
                        </td>
                        <td align="right">Delivery Date</td>
                        <td >
                        	<input name="txt_delivery_date" id="txt_delivery_date" class="datepicker"  style="width:140px;" placeholder="Display" readonly />
                        </td>
                        <td width="100" align="right">Store Name</td>
                        <td width="160" id="store_id">
                          <? echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select Store --", $selected, "" );?>
                        </td>
                        <td align="right" class="must_entry_caption">Requisition Basis</td>
						<td>
							<? echo create_drop_down( "cbo_trim_type", 152, $trim_type,"", "1", "---- Select ----", 0, "",0,"1,2" ); ?>
						</td>
                    </tr>
                    <tr>
                    	<td align="right">Job Number</td>
                        <td>
                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:140px;" onDblClick="fn_order();"  placeholder="Browse" readonly />
						<input type="hidden" id="hdnColorIdData">
                        <input type="hidden" id="hdn_order_id" name="hdn_order_id" />
						<input type="hidden" id="hdn_job_id" name="hdn_job_id" />
						<input type="hidden" id="hdn_job_no" name="hdn_job_no" />
                        </td>
						<td align="right">Remarks</td>
						<td colspan="7">
							<input type="text" class="text_boxes" style="width:420px;" name="txt_remarks" id="txt_remarks">
						</td>
                    </tr>
                </table>
                </fieldset>
                
                <fieldset style="width:1570px;">
                <table cellpadding="0" cellspacing="1" width="1570" class="rpt_table" border="1" rules="all" id="tbl_size_item_details" align="left">
                    
                </table>
           		</fieldset>
           
                <fieldset style="width:1220px;">
                <legend>Details List View</legend>
				<div id="requisition_details_container"></div>
                
                <br />
                <table cellpadding="0" cellspacing="1" width="1220">
                    <tr>
                        <td align="center" colspan="10" valign="middle"  class="button_container">
                             <?
                                echo load_submit_buttons($permission, "fnc_requisition_entry",0,1,"reset_form('exFactory_1', 'requisition_details_container_update*requisition_details_container', '', '', 'disable_anable()')",1);
                            ?>
                             <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                             <input style="display:none"  type="button" id="btn_print2" name="btn_print2"  style="width:80px;"  class="formbutton" value="Print2"   onClick="fnc_requisition_entry(5);"/>
                             <input type="button" style="display:none" id="btn_print3" name="btn_print3"  style="width:80px;"  class="formbutton" value="Print3"  onClick="fnc_requisition_entry(6);"/>

                        </td>
						<td id="button_data_panel">

						</td>
                    </tr>
                </table>
           </fieldset>
           		
                <fieldset style="width:1350px;">
                <div style="width:1350px;" id="requisition_details_container_update">
                </div>
           		</fieldset>
                </fieldset>
        </form>
        
    </div>

</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
