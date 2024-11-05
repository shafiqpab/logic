<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Roll Wise Finish Fabric Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	26-10-2021
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
echo load_html_head_contents("Roll Wise Finish Fabric Transfer Entry","../../", 1, 1, $unicode,'',''); 
		//$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][505] );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	<?
		//echo "var field_level_data= ". $data_arr . ";\n";
	?>
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();  var scanned_barcode_issue =new Array(); var barcode_rollTableId_array=new Array(); var table_barcode_no_arr=new Array();
	var barcode_trnasId_array =new Array(); var barcode_dtlsId_array=new Array(); var barcode_trnasId_to_array=new Array();

	
	//var batch_batcode_arr=new Array();
	<?
	$floor_room_rack_array=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$jsbarcode_floor_array= json_encode($floor_room_rack_array);
		echo "var jsfloor_room_rack_shelf_name_array = ". $jsbarcode_floor_array . ";\n";

	//$sql_batch=sql_select("select barcode_no from  pro_roll_details where entry_form=82 and  status_active=1 and is_deleted=0");
	//$batch_batcode_arr=array();
	//foreach($sql_batch as $inf)
	//{
		//$batch_batcode_arr[]=$inf[csf('barcode_no')];	
	//}
	//$jsbatch_batcode_arr= json_encode($batch_batcode_arr);
	//echo "var batch_batcode_arr = ". $jsbatch_batcode_arr . ";\n"
	?>
	function openmypage_issue()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		/*if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}*/
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_finish_fabric_transfer_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Transfer Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form(1);
				get_php_form_data(issue_id, "populate_data_from_data", "requires/roll_wise_finish_fabric_transfer_entry_controller");

				var com_id=$('#cbo_to_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var all_data=com_id + "__" + store_id;
				//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
				var tbl_length=$('#scanning_tbl tbody tr').length;
				//alert(floor_result+"="+tbl_length);//return;
				var JSONObject = JSON.parse(floor_result);
				for(var i=1; i<=tbl_length; i++)
				{
					$('#cboFloorTo_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject))
					{
						$('#cboFloorTo_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};
				}
									
				var barcode_nos=return_global_ajax_value( issue_id, 'barcode_nos', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
				if(trim(barcode_nos)!="")
				{					
					create_row(1,barcode_nos);
				}
				set_button_status(1, permission, 'fnc_finish_fabric_issue_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			    $("#btn_mc_6").removeClass('formbutton_disabled');
			    $("#btn_mc_6").addClass('formbutton');
			}
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
		var cbo_store_name=$('#cbo_store_name').val();
		var requisition_id=$('#txt_requisition_id').val();
		
		var cbo_store_name_from=$('#cbo_store_name_from').val();
		
		if (form_validation('cbo_transfer_criteria*cbo_store_name_from','Transfer Criteria*From Company')==false)
		{
			return;
		}
		else
		{
			if(cbo_transfer_criteria==1)
			{
				if (form_validation('cbo_company_id*cbo_to_company_id','From Company*To Company')==false)
				{
					alert("Please Select Both Company Field");return;
				}
			}
			else
			{
				if (form_validation('cbo_company_id','From Company')==false)
				{
					if(company_id<1)
					{
						alert("Please Select From Company Field");return;
					}
					// else
					// {
					// 	alert("Please Select To Store Field");return;
					// }
				}
			}
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_finish_fabric_transfer_entry_controller.php?company_id='+company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_store_name='+cbo_store_name+'&cbo_store_name_from='+cbo_store_name_from+'&requisition_id='+requisition_id+'&action=barcode_popup','Barcode Popup', 'width=1480px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			
			create_row(0,barcode_nos);

			/*if(barcode_nos!="")
			{
				var barcode_upd=barcode_nos.split(",");
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row(0,barcode_upd[k]);
				}
				set_all_onclick();
			}*/
		}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/roll_wise_finish_fabric_transfer_entry_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_finish_fabric_issue_roll_wise( operation )
	{
		if(operation==4)
		{
			/*var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_transfer_print", "requires/roll_wise_finish_fabric_transfer_entry_controller" ) 
			return;*/

			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title,'finish_fabric_transfer_print');
			return;
		}

		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		var transfer_criteria=$('#cbo_transfer_criteria').val()*1;
		if(form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			return;
		}
		else
		{
			if(transfer_criteria==1)
			{
				if(form_validation('cbo_company_id*cbo_to_company_id*txt_transfer_date*cbo_store_name','From Company*To Company*Transfer Date*To Store')==false)
				{
					return; 
				}
			}
			else
			{
				if(form_validation('cbo_company_id*cbo_store_name*txt_transfer_date','From Company*To Store*Transfer Date')==false)
				{
					return; 
				}
			}
		}
                
        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}

		
		
		var j=0; var dataString=''; 
		var store_update_upto=$('#store_update_upto').val()*1;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var toFloor=$(this).find('select[name="cboFloorTo[]"]').val();
			var toRoom=$(this).find('select[name="cboRoomTo[]"]').val();
			var toRack=$(this).find('select[name="txtRackTo[]"]').val();
			var toShelf=$(this).find('select[name="txtShelfTo[]"]').val();
			var toBin=$(this).find('select[name="txtBinTo[]"]').val();
			if(store_update_upto > 1)
			{
				if(store_update_upto==5 && (toFloor==0 || toRoom==0 || toRack==0 || toShelf==0))
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==4 && (toFloor==0 || toRoom==0 || toRack==0))
				{
					alert("Up To Rack Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==3 && (toFloor==0 || toRoom==0))
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==2 && toFloor==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
			}
			// alert(toFloor+' Ro= '+toRoom+' Ra= '+toRack+' S= '+toShelf);return;
			
			var toOrderId=$(this).find('input[name="toOrderId[]"]').val();
			var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var rollNo=$(this).find("td:eq(2)").text();
			var constructCompo=$(this).find("td:eq(5)").text();
			
			var batchNo=$(this).find('input[name="batchNo[]"]').val();
			var batch_id=$(this).find('input[name="batch_id[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var brandId=$(this).find('input[name="brandId[]"]').val();
			var fromStoreId=$(this).find('input[name="fromStoreId[]"]').val();
			var fromFloor=$(this).find('input[name="floor[]"]').val();
			var fromRoom=$(this).find('input[name="room[]"]').val();
			var fromRack=$(this).find('input[name="rack[]"]').val();
			var fromShelf=$(this).find('input[name="shelf[]"]').val();
			var fromBin=$(this).find('input[name="bin[]"]').val();
			
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var transId=$(this).find('input[name="transId[]"]').val();
			var transIdTo=$(this).find('input[name="transIdTo[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
			var febDescripId=$(this).find('input[name="febDescripId[]"]').val();
			var machineNoId=$(this).find('input[name="machineNoId[]"]').val();
			var gsm=$(this).find('input[name="prodGsm[]"]').val();
			var diaWidth=$(this).find('input[name="diaWidth[]"]').val();
			var knitDetailsId=$(this).find('input[name="knitDetailsId[]"]').val();
			var transferEntryForm=$(this).find('input[name="transferEntryForm[]"]').val();
			var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
			var rollMstId=$(this).find('input[name="rollMstId[]"]').val();
			var rollAmount=$(this).find('input[name="rollAmount[]"]').val();
			var fromBookingWithoutOrder=$(this).find('input[name="fromBookingWithoutOrder[]"]').val();
			var requiDtlsId=$(this).find('input[name="requiDtlsId[]"]').val();
			var barcodeIssue=$(this).find('input[name="barcodeIssue[]"]').val();
			var cboToBodyPart=$(this).find('select[name="cboToBodyPart[]"]').val();
			var fromBodyPart=$(this).find('input[name="fromBodyPart[]"]').val();
			var tobookingNo=$(this).find('input[name="tobookingNo[]"]').val();
			var toBookingMstId=$(this).find('input[name="toBookingMstId[]"]').val();
			var previousToBbatchId=$(this).find('input[name="previousToBbatchId[]"]').val();
			var hiddenTransferqnty=$(this).find('input[name="hiddenTransferqnty[]"]').val();

			j++;
			dataString+='&fromStoreId_' + j + '=' + fromStoreId +'&toOrderId_' + j + '=' + toOrderId +'&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&batchNo_' + j + '=' + batchNo + '&batchId_' + j + '=' + batch_id + '&colorId_' + j + '=' + colorId + '&brandId_' + j + '=' + brandId + '&fromFloor_' + j + '=' + fromFloor + '&fromRoom_' + j + '=' + fromRoom + '&fromRack_' + j + '=' + fromRack + '&fromShelf_' + j + '=' + fromShelf + '&fromBin_' + j + '=' + fromBin + '&toFloor_' + j + '=' + toFloor + '&toRoomId_' + j + '=' + toRoom + '&toRack_' + j + '=' + toRack + '&toShelf_' + j + '=' + toShelf + '&toBin_' + j + '=' + toBin + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&transIdTo_' + j + '=' + transIdTo + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&febDescripId_' + j + '=' + febDescripId+ '&machineNoId_' + j + '=' + machineNoId+ '&gsm_' + j + '=' + gsm+ '&diaWidth_' + j + '=' + diaWidth+ '&knitDetailsId_' + j + '=' + knitDetailsId+ '&transferEntryForm_' + j + '=' + transferEntryForm+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder + '&rollMstId_' + j + '=' + rollMstId + '&constructCompo_' + j + '=' + constructCompo + '&rollAmount_' + j + '=' + rollAmount + '&fromBookingWithoutOrder_' + j + '=' + fromBookingWithoutOrder + '&requiDtlsId_' + j + '=' + requiDtlsId + '&barcodeIssue_' + j + '=' + barcodeIssue + '&fromBodyPart_' + j + '=' + fromBodyPart + '&cboToBodyPart_' + j + '=' + cboToBodyPart +'&tobookingNo_' + j + '=' + tobookingNo +'&toBookingMstId_' + j + '=' + toBookingMstId +'&previousToBbatchId_' + j + '=' + previousToBbatchId +'&hiddenTransferqnty_' + j + '=' + hiddenTransferqnty;
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}		
		
		if (transfer_criteria==1 || transfer_criteria==4)
		{
			var jk=1;
			for (var jk; j>=jk; jk++) {
				if ($('#txtToOrder_'+jk).val()=="") 
				{
					alert('Please Browse To Order');
					$('#txtToOrder_'+jk).focus();
					return;
				}
			}
		}

		var jk=1;
		for (var jk; j>=jk; jk++) 
		{
			if ($('#cboToBodyPart_'+jk).val()==0) 
			{
				alert('Please Browse To Body Part');
				$('#cboToBodyPart_'+jk).focus();
				return;
			}
		}

		 //alert(dataString);return;
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_transfer_no*cbo_transfer_criteria*cbo_company_id*cbo_to_company_id*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_store_name*update_id*txt_deleted_id*txt_deleted_trnsf_dtls_id*txt_deleted_trans_id*txt_deleted_prod_id*txt_deleted_prod_qty*txt_deleted_source_roll_id*txt_deleted_barcode*txt_remarks*txt_delv_company_id',"../../")+dataString;
		
		// alert(data);return;
		freeze_window(operation);		
		http.open("POST","requires/roll_wise_finish_fabric_transfer_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_finish_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_finish_fabric_issue_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');                        
            if (response[0] * 1 == 20 * 1) 
            {
                alert(response[1]);
                release_freezing();
                return;
            }
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				
				
				fnc_reset_form(2);
				
				var com_id=$('#cbo_to_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var all_data=com_id + "__" + store_id;
				
				//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
				var tbl_length=$('#scanning_tbl tbody tr').length;
				//alert(floor_result+"="+tbl_length);//return;
				var JSONObject = JSON.parse(floor_result);
				for(var i=1; i<=tbl_length; i++)
				{
					$('#cboFloorTo_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject))
					{
						$('#cboFloorTo_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};
				}
				
				document.getElementById('update_id').value = response[1];
				var barcode_nos=return_global_ajax_value( response[1], 'barcode_nos', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
			
				if(trim(barcode_nos)!="")
				{
					create_row(1,barcode_nos);
				}
				document.getElementById('txt_transfer_no').value = response[2];
				$('#cbo_transfer_criteria').attr('disabled',true);
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_to_company_id').attr('disabled',true);

				if($('#cbo_transfer_criteria').val() != 4)
				{
					$('#cbo_store_name').attr('disabled',true);
				}
				$('#txt_deleted_id').val( '' );
				$('#txt_deleted_barcode').val( '' );
				$('#txt_deleted_trnsf_dtls_id').val( '' );
				$('#txt_deleted_trans_id').val( '' );
				$('#txt_deleted_prod_id').val( '' );
				$('#txt_deleted_prod_qty').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_finish_fabric_issue_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			}
			else if(response[0]==2)
			{
				location.reload();
			}
			release_freezing();
		}
	}
	
	//var scanned_barcode=new Array();
	
	function create_row(is_update,barcode_no)
	{

		//alert(barcode_no);
		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 

		

		
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var cbo_store_name_from=$('#cbo_store_name_from').val();
		if (form_validation('cbo_transfer_criteria*cbo_store_name_from','Transfer Criteria*From Company')==false)
		{
			return;
		}
		else
		{
			if(cbo_transfer_criteria==1)
			{
				if (form_validation('cbo_company_id*cbo_company_id','From Company*To Company')==false)
				{
					alert("Please Select Both Company Field");return;
				}
			}
			// else
			// {
			// 	if (form_validation('cbo_company_id*cbo_store_name','From Company*Store')==false)
			// 	{
			// 		alert("Please Select To Store Field");return;
			// 	}
			// }
			
		}
		
		var system_id=$('#update_id').val();
		var requi_system_id=$('#txt_requisition_id').val();
		var cbo_store_name=$('#cbo_store_name').val();
		var txt_from_product=$('#txt_from_product').val();
		var txt_from_order_id=$('#txt_from_order_id').val();
		//newly insert
		//alert(bar_code);return;
		if(is_update==0) // Save Event
		{
			var j=0;
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var table_barcode=$(this).find('input[name="barcodeNo[]"]').val();
				// alert(table_barcode);
				table_barcode_no_arr.push(table_barcode);
				j++;
			});
			
			/*var arrTostring = table_barcode_no_arr.toString();
			var barcodeString = arrTostring.substring(1);
			var barcodeArray = barcodeString.split(',');
			alert(bar_code+'===='+barcodeArray);*/

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
			var barcode_data=return_global_ajax_value( bar_code+"**"+system_id+"**"+cbo_transfer_criteria+"**"+cbo_store_name+"**"+cbo_store_name_from, 'populate_barcode_data', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
			//alert(barcode_data);return;

			var barcode_data_all=new Array(); var barcode_data_ref=new Array(); var all_sample_non_order_booking =""; var all_to_order_str = "";
			barcode_data_ref=barcode_data.split("__");
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");

				var rcv_id=barcode_data_all[0];
				var company_id=barcode_data_all[1];
				var body_part_name=barcode_data_all[2];
				var receive_basis=barcode_data_all[3];
				var receive_basis_id=barcode_data_all[4];
				var receive_date=barcode_data_all[5];
				var booking_no=barcode_data_all[6];
				var booking_id=barcode_data_all[7];
				var color=barcode_data_all[8];
				var knitting_source_id=barcode_data_all[9];
				var knitting_source=barcode_data_all[10];
				var store_id=barcode_data_all[11];
				var knitting_company_id=barcode_data_all[12];
				var batch_no=barcode_data_all[13];
				var batch_id=barcode_data_all[14];
				var rack=barcode_data_all[16];
				var self=barcode_data_all[17];
				var knitting_company_name=barcode_data_all[18];
				var dtls_id=barcode_data_all[19];
				var prod_id=barcode_data_all[20];
				var febric_description_id=barcode_data_all[21];
				var compsition_description=barcode_data_all[22];
				var gsm=barcode_data_all[23];
				var width=barcode_data_all[24];
				var roll_id=barcode_data_all[25];
				var roll_no=barcode_data_all[26];
				var po_breakdown_id=barcode_data_all[27];
				var qnty=barcode_data_all[28];
				var barcode_no=barcode_data_all[29];
				var job_no=barcode_data_all[30];
				var buyer_id=barcode_data_all[31];
				var color_id=barcode_data_all[34];
				var buyer_name=barcode_data_all[32];
				var po_number=barcode_data_all[33];
				var store_name=barcode_data_all[35];
				var body_part_id=barcode_data_all[36];
				var brand_id=barcode_data_all[37];
				var machine_no_id=barcode_data_all[38];
				var entry_form=barcode_data_all[39];
				var book_without_order=barcode_data_all[40];
				var rollMstId=barcode_data_all[41];
				var bookingNo_fab=barcode_data_all[42];
				var amount=barcode_data_all[43];
				
				var from_book_without_order=barcode_data_all[44];
				var floor_id=barcode_data_all[45];
				var room_id=barcode_data_all[46];
				var internal_ref=barcode_data_all[47];
				var bin_box_id=barcode_data_all[48];

				var multi_floor=barcode_data_all[49];
				var multi_room=barcode_data_all[50];
				var multi_rack=barcode_data_all[51];
				var multi_self=barcode_data_all[52];
				var multi_bin=barcode_data_all[53];

				
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
				else if(barcode_data_all[0]==-1)
				{
					alert('Sorry! Barcode Already Scanned. Id : '+barcode_data_all[1]);
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Sorry! Barcode Already Scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return; 
				}
				else if(barcode_data_all[0]==30)
				{
					alert('Barcode not found ');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Sorry! Barcode Already Scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return; 
				}
				//alert(company_id);
				if(company_id != cbo_company_id)
				{
					alert(company_id+'Multiple company not allowed '+cbo_company_id);
					return;
				}

				// if (requi_system_id !="") 
				// {
				// 	// alert(txt_from_order_id +'!='+ po_breakdown_id) +'||'+ (txt_from_product +'!='+ prod_id);
				// 	if( (txt_from_order_id != po_breakdown_id) || (txt_from_product != prod_id))
				// 	{
				// 		alert('Multiple Order and Fabrication not allowed');
				// 		return;
				// 	}
				// }

				if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
				{ 
					alert('Sorry! Barcode Already Scanned.'); 
					$('#txt_bar_code_num').val('');
					return; 
				}
				//alert(barcode_data);return;		
				
				var bar_code_no=$('#barcodeNo_'+row_num).val();
				
				if(bar_code_no!="")
				{
					row_num++;
					$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
					{
						$(this).attr({ 
							'id': function(_, id) 
							{ 
							  	var id=id.split("_"); 
							  	//return id[0] +"_"+ row_num 
							  	if(id.length>3){
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
							  	return "change_floor(this.value,this.id);";
							}              
						});
					}).end().prependTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:first").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr__'+row_num);//decrease_1
					$("#scanning_tbl tbody tr:first").find('input','select').val("");
					$("#decrease_"+row_num).val("-");
					
				}
				
				scanned_barcode.push(barcode_no);
				//batch_batcode_arr.push(bar_code);
				
				$("#sl_"+row_num).text(row_num);
				$("#fromStore_"+row_num).text(store_name);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#batch_"+row_num).text(batch_no);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');
				$("#rollWeight_"+row_num).text(qnty);
				$("#buyer_"+row_num).text(buyer_name);
				$("#booking_"+row_num).text(bookingNo_fab);
				
				if(from_book_without_order==1)
				{
					$("#job_"+row_num).text("");
					$("#order_"+row_num).text("");
					$("#intRef_"+row_num).text("");
				}
				else
				{
					$("#job_"+row_num).text(job_no);
					$("#order_"+row_num).text(po_number);
					$("#intRef_"+row_num).text(internal_ref);
				}
				
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#basis_"+row_num).text(receive_basis);
				$("#progBookPiNo_"+row_num).text(booking_no);

				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#batchNo_"+row_num).val(batch_no);
				$("#batchId_"+row_num).val(batch_id);
				$("#colorId_"+row_num).val(color_id);
				$("#brandId_"+row_num).val(brand_id);
				
				$("#fromBodyPart_"+row_num).val(body_part_id);
				
				$("#fromStoreId_"+row_num).val(store_id);
				$("#floor_"+row_num).val(floor_id);
				$("#room_"+row_num).val(room_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#bin_"+row_num).val(bin_box_id);
				
				$("#txtToOrder_"+row_num).val(up_to_po_no);
				$("#toOrderId_"+row_num).val(up_to_po_id);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#knitDetailsId_"+row_num).val(dtls_id);
				$("#transferEntryForm_"+row_num).val(entry_form);
				
				if(cbo_transfer_criteria == 2)
				{
					$("#bookWithoutOrder_"+row_num).val(from_book_without_order);
				}
				else
				{
					$("#bookWithoutOrder_"+row_num).val("0");
				}
				$("#rollMstId_"+row_num).val(rollMstId);
				$("#rollAmount_"+row_num).val(amount);
				$("#cons_"+row_num).prop("title","prod id = "+prod_id);
				$("#fromBookingWithoutOrder_"+row_num).val(from_book_without_order);	
				$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
				$('#txtToOrder_'+row_num).removeAttr("disabled","").removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
				
				$("#cboToBodyPart_"+row_num).removeAttr('onchange').attr("onchange","copyBodyPart(this.id,this.value);");  

				$("#cboFloorTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_room(this.value,"+row_num+"); copy_all('"+row_num+"_0'); reset_room_rack_shelf("+row_num+",'cbo_floor_to');"); 
				$("#cboRoomTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_rack(this.value,"+row_num+"); copy_all('"+row_num+"_1'); reset_room_rack_shelf("+row_num+",'cbo_room_to');"); 
				$("#txtRackTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_shelf(this.value,"+row_num+"); copy_all('"+row_num+"_2'); reset_room_rack_shelf("+row_num+",'txt_rack_to');"); 
				$("#txtShelfTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_bin(this.value,"+row_num+"); copy_all('"+row_num+"_3');"); 
				$("#txtBinTo_"+row_num).removeAttr('onchange').attr("onchange","copy_all('"+row_num+"_4');"); 

				$('#cboToBodyPart_'+row_num).html('<option value="'+0+'">--Select--</option>');

				if(multi_room !=0)
				{
					var multi_room = multi_room.split(",");
					var selectbox = $("#cboRoomTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_room.length; j++)
					{
					    list += "<option value='" +multi_room[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_room[j]]+ "</option>";
					}
					selectbox.html(list);
				}
				
				if(multi_rack !=0)
				{
					var multi_rack = multi_rack.split(",");
					var selectbox = $("#txtRackTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_rack.length; j++)
					{
					    list += "<option value='" +multi_rack[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_rack[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				if(multi_self !=0)
				{
					var multi_self = multi_self.split(",");
					var selectbox = $("#txtShelfTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_self.length; j++)
					{
					    list += "<option value='" +multi_self[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_self[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				if(multi_bin !=0)
				{
					var multi_bin = multi_bin.split(",");
					var selectbox = $("#txtBinTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_bin.length; j++)
					{
					    list += "<option value='" +multi_bin[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_bin[j]]+ "</option>";
					}
					selectbox.html(list);
				}
				
				$("#cboFloorTo_"+row_num).val(floor_id); 
				$("#cboRoomTo_"+row_num).val(room_id); 
				$("#txtRackTo_"+row_num).val(rack); 
				$("#txtShelfTo_"+row_num).val(self); 
				$("#txtBinTo_"+row_num).val(bin_box_id); 

				$('#txt_tot_row').val(row_num);
				$('#txt_bar_code_num').val('');
				$('#txt_bar_code_num').focus();	
				
				if(cbo_transfer_criteria == 2)
				{
					$('#cboToBodyPart_'+row_num).append('<option value="'+body_part_id+'" selected>'+body_part_name+'</option>');
				}
				else
				{
					if(cbo_transfer_criteria == 2 && from_book_without_order==1)
					{
						all_sample_non_order_booking += po_breakdown_id + ',';
					}

					if(cbo_transfer_criteria == 2 && from_book_without_order==0)
					{
						all_to_order_str += po_breakdown_id + ',';
					}
				}

			}

			
			if(all_sample_non_order_booking !=""){
				load_body_part_sample_wise(all_sample_non_order_booking);
			}
			if(all_to_order_str !=""){
				load_body_part_order_wise(all_to_order_str);
			}

			//alert(job_no);return;
		}
		
		else if(is_update==2) // For requisition basis
		{
			var j=0;
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var table_barcode=$(this).find('input[name="barcodeNo[]"]').val();
				// alert(table_barcode);
				table_barcode_no_arr.push(table_barcode);
				j++;
			});
			
			/*var arrTostring = table_barcode_no_arr.toString();
			var barcodeString = arrTostring.substring(1);
			var barcodeArray = barcodeString.split(',');
			alert(bar_code+'===='+barcodeArray);*/

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
			var barcode_data=return_global_ajax_value( bar_code+"**"+system_id+"**"+cbo_transfer_criteria+"**"+cbo_store_name+"**"+cbo_store_name_from+"**"+requi_system_id, 'populate_barcode_data_from_requisition', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
			//alert(barcode_data);return;

			var barcode_data_all=new Array(); var barcode_data_ref=new Array(); var all_sample_non_order_booking =""; var all_to_order_str = "";
			barcode_data_ref=barcode_data.split("__");
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");

				var rcv_id=barcode_data_all[0];
				var company_id=barcode_data_all[1];
				var body_part_name=barcode_data_all[2];
				var receive_basis=barcode_data_all[3];
				var receive_basis_id=barcode_data_all[4];
				var receive_date=barcode_data_all[5];
				var booking_no=barcode_data_all[6];
				var booking_id=barcode_data_all[7];
				var color=barcode_data_all[8];
				var knitting_source_id=barcode_data_all[9];
				var knitting_source=barcode_data_all[10];
				var store_id=barcode_data_all[11];
				var knitting_company_id=barcode_data_all[12];
				var batch_no=barcode_data_all[13];
				var batch_id=barcode_data_all[14];
				var rack=barcode_data_all[16];
				var self=barcode_data_all[17];
				var knitting_company_name=barcode_data_all[18];
				var dtls_id=barcode_data_all[19];
				var prod_id=barcode_data_all[20];
				var febric_description_id=barcode_data_all[21];
				var compsition_description=barcode_data_all[22];
				var gsm=barcode_data_all[23];
				var width=barcode_data_all[24];
				var roll_id=barcode_data_all[25];
				var roll_no=barcode_data_all[26];
				var po_breakdown_id=barcode_data_all[27];
				var qnty=barcode_data_all[28];
				var barcode_no=barcode_data_all[29];
				var job_no=barcode_data_all[30];
				var buyer_id=barcode_data_all[31];
				var color_id=barcode_data_all[34];
				var buyer_name=barcode_data_all[32];
				var po_number=barcode_data_all[33];
				var store_name=barcode_data_all[35];
				var body_part_id=barcode_data_all[36];
				var brand_id=barcode_data_all[37];
				var machine_no_id=barcode_data_all[38];
				var entry_form=barcode_data_all[39];
				var book_without_order=barcode_data_all[40];
				var rollMstId=barcode_data_all[41];
				var bookingNo_fab=barcode_data_all[42];
				var amount=barcode_data_all[43];
				
				var from_book_without_order=barcode_data_all[44];
				var floor_id=barcode_data_all[45];
				var room_id=barcode_data_all[46];
				var internal_ref=barcode_data_all[47];
				var bin_box_id=barcode_data_all[48];

				var multi_floor=barcode_data_all[49];
				var multi_room=barcode_data_all[50];
				var multi_rack=barcode_data_all[51];
				var multi_self=barcode_data_all[52];
				var multi_bin=barcode_data_all[53];
				var up_to_po_id=barcode_data_all[54];
				var up_to_po_no=barcode_data_all[55];
				var to_body_part_id=barcode_data_all[56];
				var to_job=barcode_data_all[57];
				var to_ord_book_no=barcode_data_all[58];
				var to_ord_book_id=barcode_data_all[59];

				
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
				else if(barcode_data_all[0]==-1)
				{
					alert('Sorry! Barcode Already Scanned. Id : '+barcode_data_all[1]);
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Sorry! Barcode Already Scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return; 
				}
				else if(barcode_data_all[0]==30)
				{
					alert('Barcode not found ');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Sorry! Barcode Already Scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return; 
				}
				//alert(company_id);
				if(company_id != cbo_company_id)
				{
					alert(company_id+'Multiple company not allowed '+cbo_company_id);
					return;
				}

				// if (requi_system_id !="") 
				// {
				// 	// alert(txt_from_order_id +'!='+ po_breakdown_id) +'||'+ (txt_from_product +'!='+ prod_id);
				// 	if( (txt_from_order_id != po_breakdown_id) || (txt_from_product != prod_id))
				// 	{
				// 		alert('Multiple Order and Fabrication not allowed');
				// 		return;
				// 	}
				// }

				if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
				{ 
					alert('Sorry! Barcode Already Scanned.'); 
					$('#txt_bar_code_num').val('');
					return; 
				}
				//alert(barcode_data);return;		
				
				var bar_code_no=$('#barcodeNo_'+row_num).val();
				
				if(bar_code_no!="")
				{
					row_num++;
					$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
					{
						$(this).attr({ 
							'id': function(_, id) 
							{ 
							  	var id=id.split("_"); 
							  	//return id[0] +"_"+ row_num 
							  	if(id.length>3){
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
							  	return "change_floor(this.value,this.id);";
							}              
						});
					}).end().prependTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:first").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr__'+row_num);//decrease_1
					$("#scanning_tbl tbody tr:first").find('input','select').val("");
					$("#decrease_"+row_num).val("-");
					
				}
				
				scanned_barcode.push(barcode_no);
				//batch_batcode_arr.push(bar_code);
				
				$("#sl_"+row_num).text(row_num);
				$("#fromStore_"+row_num).text(store_name);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#hideBodyPartForUpdate_"+row_num).val(to_body_part_id);
				$("#cons_"+row_num).text(compsition_description);
				$("#batch_"+row_num).text(batch_no);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');
				$("#rollWeight_"+row_num).text(qnty);
				$("#buyer_"+row_num).text(buyer_name);
				$("#booking_"+row_num).text(bookingNo_fab);
				
				if(from_book_without_order==1)
				{
					$("#job_"+row_num).text("");
					$("#order_"+row_num).text("");
					$("#intRef_"+row_num).text("");
				}
				else
				{
					$("#job_"+row_num).text(job_no);
					$("#order_"+row_num).text(po_number);
					$("#intRef_"+row_num).text(internal_ref);
				}
				if (requi_system_id !="")
				{
					$("#txt_from_product").val(prod_id);
					$("#txt_from_order_id").val(po_breakdown_id);
				}

				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#basis_"+row_num).text(receive_basis);
				$("#progBookPiNo_"+row_num).text(booking_no);

				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#batchNo_"+row_num).val(batch_no);
				$("#batchId_"+row_num).val(batch_id);
				$("#colorId_"+row_num).val(color_id);
				$("#brandId_"+row_num).val(brand_id);
				
				$("#fromBodyPart_"+row_num).val(body_part_id);
				
				$("#fromStoreId_"+row_num).val(store_id);
				$("#floor_"+row_num).val(floor_id);
				$("#room_"+row_num).val(room_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#bin_"+row_num).val(bin_box_id);
				
				$("#txtToOrder_"+row_num).val(up_to_po_no);
				$("#toOrderId_"+row_num).val(up_to_po_id);
				$("#tobookingNo_"+row_num).val(to_ord_book_no);
				$("#toBookingMstId_"+row_num).val(to_ord_book_id);
				$("#toJob_"+row_num).text(to_job);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#knitDetailsId_"+row_num).val(dtls_id);
				$("#transferEntryForm_"+row_num).val(entry_form);
				
				if(cbo_transfer_criteria == 2)
				{
					$("#bookWithoutOrder_"+row_num).val(from_book_without_order);
				}
				else
				{
					$("#bookWithoutOrder_"+row_num).val("0");
				}
				$("#rollMstId_"+row_num).val(rollMstId);
				$("#rollAmount_"+row_num).val(amount);
				$("#cons_"+row_num).prop("title","prod id = "+prod_id);
				$("#fromBookingWithoutOrder_"+row_num).val(from_book_without_order);	
				$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
				$('#txtToOrder_'+row_num).removeAttr("disabled","").removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
				$('#txtToOrder_'+row_num).attr("disabled","disabled");
				
				$("#cboToBodyPart_"+row_num).removeAttr('onchange').attr("onchange","copyBodyPart(this.id,this.value);");  

				$("#cboFloorTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_room(this.value,"+row_num+"); copy_all('"+row_num+"_0'); reset_room_rack_shelf("+row_num+",'cbo_floor_to');"); 
				$("#cboRoomTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_rack(this.value,"+row_num+"); copy_all('"+row_num+"_1'); reset_room_rack_shelf("+row_num+",'cbo_room_to');"); 
				$("#txtRackTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_shelf(this.value,"+row_num+"); copy_all('"+row_num+"_2'); reset_room_rack_shelf("+row_num+",'txt_rack_to');"); 
				$("#txtShelfTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_bin(this.value,"+row_num+"); copy_all('"+row_num+"_3');"); 
				$("#txtBinTo_"+row_num).removeAttr('onchange').attr("onchange","copy_all('"+row_num+"_4');"); 

				$('#cboToBodyPart_'+row_num).html('<option value="'+0+'">--Select--</option>');

				if(multi_room !=0)
				{
					var multi_room = multi_room.split(",");
					var selectbox = $("#cboRoomTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_room.length; j++)
					{
					    list += "<option value='" +multi_room[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_room[j]]+ "</option>";
					}
					selectbox.html(list);
				}
				
				if(multi_rack !=0)
				{
					var multi_rack = multi_rack.split(",");
					var selectbox = $("#txtRackTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_rack.length; j++)
					{
					    list += "<option value='" +multi_rack[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_rack[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				if(multi_self !=0)
				{
					var multi_self = multi_self.split(",");
					var selectbox = $("#txtShelfTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_self.length; j++)
					{
					    list += "<option value='" +multi_self[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_self[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				if(multi_bin !=0)
				{
					var multi_bin = multi_bin.split(",");
					var selectbox = $("#txtBinTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_bin.length; j++)
					{
					    list += "<option value='" +multi_bin[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_bin[j]]+ "</option>";
					}
					selectbox.html(list);
				}
				
				$("#cboFloorTo_"+row_num).val(floor_id); 
				$("#cboRoomTo_"+row_num).val(room_id); 
				$("#txtRackTo_"+row_num).val(rack); 
				$("#txtShelfTo_"+row_num).val(self); 
				$("#txtBinTo_"+row_num).val(bin_box_id); 

				$('#txt_tot_row').val(row_num);
				$('#txt_bar_code_num').val('');
				$('#txt_bar_code_num').focus();	

				if(cbo_transfer_criteria == 1 || cbo_transfer_criteria == 4)
				{
					$("#txtToOrder_"+row_num).val(up_to_po_no);
				}
				$("#toOrderId_"+row_num).val(up_to_po_id);

				
				if(cbo_transfer_criteria == 2)
				{
					$('#cboToBodyPart_'+row_num).append('<option value="'+body_part_id+'" selected>'+body_part_name+'</option>');
				}
				else if(cbo_transfer_criteria == 1 || cbo_transfer_criteria == 4)
				{
					
					if($("#bookWithoutOrder_"+row_num).val() == 0){
						all_to_order_str +=up_to_po_id+',';
					}

					if($("#bookWithoutOrder_"+row_num).val() == 1){
						all_sample_non_order_booking +=up_to_po_id+',';
					}
				}
				else
				{
					if(cbo_transfer_criteria == 2 && from_book_without_order==1)
					{
						all_sample_non_order_booking += po_breakdown_id + ',';
					}

					if(cbo_transfer_criteria == 2 && from_book_without_order==0)
					{
						all_to_order_str += po_breakdown_id + ',';
					}
				}

			}

			
			if(all_sample_non_order_booking !=""){
				load_body_part_sample_wise(all_sample_non_order_booking);
			}
			if(all_to_order_str !=""){
				load_body_part_order_wise(all_to_order_str);
			}

			//alert(job_no);return;
		}
		else  // Update Event
		{
			var barcode_data=return_global_ajax_value( system_id, 'populate_barcode_data_update', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
			//alert(barcode_data); return;
			var barcode_data_all=new Array();
			var barcode_data_ref=new Array();
			var all_to_order_str = ""; var all_sample_non_order_booking="";
			barcode_data_ref=barcode_data.split("__");
			for(var k=0; k<barcode_data_ref.length; k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");
				var rcv_id=barcode_data_all[0];
				var company_id=barcode_data_all[1];
				var body_part_name=barcode_data_all[2];
				var receive_basis=barcode_data_all[3];
				var receive_basis_id=barcode_data_all[4];
				var receive_date=barcode_data_all[5];
				var booking_no=barcode_data_all[6];
				var booking_id=barcode_data_all[7];
				var color=barcode_data_all[8];
				var knitting_source_id=barcode_data_all[9];
				var knitting_source=barcode_data_all[10];
				var store_id=barcode_data_all[11];
				var knitting_company_id=barcode_data_all[12];
				var batch_no=barcode_data_all[13];
				var batch_id=barcode_data_all[14];
				var rack=barcode_data_all[16];
				var self=barcode_data_all[17];
				var knitting_company_name=barcode_data_all[18];
				var dtls_id=barcode_data_all[19];
				var prod_id=barcode_data_all[20];
				var febric_description_id=barcode_data_all[21];
				var compsition_description=barcode_data_all[22];
				var gsm=barcode_data_all[23];
				var width=barcode_data_all[24];
				var roll_id=barcode_data_all[25];
				var roll_no=barcode_data_all[26];
				var po_breakdown_id=barcode_data_all[27];
				var qnty=barcode_data_all[28];
				var barcode_no=barcode_data_all[29];
				var job_no=barcode_data_all[30];
				var buyer_id=barcode_data_all[31];
				var color_id=barcode_data_all[34];
				var buyer_name=barcode_data_all[32];
				var po_number=barcode_data_all[33];
				var store_name=barcode_data_all[35];
				var body_part_id=barcode_data_all[36];
				var brand_id=barcode_data_all[37];
				var machine_no_id=barcode_data_all[38];
				var entry_form=barcode_data_all[39];
				var book_without_order=barcode_data_all[40];
				var up_roll_id=barcode_data_all[41];
				var up_dtls_id=barcode_data_all[42];
				var up_trans_id=barcode_data_all[43];
				var up_to_trans_id=barcode_data_all[44];
				var up_to_po_no=barcode_data_all[45];
				var up_to_po_id=barcode_data_all[46];
				var barcode_for_issue=barcode_data_all[47];
				var rollMstId=barcode_data_all[48];
				var bookingNo_fab=barcode_data_all[49];
				var rollAmount=barcode_data_all[50];
				var fromProductUp=barcode_data_all[51]; 
				var from_book_without_order=barcode_data_all[52];
				var splited_barcode=barcode_data_all[53];
				//var requ_dtls_id=barcode_data_all[56];
				var to_floor_id=barcode_data_all[54];
				var to_room_id=barcode_data_all[55];
				var to_rack_id=barcode_data_all[56];
				var to_self_id=barcode_data_all[57];
				var floor_id=barcode_data_all[58];
				var room_id=barcode_data_all[59];

				var internal_ref=barcode_data_all[61];
				var bin_box_id=barcode_data_all[62];
				var to_bin_box_id=barcode_data_all[63];
				var to_job=barcode_data_all[64];
				var to_batch_id=barcode_data_all[65];
				var to_body_part_id=barcode_data_all[66];
				var transfer_qnty=barcode_data_all[67];
				var to_ord_book_id=barcode_data_all[68];
				var to_ord_book_no=barcode_data_all[69];

				var multi_floor=barcode_data_all[70];
				var multi_room=barcode_data_all[71];
				var multi_rack=barcode_data_all[72];
				var multi_self=barcode_data_all[73];
				var multi_bin=barcode_data_all[74];
				
				/*var floor_name=jsfloor_room_rack_shelf_name_array[floor_id];
				var room_name=jsfloor_room_rack_shelf_name_array[to_room_id];
				var rack_name=jsfloor_room_rack_shelf_name_array[rack];
				var shelf_name=jsfloor_room_rack_shelf_name_array[self];*/
				if(company_id != cbo_company_id)
				{
					alert('Multiple company not allowed');
					return;
				}

				var bar_code_no=$('#barcodeNo_'+row_num).val();
				if(bar_code_no!="")
				{
					row_num++;
					$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
					{
						$(this).attr({ 
						  /*'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
						  'value': function(_, value) { return value } */         
						  	'id': function(_, id) 
							{ 
							  	var id=id.split("_"); 
							  	//return id[0] +"_"+ row_num 
							  	if(id.length>3){
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
							  	return "change_floor(this.value,this.id);";
							}    
						});
					}).end().prependTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:first").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr__'+row_num);
				}
				
				//alert(book_without_order+"=="+job_no+"=="+po_number+"=="+po_breakdown_id);
				$("#sl_"+row_num).text(row_num);
				$("#fromStore_"+row_num).text(store_name);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');
				$("#rollWeight_"+row_num).text(qnty);
				$("#buyer_"+row_num).text(buyer_name);
				$("#booking_"+row_num).text(bookingNo_fab);
				
				if(from_book_without_order==1)
				{
					$("#job_"+row_num).text("");
					$("#order_"+row_num).text("");
					$("#intRef_"+row_num).text("");
				}
				else
				{
					$("#job_"+row_num).text(job_no);
					$("#order_"+row_num).text(po_number);
					$("#intRef_"+row_num).text(internal_ref);
				}
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#basis_"+row_num).text(receive_basis);
				$("#progBookPiNo_"+row_num).text(booking_no);
				
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				
				$("#batchNo_"+row_num).val(batch_no);
				$("#batch_"+row_num).text(batch_no);
				$("#batchId_"+row_num).val(batch_id);
				$("#colorId_"+row_num).val(color_id);	
				$("#brandId_"+row_num).val(brand_id);	
				
				$("#fromBodyPart_"+row_num).val(body_part_id);
				$("#hideBodyPartForUpdate_"+row_num).val(to_body_part_id);	
						
				$("#fromStoreId_"+row_num).val(store_id);
				$("#floor_"+row_num).val(floor_id);
				$("#room_"+row_num).val(room_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#bin_"+row_num).val(bin_box_id);
			
				//company to company transfer
				if(cbo_transfer_criteria == 1 || cbo_transfer_criteria == 4)
				{
					$("#txtToOrder_"+row_num).val(up_to_po_no);
				}
				$("#toOrderId_"+row_num).val(up_to_po_id);
				$("#toBookingMstId_"+row_num).val(to_ord_book_id);
				$("#tobookingNo_"+row_num).val(to_ord_book_no);
				$("#toJob_"+row_num).text(to_job);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#knitDetailsId_"+row_num).val(dtls_id);
				$("#transferEntryForm_"+row_num).val(entry_form);
				
				$("#previousToBbatchId_"+row_num).val(to_batch_id);
				$("#hiddenTransferqnty_"+row_num).val(transfer_qnty);
				//$("#bookWithoutOrder_"+row_num).val(book_without_order);
				
				if(cbo_transfer_criteria == 2)
				{
					$("#bookWithoutOrder_"+row_num).val(from_book_without_order);
				}
				else
				{
					$("#bookWithoutOrder_"+row_num).val("0");
				}

				$("#fromBookingWithoutOrder_"+row_num).val(from_book_without_order);
				
				// if (to_floor_id!=0) 
				// {
				// 	change_floor(to_floor_id,"cboFloorTo_"+row_num);
				// }
				// if (to_room_id!=0) 
				// {
				// 	change_room(to_room_id,"cboRoomTo_"+row_num);
				// }
				// if (to_rack_id!=0) 
				// {
				// 	change_rack(to_rack_id,"txtRackTo_"+row_num);
				// }
				// if (to_self_id!=0) 
				// {
				// 	change_shelf(to_self_id,"txtShelfTo_"+row_num);
				// }

				if(multi_room !=0)
				{
					var multi_room = multi_room.split(",");
					var selectbox = $("#cboRoomTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_room.length; j++)
					{
					    list += "<option value='" +multi_room[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_room[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				if(multi_rack !=0)
				{
					var multi_rack = multi_rack.split(",");
					var selectbox = $("#txtRackTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_rack.length; j++)
					{
					    list += "<option value='" +multi_rack[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_rack[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				if(multi_self !=0)
				{
					var multi_self = multi_self.split(",");
					var selectbox = $("#txtShelfTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_self.length; j++)
					{
					    list += "<option value='" +multi_self[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_self[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				if(multi_bin !=0)
				{
					var multi_bin = multi_bin.split(",");
					var selectbox = $("#txtBinTo_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < multi_bin.length; j++)
					{
					    list += "<option value='" +multi_bin[j]+ "'>" +jsfloor_room_rack_shelf_name_array[multi_bin[j]]+ "</option>";
					}
					selectbox.html(list);
				}

				$("#cboToBodyPart_"+row_num).removeAttr('onchange').attr("onchange","copyBodyPart(this.id,this.value);"); 
				$("#cboFloorTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_room(this.value,"+row_num+"); copy_all('"+row_num+"_0'); reset_room_rack_shelf("+row_num+",'cbo_floor_to');"); 
				$("#cboRoomTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_rack(this.value,"+row_num+"); copy_all('"+row_num+"_1'); reset_room_rack_shelf("+row_num+",'cbo_room_to');"); 
				$("#txtRackTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_shelf(this.value,"+row_num+"); copy_all('"+row_num+"_2'); reset_room_rack_shelf("+row_num+",'txt_rack_to');"); 
				$("#txtShelfTo_"+row_num).removeAttr('onchange').attr("onchange","fn_load_bin(this.value,"+row_num+"); copy_all('"+row_num+"_3');"); 
				$("#txtBinTo_"+row_num).removeAttr('onchange').attr("onchange","copy_all('"+row_num+"_4');"); 


				$("#cboFloorTo_"+row_num).val(to_floor_id);
				$("#cboRoomTo_"+row_num).val(to_room_id);
				$("#txtRackTo_"+row_num).val(to_rack_id);
				$("#txtShelfTo_"+row_num).val(to_self_id);				
				$("#txtBinTo_"+row_num).val(to_bin_box_id);				
				
				$("#dtlsId_"+row_num).val(up_dtls_id);
				$("#transId_"+row_num).val(up_trans_id);
				$("#transIdTo_"+row_num).val(up_to_trans_id);
				$("#rolltableId_"+row_num).val(up_roll_id);
				$("#barcodeIssue_"+row_num).val(barcode_for_issue);
				$("#rollMstId_"+row_num).val(rollMstId);
				$("#rollAmount_"+row_num).val(rollAmount);
				$("#fromProductUp_"+row_num).val(fromProductUp);

				$("#cons_"+row_num).prop("title","prod id = "+prod_id+", from prod id = "+fromProductUp);

				
					if (barcode_no==barcode_for_issue ) 
					{
						$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_message("+row_num+");");
						$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");

					}
					else if(barcode_no==splited_barcode)
					{
						$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_message_split("+row_num+");");
						$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
					}
					else
					{
						$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
						$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
					}
				// }

				$('#txt_tot_row').val(row_num);
				$('#txt_bar_code_num').val('');
				$('#txt_bar_code_num').focus();
				
				/*
				|--------------------------------------------------------------------------
				| for floor, room, rack and shelf disable
				|--------------------------------------------------------------------------
				|
				*/
				var isFloorRoomRackShelfDisable=barcode_data_all[60];
				//issue or other's fund
				if(isFloorRoomRackShelfDisable == 1)
				{
					$("#cboFloorTo_"+row_num).attr('disabled','disabled');
					$("#cboRoomTo_"+row_num).attr('disabled','disabled')
					$("#txtRackTo_"+row_num).attr('disabled','disabled')
					$("#txtShelfTo_"+row_num).attr('disabled','disabled')
					$("#txtBinTo_"+row_num).attr('disabled','disabled')
				}
				else
				{
					$("#cboFloorTo_"+row_num).removeAttr('disabled');
					$("#cboRoomTo_"+row_num).removeAttr('disabled');
					$("#txtRackTo_"+row_num).removeAttr('disabled');
					$("#txtBinTo_"+row_num).removeAttr('disabled');
				}
				
				if(cbo_transfer_criteria == 2)
				{
					$('#cboToBodyPart_'+row_num).html('<option value="'+0+'">Select</option>');
					$('#cboToBodyPart_'+row_num).append('<option value="'+body_part_id+'" selected>'+body_part_name+'</option>');
				}
				else
				{
					
					if($("#bookWithoutOrder_"+row_num).val() == 0){
						all_to_order_str +=up_to_po_id+',';
					}

					if($("#bookWithoutOrder_"+row_num).val() == 1){
						all_sample_non_order_booking +=up_to_po_id+',';
					}
				}
			}
			
			if(all_to_order_str !=""){
				load_body_part_order_wise(all_to_order_str);
			}
			
			if(all_sample_non_order_booking !="")
			{
				load_body_part_sample_wise(all_sample_non_order_booking);
			}

		}
		calculate_total();
	}

	function load_body_part_order_wise(order_ids)
	{
		var bodypart_result = return_global_ajax_value(order_ids, 'bodypart_list_order_wise', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
		var JSONObject = JSON.parse(bodypart_result);

		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var fromBookingWithoutOrder = $(this).find('input[name="fromBookingWithoutOrder[]"]').val();
			

			var toOrderIdArr = $(this).find('input[name="toOrderId[]"]').attr("id");
			var toOrderIdArr = toOrderIdArr.split("_");

			if(cbo_transfer_criteria == 2)
			{
				var toOrder_id = $(this).find('input[name="orderId[]"]').val();
			}else{
				var toOrder_id = $(this).find('input[name="toOrderId[]"]').val();
			}
			
			//alert(cbo_transfer_criteria + '_' + fromBookingWithoutOrder);
			if( (cbo_transfer_criteria == 2  && fromBookingWithoutOrder ==0) || cbo_transfer_criteria != 2)
			{	
				//alert('he');
				$(this).find('select[name="cboToBodyPart[]"]').html('<option value="'+0+'">Select</option>');
		 		for (var key of Object.keys(JSONObject[toOrder_id]).sort())
				{
	 				$(this).find('select[name="cboToBodyPart[]"]').append('<option value="'+key+'">'+JSONObject[toOrder_id][key]+'</option>');
				};
			}

			var to_body_part_id = $("#hideBodyPartForUpdate_"+toOrderIdArr[1]*1).val();
			$("#cboToBodyPart_"+toOrderIdArr[1]*1).val(to_body_part_id);

		});
	}

	function load_body_part_sample_wise(order_ids)
	{
		var bodypart_result = return_global_ajax_value(order_ids, 'bodypart_list_sample_wise', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
		var JSONObject = JSON.parse(bodypart_result);

		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var fromBookingWithoutOrder = $(this).find('input[name="fromBookingWithoutOrder[]"]').val();
			var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();

			var toOrderIdArr = $(this).find('input[name="toOrderId[]"]').attr("id");
			var toOrderIdArr = toOrderIdArr.split("_");

			if(cbo_transfer_criteria == 2)
			{
				var toOrder_id = $(this).find('input[name="orderId[]"]').val();
			}else{
				var toOrder_id = $(this).find('input[name="toOrderId[]"]').val();
			}

			//alert(cbo_transfer_criteria + '_' + fromBookingWithoutOrder);
			if( (cbo_transfer_criteria == 2  && fromBookingWithoutOrder ==1) || cbo_transfer_criteria != 2)
			{	
				//alert(barcodeNo + '_' + toOrder_id);
				$(this).find('select[name="cboToBodyPart[]"]').html('<option value="'+0+'">Select</option>');
		 		for (var key of Object.keys(JSONObject[toOrder_id]).sort())
				{
	 				$(this).find('select[name="cboToBodyPart[]"]').append('<option value="'+key+'">'+JSONObject[toOrder_id][key]+'</option>');
				};
			}

			var to_body_part_id = $("#hideBodyPartForUpdate_"+toOrderIdArr[1]*1).val();
			$("#cboToBodyPart_"+toOrderIdArr[1]*1).val(to_body_part_id);
		});
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
		}
	});
	
	function add_dtls_data( data )
	{
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var trans_id=datas[2];
			var to_trans_id=datas[3];
			var roll_table_id=datas[4];
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_trnasId_array[barcode_no] = trans_id;
			barcode_trnasId_to_array[barcode_no] = to_trans_id;
			barcode_rollTableId_array[barcode_no] = roll_table_id;
		}
		
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			
			if(dtlsId=="") 
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="transId[]"]').val(barcode_trnasId_array[barcodeNo]);
				$(this).find('input[name="transIdTo[]"]').val(barcode_trnasId_to_array[barcodeNo]);	
				$(this).find('input[name="rolltableId[]"]').val(barcode_rollTableId_array[barcodeNo]);	
			}
		});
	}

	function fn_message(rowId)
	{
		alert("Barcode already used for Issue");
		return;
	}

	function fn_message_split(rowId)
	{
		alert("Split Reference Found.");
		return;
	}

	function fn_deleteRow( rid )
	{
		var bar_code =$("#barcodeNo_"+rid).val();

				
		var num_row =$('#scanning_tbl tbody tr').length;
		var rolltableId =$("#rolltableId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var txt_deleted_barcode=$('#txt_deleted_barcode').val();
		var txt_deleted_trnsf_dtls_id=$('#txt_deleted_trnsf_dtls_id').val();
		var trnsf_dtlsId =$("#dtlsId_"+rid).val();

		var txt_deleted_trans_id=$('#txt_deleted_trans_id').val();
		var transaction_dtlsId =$("#transIdTo_"+rid).val();
		var transaction_fromId =$("#transId_"+rid).val();

		var txt_deleted_prod_id=$('#txt_deleted_prod_id').val();
		var production_dtlsId =$("#productId_"+rid).val();

		var txt_deleted_prod_qty=$('#txt_deleted_prod_qty').val();
		var deleted_production_qty =$("#rollWgt_"+rid).val();

		var deleted_production_amount =$("#rollAmount_"+rid).val();
		var txt_up_prod_id =$("#fromProductUp_"+rid).val();
		var txt_roll_source_id =$("#rollMstId_"+rid).val();
		var txt_deleted_source_roll_id = $('#txt_deleted_source_roll_id').val();
		
		if(num_row==1)
		{
			$('#tr__'+rid+' td:not(:last-child)').each(function(index, element) {
				$(this).html('');
			});
			
			$('#tr__'+rid).find(":input:not(:button)").val('');
		}
		else
		{
			$("#tr__"+rid).remove();
		}
		
		var selected_id='';var selected_barcode='';var selected_trnsf_dtls_id='';var selected_trans_dtls_id='';var selected_prod_dtls_id='';var selected_prod_qty=''; var deleted_source_roll_id = "";
		if(rolltableId!='')
		{
			if(txt_deleted_id=='') selected_id=rolltableId; else selected_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val( selected_id );

			if(txt_deleted_barcode=='') selected_barcode=bar_code; else selected_barcode=txt_deleted_barcode+','+bar_code;
			$('#txt_deleted_barcode').val( selected_barcode );

			if(txt_deleted_trnsf_dtls_id=='') selected_trnsf_dtls_id=trnsf_dtlsId; else selected_trnsf_dtls_id=txt_deleted_trnsf_dtls_id+','+trnsf_dtlsId;
			$('#txt_deleted_trnsf_dtls_id').val( selected_trnsf_dtls_id );

			if(txt_deleted_trans_id=='') selected_trans_dtls_id=transaction_fromId + ',' + transaction_dtlsId; else selected_trans_dtls_id=txt_deleted_trans_id+','+transaction_fromId + ',' + transaction_dtlsId;
			$('#txt_deleted_trans_id').val( selected_trans_dtls_id );

			if(txt_deleted_prod_id=='') selected_prod_dtls_id=production_dtlsId; else selected_prod_dtls_id=txt_deleted_prod_id+','+production_dtlsId;
			$('#txt_deleted_prod_id').val( selected_prod_dtls_id );


			if(txt_deleted_prod_qty=='') selected_prod_qty=production_dtlsId+'='+deleted_production_qty+'='+deleted_production_amount+'='+txt_up_prod_id; else selected_prod_qty=txt_deleted_prod_qty+','+production_dtlsId+'='+deleted_production_qty+'='+deleted_production_amount+'='+txt_up_prod_id;
			$('#txt_deleted_prod_qty').val( selected_prod_qty );

			if(txt_deleted_source_roll_id=='') deleted_source_roll_id=txt_roll_source_id; else deleted_source_roll_id=txt_deleted_source_roll_id+','+txt_roll_source_id;
			$('#txt_deleted_source_roll_id').val( deleted_source_roll_id );

		}
		
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
		calculate_total();
	}
	
	function opneToOrder(str)
	{
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var txt_requisition_basis = $('#txt_requisition_basis').val();
		if(cbo_transfer_criteria==2)
		{
			alert("Store to Store basis Order Selection not allowed.");
			return;
		}
		var cbo_to_company_id = $('#cbo_to_company_id').val();
		if (form_validation('cbo_to_company_id','Company')==false)
		{
			return;
		}
		else
		{

			var item_desc_ids='';var item_gsm=""; var item_dia="";var color_id=""; var requisition_id="";
			if (txt_requisition_basis==1) 
			{
				requisition_id=$('#txt_requisition_id').val();
				// alert(requisition_id);
				var j=0;
				$("#scanning_tbl").find('tbody tr').each(function()
				{
					var febDescripId=$(this).find('input[name="febDescripId[]"]').val();
					var gsm=$(this).find('input[name="prodGsm[]"]').val();
					var diaWidth=$(this).find('input[name="diaWidth[]"]').val();
					var diaWidth=$(this).find('input[name="diaWidth[]"]').val();
					var colorID=$(this).find('input[name="colorId[]"]').val();
					
					j++;
					item_desc_ids+=febDescripId+',';
					item_gsm+=gsm+',';
					item_dia+=diaWidth+',';
					color_id+=colorID+',';
				});
				item_desc_ids = item_desc_ids.replace(/,\s*$/, ""); // remove last comma
				item_gsm = item_gsm.replace(/,\s*$/, ""); // remove last comma
				item_dia = item_dia.replace(/,\s*$/, ""); // remove last comma
				color_id = color_id.replace(/,\s*$/, ""); // remove last comma
				var item_desc_ids = item_desc_ids.split(",");
				var item_gsm = item_gsm.split(",");
				var item_dia = item_dia.split(",");
				var color_id = color_id.split(",");
				// alert(item_gsm);
				function onlyUnique(value, index, self) {
					return self.indexOf(value) === index;
				}
				var item_desc_ids = item_desc_ids.filter(onlyUnique);
				var item_gsm = item_gsm.filter(onlyUnique);
				var item_dia = item_dia.filter(onlyUnique);
				var color_id = color_id.filter(onlyUnique);
				// alert(uniqueitem_gsm);
			}


			var page_link='requires/roll_wise_finish_fabric_transfer_entry_controller.php?cbo_to_company_id='+cbo_to_company_id+'&action=to_order_popup'+'&item_desc_ids='+item_desc_ids+'&item_gsm='+item_gsm+'&item_dia='+item_dia+'&color_id='+color_id+'&txt_requisition_basis='+txt_requisition_basis+'&requisition_id='+requisition_id;
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var order_ref=this.contentDoc.getElementById("order_id").value.split("_"); 
				var order_id=order_ref[0];
				var order_no=order_ref[1];
				var job_no=order_ref[2];
				var booking_no=order_ref[3];
				var booking_mst_id=order_ref[4];
				//alert(booking_mst_id);
				if(order_no!="")
				{
					$('#toOrderId_'+str).val(order_id);
					$('#txtToOrder_'+str).val(order_no);
					$('#toJob_'+str).text(job_no);
					$('#tobookingNo_'+str).val(booking_no);
					$('#toBookingMstId_'+str).val(booking_mst_id);
					var first_tr=str-1;
					for(var k=first_tr; k>=1; k--)
					{
						$("#toOrderId_"+k).val(order_id);
						$("#txtToOrder_"+k).val(order_no);
						$("#toJob_"+k).text(job_no);
						$("#tobookingNo_"+k).val(booking_no);
						$("#toBookingMstId_"+k).val(booking_mst_id);
					}
				}

				if(order_id!="")
				{
					var bodypart_result = return_global_ajax_value(order_id, 'bodypart_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
					var JSONObject = JSON.parse(bodypart_result);

					$("#scanning_tbl").find('tbody tr').each(function()
					{
						var bodyPartId = $(this).find('select[name="cboToBodyPart[]"]').attr("id");
						var bodyPartIdSlArr = bodyPartId.split("_");
						// copy only that and below selected data
						if( str >= bodyPartIdSlArr[1]*1 )
						{
							$(this).find('select[name="cboToBodyPart[]"]').html('<option value="'+0+'">Select</option>');
							for (var key of Object.keys(JSONObject).sort())
							{
								$(this).find('select[name="cboToBodyPart[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
							};
						}
					});
				}
			}
		}
	}

	function copyBodyPart(id,value)
	{
		var idArr = id.split("_");
		var sl = idArr[1]*1;
		if($('#bodyPartIds').is(':checked'))
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var bodyPartId = $(this).find('select[name="cboToBodyPart[]"]').attr("id");
				var bodyPartIdSlArr = bodyPartId.split("_");
				// copy only that and below selected data
				if( sl >= bodyPartIdSlArr[1]*1 )
				{
					$("#cboToBodyPart_"+bodyPartIdSlArr[1]*1).val(value);
				}
			});
		}		
	}
	
	function load_room_rack_self()
	{
		return load_room_rack_self_bin('requires/roll_wise_finish_fabric_transfer_entry_controller*13*cboRoomTo_1*1', 'room',roomTdTo_1, document.getElementById('cbo_to_company_id').value,'',document.getElementById('cbo_store_name').value,document.getElementById('cboFloorTo_1').value,'','','','','','50','H');
	}

	function fnc_reset_form(source) // Inserted Data Show and Reset Data
	{
		// alert(source);1 system id Browse

		if(source==3)
		{
			if($('#txt_transfer_no').val() != ""){
				page_reload();
				return;
			}
			else
			{
				source=2;  //Only Details part reset
			}
		}


		$('#scanning_tbl tbody tr').remove();
		if(source!=2)
		{
			$('#txt_transfer_no').val('');
			$('#update_id').val('');
			$('#cbo_transfer_criteria').val(0);
			$('#cbo_company_id').val(0);
			$('#cbo_to_company_id').val(0);
			$('#cbo_to_company_id').attr('disabled',true);
			
			
			$('#txt_transfer_date').val('');
			$('#txt_challan_no').val('');
			$('#cbo_store_name').val(0);
		}
			$('#txt_deleted_id').val('');
			$('#txt_deleted_barcode').val('');
			$('#txt_deleted_trnsf_dtls_id').val('');
			$('#txt_deleted_trans_id').val('');
			$('#txt_deleted_prod_id').val('');
			$('#txt_deleted_prod_qty').val('');
			$('#txt_deleted_source_roll_id').val('');
		
		$('#txt_tot_row').val(1);

		fnc_details_row_blank();
		//$("#scanning_tbl tbody").html(html);	
	}

	function active_inactive(str) // Onchange Transfer Criteria
	{
		$('#cbo_to_company_id').val(0);
		$('#cbo_company_id').val(0);
		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller',$('#cbo_transfer_criteria').val()+'_'+str, 'load_drop_store_balnk', 'store_td' );
		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller',$('#cbo_transfer_criteria').val(), 'load_drop_to_com', 'cra_124' );
		//console.log(str);
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

		fnc_details_row_blank();
	}

	


	function fnc_details_row_blank()
	{
		var html='<tr id="tr__1" align="center" valign="middle"><td width="20" id="sl_1"></td></td><td width="70" id="barcode_1"></td><td width="45" id="roll_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="150" id="cons_1" title="" ></td><td style="word-break:break-all;" width="150" id="batch_1" align="left" title=""></td><td style="word-break:break-all;" width="40" id="gsm_1"></td><td style="word-break:break-all;" width="40" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td width="60" id="rollWeight_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="120" id="booking_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="intRef_1"></td><td style="word-break:break-all;" width="80" id="order_1" align="left"></td><td style="word-break:break-all;" width="100" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="basis_1"></td><td width="90" id="toOrder_1" placeholder="Browse For Order"><input type="text"  class="text_boxes" id="txtToOrder_1" name="txtToOrder[]" style="width:60px;" onDblClick="opneToOrder(1)" placeholder="Browse For Order" readonly/></td><td width="90" align="center" id="toBodyPartTd_1"><? echo create_drop_down( "cboToBodyPart_1", 90,$blank_array,"", 1, "--Select--", 0, "copyBodyPart(this.id,this.value)",0,"","","","","","","cboToBodyPart[]","onchange_void" ); ?></td><td style="word-break:break-all;" width="80" id="toJob_1"></td><td width="50" align="center" id="floor_td_to" class="floor_td_to"><? echo create_drop_down( "cboFloorTo_1", 50,$blank_array,"", 1, "--Select--", 0, "change_floor(this.value,this.id);",0,"","","","","","","cboFloorTo[]","onchange_void"); ?></td><td width="50" align="center" id="roomTdTo_1"><? echo create_drop_down( "cboRoomTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboRoomTo[]","onchange_void" ); ?></td><td width="50" align="center" id="rackTdTo_1"><? echo create_drop_down( "txtRackTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtRackTo[]","onchange_void" ); ?></td><td width="50" align="center" id="shelfTdTo_1"><? echo create_drop_down( "txtShelfTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtShelfTo[]","onchange_void" ); ?></td><td width="50" align="center" id="binTdTo_1"><? echo create_drop_down( "txtBinTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtBinTo[]","onchange_void" ); ?></td><td style="word-break:break-all;" width="115" id="progBookPiNo_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="batchNo[]" id="batchNo_1"/><input type="hidden" name="batch_id[]" id="batchId_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="floor[]" id="floor_1"/><input type="hidden" name="room[]" id="room_1"/><input type="hidden" name="rack[]" id="rack_1"/><input type="hidden" name="shelf[]" id="shelf_1"/><input type="hidden" name="bin[]" id="bin_1"/><input type="hidden" name="fromStoreId[]" id="fromStoreId_1"/><input type="hidden" name="toOrderId[]" id="toOrderId_1"/><input type="hidden" name="tobookingNo[]" id="tobookingNo_1"/><input type="hidden" name="previousToBbatchId[]" id="previousToBbatchId_1"/><input type="hidden" name="toBookingMstId[]" id="toBookingMstId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="requiDtlsId[]" id="requiDtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="transIdTo[]" id="transIdTo_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="febDescripId[]" id="febDescripId_1"/><input type="hidden" name="machineNoId[]" id="machineNoId_1"/><input type="hidden" name="prodGsm[]" id="prodGsm_1"/><input type="hidden" name="diaWidth[]" id="diaWidth_1"/><input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1"/><input type="hidden" name="transferEntryForm[]" id="transferEntryForm_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/><input type="hidden" name="barcodeIssue[]" id="barcodeIssue_1"/><input type="hidden" name="rollMstId[]" id="rollMstId_1"/><input type="hidden" name="rollAmount[]" id="rollAmount_1"/><input type="hidden" name="fromProductUp[]" id="fromProductUp_1"/><input type="hidden" name="fromBookingWithoutOrder[]" id="fromBookingWithoutOrder_1"/></td><input type="hidden" name="fromBodyPart[]" id="fromBodyPart_1"/><input type="hidden" name="hideBodyPartForUpdate[]" id="hideBodyPartForUpdate_1"/><input type="hidden" name="hiddenTransferqnty[]" id="hiddenTransferqnty_1"/></tr>'; 
		$("#scanning_tbl tbody").html(html);
	}

	function page_reload()
	{
		window.location.reload();
	}

	function calculate_total()
	{
		var total_roll_weight='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				//alert(rollWgt);
			total_roll_weight=total_roll_weight*1+rollWgt*1;
		});
	
		$("#total_rollwgt").text(total_roll_weight.toFixed(2));	
		//$("#total_rollwgt").text(total_roll_wgt);
	}

	function store_on_change(str)
	{
		
		
		if($("#cbo_transfer_criteria").val() == 2)
		{
			// $("#cbo_store_name").val(str);
			// load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller',company, 'load_drop_store_from', 'store_td' );
			var tns = $("#cbo_store_name_from").val();
			var tnss = $("#cbo_store_name").val();
			
			console.log(tnss);
			if($("#cbo_store_name_from").val() == $("#cbo_store_name").val())
			{
				$("#cbo_store_name").val(0);
			}
		}
	}

	function company_on_change(company)
	{
		var item_category = $('#cbo_item_category').val();
		page_link = 'cbo_company_id='+company+'&item_category='+item_category+'&action=requ_variable_settings';

		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			return;
		}

		if($("#cbo_transfer_criteria").val() != 1)
		{
			$("#cbo_to_company_id").val(company);
			load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller',$('#cbo_transfer_criteria').val()+'_'+company, 'load_drop_store_to', 'store_td' );
		}
		else
		{
			if($("#cbo_company_id").val() == $("#cbo_to_company_id").val())
			{
				$("#cbo_to_company_id").val(0);
			}
		}

		fnc_details_row_blank();

		$.ajax({
			url: 'requires/roll_wise_finish_fabric_transfer_entry_controller.php',
			type: 'POST',
			data: page_link,
			success: function (response)
			{
				//var variable_settings = response.split("**");
				
				//alert(variable_settings[0]+'='+variable_settings[1]);
				var variable_settings = response.split("**");
				if (variable_settings[0]==1) 
				{
					$('#txt_requisition_no').attr('disabled',false);
					// $('#txt_bar_code_num').attr('disabled','disabled');
					$('#txt_requisition_basis').val(variable_settings[0]);
					//$('#txt_bar_code_num').attr('disabled','disabled');
				}				
				else
				{
					$('#txt_requisition_no').attr('disabled','disabled');
					$('#txt_bar_code_num').attr('disabled',false);
					$('#txt_requisition_basis').val('');
				}

				$('#store_update_upto').val(variable_settings[1]);
			}
		});
	}

	function from_company_on_change(from_company)
	{
		// if(($("#cbo_company_id").val() == $("#cbo_to_company_id").val()) && $('#cbo_transfer_criteria').val() ==1)
		// {
		// 	$("#cbo_to_company_id").val(0);
		// 	return;
		// }

		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller',$('#cbo_transfer_criteria').val()+'_'+from_company, 'load_drop_store_from', 'from_store_td' );

	}

	function to_company_on_change(to_company)
	{
		if(($("#cbo_company_id").val() == $("#cbo_to_company_id").val()) && $('#cbo_transfer_criteria').val() ==1)
		{
			$("#cbo_to_company_id").val(0);
			return;
		}
		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller',$('#cbo_transfer_criteria').val()+'_'+to_company, 'load_drop_store_to', 'store_td' );
	}

	function openmypage_requisition_no() // Requisition Basis popup
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var cbo_store_name = $('#cbo_store_name').val();
		var cbo_store_name_from = $('#cbo_store_name_from').val();
		if (form_validation('cbo_company_id*cbo_store_name','Company*To Store')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_finish_fabric_transfer_entry_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_store_name='+cbo_store_name+'&cbo_store_name_from='+cbo_store_name_from+'&action=itemTransfer_requisition_popup','Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			var from_store_id=this.contentDoc.getElementById("hidden_from_store").value;	 //challan Id and Number
			$('#cbo_store_name_from').val(from_store_id);
			if(issue_id!="")
			{
				fnc_reset_form(1);
				get_php_form_data(issue_id, "populate_requisition_data_from_data", "requires/roll_wise_finish_fabric_transfer_entry_controller");
				
				var com_id=$('#cbo_to_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var all_data=com_id + "__" + store_id;
				//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
				var tbl_length=$('#scanning_tbl tbody tr').length;
				//alert(floor_result+"="+tbl_length);//return;
				var JSONObject = JSON.parse(floor_result);
				for(var i=1; i<=tbl_length; i++)
				{
					$('#cboFloorTo_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject))
					{
						$('#cboFloorTo_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};
				}
					
				var barcode_nos=return_global_ajax_value( issue_id, 'requisition_barcode_nos', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
				if(trim(barcode_nos)!="")
				{					
					create_row(2,barcode_nos);
				}
				//alert(barcode_nos);

				set_button_status(1, permission, 'fnc_finish_fabric_issue_roll_wise',0);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			    $("#btn_mc_6").removeClass('formbutton_disabled');
			    $("#btn_mc_6").addClass('formbutton');	   
			}
		}
	}

	function fn_load_floor(store_id)
	{
		// alert(store_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var all_data=com_id + "__" + store_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(floor_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(floor_result);
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cboFloorTo_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject))
			{
				$('#cboFloorTo_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(room_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(room_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var roomId = $(this).find('select[name="cboRoomTo[]"]').attr("id");
			var roomIdSlArr = roomId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= roomIdSlArr[1]*1 )
			{
				$(this).find('select[name="cboRoomTo[]"]').html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$(this).find('select[name="cboRoomTo[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		});
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rackId = $(this).find('select[name="txtRackTo[]"]').attr("id");
			var rackIdSlArr = rackId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= rackIdSlArr[1]*1 )
			{
				$(this).find('select[name="txtRackTo[]"]').html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$(this).find('select[name="txtRackTo[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		});
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var shelfId = $(this).find('select[name="txtShelfTo[]"]').attr("id");
			var shelfIdSlArr = shelfId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= shelfIdSlArr[1]*1 )
			{
				$(this).find('select[name="txtShelfTo[]"]').html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$(this).find('select[name="txtShelfTo[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		});
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/roll_wise_finish_fabric_transfer_entry_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var shelfId = $(this).find('select[name="txtBinTo[]"]').attr("id");
			var binIdSlArr = shelfId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= binIdSlArr[1]*1 )
			{
				$(this).find('select[name="txtBinTo[]"]').html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$(this).find('select[name="txtBinTo[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		});
	}

	function copy_all(str)
	{
		var data=str.split("_");
		var trall=$('#scanning_tbl tbody tr').length;
		var copy_tr=parseInt(trall);
		if($('#floorIds').is(':checked'))
		{
			if(data[1]==0) data_value=$("#cboFloorTo_"+data[0]).val();
		}
		if($('#roomIds').is(':checked'))
		{
			if(data[1]==1) data_value=$("#cboRoomTo_"+data[0]).val();
		}
		if($('#rackIds').is(':checked'))
		{
			if(data[1]==2) data_value=$("#txtRackTo_"+data[0]).val();
		}
		if($('#shelfIds').is(':checked'))
		{
			if(data[1]==3) data_value=$("#txtShelfTo_"+data[0]).val();
		}
		if($('#binIds').is(':checked'))
		{
			if(data[1]==4) data_value=$("#txtBinTo_"+data[0]).val();
		}

		$("#scanning_tbl").find('tbody tr').each(function()
		{

			var floorId = $(this).find('select[name="cboFloorTo[]"]').attr("id");
			var floorIdSlArr = floorId.split("_");

			// copy only that and below selected data
			if( data[0] >= floorIdSlArr[1]*1 )
			{
				if($('#floorIds').is(':checked'))
				{
					if(data[1]==0) 	$(this).find('select[name="cboFloorTo[]"]').val(data_value);
				}

				if($('#roomIds').is(':checked'))
				{
					if(data[1]==1) $(this).find('select[name="cboRoomTo[]"]').val(data_value);
				}
				if($('#rackIds').is(':checked'))
				{
					if(data[1]==2) $(this).find('select[name="txtRackTo[]"]').val(data_value);
				}
				if($('#shelfIds').is(':checked'))
				{
					if(data[1]==3) $(this).find('select[name="txtShelfTo[]"]').val(data_value);
				}
				if($('#binIds').is(':checked'))
				{
					if(data[1]==4) $(this).find('select[name="txtBinTo[]"]').val(data_value);
				}
			}
		});
	}

	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#table_body tbody tr').length;
		if (fieldName=="cbo_store_name") 
		{			
			$("#scanning_tbl").find('tbody tr').each(function()
			{
 				$(this).find('select[name="cboFloorTo[]"]').val("");
				$(this).find('select[name="cboRoomTo[]"]').val("");
				$(this).find('select[name="txtRackTo[]"]').val("");
				$(this).find('select[name="txtShelfTo[]"]').val("");
				$(this).find('select[name="txtBinTo[]"]').val("");
			});
		}
		else if (fieldName=="cbo_floor_to") 
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="cboRoomTo[]"]').val("");
				$(this).find('select[name="txtRackTo[]"]').val("");
				$(this).find('select[name="txtShelfTo[]"]').val("");
				$(this).find('select[name="txtBinTo[]"]').val("");
			});
		}
		else if (fieldName=="cbo_room_to")  
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="txtRackTo[]"]').val("");
				$(this).find('select[name="txtBinTo[]"]').val("");
			});
		}
		else if (fieldName=="txt_rack_to")  
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="txtBinTo[]"]').val("");
			});
		}
	}


	function change_floor(value,id)
    {
	    var id=id.split('_');
		var roomTd='roomTdTo_'+id[1];		
		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller', value+"_"+roomTd, 'load_drop_down_room', roomTd);
    }

    function change_room(value,id)
    {     	
    	var id=id.split('_');
		var rackTd='rackTdTo_'+id[1];	
		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller', value+"_"+rackTd, 'load_drop_down_rack', rackTd);
    }

    function change_rack(value,id)
    {
    	var id=id.split('_');
		var shelfTd='shelfTdTo_'+id[1];
		//alert(value+'='+id+'='+shelfTd);		
		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller', value+"_"+shelfTd, 'load_drop_down_shelf', shelfTd);
    }

    function change_shelf(value,id)
    {
    	var id=id.split('_');
		var binTd='binTdTo_'+id[1];
		//alert(value+'='+id+'='+binTd);		
		load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller', value+"_"+binTd, 'load_drop_down_bin', binTd);
    }
</script>
</head>
<body onLoad="set_hotkey();$('#txt_bar_code_num').focus();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="6" align="center"><b>Transfer System No&nbsp;</b>
                        	<input type="text" name="txt_transfer_no" id="txt_transfer_no" class="text_boxes" style="width:140px;"  onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
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
								
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_on_change(this.value);from_company_on_change(this.value);" );
							?>
                        </td>
                        <td>To Company</td>
                        <td id="cra_124" >
                            <? 
								echo create_drop_down( "cbo_to_company_id", 160,   $blank_array,"", 1, "--Select Company--", 0, "to_company_on_change(this.value)",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>
						<td class="must_entry_caption">From Store</td>
						<td id="from_store_td">
							<?
								echo create_drop_down( "cbo_store_name_from", 160, $blank_array,"", 1, "--Select store--", 0, "fnc_details_row_blank();store_on_change(this.value);" );
							?>	
						</td>
						<td >To Store</td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "fn_load_floor(this.value);store_on_change(this.value);" );
                            ?>	
                        </td>
                        
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                    	
						<td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
						<td>Item Category</td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',2 );
                            ?>
                        </td>

                        <td>Requisition Basis</td>
                        <td>
                            <input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:148px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_requisition_no();"  disabled="disabled"/>
                        	<input type="hidden" name="txt_requisition_id" id="txt_requisition_id"/>
                        	<input type="hidden" name="txt_requisition_basis" id="txt_requisition_basis">
                        </td> 
                    </tr>
                    <tr>
						<td>Delivery Company</td>
                        <td>
                        	<input type="text" name="txt_delv_company_id" id="txt_delv_company_id" class="text_boxes" style="width:148px" />
                            <? 
								//echo create_drop_down( "txt_delv_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
							?>
                        </td>
                         <td><strong>Roll Number</strong></td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:148px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        	<input type="hidden" name="store_update_upto" id="store_update_upto">
                        	<input type="hidden" name="hidd_requi_qty" id="hidd_requi_qty">
                        	<input type="hidden" name="txt_from_product" id="txt_from_product">
                        	<input type="hidden" name="txt_from_order_id" id="txt_from_order_id">
                        	<input type="hidden" name="txt_prev_transfer_qnty" id="txt_prev_transfer_qnty">
                        </td>
                    	<td>Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px;" />
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:2020px;text-align:left;">
				<style>
                    #scanning_tbl tr td
                    {
                        background-color:#FFF;
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;
                        height:20px;
                        overflow:auto;
                    }
                </style>
                <div id="test_dv"></div>
				<table cellpadding="0" width="2000" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="20">SL</th>
                        <th width="70">Barcode No</th>
                        <th width="45">Roll No</th>
                        <th width="80">Body Part</th>
                        <th width="150">Fabric description</th>
                        <th width="150">Batch no</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="70">Color</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Buyer</th>
                        <th width="120">Booking</th>
                        <th width="80">Job No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Order No</th>
                        <th width="100">FinishingCompany</th>
                        <th width="70">Basis</th>
                        <th width="90">To Order</th>
						<th width="90">Body Part<br><input type="checkbox" checked id="bodyPartIds" name="bodyPartIds"/></th>
                        <th width="80">To Job</th>
                    	<th width="50">Floor<br><input type="checkbox" checked id="floorIds" name="floorIds"/></th>
						<th width="50">Room<br><input type="checkbox" checked id="roomIds" name="roomIds"/></th>
						<th width="50">Rack<br><input type="checkbox" checked id="rackIds" name="rackIds"/></th>
						<th width="50">Shelf<br><input type="checkbox" checked id="shelfIds" name="shelfIds"/></th>
						<th width="50">Bin/Box<br><input type="checkbox" checked id="binIds" name="binIds"/></th>
                        <th width="115">Fiish fabric Roll<br> Delivery Challan</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:2020px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="2000" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr__1" align="center" valign="middle">
                                <td width="20" id="sl_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="45" id="roll_1"></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="150" id="cons_1" align="left" title=""></td>
                                <td style="word-break:break-all;" width="150" id="batch_1" align="left" title=""></td>
                                <td style="word-break:break-all;" width="40" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="40" id="dia_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td width="60" align="right" id="rollWeight_1"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="120" id="booking_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="80" id="intRef_1"></td>
                                <td style="word-break:break-all;" width="80" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="100" id="knitCompany_1"></td>
                                <td style="word-break:break-all;" width="70" id="basis_1"></td>
                                <td width="90" id="toOrder_1"><input type="text"  class="text_boxes" id="txtToOrder_1" name="txtToOrder[]" style="width:75px;" onDblClick="opneToOrder(1)" placeholder="Browse For Order" value="" readonly /></td>
								<td width="90" align="center" id="toBodyPartTd_1">
								<? echo create_drop_down( "cboToBodyPart_1", 90,$blank_array,"", 1, "--Select--", 0, "copyBodyPart(this.id,this.value)",0,"","","","","","","cboToBodyPart[]","onchange_void" ); 

								?>
								</td>
								</td>
                                <td style="word-break:break-all;" width="80" id="toJob_1"></td>
								<td width="50" align="center" id="floor_td_to" class="floor_td_to">
								<? echo create_drop_down( "cboFloorTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboFloorTo[]" ,"onchange_void"); ?>
								</td>
								<td width="50" align="center" id="roomTdTo_1">
		                        <? echo create_drop_down( "cboRoomTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboRoomTo[]","onchange_void" ); ?>		            
		                        </td>
								<td width="50" align="center" id="rackTdTo_1">
								<? echo create_drop_down( "txtRackTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtRackTo[]","onchange_void" ); ?>
								</td>
								<td width="50" align="center" id="shelfTdTo_1">
								<? echo create_drop_down( "txtShelfTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtShelfTo[]","onchange_void" ); ?>
								</td>
								<td width="50" align="center" id="binTdTo_1">
								<? echo create_drop_down( "txtBinTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtBinTo[]","onchange_void" ); ?>
								</td>
                                <td style="word-break:break-all;" width="115" id="progBookPiNo_1"></td>
                                <td id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="batchNo[]" id="batchNo_1"/>
                                    <input type="hidden" name="batch_id[]" id="batchId_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
									<input type="hidden" name="brandId[]" id="brandId_1"/>
                                    <input type="hidden" name="fromStoreId[]" id="fromStoreId_1"/>
                                    <input type="hidden" name="floor[]" id="floor_1"/>
                                    <input type="hidden" name="room[]" id="room_1"/>
                                    <input type="hidden" name="rack[]" id="rack_1"/>
                                    <input type="hidden" name="shelf[]" id="shelf_1"/>
                                    <input type="hidden" name="bin[]" id="bin_1"/>
                                    <input type="hidden" name="toOrderId[]" id="toOrderId_1"/>
                                    <input type="hidden" name="tobookingNo[]" id="tobookingNo_1"/>
									<input type="hidden" name="previousToBbatchId[]" id="previousToBbatchId_1" readonly/>
                                    <input type="hidden" name="toBookingMstId[]" id="toBookingMstId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="requiDtlsId[]" id="requiDtlsId_1"/>
                                    <input type="hidden" name="transId[]" id="transId_1"/>
                                    <input type="hidden" name="transIdTo[]" id="transIdTo_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="febDescripId[]" id="febDescripId_1"/>
                                    <input type="hidden" name="machineNoId[]" id="machineNoId_1"/>
                                    <input type="hidden" name="prodGsm[]" id="prodGsm_1"/>
                                    <input type="hidden" name="diaWidth[]" id="diaWidth_1"/>
                                    <input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1"/>
                                    <input type="hidden" name="transferEntryForm[]" id="transferEntryForm_1"/>
                                    <input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/>
                                    <input type="hidden" name="barcodeIssue[]" id="barcodeIssue_1"/>
                                    <input type="hidden" name="rollMstId[]" id="rollMstId_1"/>
                                    <input type="hidden" name="rollAmount[]" id="rollAmount_1"/>
                                    <input type="hidden" name="fromProductUp[]" id="fromProductUp_1"/>
                                    <input type="hidden" name="fromBookingWithoutOrder[]" id="fromBookingWithoutOrder_1"/>
									<input type="hidden" name="fromBodyPart[]" id="fromBodyPart_1"/>
                                    <input type="hidden" name="hideBodyPartForUpdate[]" id="hideBodyPartForUpdate_1"/>
									<input type="hidden" name="hiddenTransferqnty[]" id="hiddenTransferqnty_1" readonly />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                        	<th colspan="9" style="text-align: right;"><strong>Total Qnty</strong></th>
                        	<th style="text-align: right;" id="total_rollwgt"><strong></strong></th>
                        	<th colspan="18"></th>
                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="1950" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_barcode" id="txt_deleted_barcode" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_trnsf_dtls_id" id="txt_deleted_trnsf_dtls_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_trans_id" id="txt_deleted_trans_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_prod_id" id="txt_deleted_prod_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_prod_qty" id="txt_deleted_prod_qty" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_source_roll_id" id="txt_deleted_source_roll_id" class="text_boxes" value="">

                            <? 
                            	echo load_submit_buttons($permission,"fnc_finish_fabric_issue_roll_wise",0,1,"fnc_reset_form(3);",1);//page_reload()
                            	//echo load_submit_buttons($permission,"fnc_finish_fabric_issue_roll_wise",0,1,"fnc_reset_form(1);page_reload()",1);
							
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<!-- <script>
	$('#Print1').hide();
</script> -->
</html>
