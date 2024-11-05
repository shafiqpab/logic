<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Transfer Roll Wise
				
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	17-06-2021
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
echo load_html_head_contents("Grey Fabric Transfer Roll Wise","../../", 1, 1, $unicode,'',''); 
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();  var scanned_barcode_issue =new Array(); var barcode_rollTableId_array=new Array(); var table_barcode_no_arr=new Array();
	var barcode_trnasId_array =new Array(); var barcode_dtlsId_array=new Array(); var barcode_trnasId_to_array=new Array();

	var tableFilters =
		{
			col_operation: {
				id: ["total_rollwgt"],
				//col: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27],
				col: [12],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		};

	
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
	function openmypage_acknowledgement() // system id popup
	{ 
		var cbo_company_id = $('#cbo_to_company_id').val();
		/*if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}*/
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_grey_fabric_transfer_acknowledgement_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Acknowledgement Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form(1);
				get_php_form_data(issue_id, "populate_data_from_acknowledgement", "requires/roll_wise_grey_fabric_transfer_acknowledgement_controller");

				var com_id=$('#cbo_to_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var all_data=com_id + "__" + store_id;
				//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
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
									
				var barcode_nos=return_global_ajax_value( issue_id, 'barcode_nos_ack_saved', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
				if(trim(barcode_nos)!="")
				{					
					create_row(1,barcode_nos);
				}
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			    $("#btn_mc_6").removeClass('formbutton_disabled');
			    $("#btn_mc_6").addClass('formbutton');
			}
			setFilterGrid("scanning_tbl",-1,tableFilters);
		}
	}

	function openmypage_transfer() // acknowledgement Basis popup
	{ 
		var cbo_to_company_id = $('#cbo_to_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var cbo_store_name = $('#cbo_store_name').val();
		if (form_validation('cbo_to_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_grey_fabric_transfer_acknowledgement_controller.php?cbo_to_company_id='+cbo_to_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_store_name='+cbo_store_name+'&action=itemTransfer_acknowledgement_popup','Transfer Popup', 'width=935px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			//var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			var hidden_system_id=this.contentDoc.getElementById("hidden_system_id").value.split("_"); 
			// alert(hidden_system_id);
			var issue_id=hidden_system_id[0];
			var fso_yes_no_type=hidden_system_id[1];
			//alert(issue_id+'='+fso_yes_no_type);
			$('#fso_yes_no_type').val(fso_yes_no_type);
			if(issue_id!="")
			{
				fnc_reset_form(1);
				get_php_form_data(issue_id, "populate_data_from_data", "requires/roll_wise_grey_fabric_transfer_acknowledgement_controller");
				
				var com_id=$('#cbo_to_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var all_data=com_id + "__" + store_id;
				//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
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
					
				var barcode_nos=return_global_ajax_value( issue_id, 'barcode_nos_ack', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
				
				if(trim(barcode_nos)!="")
				{					
					create_row(0,barcode_nos);
				}
				else
				{
					alert('Barcode not available for Acknowledgement');return;
				}

				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',0);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			    $("#btn_mc_6").removeClass('formbutton_disabled');
			    $("#btn_mc_6").addClass('formbutton');
			}
			setFilterGrid("scanning_tbl",-1,tableFilters);
		}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/roll_wise_grey_fabric_transfer_acknowledgement_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_fabric_issue_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_to_company_id').val()+'*'+$('#txt_transfer_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_print');
			return;
		}
		if(operation==5)
		{
			if(form_validation('txt_transfer_no','Transfer System No')==false)
			{
				alert("Transfer Data First");
				return;
			}
			
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_to_company_id').val()+'*'+$('#txt_transfer_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_print_gropping');
			return;
		}
		if(operation==6)
		{
			var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
			if(form_validation('txt_transfer_no','Transfer System No')==false)
			{
				alert("Transfer Data First");
				return;
			}
			if (cbo_transfer_criteria==2) 
			{				
				var report_title="Roll Wise Grey Fabric Store To Store Transfer Entry Report";
				generate_report_file( $('#cbo_to_company_id').val()+'*'+$('#txt_transfer_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_print_gropping_2');
				return;
			}
			else
			{
				alert("This report only for Store to Store");
				return;
			}
		}

		if(operation==7 || operation==8)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_to_company_id').val()+'*'+$('#txt_transfer_no').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+operation,'grey_issue_print_2');
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

		var row_skiper = 0; var upto_type=1;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			if(row_skiper != 0)
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
						alert("Up To Shelf Value Full Fill Required For Inventory");upto_type=0; return;
					}
					else if(store_update_upto==4 && (toFloor==0 || toRoom==0 || toRack==0))
					{
						alert("Up To Rack Value Full Fill Required For Inventory"); upto_type=0; return;
					}
					else if(store_update_upto==3 && (toFloor==0 || toRoom==0))
					{
						alert("Up To Room Value Full Fill Required For Inventory"); upto_type=0; return;
					}
					else if(store_update_upto==2 && toFloor==0)
					{
						alert("Up To Floor Value Full Fill Required For Inventory"); upto_type=0; return;
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
				var rollNo=$(this).find("td:eq(3)").text();
				var constructCompo=$(this).find("td:eq(5)").text();
				
				var yarnLot=$(this).find('input[name="yarnLot[]"]').val();
				var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var stichLn=$(this).find('input[name="stichLn[]"]').val();
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
				var toBodyPart=$(this).find('input[name="toBodyPart[]"]').val();
				var fromBodyPart=$(this).find('input[name="fromBodyPart[]"]').val();
				var consRate=$(this).find('input[name="consRate[]"]').val();
				var consAmount=$(this).find('input[name="consAmount[]"]').val();
				var programNo=$(this).find('input[name="programNo[]"]').val();



				j++;
				dataString+='&fromStoreId_' + j + '=' + fromStoreId +'&toOrderId_' + j + '=' + toOrderId +'&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&yarnLot_' + j + '=' + yarnLot + '&yarnCount_' + j + '=' + yarnCount + '&colorId_' + j + '=' + colorId + '&stichLn_' + j + '=' + stichLn + '&brandId_' + j + '=' + brandId + '&fromFloor_' + j + '=' + fromFloor + '&fromRoom_' + j + '=' + fromRoom + '&fromRack_' + j + '=' + fromRack + '&fromShelf_' + j + '=' + fromShelf + '&fromBin_' + j + '=' + fromBin + '&toFloor_' + j + '=' + toFloor + '&toRoomId_' + j + '=' + toRoom + '&toRack_' + j + '=' + toRack + '&toShelf_' + j + '=' + toShelf + '&toBin_' + j + '=' + toBin + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&transIdTo_' + j + '=' + transIdTo + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&febDescripId_' + j + '=' + febDescripId+ '&machineNoId_' + j + '=' + machineNoId+ '&gsm_' + j + '=' + gsm+ '&diaWidth_' + j + '=' + diaWidth+ '&knitDetailsId_' + j + '=' + knitDetailsId+ '&transferEntryForm_' + j + '=' + transferEntryForm+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder + '&rollMstId_' + j + '=' + rollMstId + '&constructCompo_' + j + '=' + constructCompo + '&rollAmount_' + j + '=' + rollAmount + '&fromBookingWithoutOrder_' + j + '=' + fromBookingWithoutOrder + '&requiDtlsId_' + j + '=' + requiDtlsId + '&toBodyPart_' + j + '=' + toBodyPart + '&fromBodyPart_' + j + '=' + fromBodyPart + '&consRate_' + j + '=' + consRate + '&consAmount_' + j + '=' + consAmount + '&programNo_' + j + '=' + programNo;
			}
			

			row_skiper++;
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}	

		if(upto_type==0)
		{
			// alert('Floor, Room, Rack, Shelf');
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
		// alert(dataString);return;
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_transfer_no*cbo_transfer_criteria*cbo_company_id*cbo_to_company_id*txt_transfer_date*cbo_item_category*cbo_store_name*txt_transfer_acknowledge_no*update_id*txt_deleted_id*txt_deleted_trnsf_dtls_id*txt_deleted_trans_id*txt_deleted_prod_id*txt_deleted_prod_qty*txt_deleted_source_roll_id*txt_deleted_barcode*txt_remarks*txt_transfer_mst_id*fso_yes_no_type',"../../")+dataString;
		
		// alert(data);return;
		freeze_window(operation);		
		http.open("POST","requires/roll_wise_grey_fabric_transfer_acknowledgement_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_grey_fabric_issue_roll_wise_Reply_info()
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
				var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
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

				document.getElementById('update_id').value = response[2];
				var barcode_nos=return_global_ajax_value( response[1], 'barcode_nos_ack_saved', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
				if(trim(barcode_nos)!="")
				{
					create_row(1,barcode_nos);
				}
				
				document.getElementById('txt_transfer_acknowledge_no').value = response[2];
				$('#cbo_transfer_criteria').attr('disabled',true);
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_to_company_id').attr('disabled',true);
				$('#txt_transfer_no').attr('disabled',true);

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
				// add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			}
			release_freezing();
			setFilterGrid("scanning_tbl",-1,tableFilters);
		}
		
	}
	
	//var scanned_barcode=new Array();
	
	function create_row(is_update,barcode_no)
	{
		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 

		

		
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
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
				if (form_validation('cbo_to_company_id*cbo_store_name','To Company*Store')==false)
				{
					alert("Please Select To Store Field");return;
				}
			}
			
		}
		
		var ack_system_id=$('#update_id').val();
		var transfer_system_id=$('#txt_transfer_mst_id').val();
		var cbo_store_name=$('#cbo_store_name').val();
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
			var barcode_data=return_global_ajax_value( transfer_system_id, 'populate_barcode_data_from_transfer', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
			// alert(barcode_data); return;
			var barcode_data_all=new Array();
			var barcode_data_ref=new Array();
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
				var yarn_lot=barcode_data_all[13];
				var yarn_count=barcode_data_all[14];
				var stitch_length=barcode_data_all[15];
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
				var to_body_part=barcode_data_all[65];
				var cons_rate=barcode_data_all[66];
				var cons_amount=barcode_data_all[67];
				var program_no=barcode_data_all[68];
				var machine_dia=barcode_data_all[69];
				var machine_gg=barcode_data_all[70];
				var machine_dia_gg = machine_dia+" X "+ machine_gg;

				var multi_floor=barcode_data_all[71];
				var multi_room=barcode_data_all[72];
				var multi_rack=barcode_data_all[73];
				var multi_self=barcode_data_all[74];
				var multi_bin=barcode_data_all[75];
				// alert(multi_room);

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
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yLot_"+row_num).text(yarn_lot);
				$("#machine_"+row_num).text(machine_dia_gg);
				$("#mcDiaGg_"+row_num).val(machine_dia_gg);
				$("#stitchLength_"+row_num).text(stitch_length);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);				
				$("#fromStoreId_"+row_num).val(store_id);
				$("#floor_"+row_num).val(floor_id);
				$("#room_"+row_num).val(room_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#bin_"+row_num).val(bin_box_id);

				//company to company transfer
				if(cbo_transfer_criteria == 1 || cbo_transfer_criteria == 4)
				{
					$("#toOrder_"+row_num).text(up_to_po_no);
				}
				$("#toOrderId_"+row_num).val(up_to_po_id);
				$("#toJob_"+row_num).text(to_job);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#knitDetailsId_"+row_num).val(dtls_id);
				$("#transferEntryForm_"+row_num).val(entry_form);
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
				
				/*if (to_floor_id!=0) 
				{
					change_floor(to_floor_id,"cboFloorTo_"+row_num);
				}
				if (to_room_id!=0) 
				{
					change_room(to_room_id,"cboRoomTo_"+row_num);
				}
				if (to_rack_id!=0) 
				{
					change_rack(to_rack_id,"txtRackTo_"+row_num);
				}
				if (to_self_id!=0) 
				{
					change_shelf(to_self_id,"txtShelfTo_"+row_num);
				}*/

				/*
				|
				|	Load barcode wise floor, room, rack, shelf, bin_box
				|
				*/
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
				$("#fromBodyPart_"+row_num).val(body_part_id);
				$("#toBodyPart_"+row_num).val(to_body_part);
				$("#consRate_"+row_num).val(cons_rate);
				$("#consAmount_"+row_num).val(cons_amount);
				$("#programNo_"+row_num).val(program_no);

				$("#cons_"+row_num).prop("title","prod id = "+prod_id+", from prod id = "+fromProductUp);

				$('#decrease_'+row_num).removeAttr("onclick").attr("disabled","");

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
			}
		}
		else  // Update Event
		{
			var barcode_data=return_global_ajax_value( ack_system_id, 'populate_barcode_data_ack', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
			//alert(barcode_data); return;
			var barcode_data_all=new Array();
			var barcode_data_ref=new Array();
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
				var yarn_lot=barcode_data_all[13];
				var yarn_count=barcode_data_all[14];
				var stitch_length=barcode_data_all[15];
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
				var to_body_part=barcode_data_all[65];
				var cons_rate=barcode_data_all[66];
				var cons_amount=barcode_data_all[67];
				var program_no=barcode_data_all[68];
				var machine_dia=barcode_data_all[69];
				var machine_gg=barcode_data_all[70];
				var machine_dia_gg = machine_dia+" X "+ machine_gg;

				var multi_floor=barcode_data_all[71];
				var multi_room=barcode_data_all[72];
				var multi_rack=barcode_data_all[73];
				var multi_self=barcode_data_all[74];
				var multi_bin=barcode_data_all[75];
				// alert(multi_room);
				
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
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yLot_"+row_num).text(yarn_lot);
				$("#machine_"+row_num).text(machine_dia_gg);
				$("#mcDiaGg_"+row_num).val(machine_dia_gg);
				$("#stitchLength_"+row_num).text(stitch_length);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);				
				$("#fromStoreId_"+row_num).val(store_id);
				$("#floor_"+row_num).val(floor_id);
				$("#room_"+row_num).val(room_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#bin_"+row_num).val(bin_box_id);

				//company to company transfer
				if(cbo_transfer_criteria == 1 || cbo_transfer_criteria == 4)
				{
					$("#toOrder_"+row_num).text(up_to_po_no);
				}
				$("#toOrderId_"+row_num).val(up_to_po_id);
				$("#toJob_"+row_num).text(to_job);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#knitDetailsId_"+row_num).val(dtls_id);
				$("#transferEntryForm_"+row_num).val(entry_form);
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
				
				/*if (to_floor_id!=0) 
				{
					change_floor(to_floor_id,"cboFloorTo_"+row_num);
				}
				if (to_room_id!=0) 
				{
					change_room(to_room_id,"cboRoomTo_"+row_num);
				}
				if (to_rack_id!=0) 
				{
					change_rack(to_rack_id,"txtRackTo_"+row_num);
				}
				if (to_self_id!=0) 
				{
					change_shelf(to_self_id,"txtShelfTo_"+row_num);
				}*/

				/*
				|
				|	Load barcode wise floor, room, rack, shelf, bin_box
				|
				*/
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
				
				$("#cboFloorTo_"+row_num).removeAttr('title').attr("title",$("#cboFloorTo_"+row_num+" option:selected").text()); 
				$("#cboRoomTo_"+row_num).removeAttr('title').attr("title",$("#cboRoomTo_"+row_num+" option:selected").text()); 
				$("#txtRackTo_"+row_num).removeAttr('title').attr("title",$("#txtRackTo_"+row_num+" option:selected").text()); 
				$("#txtShelfTo_"+row_num).removeAttr('title').attr("title",$("#txtShelfTo_"+row_num+" option:selected").text()); 
				$("#txtBinTo_"+row_num).removeAttr('title').attr("title",$("#txtBinTo_"+row_num+" option:selected").text()); 
				
				$("#dtlsId_"+row_num).val(up_dtls_id);
				$("#transId_"+row_num).val(up_trans_id);
				$("#transIdTo_"+row_num).val(up_to_trans_id);
				$("#rolltableId_"+row_num).val(up_roll_id);
				$("#barcodeIssue_"+row_num).val(barcode_for_issue);
				$("#rollMstId_"+row_num).val(rollMstId);
				$("#rollAmount_"+row_num).val(rollAmount);
				$("#fromProductUp_"+row_num).val(fromProductUp);
				$("#fromBodyPart_"+row_num).val(body_part_id);
				$("#toBodyPart_"+row_num).val(to_body_part);
				$("#consRate_"+row_num).val(0);
				$("#consAmount_"+row_num).val(0);
				$("#programNo_"+row_num).val(0);

				$("#cons_"+row_num).prop("title","prod id = "+prod_id+", from prod id = "+fromProductUp);

				$('#decrease_'+row_num).removeAttr("onclick").attr("disabled","");

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
			}
		}
		calculate_total();
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
		}
	});
	
	function add_dtls_data_______( data )
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

		/*if( jQuery.inArray( bar_code, batch_batcode_arr )>-1) 
		{ 
			alert('Sorry! Barcode Already Scanned.'); 
			$('#txt_bar_code_num').val('');
			return; 
		}*/
		
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

			//if(txt_deleted_prod_qty=='') selected_prod_qty=deleted_production_qty; else selected_prod_qty=txt_deleted_prod_qty+','+deleted_production_qty;
			//$('#txt_deleted_prod_qty').val( selected_prod_qty );
		}
		
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
		calculate_total();
	}
	
	function opneToOrder________(str)
	{
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
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
			var item_desc_ids='';var item_gsm=""; var item_dia="";

			var page_link='requires/roll_wise_grey_fabric_transfer_acknowledgement_controller.php?cbo_to_company_id='+cbo_to_company_id+'&action=to_order_popup'+'&item_desc_ids='+item_desc_ids+'&item_gsm='+item_gsm+'&item_dia='+item_dia+'&txt_requisition_basis='+txt_requisition_basis;
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var order_ref=this.contentDoc.getElementById("order_id").value.split("_"); 
				var order_id=order_ref[0];
				var order_no=order_ref[1];
				var job_no=order_ref[2];
				//alert(job_no);
				if(order_no!="")
				{
					$('#toOrderId_'+str).val(order_id);
					$('#txtToOrder_'+str).val(order_no);
					$('#toJob_'+str).text(job_no);
					var first_tr=str-1;
					for(var k=first_tr; k>=1; k--)
					{
						$("#toOrderId_"+k).val(order_id);
						$("#txtToOrder_"+k).val(order_no);
						$("#toJob_"+k).text(job_no);
					}
				}
			}
		}
	}
	
	function load_room_rack_self()
	{
		return load_room_rack_self_bin('requires/roll_wise_grey_fabric_transfer_acknowledgement_controller*13*cboRoomTo_1*1', 'room',roomTdTo_1, document.getElementById('cbo_to_company_id').value,'',document.getElementById('cbo_store_name').value,document.getElementById('cboFloorTo_1').value,'','','','','','50','H');
	}

	function fnc_reset_form(source) // Inserted Data Show and Reset Data
	{
		// alert(source);1 system id Browse
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
		load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller',$('#cbo_transfer_criteria').val()+'_'+str, 'load_drop_store_balnk', 'store_td' );
		
		fnc_details_row_blank();
	}


	function fnc_details_row_blank()
	{
		var html='<tr id="tr__1" align="center" valign="middle"><td width="20" id="sl_1"></td><td width="100" id="fromStore_1"></td><td width="70" id="barcode_1"></td><td width="45" id="roll_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="150" id="cons_1" title="" ></td><td style="word-break:break-all;" width="150" id="yLot_1" align="left" title=""></td><td style="word-break:break-all;" width="100" id="machine_1" align="left" title=""></td><td style="word-break:break-all;" width="100" id="stitchLength_1" title=""></td><td style="word-break:break-all;" width="40" id="gsm_1"></td><td style="word-break:break-all;" width="40" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td width="60" id="rollWeight_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="120" id="booking_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="intRef_1"></td><td style="word-break:break-all;" width="80" id="order_1" align="left"></td><td style="word-break:break-all;" width="100" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="basis_1"></td><td style="word-break:break-all;" width="90" id="toOrder_1"></td><td style="word-break:break-all;" width="80" id="toJob_1"></td><td width="50" align="center" id="floor_td_to" class="floor_td_to"><? echo create_drop_down( "cboFloorTo_1", 50,$blank_array,"", 1, "--Select--", 0, "change_floor(this.value,this.id);",0,"","","","","","","cboFloorTo[]","onchange_void"); ?></td><td width="50" align="center" id="roomTdTo_1"><? echo create_drop_down( "cboRoomTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboRoomTo[]","onchange_void" ); ?></td><td width="50" align="center" id="rackTdTo_1"><? echo create_drop_down( "txtRackTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtRackTo[]","onchange_void" ); ?></td><td width="50" align="center" id="shelfTdTo_1"><? echo create_drop_down( "txtShelfTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtShelfTo[]","onchange_void" ); ?></td><td width="50" align="center" id="binTdTo_1"><? echo create_drop_down( "txtBinTo_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtBinTo[]","onchange_void" ); ?></td><td style="word-break:break-all;" width="115" id="progBookPiNo_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="yarnLot[]" id="yarnLot_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="stichLn[]" id="stichLn_1"/><input type="hidden" name="mcDiaGg[]" id="mcDiaGg_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="floor[]" id="floor_1"/><input type="hidden" name="room[]" id="room_1"/><input type="hidden" name="rack[]" id="rack_1"/><input type="hidden" name="shelf[]" id="shelf_1"/><input type="hidden" name="bin[]" id="bin_1"/><input type="hidden" name="fromStoreId[]" id="fromStoreId_1"/><input type="hidden" name="toOrderId[]" id="toOrderId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="requiDtlsId[]" id="requiDtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="transIdTo[]" id="transIdTo_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="febDescripId[]" id="febDescripId_1"/><input type="hidden" name="machineNoId[]" id="machineNoId_1"/><input type="hidden" name="prodGsm[]" id="prodGsm_1"/><input type="hidden" name="diaWidth[]" id="diaWidth_1"/><input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1"/><input type="hidden" name="transferEntryForm[]" id="transferEntryForm_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/><input type="hidden" name="barcodeIssue[]" id="barcodeIssue_1"/><input type="hidden" name="rollMstId[]" id="rollMstId_1"/><input type="hidden" name="rollAmount[]" id="rollAmount_1"/><input type="hidden" name="fromProductUp[]" id="fromProductUp_1"/><input type="hidden" name="fromBookingWithoutOrder[]" id="fromBookingWithoutOrder_1"/><input type="hidden" name="fromBodyPart[]" id="fromBodyPart_1"/><input type="hidden" name="toBodyPart[]" id="toBodyPart_1"/><input type="hidden" name="consRate[]" id="consRate_1"/><input type="hidden" name="consAmount[]" id="consAmount_1"/><input type="hidden" name="programNo[]" id="programNo_1"/></td></tr>'; 
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

	function company_on_change(to_company)
	{
		load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller',$('#cbo_transfer_criteria').val()+'_'+to_company, 'load_drop_store_to', 'store_td' );

		var item_category = $('#cbo_item_category').val();
		page_link = 'cbo_company_id='+to_company+'&item_category='+item_category+'&action=requ_variable_settings';

		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			return;
		}

		fnc_details_row_blank();

		$.ajax({
			url: 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller.php',
			type: 'POST',
			data: page_link,
			success: function (response)
			{
				var variable_settings = response.split("**");
				//alert(variable_settings[0]+'='+variable_settings[1]);
				$('#store_update_upto').val(variable_settings[1]);
			}
		});
	}

	// ============================= floor_room_rack_shelf section Start =============================
	function fn_load_floor(store_id)
	{
		// alert(store_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var all_data=com_id + "__" + store_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(floor_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(floor_result);
		// for(var i=1; i<=tbl_length; i++)
		for(var i=sequenceNo; i>=1; i--)
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

		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");
		var copy_mc_dia_gg=$("#copy_mc_dia_gg").is(":checked");

		var txtColor = document.getElementById('colorId_'+sequenceNo).value;
		var txtLot = document.getElementById('yarnLot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('stichLn_'+sequenceNo).value;
		var mcDiaGg = document.getElementById('mcDiaGg_'+sequenceNo).value;
		// alert(txtStitchLength+'='+mcDiaGg);

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(room_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(room_result);

		if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
	                }
                }
			}
		}

		else if(copy_color_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_lot_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
	                }
                }
			}
		}
		else if(copy_stitch_length && copy_mc_dia_gg && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}

		else if(copy_color_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtColor == txtColor_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_lot_wise && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtLot == txtLot_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_stitch_length && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtStitchLength == txtStitchLength_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if(copy_mc_dia_gg && $('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (mcDiaGg == mcDiaGg_check)
	                {
						$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');

						};
	                }
                }
			}
		}
		else if($('#floorIds').is(':checked'))
		{
			// alert(sequenceNo+'='+tbl_length);
			for(var i=sequenceNo; i>=1; i--)
			{
				// alert(i);
				if ( $('#cboFloorTo_'+i).prop('disabled')== false)
				{
					$('#cboRoomTo_'+i).html('<option value="'+0+'">Select</option>');// problem found is
					for (var key of Object.keys(JSONObject).sort())
					{
						$('#cboRoomTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cboRoomTo_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#cboRoomTo_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();

		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");
		var copy_mc_dia_gg=$("#copy_mc_dia_gg").is(":checked");

		var txtColor = document.getElementById('colorId_'+sequenceNo).value;
		var txtLot = document.getElementById('yarnLot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('stichLn_'+sequenceNo).value;
		var mcDiaGg = document.getElementById('mcDiaGg_'+sequenceNo).value;

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);

		if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
	                if (txtColor == txtColor_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							// $('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
	                if (txtLot == txtLot_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							// $('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_stitch_length && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtStitchLength == txtStitchLength_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && $('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (mcDiaGg == mcDiaGg_check)
	                {
						$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if($('#roomIds').is(':checked'))
		{
			// for(var i=sequenceNo; i<=tbl_length; i++)
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#cboRoomTo_'+i).prop('disabled')== false)
				{
					$('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						// $('#txtRackTo_'+i).html('<option value="'+0+'">Select</option>');
						$('#txtRackTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtRackTo_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#txtRackTo_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();

		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");
		var copy_mc_dia_gg=$("#copy_mc_dia_gg").is(":checked");

		var txtColor = document.getElementById('colorId_'+sequenceNo).value;
		var txtLot = document.getElementById('yarnLot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('stichLn_'+sequenceNo).value;
		var mcDiaGg = document.getElementById('mcDiaGg_'+sequenceNo).value;

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
	                if (txtColor == txtColor_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
	                if (txtLot == txtLot_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_stitch_length && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtStitchLength == txtStitchLength_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && $('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (mcDiaGg == mcDiaGg_check)
	                {
						$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if($('#rackIds').is(':checked'))
		{
			// for(var i=sequenceNo; i<=tbl_length; i++)
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtRackTo_'+i).prop('disabled')== false)
				{
					$('#txtShelfTo_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$('#txtShelfTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtShelfTo_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#txtShelfTo_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_to_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();

		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");
		var copy_mc_dia_gg=$("#copy_mc_dia_gg").is(":checked");

		var txtColor = document.getElementById('colorId_'+sequenceNo).value;
		var txtLot = document.getElementById('yarnLot_'+sequenceNo).value;
		var txtStitchLength = document.getElementById('stichLn_'+sequenceNo).value;
		var mcDiaGg = document.getElementById('mcDiaGg_'+sequenceNo).value;

		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
	                if (txtColor == txtColor_check && txtLot == txtLot_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_color_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtColor_check = document.getElementById('colorId_'+i).value;
	                if (txtColor == txtColor_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_lot_wise && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtLot_check = document.getElementById('yarnLot_'+i).value;
	                if (txtLot == txtLot_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_stitch_length && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var txtStitchLength_check = document.getElementById('stichLn_'+i).value;
	                if (txtStitchLength == txtStitchLength_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if(copy_mc_dia_gg && $('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					var mcDiaGg_check = document.getElementById('mcDiaGg_'+i).value;
	                if (mcDiaGg == mcDiaGg_check)
					{
						$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
						for (var key of Object.keys(JSONObject).sort())
						{
							$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
						};
					}
				}
			}
		}
		else if($('#shelfIds').is(':checked'))
		{
			// for(var i=sequenceNo; i<=tbl_length; i++)
			for(var i=sequenceNo; i>=1; i--)
			{
				if ( $('#txtShelfTo_'+i).prop('disabled')== false)
				{
					$('#txtBinTo_'+i).html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$('#txtBinTo_'+i).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
					};
				}
			}
		}
		else
		{
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtBinTo_'+sequenceNo).html('<option value="'+0+'">Select</option>');
				$('#txtBinTo_'+sequenceNo).append('<option value="'+JSONObject[key]+'">'+key+'</option>');
			};
		}
	}

	function copy_all(str)
	{		
		var data=str.split("_");
		var trall=$('#scanning_tbl tbody tr').length;
		var copy_tr=parseInt(trall)-1;
		// copy_tr2 = copy_tr-1;
		// alert(copy_tr+'='+data[0]);
		// alert(data[0]);

		var copy_color_wise=$("#copy_color_wise").is(":checked");
        var copy_lot_wise=$("#copy_lot_wise").is(":checked");
        var copy_stitch_length=$("#copy_stitch_length").is(":checked");
        var copy_mc_dia_gg=$("#copy_mc_dia_gg").is(":checked");

        var txtColor = document.getElementById('colorId_'+data[0]).value;
		var txtLot = document.getElementById('yarnLot_'+data[0]).value;
		var txtStitchLength = document.getElementById('stichLn_'+data[0]).value;
		var mcDiaGg = document.getElementById('mcDiaGg_'+data[0]).value;
		// alert(txtColor+'='+data[0]);

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

		var first_tr=parseInt(data[0]);//16
		for(var k=first_tr; k>=1; k--)
		{
			// alert(k);
			var txtColor_check = document.getElementById('colorId_'+k).value;
			var txtLot_check = document.getElementById('yarnLot_'+k).value;
			var txtStitchLength_check = document.getElementById('stichLn_'+k).value;
			var mcDiaGg_check = document.getElementById('mcDiaGg_'+k).value;
			// alert(txtColor_check);

			// Floor
			if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
			else if(copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                    }
                }
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_stitch_length && copy_mc_dia_gg && $('#floorIds').is(':checked'))
	        {
                if (txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_lot_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
			{
				if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                    }
                }
			}
			else if(copy_color_wise && $('#floorIds').is(':checked'))
	        {
                if (txtColor == txtColor_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                	}
                }
	        }
	        else if(copy_lot_wise && $('#floorIds').is(':checked'))
	        {
                if (txtLot == txtLot_check)
                {
                	// alert(x+'='+floor_id);
                	// alert(txtColor+'='+txtColor_check);
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                   		if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                   	}
                }
	        }
	        else if(copy_stitch_length && $('#floorIds').is(':checked'))
	        {
                if (txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_mc_dia_gg && $('#floorIds').is(':checked'))
	        {
                if (mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
                    }
                }
	        }
	        else if($('#floorIds').is(':checked'))
	        {
	        	if ( $('#cboFloorTo_'+k).prop('disabled')== false)
				{
					// alert('floor='+k);
	        		if(data[1]==0) 	$("#cboFloorTo_"+k).val(data_value);
	        	}
	        }

	        //Room
	        if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
	        {
                if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && txtLot == txtLot_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
	        {
                if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
	        }
	        else if(copy_mc_dia_gg && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
			{
				if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
                {
                	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
                    	if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
                    }
                }
			}
			else if(copy_color_wise && $('#roomIds').is(':checked'))
			{
				if (txtColor == txtColor_check)
				{
					if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
						if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_lot_wise && $('#roomIds').is(':checked'))
			{
				if (txtLot == txtLot_check)
				{
					if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
						if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_stitch_length && $('#roomIds').is(':checked'))
			{
				if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
						if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_mc_dia_gg && $('#roomIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check)
				{
					if ( $('#cboRoomTo_'+k).prop('disabled')== false)
					{
						if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
					}
				}
			}
			else if($('#roomIds').is(':checked'))
	        {
	        	if ( $('#cboRoomTo_'+k).prop('disabled')== false)
				{
	        		if(data[1]==1) 	$("#cboRoomTo_"+k).val(data_value);
	        	}
	        }

	        //Rack
	        if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#rackIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#rackIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
	        {
	            if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_mc_dia_gg && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
			{
				if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
			}
	        else if(copy_color_wise && $('#rackIds').is(':checked'))
	        {
	        	if (txtColor == txtColor_check)
				{
					if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	        			if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	        		}
	        	}
	        }
	        else if(copy_lot_wise && $('#rackIds').is(':checked'))
	        {
	        	if (txtLot == txtLot_check)
				{
					if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	        			if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	        		}
	        	}
	        }
	        else if(copy_stitch_length && $('#rackIds').is(':checked'))
	        {
	        	if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	        			if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	        		}
	        	}
	        }
	        else if(copy_mc_dia_gg && $('#rackIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtRackTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
	                }
	            }
			}
			else if($('#rackIds').is(':checked'))
			{
				if ( $('#txtRackTo_'+k).prop('disabled')== false)
				{
					if(data[1]==2) 	$("#txtRackTo_"+k).val(data_value);
				}
			}

			//Shelf
			if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#shelfIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
	        {
	            if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_mc_dia_gg && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
			{
				if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_color_wise && $('#shelfIds').is(':checked'))
			{
				if (txtColor == txtColor_check)
				{
					if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
						if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_lot_wise && $('#shelfIds').is(':checked'))
			{
				if (txtLot == txtLot_check)
				{
					if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
						if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
						if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_mc_dia_gg && $('#shelfIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check)
				{
					if ( $('#txtShelfTo_'+k).prop('disabled')== false)
					{
						if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
					}
				}
			}
			else if($('#rackIds').is(':checked'))
			{
				if ( $('#txtShelfTo_'+k).prop('disabled')== false)
				{
					if(data[1]==3) 	$("#txtShelfTo_"+k).val(data_value);
				}
			}

			//Bin
			if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#binIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#binIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#binIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#binIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#binIds').is(':checked'))
	        {
	            if (mcDiaGg == mcDiaGg_check && txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_lot_wise && $('#binIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && txtLot == txtLot_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
	        }
			else if(copy_color_wise && copy_stitch_length && $('#binIds').is(':checked'))
			{
				if (txtColor == txtColor_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_lot_wise && copy_stitch_length && $('#binIds').is(':checked'))
			{
				if (txtLot == txtLot_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#binIds').is(':checked'))
	        {
	            if (txtColor == txtColor_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
	        }
	        else if(copy_mc_dia_gg && copy_stitch_length && $('#binIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check && txtStitchLength == txtStitchLength_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#binIds').is(':checked'))
			{
				if (txtLot == txtLot_check && mcDiaGg == mcDiaGg_check)
	            {
	            	if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
	                	if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
	                }
	            }
			}
			else if(copy_color_wise && $('#binIds').is(':checked'))
			{
				if (txtColor == txtColor_check)
				{
					if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
						if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_lot_wise && $('#binIds').is(':checked'))
			{
				if (txtLot == txtLot_check)
				{
					if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
						if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_stitch_length && $('#binIds').is(':checked'))
			{
				if (txtStitchLength == txtStitchLength_check)
				{
					if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
						if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
					}
				}
			}
			else if(copy_mc_dia_gg && $('#binIds').is(':checked'))
			{
				if (mcDiaGg == mcDiaGg_check)
				{
					if ( $('#txtBinTo_'+k).prop('disabled')== false)
					{
						if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
					}
				}
			}
			else if($('#binIds').is(':checked'))
			{
				if ( $('#txtBinTo_'+k).prop('disabled')== false)
				{
					if(data[1]==4) 	$("#txtBinTo_"+k).val(data_value);
				}
			}
		}
	}

	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#table_body tbody tr').length;

		var copy_color_wise=$("#copy_color_wise").is(":checked");
		var copy_lot_wise=$("#copy_lot_wise").is(":checked");
		var copy_stitch_length=$("#copy_stitch_length").is(":checked");
		var copy_mc_dia_gg=$("#copy_mc_dia_gg").is(":checked");

		var txtColor = document.getElementById('colorId_'+id).value;
		var txtLot = document.getElementById('yarnLot_'+id).value;
		var txtStitchLength = document.getElementById('stichLn_'+id).value;
		var mcDiaGg = document.getElementById('mcDiaGg_'+id).value;

		if (fieldName=="cbo_floor_to")
		{
			if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
					var mcDiaGg_check = $("#stichLn_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (mcDiaGg==mcDiaGg_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var yarnLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (yarnLot==yarnLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_mc_dia_gg && copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var yarnLot_check = $("#yarnLot_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (yarnLot==yarnLot_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();

	                if (txtColor==txtColor_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
					// alert(i+'='+txtColor +"=="+ txtColor_check);
	                if (txtLot == txtLot_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_stitch_length && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_mc_dia_gg && $('#floorIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg"+i).val();
	                if (mcDiaGg == mcDiaGg_check)
	                {
	                	if ( $('#cboFloorTo_'+i).prop('disabled')== false)
						{
		                	$("#cboRoomTo_"+i).val(0);
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if($('#floorIds').is(':checked'))
			{
				// for (var i = id; numRow>=i; i++)
				for(var i=id; i>=1; i--)
				{
					if ( $('#cboFloorTo_'+i).prop('disabled')== false)
					{
	                	$("#cboRoomTo_"+i).val(0);
						$("#txtRackTo_"+i).val(0);
						$("#txtShelfTo_"+i).val(0);
						$("#txtBinTo_"+i).val(0);
					}
				}
			}
			else
			{
				$("#cboRoomTo_"+i).val(0);
				$("#txtRackTo_"+i).val(0);
				$("#txtShelfTo_"+i).val(0);
				$("#txtBinTo_"+i).val(0);
			}
		}
		else if (fieldName=="cbo_room_to")
		{
			if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (mcDiaGg==mcDiaGg_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();

	                if (txtColor==txtColor_check && txtLot==txtLot_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_mc_dia_gg && copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();

	                if (mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtLot==txtLot_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if(copy_color_wise && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();

	                if (txtColor==txtColor_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}			
			else if(copy_lot_wise && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
	                if (txtLot == txtLot_check)
					{
						if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_stitch_length && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
					{
						if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
							$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_mc_dia_gg && $('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();

	                if (mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#cboRoomTo_'+i).prop('disabled')== false)
						{
		                	$("#txtRackTo_"+i).val(0);
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
	                }
				}
			}
			else if($('#roomIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					if ( $('#cboRoomTo_'+i).prop('disabled')== false)
					{
						$("#txtRackTo_"+i).val(0);
						$("#txtShelfTo_"+i).val(0);
						$("#txtBinTo_"+i).val(0);
					}
				}
			}
			else
			{
				$("#txtRackTo_"+id).val(0);
				$("#txtShelfTo_"+id).val(0);
				$("#txtBinTo_"+id).val(0);
			}
		}
		else if (fieldName=="txt_rack_to")
		{
			if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (mcDiaGg==mcDiaGg_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_mc_dia_gg && copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtLot==txtLot_check && mcDiaGg==mcDiaGg_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
	                if (txtColor == txtColor_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
	                if (txtLot == txtLot_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_stitch_length && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_mc_dia_gg && $('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (mcDiaGg == mcDiaGg_check)
	                {
	                	if ( $('#txtRackTo_'+i).prop('disabled')== false)
						{
							$("#txtShelfTo_"+i).val(0);
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if($('#rackIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					if ( $('#txtRackTo_'+i).prop('disabled')== false)
					{
						$("#txtShelfTo_"+id).val(0);
						$("#txtBinTo_"+id).val(0);
					}
				}
			}
			else
			{
				$("#txtShelfTo_"+id).val(0);
				$("#txtBinTo_"+id).val(0);
			}
		}
		else if (fieldName=="txt_shelf_to")
		{
			if(copy_color_wise && copy_lot_wise && copy_stitch_length && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check && mcDiaGg==mcDiaGg_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check && mcDiaGg==mcDiaGg_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_mc_dia_gg && copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (mcDiaGg==mcDiaGg_check && txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_lot_wise && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtLot_check = $("#yarnLot_"+i).val();
	                if (txtColor==txtColor_check && txtLot==txtLot_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtColor==txtColor_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtLot==txtLot_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtColor==txtColor_check && mcDiaGg==mcDiaGg_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_mc_dia_gg && copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (mcDiaGg==mcDiaGg_check && txtStitchLength==txtStitchLength_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && copy_mc_dia_gg && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (txtLot==txtLot_check && mcDiaGg==mcDiaGg_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_color_wise && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtColor_check = $("#colorId_"+i).val();
	                if (txtColor == txtColor_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_lot_wise && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtLot_check = $("#yarnLot_"+i).val();
	                if (txtLot == txtLot_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_stitch_length && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var txtStitchLength_check = $("#stichLn_"+i).val();
	                if (txtStitchLength == txtStitchLength_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if(copy_mc_dia_gg && $('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					var mcDiaGg_check = $("#mcDiaGg_"+i).val();
	                if (mcDiaGg == mcDiaGg_check)
					{
						if ( $('#txtShelfTo_'+i).prop('disabled')== false)
						{
							$("#txtBinTo_"+i).val(0);
						}
					}
				}
			}
			else if($('#shelfIds').is(':checked'))
			{
				for(var i=id; i>=1; i--)
				{
					if ( $('#txtShelfTo_'+i).prop('disabled')== false)
					{
						$("#txtBinTo_"+id).val(0);
					}
				}
			}
			else
			{
				$("#txtBinTo_"+id).val(0);
			}
		}
	}


	/*function change_floor(value,id)
    {
	    var id=id.split('_');
		var roomTd='roomTdTo_'+id[1];		
		load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller', value+"_"+roomTd, 'load_drop_down_room', roomTd);
    }

    function change_room(value,id)
    {     	
    	var id=id.split('_');
		var rackTd='rackTdTo_'+id[1];	
		load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller', value+"_"+rackTd, 'load_drop_down_rack', rackTd);
    }

    function change_rack(value,id)
    {
    	var id=id.split('_');
		var shelfTd='shelfTdTo_'+id[1];
		//alert(value+'='+id+'='+shelfTd);		
		load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller', value+"_"+shelfTd, 'load_drop_down_shelf', shelfTd);
    }

    function change_shelf(value,id)
    {
    	var id=id.split('_');
		var binTd='binTdTo_'+id[1];
		//alert(value+'='+id+'='+binTd);		
		load_drop_down( 'requires/roll_wise_grey_fabric_transfer_acknowledgement_controller', value+"_"+binTd, 'load_drop_down_bin', binTd);
    }*/
    // =============================== floor_room_rack_shelf section End =============================
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
                        <td colspan="6" align="center"><b>Acknowledge System No&nbsp;</b>
                        	<input type="text" name="txt_transfer_acknowledge_no" id="txt_transfer_acknowledge_no" class="text_boxes" style="width:140px;"  onDblClick="openmypage_acknowledgement()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Acknowledge Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2,4');
                            ?>
                        </td>

                        <td class="must_entry_caption">To Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_to_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_on_change(this.value);" );
							?>
                        </td>
                        <td class="must_entry_caption">Acknowledge Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">To Store</td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "fnc_details_row_blank();fn_load_floor(this.value);" );
                            ?>	
                        </td>
                        <td>Item Category</td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',13 );
                            ?>
                        </td>
                        <td>Transfer Basis</td>
                        <td>
                            <input type="text" name="txt_transfer_no" id="txt_transfer_no" class="text_boxes" style="width:148px;" placeholder="Double Click To Search" onDblClick="openmypage_transfer();"/>
                        	<input type="hidden" name="txt_transfer_mst_id" id="txt_transfer_mst_id"/>
                        	<input type="hidden" name="cbo_company_id" id="cbo_company_id"/>
                        	<input type="hidden" name="store_update_upto" id="store_update_upto">
                        	<input type="hidden" name="fso_yes_no_type" id="fso_yes_no_type">
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                    	<td>Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px;" />
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:2230px;text-align:left;">
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
                <legend>
                    Roll Details
                    <span style="margin-left: 650px">
                        Color Wise <input type="checkbox" id="copy_color_wise" name="copy_color_wise"/>
						&nbsp;
                        Lot Wise <input type="checkbox" id="copy_lot_wise" name="copy_lot_wise" />
                        &nbsp;
                        Stitch Length Wise <input type="checkbox" id="copy_stitch_length" name="copy_stitch_length"/>
                        &nbsp;
                        Machine Dia X GG <input type="checkbox" id="copy_mc_dia_gg" name="copy_mc_dia_gg"/>
					</span>
                </legend>
                <div id="test_dv"></div>
				<table cellpadding="0" width="2210" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="20">SL</th>
                        <th width="100">From Store</th>
                        <th width="70">Barcode No</th>
                        <th width="45">Roll No</th>
                        <th width="80">Body Part</th>
                        <th width="150">Construction/ Composition</th>
                        <th width="150">Yarn Lot</th>
                        <th width="100">Machine Dia X GG</th>
                        <th width="100">Stitch Length</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="70">Color</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Buyer</th>
                        <th width="120">Booking</th>
                        <th width="80">Job No/FSO</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Order No</th>
                        <th width="100">Knit Company</th>
                        <th width="70">Basis</th>
                        <th width="90">To Order</th>
                        <th width="80">To Job/FSO</th>
                    	<th width="50">Floor<br><input type="checkbox" checked id="floorIds" name="floorIds"/></th>
						<th width="50">Room<br><input type="checkbox" checked id="roomIds" name="roomIds"/></th>
						<th width="50">Rack<br><input type="checkbox" checked id="rackIds" name="rackIds"/></th>
						<th width="50">Shelf<br><input type="checkbox" checked id="shelfIds" name="shelfIds"/></th>
						<th width="50">Bin/Box<br><input type="checkbox" checked id="binIds" name="binIds"/></th>
                        <th width="115">Knit. Delivery Challan</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:2230px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="2210" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr__1" align="center" valign="middle">
                                <td width="20" id="sl_1"></td>
                                <td width="100" id="fromStore_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="45" id="roll_1"></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="150" id="cons_1" align="left" title=""></td>
                                <td style="word-break:break-all;" width="150" id="yLot_1" align="left" title=""></td>
                                <td style="word-break:break-all;" width="100" id="machine_1" align="left" title=""></td>
                                <td style="word-break:break-all;" width="100" id="stitchLength_1" title=""></td>
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
                                <td style="word-break:break-all;" width="90" id="toOrder_1"></td>
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
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="yarnLot[]" id="yarnLot_1"/>
                                    <input type="hidden" name="yarnCount[]" id="yarnCount_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="stichLn[]" id="stichLn_1"/>
                                    <input type="hidden" name="mcDiaGg[]" id="mcDiaGg_1"/>
                                    <input type="hidden" name="brandId[]" id="brandId_1"/>
                                    <input type="hidden" name="fromStoreId[]" id="fromStoreId_1"/>
                                    <input type="hidden" name="floor[]" id="floor_1"/>
                                    <input type="hidden" name="room[]" id="room_1"/>
                                    <input type="hidden" name="rack[]" id="rack_1"/>
                                    <input type="hidden" name="shelf[]" id="shelf_1"/>
                                    <input type="hidden" name="bin[]" id="bin_1"/>
                                    <input type="hidden" name="toOrderId[]" id="toOrderId_1"/>
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
                                    <input type="hidden" name="toBodyPart[]" id="toBodyPart_1"/>
                                    <input type="hidden" name="consRate[]" id="consRate_1"/>
                                    <input type="hidden" name="consAmount[]" id="consAmount_1"/>
                                    <input type="hidden" name="programNo[]" id="programNo_1"/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                        	<th colspan="12" style="text-align: right;"><strong>Total Qnty</strong></th>
                        	<th style="text-align: right;" id="total_rollwgt"><strong></strong></th>
                        	<th colspan="18"></th>
                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="2050" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
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
                            	echo load_submit_buttons($permission,"fnc_grey_fabric_issue_roll_wise",0,1,"fnc_reset_form(1);page_reload()",1);
                            ?>
                             <!-- <input type="button" name="btn_mc" id="btn_mc" class="formbutton" value="Total Roll Wise" style=" width:100px;" onClick="fnc_grey_fabric_issue_roll_wise(5);" >

                             <input type="button" name="btn_mc_6" id="btn_mc_6" class="formbutton" value="Total Roll Wise-2" style=" width:100px;" onClick="fnc_grey_fabric_issue_roll_wise(6);" >

                             <input type="button" name="print_2" id="print_2" class="formbutton" value="print 2" style="width:100px;" onClick="fnc_grey_fabric_issue_roll_wise(7);" >
                             <input type="button" name="print_3" id="print_3" class="formbutton" value="print 3" style="width:100px;" onClick="fnc_grey_fabric_issue_roll_wise(8);" > -->
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	setFilterGrid("scanning_tbl",-1,tableFilters);
</script>
</html>
