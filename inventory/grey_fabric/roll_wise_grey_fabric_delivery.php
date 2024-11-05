<?
/*-------------------------------------------- Comments
Purpose			: This form will create Grey Fabric Issue Roll Wise

Functionality	:
JS Functions	:
Created by		: Fuad
Creation date 	: 18-02-2015
Updated by 		: Zaman
Update date		: 10.12.2019
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
	echo load_html_head_contents("Grey Fabric Issue Roll Wise","../../", 1, 1, $unicode,'','');

	//var_dump($_SESSION['logic_erp']['data_arr'][61]);
	
	?>
	<script>

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
		var permission='<? echo $permission; ?>';
		<?
			$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][61] );
			echo "var field_level_data= ". $data_arr . ";\n";
		?>
		var scanned_barcode=new Array();
		<?
		$scanned_barcode_array=array();

		$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
		$machine_array=return_library_array( "select id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
		$location_array=return_library_array( "select id, location_name from lib_location", "id", "location_name");

		$floor_room_rack_array=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
		
		if(empty($floor_room_rack_array))
		{
			$floor_room_rack_array = array();
		}

		$jsbarcode_buyer_name_array= json_encode($buyer_name_array);
		echo "var jsbuyer_name_array = ". $jsbarcode_buyer_name_array . ";\n";

		$jsbarcode_location_array= json_encode($location_array);
		echo "var jslocation_name_array = ". $jsbarcode_location_array . ";\n";

		$jsbarcode_machine_array= json_encode($machine_array);
		echo "var jsmachine_array = ". $jsbarcode_machine_array . ";\n";

		$jsbarcode_floor_array= json_encode($floor_room_rack_array);
		echo "var jsfloor_room_rack_shelf_name_array = ". $jsbarcode_floor_array . ";\n";


		$floor_arr=array(); $room_arr=array(); $rack_arr=array(); $shelf_arr=array();
		
		$composition_arr=array(); $constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		foreach( $data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}

		$jsconstructtion_arr=json_encode($constructtion_arr);
		echo "var constructtion_arr = ". $jsconstructtion_arr . ";\n";

		$jscomposition_arr=json_encode($composition_arr);
		echo "var composition_arr = ". $jscomposition_arr . ";\n";

	/*$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=61 and is_returned!=1 and status_active=1 and is_deleted=0");
	foreach($scanned_barcode_data as $row)
	{
		$scanned_barcode_array[]=$row[csf('barcode_no')];
	}
	$jsscanned_barcode_array= json_encode($scanned_barcode_array);
	echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";*/
	?>
	function load_scanned_barcode(issue_id)
	{
		scanned_barcode=new Array();
		var scanned_barcode_nos=trim(return_global_ajax_value( issue_id, 'load_scanned_barcode_nos', '', 'requires/roll_wise_grey_fabric_delivery_controller'));
		scanned_barcode=eval(scanned_barcode_nos);

		set_button_status(0, permission, 'fnc_grey_fabric_issue_roll_wise',1);
	}

	function openmypage_issue()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_grey_fabric_delivery_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Issue Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			var posted_account=this.contentDoc.getElementById("hidden_posted_account").value;	 //Posted In account

			var within_group 	= this.contentDoc.getElementById("hidden_within_group").value;
			var buyer_id 		= this.contentDoc.getElementById("hidden_buery_id").value;
			var po_company_id 	= this.contentDoc.getElementById("hidden_po_company_id").value;

			if(issue_id!="")
			{
				load_scanned_barcode( issue_id );
				get_php_form_data(issue_id, "populate_data_from_data", "requires/roll_wise_grey_fabric_delivery_controller");

				load_drop_down('requires/roll_wise_grey_fabric_delivery_controller', po_company_id+"_"+within_group, 'load_drop_down_company', 'party_td' );
				$('#cbo_party').val(po_company_id).attr('disabled','disabled');

				var html=return_global_ajax_value(issue_id, 'populate_barcode_data_update', '', 'requires/roll_wise_grey_fabric_delivery_controller');
				if(trim(html)!="")
				{
					$("#scanning_tbl tbody").html(html);

					/*var tableFilters =
					{
						col_19: "none"
					}

					setFilterGrid("scanning_tbl",-1,tableFilters);*/

					calculate_total();
				}

				/*if(trim(barcode_nos)!="")
				{
					var barcode_upd=barcode_nos.split(",");
					for(var k=0; k<barcode_upd.length; k++)
					{
						create_row(1,barcode_upd[k],0);
					}
				}*/


				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
				$("#btn_mc").addClass('formbutton');

				$("#is_posted_accout").val(posted_account);
				if(posted_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
				else 				  document.getElementById("accounting_posted_status").innerHTML="";

			}
		}
	}

	function generate_report_file(data,action)
	{
		window.open("requires/roll_wise_grey_fabric_delivery_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_fabric_issue_roll_wise( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_print');
			return;
		}

		if($("#is_posted_accout").val()==1)
		{
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}

		if(form_validation('cbo_company_id*cbo_location*cbo_store_name*txt_issue_date*txt_fso_no','Company*Location*Store*Issue Date*FSO')==false)
		{
			return;
		}
		
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Today");
			return;
		}

		var j=0;
		var dataString='';
		var all_barcodes='';
		var new_barcode_no = "";
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var isReturned=$(this).find('input[name="isReturned[]"]').val();
			if(isReturned != 1)
			{
				//N.B. Here Returned Barcodes will not pass in the Update Event
				
				var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
				var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
				var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
				var productId=$(this).find('input[name="productId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var rollId=$(this).find('input[name="rollId[]"]').val();
				var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
				var rollNo=$(this).find("td:eq(2)").text();

				var yarnLot= encodeURIComponent($(this).find('input[name="yarnLot[]"]').val());
				var stichLn= encodeURIComponent($(this).find('input[name="stichLn[]"]').val());

				var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var locationId=$(this).find('input[name="locationId[]"]').val();
				var machineId=$(this).find('input[name="machineId[]"]').val();
				var brandId=$(this).find('input[name="brandId[]"]').val();

				var floorId=0;
				var roomId=0;
				var rackId=0;
				var shelfId=0;
				var binId=0;

				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				var transId=$(this).find('input[name="transId[]"]').val();
				var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
				var rollRate=$(this).find('input[name="rollRate[]"]').val();
				var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
				var smnBooking=$(this).find('input[name="smnBooking[]"]').val();
				var isSalesOrder=$(this).find('input[name="isSalesOrder[]"]').val();
				var storeId=$(this).find('input[name="storeId[]"]').val();
				var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
				var orderNo=$(this).find("td:eq(18)").text();
				

				var yarnRate=$(this).find('input[name="yarnRate[]"]').val();
				var knittingCharge=$(this).find('input[name="knittingCharge[]"]').val();

				j++;
				dataString+='&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&yarnLot_' + j + '=' + yarnLot + '&yarnCount_' + j + '=' + yarnCount + '&colorId_' + j + '=' + colorId + '&stichLn_' + j + '=' + stichLn + '&brandId_' + j + '=' + brandId + '&floorId_' + j + '=' + floorId + '&roomId_' + j + '=' + roomId + '&rack_' + j + '=' + rackId + '&shelf_' + j + '=' + shelfId + '&bin_' + j + '=' + binId + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo + '&locationId_' + j + '=' + locationId + '&machineId_' + j + '=' + machineId+ '&rollRate_' + j + '=' + rollRate+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder+ '&smnBooking_' + j + '=' + smnBooking+'&isSalesOrder_' + j + '=' + isSalesOrder + '&orderNo_' + j + '=' + orderNo + '&storeId_' +j + '=' + storeId + '&hiddenQtyInPcs_' +j + '=' + hiddenQtyInPcs + '&bodyPartId_' +j + '=' + bodyPartId + '&yarnRate_' +j + '=' + yarnRate + '&knittingCharge_' +j + '=' + knittingCharge;
				all_barcodes+=+ barcodeNo+',';

				if(rolltableId>0)
				{

				}
				else
				{
					new_barcode_no +=+ barcodeNo+',';
				}
			}
		});

		if(j<1)
		{
			alert('No data');
			return;
		}

		if(operation==2)
		{
			var response=trim(return_global_ajax_value($("#update_id").val()+"_"+all_barcodes, 'check_barcode_for_delete', '', 'requires/roll_wise_grey_fabric_delivery_controller'));
			response=response.split("_");
			//alert(response[0])
			if(response[0]==1) {alert( response[1]+" in Posted in accounts.Delete Restricted"); return;}
			if(response[0]==2) {alert( response[1]+" are used in Grey Roll Receive By Batch.Delete Restricted"); return;}
			show_msg('13');
			return;
		}

		//alert(dataString);return;
		//cbo_dyeing_source
		//cbo_dyeing_comp
		// txt_batch_id
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*cbo_company_id*cbo_location*cbo_store_name*txt_issue_date*txt_fso_no*hdn_fso_id*cbo_party*update_id*txt_deleted_id*txt_remarks*txt_booking_no*hdn_booking_id',"../../")+dataString+'&new_barcode_nos='+new_barcode_no;
		// alert(data);return;
		freeze_window(operation);

		http.open("POST","requires/roll_wise_grey_fabric_delivery_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_grey_fabric_issue_roll_wise_Reply_info()
	{
		if(http.readyState == 4)
		{
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
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_issue_no').value = response[2];
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_location').attr('disabled',true);
				$('#cbo_store_name').attr('disabled',true);
				$('#txt_deleted_id').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);

				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
			}
			release_freezing();
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

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_grey_fabric_delivery_controller.php?company_id='+company_id + '&store_id=' + cbo_store_name + '&cbo_location='+ cbo_location + '&fabric_sales_order_id='+ fabric_sales_order_id +'&action=barcode_popup','Barcode Popup', 'width=1360px,height=380px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

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

	//var scanned_barcode=new Array();
	function create_row(barcode_no)
	{
		var prev_fso_id=$("#hdn_fso_id").val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_location=$('#cbo_location').val();
		var cbo_store_name=$('#cbo_store_name').val();
		if (form_validation('cbo_company_id*cbo_location*cbo_store_name','Company*Location*Store Name')==false)
		{
			$('#txt_bar_code_num').val();
			return;
		}

		var row_num=$('#txt_tot_row').val();
		var barcode_nos=trim(barcode_no);
		var num_row=$('#scanning_tbl tbody tr').length;
		var barcode_data=trim(return_global_ajax_value(barcode_nos+"_"+cbo_location+"_"+cbo_store_name, 'populate_barcode_data', '', 'requires/roll_wise_grey_fabric_delivery_controller'));
		//alert(barcode_data);return;
		var barcode_res=trim(barcode_data).split('!!');

		if(barcode_res[0]==999)
		{
			alert(barcode_res[1]);
			$('#txt_bar_code_num').val('');
			return;
		}
		if(barcode_res[0]==0)
		{
			alert('Barcode is Not Valid');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}
		if(barcode_res[0]==99)
		{
			//alert('Barcode is already scanned.');
			alert("Sorry! Barcode Already Scanned. Challan No: "+ barcode_res[1] + " Barcode No "+ barcode_nos);
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
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
			var company_id=data[1];
			var roll_no=data[2];
			var roll_id=data[3];
			var location_id=data[4];
			var location_name=jslocation_name_array[location_id];
			var machine_no_id=data[5];
			if(machine_no_id*1>0) var machine_name=jsmachine_array[machine_no_id]; else var machine_name='';
			var body_part=data[6];
			var bwo=data[7];
			var receive_basis=data[8];
			var receive_basis_id=data[9];
			var booking_no=data[10];
			var booking_id=data[11];
			var color=data[12];
			var color_id=data[13];
			var knitting_source_id=data[14];
			var knitting_source=data[15];
			var knitting_company_id=data[16];
			var knit_company=data[17];
			var yarn_lot=data[18];
			var yarn_count=data[19];
			var stitch_length=data[20];
			var brand_id=data[21];
			var rack=data[22];			
			var shelf=data[23];
			var prod_id=data[24];
			var deter_id=data[25];
			var cons_comp=constructtion_arr[deter_id]+", "+composition_arr[deter_id];
			var gsm=data[26];
			var width=data[27];
			var qnty=data[28];
			var rate=data[29];
			var booking_without_order=data[30];
			var yarn_count_show=data[31];
			var floor_id=data[32];			
			var room_id=data[33];
			var bin_box_id=data[34];		
			
			var po_id=data[35];
			var buyer_id=data[36];
			var buyer_name=jsbuyer_name_array[buyer_id];
			var po_no=data[37];
			var job_no=data[38];
			var is_sales=data[39];
			var bookingNumber=data[40];
			var store_id=data[41];
			var internal_ref_no=data[42];
			var qtyInPcs=data[43]*1;
			var collarCuffSize=data[44];
			var body_part_id_latest=data[45];
			var body_part_no_latest=data[46];

			var yarn_rate=data[47];
			var knittingCharge=data[48];
			var roll_rate=data[49];
			var withinGroup=data[50];
			var po_company_id=data[51];
			var fso_booking_id=data[52];

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

			if(prev_fso_id !="" && (prev_fso_id != po_id))
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

			var breakd = "<br/>";
			$("#sl_"+row_num).text(row_num);
			$("#barcode_"+row_num).text(bar_code);
			$("#roll_"+row_num).text(roll_no);
			$("#location_"+row_num).text(location_name);
			//$("#prodId_"+row_num).text(prod_id);
			//$("#bodyPart_"+row_num).text(body_part);
			$("#bodyPart_"+row_num).text(body_part_no_latest);
			$("#bodyPartId_"+row_num).val(body_part_id_latest);
			$("#cons_"+row_num).text(cons_comp);
			$("#gsm_"+row_num).text(gsm);
			$("#dia_"+row_num).text(width);
			$("#stL_"+row_num).text(stitch_length);
			$("#color_"+row_num).text(color);
			$("#lot_"+row_num).text(yarn_lot);
			$("#count_"+row_num).text(yarn_count_show);
			//$("#machine_"+row_num).text(machine_name);
			$("#rollWeight_"+row_num).text(qnty);
			$("#qtyInPcs_"+row_num).text(qtyInPcs);
			$("#collarCuffSize_"+row_num).text(collarCuffSize);
			$("#order_"+row_num).text(po_no);
			$("#txt_fso_no").val(po_no);
			$("#hdn_fso_id").val(po_id);
			$("#txt_booking_no").val(bookingNumber);
			$("#hdn_booking_id").val(fso_booking_id);
			$("#buyer_"+row_num).text(buyer_name);
			$("#bookingNo_"+row_num).html(bookingNumber+"<br>------<br>"+bwo+"<p>");
			//$("#internalRefNo_"+row_num).html(internal_ref_no);
			$("#job_"+row_num).text(job_no);
			$("#orderId_"+row_num).val(po_id);
			//$("#progBookPiNo_"+row_num).text('');
			$("#knitCompany_"+row_num).text(knit_company);
			$("#basis_"+row_num).text(receive_basis);
			//$("#progBookPiNo_"+row_num).text(booking_no);
			$("#barcodeNo_"+row_num).val(bar_code);
			$("#recvBasis_"+row_num).val(receive_basis_id);
			$("#progBookPiId_"+row_num).val(booking_id);
			$("#productId_"+row_num).val(prod_id);
			$("#rollId_"+row_num).val(roll_id);
			$("#rollWgt_"+row_num).val(qnty);
			$("#hiddenQtyInPcs_"+row_num).val(qtyInPcs);
			$("#yarnLot_"+row_num).val(yarn_lot);
			$("#yarnCount_"+row_num).val(yarn_count);
			$("#colorId_"+row_num).val(color_id);
			$("#stichLn_"+row_num).val(stitch_length);
			$("#locationId_"+row_num).val(location_id);
			$("#machineId_"+row_num).val(machine_no_id);
			$("#brandId_"+row_num).val(brand_id);
			
			//$("#rollRate_"+row_num).val(rate);
			$("#bookWithoutOrder_"+row_num).val(booking_without_order);
			$("#smnBooking_"+row_num).val(bwo);
			$("#isSalesOrder_"+row_num).val(is_sales);
			$("#storeId_"+row_num).val(store_id);
			$("#dtlsId_"+row_num).val('');
			$("#transId_"+row_num).val('');
			$("#rolltableId_"+row_num).val('');

			$("#yarnRate_"+row_num).val(yarn_rate);
			$("#knittingCharge_"+row_num).val(knittingCharge);
			$("#rollRate_"+row_num).val(roll_rate);


			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			$('#txt_tot_row').val(row_num);
			scanned_barcode.push(bar_code);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();

			if(withinGroup==1)
			{
				load_drop_down('requires/roll_wise_grey_fabric_delivery_controller', po_company_id+"_"+withinGroup, 'load_drop_down_company', 'party_td' );				
			}
			else
			{
				load_drop_down('requires/roll_wise_grey_fabric_delivery_controller', buyer_id+"_"+withinGroup, 'load_drop_down_buyer', 'party_td' );
				$('#cbo_party').val(buyer_id);
			}
			$('#cbo_party').attr('disabled','disabled');
		}
		calculate_total();
	}

	function calculate_total()
	{
		var total_roll_weight='';
		var total_roll_qtyInPcs='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			total_roll_weight=total_roll_weight*1+rollWgt*1;
			
			var qtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
			total_roll_qtyInPcs=total_roll_qtyInPcs*1+qtyInPcs*1;
		});
		
		$("#roll_weight_total").text(total_roll_weight.toFixed(2));
		$("#roll_qtyInPcs_total").text(total_roll_qtyInPcs);
	}

	function add_dtls_data( data )
	{
		var barcode_dtlsId_array=new Array(); var barcode_trnasId_array=new Array(); var barcode_rollTableId_array=new Array();
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var trans_id=datas[2];
			var roll_table_id=datas[3];

			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_trnasId_array[barcode_no] = trans_id;
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
				$(this).find('input[name="rolltableId[]"]').val(barcode_rollTableId_array[barcodeNo]);
			}
		});
	}

	function fn_deleteRow( rid )
	{
		var bar_code =$("#barcodeNo_"+rid).val();
		var dtlsId =$("#dtlsId_"+rid).val();
		if(bar_code!="" && dtlsId!="")
		{
			var response=trim(return_global_ajax_value( bar_code+"_"+dtlsId, 'roll_used_check', '', 'requires/roll_wise_grey_fabric_delivery_controller'));
			if(response==1)
			{
				alert('Sorry! Barcode Already Used In Grey Receive By Batch.');
				return;
			}
			else if(response==2)
			{
				alert('Sorry! Barcode Already Used In Grey Fabric Issue Return.');
				return;
			}
		}
		var num_row =$('#scanning_tbl tbody tr').length;
		var rolltableId =$("#rolltableId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();

		if(num_row==1)
		{
			$('#tr_'+rid+' td:not(:last-child)').each(function(index, element) {
				$(this).html('');
			});

			$('#tr_'+rid).find(":input:not(:button)").val('');
		}
		else
		{
			$("#tr_"+rid).remove();
		}

		calculate_total();

		var selected_id='';
		if(rolltableId!='')
		{
			if(txt_deleted_id=='') selected_id=rolltableId; else selected_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val( selected_id );
		}

		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
	}

	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();

		var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="70" id="barcode_1"></td><td width="50" id="roll_1"></td><td width="70" id="location_1"></td><td style="word-break:break-all;" width="70" id="bodyPart_1"></td><td style="word-break:break-all;" width="100" id="cons_1"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="50" id="stL_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="70" id="lot_1"></td><td style="word-break:break-all;" width="70" id="count_1"></td><td width="60" id="rollWeight_1" align="right"></td><td width="60" id="qtyInPcs_1" align="right"></td><td width="60" align="right" id="collarCuffSize_1"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="60" id="bookingNo_1"><td style="word-break:break-all;" width="70" id="job_1"></td><td style="word-break:break-all;" width="70" id="order_1" align="left"></td><td style="word-break:break-all;" width="90" id="knitCompany_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/><input type="hidden" name="yarnLot[]" id="yarnLot_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="stichLn[]" id="stichLn_1"/><input type="hidden" name="locationId[]" id="locationId_1"/><input type="hidden" name="machineId[]" id="machineId_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="rollRate[]" id="rollRate_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/><input type="hidden" name="smnBooking[]" id="smnBooking_1"/><input type="hidden" name="isSalesOrder[]" id="isSalesOrder_1"/><input type="hidden" name="storeId[]" id="storeId_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="yarnRate[]" id="yarnRate_1"/><input type="hidden" name="knittingCharge[]" id="knittingCharge_1"/></td></tr>';

		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_party').val(0);
		$('#txt_fso_no').val('');
		$('#hdn_fso_id').val('');
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_no').val('');
		$('#txt_issue_date').val('');
		$('#txt_deleted_id').val('');
		$('#hdn_fso_id').val('');
		$('#cbo_party').val(0);
		$('#txt_booking_no').val('');
		$('#hdn_booking_id').val('');
		$('#roll_weight_total').text('');
		$('#roll_qtyInPcs_total').text('');
		$("#scanning_tbl tbody").html(html);
		document.getElementById("accounting_posted_status").innerHTML="";
	}

	
	function sales_roll_issue_challan_print()
	{
		// generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'sales_roll_issue_challan_print');

		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+$('#txt_issue_date').val()+'*'+$('#cbo_store_name').val()+'*'+$('#cbo_location').val(),'sales_roll_issue_challan_print');
		return;
	}
	

	function print_report_button_setting(com_id)
	{
		var report_ids=return_global_ajax_value( com_id, 'check_report_button', '', 'requires/roll_wise_grey_fabric_delivery_controller');
		//alert(report_ids);
		if(trim(report_ids)!="")
		{
			$("#print1").hide();

			var report_id=report_ids.split(",");
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==78) $("#print1").show();
			}
		}
		else
		{
			$("#print1").hide();
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
                        <td align="right"><b>Delivery Challan</b></td>
                        <td>
							<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
							<input type="hidden" name="update_id" id="update_id"/>
						</td>
                        <td colspan="2"></td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption">Company</td>
						<td>
							<?
							echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", 0, "print_report_button_setting(this.value);load_drop_down('requires/roll_wise_grey_fabric_delivery_controller', this.value, 'load_drop_down_location', 'location_td' );",0 );
							?>
						</td>
						
						<td align="right" class="must_entry_caption">Location</td>
						<td id="location_td">
							<?
							echo create_drop_down("cbo_location", 152, $blank_array,"", 1,"-- Select Location --", 0,"");
							?>
						</td>
						
						<td align="right" class="must_entry_caption"> Store Name </td>
						<td id="store_td">
							<?
							echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select store--", 1, "" );
							?>
						</td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption" width="100">Delivery Date</td>
						<td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:140px;" value="<? echo date("d-m-Y"); ?>" readonly /></td>
						
						<td align="right" class="must_entry_caption" width="100">FSO No</td>
						<td>
							<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:140px;" placeholder="Display" readonly="" disabled />
							<input type="hidden" name="hdn_fso_id" id="hdn_fso_id" class="text_boxes" value="" />
						</td>
						
						<td align="right">Party</td>
						<td id="party_td">
							<? echo create_drop_down("cbo_party", 152, $blank_array,"", 1,"-- Select Party --", 0,""); ?>
						</td>
					</tr>
					<tr>
						<td align="right">Booking</td>
						<td>
							<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:140px;" placeholder="Display" readonly="" disabled />
							<input type="hidden" name="hdn_booking_id" id="hdn_booking_id"  />
						</td>
						
						<td align="right">Remarks </td>
						<td colspan="3">
							<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:412px">
						</td>						
					</tr>
					<tr>
						<td colspan="2"></td>
                        <td align="right"><strong>Roll Number</strong></td>
                        <td>
							<input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
						</td>
						<td colspan="2" align="center" style="display:none"><input type="button" name="loadData" id="loadData" value="Load Data" class="formbuttonplasminus" style="width:100px" onClick="fnc_loadData();" /></td>
					</tr>
					<tr>
						<td height="5" colspan="6">
							<input type="hidden" name="is_posted_accout" id="is_posted_accout"/>
							<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
						</td>
					</tr>
				</table>
			</fieldset>
			<br>
			<fieldset style="width:1195px;text-align:left">
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
			<table cellpadding="0" width="1355" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="70">Barcode No</th>
					<th width="50">Roll No</th>
					<th width="70">SC Location</th>
					<th width="70">Body Part</th>
					<th width="100">Fab. Composition</th>
					<th width="50">GSM</th>
					<th width="50">Dia</th>
					<th width="50">Stitch Length</th>
					<th width="70">Color</th>
					<th width="70">Y.Lot</th>
					<th width="70">Count</th>
					<th width="60">Roll Wgt.</th>
                    <th width="60">Qty. In Pcs</th>
                    <th width="60">Size</th>
					<th width="60">Cus. Buyer</th>
					<th width="60">Cus. Booking No</th>
					<th width="70">Job No</th>
					<th width="70">Order/FSO No</th>
					<th width="90">Knit Company</th>
					<th></th>
				</thead>
			</table>
			<div style="width:1355px; max-height:250px; overflow-y:scroll" align="left">
				<table cellpadding="0" cellspacing="0" width="1338" border="1" id="scanning_tbl" rules="all" class="rpt_table">
					<tbody>
						<tr id="tr_1" align="center" valign="middle">
							<td width="30" id="sl_1"></td>
							<td width="70" id="barcode_1"></td>
							<td width="50" id="roll_1"></td>
							<td width="70" id="location_1"></td>
							
							<td style="word-break:break-all;" width="70" id="bodyPart_1"></td>
							<td style="word-break:break-all;" width="100" id="cons_1" align="left"></td>
							<td style="word-break:break-all;" width="50" id="gsm_1"></td>
							<td style="word-break:break-all;" width="50" id="dia_1"></td>
							<td style="word-break:break-all;" width="50" id="stL_1"></td>
							<td style="word-break:break-all;" width="70" id="color_1"></td>
							<td style="word-break:break-all;" width="70" id="lot_1"></td>
							<td style="word-break:break-all;" width="70" id="count_1"></td>
							<td width="60" align="right" id="rollWeight_1"></td>
                            <td width="60" align="right" id="qtyInPcs_1"></td>
                            <td width="60" align="right" id="collarCuffSize_1"></td>
							<td style="word-break:break-all;" width="60" id="buyer_1"></td>
							<td style="word-break:break-all;" width="60" id="bookingNo_1"></td>
							<td style="word-break:break-all;" width="70" id="job_1"></td>
							<td style="word-break:break-all;" width="70" id="order_1" align="left"></td>
							<td style="word-break:break-all;" width="90" id="knitCompany_1"></td>
							<td id="button_1" align="center">
								<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
								<input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
								<input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
								<input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
								<input type="hidden" name="productId[]" id="productId_1"/>
								<input type="hidden" name="orderId[]" id="orderId_1"/>
								<input type="hidden" name="rollId[]" id="rollId_1"/>
                                
								<input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/>
                                
								<input type="hidden" name="yarnLot[]" id="yarnLot_1"/>
								<input type="hidden" name="yarnCount[]" id="yarnCount_1"/>
								<input type="hidden" name="colorId[]" id="colorId_1"/>
								<input type="hidden" name="stichLn[]" id="stichLn_1"/>
								<input type="hidden" name="locationId[]" id="locationId_1"/>
								<input type="hidden" name="machineId[]" id="machineId_1"/>
								<input type="hidden" name="brandId[]" id="brandId_1"/>
					
								<input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
								<input type="hidden" name="transId[]" id="transId_1"/>
								<input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
								<input type="hidden" name="rollRate[]" id="rollRate_1"/>
								<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/>
								<input type="hidden" name="smnBooking[]" id="smnBooking_1"/>
								<input type="hidden" name="isSalesOrder[]" id="isSalesOrder_1"/>
								<input type="hidden" name="storeId[]" id="storeId_1"/>
								<input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                <input type="hidden" name="yarnRate[]" id="yarnRate_1"/>
                                <input type="hidden" name="knittingCharge[]" id="knittingCharge_1"/>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="12" align="right">Total</th>
							<th id="roll_weight_total"></th>
                            <th id="roll_qtyInPcs_total"></th>
							<th colspan="7"> </th>
						</tr>
					</tfoot>
				</table>
			</div>
			<br>
			<table width="1350" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
				<tr>
					<td align="center" class="button_container">
						<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
						<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
						<? echo load_submit_buttons($permission,"fnc_grey_fabric_issue_roll_wise",0,1,"fnc_reset_form(); load_scanned_barcode();",1); ?>

						<input type="button" name="print1" id="print1" class="formbutton" value="Print" style=" width:120px; display:none" onClick="sales_roll_issue_challan_print()" >
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>

//$("#Print1").val("GIN1");
$("#Print1").hide();


</script>
</html>
