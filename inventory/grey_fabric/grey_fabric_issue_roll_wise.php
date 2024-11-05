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
		var taken_po_no='';

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
		var scanned_barcode_nos=trim(return_global_ajax_value( issue_id, 'load_scanned_barcode_nos', '', 'requires/grey_fabric_issue_roll_wise_controller'));
		scanned_barcode=eval(scanned_barcode_nos);

		set_button_status(0, permission, 'fnc_grey_fabric_issue_roll_wise',1);
	}

	/*function fnc_loadData()
	{
		var po_details_array=new Array();
		var po_details_data=trim(return_global_ajax_value( '', 'populate_barcode_datas', '', 'requires/grey_fabric_issue_roll_wise_controller'));
		po_details_array=jQuery.parseJSON(po_details_data);
	}*/

	function openmypage_issue()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Issue Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			var posted_account=this.contentDoc.getElementById("hidden_posted_account").value;	 //Posted In account

			if(issue_id!="")
			{
				load_scanned_barcode( issue_id );
				get_php_form_data(issue_id, "populate_data_from_data", "requires/grey_fabric_issue_roll_wise_controller");

				var html=return_global_ajax_value(issue_id, 'populate_barcode_data_update', '', 'requires/grey_fabric_issue_roll_wise_controller');
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
		window.open("requires/grey_fabric_issue_roll_wise_controller.php?data=" + data+'&action='+action, true );
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

		if(form_validation('cbo_company_id*cbo_dyeing_source*cbo_dyeing_comp*txt_issue_date*cbo_issue_purpose','Company*Dyeing Source*Dyeing Company*Issue Date*Issue Purpose')==false)
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
		var blank_store=0;
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
				var rollNo=$(this).find("td:eq(30)").text();

				//var yarnLot=$(this).find('input[name="yarnLot[]"]').val();

				var yarnLot= encodeURIComponent($(this).find('input[name="yarnLot[]"]').val());
				var stichLn= encodeURIComponent($(this).find('input[name="stichLn[]"]').val());


				var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				//var stichLn=$(this).find('input[name="stichLn[]"]').val();
				var locationId=$(this).find('input[name="locationId[]"]').val();
				var machineId=$(this).find('input[name="machineId[]"]').val();
				var brandId=$(this).find('input[name="brandId[]"]').val();

				var floorId=$(this).find('input[name="floorId[]"]').val();
				var roomId=$(this).find('input[name="roomId[]"]').val();
				var rackId=$(this).find('input[name="rackId[]"]').val();
				var shelfId=$(this).find('input[name="shelfId[]"]').val();
				var binId=$(this).find('input[name="binId[]"]').val();

				/*var floor=$(this).find("td:eq(4)").text();
				var room=$(this).find("td:eq(5)").text();
				var rack=$(this).find("td:eq(6)").text();
				var shelf=$(this).find("td:eq(7)").text();*/

				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				var transId=$(this).find('input[name="transId[]"]').val();
				var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
				var rollRate=$(this).find('input[name="rollRate[]"]').val();
				var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
				var smnBooking=$(this).find('input[name="smnBooking[]"]').val();
				var isSalesOrder=$(this).find('input[name="isSalesOrder[]"]').val();
				var storeId=$(this).find('input[name="storeId[]"]').val();
				var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
				var orderNo=$(this).find("td:eq(22)").text();


				var yarnRate=$(this).find('input[name="yarnRate[]"]').val();
				var knittingCharge=$(this).find('input[name="knittingCharge[]"]').val();

				j++;
				if(storeId=="" || storeId==0)
				{
					blank_store++;
					alert("Store not found.");
					return;
				}
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

		if(blank_store>0)
		{
			alert("Store not found.");
			return;
		}
		if(j<1)
		{
			alert('No data');
			return;
		}

		if(operation==2)
		{
			var response=trim(return_global_ajax_value($("#update_id").val()+"_"+all_barcodes, 'check_barcode_for_delete', '', 'requires/grey_fabric_issue_roll_wise_controller'));
			response=response.split("_");
			//alert(response[0])
			if(response[0]==1) {alert( response[1]+" in Posted in accounts.Delete Restricted"); return;}
			if(response[0]==2) {alert( response[1]+" are used in Grey Roll Receive By Batch.Delete Restricted"); return;}
			show_msg('13');
			return;
		}

		//alert(dataString);return;

		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*cbo_company_id*cbo_dyeing_source*cbo_dyeing_comp*txt_issue_date*cbo_issue_purpose*txt_batch_no*txt_batch_id*update_id*txt_deleted_id*txt_remarks*txt_attention*txt_deleted_barcodes',"../../")+dataString+'&new_barcode_nos='+new_barcode_no;
		// alert(dataString);return;
		freeze_window(operation);

		http.open("POST","requires/grey_fabric_issue_roll_wise_controller.php",true);
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
				$('#txt_deleted_id').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);
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

	function openmypage_barcode()
	{
		var company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_roll_wise_controller.php?company_id='+company_id+'&action=barcode_popup','Barcode Popup', 'width=1360px,height=380px,center=1,resize=1,scrolling=0','../')
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
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			$('#txt_bar_code_num').val();
			return;
		}

		var mix_po_variable_settings=trim(return_global_ajax_value(cbo_company_id, 'mix_po_variable_settings', '', 'requires/grey_fabric_issue_roll_wise_controller'));
		//alert(mix_po_variable_settings);

		var row_num=$('#txt_tot_row').val();
		var barcode_nos=trim(barcode_no);
		var num_row=$('#scanning_tbl tbody tr').length;
		var barcode_data=trim(return_global_ajax_value_post(barcode_nos, 'populate_barcode_data', '', 'requires/grey_fabric_issue_roll_wise_controller'));
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
			var brand_show=data[35];
			var program_no=data[36];
			//alert(brand_show);
			//if (jsfloor_room_rack_shelf_name_array.length!=0 || jsfloor_room_rack_shelf_name_array!='undefiend')
			if ((jsfloor_room_rack_shelf_name_array && jsfloor_room_rack_shelf_name_array.length > 0) || jsfloor_room_rack_shelf_name_array!='undefiend' )
			{
				var floor_name=jsfloor_room_rack_shelf_name_array[floor_id];
				var room_name=jsfloor_room_rack_shelf_name_array[room_id];
				var rack_name=jsfloor_room_rack_shelf_name_array[rack];
				var shelf_name=jsfloor_room_rack_shelf_name_array[shelf];
				var bin_name=jsfloor_room_rack_shelf_name_array[bin_box_id];
			}
			else
			{
				floor_name='';
				room_name='';
				rack_name='';
				shelf_name='';
				bin_name='';
			}

			var po_id=data[37];
			var buyer_id=data[38];
			var buyer_name=jsbuyer_name_array[buyer_id];
			var po_no=data[39];
			var job_no=data[40];
			var is_sales=data[41];
			var bookingNumber=data[42];
			var store_id=data[43];
			var internal_ref_no=data[44];
			var qtyInPcs=data[45]*1;
			var collarCuffSize=data[46];
			var body_part_id_latest=data[47];
			var body_part_no_latest=data[48];

			var yarn_rate=data[49];
			var knittingCharge=data[50];
			var roll_rate=data[51];
			var style_no=data[52];

			/*var po_id=data[31];
			var buyer_id=data[32];
			var buyer_name=jsbuyer_name_array[buyer_id];
			var po_no=data[33];
			var job_no=data[34];
			var is_sales=data[35];
			var bookingNumber=data[36]; */


			if( jQuery.inArray( bar_code, scanned_barcode )>-1)
			{
				alert('Sorry! Barcode Already Scanned.');
				$('#txt_bar_code_num').val('');
				return;
			}

			if(mix_po_variable_settings==2)
			{
				if(taken_po_no=='')
				{
					taken_po_no=po_no;

				}
				else
				{
					if(taken_po_no !=po_no)
					{
						alert('Sorry! job Mixed Not Allowed.');
						$('#txt_bar_code_num').val('');
						return;
					}

				}
			}


			if(cbo_company_id!=company_id)
			{
				alert("Multiple Company Not Allowed");
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
			$("#prodId_"+row_num).text(prod_id);
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
			$("#machine_"+row_num).text(machine_name);
			$("#rollWeight_"+row_num).text(qnty);
			$("#qtyInPcs_"+row_num).text(qtyInPcs);
			$("#collarCuffSize_"+row_num).text(collarCuffSize);
			$("#order_"+row_num).text(po_no);
			$("#buyer_"+row_num).text(buyer_name);
			$("#bookingNo_"+row_num).html(bookingNumber+"<br>------<br>"+bwo+"<p>");
			$("#internalRefNo_"+row_num).html(internal_ref_no);
			$("#job_"+row_num).text(job_no);
			$("#style_"+row_num).text(style_no);
			$("#orderId_"+row_num).val(po_id);
			$("#progBookPiNo_"+row_num).text('');
			$("#knitCompany_"+row_num).text(knit_company);
			$("#basis_"+row_num).text(receive_basis);
			$("#progBookPiNo_"+row_num).text(booking_no);
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
			$("#floor_"+row_num).text(floor_name);
			$("#room_"+row_num).text(room_name);
			$("#rack_"+row_num).text(rack_name);
			$("#shelf_"+row_num).text(shelf_name);
			$("#bin_"+row_num).text(bin_name);
			$("#floorId_"+row_num).val(floor_id);
			$("#roomId_"+row_num).val(room_id);
			$("#rackId_"+row_num).val(rack);
			$("#shelfId_"+row_num).val(shelf);
			$("#binId_"+row_num).val(bin_box_id);
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
			$("#brand_"+row_num).text(brand_show);
			$("#program_"+row_num).text(program_no);


			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			$('#txt_tot_row').val(row_num);
			scanned_barcode.push(bar_code);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
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
			var response=trim(return_global_ajax_value( bar_code+"_"+dtlsId, 'roll_used_check', '', 'requires/grey_fabric_issue_roll_wise_controller'));
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
		var txt_deleted_barcodes=$('#txt_deleted_barcodes').val();

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

		var selected_id=''; var deleted_barcode='';
		if(rolltableId!='')
		{
			if(txt_deleted_id=='') selected_id=rolltableId; else selected_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val( selected_id );
		}
		if(txt_deleted_barcodes=='') deleted_barcode=bar_code; else deleted_barcode=txt_deleted_barcodes+','+bar_code;
		$('#txt_deleted_barcodes').val( deleted_barcode );


		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
	}

	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/grey_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value; //Access form field with id="emailfield"

				if(batch_id!="")
				{
					$('#txt_batch_no').val(batch_no);
					$('#txt_batch_id').val(batch_id);
				}
			}
		}
	}

	function check_batch(data)
	{
		if(data=="")
		{
			$('#txt_batch_id').val('');
			return;
		}
		var cbo_company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			return;
		}
		var batch_id=return_global_ajax_value( data+"**"+cbo_company_id, 'check_batch_no', '', 'requires/grey_fabric_issue_roll_wise_controller');
		if(batch_id==0)
		{
			alert("Batch No Found");
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			return;
		}
	}

	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();

		var html='<tr id="tr_1" align="center" valign="middle"><td width="30" id="sl_1"></td><td width="70" id="barcode_1"></td><td width="50" id="roll_1"></td><td width="70" id="location_1"></td><td width="50" id="floor_1"></td><td width="50" id="room_1"></td><td width="50" id="rack_1"></td><td width="50" id="shelf_1"><td width="50" id="bin_1"></td><td width="50" id="prodId_1"></td><td style="word-break:break-all;" width="70" id="bodyPart_1"></td><td style="word-break:break-all;" width="100" id="cons_1"></td><td style="word-break:break-all;" width="50" id="gsm_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="50" id="stL_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="70" id="lot_1"></td><td style="word-break:break-all;" width="70" id="brand_1"></td><td style="word-break:break-all;" width="70" id="count_1"></td><td style="word-break:break-all;" width="60" id="machine_1"></td><td width="60" id="rollWeight_1" align="right"></td><td width="60" id="qtyInPcs_1" align="right"></td><td width="60" align="right" id="collarCuffSize_1"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="60" id="bookingNo_1"><td style="word-break:break-all;" width="100" id="internalRefNo_1"></td><td style="word-break:break-all;" width="70" id="style_1"></td><td style="word-break:break-all;" width="70" id="job_1"></td><td style="word-break:break-all;" width="70" id="order_1" align="left"></td><td style="word-break:break-all;" width="90" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="basis_1"></td><td style="word-break:break-all;" width="100" id="progBookPiNo_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/><input type="hidden" name="yarnLot[]" id="yarnLot_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="stichLn[]" id="stichLn_1"/><input type="hidden" name="locationId[]" id="locationId_1"/><input type="hidden" name="machineId[]" id="machineId_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="floorId[]" id="floorId_1"/><input type="hidden" name="roomId[]" id="roomId_1"/><input type="hidden" name="rackId[]" id="rackId_1"/><input type="hidden" name="shelfId[]" id="shelfId_1"/><input type="hidden" name="binId[]" id="binId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="rollRate[]" id="rollRate_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/><input type="hidden" name="smnBooking[]" id="smnBooking_1"/><input type="hidden" name="isSalesOrder[]" id="isSalesOrder_1"/><input type="hidden" name="storeId[]" id="storeId_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="yarnRate[]" id="yarnRate_1"/><input type="hidden" name="knittingCharge[]" id="knittingCharge_1"/></td></tr>';

		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_dyeing_source').val(0);
		$('#cbo_dyeing_comp').val(0);
		$('#cbo_dyeing_source').attr('disabled',false);
		$('#cbo_dyeing_comp').attr('disabled',false);
		$('#cbo_issue_purpose').attr('disabled',false);
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_no').val('');
		$('#txt_issue_date').val('');
		$('#txt_deleted_id').val('');
		$('#cbo_issue_purpose').val(0);
		$('#txt_batch_no').val('');
		$('#txt_batch_id').val('');
		$('#roll_weight_total').text('');
		$('#roll_qtyInPcs_total').text('');
		$("#scanning_tbl tbody").html(html);
		document.getElementById("accounting_posted_status").innerHTML="";
	}

	function barcode_print()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'issue_challan_print');
	}

	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}

	function fnc_mc_wise_print()
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'mc_wise_print');
	}

	function fnc_print_mg_two()
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'print_mg_two');
	}

	function fabric_details_bpkw()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print_bpkw');
	}
	function fabric_details_bpkw_gin5()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print_bpkw_gin5');
	}
	function fabric_details_bpkw_gin6()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print_bpkw_gin6');
	}
	function fabric_details_bpkw_gin7()
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'fabric_details_print_bpkw_gin7');
	}
	function fabric_details_bpkw_gin8()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print_bpkw_gin8');
	}
	function fabric_details_bpkw_tg1()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print_bpkw_tg1');
	}
	function gray_fabric_rollissue_challan()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'roll_issue_challan_print');

	}
	function gray_fabric_rollissue_challan1()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'roll_issue_challan_print1');

	}
	function gray_fabric_rollissue_challan2()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'roll_issue_challan_print2');
	}
	function gray_fabric_rollissue_challan3()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'roll_issue_challan_print3');
	}

	function gray_fabric_rollissue_challan5()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'roll_issue_challan_print5');
	}



	function gray_fabric_rollissue_challan4()
	{
		if($("#update_id").val()=="")
		{
			alert("Select Save Data First....");
			return;
		}
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'roll_issue_challan_print_atg');
	}
	function sales_roll_issue_challan_print()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'sales_roll_issue_challan_print');
	}
	function sales_roll_issue_challan_print2()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'sales_roll_issue_challan_print2');
	}
	function sales_roll_issue_challan_print3()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+ '*' + $("#no_copy").val(),'sales_roll_issue_challan_print3');
	}
	function sales_roll_issue_challan_print4()
	{
		var update_id = $('#update_id').val();
		if(update_id =="")
		{
			alert('Sorry! Issue No Browse First.');
			return;
		}
		else
		{
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+update_id,'sales_roll_issue_challan_print_mg');
		}
	}

	function print_multi_challan()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var cbo_dyeing_source = $('#cbo_dyeing_source').val();
		var cbo_dyeing_comp = $('#cbo_dyeing_comp').val();



		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&cbo_dyeing_source='+ cbo_dyeing_source +'&cbo_dyeing_comp='+ cbo_dyeing_comp +'&action=multi_issue_popup','Issue Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;

			//print_report( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'roll_issue_challan_print2');
			//print_report( $('#cbo_company_id').val()+'*'+returnNumber+'*'+report_title, "yarn_receive_multy_return_print_2", "requires/yarn_receive_return_controller" )
			print_report( $('#cbo_company_id').val()+'*'+issue_id,'sales_multi_issue_challan_print',"requires/grey_fabric_issue_roll_wise_controller");
		}
	}

	function gray_fabric_no_of_copy(typeid)
	{

		var report_title = $("div.form_caption").html();
		if ($("#cbo_dyeing_source").val() ==3)
		{
			var show_val_column = "0";
			var r = confirm("Press \"OK\" to open with actual buyer or Cancel.");
			//var r = confirm("'Do you want to print with actual buyer?' Ok/Cancel");
			if (r == true)
			{
				var show_dyeing_source = "1";
			}
			else
			{
				var show_dyeing_source = "0";
			}
		}
		if(typeid==1)
		{
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_issue_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + $('#cbo_dyeing_source').val()+ '*' + $("#no_copy").val()+ '*' + $("#cbo_dyeing_comp").val() + '*' + show_dyeing_source, 'roll_issue_no_of_copy_print');
        	return;

		}
		else if(typeid==2)
		{
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_issue_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + $('#cbo_dyeing_source').val()+ '*' + $("#no_copy").val()+ '*' + $("#cbo_dyeing_comp").val() + '*' + show_dyeing_source, 'roll_issue_no_of_copy_print_charka');
       		return;
		}
		else
		{
			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_issue_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + $('#cbo_dyeing_source').val()+ '*' + $("#no_copy").val()+ '*' + $("#cbo_dyeing_comp").val() + '*' + show_dyeing_source, 'roll_issue_no_of_copy_print_ccl');
       		return;
		}

	}


	function print_report_button_setting(com_id)
	{
		var report_ids=return_global_ajax_value( com_id, 'check_report_button', '', 'requires/grey_fabric_issue_roll_wise_controller');
		//alert(report_ids);
		if(trim(report_ids)!="")
		{
			$("#print_barcode").hide();
			$("#btn_fabric_details").hide();
			$("#btn_mc").hide();
			$("#btn_fabric_details_bpkw").hide();
			$("#btn_fabric_details_bpkw_gin5").hide();
			$("#btn_fabric_details_bpkw_gin6").hide();
			$("#print_with_collar_cuff").hide();
			$("#btn_gin1").hide();
			$("#btn_gray_fabric_rollissue_challan").hide();
			$("#sales_wise_issue").hide();
			$("#print1").hide();
			$("#print2").hide();
			$("#print3").hide();
			$("#print4").hide();
			$("#btn_fabric_details_bpkw_gin7").hide();
			$("#print_with_collar_cuff_outside").hide();
			$("#btn_fabric_details_bpkw_tg1").hide();
			$("#btn_print_mg2").hide();
			$("#print_with_collar_cuff_outside_atg").hide();
			$("#print_mg").hide();
			$("#btn_fabric_details_bpkw_gin8").hide();
			$("#print_with_collar_cuff_outside_1").hide();


			var report_id=report_ids.split(",");
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==68) $("#print_barcode").show();
				if(report_id[k]==69) $("#btn_fabric_details").show();
				if(report_id[k]==70) $("#btn_mc").show();
				if(report_id[k]==71) $("#btn_fabric_details_bpkw").show();
				if(report_id[k]==181) $("#btn_fabric_details_bpkw_gin5").show();
				if(report_id[k]==388) $("#btn_fabric_details_bpkw_gin6").show();
				if(report_id[k]==236) $("#print_with_collar_cuff").show();
				if(report_id[k]==325) $("#btn_gray_fabric_rollissue_challan").show();
				if(report_id[k]==326) $("#sales_wise_issue").show();
				if(report_id[k]==327) $("#btn_gin1").show();
				if(report_id[k]==78) $("#print1").show();
				if(report_id[k]==136) $("#print3").show();
				if(report_id[k]==137) $("#print4").show();
				if(report_id[k]==365) $("#print_11").show();
				if(report_id[k]==84) $("#print2").show();
				if(report_id[k]==415) $("#btn_fabric_details_bpkw_gin7").show();
				if(report_id[k]==451) $("#print_with_collar_cuff_outside").show();
				if(report_id[k]==839) $("#btn_fabric_details_bpkw_tg1").show();
				if(report_id[k]==860) $("#btn_print_mg2").show();
				if(report_id[k]==866) $("#print_with_collar_cuff_outside_atg").show();
				if(report_id[k]==848) $("#print_mg").show();
				if(report_id[k]==883) $("#btn_fabric_details_bpkw_gin8").show();
				if(report_id[k]==885) $("#print_with_collar_cuff_outside_1").show();
			}
		}
		else
		{
			$("#print_barcode").hide();
			$("#btn_fabric_details").hide();
			$("#btn_mc").hide();
			$("#btn_fabric_details_bpkw").hide();
			$("#btn_fabric_details_bpkw_gin5").hide();
			$("#btn_fabric_details_bpkw_gin6").hide();
			$("#print_with_collar_cuff").hide();
			$("#btn_gin1").hide();
			$("#btn_gray_fabric_rollissue_challan").hide();
			$("#sales_wise_issue").hide();
			$("#print1").hide();
			$("#print_11").hide();
			$("#print2").hide();
			$("#print3").hide();
			$("#print4").hide();
			$("#btn_fabric_details_bpkw_gin7").hide();
			$("#print_with_collar_cuff_outside").hide();
			$("#btn_fabric_details_bpkw_tg1").hide();
			$("#btn_print_mg2").hide();
			$("#print_with_collar_cuff_outside_atg").hide();
			$("#print_mg").hide();
			$("#btn_fabric_details_bpkw_gin8").hide();
			$("#print_with_collar_cuff_outside_1").hide();

		}


	}

	function fld_lvl_source_fnc(comp_id)
	{
		var fld_lvl_source = <? echo $data_arr; ?>;
		fld_lvl_source =fld_lvl_source[comp_id]['cbo_dyeing_source']['defalt_value'];
		return 	fld_lvl_source;
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
                        <td align="right"><b>Issue No</b></td>
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
							echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  $company_cond order by company_name","id,company_name", 1, "-- Select --", 0, "print_report_button_setting(this.value);load_drop_down( 'requires/grey_fabric_issue_roll_wise_controller',fld_lvl_source_fnc(this.value)+'**'+this.value,'load_drop_down_knitting_com','dyeing_company_td' );",0 );
							?>
						</td>
						<td class="must_entry_caption" align="right">Dyeing Source</td>
						<td>
							<?
							echo create_drop_down( "cbo_dyeing_source", 152, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_fabric_issue_roll_wise_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );

							?>
						</td>
						<td align="right" class="must_entry_caption">Dyeing Company</td>
						<td id="dyeing_company_td">
							<?
							echo create_drop_down( "cbo_dyeing_comp", 152, $blank_array,"", 1, "-- Select --", $selected, "","","" );
							?>
						</td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption" width="100">Issue Date</td>
						<td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:140px;" value="<? echo date("d-m-Y"); ?>" readonly /></td>
						<td align="right" class="must_entry_caption" width="100">Issue Purpose</td>
						<td>
							<?
							echo create_drop_down( "cbo_issue_purpose", 152, $yarn_issue_purpose,"", 1, "-- Select Purpose --", 11, "","","11,3,4,8,26,29,30");
							?>
						</td>
						<td align="right">Batch No</td>
						<td>
							<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Write / Browse" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
							<input type="hidden" id="txt_batch_id" />
						</td>
					</tr>
					<!--<tr>
						<td height="5" colspan="6"></td>
					</tr>-->
					<tr>
						<td align="right">Remarks </td>
						<td colspan="3">
							<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:412px">
						</td>
						<td align="right">Attention</td>
						<td>
							<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:140px;" placeholder="Write" />
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
			<fieldset style="width:1905px;text-align:left">
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
			<table cellpadding="0" width="2215" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="60">Buyer</th>
					<th width="70">Style</th>
					<th width="70">Job No</th>
					<th width="70">Order/FSO No</th>
					<th width="60">Booking No</th>
					<th width="70">Basis</th>
					<th width="90">Knit Company</th>
					<th width="70">Location</th>
					<th width="100">Internal Ref No.</th>
					<th width="100">Deli. / Booking/ PI No/ Trans No</th>
					<th width="60">Machine No.</th>
					<th width="70">Body Part</th>
					<th width="50">Product Id</th>
					<th width="60">Qty. In Pcs</th>
					<th width="60">Size</th>
					<th width="70">Count</th>
					<th width="70">Brand</th>
					<th width="70">Lot</th>
					<th width="100">Construction/ Composition</th>
					<th width="50">Stitch Length</th>
					<th width="50">Dia</th>
					<th width="50">GSM</th>
					<th width="70">Program</th>
					<th width="70">Color</th>
					<th width="50">Floor</th>
					<th width="50">Room</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="50">Bin/Box</th>
					<th width="50">Roll No</th>
					<th width="70">Barcode No</th>
					<th width="60">Roll Wgt.</th>
					<th></th>
				</thead>
			</table>
			<div style="width:2215px; max-height:250px; overflow-y:scroll" align="left">
				<table cellpadding="0" cellspacing="0" width="2198" border="1" id="scanning_tbl" rules="all" class="rpt_table">
					<tbody>
						<tr id="tr_1" align="center" valign="middle">
							<td width="30" id="sl_1"></td>
							<td style="word-break:break-all;" width="60" id="buyer_1"></td>
							<td style="word-break:break-all;" width="70" id="style_1"></td>
							<td style="word-break:break-all;" width="70" id="job_1"></td>
							<td style="word-break:break-all;" width="70" id="order_1" align="left"></td>
							<td style="word-break:break-all;" width="60" id="bookingNo_1"></td>
							<td style="word-break:break-all;" width="70" id="basis_1"></td>
							<td style="word-break:break-all;" width="90" id="knitCompany_1"></td>
							<td width="70" id="location_1"></td>
							<td style="word-break:break-all;" width="100" id="internalRefNo_1"></td>
							<td style="word-break:break-all;" width="100" id="progBookPiNo_1"></td>
							<td style="word-break:break-all;" width="60" id="machine_1"></td>
							<td style="word-break:break-all;" width="70" id="bodyPart_1"></td>
							<td width="50" id="prodId_1"></td>
							<td width="60" align="right" id="qtyInPcs_1"></td>
							<td width="60" align="right" id="collarCuffSize_1"></td>
							<td style="word-break:break-all;" width="70" id="count_1"></td>
							<td style="word-break:break-all;" width="70" id="brand_1"></td>
							<td style="word-break:break-all;" width="70" id="lot_1"></td>
							<td style="word-break:break-all;" width="100" id="cons_1" align="left"></td>
							<td style="word-break:break-all;" width="50" id="stL_1"></td>
							<td style="word-break:break-all;" width="50" id="dia_1"></td>
							<td style="word-break:break-all;" width="50" id="gsm_1"></td>
							<td style="word-break:break-all;" width="70" id="program_1"></td>
							<td style="word-break:break-all;" width="70" id="color_1"></td>
							<td width="50" id="floor_1"></td>
							<td width="50" id="room_1"></td>
							<td width="50" id="rack_1"></td>
							<td width="50" id="shelf_1"></td>
							<td width="50" id="bin_1"></td>
							<td width="50" id="roll_1"></td>
							<td width="70" id="barcode_1"></td>
							<td width="60" align="right" id="rollWeight_1"></td>
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
								<input type="hidden" name="floorId[]" id="floorId_1"/>
								<input type="hidden" name="roomId[]" id="roomId_1"/>
								<input type="hidden" name="rackId[]" id="rackId_1"/>
								<input type="hidden" name="shelfId[]" id="shelfId_1"/>
								<input type="hidden" name="binId[]" id="binId_1"/>
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
							<th colspan="14" align="right">Total</th>

                            <th id="roll_qtyInPcs_total"></th>
							<th colspan="17"> </th>
							<th id="roll_weight_total"></th>
						</tr>
					</tfoot>
				</table>
			</div>
			<br>
			<table width="2000" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
				<tr>
					<td align="center" class="button_container">
						<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
						<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
						<input type="hidden" name="txt_deleted_barcodes" id="txt_deleted_barcodes" class="text_boxes" value="">
						<? echo load_submit_buttons($permission,"fnc_grey_fabric_issue_roll_wise",0,1,"fnc_reset_form(); load_scanned_barcode();",1); ?>

						<input type="button" name="btn_gin1" id="btn_gin1" class="formbutton" value="GIN1" style=" width:80px; display:none" onClick="gray_fabric_rollissue_challan1()" >

						<input type="button" name="btn_gray_fabric_rollissue_challan" id="btn_gray_fabric_rollissue_challan" class="formbutton" value="GIN2" style=" width:80px; display:none" onClick="gray_fabric_rollissue_challan();" >

						<input type="button" name="sales_wise_issue" id="sales_wise_issue" class="formbutton" value="Sales Wise Issue" style=" width:120px; display:none" onClick="sales_roll_issue_challan_print()" >
						<input type="button" name="print2" id="print2" class="formbutton" value="Print 2" style=" width:120px; display:none" onClick="sales_roll_issue_challan_print2()" >
						<input type="button" name="print_mg" id="print_mg" class="formbutton" value="Print MG" style=" width:120px;display:none;" onClick="sales_roll_issue_challan_print4()" >
					</td>
				</tr>
				<tr>
					<td align="center" >
						<input type="button" name="print_barcode" id="print_barcode" class="formbutton" value="Print Barcode" style=" width:100px; display:none" onClick="barcode_print();" >
						<input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton" value="Fab Details (Outside)" style=" width:120px; display:none" onClick="fabric_details();" >
						<input type="button" name="btn_mc" id="btn_mc" class="formbutton" value="GIN3-MC" style=" width:100px; display:none" onClick="fnc_mc_wise_print();" >
						<input type="button" name="btn_fabric_details_bpkw" id="btn_fabric_details_bpkw" class="formbutton" value="GIN4" style=" width:100px; display:none" onClick="fabric_details_bpkw();" >
                        <input type="button" name="btn_fabric_details_bpkw_gin5" id="btn_fabric_details_bpkw_gin5" class="formbutton" value="GIN5" style=" width:80px; display:none" onClick="fabric_details_bpkw_gin5();" >
                        <input type="button" name="btn_fabric_details_bpkw_gin6" id="btn_fabric_details_bpkw_gin6" class="formbutton" value="GIN6" style=" width:80px; display:none" onClick="fabric_details_bpkw_gin6();" >
						<input type="button" name="btn_fabric_details_bpkw_gin7" id="btn_fabric_details_bpkw_gin7" class="formbutton" value="GIN7" style=" width:80px; display:none" onClick="fabric_details_bpkw_gin7();" >
						<input type="button" name="btn_fabric_details_bpkw_gin8" id="btn_fabric_details_bpkw_gin8" class="formbutton" value="GIN8" style=" width:80px; display:none" onClick="fabric_details_bpkw_gin8();" >
                        <input type="button" name="print_with_collar_cuff" id="print_with_collar_cuff" class="formbutton" value="Print With Collar Cuff" style=" width:120px;display:none;" onClick="gray_fabric_rollissue_challan2();" >
						<input type="button" name="print_with_collar_cuff_outside" id="print_with_collar_cuff_outside" class="formbutton" value="Print With Collar Cuff-outside" style=" width:165px;display:none;" onClick="gray_fabric_rollissue_challan3();" >
						<input type="button" name="print_with_collar_cuff_outside_atg" id="print_with_collar_cuff_outside_atg" class="formbutton" value="Print With Collar Cuff-outside-ATG" style=" width:190px; display:none" onClick="gray_fabric_rollissue_challan4();" >
                        <input type="text" value="1"  title="No. of copy" placeholder="No. of copy" id="no_copy" class="text_boxes_numeric" style="width:55px;"/>
                		<input type="button" class="formbutton" name="print1" id="print1" style=" width:80px; display:none" value="Print 1" onClick="gray_fabric_no_of_copy(1)"/>
                		<input type="button" title="print for Charka" class="formbutton" name="print3" id="print3" style=" width:80px; display:none" value="Print 3" onClick="gray_fabric_no_of_copy(2)"/>
						<input type="button" title="print for Charka" class="formbutton" name="print4" id="print4" style=" width:80px; display:none" value="Print 4" onClick="sales_roll_issue_challan_print3()"/>
						<input type="button" name="btn_fabric_details_bpkw_tg1" id="btn_fabric_details_bpkw_tg1" class="formbutton" value="TG-1" style=" width:55px; display:none" onClick="fabric_details_bpkw_tg1();" >
                		<input type="button" class="formbutton" name="print_11" id="print_11" style=" width:110px; display:none" value="Print multi challan" onClick="print_multi_challan()"/>
                		<input type="button" class="formbutton" name="btn_print_mg2" id="btn_print_mg2" style=" width:110px; display:none" value="Print MG2" onClick="fnc_print_mg_two()"/>

						<input type="button" name="print_with_collar_cuff_outside_1" id="print_with_collar_cuff_outside_1" class="formbutton" value="Print With Collar Cuff-outside-1" style=" width:175px;display:none;" onClick="gray_fabric_rollissue_challan5();" >

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
