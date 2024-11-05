<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Issue Return Roll Wise
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	30-11-2015
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
echo load_html_head_contents("Grey Fabric Issue Return Roll Wise","../../", 1, 1, $unicode,'',''); 

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var tableFilters = 
	{
		col_0: "none",
		col_operation: {
		id: ["total_rollwgt","total_qtyInPcs"],
		col: [11,12],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"],
		}
	} 

	var scanned_barcode=new Array();
	var scanned_barcode_issue =new Array();
	var batch_batcode_arr=new Array();
	var barcode_rollTableId_array=new Array();
	var barcode_trnasId_array =new Array();
	var barcode_dtlsId_array=new Array();
	var barcode_trnasId_to_array=new Array();
	<?
	$floor_room_rack_array=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	$jsbarcode_floor_array= json_encode($floor_room_rack_array);
	//echo "var jsfloor_room_rack_shelf_name_array = ". $jsbarcode_floor_array . ";\n";
	?>
	function openmypage_issue()
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_rtn_roll_wise_controller.php?action=issue_popup','Transfer Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form();
				get_php_form_data(issue_id, "populate_data_from_data", "requires/grey_fabric_issue_rtn_roll_wise_controller");
				var com_id=$('#cbo_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var location_id=$('#cbo_location_name').val();
				var all_data=com_id + "__" + store_id + "__" + location_id;
				//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
				var JSONObject = JSON.parse(floor_result);

				var floor_id = Object.keys(JSONObject);
				var all_floor_id = floor_id.join(",");
				if(all_floor_id==""){all_floor_id=0;}
				if(all_floor_id!=0)
				{
					// alert(all_floor_id);
					var all_data2=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id;
					var room_result = return_global_ajax_value(all_data2, 'room_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');

					var JSONObjectRoom = JSON.parse(room_result);
					var room_id = Object.keys(JSONObjectRoom);
					var all_room_id = room_id.join(",");
					//alert(all_floor_id+'=='+all_room_id);
					var all_data3=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id + "__" + all_room_id;
					//alert(all_data3);
					var rack_result = return_global_ajax_value(all_data3, 'rack_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
					var JSONObjectRack = JSON.parse(rack_result);

					var rack_id = Object.keys(JSONObjectRack);					
					var all_rack_id = rack_id.join(",");
					var all_data4=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id;
					var self_result = return_global_ajax_value(all_data4, 'self_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
					var tbl_length=$('#scanning_tbl tbody tr').length;
					//alert(floor_result+"="+tbl_length);//return;					
					var JSONObjectSelf = JSON.parse(self_result);

					var shelf_id = Object.keys(JSONObjectSelf);					
					var all_shelf_id = shelf_id.join(",");
					var all_data5=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id+ "__" + all_shelf_id;
					var bin_result = return_global_ajax_value(all_data5, 'bin_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
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
				create_row(1,issue_id);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_return_roll_wise',1);
			}
		}
	}
	
	function set_master_form_data(ref_no,is_barcode)
	{
		var company_id=$('#cbo_company_id').val();
		if(company_id==0)
		{
			get_php_form_data(ref_no+'__'+is_barcode, "populate_master_from_data", "requires/grey_fabric_issue_rtn_roll_wise_controller");
		}
		
		if($('#txt_system_no').val() =="")
		{
			load_drop_down( 'requires/grey_fabric_issue_rtn_roll_wise_controller', $('#cbo_company_id').val(), 'load_drop_down_store', 'store_td' );
		}
		
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_rtn_roll_wise_controller.php?company_id='+company_id+'&action=barcode_popup','Barcode Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			get_php_form_data(barcode_nos+'__'+1, "populate_master_from_data", "requires/grey_fabric_issue_rtn_roll_wise_controller");
			load_drop_down( 'requires/grey_fabric_issue_rtn_roll_wise_controller', $('#cbo_company_id').val(), 'load_drop_down_store', 'store_td' );
			if(barcode_nos!="")
			{
				create_row(0,barcode_nos);
				
				/* var proQtyTotal =0;
				var qcQntyTotal = 0;
				$("#scanning_tbl").find('tbody tr').each(function()
				{
					proQtyTotal+=$(this).find('td:nth-child(18)').html()*1;
					qcQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
				 });
				 //alert(qcQntyTotal);
				 $("#total_prodQnty").html(proQtyTotal);*/
				set_all_onclick();
			}
		}
	}
	
	function openmypage_issuechallan()
	{
		var company_id=$('#cbo_company_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_rtn_roll_wise_controller.php?company_id='+company_id+'&action=challan_popup','Challan Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_nos=this.contentDoc.getElementById("hidden_issue_id").value.split(","); //Issue Nos
			$('#cbo_company_id').val(issue_nos[1]);
			$('#txt_issue_id').val(issue_nos[2]);
			$('#store_update_upto').val(issue_nos[3]);
			
			if(issue_nos[0]!="")
			{
				create_row(2,issue_nos[0]);
				set_all_onclick();
				load_drop_down( 'requires/grey_fabric_issue_rtn_roll_wise_controller', issue_nos[1], 'load_drop_down_store', 'store_td' );
			}
		}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/grey_fabric_issue_rtn_roll_wise_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_fabric_issue_return_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_return_print');
			return;
		}
		if(operation==5)
		{
			var update_id=$('#update_id').val();
			if(update_id<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_return_print_grouping');
			return;
		}
		if(operation==6)
		{
			var update_id=$('#update_id').val();
			if(update_id<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_return_print2');
			return;
		}
		
		if(form_validation('cbo_company_id*cbo_store_name*txt_issue_rtn_date','From Company*To Store*Transfer Date')==false)
		{
			return; 
		}
                
        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_rtn_date').val(), current_date)==false)
		{
			alert("Return Date Can not Be Greater Than Current Date");
			return;
		}
                
                
		var j=0; var dataString='';var m=1;
		var store_update_upto=$('#store_update_upto').val()*1;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			//if($('#txtcheck_1').attr('checked'))
			if($(this).find('input[name="txtcheck[]"]').attr('checked'))
			{
				//var fromStoreId=$(this).find('input[name="fromStoreId[]"]').val();
				//var toOrderId=$(this).find('input[name="toOrderId[]"]').val();
				var floor = 0;var room=0;var rack=0;var shelf=0;var bin=0;
				if(store_update_upto > 1)
				{
					var floor=$(this).find('select[name="cbo_floor_to[]"]').val();
					var room=$(this).find('select[name="cbo_room_to[]"]').val();
					var rack=$(this).find('select[name="txt_rack_to[]"]').val();
					var shelf=$(this).find('select[name="txt_shelf_to[]"]').val();
					var bin=$(this).find('select[name="txt_bin_to[]"]').val();
					
					if(store_update_upto==5 && (floor==0 || room==0 || rack==0 || shelf==0))
					{
						alert("Up To Shelf Value Full Fill Required For Inventory");return;
					}
					else if(store_update_upto==4 && (floor==0 || room==0 || rack==0))
					{
						alert("Up To Rack Value Full Fill Required For Inventory");return;
					}
					else if(store_update_upto==3 && (floor==0 || room==0))
					{
						alert("Up To Room Value Full Fill Required For Inventory");return;
					}
					else if(store_update_upto==2 && floor==0)
					{
						alert("Up To Floor Value Full Fill Required For Inventory");return;
					}
				}
				else
				{
					var floor=$(this).find('select[name="cbo_floor_to[]"]').val();
					var room=$(this).find('select[name="cbo_room_to[]"]').val();
					var rack=$(this).find('select[name="txt_rack_to[]"]').val();
					var shelf=$(this).find('select[name="txt_shelf_to[]"]').val();
					var bin=$(this).find('select[name="txt_bin_to[]"]').val();
				}

				
				
				// alert(store_update_upto+'Fl='+floor+' Ro= '+room+' Ra= '+rack+' S= '+shelf);return;

				var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
				var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
				var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
				var productId=$(this).find('input[name="productId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var BookWithoutOrd=$(this).find('input[name="BookWithoutOrd[]"]').val();
				var rollId=$(this).find('input[name="rollId[]"]').val();
				var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				var rollNo=$(this).find("td:eq(3)").text();
				
				var yarnLot=$(this).find('input[name="yarnLot[]"]').val();
				var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var stichLn=$(this).find('input[name="stichLn[]"]').val();
				var brandId=$(this).find('input[name="brandId[]"]').val();

				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				var transId=$(this).find('input[name="transId[]"]').val();
				var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
				var febDescripId=$(this).find('input[name="febDescripId[]"]').val();
				var machineNoId=$(this).find('input[name="machineNoId[]"]').val();
				var gsm=$(this).find('input[name="prodGsm[]"]').val();
				var diaWidth=$(this).find('input[name="diaWidth[]"]').val();
				var rollRate=$(this).find('input[name="rollRate[]"]').val();
				var rollAmt=$(this).find('input[name="rollAmt[]"]').val();
				var colorRange=$(this).find('input[name="colorRange[]"]').val();
				var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
				var is_sales=$(this).find('input[name="isSales[]"]').val();
				var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();

				var yarnRate=$(this).find('input[name="yarnRate[]"]').val();
				var knittingCharge=$(this).find('input[name="knittingCharge[]"]').val();
	
				j++;
				dataString+='&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId+ '&BookWithoutOrd_' + j + '=' + BookWithoutOrd + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&yarnLot_' + j + '=' + yarnLot + '&yarnCount_' + j + '=' + yarnCount + '&colorId_' + j + '=' + colorId + '&stichLn_' + j + '=' + stichLn + '&brandId_' + j + '=' + brandId + '&floorId_' + j + '=' + floor + '&roomId_' + j + '=' + room + '&rack_' + j + '=' + rack + '&shelf_' + j + '=' + shelf + '&bin_' + j + '=' + bin + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&febDescripId_' + j + '=' + febDescripId+ '&machineNoId_' + j + '=' + machineNoId+ '&gsm_' + j + '=' + gsm+ '&diaWidth_' + j + '=' + diaWidth+ '&rollRate_' + j + '=' + rollRate+ '&rollAmt_' + j + '=' + rollAmt+ '&colorRange_' + j + '=' + colorRange+ '&hiddenQtyInPcs_' + j + '=' + hiddenQtyInPcs+ '&isSales_' + j + '=' + is_sales + '&bodyPartId_' + j + '=' + bodyPartId + '&yarnRate_' + j + '=' + yarnRate + '&knittingCharge_' + j + '=' + knittingCharge;
			}
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
        disable_enable_fields("txt_issue_challan_no*cbo_company_id",1);

		// alert(dataString);

		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*update_id*txt_issue_id*cbo_company_id*cbo_store_name*txt_challan_no*cbo_location_name*txt_issue_rtn_date*txt_challan_no',"../../")+dataString;
		// alert(data);return;
		
		freeze_window(operation);
		
		http.open("POST","requires/grey_fabric_issue_rtn_roll_wise_controller.php",true);
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
            if(response[0]*1==20*1)
            {
                release_freezing();
                alert(response[1]);
                return;
            }
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				fnc_reset_form();
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_no').value = response[2];

				get_php_form_data(response[1], "populate_data_from_data", "requires/grey_fabric_issue_rtn_roll_wise_controller");
				var com_id=$('#cbo_company_id').val();
				var store_id=$('#cbo_store_name').val();
				var location_id=$('#cbo_location_name').val();
				var all_data=com_id + "__" + store_id + "__" + location_id;
				//alert(all_data);return;
				var floor_result = return_global_ajax_value(all_data, 'floor_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
				var JSONObject = JSON.parse(floor_result);

				var floor_id = Object.keys(JSONObject);
				var all_floor_id = floor_id.join(",");
				if(all_floor_id==""){all_floor_id=0;}
				if(all_floor_id!=0)
				{
					// alert(all_floor_id);
					var all_data2=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id;
					var room_result = return_global_ajax_value(all_data2, 'room_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');

					var JSONObjectRoom = JSON.parse(room_result);
					var room_id = Object.keys(JSONObjectRoom);
					var all_room_id = room_id.join(",");
					//alert(all_floor_id+'=='+all_room_id);
					var all_data3=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id + "__" + all_room_id;
					//alert(all_data3);
					var rack_result = return_global_ajax_value(all_data3, 'rack_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
					var JSONObjectRack = JSON.parse(rack_result);

					var rack_id = Object.keys(JSONObjectRack);					
					var all_rack_id = rack_id.join(",");
					var all_data4=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id;
					var self_result = return_global_ajax_value(all_data4, 'self_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
					var tbl_length=$('#scanning_tbl tbody tr').length;
					// alert(floor_result+"="+tbl_length);//return;					
					var JSONObjectSelf = JSON.parse(self_result);

					var shelf_id = Object.keys(JSONObjectSelf);					
					var all_shelf_id = shelf_id.join(",");
					var all_data5=com_id + "__" + store_id + "__" + location_id + "__" + all_floor_id + "__" + all_room_id + "__" + all_rack_id+ "__" + all_shelf_id;
					var bin_result = return_global_ajax_value(all_data5, 'bin_list1', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
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
			    create_row(1,response[1]);

				$('#cbo_transfer_criteria').attr('disabled',true);
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_to_company_id').attr('disabled',true);
				$('#txt_deleted_id').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_return_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');

			}
			release_freezing();
		}
	}
	
	
	//var scanned_barcode=new Array();
	function create_row(is_update,barcode_no)
	{
		var row_num=$('#txt_tot_row').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 
		
		var cbo_company_id = $('#cbo_company_id').val();
		var system_id=$('#update_id').val();
		if(is_update==0) // save
		{
			var barcode_data=return_global_ajax_value( bar_code, 'populate_barcode_data', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
			//alert( barcode_data); //return;
			// setFilterGrid("scanning_tbl",-1,tableFilters);
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			//alert(barcode_data_ref[0]);return;
			var total_roll_wgt=0;
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
				var buyer_name=barcode_data_all[32];
				var po_number=barcode_data_all[33];
				var file_no=barcode_data_all[34];
				var color_id=barcode_data_all[35];
				var store_name=barcode_data_all[36];
				var bordy_part_id=barcode_data_all[37];
				var brand_id=barcode_data_all[38];
				var machine_name=barcode_data_all[39];
				var machine_no_id=barcode_data_all[40];
				var entry_form=barcode_data_all[41];
				var issue_roll_tbl_id=barcode_data_all[42];
				//var roll_rate=barcode_data_all[43];
				var roll_amt=barcode_data_all[44];
				var color_range_id=barcode_data_all[45];
				var booking_without_order=barcode_data_all[46];
				var qtyInPcs=barcode_data_all[47];
				var is_sales=barcode_data_all[48];
				var body_part_id=barcode_data_all[49];

				var yarn_rate=barcode_data_all[50];
				var kniting_charge=barcode_data_all[51];
				var roll_rate=barcode_data_all[52];

				/*var floor_id=barcode_data_all[47];	
				var room_id=barcode_data_all[48];			
				var floor_name=jsfloor_room_rack_shelf_name_array[floor_id];
				var room_name=jsfloor_room_rack_shelf_name_array[room_id];
				var rack_name=jsfloor_room_rack_shelf_name_array[rack];
				var shelf_name=jsfloor_room_rack_shelf_name_array[self];*/
				//alert(barcode_data_all[47]);
				
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
				
				if( jQuery.inArray( barcode_no, scanned_barcode )>-1) 
				{ 
					alert('Sorry! Barcode Already Scanned.'); 
					$('#txt_bar_code_num').val('');
					return; 
				}
				// alert('ok');
				//alert(barcode_data);return;
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

						  'id': function(_, id) { 
						  	var id=id.split("_"); 
						  	
						  	if(id.length>3){
						  		var check_id=id[0] +"_"+ id[1] +"_"+ id[2];
						  		if(check_id=="cbo_floor_to")
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
				//batch_batcode_arr.push(barcode_no);
				
				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				$("#showStichLn_"+row_num).text(stitch_length);
				$("#showLot_"+row_num).text(yarn_lot);
				//$("#diaType_"+row_num).text('');machine_name
				$("#rollWeight_"+row_num).text(qnty);
				$("#qtyInPcs_"+row_num).text(qtyInPcs);
				$("#buyer_"+row_num).text(buyer_name);
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#file_"+row_num).text(file_no);
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#machine_"+row_num).text(machine_name);
				$("#progBookPiNo_"+row_num).text(booking_no);
				$("#prodBasis_"+row_num).text(receive_basis);
				
				//alert(total_roll_wgt);
				
				$("#cbo_floor_to_"+row_num).val('');
				$("#cbo_room_to_"+row_num).val('');
				$("#txt_rack_to_"+row_num).val('');
				$("#txt_shelf_to_"+row_num).val('');
				$("#txt_bin_to_"+row_num).val('');
				
				
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#BookWithoutOrd_"+row_num).val(booking_without_order);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);

				//$("#floorId_"+row_num).val(floor_id);
				//$("#roomId_"+row_num).val(room_id);
				$("#rackId_"+row_num).val(rack);
				$("#shelfId_"+row_num).val(self);

				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#rolltableId_"+row_num).val(issue_roll_tbl_id);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollAmt_"+row_num).val(roll_amt);
				$("#colorRange_"+row_num).val(color_range_id);
				$("#hiddenQtyInPcs_"+row_num).val(qtyInPcs);
				$("#isSales_"+row_num).val(is_sales);
				$("#bodyPartId_"+row_num).val(body_part_id);

				$("#yarnRate_"+row_num).val(yarn_rate);
				$("#knittingCharge_"+row_num).val(kniting_charge);
			}
			
			$('#txt_tot_row').val(row_num);
			//$('#total_rollwgt').val(total_roll_wgt);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			
			//barcode_data_all=barcode_data.split("**");
			//alert(total_roll_wgt);//return;			
		}
		else if(is_update==2) // issue scan
		{
			var barcode_data=return_global_ajax_value( bar_code, 'populate_issue_data', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
			setFilterGrid("scanning_tbl",-1,tableFilters);
			var barcode_data_all=new Array(); var barcode_data_ref=new Array(); var barcode_data_ref_withrcv = new Array();
			barcode_data_ref_withrcv=barcode_data.split("####");
			barcode_data_ref=barcode_data_ref_withrcv[0].split("__");
			//alert(barcode_data_ref[0]);return;
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
				var buyer_name=barcode_data_all[32];
				var po_number=barcode_data_all[33];
				var file_no=barcode_data_all[34];
				var color_id=barcode_data_all[35];
				var store_name=barcode_data_all[36];
				var bordy_part_id=barcode_data_all[37];
				var brand_id=barcode_data_all[38];
				var machine_name=barcode_data_all[39];
				var machine_no_id=barcode_data_all[40];
				var entry_form=barcode_data_all[41];
				var issue_roll_tbl_id=barcode_data_all[42];
				//var roll_rate=barcode_data_all[43];
				var roll_amt=barcode_data_all[44];
				var color_range_id=barcode_data_all[45];
				var booking_without_order=barcode_data_all[46];
				var qtyInPcs=barcode_data_all[47];
				var is_sales=barcode_data_all[48];
				var body_part_id=barcode_data_all[49];

				var yarn_rate=barcode_data_all[50];
				var kniting_charge=barcode_data_all[51];
				var roll_rate=barcode_data_all[52];

				/*var floor_id=barcode_data_all[47];
				var room_id=barcode_data_all[48];
				var floor_name=jsfloor_room_rack_shelf_name_array[floor_id];
				var room_name=jsfloor_room_rack_shelf_name_array[room_id];
				var rack_name=jsfloor_room_rack_shelf_name_array[rack];
				var shelf_name=jsfloor_room_rack_shelf_name_array[self];*/
				//alert(qtyInPcs);

				if(barcode_data_all[0]==0)
				{
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_issue_challan_no').val('');
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
				
				if( jQuery.inArray( barcode_no, scanned_barcode )>-1) 
				{ 
					alert('Sorry! Barcode Already Scanned.'); 
					$('#txt_issue_challan_no').val('');
					return; 
				}
				
				//alert(barcode_data);return;
				
				
				var bar_code_no=$('#barcodeNo_'+row_num).val();
				if(bar_code_no!="")
				{
					row_num++;
					$("#scanning_tbl tbody tr:last-child").clone().find("td,input,select").each(function()
					{
						onchange_value="";
						$(this).attr({ 
						  'id': function(_, id) { 
						  	var id=id.split("_"); 
						  	if(id.length>3){
						  		var check_id=id[0] +"_"+ id[1] +"_"+ id[2];
						  		if(check_id=="cbo_floor_to")
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
				//batch_batcode_arr.push(barcode_no);
				
				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				$("#showStichLn_"+row_num).text(stitch_length);
				$("#showLot_"+row_num).text(yarn_lot);
				//$("#diaType_"+row_num).text('');machine_name
				$("#rollWeight_"+row_num).text(qnty);
				$("#qtyInPcs_"+row_num).text(qtyInPcs);
				
				$("#buyer_"+row_num).text(buyer_name);
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#file_"+row_num).text(file_no);
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#machine_"+row_num).text(machine_name);
				$("#progBookPiNo_"+row_num).text(booking_no);
				$("#prodBasis_"+row_num).text(receive_basis);
				
				/*$("#floor_"+row_num).text(floor_name);
				$("#room_"+row_num).text(room_name);
				$("#rack_"+row_num).text(rack_name);
				$("#shelf_"+row_num).text(shelf_name);*/

				$("#cbo_floor_to_"+row_num).val('');
				$("#cbo_room_to_"+row_num).val('');
				$("#txt_rack_to_"+row_num).val('');
				$("#txt_shelf_to_"+row_num).val('');
				$("#txt_bin_to_"+row_num).val('');
				
				
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#BookWithoutOrd_"+row_num).val(booking_without_order);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);

				//$("#floorId_"+row_num).val(floor_id);
				//$("#roomId_"+row_num).val(room_id);
				$("#rackId_"+row_num).val(rack);
				$("#shelfId_"+row_num).val(self);

				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#rolltableId_"+row_num).val(issue_roll_tbl_id);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollAmt_"+row_num).val(roll_amt);
				$("#colorRange_"+row_num).val(color_range_id);
				$("#hiddenQtyInPcs_"+row_num).val(qtyInPcs);
				$("#isSales_"+row_num).val(is_sales);
				$("#bodyPartId_"+row_num).val(body_part_id);

				$("#yarnRate_"+row_num).val(yarn_rate);
				$("#knittingCharge_"+row_num).val(kniting_charge);
			}
			if(barcode_data_ref_withrcv[1]!=""){
				alert('Receive for batch Found.\nReceive no : '+barcode_data_ref_withrcv[1]);
			}
			
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			//barcode_data_all=barcode_data.split("**");
			//alert(barcode_data);return;
		}
		else // update
		{
			//var barcode_data=return_global_ajax_value( bar_code+'**'+system_id, 'populate_barcode_data_update', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
			var barcode_data=return_global_ajax_value(bar_code, 'populate_barcode_data_update', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
			//alert(barcode_data);return;
			
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			//alert(barcode_data_ref[0]);return;
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
				var buyer_name=barcode_data_all[32];
				var po_number=barcode_data_all[33];
				var file_no=barcode_data_all[34];
				var color_id=barcode_data_all[35];
				var store_name=barcode_data_all[36];
				var bordy_part_id=barcode_data_all[37];
				var brand_id=barcode_data_all[38];
				var machine_name=barcode_data_all[39];
				var machine_no_id=barcode_data_all[40];
				var entry_form=barcode_data_all[41];
				var issue_roll_tbl_id=barcode_data_all[42];
				//var roll_rate=barcode_data_all[43];
				var roll_amt=barcode_data_all[44];
				var color_range_id=barcode_data_all[45];
				var update_dtls_id=barcode_data_all[46];
				var booking_without_order=barcode_data_all[47];
				var floor_id=barcode_data_all[48];
				var room_id=barcode_data_all[49];
				var rack_id=barcode_data_all[50];
				var self_id=barcode_data_all[51];
				var qtyInPcs=barcode_data_all[52];
				var is_sales=barcode_data_all[53];
				var bin_id=barcode_data_all[54];
				var trans_id=barcode_data_all[55];
				var body_part_id=barcode_data_all[56];

				var yarn_rate=barcode_data_all[57];
				var kniting_charge=barcode_data_all[58];
				var roll_rate=barcode_data_all[59];
				
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
						  		if(check_id=="cbo_floor_to")
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
				
				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				$("#showStichLn_"+row_num).text(stitch_length);
				$("#showLot_"+row_num).text(yarn_lot);
				//$("#diaType_"+row_num).text('');machine_name
				$("#rollWeight_"+row_num).text(qnty);
				$("#qtyInPcs_"+row_num).text(qtyInPcs);
				
				$("#buyer_"+row_num).text(buyer_name);
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#file_"+row_num).text(file_no);
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#machine_"+row_num).text(machine_name);
				$("#progBookPiNo_"+row_num).text(booking_no);
				$("#prodBasis_"+row_num).text(receive_basis);
				
				/*$("#floor_"+row_num).text(floor_name);
				$("#room_"+row_num).text(room_name);
				$("#rack_"+row_num).text(rack_name);
				$("#shelf_"+row_num).text(shelf_name);*/

				$("#cbo_floor_to_"+row_num).val(floor_id);
				$("#cbo_room_to_"+row_num).val(room_id);
				$("#txt_rack_to_"+row_num).val(rack_id);
				$("#txt_shelf_to_"+row_num).val(self_id);
				$("#txt_bin_to_"+row_num).val(bin_id);
				
				
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#BookWithoutOrd_"+row_num).val(booking_without_order);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);

				//$("#floorId_"+row_num).val(floor_id);
				//$("#roomId_"+row_num).val(room_id);
				$("#rackId_"+row_num).val(rack);
				$("#shelfId_"+row_num).val(self);

				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#rolltableId_"+row_num).val(issue_roll_tbl_id);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollAmt_"+row_num).val(roll_amt);
				$("#colorRange_"+row_num).val(color_range_id);
				$("#dtlsId_"+row_num).val(update_dtls_id);
				$("#hiddenQtyInPcs_"+row_num).val(qtyInPcs);
				$("#isSales_"+row_num).val(is_sales);
				$("#transId_"+row_num).val(trans_id);
				$("#bodyPartId_"+row_num).val(body_part_id);

				$("#yarnRate_"+row_num).val(yarn_rate);
				$("#knittingCharge_"+row_num).val(kniting_charge);
				
				$("#txtcheck_"+row_num).attr('checked',true);
				$("#txtcheck_"+row_num).attr('disabled',true);
			}
			
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			setFilterGrid("scanning_tbl",-1,tableFilters);
		}
		//alert(up_roll_id);
		calculate_total();
	}
	
	function calculate_total()
	{
		var total_roll_weight=0;
		var total_roll_qtyInPcs=0;
		$("#scanning_tbl").find('tbody tr:not(.fltrow)').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
			//alert(rollWgt);
			total_roll_weight=total_roll_weight*1+rollWgt*1;
			total_roll_qtyInPcs=total_roll_qtyInPcs*1+hiddenQtyInPcs*1;
		});
	
		$("#total_rollwgt").text(total_roll_weight.toFixed(2));	
		$("#total_qtyInPcs").text(total_roll_qtyInPcs);
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
			set_master_form_data(bar_code,1);
		}
	});
	
	$('#txt_issue_challan_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var issue_no=$('#txt_issue_challan_no').val();
			create_row(2,issue_no);
			set_master_form_data(issue_no,2);
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
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
		}
		
		$("#scanning_tbl").find('tbody tr:not(.fltrow)').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			// alert('dtls'+dtlsId);
			if(dtlsId=="") 
			{
				// alert(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
			}
			if($(this).find('input[name="txtcheck[]"]').attr('checked'))
			{
				$(this).find('input[name="txtcheck[]"]').attr('disabled',true);
			}
			
		});
	}
	
	function load_room_rack_self()
	{
		/*var com = $('#cbo_floor_to_1').val();
		alert(com);return;*/
		return load_room_rack_self_bin('requires/grey_fabric_issue_rtn_roll_wise_controller*13*cbo_room_to_1*1', 'room',room_td_to, document.getElementById('cbo_company_id').value,document.getElementById('cbo_location_name').value,document.getElementById('cbo_store_name').value,document.getElementById('cbo_floor_to_1').value,'','','','','','50','H');
		//load_room_rack_self_bin($action_from,$data);
	}

	function fn_load_floor(store_id)
	{
		// alert(store_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var all_data=com_id + "__" + store_id + "__" + location_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length-1;
		//alert(floor_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(floor_result);
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cbo_floor_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject))
			{
				$('#cbo_floor_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
		// alert(floor_id);return;
		console.log(floor_id);
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		console.log(all_data);
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length-1;
		//alert(room_result+"="+tbl_length);return;
		console.log(room_result+"="+tbl_length);
		var JSONObject = JSON.parse(room_result);
		/*for(var i=1; i<=tbl_length; i++)
		{
			$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_room_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}*/

		// =============
		if($('#floorIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$('#cbo_room_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		}
		else
		{
			$('#cbo_room_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_room_to_'+sequenceNo).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
		// =======
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		// alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length-1;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);
		/*for(var i=1; i<=tbl_length; i++)
		{
			$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_rack_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}*/

		// ======
		if($('#roomIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$('#txt_rack_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		}
		else
		{
			$('#txt_rack_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_rack_to_'+sequenceNo).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
		// ==========
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length-1;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);
		/*for(var i=1; i<=tbl_length; i++)
		{
			$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_shelf_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}*/
		// =========
		if($('#rackIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$('#txt_shelf_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		}
		else
		{
			$('#txt_shelf_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_shelf_to_'+sequenceNo).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
		// =========
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var bin_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/grey_fabric_issue_rtn_roll_wise_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length-1;
		//alert(bin_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(bin_result);
		/*for(var i=1; i<=tbl_length; i++)
		{
			$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_bin_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}*/
		if($('#shelfIds').is(':checked'))
		{
			for(var i=sequenceNo; i<=tbl_length; i++)
			{
				$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
				for (var key of Object.keys(JSONObject).sort())
				{
					$('#txt_bin_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
				};
			}
		}
		else
		{
			$('#txt_bin_to_'+sequenceNo).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_bin_to_'+sequenceNo).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function copy_all(str)
	{
		var data=str.split("_");
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

		// ======================================================
        // alert($('#table_body tbody tr:not([style*="display: none"])').length-1);
        // alert($('#table_body tbody tr:visible').length-1);
		// ======================================================
		var first_tr=parseInt(data[0])+1;
		for(var k=first_tr; k<=copy_tr; k++)
		{
			if($('#floorIds').is(':checked'))
			{
				if($('#tr__'+k).is(':visible')) // html search by not in tr display: none
				{
					if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
				}
			}
			if($('#roomIds').is(':checked'))
			{
				if($('#tr__'+k).is(':visible'))
				{
					if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
				}
			}
			if($('#rackIds').is(':checked'))
			{
				if($('#tr__'+k).is(':visible'))
				{
					if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
				}
			}
			if($('#shelfIds').is(':checked'))
			{
				if($('#tr__'+k).is(':visible'))
				{
					if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
				}
			}
			if($('#binIds').is(':checked'))
			{
				if($('#tr__'+k).is(':visible'))
				{
					if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
				}
			}	
		}
	}

	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#scanning_tbl tbody tr').length-1;
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
			if($('#floorIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++) 
				{
					$("#cbo_room_to_"+i).val('');
					$("#txt_rack_to_"+i).val('');
					$("#txt_shelf_to_"+i).val('');
					$("#txt_bin_to_"+i).val('');
				}
			}
			else
			{
				$("#cbo_room_to_"+id).val('');
				$("#txt_rack_to_"+id).val('');
				$("#txt_shelf_to_"+id).val('');
				$("#txt_bin_to_"+id).val('');
			}
		}
		else if (fieldName=="cbo_room_to")  
		{
			if($('#roomIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++) 
				{
					$("#txt_rack_to_"+i).val('');
					$("#txt_shelf_to_"+i).val('');
					$("#txt_bin_to_"+i).val('');
				}
			}
			else
			{
				$("#txt_rack_to_"+id).val('');
				$("#txt_shelf_to_"+id).val('');
				$("#txt_bin_to_"+id).val('');
			}
		}
		else if (fieldName=="txt_rack_to")  
		{
			if($('#rackIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++) 
				{
					$("#txt_shelf_to_"+i).val('');
					$("#txt_bin_to_"+i).val('');
				}
			}
			else
			{
				$("#txt_shelf_to_"+id).val('');
				$("#txt_bin_to_"+id).val('');
			}
		}
		else if (fieldName=="txt_shelf_to")  
		{
			if($('#shelfIds').is(':checked'))
			{
				for (var i = id; numRow>=i; i++) 
				{
					$("#txt_bin_to_"+i).val('');
				}				
			}
			else
			{
				$("#txt_bin_to_"+id).val('');
			}
		}
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
						
		var html='<tr id="tr__1" align="center" valign="middle"><td id="button_1" align="center" width="40"><input type="checkbox" id="txtcheck_1" name="txtcheck[]" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="BookWithoutOrd[]" id="BookWithoutOrd_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/><input type="hidden" name="yarnLot[]" id="yarnLot_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="stichLn[]" id="stichLn_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="rackId[]" id="rackId_1"/><input type="hidden" name="shelfId[]" id="shelfId_1"/><input type="hidden" name="binId[]" id="binId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="febDescripId[]" id="febDescripId_1"/><input type="hidden" name="machineNoId[]" id="machineNoId_1"/><input type="hidden" name="prodGsm[]" id="prodGsm_1"/><input type="hidden" name="diaWidth[]" id="diaWidth_1"/><input type="hidden" name="rollRate[]" id="rollRate_1"/><input type="hidden" name="rollAmt[]" id="rollAmt_1"/><input type="hidden" name="colorRange[]" id="colorRange_1"/><input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/><input type="hidden" name="isSales[]" id="isSales_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="yarnRate[]" id="yarnRate_1"/><input type="hidden" name="knittingCharge[]" id="knittingCharge_1"/></td><td width="30" id="sl_1"></td><td width="70" id="barcode_1"></td><td width="45" id="roll_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="150" id="cons_1"></td><td style="word-break:break-all;" width="40" id="gsm_1"></td><td style="word-break:break-all;" width="40" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="70" id="showStichLn_1"></td><td style="word-break:break-all;" width="70" id="showLot_1"></td><td width="60" id="rollWeight_1" align="right"></td><td width="60" id="qtyInPcs_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="100" id="order_1" align="left"><td style="word-break:break-all;" width="80" id="file_1" align="left"></td><td style="word-break:break-all;" width="110" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="machine_1"></td><td width="90" id="progBookPiNo_1"></td><td width="50" align="center" id="floor_td_to" class="floor_td_to"><? echo create_drop_down( "cbo_floor_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, 1); copy_all(\'1_0\'); reset_room_rack_shelf(1,\'cbo_floor_to\');",0,"","","","","","","cbo_floor_to[]","onchange_void"); ?></td><td width="50" align="center" id="room_td_to"><? echo create_drop_down( "cbo_room_to_1", 50,"$blank_array","", 1, "--Select--", 0, "fn_load_rack(this.value, 1); copy_all(\'1_1\'); reset_room_rack_shelf(1,\'cbo_room_to\');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?></td><td width="50" align="center" id="rack_td_to"><? echo create_drop_down( "txt_rack_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, 1); copy_all(\'1_2\'); reset_room_rack_shelf(1,\'txt_rack_to\');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?></td><td width="50" align="center" id="shelf_td_to"><? echo create_drop_down( "txt_shelf_to_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, 1); copy_all(\'1_3\'\); reset_room_rack_shelf(1,\'txt_shelf_to\');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?></td><td width="50" align="center" id="bin_td_to"><? echo create_drop_down( "txt_bin_to_1", 50,$blank_array,"", 1, "--Select--", 0, "copy_all(\'1_4\'\);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?></td><td style="word-break:break-all;" width="115" id="prodBasis_1"></td></tr>';

		//load_room_rack_self();

		 

		
		
		$('#txt_system_no').val('');
		$('#update_id').val('');
		$('#txt_bar_code_num').val('');
		$('#txt_issue_challan_no').val('');
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',true);
		$('#txt_tot_row').val(1);
		
		$('#txt_issue_rtn_date').val('');
		$('#txt_challan_no').val('');
		$('#cbo_store_name').val(0);
		$('#txt_deleted_id').val('');
		$("#scanning_tbl tbody").html(html);	
	}
	
	
	function check_all()
	{
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
                        <td colspan="2"></td>
                        <td align="right"><b>System No</b></td>
                        <td>
                        	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:148px;"  onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                    	<td align="right">Barcode Number</td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:150px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td align="right">Issue Number</td>
                        <td>
                            <input type="text" name="txt_issue_challan_no" id="txt_issue_challan_no" class="text_boxes" style="width:150px;" onDblClick="openmypage_issuechallan()" placeholder="Browse/Write/scan"/>
                            <input type="hidden" id="txt_issue_id" name="txt_issue_id" />
                        </td>
                        
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption">To Store</td>
                        <td id = "store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "fn_load_floor(this.value);" );
                            ?>	
                        </td>
                        <input type="hidden" name="cbo_location_name" id="cbo_location_name">
                        <input type="hidden" name="store_update_upto" id="store_update_upto" readonly>

                        <td align="right" class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_issue_rtn_date" id="txt_issue_rtn_date" value="<?echo date('d-m-Y')?>" class="datepicker" style="width:150px;" readonly placeholder="Select Date" />
                        </td>
                        <td align="right">Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <!--<tr>
                    </tr>-->
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1825px;text-align:left">
				<style>
                    #scanning_tbl tr td
                    {
                        /*background-color:#FFF;*/
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;
                        /*height:20px;*/
                        overflow:auto;
                    }
                </style>
                <div id="test_dv"></div>
				<table cellpadding="0" width="1805" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40">Check All <br><input type="checkbox" id="txt_check_all" name="txt_check_all" onClick="check_all(this.value)" /></th>
                    	<th width="30">SL</th>
                        <th width="70">Barcode No</th>
                        <th width="45">Roll No</th>
                        <th width="80">Body Part</th>
                        <th width="150">Construction/ Composition</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="70">Color</th>
                        <th width="70">Stitch Length</th>
                        <th width="70">Lot</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Qty. In Pcs</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="100">Order No</th>
                        <th width="80">File No</th>
                        <th width="110">Knit Company</th>
                        <th width="70">Machine No</th>
                        <th width="90">Program/ Booking /Pi No</th>
                        <th width="50"><input type="checkbox" checked id="floorIds" name="floorIds"/><br>Floor</th>
						<th width="50"><input type="checkbox" checked id="roomIds" name="roomIds"/><br>Room</th>
						<th width="50"><input type="checkbox" checked id="rackIds" name="rackIds"/><br>Rack</th>
						<th width="50"><input type="checkbox" checked id="shelfIds" name="shelfIds"/><br>Shelf</th>
						<th width="50"><input type="checkbox" checked id="binIds" name="binIds"/><br>Bin/Box</th>
                        <th >Production Basis</th>
                    </thead>
                 </table>
                 <div style="width:1825px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1805" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr__1" align="center" valign="middle">
                            	<td id="button_1" align="center" width="40">
                                	<input type="checkbox" id="txtcheck_1" name="txtcheck[]" />
                            	  	<input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="BookWithoutOrd[]" id="BookWithoutOrd_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="yarnLot[]" id="yarnLot_1"/>
                                    <input type="hidden" name="yarnCount[]" id="yarnCount_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="stichLn[]" id="stichLn_1"/>
                                    <input type="hidden" name="brandId[]" id="brandId_1"/>
									<input type="hidden" name="rackId[]" id="rackId_1"/>
									<input type="hidden" name="shelfId[]" id="shelfId_1"/>
									<input type="hidden" name="binId[]" id="binId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="transId[]" id="transId_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="febDescripId[]" id="febDescripId_1"/>
                                    <input type="hidden" name="machineNoId[]" id="machineNoId_1"/>
                                    <input type="hidden" name="prodGsm[]" id="prodGsm_1"/>
                                    <input type="hidden" name="diaWidth[]" id="diaWidth_1"/>
                                    <input type="hidden" name="rollRate[]" id="rollRate_1"/>
                                    <input type="hidden" name="rollAmt[]" id="rollAmt_1"/>
                                    <input type="hidden" name="colorRange[]" id="colorRange_1"/>
                                    <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/>
                                    <input type="hidden" name="isSales[]" id="isSales_1"/>
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                    <input type="hidden" name="yarnRate[]" id="yarnRate_1"/>
                                	<input type="hidden" name="knittingCharge[]" id="knittingCharge_1"/>
                                </td>
                                <td width="30" id="sl_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="45" id="roll_1"></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="150" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="40" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="40" id="dia_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td style="word-break:break-all;" width="70" id="showStichLn_1"></td>
                                <td style="word-break:break-all;" width="70" id="showLot_1"></td>
                                <td width="60" align="right" id="rollWeight_1"></td>
                                <td width="60" align="right" id="qtyInPcs_1"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="100" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="80" id="file_1" align="left"></td>
                                <td style="word-break:break-all;" width="110" id="knitCompany_1"></td>
                                <td style="word-break:break-all;" width="70" id="machine_1"></td>
                                <td width="90" id="progBookPiNo_1"></td>

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
								
                                <td id="prodBasis_1"></td>
                            </tr>
                        </tbody>
                	</table>
                	<table class="rpt_table" id="tbl_footer" rules="all" width="1805" cellspacing="1" cellpadding="0">
                		<tfoot>
                            <tr>
                                <th width="40"></th>
	                        	<th width="30"></th>
	                            <th width="70"></th>
	                            <th width="45"></th>
	                            <th width="80"></th>
	                            <th width="150"></th>
	                            <th width="40"></th>
	                            <th width="40"></th>
	                            <th width="70"></td>
	                            <th width="70"></td>
	                            <th width="70">Total</td>
	                            <th width="60" id="total_rollwgt"></th>
	                         	<th width="60" id="total_qtyInPcs"></th>
                                <th colspan="13"> </th>
                            </tr>
                    	</tfoot>
                	</table>
                </div>
                <br>
                <table width="1805" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_grey_fabric_issue_return_roll_wise",0,1,"fnc_reset_form()",1);
                            ?>
                            <input type="button" class="formbutton" style="width:120px" value="Print Gropping" onClick="fnc_grey_fabric_issue_return_roll_wise(5)" />
							<input type="button" class="formbutton" style="width:120px" value="Print 2" onClick="fnc_grey_fabric_issue_return_roll_wise(6)" />
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	setFilterGrid("scanning_tbl",-1,tableFilters);
	$('#cbo_company_id').val(0);
</script>
</html>
