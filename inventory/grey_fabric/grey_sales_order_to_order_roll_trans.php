<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Roll wise Grey Sales Order To Sales Order Transfer Entry

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	10-04-2017
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
$user_level=$_SESSION['logic_erp']['user_level'];
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Roll wise Grey Sales Order To Sales Order Transfer Entry","../../", 1, 1, '','','');
echo load_html_head_contents("Roll wise Grey Sales Order To Sales Order Transfer Entry","../../", 1, 1, $unicode,'','');
?>
<script>
	var permission='<? echo $permission; ?>';
	var user_level='<? echo $user_level; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var to_item_arr=new Array(); var scanned_barcode=new Array(); var table_barcode_no_arr=new Array();

	var tableFilters =
	{
		col_0: "none",
		col_operation: {
		id: ["total_roll_wgt_show"],
		col: [18],
		operation: ["sum"],
		write_method: ["innerHTML"],
		}
	}

	function openmypage_systemId()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

		if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
		{
			return;
		}

		var title = 'Item Transfer Info';
		var page_link = 'requires/grey_sales_order_to_order_roll_trans_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=orderToorderTransfer_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=380px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"

			fnc_reset_form();

			get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/grey_sales_order_to_order_roll_trans_controller" );
			if (cbo_transfer_criteria==1)
			{
				var company_id = $('#cbo_to_company_id').val();
			}
			else
			{
				var company_id = $('#cbo_company_id').val();
			}
			//show_list_view(transfer_id+"**"+$('#txt_from_order_id').val()+"**"+company_id+"**"+$('#cbo_store_name').val()+"**"+$('#txt_to_order_id').val()+"**"+$('#cbo_transfer_criteria').val(),'show_transfer_listview','tbl_details','requires/grey_sales_order_to_order_roll_trans_controller','');

			/*var row_num=$('#scanning_tbl tbody tr').length;
			var total_roll_wgt = 0;selected_roll_wgt=0;
			for (var j=1; j<=row_num; j++)
			{
				total_roll_wgt += $('#rollWgt_'+j).val()*1;

				if($('#tr__'+j).is(':checked'))
				{
					selected_roll_wgt += $('#rollWgt_'+j).val()*1;
				}
			}
			$("#total_roll_wgt_show").text(total_roll_wgt.toFixed(2));
			$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));*/


			// setFilterGrid('scanning_tbl',-1);

			var com_id=$('#cbo_to_company_id').val();
			var store_id=$('#cbo_store_name').val();
			var all_data=com_id + "__" + store_id;
				//alert(all_data);return;
			var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
			var JSONObject = JSON.parse(floor_result);

			var floor_id = Object.keys(JSONObject);
			var all_floor_id = floor_id.join(",");
			if(all_floor_id==""){all_floor_id=0;}
			if(all_floor_id!=0)
			{
				// alert(all_floor_id);
				var all_data2=com_id + "__" + store_id + "__" + all_floor_id;
				var room_result = return_global_ajax_value(all_data2, 'room_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');

				var JSONObjectRoom = JSON.parse(room_result);
				var room_id = Object.keys(JSONObjectRoom);
				var all_room_id = room_id.join(",");
				//alert(all_floor_id+'=='+all_room_id);
				// var all_data3=com_id + "__" + store_id + "__" + all_floor_id + "__" + all_room_id;
				var all_data3=com_id + "__" + store_id + "__" + all_room_id;
				//alert(all_data3);
				var rack_result = return_global_ajax_value(all_data3, 'rack_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
				var JSONObjectRack = JSON.parse(rack_result);

				var rack_id = Object.keys(JSONObjectRack);
				var all_rack_id = rack_id.join(",");
				// var all_data4=com_id + "__" + store_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id;
				var all_data4=com_id + "__" + store_id + "__" + all_rack_id;
				var self_result = return_global_ajax_value(all_data4, 'shelf_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
				var tbl_length=$('#scanning_tbl tbody tr').length;
				//alert(floor_result+"="+tbl_length);//return;
				var JSONObjectSelf = JSON.parse(self_result);

				var shelf_id = Object.keys(JSONObjectSelf);
				var all_shelf_id = shelf_id.join(",");
				// var all_data5=com_id + "__" + store_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id+ "__" + all_shelf_id;
				var all_data5=com_id + "__" + store_id + "__" + all_shelf_id;
				var bin_result = return_global_ajax_value(all_data5, 'bin_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
				var tbl_length=$('#scanning_tbl tbody tr').length;
				var JSONObjectBin = JSON.parse(bin_result);
			}
			else
			{
				var JSONObjectRoom = JSON.parse(0);
				var JSONObjectRack = JSON.parse(0);
				var JSONObjectSelf = JSON.parse(0);
				var JSONObjectBin = JSON.parse(0);
			}
			for(var i=1; i<=tbl_length; i++)
			{
				$('#cbo_floor_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject))
				{
					$('#cbo_floor_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};

				$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObjectRoom))
				{
					$('#cbo_room_to_'+i).append('<option value="'+key+'">'+JSONObjectRoom[key]+'</option>');
				};

				$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObjectRack))
				{
					$('#txt_rack_to_'+i).append('<option value="'+key+'">'+JSONObjectRack[key]+'</option>');
				};

				$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObjectSelf))
				{
					$('#txt_shelf_to_'+i).append('<option value="'+key+'">'+JSONObjectSelf[key]+'</option>');
				};

				$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObjectBin))
				{
					$('#txt_bin_to_'+i).append('<option value="'+key+'">'+JSONObjectBin[key]+'</option>');
				};
			}

			create_row(1,transfer_id);
			disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no*cbo_from_store_name', 1, '', '' );
		}
	}

	function openmypage_orderNo(type)
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		if (cbo_transfer_criteria==1 && type=="to") // company to company
		{
			if (form_validation('cbo_to_company_id','To Company')==false)
			{
				return;
			}
			cbo_company_id=$('#cbo_to_company_id').val();
		}
		else
		{
			cbo_company_id=$('#cbo_company_id').val();
		}

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		if(type=="to")
		{
			if( $("#txt_from_order_no").val() == "")
			{
				if (form_validation('txt_from_order_no','From Order')==false)
				{
					return;
				}
			}
		}
		var txt_from_order_id = $("#txt_from_order_id").val();
		var title = 'Order Info';
		var page_link = 'requires/grey_sales_order_to_order_roll_trans_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&txt_from_order_id='+txt_from_order_id+'&action=order_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"

			var theform=this.contentDoc.forms[0];
			var order_ref=this.contentDoc.getElementById("order_id").value.split("_");
			// alert(order_ref);
			var order_id=order_ref[0];
			var desc_str=order_ref[1];

			if(type=='to')
			{
				var order_id=order_ref[0];
				var desc_str=order_ref[1];
				// $('#desc_str').val(desc_str);
			}

			get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/grey_sales_order_to_order_roll_trans_controller" );
			if(type=='from')
			{
				show_list_view(order_id,'show_dtls_list_view','tbl_details','requires/grey_sales_order_to_order_roll_trans_controller','');

				var row_num=$('#scanning_tbl tbody tr').length;
				var total_roll_wgt = 0;
				for (var j=1; j<=row_num; j++)
				{
					total_roll_wgt += $('#rollWgt_'+j).val()*1;
				}
				$("#total_roll_wgt_show").text(total_roll_wgt.toFixed(2));

				setFilterGrid('scanning_tbl',-1);
			}
		}
	}

	function openmypage_toColor()
	{
		var cbo_to_company_id = $('#cbo_to_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var txt_to_order_id = $('#txt_to_order_id').val();
		var txt_to_order_no = $('#txt_to_order_no').val();

		if (form_validation('cbo_to_company_id*txt_to_order_no','Company*To Order')==false)
		{
			return;
		}

		var title = 'Order Info';
		var page_link = 'requires/grey_sales_order_to_order_roll_trans_controller.php?cbo_to_company_id='+cbo_to_company_id+'&txt_to_order_id='+txt_to_order_id+'&action=to_color_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=300px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var color_str_data=this.contentDoc.getElementById("color_str_ref").value.split("_");
			//alert(color_str_data[0]);return;
			var color_id=color_str_data[0];
			var color_name=color_str_data[1];
			$('#hid_to_color_id').val(color_id);
			$('#txt_to_color_no').val(color_name);
		}
	}

	function fnc_grey_transfer_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print", "requires/grey_sales_order_to_order_roll_trans_controller" )
			return;
		}
		else if(operation==5)
		{
			if($('#update_id').val()=="")
			{
				alert("Please Select Save Data First....");
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print_2", "requires/grey_sales_order_to_order_roll_trans_controller" )
			return;
		}
		else if(operation==6)
		{
			if($('#update_id').val()=="")
			{
				alert("Please Select Save Data First....");
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print_3", "requires/grey_sales_order_to_order_roll_trans_controller" )
			return;
		}
		else if(operation==7)
		{
			if($('#update_id').val()=="")
			{
				alert("Please Select Save Data First....");
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print_4", "requires/grey_sales_order_to_order_roll_trans_controller" )
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}

			if( form_validation('cbo_transfer_criteria*cbo_company_id*cbo_to_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no*cbo_store_name','Transfer Criteria*Company*To Company*Transfer Date*From Order No*To Order No*To Store Name')==false )
			{
				return;
			}

            var current_date='<? echo date("d-m-Y"); ?>';
            if(date_compare($('#txt_transfer_date').val(), current_date)==false)
            {
                alert("Transfer Date Can not Be Greater Than Current Date");
                return;
            }

			var txt_deleted_id=''; var selected_row=0; var i=0; var data_all='';  var txt_deleted_prod_qty ='';

			var store_update_upto=$('#store_update_upto').val()*1;
			var row_num=$('#scanning_tbl tbody tr').length-1;
			// for (var j=1; j<=row_num; j++)
			var j=0;
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				if($(this).find('input[name="txtcheck[]"]').is(':not(:checked)'))
				{
					var updateIdDtls=$(this).find('input[name="dtlsId[]"]').val();
					// alert(updateIdDtls+'=next');
					if (updateIdDtls!="")
					{
						/*var transIdFrom=$('#transIdFrom_'+j).val();
						var transIdTo=$('#transIdTo_'+j).val();
						var rolltableId=$('#rolltableId_'+j).val();
						var rollId=$('#rollId_'+j).val();
						var delBarcodeNo=$('#barcodeNo_'+j).val();
						var productId=$('#productId_'+j).val();
						var toProductUp=$('#toProductUp_'+j).val();
						var rollWgt=$('#rollWgt_'+j).val();
						var delete_amount = $('#rollRate_'+j).val()*rollWgt;*/

						var transIdFrom=$(this).find('input[name="transIdFrom[]"]').val();
						var transIdTo=$(this).find('input[name="transIdTo[]"]').val();
						var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
						var rollId=$(this).find('input[name="rollId[]"]').val();
						var delBarcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
						var productId=$(this).find('input[name="productId[]"]').val();
						var toProductUp=$(this).find('input[name="toProductUp[]"]').val();
						var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
						var rollRate=$(this).find('input[name="rollRate[]"]').val();
						var delete_amount = rollRate*rollWgt;

						//selected_row++;
						if(txt_deleted_id=="") txt_deleted_id=updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;
						else txt_deleted_id+=","+updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;

						if(txt_deleted_prod_qty=='') txt_deleted_prod_qty=toProductUp+"_"+rollWgt+"_"+productId+"_"+delete_amount;
						else txt_deleted_prod_qty =txt_deleted_prod_qty+","+toProductUp+"_"+rollWgt+"_"+productId+"_"+delete_amount;
					}
				}

				// if($('#tr__'+j).is(':checked'))
				if($(this).find('input[name="txtcheck[]"]').attr('checked'))
				{
					if(store_update_upto > 1)
					{
						var cbo_floor_to=$(this).find('select[name="cbo_floor_to[]"]').val();
						var cbo_room_to=$(this).find('select[name="cbo_room_to[]"]').val();
						var txt_rack_to=$(this).find('select[name="txt_rack_to[]"]').val();
						var txt_shelf_to=$(this).find('select[name="txt_shelf_to[]"]').val();
						var txt_bin_to=$(this).find('select[name="txt_bin_to[]"]').val();

						if(store_update_upto==5 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0))
						{
							alert("Up To Shelf Value Full Fill Required For Inventory");return;
						}
						else if(store_update_upto==4 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0))
						{
							alert("Up To Rack Value Full Fill Required For Inventory");return;
						}
						else if(store_update_upto==3 && (cbo_floor_to==0 || cbo_room_to==0))
						{
							alert("Up To Room Value Full Fill Required For Inventory");return;
						}
						else if(store_update_upto==2 && cbo_floor_to==0)
						{
							alert("Up To Floor Value Full Fill Required For Inventory");return;
						}
					}

					var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
					var rollNo=$(this).find('input[name="rollNo[]"]').val();
					var progId=$(this).find('input[name="progId[]"]').val();
					var productId=$(this).find('input[name="productId[]"]').val();
					var rollId=$(this).find('input[name="rollId[]"]').val();
					var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
					var yarnLot=$(this).find('input[name="yarnLot[]"]').val();
					var colorName=$(this).find('input[name="colorName[]"]').val();
					var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
					var stichLn=$(this).find('input[name="stichLn[]"]').val();
					var machineDia=$(this).find('input[name="machineDia[]"]').val();
					var brandId=$(this).find('input[name="brandId[]"]').val();
					var floor=$(this).find('input[name="floor[]"]').val();
					var room=$(this).find('input[name="room[]"]').val();
					var rack=$(this).find('input[name="rack[]"]').val();
					var shelf=$(this).find('input[name="shelf[]"]').val();
					var binbox=$(this).find('input[name="binbox[]"]').val();
					var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
					var transIdFrom=$(this).find('input[name="transIdFrom[]"]').val();
					var transIdTo=$(this).find('input[name="transIdTo[]"]').val();
					var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
					var transRollId=$(this).find('input[name="transRollId[]"]').val();
					var storeId=$(this).find('input[name="storeId[]"]').val();
					var requiDtlsId=$(this).find('input[name="requiDtlsId[]"]').val();
					var cbo_floor_to=$(this).find('select[name="cbo_floor_to[]"]').val();
					var cbo_room_to=$(this).find('select[name="cbo_room_to[]"]').val();
					var txt_rack_to=$(this).find('select[name="txt_rack_to[]"]').val();
					var txt_shelf_to=$(this).find('select[name="txt_shelf_to[]"]').val();
					var txt_bin_to=$(this).find('select[name="txt_bin_to[]"]').val();
					var frombodypartId=$(this).find('input[name="frombodypartId[]"]').val();
					var cboToBodyPart=$(this).find('select[name="cboToBodyPart[]"]').val();
					var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
					var ItemDesc=$(this).find('input[name="ItemDesc[]"]').val();
					var rollRate=$(this).find('input[name="rollRate[]"]').val();
					var ItemDtls=$(this).find('input[name="ItemDtls[]"]').val();
					var rollAmount=$(this).find('input[name="rollAmount[]"]').val();
					var toProductUp=$(this).find('input[name="toProductUp[]"]').val();

					i++;
					data_all+="&barcodeNo_" + i + "='" + barcodeNo+"'"+"&rollNo_" + i + "='" + rollNo+"'"+"&progId_" + i + "='" + progId+"'"+"&productId_" + i + "='" + productId+"'"+"&rollId_" + i + "='" + rollId+"'"+"&rollWgt_" + i + "='" + rollWgt+"'"+"&yarnLot_" + i + "='" + yarnLot+"'" + "&colorName_" + i + "='" + colorName+"'"+"&yarnCount_" + i + "='" + yarnCount+"'"+"&stichLn_" + i + "='" + stichLn+"'"+"&brandId_" + i + "='" + brandId+"'"+"&floor_" + i + "='" + floor+"'"+"&room_" + i + "='" + room+"'"+"&rack_" + i + "='" + rack+"'"+"&shelf_" + i + "='" + shelf+"'"+"&binbox_" + i + "='" + binbox+"'"+"&dtlsId_" + i + "='" + dtlsId+"'"+"&transIdFrom_" + i + "='" + transIdFrom+"'"+"&transIdTo_" + i + "='" + transIdTo+"'"+"&rolltableId_" + i + "='" + rolltableId+"'"+"&transRollId_" + i + "='" + transRollId+"'"+"&storeId_" + i + "='" + storeId+"'"+"&requiDtlsId_" + i + "='" + requiDtlsId+"'"+"&cbo_floor_to_" + i + "='" + cbo_floor_to+"'"+"&cbo_room_to_" + i + "='" + cbo_room_to+"'"+"&txt_rack_to_" + i + "='" + txt_rack_to+"'"+"&txt_shelf_to_" + i + "='" + txt_shelf_to+"'"+"&txt_bin_to_" + i + "='" + txt_bin_to+"'"+"&frombodypartId_" + i + "='" + frombodypartId+"'"+"&cbo_To_BodyPart_" + i + "='" + cboToBodyPart+"'"+"&txtRemarks_" + i + "='" + txtRemarks+"'"+"&ItemDesc_" + i + "='" + ItemDesc+"'"+"&rollRate_" + i + "='" + rollRate+"'"+"&ItemDtls_" + i + "='" + ItemDtls+"'"+"&rollAmount_" + i + "='" + rollAmount+"'"+"&toProductUp_" + i + "='" + toProductUp+"'";

					selected_row++;
				}
			});
			// alert(selected_row);
			if(selected_row<1)
			{
				alert("Please Select Barcode No.");
				return;
			}

			var jk=1;
			for (var jk; i>=jk; jk++)
			{
				if ($('#cbo_To_BodyPart_'+jk).val()==0)
				{
					alert('Please Select Body Part');
					$('#cbo_To_BodyPart_'+jk).focus();
					return;
				}
			}

			var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*update_id*txt_requisition_no*txt_requisition_id*cbo_complete_status*requisition_and_order_basis*cbo_from_store_name*cbo_store_name*cbo_transfer_criteria*cbo_to_company_id*cbo_purpose_id*hid_to_color_id*txt_driver_name*txt_mobile_no*txt_vehicle_no*txt_remark";
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+'&total_row='+i+'&txt_deleted_id='+txt_deleted_id+'&txt_deleted_prod_qty='+txt_deleted_prod_qty+data_all;

			//alert(data);//return;
			freeze_window(operation);
			http.open("POST","requires/grey_sales_order_to_order_roll_trans_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_grey_transfer_entry_reponse;
		}
	}

	function fnc_grey_transfer_entry_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			//alert(http.responseText);release_freezing();return;
            if (reponse[0] * 1 == 20 * 1)
            {
                alert(reponse[1]);
                release_freezing();
                return;
            }
			show_msg(reponse[0]);

			if(reponse[0]==0 || reponse[0]==1)
			{
				fnc_reset_form();

				$("#update_id").val(reponse[1]);
				$("#txt_system_id").val(reponse[2]);
				get_php_form_data(reponse[1], "populate_data_from_transfer_master", "requires/grey_sales_order_to_order_roll_trans_controller" );

				var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
				if (cbo_transfer_criteria==1)
				{
					var company_id = $('#cbo_to_company_id').val();
				}
				else
				{
					var company_id = $('#cbo_company_id').val();
				}
				//show_list_view(reponse[1]+"**"+$('#txt_from_order_id').val()+"**"+company_id+"**"+$('#cbo_store_name').val()+"**"+$('#txt_to_order_id').val()+"**"+$('#cbo_transfer_criteria').val(),'show_transfer_listview','tbl_details','requires/grey_sales_order_to_order_roll_trans_controller','');

				var com_id=$('#cbo_to_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var all_data=com_id + "__" + store_id;
					//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
				var JSONObject = JSON.parse(floor_result);

				var floor_id = Object.keys(JSONObject);
				var all_floor_id = floor_id.join(",");
				if(all_floor_id==""){all_floor_id=0;}
				if(all_floor_id!=0)
				{
					// alert(all_floor_id);
					var all_data2=com_id + "__" + store_id + "__" + all_floor_id;
					var room_result = return_global_ajax_value(all_data2, 'room_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');

					var JSONObjectRoom = JSON.parse(room_result);
					var room_id = Object.keys(JSONObjectRoom);
					var all_room_id = room_id.join(",");
					//alert(all_floor_id+'=='+all_room_id);
					// var all_data3=com_id + "__" + store_id + "__" + all_floor_id + "__" + all_room_id;
					var all_data3=com_id + "__" + store_id + "__" + all_room_id;
					//alert(all_data3);
					var rack_result = return_global_ajax_value(all_data3, 'rack_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
					var JSONObjectRack = JSON.parse(rack_result);

					var rack_id = Object.keys(JSONObjectRack);
					var all_rack_id = rack_id.join(",");
					// var all_data4=com_id + "__" + store_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id;
					var all_data4=com_id + "__" + store_id + "__" + all_rack_id;
					var self_result = return_global_ajax_value(all_data4, 'shelf_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
					var tbl_length=$('#scanning_tbl tbody tr').length;
					//alert(floor_result+"="+tbl_length);//return;
					var JSONObjectSelf = JSON.parse(self_result);

					var shelf_id = Object.keys(JSONObjectSelf);
					var all_shelf_id = shelf_id.join(",");
					// var all_data5=com_id + "__" + store_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id+ "__" + all_shelf_id;
					var all_data5=com_id + "__" + store_id + "__" + all_shelf_id;
					var bin_result = return_global_ajax_value(all_data5, 'bin_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
					var tbl_length=$('#scanning_tbl tbody tr').length;
					var JSONObjectBin = JSON.parse(bin_result);
				}
				else
				{
					var JSONObjectRoom = JSON.parse(0);
					var JSONObjectRack = JSON.parse(0);
					var JSONObjectSelf = JSON.parse(0);
					var JSONObjectBin = JSON.parse(0);
				}
				for(var i=1; i<=tbl_length; i++)
				{
					$('#cbo_floor_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject))
					{
						$('#cbo_floor_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};

					$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObjectRoom))
					{
						$('#cbo_room_to_'+i).append('<option value="'+key+'">'+JSONObjectRoom[key]+'</option>');
					};

					$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObjectRack))
					{
						$('#txt_rack_to_'+i).append('<option value="'+key+'">'+JSONObjectRack[key]+'</option>');
					};

					$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObjectSelf))
					{
						$('#txt_shelf_to_'+i).append('<option value="'+key+'">'+JSONObjectSelf[key]+'</option>');
					};

					$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObjectBin))
					{
						$('#txt_bin_to_'+i).append('<option value="'+key+'">'+JSONObjectBin[key]+'</option>');
					};
				}

				create_row(1,reponse[1]);

				/*var row_num=$('#scanning_tbl tbody tr').length;
				var total_roll_wgt = 0;selected_roll_wgt=0;
				for (var j=1; j<=row_num; j++)
				{
					total_roll_wgt += $('#rollWgt_'+j).val()*1;

					if($('#txtcheck_'+j).is(':checked'))
					{
						selected_roll_wgt += $('#rollWgt_'+j).val()*1;
					}
				}
				$("#total_roll_wgt_show").text(total_roll_wgt.toFixed(2));
				$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));*/


				set_button_status(1, permission, 'fnc_grey_transfer_entry',1,1);
				disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_to_company_id*txt_from_order_no*txt_to_order_no*txt_requisition_no*cbo_from_store_name', 1, '', '' );
			}
			release_freezing();
		}
	}

	function openmypage_orderInfo(type)
	{
		var txt_order_no = $('#txt_'+type+'_order_no').val();
		var txt_order_id = $('#txt_'+type+'_order_id').val();

		if (form_validation('txt_'+type+'_order_no','Order No')==false)
		{
			alert("Please Select Order No.");
			return;
		}

		var title = 'Order Info';
		var page_link = 'requires/grey_sales_order_to_order_roll_trans_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
	}

	function check_all(tot_check_box_id)
	{
		var to_order_id=$("#txt_to_order_id").val();
		if (to_order_id=="")
		{
			alert('To Sales Order Select First');
			$('#'+tot_check_box_id).prop("checked", false);return;
		}

		var to_item=$("#desc_str").val();
		var to_item_arr=to_item.split(",");


		if ($('#'+tot_check_box_id).is(":checked"))
		{
			var j=1;
			$('#scanning_tbl tbody tr').each(function()
			{
				if (j!=1)
				{
					//$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
					if($(this).css('display') == 'none')
					{
						$(this).find('input[name="txtcheck[]"]').attr('checked', false);
					}
					else
					{
						$(this).find('input[name="txtcheck[]"]').attr('checked', true);
					}

					var from_item=$(this).find('input[name="ItemDesc[]"]').val();
					if (user_level==2) // User managment > User Level = Admin User
					{
						var from_item_arr=from_item.split("*");
						// alert(from_item_arr[0]+'='+to_item_arr+'==');
						if( $.inArray( from_item_arr[0], to_item_arr ) > -1 )
						{
							// alert('Found');
							/*$('#tbl_'+str).prop("checked", false);
							return;*/
						}
						else
						{
							alert('Item Not Found In To Order');
							$('#'+tot_check_box_id).prop("checked", false);
							$(this).find('input[name="txtcheck[]"]').attr('checked', false);return;
						}
					}
					else
					{
						// alert(from_item+'='+to_item_arr);
						if( $.inArray( from_item, to_item_arr ) > -1 )
						{
							// alert('Found');
							/*$('#tbl_'+str).prop("checked", false);
							return;*/
						}
						else
						{
							alert('Item Not Found In To Order');
							$('#'+tot_check_box_id).prop("checked", false);
							$(this).find('input[name="txtcheck[]"]').attr('checked', false);return;
						}
					}
				}

				j++;

			});
		}
		else
		{
			/*$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});*/

			if($('#txt_check_all').attr('checked'))
			{
				$('#scanning_tbl tbody').find('tr').each(function(index, element) {
					$('input:checkbox:not(:disabled)').attr('checked','checked');
	            });
			}
			else
			{
				$('#scanning_tbl tbody').find('tr').each(function(index, element) {
					$('input:checkbox:not(:disabled)').removeAttr('checked');
	            });
			}
		}


		var row_num=$('#scanning_tbl tbody tr').length;
		var selected_roll_wgt=0;
		for (var j=1; j<=row_num; j++)
		{

			if($('#txtcheck_'+j).is(':checked'))
			{
				selected_roll_wgt += $('#rollWgt_'+j).val()*1;
			}
		}
		$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));
	}

	function reset_form_all()
	{
		disable_enable_fields('cbo_company_id*txt_from_order_no*txt_to_order_no',0);
		reset_form('transferEntry_1','tbl_details','','','');
	}

	function show_selected_total(str)
	{
		// alert(str);
		var to_order_id=$("#txt_to_order_id").val();
		if (to_order_id=="")
		{
			alert('To Sales Order Select First');
			$('#txtcheck_'+str).prop("checked", false);return;
		}

		var from_item=$("#ItemDesc_"+str).val();
		// alert(from_item);
		var to_item=$("#desc_str").val();
		// var to_item_array = new Array(to_item);
		// var to_item=to_item.split(",");
		// alert(to_item.join('='));
		// 6*180*62=21*220*72=24*200*55=31*140*66
		// var from_item=6*180*62;
		// alert(to_item.length);
		/*for (var i = 0; i < to_item.length; i++)
		{
			alert(to_item[i]+'='+from_item);
			if(to_item[i] != from_item)
			{
				alert('Item Missmatch');
				$('#tbl_'+str).prop("checked", false);//return;
			}
		}*/

		if (user_level==2) // User managment > User Level = Admin User
		{
			var from_item_arr=from_item.split("*");
			var to_item_arr=to_item.split(",");
			// alert(from_item_arr[0]+'='+to_item_arr);
			if( $.inArray( from_item_arr[0], to_item_arr ) > -1 )
			{
				// alert('Found');
				/*$('#tbl_'+str).prop("checked", false);
				return;*/
			}
			else
			{
				alert('Item Not Found In To Order');
				$('#txtcheck_'+str).prop("checked", false);return;
			}
		}
		else
		{
			var to_item_arr=to_item.split(",");
			// alert(from_item+'='+to_item_arr);
			if( $.inArray( from_item, to_item_arr ) > -1 )
			{
				// alert('Found');
				/*$('#tbl_'+str).prop("checked", false);
				return;*/
			}
			else
			{
				alert('Item Not Found In To Order');
				$('#tr__'+str).prop("checked", false);return;
			}
		}


		var roll_wgt=0; var pre_wgt = 0;
		roll_wgt =$('#rollWgt_'+str).val()*1;
		pre_wgt = $("#selected_roll_wgt_show").text()*1;
		if($('#txtcheck_'+str).is(":checked"))
		{
			$("#selected_roll_wgt_show").text(pre_wgt+roll_wgt);
		}
		else
		{
			$("#selected_roll_wgt_show").text(pre_wgt-roll_wgt);
		}
		var total_roll_wgt = $("#selected_roll_wgt_show").text()*1
		var requi_qty = $("#hidd_requi_qty").val()*1
		// alert(requi_qty+'<='+total_roll_wgt);
		if (requi_qty<=total_roll_wgt)
		{
			$("#cbo_complete_status").val(2);
		}
		else{
			$("#cbo_complete_status").val(1);
		}
	}

	function fnc_company_onchang_reset(company)
	{
		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			$("#cbo_company_id").val(0);
			return;
		}

		if($("#cbo_transfer_criteria").val() != 1)
		{
			$("#cbo_to_company_id").val(company);
			load_drop_down( 'requires/grey_sales_order_to_order_roll_trans_controller',$('#cbo_transfer_criteria').val()+'_'+company, 'load_drop_store_to', 'store_td' );
		}
		else
		{
			if($("#cbo_company_id").val() == $("#cbo_to_company_id").val())
			{
				$("#cbo_to_company_id").val(0);
			}
		}

		page_link = 'cbo_company_id='+company+'&action=requ_variable_settings';

		$.ajax({
			url: 'requires/grey_sales_order_to_order_roll_trans_controller.php',
			type: 'POST',
			data: page_link,
			success: function (response)
			{
				var variable_settings = response.split("**");
				//alert(variable_settings[0]+'='+variable_settings[1]);
				$('#requisition_and_order_basis').val(variable_settings[2]);
				$('#requisition_basis').val(variable_settings[0]);
				if (variable_settings[0]==1) // Requisition Basis
				{
					$('#txt_requisition_no').attr('disabled',false);
					$('#txt_from_order_no').attr('disabled','disabled');
					$('#txt_to_order_no').attr('disabled','disabled');
					// $('#txt_bar_code_num').attr('disabled',true);
				}
				else
				{

					// $('#txt_bar_code_num').attr('disabled',false);

					$('#txt_requisition_no').attr('disabled','disabled');

					if ($("#cbo_transfer_criteria").val()==2) // Store to Store
					{
						$('#txt_to_order_no').attr('disabled','disabled');
						$('#txt_to_order_no').val('');
						$('#txt_to_order_id').val(0);
					}
					else
					{
						$('#txt_to_order_no').attr('disabled',false);
						$('#txt_to_order_no').val('');
						$('#txt_to_order_id').val(0);
					}
					//$('#txt_from_order_no').attr('disabled',false);
					//$('#txt_to_order_no').attr('disabled',false);
				}
				/*if(variable_settings[1]==2)
				{
					$('#store_update_upto').val(variable_settings[1]);
				}*/
				$('#store_update_upto').val(variable_settings[1]);
				if(variable_settings[2]==1) // 1(Yes) For Urmi Requrment
				{
					$('#hidd_requi_qty').show();
					$('#hidd_requi_qty_td').show();
					$('#cbo_complete_status_td').show();
					$('#complite_status_td').show();
					$('#roll_purpose_td').show();
					$('#roll_purpose_tds').show();
				}
				else
				{
					$('#hidd_requi_qty').hide();
					$('#hidd_requi_qty_td').hide();
					$('#cbo_complete_status_td').hide();
					$('#complite_status_td').hide();
					$('#roll_purpose_td').hide();
					$('#roll_purpose_tds').hide();
				}
			}
		});

		$('#txt_from_order_no').val('');
		$('#txt_from_order_id').val('');
		$('#txt_from_booking_no').val('');
		$('#cbo_from_company').val('');
		$('#cbo_from_buyer_name').val('');
		$('#txt_from_style_ref').val('');
		$('#txt_from_gmts_item').val('');

		$('#txt_to_order_no').val('');
		$('#txt_to_order_id').val('');
		$('#txt_to_booking_no').val('');
		$('#cbo_to_company').val('');
		$('#cbo_to_buyer_name').val('');
		$('#txt_to_style_ref').val('');
		$('#txt_to_gmts_item').val('');

		$("#tbl_details").html("");
	}

	function openmypage_requisition_no()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var requisition_order_basis = $('#requisition_and_order_basis').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}

		var title = 'Item Transfer Info';
		var page_link = 'requires/grey_sales_order_to_order_roll_trans_controller.php?cbo_company_id='+cbo_company_id+'&requisition_order_basis='+requisition_order_basis+'&action=orderToorderTransferRequisition_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=380px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"

			get_php_form_data(transfer_id, "populate_data_from_transfer_requi_master", "requires/grey_sales_order_to_order_roll_trans_controller" );
			var requisition_and_order_basis = $('#requisition_and_order_basis').val();
			if (requisition_and_order_basis==1) // 1 (yes) for urmi
			{
				// Data will come from order
				//show_list_view($('#txt_from_order_id').val(),'show_dtls_list_view','tbl_details','requires/grey_sales_order_to_order_roll_trans_controller','');

				/*var from_order_id = $('#txt_from_order_id').val();
				if(trim(transfer_id)!="" && from_order_id!="")
				{
					create_row(2,from_order_id);
				}*/
			}

			var row_num=$('#scanning_tbl tbody tr').length;
			var total_roll_wgt = 0;selected_roll_wgt=0;
			for (var j=1; j<=row_num; j++)
			{
				total_roll_wgt += $('#rollWgt_'+j).val()*1;

				if($('#txtcheck_'+j).is(':checked'))
				{
					selected_roll_wgt += $('#rollWgt_'+j).val()*1;
				}
			}
			$("#total_roll_wgt_show").text(total_roll_wgt.toFixed(2));
			$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));


			// setFilterGrid('scanning_tbl',-1);
			disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
		}
	}

	function company_on_change(company)
	{
		load_drop_down( 'requires/grey_sales_order_to_order_roll_trans_controller',$('#cbo_transfer_criteria').val()+'_'+company, 'load_drop_store_from', 'from_store_td' );
		fnc_reset_form();
		setFilterGrid("scanning_tbl",-1,tableFilters);
		$('#selected_roll_wgt_show').text(0);
	}

	function fn_load_floor(store_id)
	{
		var com_id=$('#cbo_company_id').val();
		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if (transfer_criteria==1) // company to company
		{
			var com_id=$('#cbo_to_company_id').val();
		}
		var all_data=com_id + "__" + store_id;
		//alert(all_data);//return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(floor_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(floor_result);
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cbo_floor_to_'+i).html('<option value="'+0+'">--Select--</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				//alert(Object.keys(JSONObject));
				$('#cbo_floor_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var store_id=$('#cbo_store_name').val();
		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if (transfer_criteria==1) // company to company
		{
			var com_id=$('#cbo_to_company_id').val();
		}
		var all_data=com_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(room_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(room_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#cbo_room_to_'+i).html('<option value="'+0+'">--Select--</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_room_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if (transfer_criteria==1) // company to company
		{
			var com_id=$('#cbo_to_company_id').val();
		}
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txt_rack_to_'+i).html('<option value="'+0+'">--Select--</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_rack_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if (transfer_criteria==1) // company to company
		{
			var com_id=$('#cbo_to_company_id').val();
		}
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txt_shelf_to_'+i).html('<option value="'+0+'">--Select--</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_shelf_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if (transfer_criteria==1) // company to company
		{
			var com_id=$('#cbo_to_company_id').val();
		}
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txt_bin_to_'+i).html('<option value="'+0+'">--Select--</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_bin_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function load_bodypart_list()
	{
		//alert(orderId);
		var com_id=$('#cbo_company_id').val();
		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if (transfer_criteria==1) // company to company
		{
			var com_id=$('#cbo_to_company_id').val();
		}
		var orderId=$('#txt_to_order_id').val();
		var all_data=com_id + "__" + orderId;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bodypart_list', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);
		//alert(JSONObject);//return;
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cbo_To_BodyPart_'+i).html('<option value="'+0+'">--Select--</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_To_BodyPart_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function copyBodyPart(str) // Body Part copy all method
	{
		var data=str.split("_");
		// var trall=$("#txt_tr_length").val();
		var trall=$('#scanning_tbl tbody tr').length-1;
		var copy_tr=parseInt(trall);
		if($('#bodyPartIds').is(':checked'))
		{
			if(data[1]==0) data_value=$("#cbo_To_BodyPart_"+data[0]).val();
		}

		var first_tr=parseInt(data[0])+1;
		for(var k=first_tr; k<=copy_tr; k++)
		{
			if($('#bodyPartIds').is(':checked'))
			{
				if(data[1]==0) 	$("#cbo_To_BodyPart_"+k).val(data_value);
			}

		}
	}

	function copy_all(str) // floor, room, rack, shelf, bin copy all method
	{
		var data=str.split("_");
		// var trall=$("#txt_tr_length").val();
		var trall=$('#scanning_tbl tbody tr').length-1;
		var copy_tr=parseInt(trall);
		if($('#floorIds').is(':checked'))
		{
			if(data[1]==0) data_value=$("#cbo_floor_to_"+data[0]).val();
		}
		if($('#roomIds').is(':checked'))
		{
			if(data[1]==1) data_value=$("#cbo_room_to_"+data[0]).val();
		}
		if($('#rackIds').is(':checked'))
		{
			if(data[1]==2) data_value=$("#txt_rack_to_"+data[0]).val();
		}
		if($('#shelfIds').is(':checked'))
		{
			if(data[1]==3) data_value=$("#txt_shelf_to_"+data[0]).val();
		}
		if($('#binIds').is(':checked'))
		{
			if(data[1]==4) data_value=$("#txt_bin_to_"+data[0]).val();
		}

		var first_tr=parseInt(data[0])+1;
		for(var k=first_tr; k<=copy_tr; k++)
		{
			if($('#floorIds').is(':checked'))
			{
				if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
			}
			if($('#roomIds').is(':checked'))
			{
				if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
			}
			if($('#rackIds').is(':checked'))
			{
				if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
			}
			if($('#shelfIds').is(':checked'))
			{
				if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
			}
			if($('#binIds').is(':checked'))
			{
				if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
			}
		}
	}

	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#scanning_tbl tbody tr').length;
		if (fieldName=="cbo_store_name")
		{
			for (var i = 1;numRow>=i; i++)
			{
				$("#cbo_floor_to_"+i).val('');
				$("#cbo_room_to_"+i).val('');
				$("#txt_rack_to_"+i).val('');
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}
		}
		else if (fieldName=="cbo_floor_to")
		{
			for (var i = id; numRow>=i; i++)
			{
				$("#cbo_room_to_"+i).val('');
				$("#txt_rack_to_"+i).val('');
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}

		}
		else if (fieldName=="cbo_room_to")
		{
			for (var i = id; numRow>=i; i++)
			{
				$("#txt_rack_to_"+i).val('');
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}
		}
		else if (fieldName=="txt_rack_to")
		{
			for (var i = id; numRow>=i; i++)
			{
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}
		}
		else if (fieldName=="txt_shelf_to")
		{
			for (var i = id; numRow>=i; i++)
			{
				$("#txt_bin_to_"+i).val('');
			}
		}
	}

	function active_inactive(str) // Onchange Transfer Criteria
	{
		$('#cbo_to_company_id').val(0);
		$('#cbo_company_id').val(0);
		load_drop_down( 'requires/grey_sales_order_to_order_roll_trans_controller',$('#cbo_transfer_criteria').val()+'_'+str, 'load_drop_from_store_balnk', 'from_store_td' );
		load_drop_down( 'requires/grey_sales_order_to_order_roll_trans_controller',$('#cbo_transfer_criteria').val()+'_'+str, 'load_drop_store_balnk', 'store_td' );
		if(str==1)
		{
			$('#cbo_to_company_id').removeAttr('disabled','disabled');
			$('#cbo_company_id').val(0);
		}
		else
		{
			$('#cbo_to_company_id').attr('disabled','disabled');
			$('#cbo_to_company_id').val(0);
		}

		if (str==2) // Store to Store
		{
			$('#txt_to_order_no').attr('disabled','disabled');
			$('#txt_to_order_no').val('');
			$('#txt_to_order_id').val(0);
		}
		else
		{
			$('#txt_to_order_no').attr('disabled',false);
			$('#txt_to_order_no').val('');
			$('#txt_to_order_id').val(0);
		}

		// fnc_details_row_blank();
		fnc_reset_form();
		setFilterGrid("scanning_tbl",-1,tableFilters);
		$('#selected_roll_wgt_show').text(0);
	}

	function to_company_on_change(to_company)
	{
		if(($("#cbo_company_id").val() == $("#cbo_to_company_id").val()) && $('#cbo_transfer_criteria').val() ==1)
		{
			$("#cbo_to_company_id").val(0);
			return;
		}
		load_drop_down( 'requires/grey_sales_order_to_order_roll_trans_controller',$('#cbo_transfer_criteria').val()+'_'+to_company, 'load_drop_store_to', 'store_td' );
	}

	function openmypage_barcode() // Barcode browse
	{
		var company_id=$('#cbo_company_id').val();
		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
		var cbo_store_name=$('#cbo_from_store_name').val();
		var requisition_id=$('#txt_requisition_id').val()*1;
		var requisition_basis = $('#requisition_basis').val();
		if (requisition_basis==1 && requisition_id=="") 
		{
			alert('Please Select Requisition No');
			$('#txt_requisition_no').focus();
			return;
		}
		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			return;
		}
		else
		{
			if (form_validation('cbo_company_id*cbo_from_store_name','From Company*Store')==false)
			{
				return;
			}
			/*if(cbo_transfer_criteria==2) // Store to store
			{
				if (form_validation('cbo_company_id*cbo_from_store_name','From Company*Store')==false)
				{
					return;
				}
			}
			else
			{
				if (form_validation('cbo_company_id','From Company')==false)
				{
					return;
				}
			}*/
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_sales_order_to_order_roll_trans_controller.php?company_id='+company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_store_name='+cbo_store_name+'&requisition_id='+requisition_id+'&action=barcode_popup','Barcode Popup', 'width=1240px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			if(barcode_nos!="")
			{
				create_row(0,barcode_nos);
			}
		}
	}

	$('#txt_bar_code_num').live('keydown', function(e) { // barcode scan
		if (form_validation('cbo_company_id*cbo_from_store_name','From Company*Store')==false)
		{
			return;
		}
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
		}
	});

	function create_row(is_update,barcode_no)
	{
		var row_num=$('#txt_tot_row').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length;

		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var cbo_from_store_name = $('#cbo_from_store_name').val();
		var requisition_id=$('#txt_requisition_id').val()*1;

		var system_id=$('#update_id').val();
		if(is_update==0) // save
		{
			var j=0;
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var table_barcode=$(this).find('input[name="barcodeNo[]"]').val();
				// alert(table_barcode);
				table_barcode_no_arr.push(table_barcode);
				j++;
			});

			var barcode_array = bar_code.split(',');
			for(var i = 0; i < barcode_array.length; i++)
			{
			   console.log(barcode_array[i]);
			   //alert(barcode_array[i]);
			   if( jQuery.inArray( barcode_array[i], table_barcode_no_arr )>-1)
				{
					alert('Sorry! Barcode Already Scanned.');
					$('#txt_bar_code_num').val('');
					return;
				}
			}

			var barcode_data=return_global_ajax_value(bar_code+"**"+requisition_id, 'show_dtls_list_view', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
			//alert( barcode_data); //return;
			// setFilterGrid('scanning_tbl',-1);
			barcode_data=barcode_data.trim();//Remove space
			//alert( barcode_data); //return;
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			//alert(barcode_data_ref[0]);return;
			var total_roll_wgt=0;
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");

				var order_id=barcode_data_all[1];

				var txt_from_order_no = $('#txt_from_order_no').val();
				if (txt_from_order_no=="")
				{
					get_php_form_data(order_id+"**"+'from', "populate_data_from_order", "requires/grey_sales_order_to_order_roll_trans_controller");
					if (cbo_transfer_criteria==2)
					{
						get_php_form_data(order_id+"**"+'to', "populate_data_from_order", "requires/grey_sales_order_to_order_roll_trans_controller");
					}
				}

				if (barcode_data==999) 
				{
					alert('Barcode Not Found In This Requisition.');return;
				}

				var txt_from_order_id = $('#txt_from_order_id').val();
				// alert(txt_from_order_id+'!='+order_id);
				if (txt_from_order_id!=order_id)
				{
					alert('Multiple Sales Order Not Allowed');return;
				}

				var store_id=barcode_data_all[26];//from store id
				// alert(cbo_from_store_name+'!='+store_id);
				if (cbo_from_store_name!=store_id)
				{
					alert('Multiple Store Not Allowed');return;
				}

				var barcode_no=barcode_data_all[0];
				var po_breakdown_id=barcode_data_all[1];
				var roll_no=barcode_data_all[2];
				var program_no=barcode_data_all[3];
				var prod_id=barcode_data_all[4];
				var fabric_dtls=barcode_data_all[5];
				var ycount=barcode_data_all[6];
				var brand_name=barcode_data_all[7];
				var yarn_lot=barcode_data_all[8];
				var color_names=barcode_data_all[9];
				var stitch_length=barcode_data_all[10];
				var itemDesc=barcode_data_all[11];
				var amount=barcode_data_all[12];
				var roll_rate=barcode_data_all[13];
				var roll_id=barcode_data_all[14];//roll_id_prev
				var qnty=barcode_data_all[15];
				var color_id=barcode_data_all[16];
				var yarn_count_id=barcode_data_all[17];
				var brand_id=barcode_data_all[18];
				var body_part_id=barcode_data_all[19];
				var floor_id=barcode_data_all[20];
				var room_id=barcode_data_all[21];
				var rack=barcode_data_all[22];
				var self=barcode_data_all[23];
				var bin_box=barcode_data_all[24];
				var transRollId=barcode_data_all[25];//roll_id
				var store_id=barcode_data_all[26];
				var remarks=barcode_data_all[27];//transfer requ remarks
				var mc_dia=barcode_data_all[28];
				var requ_dtls_id=barcode_data_all[29];//transfer requ dtls id

				if(barcode_data_all[0]==0)
				{
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return;
				}
				if(barcode_data_all[0]==-1)
				{
					alert('Barcode is Already Receive By Batch');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Already Receive By Batch.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_issue_challan_no').val('');
					return;
				}

				if( jQuery.inArray( bar_code, scanned_barcode )>-1)
				{
					alert('Sorry! Barcode Already Scanned.');
					$('#txt_bar_code_num').val('');
					return;
				}

				var bar_code_no=$('#barcodeNo_'+row_num).val();
				if(bar_code_no!="")
				{
					// alert(row_num+'=ok2');
					row_num++;
					// alert(row_num+'=ok3');
					$("#scanning_tbl tbody tr:last-child").clone().find("td,input,select").each(function()
					{
						// alert('test');
						onchange_value="";
						$(this).attr({
							'id': function(_, id)
							{
							  	var id=id.split("_");

							  	if(id.length>3)
							  	{
							  		var check_id=id[0] +"_"+ id[1] +"_"+ id[2];
							  		// alert(check_id);
							  		if(check_id=="cbo_To_BodyPart")
							  		{
							  			onchange_value="copyBodyPart(\'"+row_num+"_0\');";
							  		}
							  		else if(check_id=="cbo_floor_to")
							  		{

							  			onchange_value="fn_load_room(this.value, "+row_num+");copy_all(\'"+row_num+"_0\');reset_room_rack_shelf("+row_num+",\'cbo_floor_to\')";
							  		}
							  		else if(check_id=="cbo_room_to")
							  		{
							  			onchange_value="fn_load_rack(this.value, "+row_num+");copy_all(\'"+row_num+"_1\');reset_room_rack_shelf("+row_num+",\'cbo_room_to\')";
							  		}
							  		else if(check_id=="txt_rack_to")
							  		{
							  			onchange_value="fn_load_shelf(this.value, "+row_num+");copy_all(\'"+row_num+"_2\');reset_room_rack_shelf("+row_num+",\'txt_rack_to\')";
							  		}
							  		else if(check_id=="txt_shelf_to")
							  		{
							  			onchange_value="fn_load_bin(this.value, "+row_num+");copy_all(\'"+row_num+"_3\');reset_room_rack_shelf("+row_num+",\'txt_shelf_to\')";
							  		}
							  		else if(check_id=="txt_bin_to")
							  		{
							  			onchange_value="copy_all(\'"+row_num+"_4\')";
							  		}
							  		return id[0] +"_"+ id[1] +"_"+ id[2] +"_"+ row_num
							  	}
							  	if(id.length>2){
							  		return id[0] +"_"+ id[1] +"_"+ id[2]
							  	}
							  	else{
							  		return id[0] +"_"+ row_num
							  	}
						 	},
						  'value': function(_, value) { return value }/*,
						  'class': function(){
						  	return "combo_boxes onchange_void"
						  }*/,
						  'onchange': function(){
						  	return onchange_value;
						  }
						});
					}).end().appendTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:last-child").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:last-child").removeAttr('id').attr('id','tr__'+row_num);//decrease_1
					$("#scanning_tbl tbody tr:last-child").find(':input:not(:button)','select').val("");
					$("#scanning_tbl tbody tr:last-child").find(':input(:checkbox)').attr('checked',false).attr('disabled',false);
				}

				scanned_barcode.push(barcode_no);

				// alert(row_num);

				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#txtProgramNo_"+row_num).text(program_no);
				$("#txtProdId_"+row_num).text(prod_id);
				$("#txtFabricDetails_"+row_num).text(fabric_dtls);// contruction_composition_gsm_dia
				$("#txtYcount_"+row_num).text(ycount);
				$("#txtBrandName_"+row_num).text(brand_name);
				$("#txtYarnLot_"+row_num).text(yarn_lot);
				$("#txtColorName_"+row_num).text(color_names);
				$("#cbo_To_BodyPart_"+row_num).val('');
				$("#cbo_floor_to_"+row_num).val('');
				$("#cbo_room_to_"+row_num).val('');
				$("#txt_rack_to_"+row_num).val('');
				$("#txt_shelf_to_"+row_num).val('');
				$("#txt_bin_to_"+row_num).val('');
				$("#txtStitchLength_"+row_num).text(stitch_length);
				$("#txtMachineDia_"+row_num).text(mc_dia);
				$("#txtRollWgt_"+row_num).text(qnty);
				$("#remarks_"+row_num).text(remarks);

				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#rollNo_"+row_num).val(roll_no);
				$("#progId_"+row_num).val(program_no);
				$("#productId_"+row_num).val(prod_id);
				$("#toProductUp_"+row_num).val('');// to product id for update event
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#ItemDesc_"+row_num).val(itemDesc);//$itemDesc=$row[csf('detarmination_id')].'*'.$row[csf('gsm')].'*'.$row[csf('dia_width')];
				$("#ItemDtls_"+row_num).val(fabric_dtls);
				$("#rollAmount_"+row_num).val(amount);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollId_"+row_num).val(roll_id);//roll_id_prev
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#colorName_"+row_num).val(color_id);
				$("#yarnCount_"+row_num).val(yarn_count_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#machineDia_"+row_num).val(mc_dia);
				$("#brandId_"+row_num).val(brand_id);
				$("#frombodypartId_"+row_num).val(body_part_id);
				$("#floor_"+row_num).val(floor_id);
				$("#room_"+row_num).val(room_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#binbox_"+row_num).val(bin_box);
				$("#dtlsId_"+row_num).val();// only for update event
				$("#requiDtlsId_"+row_num).val(requ_dtls_id);// only for update event
				$("#transIdFrom_"+row_num).val();// only for update event
				$("#transIdTo_"+row_num).val();// only for update event
				$("#rolltableId_"+row_num).val();// only for update event
				$("#transRollId_"+row_num).val(transRollId);//current id
				$("#storeId_"+row_num).val(store_id);
				$("#txtRemarks_"+row_num).val(remarks);

				$('#txt_tot_row').val(row_num);

				$('#txtcheck_'+row_num).attr("onClick","show_selected_total("+row_num+");");
			}

			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
		}
		else // update
		{
			var barcode_data=return_global_ajax_value(bar_code+"**"+$('#txt_from_order_id').val()+"**"+$('#cbo_company_id').val()+"**"+$('#cbo_store_name').val()+"**"+$('#txt_to_order_id').val()+"**"+$('#cbo_transfer_criteria').val(), 'show_transfer_listview', '', 'requires/grey_sales_order_to_order_roll_trans_controller');
			barcode_data=barcode_data.trim();//Remove space
			//alert(barcode_data);return;
			setFilterGrid('scanning_tbl',-1,tableFilters);
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			//alert(barcode_data_ref[0]);return;
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");
				var barcode_no=barcode_data_all[0];
				var po_breakdown_id=barcode_data_all[1];
				var roll_no=barcode_data_all[2];
				var program_no=barcode_data_all[3];
				var prod_id=barcode_data_all[4];
				var fabric_dtls=barcode_data_all[5];
				var ycount=barcode_data_all[6];
				var brand_name=barcode_data_all[7];
				var yarn_lot=barcode_data_all[8];
				var color_names=barcode_data_all[9];
				var stitch_length=barcode_data_all[10];
				var itemDesc=barcode_data_all[11];
				var amount=barcode_data_all[12];
				var roll_rate=barcode_data_all[13];
				var roll_id=barcode_data_all[14];//roll_id_prev....
				var qnty=barcode_data_all[15];
				var color_id=barcode_data_all[16];
				var yarn_count_id=barcode_data_all[17];
				var brand_id=barcode_data_all[18];
				var body_part_id=barcode_data_all[19];
				var from_floor=barcode_data_all[20];
				var from_room=barcode_data_all[21];
				var from_rack=barcode_data_all[22];
				var from_self=barcode_data_all[23];
				var from_bin_box=barcode_data_all[24];
				var transRollId=barcode_data_all[25];//roll_id....
				var store_id_from=barcode_data_all[26];
				var remarks=barcode_data_all[27];
				var to_prod_id=barcode_data_all[28];
				var dtls_id=barcode_data_all[29];
				var requ_dtls_id=barcode_data_all[30];
				var from_trans_id=barcode_data_all[31];
				var to_trans_id=barcode_data_all[32];
				var rolltableId=barcode_data_all[33];//...
				var to_body_part=barcode_data_all[34];
				var to_floor_id=barcode_data_all[35];
				var to_room=barcode_data_all[36];
				var to_rack=barcode_data_all[37];
				var to_shelf=barcode_data_all[38];
				var to_bin_box=barcode_data_all[39];
				var checked_flag=barcode_data_all[40];
				var disabled_flag=barcode_data_all[41];
				var mc_dia=barcode_data_all[42];

				//alert('floor= '+floor_id+' Ro= '+room_id+' rack= '+rack_id+' self= '+self_id);

				var bar_code_no=$('#barcodeNo_'+row_num).val();
				if(bar_code_no!="")
				{
					// alert(row_num+'=ok2');
					row_num++;
					// alert(row_num+'=ok3');
					$("#scanning_tbl tbody tr:last-child").clone().find("td,input,select").each(function()
					{
						onchange_value="";
						$(this).attr({
						  'id': function(_, id) {
						  	var id=id.split("_");
						  	if(id.length>3){
						  		var check_id=id[0] +"_"+ id[1] +"_"+ id[2];
						  		if(check_id=="cbo_To_BodyPart")
						  		{
						  			onchange_value="copyBodyPart(\'"+row_num+"_0\');";
						  		}
						  		else if(check_id=="cbo_floor_to")
						  		{

						  			onchange_value="fn_load_room(this.value, "+row_num+");copy_all(\'"+row_num+"_0\');reset_room_rack_shelf("+row_num+",\'cbo_floor_to\')";
						  		}
						  		else if(check_id=="cbo_room_to")
						  		{
						  			onchange_value="fn_load_rack(this.value, "+row_num+");copy_all(\'"+row_num+"_1\');reset_room_rack_shelf("+row_num+",\'cbo_room_to\')";
						  		}
						  		else if(check_id=="txt_rack_to")
						  		{
						  			onchange_value="fn_load_shelf(this.value, "+row_num+");copy_all(\'"+row_num+"_2\');reset_room_rack_shelf("+row_num+",\'txt_rack_to\')";
						  		}
						  		else if(check_id=="txt_shelf_to")
						  		{
						  			onchange_value="fn_load_bin(this.value, "+row_num+");copy_all(\'"+row_num+"_3\');reset_room_rack_shelf("+row_num+",\'txt_shelf_to\')";
						  		}
						  		else if(check_id=="txt_bin_to")
						  		{
						  			onchange_value="copy_all(\'"+row_num+"_4\')";
						  		}
						  		return id[0] +"_"+ id[1] +"_"+ id[2] +"_"+ row_num
						  	}
						  	if(id.length>2){
						  		return id[0] +"_"+ id[1] +"_"+ id[2]
						  	}
						  	else{
						  		return id[0] +"_"+ row_num
						  	}
						  },
						  	'value': function(_, value) { return value },
						  	'onchange': function(){
							  	return onchange_value;
							}
						});
					}).end().appendTo("#scanning_tbl");

					$("#scanning_tbl tbody tr:last-child").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:last-child").removeAttr('id').attr('id','tr__'+row_num);//decrease_1
					$("#scanning_tbl tbody tr:last-child").find(':input:not(:button)','select').val("");
					$("#scanning_tbl tbody tr:last-child").find(':input(:checkbox)').attr('checked',false).attr('disabled',false);
				}

				scanned_barcode.push(barcode_no);
				//batch_batcode_arr.push(barcode_no);
				// below show data
				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#txtProgramNo_"+row_num).text(program_no);
				$("#txtProdId_"+row_num).text(prod_id);
				$("#txtFabricDetails_"+row_num).text(fabric_dtls);// contruction_composition_gsm_dia
				$("#txtYcount_"+row_num).text(ycount);
				$("#txtBrandName_"+row_num).text(brand_name);
				$("#txtYarnLot_"+row_num).text(yarn_lot);
				$("#txtColorName_"+row_num).text(color_names);

				$("#cbo_To_BodyPart_"+row_num).val(to_body_part);
				$("#cbo_floor_to_"+row_num).val(to_floor_id);
				$("#cbo_room_to_"+row_num).val(to_room);
				$("#txt_rack_to_"+row_num).val(to_rack);
				$("#txt_shelf_to_"+row_num).val(to_shelf);
				$("#txt_bin_to_"+row_num).val(to_bin_box);

				$("#txtStitchLength_"+row_num).text(stitch_length);
				$("#txtMachineDia_"+row_num).text(mc_dia);
				$("#txtRollWgt_"+row_num).text(qnty);
				$("#remarks_"+row_num).text(remarks);

				// below hidden data
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#rollNo_"+row_num).val(roll_no);
				$("#progId_"+row_num).val(program_no);
				$("#productId_"+row_num).val(prod_id);
				$("#toProductUp_"+row_num).val(to_prod_id);// to product id for update event
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#ItemDesc_"+row_num).val(itemDesc);//$itemDesc=$row[csf('detarmination_id')].'*'.$row[csf('gsm')].'*'.$row[csf('dia_width')];
				$("#ItemDtls_"+row_num).val(fabric_dtls);
				$("#rollAmount_"+row_num).val(amount);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollId_"+row_num).val(roll_id);//roll_id_prev
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#colorName_"+row_num).val(color_id);
				$("#yarnCount_"+row_num).val(yarn_count_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#machineDia_"+row_num).val(mc_dia);
				$("#brandId_"+row_num).val(brand_id);
				$("#frombodypartId_"+row_num).val(body_part_id);
				$("#floor_"+row_num).val(from_floor);
				$("#room_"+row_num).val(from_room);
				$("#rack_"+row_num).val(from_rack);
				$("#shelf_"+row_num).val(from_self);
				$("#binbox_"+row_num).val(from_bin_box);
				$("#dtlsId_"+row_num).val(dtls_id);// only for update event
				$("#requiDtlsId_"+row_num).val(requ_dtls_id);// only for update event
				$("#transIdFrom_"+row_num).val(from_trans_id);// only for update event
				$("#transIdTo_"+row_num).val(to_trans_id);// only for update event
				$("#rolltableId_"+row_num).val(rolltableId);// only for update event
				$("#transRollId_"+row_num).val(transRollId);//current id
				$("#storeId_"+row_num).val(store_id_from);
				$("#txtRemarks_"+row_num).val(remarks);

				$('#txt_tot_row').val(row_num);

				$('#txtcheck_'+row_num).attr("onClick","show_selected_total("+row_num+");");

				if (checked_flag==1)
				{
					$("#txtcheck_"+row_num).attr('checked',true);
				}
				else
				{
					$("#txtcheck_"+row_num).attr('disabled',false);
				}
				if (disabled_flag==1)
				{
					$("#txtcheck_"+row_num).attr('disabled',true);
				}
				else
				{
					$("#txtcheck_"+row_num).attr('disabled',false);
				}
				// =======================================================================
			}

			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
		}
		//alert(up_roll_id);
		calculate_total();
	}

	function calculate_total()
	{
		var total_roll_weight='';var selected_total_roll_wgt='';
		$("#scanning_tbl").find('tbody tr:not(.fltrow)').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			// alert(rollWgt+'==');
			total_roll_weight=total_roll_weight*1+rollWgt*1;


			if($(this).find('input[name="txtcheck[]"]').attr('checked'))
			{
				var selected_rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				// alert(selected_rollWgt+'##');
				selected_total_roll_wgt=selected_total_roll_wgt*1+selected_rollWgt*1;
			}
		});
		// alert(total_roll_weight+'='+selected_total_roll_wgt);
		$("#total_roll_wgt_show").text(total_roll_weight.toFixed(2));
		$("#selected_roll_wgt_show").text(selected_total_roll_wgt.toFixed(2));
	}

	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();

		var html='<tr id="tr__1" align="center" valign="middle"><td id="button_1" align="center" width="40"><input type="checkbox" name="txtcheck[]" id="txtcheck_1" onClick="show_selected_total(1)"/><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="rollNo[]" id="rollNo_1"/><input type="hidden" name="progId[]" id="progId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="toProductUp[]" id="toProductUp_1"><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="ItemDesc[]" id="ItemDesc_1"/><input type="hidden" name="ItemDtls[]" id="ItemDtls_1"/><input type="hidden" name="rollAmount[]" id="rollAmount_1"/><input type="hidden" name="rollRate[]" id="rollRate_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="yarnLot[]" id="yarnLot_1"/><input type="hidden" name="colorName[]" id="colorName_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="stichLn[]" id="stichLn_1"/><input type="hidden" name="machineDia[]" id="machineDia_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="frombodypartId[]" id="frombodypartId_1"/><input type="hidden" name="floor[]" id="floor_1"/><input type="hidden" name="room[]" id="room_1"/><input type="hidden" name="rack[]" id="rack_1"/><input type="hidden" name="shelf[]" id="shelf_1"/><input type="hidden" name="binbox[]" id="binbox_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="requiDtlsId[]" id="requiDtlsId_1"/><input type="hidden" name="transIdFrom[]" id="transIdFrom_1"/><input type="hidden" name="transIdTo[]" id="transIdTo_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="transRollId[]" id="transRollId_1"/><input type="hidden" name="storeId[]" id="storeId_1"/><input type="hidden" name="txtRemarks[]" id="txtRemarks_1"/></td><td width="40" id="sl_1"></td><td width="80" id="barcode_1"></td><td width="50" id="roll_1"></td><td width="70" id="txtProgramNo_1"></td><td width="60" id="txtProdId_1"></td><td width="180" id="txtFabricDetails_1"></td><td width="80" id="txtYcount_1"></td><td width="70" id="txtBrandName_1"></td><td style="word-break:break-all;" width="80" id="txtYarnLot_1"></td><td style="word-break:break-all;" width="100" id="txtColorName_1"></td><td width="100" align="center" id="toBodyPartTd_1"><? echo create_drop_down( "cbo_To_BodyPart_1", 100,$blank_array,"", 1, "--Select--", 0, "copyBodyPart(\'1_0\')",0,"","","","","","","cboToBodyPart[]","onchange_void" );?></td><td width="50" align="center" id="floor_td_to" class="floor_td_to"><? echo create_drop_down( "cbo_floor_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, 1); copy_all(\'1_0\'); reset_room_rack_shelf(1,\'cbo_floor_to\');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?></td><td width="50" align="center" id="room_td_to"><? echo create_drop_down( "cbo_room_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, 1); copy_all(\'1_1\'); reset_room_rack_shelf(1,\'cbo_room_to\');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?></td><td width="50" align="center" id="rack_td_to"><? echo create_drop_down( "txt_rack_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, 1); copy_all(\'1_2\'); reset_room_rack_shelf(1,\'txt_rack_to\');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?></td><td width="50" align="center" id="shelf_td_to"><? echo create_drop_down( "txt_shelf_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, 1); copy_all(\'1_3\');reset_room_rack_shelf(1,\'txt_shelf_to\');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?></td><td width="50" align="center" id="bin_td_to"><? echo create_drop_down( "txt_bin_to_1", 50,$blank_array,"", 1, "--Select--", 0, "copy_all(\'1_4\');",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?></td><td width="80" id="txtStitchLength_1"></td><td width="80" id="txtMachineDia_1"></td><td width="80" id="txtRollWgt_1" align="right"></td><td width="" id="remarks_1"></td></tr>';

		$('#txt_system_id').val('');
		$('#update_id').val('');
		$('#txt_bar_code_num').val('');
		$('#txt_challan_no').val('');
		// $('#cbo_company_id').val(0);
		// $('#cbo_company_id').attr('disabled',true);
		$('#txt_tot_row').val(1);

		$('#txt_transfer_date').val('');
		$('#cbo_from_store_name').val(0);
		$('#cbo_store_name').val(0);
		$('#txt_from_order_no').val('');
		$('#txt_from_order_id').val('');
		// $('#txt_deleted_id').val('');
		$("#scanning_tbl tbody").html(html);
	}

