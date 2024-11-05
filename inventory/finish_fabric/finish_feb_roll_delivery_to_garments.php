<?
/*-- ------------------------------------------ Comments
Purpose			: 	This form will create Finish Fabric Delivery to Garments Roll Wise
Functionality	:
JS Functions	:
Created by		:	
Creation date 	: 	
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
	var scanned_barcode=new Array();

	<?
	$floor_room_rack_array=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	$jsbarcode_floor_array= json_encode($floor_room_rack_array);
		echo "var jsfloor_room_rack_shelf_name_array = ". $jsbarcode_floor_array . ";\n";
	?>
	function generate_report_file(data,action)
	{
		window.open("requires/finish_feb_roll_delivery_to_garments_controller.php?data=" + data+'&action='+action, true );
	}
	function fnc_finish_delivery_entry( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		if(operation==4) // Print
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#update_mst_id').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#cbo_store_name').val()+'*'+$('#cbo_location').val(),'finish_delivery_print'); 
			return;
		}
		if(operation==5) // Print 2
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#update_mst_id').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#cbo_store_name').val()+'*'+$('#cbo_location').val(),'finish_delivery_print2');
			return;
		}
		if(operation==6) // Print 3
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#update_mst_id').val()+'*'+$('#txt_delivery_date').val()+'*'+$('#cbo_store_name').val()+'*'+$('#cbo_location').val(),'finish_delivery_print3');
			return;
		}

		var current_date = '<? echo date("d-m-Y"); ?>';
        if (date_compare($('#txt_delivery_date').val(), current_date) == false) {
            alert("Receive Date Can not Be Greater Than Current Date");
            return;
        }

		if( form_validation('cbo_company_id*cbo_location*cbo_store_name*txt_delivery_date*txt_fso_no*cbo_party*txt_booking_no','Company*Location*Store Name*Delivery Date*FSO No*Party*Booking No')==false )
		{
			return;
		}
		var store_update_upto=$('#store_update_upto').val()*1;
		var floor_flag = 0; room_flag = 0; rack_flag = 0; shelf_flag = 0;
		var j=1; var dataString=''; var prod_ids_ref=""; var chk_active=0;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var floor=$(this).find('input[name="floorId[]"]').val();
			var room=$(this).find('input[name="roomId[]"]').val();
			var rack=$(this).find('input[name="rackId[]"]').val();
			var self=$(this).find('input[name="selfId[]"]').val();
			
			if($(this).find('input[name="checkRow[]"]').is(':checked'))
			{
				var activeId=1;
				if(store_update_upto > 1)
				{
					if(store_update_upto==5 && (floor==0 || room==0 || rack==0 || self==0))
					{
						if(shelf_flag == 0)
						{
							shelf_flag = 1;
						}
					}
					else if(store_update_upto==4 && (floor==0 || room==0 || rack==0))
					{
						if(rack_flag == 0)
						{
							rack_flag = 1;
						}							
					}
					else if(store_update_upto==3 && floor==0 || room==0)
					{
						if(room_flag == 0)
						{
							room_flag = 1;
						}
					}
					else if(store_update_upto==2 && floor==0)
					{
						if(floor_flag == 0)
						{
							floor_flag = 1;
						}
					}
				}
			}
			else
			{
				var activeId=0;
			}
			 
			var updateDetailsId=$(this).find('input[name="dtlsId[]"]').val();
			var transId=$(this).find('input[name="transId[]"]').val();
			var rollTableId=$(this).find('input[name="rollTableId[]"]').val();

			var rollId=$(this).find('input[name="rollId[]"]').val();
			var batchId=$(this).find('input[name="batchID[]"]').val();
			var bodyPart=$(this).find('input[name="bodyPartId[]"]').val();
			 var colorId=$(this).find('input[name="colorId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var buyerId=$(this).find('input[name="buyerId[]"]').val();
			var rollQty=$(this).find('input[name="rollQty[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var reprocess=$(this).find('input[name="reProcess[]"]').val();
			var preReprocess=$(this).find('input[name="prereProcess[]"]').val();
			var IsSalesId=$(this).find('input[name="IsSalesId[]"]').val();
			var rollNo=$(this).find("td:eq(2)").text();
			var rollGsm=$(this).find("td:eq(8)").text();
			var rolldia=$(this).find("td:eq(9)").text();
			var currentWgt=$(this).find('input[name="currentQty[]"]').val();
			var rejectQty=$(this).find('input[name="rejectQnty[]"]').val();
			
			var job_no=$(this).find('input[name="JobNumber[]"]').val();
			var wideTypeId=$(this).find('input[name="wideTypeId[]"]').val();
			var bookingWithoutOrder=$(this).find('input[name="bookingWithoutOrder[]"]').val();
			var bookingNumber=$("#txt_booking_no").val();
			//alert(barcodeNo + " = " + activeId);
			var systemId=trim($(this).find("td:eq(22)").text());

			
			dataString+='&rollId_' + j + '=' + rollId  + '&bodyPart_' + j + '=' + bodyPart + '&colorId_' + j + '='+colorId  + '&productId_' + j + '='+ productId + '&orderId_' + j + '=' + orderId + '&rollGsm_' + j + '=' + rollGsm + '&rollQty_' + j + '=' + rollQty + '&currentWgt_' + j + '=' + currentWgt+ '&deterId_' + j + '=' + deterId +'&rejectQty_' + j + '=' + rejectQty+ '&job_no_' + j + '=' + job_no + '&floor_' + j + '=' + floor + '&room_' + j + '=' + room + '&rack_' + j + '=' + rack + '&self_' + j + '=' + self + '&rolldia_' + j + '=' + rolldia+ '&updateDetailsId_' + j + '=' + updateDetailsId+ '&activeId_' + j + '=' + activeId+ '&barcodeNo_' + j + '=' + barcodeNo+ '&rollNo_' + j + '=' + rollNo+ '&systemId_' + j + '=' + systemId+ '&batchId_' + j + '=' + batchId+ '&wideTypeId_' + j + '=' + wideTypeId+ '&buyerId_' + j + '=' + buyerId+ '&rollTableId_' + j + '=' + rollTableId+ '&transId_' + j + '=' + transId+ '&reprocess_' + j + '=' + reprocess+ '&preReprocess_' + j + '=' + preReprocess+ '&IsSalesId_' + j + '=' + IsSalesId + '&bookingWithoutOrder_' + j + '=' + bookingWithoutOrder+ '&bookingNumber_' + j + '=' + bookingNumber;

			prod_ids_ref += '&productId_' + j + '='+ productId; 

			chk_active += activeId*1;
			j++;
		});

		// Store upto validation start
		if(shelf_flag == 1)
		{
			alert("Up To Shelf Value Full Fill Required For Inventory");return;
		}
		else if(rack_flag == 1)
		{
			alert("Up To Rack Value Full Fill Required For Inventory");return;
		}
		else if(room_flag == 1)
		{
			alert("Up To Room Value Full Fill Required For Inventory");return;
		}
		else if(floor_flag == 1)
		{
			alert("Up To Floor Value Full Fill Required For Inventory");return;
		}
		// Store upto validation End

		if(chk_active<1)
		{
			alert("Plz Select atleast One roll.");
			$("#checkRow_1").focus();
			return;
		}

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location*txt_system_id*update_mst_id*txt_delivery_date*txt_fso_no*hdn_fso_id*hdn_buyer_id*txt_po_job*hdn_within_group*cbo_party*cbo_store_name*hdn_booking_id*txt_booking_no*txt_tot_row*txt_coure_tube*txt_remarks*txt_delivery_addr*txt_challan_no',"../../") + dataString;
		// alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_feb_roll_delivery_to_garments_controller.php",true);
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

					$('#cbo_company_id').attr('disabled','disabled');
					$('#txt_fso_no').attr('disabled','disabled');
					$('#cbo_location').attr('disabled','disabled');
					$('#cbo_store_name').attr('disabled','disabled');

					var hdn_fso_id = $("#hdn_fso_id").val();
					var cbo_company_id = $('#cbo_company_id').val();
					var cbo_store_name = $('#cbo_store_name').val();
					var sales_booking_no = $('#txt_booking_no').val();

					var html=return_global_ajax_value(reponse[1], 'populate_barcode_data_update', '', 'requires/finish_feb_roll_delivery_to_garments_controller');

					//reset_form('finishFabricEntry_1','','','','','cbo_company_id*cbo_location*txt_system_id*txt_delivery_date*txt_fso_no*hdn_fso_id*hdn_buyer_id*txt_po_job*cbo_party*update_mst_id*txt_booking_no*hdn_booking_id*cbo_store_name');

					set_button_status(1, permission, 'fnc_finish_delivery_entry',1);
				}
				release_freezing();
			}
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
			var page_link = 'requires/finish_feb_roll_delivery_to_garments_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

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

				get_php_form_data(system_id, "populate_data_from_to_garments","requires/finish_feb_roll_delivery_to_garments_controller" );


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


				$('#txt_booking_no').attr('disabled','disabled');
				$('#txt_batch_no').attr('readonly','readonly');
				$('#txt_fso_no').attr('readonly','readonly');

				load_drop_down('requires/finish_feb_roll_delivery_to_garments_controller', po_company_id+"_"+within_group, 'load_drop_down_company', 'party_td' );
				$('#cbo_party').val(po_company_id).attr('disabled','disabled');

				var html=return_global_ajax_value(system_id, 'populate_barcode_data_update', '', 'requires/finish_feb_roll_delivery_to_garments_controller');

				if(trim(html)!="")
				{
					$("#scanning_tbl tbody").html(html);
					calculate_total();
				}

				set_button_status(1, permission, 'fnc_finish_delivery_entry',1);
			}
		}
	}


	function openmypage_barcode()
	{
		var company_id=$('#cbo_company_id').val();
		var cbo_store_name=$('#cbo_store_name').val();
		var cbo_location=$('#cbo_location').val();
		var fabric_sales_order_id=$('#hdn_fso_id').val();
		if (form_validation('cbo_company_id*cbo_location*cbo_store_name','Company*Location*Store')==false)
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_feb_roll_delivery_to_garments_controller.php?company_id='+company_id + '&store_id=' + cbo_store_name + '&cbo_location='+ cbo_location + '&fabric_sales_order_id='+ fabric_sales_order_id +'&action=barcode_popup','Barcode Popup', 'width=1360px,height=380px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value;			
			if(barcode_nos!="")
			{
				create_row(barcode_nos);
			}
		}
	}

	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(bar_code);
		}
	});

	function create_row(barcode_no)
	{
		var prev_fso_id=$("#hdn_fso_id").val();
		// alert(prev_fso_id);
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			$('#txt_bar_code_num').val();
			return;
		}
		var row_num= $('#scanning_tbl tbody tr').length; //$('#txt_tot_row').val();
		var barcode_nos=trim(barcode_no);
		var num_row=$('#scanning_tbl tbody tr').length;


		var msg=0;
		var barcode_da = barcode_nos.split(",");
		 $("#scanning_tbl").find('tbody tr').each(function() 
		 {
            for (var k = 0; k < barcode_da.length; k++) 
            {
                var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
                if(trim(barcodeNo) == barcode_da[k])
                {
                    msg++;
                    return;
                }
            }
        });
        if(msg>0){
            alert("Barcode already scanned");
            $('#txt_bar_code_num').val('');
            return;
        }


		var barcode_data=trim(return_global_ajax_value(barcode_nos, 'populate_barcode_data', '', 'requires/finish_feb_roll_delivery_to_garments_controller'));

		var barcode_res=trim(barcode_data).split('!!');

		if(barcode_res[0]==0)
		{
			alert('Barcode is Not Valid');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() 
			{
				$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}
		if(barcode_res[0]==99)
		{
			alert("Sorry! Barcode Already Scanned. Challan No: "+ barcode_res[1] + " Barcode No "+ barcode_nos);
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function()
			{
				$('#messagebox_main', window.parent.document).html('Barcode is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}

		var barcode_datas=barcode_data.split("#");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var data=barcode_datas[k].split("**");

			var bar_code=data[0];
			var roll_no=data[1];
			var batch_id=data[2];
			var batch_no=data[3];
			var body_part_id=data[4];
			var body_part=data[5];
			var fabric_description_id=data[6];
			var construction=data[7]; 
			var composition=data[8];
			var color_id=data[9];
			var color_no=data[10];
			var gsm=data[11];
			var width=data[12];
			var roll_qnty=data[13];
			var reject_qnty=data[14];
			var used_qnty = reject_qnty*1 + roll_qnty*1 + data[40]*1;
			// used_qnty = reject_qnty + roll_qnty + processLoss_qty
			
			var floor=data[15];
			var room=data[16];
			var rack_no=data[17];
			var shelf_no=data[18];

			if (jsfloor_room_rack_shelf_name_array.length!=0 || jsfloor_room_rack_shelf_name_array!='undefiend') 
			{
				var floor_name=jsfloor_room_rack_shelf_name_array[floor];
				var room_name=jsfloor_room_rack_shelf_name_array[room];
				var rack_name=jsfloor_room_rack_shelf_name_array[rack_no];
				var shelf_name=jsfloor_room_rack_shelf_name_array[shelf_no];
			}
			else
			{
				floor_name='';
				room_name='';
				rack_name='';
				shelf_name='';
			}

			var dia_width_type_id=data[19];
			var year=data[20];
			var po_job_no=data[21];
			var buyer_id=data[22];
			var sales_order_no=data[23];
			var fso_id=data[24];
			var prod_id=data[25];
			var recv_number=data[26];
			var sales_booking_no=data[27];
			var booking_id=data[28];
			var company_id=data[29];
			var roll_id=data[30];
			var reprocess=data[31];
			var prev_reprocess=data[32];
			var booking_without_order=data[33];
			var sales_booking_no=data[34];
			var booking_id=data[35];
			var dia_width_type_no=data[36];
			var withinGroup=data[37];
			var po_company_id=data[38];
			var buyer_name=data[39];
			var processLoss=data[40];
			var location_id=data[41];
			var store_id=data[42];

			if( jQuery.inArray( bar_code, scanned_barcode )>-1)
			{
				alert('Sorry! Barcode Already Scanned.');
				$('#txt_bar_code_num').val('');
				return;
			}

			if(cbo_company_id!=company_id)
			{
				alert("Multiple Company Not Allowed");
				return;
			}

			if(store_id!=$("#cbo_store_name").val() && $("#cbo_store_name").val() !=0)
			{
				alert("Multiple Store Not Allowed");
				return;
			}
			if(location_id!=$("#cbo_location").val() && $("#cbo_location").val() !=0)
			{
				alert("Multiple Location Not Allowed");
				return;
			}

			if(prev_fso_id !="" && (prev_fso_id != fso_id))
			{
				alert("Sales Order Mixed is Not Allowed");
				$('#txt_bar_code_num').val('');
				return;
			}

			var bar_code_no=$('#barcodeNo_'+row_num).val();
			if(bar_code_no!="")
			{
				row_num++;
				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
						'value': function(_, value) { return value }
					});
				}).end().prependTo("#scanning_tbl");

				$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);
			}

			$("#sl_"+row_num).html(row_num+'&nbsp;&nbsp;'+"<input type='checkbox' id='checkRow_"+ row_num +"' name='checkRow[]' ></td>");

			$("#barcode_"+row_num).text(bar_code);
			$("#rollNo_"+row_num).text(roll_no);
			$("#batchNo_"+row_num).text(batch_no);
			$("#bodyPart_"+row_num).text(body_part);
			$("#cons_"+row_num).text(construction);
			$("#comps_"+row_num).text(composition);
			$("#color_"+row_num).text(color_no);
			$("#gsm_"+row_num).text(gsm);
			$("#dia_"+row_num).text(width);
			$("#currentQty_"+row_num).val(roll_qnty);
			$("#rejectQty_"+row_num).text(reject_qnty);
			$("#processLoss_"+row_num).text(processLoss);
			$("#usedQty_"+row_num).text(used_qnty);

			$("#floorName_"+row_num).text(floor_name);
			$("#roomName_"+row_num).text(room_name);
			$("#rackName_"+row_num).text(rack_name);
			$("#selfName_"+row_num).text(shelf_name);

			$("#floorId_"+row_num).val(floor);
			$("#roomId_"+row_num).val(room);
			$("#rackId_"+row_num).val(rack_no);
			$("#selfId_"+row_num).val(shelf_no);

			$("#wideType_"+row_num).text(dia_width_type_no);
			$("#year_"+row_num).text(year);
			$("#job_"+row_num).text(po_job_no);
			$("#buyer_"+row_num).text(buyer_name);
			$("#order_"+row_num).text(sales_order_no);
			$("#txt_fso_no").val(sales_order_no);
			$("#hdn_fso_id").val(fso_id);
			$("#prodId_"+row_num).text(prod_id);
			//$("#systemId_"+row_num).text(recv_number);

			$("#barcodeNo_"+row_num).val(bar_code);

			$("#deterId_"+row_num).val(fabric_description_id);
			$("#productId_"+row_num).val(prod_id);
			$("#orderId_"+row_num).val(fso_id);
			$("#rollId_"+row_num).val(roll_id);
			$("#rollQty_"+row_num).val(roll_qnty);
			$("#batchID_"+row_num).val(batch_id);
			$("#bodyPartId_"+row_num).val(body_part_id);
			$("#colorId_"+row_num).val(color_id);
			$("#wideTypeId_"+row_num).val(dia_width_type_id);
			$("#JobNumber_"+row_num).val(po_job_no);
			$("#buyerId_"+row_num).val(buyer_id);
			$("#reProcess_"+row_num).val(reprocess);
			$("#prereProcess_"+row_num).val(prev_reprocess);
			$("#IsSalesId_"+row_num).val(1);
			$("#bookingWithoutOrder_"+row_num).val(booking_without_order);
			$("#hdn_booking_id").val(booking_id);
			$("#rejectQnty_"+row_num).val(reject_qnty);
			$("#usedQnty_"+row_num).val(used_qnty);
			$("#txt_booking_no").val(sales_booking_no);
                     
			$("#dtlsId_"+row_num).val('');
			$("#transId_"+row_num).val('');
			$("#rollTableId_"+row_num).val('');


			$('#txt_tot_row').val(row_num);
			scanned_barcode.push(bar_code);
			$('#txt_bar_code_num').val('');

			$('#txt_bar_code_num').focus();

			if($("#cbo_location").val()==0)
			{
				$("#cbo_location").val(location_id);
				load_drop_down('requires/finish_feb_roll_delivery_to_garments_controller', location_id+'_'+company_id, 'load_drop_down_store','store_td');
			}
			if($("#cbo_store_name").val()==0)
			{
				$("#cbo_store_name").val(store_id);
			}

			$("#cbo_location").attr('disabled','disabled');
			$("#cbo_store_name").attr('disabled','disabled');

		}

		if(withinGroup==1)
		{
			load_drop_down('requires/finish_feb_roll_delivery_to_garments_controller', po_company_id+"_"+withinGroup, 'load_drop_down_company', 'party_td' );				
		}
		else
		{
			load_drop_down('requires/finish_feb_roll_delivery_to_garments_controller', buyer_id+"_"+withinGroup, 'load_drop_down_buyer', 'party_td' );
			$('#cbo_party').val(buyer_id);
		}


		$('#cbo_party').attr('disabled','disabled');
		//$("#cbo_location").attr('disabled','disabled');
		//$("#cbo_store_name").attr('disabled','disabled');

		calculate_total();
	}

	function calculate_total()
	{
		var total_roll_weight='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="currentQty[]"]').val();
			total_roll_weight=total_roll_weight*1+rollWgt*1;
		});
		$("#rollQntyTotal").text(total_roll_weight.toFixed(2));
	}


	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();

		var html='<tr id="tr_1" align="center" valign="middle"><td width="40" id="sl_1">1 &nbsp;&nbsp;<input type="checkbox" id="checkRow_1" name="checkRow[]" ></td><td width="80" id="barcode_1"></td><td width="45" id="rollNo_1"></td><td width="60" id="batchNo_1"></td><td width="80" id="bodyPart_1" align="left"></td><td width="80" id="cons_1" align="left"></td><td width="80" id="comps_1" align="left"></td><td width="70" id="color_1"></td><td width="40" id="gsm_1"></td><td width="40" id="dia_1" ></td><td width="50" id="rollWgt_1"><input type="text" id="currentQty_1" class="text_boxes_numeric"  style="width:35px" name="currentQty[]" onChange="fnc_rollQntyChange()" disabled /></td><td width="50" id="rejectQty_1"></td><td width="50" id="processLoss_1"></td><td width="50" id="usedQty_1"></td><td width="50" id="floorName_1"></td><td width="50" id="roomName_1"></td><td width="50" id="rackName_1"></td><td width="50" id="selfName_1"></td><td width="60" id="wideType_1"></td><td width="45" id="year_1" align="center"></td><td width="90" id="job_1"></td><td width="65" id="buyer_1"></td><td width="80" id="order_1" align="left"></td><td width="100" id="prodId_1"></td><input type="hidden" name="barcodeNo[]" id="barcodeNo_1" value=""/><input type="hidden" name="productionId[]" id="productionId_1" value=""/><input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1" value=""/><input type="hidden" name="deterId[]" id="deterId_1" value=""/><input type="hidden" name="productId[]" id="productId_1" value=""/><input type="hidden" name="orderId[]" id="orderId_1" value=""/><input type="hidden" name="rollId[]" id="rollId_1" value=""/><input type="hidden" name="rollQty[]" id="rollQty_1"  value="" /><input type="hidden" name="batchID[]" id="batchID_1"  value="" /><input type="hidden" name="bodyPartId[]" id="bodyPartId_1" value=""/><input type="hidden" name="colorId[]" id="colorId_1" value=""/><input type="hidden" name="wideTypeId[]" id="wideTypeId_1" /><input type="hidden" name="JobNumber[]" id="JobNumber_1"  /><input type="hidden" name="buyerId[]" id="buyerId_1" /><input type="hidden" name="reProcess[]" id="reProcess_1"/><input type="hidden" name="prereProcess[]" id="prereProcess_1"/><input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/><input type="hidden" name="rejectQnty[]" id="rejectQnty_1"/><input type="hidden" name="usedQnty[]" id="usedQnty_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId"  /><input type="hidden" name="rollTableId[]" id="rollTableId_1" /><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1" /><input type="hidden" name="floorId[]" id="floorId_1"/><input type="hidden" name="roomId[]" id="roomId_1"/><input type="hidden" name="rackId[]" id="rackId_1"/><input type="hidden" name="selfId[]" id="selfId_1"/></tr>';

		$('#cbo_company_id').val(0);

		$('#store_td').html('<? echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );?>');
		$('#location_td').html('<? echo create_drop_down("cbo_location", 152, $blank_array,"", 1,"-- Select Location --", 0,"");?>');

		$('#cbo_company_id').attr('disabled',false);


		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_system_id').val('');
		$('#txt_delivery_date').val('');
		$('#txt_fso_no').val('');
		$('#hdn_fso_id').val('');
		//$('#txt_deleted_id').val('');

		$('#rollQntyTotal').text('');
		$("#scanning_tbl tbody").html(html);
		scanned_barcode=[];
	}

	function fnc_reset_dtls()
	{
		$('#scanning_tbl tbody tr').remove();

		var html='<tr id="tr_1" align="center" valign="middle"><td width="40" id="sl_1">1 &nbsp;&nbsp;<input type="checkbox" id="checkRow_1" name="checkRow[]" ></td><td width="80" id="barcode_1"></td><td width="45" id="rollNo_1"></td><td width="60" id="batchNo_1"></td><td width="80" id="bodyPart_1" align="left"></td><td width="80" id="cons_1" align="left"></td><td width="80" id="comps_1" align="left"></td><td width="70" id="color_1"></td><td width="40" id="gsm_1"></td><td width="40" id="dia_1" ></td><td width="50" id="rollWgt_1"><input type="text" id="currentQty_1" class="text_boxes_numeric"  style="width:35px" name="currentQty[]" onChange="fnc_rollQntyChange()" disabled /></td><td width="50" id="rejectQty_1"></td><td width="50" id="processLoss_1"></td><td width="50" id="usedQty_1"></td><td width="50" id="floorName_1"></td><td width="50" id="roomName_1"></td><td width="50" id="rackName_1"></td><td width="50" id="selfName_1"></td><td width="60" id="wideType_1"></td><td width="45" id="year_1" align="center"></td><td width="90" id="job_1"></td><td width="65" id="buyer_1"></td><td width="80" id="order_1" align="left"></td><td width="100" id="prodId_1"></td><input type="hidden" name="barcodeNo[]" id="barcodeNo_1" value=""/><input type="hidden" name="productionId[]" id="productionId_1" value=""/><input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1" value=""/><input type="hidden" name="deterId[]" id="deterId_1" value=""/><input type="hidden" name="productId[]" id="productId_1" value=""/><input type="hidden" name="orderId[]" id="orderId_1" value=""/><input type="hidden" name="rollId[]" id="rollId_1" value=""/><input type="hidden" name="rollQty[]" id="rollQty_1"  value="" /><input type="hidden" name="batchID[]" id="batchID_1"  value="" /><input type="hidden" name="bodyPartId[]" id="bodyPartId_1" value=""/><input type="hidden" name="colorId[]" id="colorId_1" value=""/><input type="hidden" name="wideTypeId[]" id="wideTypeId_1" /><input type="hidden" name="JobNumber[]" id="JobNumber_1"  /><input type="hidden" name="buyerId[]" id="buyerId_1" /><input type="hidden" name="reProcess[]" id="reProcess_1"/><input type="hidden" name="prereProcess[]" id="prereProcess_1"/><input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/><input type="hidden" name="rejectQnty[]" id="rejectQnty_1"/><input type="hidden" name="usedQnty[]" id="usedQnty_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId"  /><input type="hidden" name="rollTableId[]" id="rollTableId_1" /><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1" /><input type="hidden" name="floorId[]" id="floorId_1"/><input type="hidden" name="roomId[]" id="roomId_1"/><input type="hidden" name="rackId[]" id="rackId_1"/><input type="hidden" name="selfId[]" id="selfId_1"/></tr>';


		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_system_id').val('');
		$('#txt_delivery_date').val('');
		$('#txt_fso_no').val('');
		$('#hdn_fso_id').val('');
		$('#txt_booking_no').val('');
		$('#hdn_booking_id').val('');
		$('#cbo_party').val(0);
		

		//$('#txt_deleted_id').val('');

		$('#rollQntyTotal').text('');
		$("#scanning_tbl tbody").html(html);
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
	    xmlhttp.open("POST", "requires/finish_feb_roll_delivery_to_garments_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);
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
<body onLoad="set_hotkey()">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:690px;" align="center">   
				<fieldset style="width:690px;">
					<legend>Finish Fabric Delivery Entry</legend>
					<fieldset>
						<table cellpadding="0" cellspacing="2" width="680" border="0">
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
								<td class="must_entry_caption">Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0,"load_drop_down('requires/finish_feb_roll_delivery_to_garments_controller', this.value, 'load_drop_down_location','location_td');company_on_change(this.value);fnc_reset_dtls();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/finish_feb_roll_delivery_to_garments_controller' )" );
									?>
									<input type="hidden" name="store_update_upto" id="store_update_upto">
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
									<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:130px;" placeholder="FSO No"  readonly disabled />
									<input type="hidden" name="hdn_fso_id" id="hdn_fso_id" class="text_boxes" value="" />
									<input type="hidden" name="hdn_within_group" id="hdn_within_group" class="text_boxes" value="" />							
									<input type="hidden" name="hdn_buyer_id" id="hdn_buyer_id" class="text_boxes" value="" />
									<input type="hidden" name="txt_po_job" id="txt_po_job" class="text_boxes" readonly />
								</td>
								<td>Party</td>
								<td id="party_td">
									<? echo create_drop_down("cbo_party", 152, $blank_array,"", 1,"-- Select Party --", 0,""); ?>
								</td>
								<td class="must_entry_caption">Sales/Booking</td>
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
								<td>Roll No</td>
								<td>
									<input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
								</td>
								<td>No. of Coure Tube</td>
								<td>
									<input type="text" name="txt_coure_tube" id="txt_coure_tube" class="text_boxes_numeric" style="width:140px;" placeholder="Write"/>
								</td>
							</tr>
							<tr>
								<td>Deli to Addr.</td>
								<td>
									<input type="text" name="txt_delivery_addr" id="txt_delivery_addr" class="text_boxes" style="width:130px;" placeholder="Write"/>
								</td>
								<td>Remarks</td>
								<td colspan="3">
									<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:395px;" placeholder="Write"/>
								</td>
							</tr>
							<tr>
								<td>Challan No.</td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:130px;" value=''>
								</td>
								<td colspan="4"></td>
							</tr>
							
						</table>
					</fieldset>
				</fieldset>
			</div>
			<br clear="all" />
		</form>
	</div>
	<fieldset style="width:1510px;text-align:left">
		<style>
            #scanning_tbl tr td
            {
                background-color:#FFF;
                color:#000;
                border: 1px solid #666666;
                line-height:12px;
                height:20px;
                overflow:auto;
                word-break:break-all;
            }
        </style>
		<table cellpadding="0" width="1480" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
            <thead>
            	<th width="40">Check All <br><input type="checkbox" id="txt_check_all" name="txt_check_all" onClick="check_all(this.value)" /></th>
                <th width="80">Barcode No</th>
                <th width="45">Roll No</th>
                <th width="60">Batch No</th>
                <th width="80">Body Part</th>
                <th width="80">Construction</th>
                <th width="80">Composition</th>
                <th width="70">Color</th>
                <th width="40">GSM</th>
                <th width="40">Dia</th>
                <th width="50">Roll Qty.</th>
                <th width="50">Reject Qty.</th>
                <th width="50">Process Loss</th>
                <th width="50">Finish Wgt.</th>
                <th width="50">Floor</th>
                <th width="50">Room</th>
                <th width="50">Rack</th>
                <th width="50">Shelf</th>
                <th width="60">Dia/ Width Type</th>
                <th width="45">Year</th>
                <th width="90">Job No</th>
                <th width="65">Buyer</th>
                <th width="80">Order No</th>
                <th width="100">Product Id</th>
            </thead>
        </table>
        <div style="width:1510px; max-height:250px; overflow-y:scroll" align="left">
         	<table cellpadding="0" cellspacing="0" width="1480" border="1" id="scanning_tbl" rules="all" class="rpt_table">
            	<tbody id="list_view_container">
                	<tr id="tr_1" align="center" valign="middle">
                        <td width="40" id="sl_1">1 &nbsp;&nbsp;
                       		<input type="checkbox" id="checkRow_1" name="checkRow[]" ></td>
                        <td width="80" id="barcode_1"></td>
                        <td width="45" id="rollNo_1"></td>
                        <td width="60" id="batchNo_1"></td>
                        <td width="80" id="bodyPart_1" align="left"></td>
                        <td width="80" id="cons_1" align="left"></td>
                        <td width="80" id="comps_1" align="left"></td>
                        <td width="70" id="color_1"></td>
                        <td width="40" id="gsm_1"></td>
                        <td width="40" id="dia_1" ></td>
                        <td width="50" id="rollWgt_1">
                        <input type="text" id="currentQty_1" class="text_boxes_numeric"  style="width:35px" name="currentQty[]" onChange="fnc_rollQntyChange()" disabled /></td>
                        <td width="50" id="rejectQty_1"></td>
                        <td width="50" id="processLoss_1"></td>
                        <td width="50" id="usedQty_1"></td>

                        

                        <td width="50" id="floorName_1"></td>
						<td width="50" id="roomName_1"></td>
						<td width="50" id="rackName_1"></td>
						<td width="50" id="selfName_1"></td>

                        <td width="60" id="wideType_1"></td>
                        <td width="45" id="year_1" align="center"></td>
                        <td width="90" id="job_1"></td>
                        <td width="65" id="buyer_1"></td>
                        <td width="80" id="order_1" align="left"></td>
                        <td width="100" id="prodId_1"></td>
                        <input type="hidden" name="barcodeNo[]" id="barcodeNo_1" value=""/>
                            <input type="hidden" name="productionId[]" id="productionId_1" value=""/>
                            <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1" value=""/>
                            <input type="hidden" name="deterId[]" id="deterId_1" value=""/>
                            <input type="hidden" name="productId[]" id="productId_1" value=""/>
                            <input type="hidden" name="orderId[]" id="orderId_1" value=""/>
                            <input type="hidden" name="rollId[]" id="rollId_1" value=""/>
                            <input type="hidden" name="rollQty[]" id="rollQty_1"  value="" />
                            <input type="hidden" name="batchID[]" id="batchID_1"  value="" />
                            <input type="hidden" name="bodyPartId[]" id="bodyPartId_1" value=""/> 
                            <input type="hidden" name="colorId[]" id="colorId_1" value=""/> 
                            
                            <input type="hidden" name="wideTypeId[]" id="wideTypeId_1" /> 
                            <input type="hidden" name="JobNumber[]" id="JobNumber_1"  /> 
                            <input type="hidden" name="buyerId[]" id="buyerId_1" /> 
                            <input type="hidden" name="reProcess[]" id="reProcess_1"/>
                            <input type="hidden" name="prereProcess[]" id="prereProcess_1"/>
                            <input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/>
                            <input type="hidden" name="rejectQnty[]" id="rejectQnty_1"/>
                            <input type="hidden" name="usedQnty[]" id="usedQnty_1"/>

                            <input type="hidden" name="dtlsId[]" id="dtlsId_1"  /> 
                            <input type="hidden" name="transId[]" id="transId_1"  /> 
                            <input type="hidden" name="rollTableId[]" id="rollTableId_1"  /> 
                            <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"  /> 

                            <input type="hidden" name="floorId[]" id="floorId_1"/>
							<input type="hidden" name="roomId[]" id="roomId_1"/>
							<input type="hidden" name="rackId[]" id="rackId_1"/>
							<input type="hidden" name="selfId[]" id="selfId_1"/>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10">Total</th>
                        <th id="rollQntyTotal"></th>
                        <th id="rejectQntyTotal_id"></th>
                        <th id="processLossTotal_id"></th>
                        <th id="usedQntyTotal_id"></th>
                        <th colspan="10"></th>
                    </tr>
                </tfoot>
        	</table>
    	</div>
		<table cellpadding="0" cellspacing="1" width="100%" border="0">
			<tr>
				<td align="center" colspan="4" class="button_container">
					<?
					echo load_submit_buttons($permission, "fnc_finish_delivery_entry", 0,1,"fnc_reset_form()",1);
					?>
					<input type="button" name="print2" id="print2" class="formbutton" value="Print 2" style=" width:100px" onClick="fnc_finish_delivery_entry(5);">
					<input type="button" name="print3" id="print3" class="formbutton" value="Print 3" style=" width:100px" onClick="fnc_finish_delivery_entry(6);">
					<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
					<input type="hidden" id="update_mst_id" name="update_mst_id" value="" />
					<input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" />
					<input type="hidden" id="hidden_pre_product_id" name="hidden_pre_product_id" value="" />
					<input name="update_dtls_id" id="update_dtls_id" readonly type="hidden">
					<input name="update_trans_id" id="update_trans_id" readonly type="hidden">
				</td>
			</tr>
		</table>
		<div style="width:620px;" id="list_container_finishing"></div>
	</fieldset>  
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$("#cbo_location").val(0);
</script>
</html>