</script>

<style>
    #scanning_tbl tr td
    {
        background-color:#FFF;
        color:#000;
        border: 1px solid #666666;
        line-height:12px;
        overflow:auto;
    }
</style>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:760px;">
        <legend>Roll wise Grey Fabric Sales Order To Sales Order Transfer Entry</legend>
            <fieldset style="width:950px;">
                <table width="940" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Transfer System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                        <td colspan="4" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Transfer Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2,4');
                            ?>
                        </td>

                        <td class="must_entry_caption">Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "fnc_company_onchang_reset(this.value);company_on_change(this.value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/grey_sales_order_to_order_roll_trans_controller' );" );
                            ?>
                        </td>
                        <td>To Company</td>
                        <td>
                            <?
								echo create_drop_down( "cbo_to_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "to_company_on_change(this.value)",1 );
							?>
                        </td>

						<td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>


                    </tr>
                    <tr>
                        
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                    	<td>Requisition Basis</td>
                        <td>
                            <input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:148px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_requisition_no();"  disabled="disabled"/>
                        	<input type="hidden" name="txt_requisition_id" id="txt_requisition_id"/>
                        	<input type="hidden" name="store_update_upto" id="store_update_upto">
                        	<input type="hidden" name="requisition_and_order_basis" id="requisition_and_order_basis">
                        	<input type="hidden" name="requisition_basis" id="requisition_basis">
                        </td>

						<td>Driver Name:</td>
                        <td>
                            <input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:148px;" maxlength="20" title="" />
                        </td>
                    	<td>Mobile No.</td>
                        <td>
                            <input type="text" name="txt_mobile_no" id="txt_mobile_no" class="text_boxes" style="width:148px;" maxlength="20" title="" />
                        </td>

                    </tr>

					<tr>
						<td>Vehicle No.</td>
                        <td>
                            <input type="text" name="txt_vehicle_no" id="txt_vehicle_no" class="text_boxes" style="width:148px;" maxlength="20" title="" />
                        </td>
						<td>Remark.</td>
                        <td>
                            <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:148px;" maxlength="20" title="" />
                        </td>
						
						<td id="hidd_requi_qty_td" style="display: none;">Requisition Qty</td>
                        <td><input type="text" name="hidd_requi_qty" id="hidd_requi_qty" class="text_boxes" style="width:150px; display: none;" disabled="disabled" placeholder="Display" /></td>
                        <td id="complite_status_td" style="display: none;" title="Completion Status">Com. Status</td>
                        <td id="cbo_complete_status_td" style="display: none;">
							<?
							$complite_status_arr=array( 1=> 'Partial', 2=> 'Full' );
							echo create_drop_down( "cbo_complete_status", 160, $complite_status_arr,"", 0, "-- Select --", 1, "",0,'1,2','','','','' );
							?>
                        </td>

                    </tr>

					
                    <tr>
                    	
                        <td  id="roll_purpose_td" style="display:none" title="">Purpose</td>
                        <td id="roll_purpose_tds" style="display:none">
							<?

							echo create_drop_down( "cbo_purpose_id", 160, $roll_transfer_purpose_arr,"", 1, "-- Select --", 0, "",0,'','','','','' );
							?>
                        </td>
                    </tr>
					

					




                </table>
            </fieldset>
            <table width="750" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>From Sales Order</legend>
                            <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">
                                <tr>
                                    <td width="30%" class="must_entry_caption">Sales Order No</td>
                                    <td>
                                        <input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly disabled="" />
                                        <input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Fab. Booking No</td>
                                    <td><input type="text" name="txt_from_booking_no" id="txt_from_booking_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Po Company</td>
                                    <td><? echo create_drop_down( "cbo_from_company", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", '', "" ,1); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Po Buyer</td>
                                    <td><? echo create_drop_down( "cbo_from_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cust. Buyer</td>
                                    <td><? echo create_drop_down( "cbo_from_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Style Ref.</td>
                                    <td><input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>IR/IB</td>
                                    <td><input type="text" name="txt_from_int_ref" id="txt_from_int_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td><input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
									<td class="must_entry_caption">From Store</td>
									<td id="from_store_td">
										<?
											echo create_drop_down( "cbo_from_store_name", 160, $blank_array,"", 1, "--Select Store--", 0, "" );
										?>
									</td>
								</tr>
                            </table>
                        </fieldset>
                    </td>
                    <td width="2%" valign="top"></td>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>To Sales Order</legend>
                            <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >
                                <tr>
                                    <td width="30%" class="must_entry_caption">Sales Order No</td>
                                    <td>
                                        <input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
                                        <input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Fab. Booking No</td>
                                    <td><input type="text" name="txt_to_booking_no" id="txt_to_booking_no" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Po Company</td>
                                    <td><? echo create_drop_down( "cbo_to_company", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "Display", '', "" ,1); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Po Buyer</td>
                                    <td><? echo create_drop_down( "cbo_to_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cust. Buyer</td>
                                    <td><? echo create_drop_down( "cbo_to_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Style Ref.</td>
                                    <td><input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>IR/IB</td>
                                    <td><input type="text" name="txt_to_int_ref" id="txt_to_int_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td><input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
								<tr>
									<td class="must_entry_caption">To Store</td>
									<td id="store_td">
										<?
											echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "fn_load_floor(this.value);" );
										?>
									</td>
								</tr>
								<tr>
                                    <td width="30%">To Color</td>
                                    <td>
                                        <input type="text" name="txt_to_color_no" id="txt_to_color_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_toColor();" readonly />
                                        <input type="hidden" name="hid_to_color_id" id="hid_to_color_id" readonly>
                                    </td>
                                </tr>
                            </table>
                       </fieldset>
                    </td>
                </tr>
			</table>
            <fieldset style="width:1640px;text-align:left">
            	<div style="margin-bottom:5px; text-align: center;">
                    <strong>Roll Scan/Browse</strong>
                    <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:100px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                </div>
				<table cellpadding="0" width="1620" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
                    	<th width="40">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="70">Program No</th>
                        <th width="60">Product Id</th>
                        <th width="180">Fabric Description</th>
                        <th width="80">Y/Count</th>
                        <th width="70">Y/Brand</th>
                        <th width="80">Y/Lot</th>
                        <th width="100">Color</th>
						<th width="100" class="must_entry_caption"><input type="checkbox" checked id="bodyPartIds" name="bodyPartIds"/><br>Body Part</th>
						<th width="50"><input type="checkbox" checked id="floorIds" name="floorIds"/><br>Floor</th>
						<th width="50"><input type="checkbox" checked id="roomIds" name="roomIds"/><br>Room</th>
						<th width="50"><input type="checkbox" checked id="rackIds" name="rackIds"/><br>Rack</th>
						<th width="50"><input type="checkbox" checked id="shelfIds" name="shelfIds"/><br>Shelf</th>
						<th width="50"><input type="checkbox" checked id="binIds" name="binIds"/><br>Bin/Box</th>
                        <th width="80">Stitch Length</th>
                        <th width="80">Machine Dia</th>
                        <th width="80">Roll Wgt. (Kg)</th>
                        <th>Remarks</th>
                    </thead>
                </table>
                <div style="width:1650px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1620" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                    		<tr id="tr__1" align="center" valign="middle">
                            	<td id="button_1" align="center" width="40">
                            		<input type="checkbox" name="txtcheck[]" id="txtcheck_1" onClick="show_selected_total(1)"/>
                            		<input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
									<input type="hidden" name="rollNo[]" id="rollNo_1"/>
									<input type="hidden" name="progId[]" id="progId_1"/>
									<input type="hidden" name="productId[]" id="productId_1"/>
									<input type="hidden" name="toProductUp[]" id="toProductUp_1">
									<input type="hidden" name="orderId[]" id="orderId_1"/>
									<input type="hidden" name="ItemDesc[]" id="ItemDesc_1"/>
									<input type="hidden" name="ItemDtls[]" id="ItemDtls_1"/>
									<input type="hidden" name="rollAmount[]" id="rollAmount_1"/>
									<input type="hidden" name="rollRate[]" id="rollRate_1"/>
									<input type="hidden" name="rollId[]" id="rollId_1"/>
									<input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
									<input type="hidden" name="yarnLot[]" id="yarnLot_1"/>
									<input type="hidden" name="colorName[]" id="colorName_1"/>
									<input type="hidden" name="yarnCount[]" id="yarnCount_1"/>
									<input type="hidden" name="stichLn[]" id="stichLn_1"/>
									<input type="hidden" name="machineDia[]" id="machineDia_1"/>
									<input type="hidden" name="brandId[]" id="brandId_1"/>
									<input type="hidden" name="frombodypartId[]" id="frombodypartId_1"/>
									<input type="hidden" name="floor[]" id="floor_1"/>
									<input type="hidden" name="room[]" id="room_1"/>
									<input type="hidden" name="rack[]" id="rack_1"/>
									<input type="hidden" name="shelf[]" id="shelf_1"/>
									<input type="hidden" name="binbox[]" id="binbox_1"/>
									<input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
									<input type="hidden" name="requiDtlsId[]" id="requiDtlsId_1"/>
									<input type="hidden" name="transIdFrom[]" id="transIdFrom_1"/>
									<input type="hidden" name="transIdTo[]" id="transIdTo_1"/>
									<input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
									<input type="hidden" name="transRollId[]" id="transRollId_1"/>
									<input type="hidden" name="storeId[]" id="storeId_1"/>
									<input type="hidden" name="txtRemarks[]" id="txtRemarks_1"/>
                                </td>
                                <td width="40" id="sl_1"></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="50" id="roll_1"></td>
                                <td width="70" id="txtProgramNo_1"></td>
                                <td width="60" id="txtProdId_1"></td>
                                <td width="180" id="txtFabricDetails_1"></td>
                                <td width="80" id="txtYcount_1"></td>
                                <td width="70" id="txtBrandName_1"></td>
                                <td width="80" id="txtYarnLot_1" style="word-break:break-all;"></td>
                                <td width="100" id="txtColorName_1" style="word-break:break-all;"></td>
                                <td width="100" align="center" id="toBodyPartTd_1">
								<? echo create_drop_down( "cbo_To_BodyPart_1", 100,$blank_array,"", 1, "--Select--", 0, "copyBodyPart('1_0')",0,"","","","","","","cboToBodyPart[]","onchange_void" );
								?>
								</td>
                                <td width="50" align="center" id="floor_td_to" class="floor_td_to">
								<? echo create_drop_down( "cbo_floor_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, 1); copy_all('1_0'); reset_room_rack_shelf(1,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
								</td>
								<td width="50" align="center" id="room_td_to">
		                        <? echo create_drop_down( "cbo_room_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, 1); copy_all('1_1'); reset_room_rack_shelf(1,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?></td>
								<td width="50" align="center" id="rack_td_to">
								<? echo create_drop_down( "txt_rack_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, 1); copy_all('1_2'); reset_room_rack_shelf(1,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
								</td>
								<td width="50" align="center" id="shelf_td_to">
								<? echo create_drop_down( "txt_shelf_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, 1); copy_all('1_3');reset_room_rack_shelf(1,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
								</td>
								<td width="50" align="center" id="bin_td_to">
								<? echo create_drop_down( "txt_bin_to_1", 50,$blank_array,"", 1, "--Select--", 0, "copy_all('1_4');",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
								</td>
                                <td width="80" id="txtStitchLength_1"></td>
                                <td width="80" id="txtMachineDia_1"></td>
                                <td width="80" id="txtRollWgt_1" align="right"></td>
                                <td width="" id="remarks_1"></td>
                            </tr>
                        </tbody>
                	</table>
                </div>
                <table cellpadding="0" cellspacing="0" width="1620" border="1" rules="all" class="rpt_table" align="left">
                    <tfoot>
                    	<tr>
	                    	<th width="40"></th>
	                    	<th width="40"></th>
	                        <th width="80"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="60"></th>
	                        <th width="180"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="50"></th>
	                        <th width="50"></th>
	                        <th width="50"></th>
	                        <th width="50"></th>
	                        <th width="50"></th>
	                        <th width="80"></th>
	                        <th width="80">Total Weight</th>
	                        <th width="80" id="total_roll_wgt_show">&nbsp;</th>
	                        <th>&nbsp;</th>
                    	</tr>
                    	<tr>
	                    	<th width="40"></th>
	                    	<th width="40"></th>
	                        <th width="80"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="60"></th>
	                        <th width="180"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
							<th width="50"></th>
	                        <th width="50"></th>
	                        <th width="50"></th>
	                        <th width="50"></th>
	                        <th width="50"></th>
	                        <th width="80"></th>
	                        <th width="80">Selected total</th>
	                        <th width="80" id="selected_roll_wgt_show">&nbsp;</th>
	                        <th>&nbsp;</th>
                    	</tr>
                    </tfoot>
                </table>
                <br>
                <table width="1240" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <?
                            	 echo load_submit_buttons($permission, "fnc_grey_transfer_entry", 0,1,"fnc_reset_form()",1);//reset_form_all()
                            ?>
                            <input type="hidden" name="desc_str" id="desc_str">

							<input type="button" name="print2" id="print2" value="Print 2" onClick="fnc_grey_transfer_entry(5)" style="width:80px;display:none;" class="formbutton"/>
							<input type="button" name="print3" id="print3" value="Print 3" onClick="fnc_grey_transfer_entry(6)" style="width:80px;display:none;" class="formbutton"/>

							<input type="button" name="print4" id="print4" value="Print 4" onClick="fnc_grey_transfer_entry(7)" style="width:80px;display:none;" class="formbutton"/>

                        </td>
                    </tr>
                </table>
			</fieldset>
        </fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	setFilterGrid("scanning_tbl",-1,tableFilters);
</script>
</html>
